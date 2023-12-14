<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function getUsersWithSearch(Request $request)
    {
        $perPageRecords = !empty($request->query('per_page_record')) ? $request->query('per_page_record') : 10;
        $searchQuery = $request->input('search');

        $query = User::where('id', '!=', $request->user()->id)
            ->where('role', '!=', User::ROLE_ADMIN)->where('email_verified_at', '!=', null)
            ->where(function ($query) use ($searchQuery) {
                $query->orWhere('username', 'like', '%' . $searchQuery . '%')
                    ->orWhere('full_name', 'like', '%' . $searchQuery . '%');
            });
        $paginate = $query->paginate($perPageRecords);

        return returnSuccessResponse("User list", User::getPaginateObj($paginate));
    }
    public function follow(Request $request)
    {
        $rules = [
            'receiver_id' => 'required|exists:users,id',
        ];
        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            throw new HttpResponseException(returnValidationErrorResponse(getValidatorErrors($validator)));
        }

        $userId = $inputArr['receiver_id'];

        $toUser = User::find($userId);

        $isRecord = Follow::where('receiver_id', $userId)->where('sender_id', $request->user()->id)->first();
        if ($isRecord) {
            return returnErrorResponse("Already following.");
        }
        $model = new Follow();
        $model->receiver_id = $toUser->id;
        $model->sender_id = auth()->user()->id;
        if ($model->save()) {

            return returnSuccessResponse('Follow successfully.');
        }

        return returnErrorResponse("Failed to follow.");
    }
    public function unFollow(Request $request)
    {
        $rules = [
            'receiver_id' => 'required|exists:users,id',
        ];
        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            throw new HttpResponseException(returnValidationErrorResponse(getValidatorErrors($validator)));
        }

        $userId = $inputArr['receiver_id'];

        $isFollow = Follow::where('receiver_id', $userId)
            ->where('sender_id', $request->user()->id)->first();
        // dd($isFollow);
        if ($isFollow == null)
            return returnErrorResponse('already unfollowed');

        if ($isFollow->delete()) {
            return returnSuccessResponse('unfollowed.');
        }
    }

    public function following(Request $request)
    {
        $perPageRecords = !empty($request->query('per_page_record')) ? $request->query('per_page_record') : 10;

        $query = User::select("users.*")->join('follows', 'follows.receiver_id', '=', 'users.id')->where('follows.sender_id', auth()->user()->id)->orderBy('follows.id', 'desc');

        $paginate = $query->paginate($perPageRecords);

        return returnSuccessResponse("list success", User::getPaginateObj($paginate));
    }

    public function followers(Request $request)
    {
        $perPageRecords = !empty($request->query('per_page_record')) ? $request->query('per_page_record') : 10;

        $query = User::select("users.*")->join('follows', 'follows.sender_id', '=', 'users.id')->where('follows.receiver_id', auth()->user()->id)->orderBy('follows.id', 'desc');

        $paginate = $query->paginate($perPageRecords);

        return returnSuccessResponse("list success", User::getPaginateObj($paginate));
    }
    public function getMultipleUsers(Request $request)
    {
        $ids = explode(',', $request->user_id);
        $users = User::whereIn('id', $ids)->where('role', '!=', User::ROLE_ADMIN)->get();
        return returnSuccessResponse("users", getModelsJsonObj($users, 'jsonResponse'));
    }
    public function getS3Details(Request $request)
    {
        $settings = [
            'accessKey' => !empty(env('AWS_ACCESS_KEY_ID')) ? env('AWS_ACCESS_KEY_ID') : "Not in env",
            'secretKey' => !empty(env("AWS_SECRET_ACCESS_KEY")) ? env("AWS_SECRET_ACCESS_KEY") : "Not in env",
            'region' => !empty(env('AWS_DEFAULT_REGION')) ? env('AWS_DEFAULT_REGION') : "Not in env",
            'bucket' => !empty(env('AWS_BUCKET')) ? env('AWS_BUCKET') : "Not in env",
            'AWS_USE_PATH_STYLE_ENDPOINT' => env('AWS_USE_PATH_STYLE_ENDPOINT'),
            'file_path' => '/goomi',
        ];
        return returnSuccessResponse("S3 details", $settings);
    }
}
