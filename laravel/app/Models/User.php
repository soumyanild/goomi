<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    const ROLE_ADMIN = 1;
    const ROLE_USER = 2;
    const BUSSINESS = 3;

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    const STATUS_PENDING = 1;
    const STATUS_APROVE = 2;
    const STATUS_REJECTED = 3;

    const GENDER_MALE = 1;
    const GENDER_FEMALE = 0;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */


    public function getUserRole()
    {
        return $this->belongsTo(Role::class, 'role');
    }

    public function isFollow()
    {

        $isFollow = Follow::where('receiver_id', $this->id)->where('sender_id', auth()->user()->id)->exists();
        if ($isFollow)
            return true;
        return false;
    }

    public function myFollowers()
    {

        $followers = Follow::where('receiver_id', auth()->user()->id)->count();
        if ($followers)
            return $followers;
        return 0;
    }

    public function myFollowing()
    {

        $following = Follow::where('sender_id', auth()->user()->id)->count();
        if ($following)
            return $following;
        return 0;
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function getProfileImageAttribute($value)
    {
        return !empty($value) ? asset($value) : null;
    }

    public function saveNewUser($inputArr)
    {
        return self::create($inputArr);
    }
    public function findUserById($id)
    {
        return self::find($id);
    }
    public function updateUserById($id, $inputArr)
    {
        return self::where('id', $id)->update($inputArr);
    }

    public function getStatus()
    {

        $list = [
            self::STATUS_ACTIVE => "Active",
            self::STATUS_INACTIVE => "Inactive"
        ];

        return isset($list[$this->status]) ? $list[$this->status] : "Not defined";
    }

    public function getStatusBadge()
    {

        $list = [
            self::STATUS_ACTIVE => "primary",
            self::STATUS_INACTIVE => "danger"
        ];

        return isset($list[$this->status]) ? $list[$this->status] : "danger";
    }

    public function getRole()
    {

        $list = [
            self::ROLE_ADMIN => "Admin",
            self::ROLE_USER => "User"
        ];

        return isset($list[$this->role]) ? $list[$this->role] : "Not defined";
    }


    public static function getColumnForSorting($value)
    {

        $list = [
            0 => 'id',
            1 => 'full_name',
            2 => 'email',
            3 => 'status',
            4 => 'created_at'
        ];

        return isset($list[$value]) ? $list[$value] : "";
    }

    public function getAllUsers($request = null, $flag = false)
    {
        if (isset($request['order'])) {
            $columnNumber = $request['order'][0]['column'];
            $order = $request['order'][0]['dir'];
        } else {
            $columnNumber = 4;
            $order = "desc";
        }

        $column = self::getColumnForSorting($columnNumber);
        if ($columnNumber == 0) {
            $order = "desc";
        }

        if (empty($column)) {
            $column = 'id';
        }
        $query = self::orderBy($column, $order)->where('role', '!=', self::ROLE_ADMIN);


        if (!empty($request)) {

            $search = $request['search']['value'];

            if (!empty($search)) {
                $query->where(function ($query) use ($request, $search) {
                    $query->orWhere('full_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('email', 'LIKE', '%' . $search . '%')
                        ->orWhere('created_at', 'LIKE', '%' . $search . '%');
                });

                if (empty(strcasecmp("Inactive", $search))) {
                    $query->orWhere('status', 0);

                }
                if (empty(strcasecmp("Active", $search))) {
                    $query->orWhere('status', 1);

                }

                // if(is_int(stripos("Inactive", $search))){
                //           $query->orWhere( 'status',  0);

                //       }
                // if(is_int(stripos("Active", $search))){
                //            $query->orWhere( 'status',  1);

                //        }


                if ($flag)
                    return $query->count();
            }

            $start = $request['start'];
            $length = $request['length'];
            $query->offset($start)->limit($length);


        }

        $query = $query->get();
        return $query;
    }

    public function generateEmailVerificationOtp()
    {
        // $otp = 1234;
        // return $otp;

        $otp = mt_rand(1000, 9999);
        // $otp = 123456;
        $count = self::where('email_verification_otp', $otp)->count();
        if ($count > 0) {
            $this->generateEmailVerificationOtp();
        }
        return $otp;
    }

    public function jsonResponse($flag = false)
    {

        $json['id'] = $this->id;
        $json['full_name'] = $this->full_name;
        $json['username'] = $this->username;
        $json['email'] = $this->email;
        $json['phone_number'] = $this->phone_number;
        $json['phone_code'] = $this->phone_code;
        $json['iso_code'] = $this->iso_code;
        $json['profile_image'] = $this->profile_image;
        $json['role'] = $this->role;
        $json['status'] = $this->status;
        $json['email_verification_otp'] = $this->email_verification_otp;
        $json['notification'] = $this->notification;
        $json['email_notification'] = $this->email_notification;
        $json['email_verified_at'] = $this->email_verified_at;
        $json['type'] = $this->type;
        $json['gender'] = $this->gender;
        $json['gender'] = $this->gender;
        $json['gender_title'] = ($this->gender == 1) ? "Male" : "Female";
        if (!empty(auth()->user())) {
            $json['my_followers'] = $this->myFollowers();
            $json['my_following'] = $this->myFollowing();
        }
        $json['created_at'] = $this->created_at->toDateTimeString();
        $json['updated_at'] = $this->updated_at->toDateTimeString();

        if ($flag) {
            $json['documnets'] = $this->documnets;
        }

        return $json;
    }
    public function loginSignupResponse($flag = false)
    {
        $json['id'] = $this->id;
        $json['email_verification_otp'] = $this->email_verification_otp;
        $json['role'] = $this->role;
        $json['phone_number'] = $this->phone_number;
        $json['phone_code'] = '+' . $this->phone_code;
        if ($flag) {
            $json['documents'] = $this->documnets;
        }
        return $json;
    }

    public function documnets()
    {
        return $this->hasMany(UserDocuments::class, 'user_id', 'id');
    }

    public function isSuperAdmin()
    {

        return ($this->created_by == 0);
    }

    public static function getMonthlyUsersRegistered()
    {
        $date = new \DateTime(date('Y-m'));

        $date->modify('-8 months');

        $count = [];
        for ($i = 1; $i <= 8; $i++) {
            $date->modify('+1 months');
            $month = $date->format('Y-m');
            $displayMonth = $date->format("M");

            $userCount = self::where('created_by', '!=', 0)->where('created_at', 'like', '%' . $month . '%')->count();

            $count['month'][$i] = $displayMonth;
            $count['users'][$i] = $userCount;

        }
        return $count;
    }

    public static function getActiveInactiveCount()
    {

        $data[] = [
            'name' => 'Active User',
            'y' => self::where(['status' => self::STATUS_ACTIVE])->where('role', self::ROLE_USER)->count(),
            'sliced' => true,
            'selected' => true,
            'color' => '#7367f0'
        ];
        $data[] = [
            'name' => 'Inactive User',
            'y' => self::where(['status' => self::STATUS_INACTIVE])->where('role', self::ROLE_USER)->count(),
            'color' => "#212529"

        ];

        return json_encode($data);
    }

    public static function getPaginateObj($paginate, $jsonObj = "jsonObj")
    {

        $items = $paginate->items();
        $json = [];
        foreach ($items as $item) {
            $json[] = $item->$jsonObj();
        }
        return [
            "list" => $json,
            "current_page" => $paginate->currentPage(),
            "next_page" => $paginate->nextPageUrl(),
            "last_page" => $paginate->lastPage(),
            "per_page" => $paginate->perPage(),
            "total" => $paginate->total(),
        ];
    }

    public function jsonObj()
    {
        $jsonData = $this->jsonResponse();
        $json['id'] = $jsonData['id'];
        $json['full_name'] = $jsonData['full_name'];
        $json['email'] = $this->email;
        $json['username'] = $this->username;
        $json['profile_image'] = $jsonData['profile_image'];
        $json['role'] = $jsonData['role'];
        $json['is_follow'] = $this->isFollow();

        return $json;
    }

    public function getApproveUsers()
    {
        if (!empty($this->type)) {
            return $this->type == 1 ? 'Pending' : ($this->type == 2 ? 'Approve' : ($this->type == 3 ? 'Rejected' : 'Unknown'));
        } else {
            return "Email not verified yet";
        }
    }

    // public static function checkPhoneNumberExist($phoneNumber)
    // {
    //     $phoneNumber = 
    //     return !empty($phoneNumber) ? true : false;
    // }

}
