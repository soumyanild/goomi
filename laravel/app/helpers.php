<?php

use Carbon\Carbon;
use App\Models\User;
use App\Models\Permission;
use App\Models\RolePermission;


const RECORD_PER_PAGE = 10;
const MANAGE_USERS = 1;
const MANAGE_CUSTOMERS = 2;
const MANAGE_ROLES = 3;

function generateBarcode($info)
{
    $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
    return '<img src="data:image/png;base64,' . base64_encode($generator->getBarcode($info, $generator::TYPE_CODE_128)) . '" >';

}

function trimArray($array)
{
    $newArr = [];
    foreach ($array as $key => $data) {
        $newArr[$key] = is_string($data) ? trim($data) : $data;

    }
    return $newArr;
}


function hasPermission($permissionType)
{
    $permissionArr = [
        'is_read' => false,
        'is_write' => false,
        'is_create' => false,
    ];

    $user = auth()->user();
    if ($user->isSuperAdmin()) {
        $permissionArr = [
            'is_read' => true,
            'is_write' => true,
            'is_create' => true,
        ];
        return $permissionArr;

    }

    $permission = Permission::where(['type' => $permissionType])->first();
    if (empty($permission))
        return $permissionArr;

    $rolePermission = RolePermission::where(['permission_id' => $permission->id, 'role_id' => $user->role])->first();

    if (!empty($rolePermission)) {
        if ($rolePermission->is_writable)
            $permissionArr['is_write'] = true;

        if ($rolePermission->is_readable)
            $permissionArr['is_read'] = true;

        if ($rolePermission->is_creatable)
            $permissionArr['is_create'] = true;
    }

    return $permissionArr;

}


function saveUploadedFile($file, $folder = "images")
{
    $fileName = rand() . '_' . time() . '.' . $file->getClientOriginalExtension();
    Storage::disk($folder)->putFileAs('/', $file, $fileName);
    return Storage::disk($folder)->url($fileName);
}


if (!function_exists('deleteFile')) {

    function deleteFile($file, $path = null)
    {
        try {
            $file = !empty($path) ? $path . $file : $file;
            $filename = explode(url('/') . '/', $file);
            $filePath = $filename[1];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        } catch (\Exception $ex) {
            Log::error('FAILED Delete Error ' . $ex->getMessage());
        }
        return true;
    }
}


if (!function_exists('returnNotFoundResponse')) {

    function returnNotFoundResponse($message = '', $data = array())
    {
        $returnArr = [
            'statusCode' => 404,
            'status' => 'not found',
            'message' => $message,
            'data' => ($data) ? ($data) : ((object) $data)
        ];
        return response()->json($returnArr, 404);
    }
}

if (!function_exists('returnValidationErrorResponse')) {

    function returnValidationErrorResponse($message = '', $data = array())
    {
        $returnArr = [
            'statusCode' => 422,
            'status' => 'vaidation error',
            'message' => $message,
            'data' => ($data) ? ($data) : ((object) $data)
        ];
        return response()->json($returnArr, 422);
    }
}

if (!function_exists('returnSuccessResponse')) {

    function returnSuccessResponse($message = '', $data = array(), $is_array = false)
    {
        $is_array = !empty($is_array) ? [] : (object) [];
        $returnArr = [
            'statusCode' => 200,
            'status' => 'success',
            'message' => $message,
            'data' => ($data) ? ($data) : $is_array
        ];
        return response()->json($returnArr, 200);
    }
}

if (!function_exists('returnErrorResponse')) {

    function returnErrorResponse($message = '', $data = array())
    {
        $returnArr = [
            'statusCode' => 500,
            'status' => 'error',
            'message' => $message,
            'data' => ($data) ? ($data) : ((object) $data)
        ];
        return response()->json($returnArr, 500);
    }
}

if (!function_exists('returnCustomErrorResponse')) {

    function returnCustomErrorResponse($message = '', $data = array())
    {
        $returnArr = [
            'statusCode' => 404,
            'status' => 'error',
            'message' => $message,
            'data' => ($data) ? ($data) : ((object) $data)
        ];
        return response()->json($returnArr, 200);
    }
}

if (!function_exists('returnError301Response')) {

    function returnError301Response($message = '', $data = array())
    {
        $returnArr = [
            'statusCode' => 301,
            'status' => 'error',
            'message' => $message,
            'data' => ($data) ? ($data) : ((object) $data)
        ];
        return response()->json($returnArr, 301);
    }
}

if (!function_exists('notAuthorizedResponse')) {

    function notAuthorizedResponse($message = '', $data = array())
    {
        $returnArr = [
            'statusCode' => 401,
            'status' => 'error',
            'message' => $message,
            'data' => ($data) ? ($data) : ((object) $data)
        ];
        return response()->json($returnArr);
    }
}

if (!function_exists('forbiddenResponse')) {

    function forbiddenResponse($message = '', $data = array())
    {
        $returnArr = [
            'statusCode' => 403,
            'status' => 'error',
            'message' => $message,
            'data' => ($data) ? ($data) : ((object) $data)
        ];
        return response()->json($returnArr, 403);
    }
}

if (!function_exists('getCities')) {
    function getCities()
    {
        $citiesArr = \App\Models\City::getCitiesDropdownArr();
        return $citiesArr;
    }
}

if (!function_exists('getStates')) {
    function getStates()
    {
        $statesArr = \App\Models\State::getStatesDropdownArr();
        return $statesArr;
    }
}

if (!function_exists('getCountries')) {
    function getCountries()
    {
        $countriesArr = \App\Models\Country::getCountriesDropdownArr();
        return $countriesArr;
    }
}

if (!function_exists('frontendDateTimeFormat')) {
    function frontendDateTimeFormat($date = '', $format = 'Y-m-d H:i A', $timeZone = '')
    {
        if ($date) {
            $timeZone = ($timeZone) ? ($timeZone) : (env('APP_TIMEZONE'));
            if ($timeZone) {
                return Carbon::parse($date)->timeZone($timeZone)->format($format);
            }
            return Carbon::parse($date)->format($format);
        }
        return $date;
    }
}

if (!function_exists('isValidDate')) {
    function isValidDate($date = "null")
    {
        try {
            \Carbon\Carbon::parse($date);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}

if (!function_exists('getValidatorErrors')) {

    function getValidatorErrors($validator)
    {
        return implode("", $validator->errors()->all());
    }
}

if (!function_exists('getModelsJsonObj')) {

    function getModelsJsonObj($models, $json = "jsonObj", $options = [])
    {
        $arr = [];
        if ($models->count()) {

            foreach ($models as $value) {
                $arr[] = $value->$json($options);
            }
        }
        return $arr;
    }
}