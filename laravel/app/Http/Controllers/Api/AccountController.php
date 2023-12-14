<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserDocuments;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Auth;

class AccountController extends Controller {
    public function changePassword(Request $request) {

        $rules = [
            'old_password' => 'required',
            'new_password' => 'required',
            'confirm_new_password' => 'required|same:new_password',
        ];
        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
        }
        $userObj = $request->user();
        if(!$userObj) {
            return notAuthorizedResponse('User is not authorized');
        }

        if(!Hash::check($request->old_password, $userObj->password)) {
            throw new HttpResponseException(returnValidationErrorResponse('Invalid old Password'));
        }

        $userObj->password = $request->get('new_password');
        if(!$userObj->save()) {
            return returnErrorResponse('Unable to change Password');
        }

        $returnArr = $userObj->jsonResponse();
        $returnArr['auth_token'] = $request->bearerToken();
        return returnSuccessResponse('Password updated successfully', $returnArr);
    }

    public function getProfile(Request $request) {
        $userObj = $request->user();
        // if (!$userObj) {
        //     return notAuthorizedResponse('User is not authorized');
        // }

        $returnArr = $userObj->jsonResponse();
        // $returnArr['auth_token'] = $request->bearerToken();
        return returnSuccessResponse('User profile', $returnArr);
    }

    public function notification(Request $request) {

        $rules = [
            'notification_type' => 'required|integer|min:1|max:2'
        ];
        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
        }

        $user = auth()->user();

        if($request->notification_type == User::DEVICE_NOTIFICATION) {
            $attribute = "notification";
        } else {
            $attribute = "email_notification";

        }

        if($user->$attribute == 1) {
            $user->$attribute = 0;
            $message = "Notification off successfully.";
        } else {
            $user->$attribute = 1;
            $message = "Notification on successfully.";
        }
        if($user->save())
            return returnSuccessResponse($message, $user->jsonResponse());

    }

    public function updateProfile(Request $request) {

        $userObj = $request->user();
        $rules = [
            'full_name' => 'required',
            'username' => [
                'required',
                Rule::unique('users')->ignore($userObj->id),
            ],
            // 'phone_number' => 'required',
            // 'gender' => 'required',
            // 'role' => "required",
            'document' => "required_if:role,==,3" // 3 for busseness 
        ];
        $message = [
            'document.required_if' => "The document field is required when role is Bussiness."
        ];
        if(!$userObj) {
            return notAuthorizedResponse('User is not authorized');
        }
        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules, $message);
        if($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
        }
        $userObj = User::where('id', $request->user()->id)->with('documnets')->first();
        if(!$userObj) {
            return notAuthorizedResponse('User is not authorized');
        }

        $userObj->full_name = $request->full_name;
        $userObj->username = $request->username;
        $userObj->email = $request->email;
        if($request->hasFile('profile_image')) {
            deleteFile($userObj->profile_image);
            $userObj->profile_image = saveUploadedFile($request->profile_image);
        }
        if($request->hasFile('document')) {
            $documents = $request->file('document');
            foreach($userObj->documnets as $value) {
                // dd($value->document);
                deleteFile($value->document);
                $value->delete();
            }
            foreach($documents as $file) {
                $documentPath = saveUploadedFile($file);
                UserDocuments::updateOrCreate([
                    'user_id' => $userObj->id,
                    'document' => $documentPath
                ]);
            }
            $userObj->role = $request->role;
        }
        if(!$userObj->save()) {
            return returnErrorResponse('Unable to save data');
        }

        $returnArr = $userObj->jsonResponse(true);
        return returnSuccessResponse('Profile updated successfully', $returnArr);
    }
}
