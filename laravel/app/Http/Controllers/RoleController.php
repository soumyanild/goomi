<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use App\Models\Permission;
use App\Models\RolePermission;
use Illuminate\Support\Facades\Response;



class RoleController extends Controller
{
    protected $usersPermissionArr;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
          $usersPermissionArr =  hasPermission(MANAGE_ROLES);
         $this->usersPermissionArr = $usersPermissionArr;
         return $next($request);
        });
    }

    public function index(Request $request, Role $role)
    {
        $isRead =  $this->usersPermissionArr['is_read'];
        $isWrite =  $this->usersPermissionArr['is_write'];
        $isCreate =  $this->usersPermissionArr['is_create'];

        if(!$isRead && !$isWrite && !$isCreate)
            return redirect()->back()->with('error',"You are not allowed to perform this action.");

        if ($request->ajax()) {
            $roles = $role->getAllRoles();
            return datatables()
                   ->of($roles)
                   ->addIndexColumn()
                   ->addColumn('created_at', function ($role) {
                       return $role->created_at;
                   })
                   ->addColumn('action', function ($role)use($isWrite) {
                        $btn = '';
                        $btn = '<a  href="javascript:void(0);" title="Edit" class="edit-role" data-id="' .encrypt($role->id). '"><i class="fas fa-edit ml-1"></i></a>&nbsp;&nbsp;';
                    
                        if(($role->is_deleteable ==1) && ($isWrite)){
                            $btn .= '<a href="javascript:void(0);" delete_form="delete_customer_form"  data-id="' .$role->id. '" class="delete-datatable-record text-danger delete-roles-record" title="Delete"><i class="fas fa-trash ml-1"></i></a>';
                        }
                        return $btn;
                    })
                    ->rawColumns([
                        'action'  
                    ])
                    ->make(true);
        }
        $permissions = Permission::get();
        return view('role.index',compact("permissions","isCreate"));
    }

    public function edit($id)
    {
        $rolesArray = [];
        $id = decrypt($id);
        $model = Role::find($id);
        $rolePermissions = RolePermission::select('permission_id','is_creatable','is_writable','is_readable')->where(['role_id'=>$id])->get();

        foreach ($rolePermissions as $key => $rolePermission) {

            $rolesArray[$rolePermission->permission_id]['is_readable'] = $rolePermission->is_readable;
            $rolesArray[$rolePermission->permission_id]['is_writable'] = $rolePermission->is_writable;
            $rolesArray[$rolePermission->permission_id]['is_creatable'] = $rolePermission->is_creatable;

        }
        return returnSuccessResponse('Role deleted successfully',["id"=>encrypt($id),"title"=>$model->title,"rolePermissions"=>$rolesArray]);

       
    }


    public function createRole(Request $request){

        $isCreate =  $this->usersPermissionArr['is_create'];

        if(!$isCreate)
            return forbiddenResponse('You are not allowed to perform this action.');

        $rules = array(
            'title' => 'required|unique:roles,title,Null,id,deleted_at,NULL',
            'permissions'=>'required'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
        'success' => false,
        'errors' => $validator->getMessageBag()->toArray()

         ), 400); // 400 being the HTTP code for an invalid request.
        } 

        $model = new Role;
        $model = $model->fill($request->all());
        $model->created_by = auth()->user()->id;
       
        if ($model->save()) {

            foreach ($request->permissions as $key => $value) {
              
                $rolePermission = new RolePermission;
                $rolePermission->role_id = $model->id;
                $rolePermission->permission_id = $value['permission'];
                $rolePermission->is_readable = !empty($value['is_readable'])?$value['is_readable']:0;
                $rolePermission->is_writable = !empty($value['is_writable'])?$value['is_writable']:0;
                $rolePermission->is_creatable = !empty($value['is_creatable'])?$value['is_creatable']:0;
                $rolePermission->created_by = auth()->user()->id;
                $rolePermission->save();
  
        } 
        
        }
    return returnSuccessResponse('Role created successfully');

    }

     public function updateRole(Request $request){

        $isWrite =  $this->usersPermissionArr['is_write'];

        if(!$isWrite)
            return forbiddenResponse('You are not allowed to perform this action.');

        $id = decrypt($request->role_id);

        $rules = array(
            'role_id'=>"required",
            'title' => 'required|unique:roles,title,'.$id.',id,deleted_at,NULL',
            'permissions'=>'required'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return Response::json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()

            ), 400); // 400 being the HTTP code for an invalid request.
        } 


        $model =Role::find($id);
        if(empty($model))
            return returnErrorResponse('Something went wrong. Please try again later');

        $model->title = $request->title;
       
        if ($model->save()) {

            foreach ($request->permissions as $key => $value) {

                $rolePermission = RolePermission::where(['role_id'=>$model->id,'permission_id'=>$value['permission']])->first();
                if(empty($rolePermission))
                 $rolePermission = new RolePermission;

                $rolePermission->role_id = $model->id;
                $rolePermission->permission_id = $value['permission'];
                $rolePermission->is_readable = !empty($value['is_readable'])?$value['is_readable']:0;
                $rolePermission->is_writable = !empty($value['is_writable'])?$value['is_writable']:0;
                $rolePermission->is_creatable = !empty($value['is_creatable'])?$value['is_creatable']:0;
                $rolePermission->created_by = auth()->user()->id;
                $rolePermission->save();
  
            } 
        
        }
        return returnSuccessResponse('Role updated successfully');
    }

    public function destroy($id)
    {
        $role = Role::find($id);
        
        if(!$role){
            return returnNotFoundResponse('This user does not exist');
        }

        $checkIfExist = User::where(['role'=>$role->id])->count();

        if($checkIfExist)
            return returnErrorResponse('One or more user exists with this role.');

        
        $hasDeleted = $role->delete();
        if($hasDeleted){
            return returnSuccessResponse('Role deleted successfully');
        }
        
        return returnErrorResponse('Something went wrong. Please try again later');
    }
}
