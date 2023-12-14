@extends('layouts.admin')

@section('title') View Profile @endsection

@section('content')

 <section>

   <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{route('user.home')}}">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active">Profile
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

                <div class="row">
                    <div class="col-md-3">

                      <img src="{{$userObject->profile_image ? $userObject->profile_image : asset('images/theme/portrait/small/avatar-s-11.jpg')}}" style="width:100%;">

                    </div>

                    <div class="col-md-9">
                        <div class="table-responsive">
                            <table id="w0" class="table table-striped table-bordered detail-view">
                                <tbody>
                       
                      
                        <tr>
                            <th>Name</th>
                            <td colspan="1">{{$userObject->full_name}}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td colspan="1">{{$userObject->email}}</td>
                            
                         </tr>
                         <tr>
                            <th>Role</th>
                            <td colspan="1">{{$userObject->getRole()}}</td>
                            
                         </tr>
                       
                 </tbody>
             </table>

         </div> 
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