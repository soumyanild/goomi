//To Enable Role and Permissions.

Add this function in User Seeder

 public function run()
    {
        $isAdminExist = DB::table('users')->where(['email' => "admin@yopmail.com"])->count();
        if(!$isAdminExist){
            $adminRole = DB::table('roles')->where(['is_deleteable' => 0])->first();
            DB::table('users')->insert([
                'full_name' => 'Admin',
                'email' => 'admin@yopmail.com',
                'phone_number' => '123456789',
                'role' => $adminRole ? $adminRole->id : 0,
                'password' => Hash::make('admin@123')
            ]);
        }
    }

//Then Change Database Seeder Add this
 $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            UserSeeder::class
        ]);



//To Hide Side Bar on the bases of role and permissions use this code.
@php
    $isSuperAdmin = auth()->user()->created_by == 0 ? true : false;

    if(!$isSuperAdmin){
        $user = auth()->user();
        $userRole = $user->getUserRole;
        if(!empty($userRole)){
            $rolePermissions = $userRole->rolePermissions;
            $permissionTypesArr = [];
            foreach($rolePermissions as $key => $rolePermission){
                if($rolePermission->is_creatable || $rolePermission->is_writable || $rolePermission->is_readable){
                    $permissionObj = $rolePermission->permission;
                    array_push($permissionTypesArr, $permissionObj->type);
                }
            }
        }
    }
@endphp

 @if($isSuperAdmin || in_array(MANAGE_CUSTOMERS, $permissionTypesArr))
                <li class=" nav-item {{request()->is('customer') || request()->is('customer/*')?'active':''}}">
                    <a class="d-flex align-items-center" href="{{route('customer.index')}}"><i data-feather="user"></i><span class="menu-title text-truncate" data-i18n="Permission">Customers</span></a>
                </li>
@endif