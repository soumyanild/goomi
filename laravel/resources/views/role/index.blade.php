@extends('layouts.admin')

@section('title')Roles @endsection

@section('content')
 
           

    <!-- Main content -->
    <section>
        <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{route('user.home')}}">Home</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="{{route('role.index')}}">Roles</a>
                                    </li>
                                    <li class="breadcrumb-item active">Roles List
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
      <div>

        <div class="row">
          <div class="col-12">
            <div class="card data-table">
               <div class="card-header">
                  <h4 class="m-0"><i class="fas fa-users mr-2"></i>&nbsp;{{ __('Roles') }}</h4>
                  @if($isCreate)
                  <a href="javascript:void(0)" id="create-role-button" data-bs-target="#addRoleModal" data-bs-toggle="modal" class="dt-button create-new btn btn-primary">

               <i class="fas fa-plus"></i>&nbsp;&nbsp;Create New Role</a>
               @endif
              </div>
            
              <!-- /.card-header -->
              <div class="card-body">
                <table id="roleTable" class="table table-bordered table-hover">
                  <thead>
                  <tr>
                    <th>S.No</th>
                    <th>Title</th>
                    <th>Created At</th>
                    <th data-orderable="false">Action</th>
                  </tr>
                  </thead>
              
                </table>
              </div>
          
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
    </div>
       </div>    
      </div>
      <!-- /.container-fluid -->
    </section>

     <!-- Create Role Modal -->
                <div class="modal fade" id="addRoleModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered modal-add-new-role">
                        <div class="modal-content">
                            <div class="modal-header bg-transparent">
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body px-5 pb-5">
                                <div class="text-center mb-4">
                                    <h1 class="role-title">Create New Role</h1>
                                    <p>Set role permissions</p>
                                </div>
                                <!-- Add role form -->
                                <form id="createRole">
                                    <div class="col-12">
                                        <label class="form-label" for="modalRoleName">Role Title</label>
                                        <input type="text" id="title" name="title" class="form-control" placeholder="Enter role title" tabindex="-1" />
                                          <span class="invalid-feedback" role="alert">
                                          <strong class="title_error"></strong>
                                      </span>
                                    </div>
                                    <div class="col-12">
                                        <h4 class="mt-2 pt-50">Role Permissions</h4>
                                        <!-- Permission table -->
                                        <div class="table-responsive">
                                            <table class="table table-flush-spacing">
                                                <tbody>
                                                    <tr>
                                                        <td class="text-nowrap fw-bolder">
                                                            Administrator Access
                                                            <span data-bs-toggle="tooltip" data-bs-placement="top" title="Allows a full access to the system">
                                                                <i data-feather="info"></i>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" id="selectAll" />
                                                                <label class="form-check-label" for="selectAll"> Select All </label>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @foreach($permissions as $key=>$permission)
                                                    <tr>
                                                        <td class="text-nowrap fw-bolder">{{$permission->title}}</td>
                                                        <td>
                                                            <div class="d-flex">
                                                                <div class="form-check me-3 me-lg-5">
                                                                  <input type="hidden" name="permissions[{{$key}}][permission]" value="{{$permission->id}}">
                                                                    <input class="form-check-input create-role-permission" type="checkbox" id="is_readable_{{$permission->id}}" name="permissions[{{$key}}][is_readable]" value="1" />
                                                                    <label class="form-check-label" for="is_readable_{{$permission->id}}"> View </label>
                                                                </div>
                                                                <div class="form-check me-3 me-lg-5">
                                                                    <input class="form-check-input create-role-permission" type="checkbox" id="is_writable_{{$permission->id}}" name="permissions[{{$key}}][is_writable]" value="1" />
                                                                    <label class="form-check-label" for="is_writable_{{$permission->id}}"> Edit/Delete </label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input create-role-permission" type="checkbox" id="is_creatable_{{$permission->id}}" name="permissions[{{$key}}][is_creatable]" value="1" />
                                                                    <label class="form-check-label" for="is_creatable_{{$permission->id}}"> Create </label>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                   
                                                </tbody>
                                            </table>
                                        </div>
                                        <!-- Permission table -->
                                    </div>
                                    <div class="col-12 text-center mt-2">
                                        <button  class="btn btn-primary me-1" id="create-role">Submit</button>
                                        <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="modal" aria-label="Close">
                                            Discard
                                        </button>
                                    </div>
                                </form>
                                <!--/ Add role form -->
                            </div>
                        </div>
                    </div>
                </div>
                <!--/ Create  Role Modal -->




                 <!-- Update Role Modal -->
                <div class="modal fade" id="updateRoleModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered modal-add-new-role">
                        <div class="modal-content">
                            <div class="modal-header bg-transparent">
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body px-5 pb-5">
                                <div class="text-center mb-4">
                                    <h1 class="role-title">Update Role</h1>
                                    <p>Set role permissions</p>
                                </div>
                                <!-- Add role form -->
                                <form id="updateRole">
                                    <div class="col-12">
                                          <input type="hidden" name="role_id" id="role_id" value="">

                                        <label class="form-label" for="modalRoleName">Role Title</label>
                                        <input type="text" id="update_title" name="title" class="form-control" placeholder="Enter role title" tabindex="-1" />
                                          <span class="invalid-feedback" role="alert">
                                          <strong class="update_title_error"></strong>
                                      </span>
                                    </div>
                                    <div class="col-12">
                                        <h4 class="mt-2 pt-50">Role Permissions</h4>
                                        <!-- Permission table -->
                                        <div class="table-responsive">
                                            <table class="table table-flush-spacing">
                                                <tbody>
                                                    <tr>
                                                        <td class="text-nowrap fw-bolder">
                                                            Administrator Access
                                                            <span data-bs-toggle="tooltip" data-bs-placement="top" title="Allows a full access to the system">
                                                                <i data-feather="info"></i>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" id="updateSelectAll" />
                                                                <label class="form-check-label" for="selectAll"> Select All </label>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @foreach($permissions as $key=>$permission)
                                                    <tr>
                                                        <td class="text-nowrap fw-bolder">{{$permission->title}}</td>
                                                        <td>
                                                            <div class="d-flex">
                                                                <div class="form-check me-3 me-lg-5">
                                                                  <input type="hidden"  name="permissions[{{$key}}][permission]" value="{{$permission->id}}">
                                                                    <input id="is_readable_update_{{$permission->id}}" class="form-check-input update-role-permission" type="checkbox"  name="permissions[{{$key}}][is_readable]" value="1" />
                                                                    <label class="form-check-label" for="is_readable_update_{{$permission->id}}"> View </label>
                                                                </div>
                                                                <div class="form-check me-3 me-lg-5">
                                                                    <input id="is_writable_update_{{$permission->id}}" class="form-check-input update-role-permission" type="checkbox"  name="permissions[{{$key}}][is_writable]" value="1" />
                                                                    <label class="form-check-label" for="is_writable_update_{{$permission->id}}"> Edit/Delete </label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input id="is_creatable_update_{{$permission->id}}" class="form-check-input update-role-permission" type="checkbox"  name="permissions[{{$key}}][is_creatable]" value="1" />
                                                                    <label class="form-check-label" for="is_creatable_update_{{$permission->id}}"> Create </label>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                   
                                                </tbody>
                                            </table>
                                        </div>
                                        <!-- Permission table -->
                                    </div>
                                    <div class="col-12 text-center mt-2">
                                        <button  class="btn btn-primary me-1" id="update-role">Update</button>
                                        <button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="modal" aria-label="Close">
                                            Discard
                                        </button>
                                    </div>
                                </form>
                                <!--/ Add role form -->
                            </div>
                        </div>
                    </div>
                </div>
                <!--/ Add Role Modal -->  
  

  @push('page_script')

      @include('include.dataTableScripts')   


      <script src="{{ asset('js/pages/role/index.js') }}"></script>
      <script src="{{ asset('js/pages/role/createRole.js') }}"></script>



  @endpush

       
@endsection