@extends('layouts.admin')

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
                                     <li class="breadcrumb-item"><a href="{{route('page.index')}}">Page</a>
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
              
                          <th>Id</th>
                          <td colspan="1">{{$model->id}}</td>
                           <th>Title</th>
                            <td colspan="1">{{$model->title}}</td>
                        </tr>
                         <tr>
                            <th>Page Type</th>
                            <td colspan="1">{{$model->getPageType()}}</td>
                            <th>Created At</th>
                            <td colspan="1">{{$model->created_at}}</td>                           
                          </tr>
                                
                           </tbody>
                           </table>

                           <br>
                          {!!$model->description!!}


                           <div class="row"> 
                            <div class="col-md-12 text-center">
                            <a id="tool-btn-manage"  class="btn btn-primary text-right" href="{{route('page.index')}}" title="Back">Back</a>
                          
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
