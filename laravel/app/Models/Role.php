<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\RolePermission;
use App\Models\Permission;



class Role extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
    	'title',
    	'is_deleteable',
    	'created_by'
    ];

	public function rolePermissions(){
        return $this->hasMany(RolePermission::class);
    }

    public function getAllRoles(){

    	return self::get();
    }

    public static function createRole($title){

    	$role = new Role;
    	$role->title = $title;
    	$role->created_by = auth()->user()->id;
    	if($role->save()){

    		$permissions = Permission::get();

    		foreach ($permissions as $key => $permission) {

    			$rolePermission = new RolePermission;
    			$rolePermission->role_id = $role->id;
    			$rolePermission->permission_id = $permission->id;
    			$rolePermission->created_by = $role->created_by;
    			$rolePermission->save();


    		}

    	return $role->id;


    	}

    	return false;


    }
}
