@extends('layouts.admin')

@section('title') User @endsection

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
                                     <li class="breadcrumb-item"><a href="{{route('users.index')}}">User</a>
                                    </li>
                                    <li class="breadcrumb-item active">View
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
              </div>


        <div class="row">
          <div class="col-12">
            <div class="card">
  
              <!-- /.card-header -->
              <div class="card-body">

                     <table id="w0" class="table table-striped table-bordered detail-view">
                      <tbody>
                        <tr>
              
                          <th>Full Name</th>
                          <td colspan="1">{{$userObj->full_name}}</td>
                           <th>Email</th>
                            <td colspan="1"><a @if($userObj->email) href="mailto:jashely775@gmail.com" @endif>{{$userObj->email ?? "Not Set"}}</a></td>
                        </tr>
                         <tr>
                           <th>Status</th>
                            <td colspan="1"><span class="badge badge-light-{{$userObj->getStatusBadge()}}">{{$userObj->getStatus()}}</span>
                                 <th>Role</th>
                                    <td colspan="1">{{$userObj->getRole()}}</td>
                           
                          </tr>
                                  <tr>
                    
                                   
                                      <th>Created At</th>
                                    <td colspan="1">{{$userObj->created_at}}</td>
                                    
                                  </tr>

                                    
                               </tbody>
                           </table>
                           <br>

                           <div class="row"> 
                            <div class="col-md-12 text-center">
                            <a id="tool-btn-manage"  class="btn btn-primary text-right" href="{{route('users.index')}}" title="Back">Back</a>
                           
                            <a href="{{route('user.changeStatus',encrypt($userObj->id))}}" class="active_status btn btn-{{($userObj->status ==1)?'danger':'primary'}}"  data-id = {{encrypt($userObj->id)}} title="Manage">{{($userObj->status == 1)?"Inactive":"Active"}}</i></a>
                            </div>
                </div>


              
              </div>
          
              <!-- /.card-body -->

            </div>



            <!-- /.card -->
        </div>
       </div>   




      
 
</section>



@endsection
