<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ForgetPasswordMail;
use App\Mail\RegisterMail;
use App\Mail\ResendOtpMail;
use App\Models\UserDocuments;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Auth;
use App\Models\ErrorLog;
use App\Models\EmailQueue;

class AuthController extends Controller
{
    public function register(Request $request, User $user)
    {
        $rules = [
            'full_name' => 'required',
            'email' => 'required|email:rfc,dns,filter|unique:users,email,NULL,id,deleted_at,NULL',
            'password' => [
                'required',
                'string',
                'min:8',
                'max:15',
                'regex:/^(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*])[A-Za-z0-9!@#$%^&*]+$/',
            ],
            'phone_number' => "required|unique:users,phone_number,NULL,id,deleted_at,NULL",
            'gender' => "required",
            'device_type' => 'required',
            'role' => 'required',
            'document' => "required_if:role,==,3" // 3 for busseness 
            // 'fcm_token' => 'required'

        ];
        $message = [
            'password.regex' => "Passwoed should be one capital letter, one number, one special characters",
            'document.required_if' => 'Document is required for admin approval'
        ];
        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
        }
        $inputArr = $request->except(["document"]);
        $user = $user->fill($inputArr);
        $otp = $user->generateEmailVerificationOtp();
        $user->email_verification_otp = $otp;
        $details = [
            'full_name' => $request->full_name,
            'otp' => $otp
        ];
        try {
            \Mail::to($request->email)->send(new RegisterMail($details));
        } catch (\Exception $ex) {
            return back()->with('error', 'Mail could not be send,We have some SMTP server Issues.Please try again later.');
        }
        try {
            DB::beginTransaction();
            if (!$user->save()) {
                DB::rollback();
                return returnErrorResponse('Unable to register user. Please try again later');
            }
            if ($request->hasFile('document')) {
                $documents = $request->file('document');
                foreach ($documents as $file) {
                    $documentPath = saveUploadedFile($file);
                    UserDocuments::create([
                        'user_id' => $user->id,
                        'document' => $documentPath
                    ]);
                }
            }
            // $returnArr = $user->jsonResponse();
            // $returnArr['documents'] = $document;
            DB::commit();
            return returnSuccessResponse('You are registered successfully.Please verify otp sent to you on mail.', $user->jsonResponse(true));

        } catch (\Throwable $th) {
            DB::rollback();
            return returnErrorResponse('Some thing is went wrong');
        }


    }

    public function verifyOtp(Request $request, User $user)
    {
        $rules = [
            'user_id' => 'required',
            'otp' => 'required'
        ];

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
        }

        $userObj = User::where('id', $inputArr['user_id'])
            ->where('email_verification_otp', $inputArr['otp'])
            ->first();
        if (!$userObj) {
            return returnNotFoundResponse('Invalid OTP');
        }

        $userObj->email_verified_at = Carbon::now();
        $userObj->email_verification_otp = null;
        $userObj->type = $userObj->role == User::ROLE_USER ? User::STATUS_APROVE : User::STATUS_PENDING;
        $userObj->save();

        $updatedUser = User::find($inputArr['user_id']);
        $returnArr = $updatedUser->jsonResponse();
        // $message = 'OTP verified successfully Wait for admin approval';
        // if ($userObj->role == User::ROLE_USER) {
        $authToken = $returnArr['auth_token'] = $updatedUser->createToken('authToken')->plainTextToken;
        $returnArr['auth_token'] = $authToken;
        $message = 'OTP verified successfully';
        // }
        return returnSuccessResponse($message, $returnArr);
    }

    public function resendOtp(Request $request, User $user)
    {
        $userId = $request->user_id;
        if (!$userId) {
            throw new HttpResponseException(returnValidationErrorResponse('Please send user id with this request'));
        }
        $userObj = User::where('id', $userId)->first();
        if (!$userObj) {
            return returnNotFoundResponse('User not found with this user id');
        }

        $verificationOtp = $userObj->generateEmailVerificationOtp();
        $userObj->email_verified_at = null;
        $userObj->email_verification_otp = $verificationOtp;
        $details = [
            'full_name' => $request->full_name,
            'otp' => $verificationOtp
        ];
        try {
            \Mail::to($request->email)->send(new ResendOtpMail($details));
        } catch (\Exception $ex) {
            return back()->with('error', 'Mail could not be send,We have some SMTP server Issues.Please try again later.');
        }
        $userObj->save();
        return returnSuccessResponse('OTP resend successfully!', $userObj->jsonResponse());
    }

    public function login(Request $request)
    {
        $rules = [
            'email' => 'required',
            'password' => 'required',
            'device_type' => 'required',
            // 'fcm_token' => 'required'

        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
        }

        $inputArr = $request->all();

        $userObj = User::where('phone_number', $inputArr['phone_number'])->first();
        if (empty($userObj))
            return returnNotFoundResponse('User Not found.');

        if ($userObj->status == User::STATUS_INACTIVE)
            return returnErrorResponse("Your account is inactive please contact with admin.");

        if (empty($userObj->email_verified_at)) {
            return returnNotFoundResponse("Verify email first");
        }
        switch ($userObj->type) {
            case User::STATUS_PENDING:
                return returnValidationErrorResponse("Please wait for admin approval");
            case User::STATUS_REJECTED:
                return returnValidationErrorResponse("Admin Reject your document please resend it");
            default:
                returnErrorResponse("Some thing went wrong");
                break;
        }

        if (!Auth::attempt(['phone_number' => $inputArr['phone_number'], 'password' => $inputArr['password']])) {
            return returnNotFoundResponse('Invalid credentials');
        }

        $userObj->device_type = $inputArr['device_type'];
        $userObj->fcm_token = $inputArr['fcm_token'];
        $userObj->save();

        $userObj->tokens()->delete();
        $authToken = $userObj->createToken('authToken')->plainTextToken;
        $returnArr = $userObj->jsonResponse();
        $returnArr['auth_token'] = $authToken;

        return returnSuccessResponse('User logged in successfully', $returnArr);
    }

    public function logout(Request $request)
    {
        $userObj = $request->user();
        if (!$userObj) {
            return returnNotAuthorizedResponse('You are not authorized');
        }

        $userObj->tokens()->delete();
        $userObj->fcm_token = null;
        $userObj->save();
        return returnSuccessResponse('User logged out successfully');
    }

    public function forgotPassword(Request $request, User $user)
    {
        $rules = [
            'email' => 'required',
        ];

        $messages = [
            'email.required' => 'Please enter email address.'
        ];

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules, $messages);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
        }

        $userObj = User::where('email', $inputArr['email'])
            ->first();
        if (!$userObj) {
            return returnNotFoundResponse('User not found with this email address');
        }

        if (empty($userObj->email_verified_at))
            return returnNotFoundResponse('Please verify your email.');

        $resetPasswordOtp = $userObj->generateEmailVerificationOtp();
        $userObj->email_verification_otp = $resetPasswordOtp;
        $details = [
            'full_name' => $userObj->full_name,
            'otp' => $resetPasswordOtp
        ];
        try {
            \Mail::to($request->email)->send(new ForgetPasswordMail($details));
        } catch (\Exception $ex) {
            return back()->with('error', 'Mail could not be send,We have some SMTP server Issues.Please try again later.');
        }
        $userObj->save();
        return returnSuccessResponse('Reset password OTP sent successfully', $userObj->jsonResponse());
    }

    public function verifyForgotPasswordOtp(Request $request, User $user)
    {
        $rules = [
            'user_id' => 'required',
            'reset_password_otp' => 'required'
        ];

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
        }

        $userObj = User::where('id', $inputArr['user_id'])
            ->where('email_verification_otp', $inputArr['reset_password_otp'])
            ->first();
        if (!$userObj) {
            return returnNotFoundResponse('Invalid reset password OTP');
        }


        $userObj->email_verification_otp = null;
        $userObj->save();

        $updatedUser = User::find($inputArr['user_id']);
        $returnArr = $updatedUser->jsonResponse();

        return returnSuccessResponse('Reset Password OTP verified successfully', $returnArr);
    }

    public function resendForgotPasswordOtp(Request $request, User $user)
    {
        $userId = $request->user_id;
        if (!$userId) {
            throw new HttpResponseException(returnValidationErrorResponse('Please send user id with this request'));
        }
        $userObj = User::where('id', $userId)->first();
        if (!$userObj) {
            return returnNotFoundResponse('User not found with this user id');
        }

        $verificationOtp = $userObj->generateEmailVerificationOtp();
        $userObj->email_verification_otp = $verificationOtp;
        $details = [
            'full_name' => $userObj->full_name,
            'otp' => $verificationOtp
        ];
        try {
            \Mail::to($userObj->email)->send(new ResendOtpMail($details));
        } catch (\Exception $ex) {
            return back()->with('error', 'Mail could not be send,We have some SMTP server Issues.Please try again later.');
        }
        $userObj->save();

        return returnSuccessResponse('Reset password OTP resend successfully!', $userObj->jsonResponse());
    }


    public function resetPassword(Request $request, User $user)
    {

        $rules = [
            'user_id' => 'required',
            'new_password' => 'required|min:6|max:10',
            'confirm_new_password' => 'required|same:new_password'

        ];
        $inputArr = $request->all();
        $message = [
            'confirm_new_password.same' => 'Password and confirm password should be same.',
        ];
        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
        }

        $userObj = User::where('id', $inputArr['user_id'])->first();
        if (!$userObj) {
            return returnNotFoundResponse('User not found');
        }

        $userObj->password = $inputArr['new_password'];
        $userObj->save();


        return returnSuccessResponse('Password reset successfully', $userObj->jsonResponse());
    }

    public function test()
    {
        $user = User::create(['full_name' => 'test123']);
        return response()->json([$user]);
    }
    public function signUpLogin(Request $request)
    {
        $rules = [
            'phone_number' => 'required',
            'phone_code' => 'required',

        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
        }
        $phoneNumber = $request->input('phone_number');

        $user = User::firstOrNew([
            'phone_number' => $phoneNumber,
            'role' => User::ROLE_USER,
            'phone_code' => $request->phone_code
        ]);
        $flag = false;
        if ($user->role == User::BUSSINESS) {
            $flag = true;
        }

        if (!$user->exists) {
            $user->email_verification_otp = 111111; //mt_rand(100000, 999999);
            $user->save();

            return returnSuccessResponse("Otp sent Successfully ", ['user_id' => $user->loginSignupResponse($flag), 'user_exist' => false]);
        }
        $user->email_verification_otp = 111111; //mt_rand(100000, 999999);
        $user->save();

        return returnSuccessResponse("Otp sent Successfully", ['user_id' => $user->loginSignupResponse($flag), 'user_exist' => true]);
    }
}
