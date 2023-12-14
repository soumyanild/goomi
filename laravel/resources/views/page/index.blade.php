@extends('layouts.admin')
@section('title')Pages @endsection
@section('content')


  
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="content-header-left col-md-9 col-12 mb-2">
          <div class="row breadcrumbs-top">
              <div class="col-12">
                  <div class="breadcrumb-wrapper">
                      <ol class="breadcrumb">
                          <li class="breadcrumb-item"><a href="{{route('user.home')}}">Home</a>
                          </li>
                          <li class="breadcrumb-item"><a href="{{route('page.index')}}">Pages</a>
                          </li>
                          <li class="breadcrumb-item active">Page List
                          </li>
                      </ol>
                  </div>
              </div>
          </div>
      </div>


      <div class="row">
          <div class="col-12">
            <div class="card data-table">
               <div class="card-header">
                  <div class="heading-text">
                    <h4 class="m-0"><i class="fas fa-file mr-2"></i>&nbsp;{{ __('Pages') }}</h4>
                  </div>

                  <div class="right-side mr-2">



                <a href="{{ route('page.create') }}" class="dt-button btn btn-primary"><i class="fas fa-plus"></i>&nbsp;&nbsp;Create New Page</a>

              </div>
              </div>
            
              <!-- /.card-header -->
              <div class="card-body">
                <table id="pageTable" class="table table-bordered table-hover">
                  <thead>
                  <tr>
                    <th>S.No</th>
                    <th>Title</th>
                    <th>Page Type</th>
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
      <!-- /.container-fluid -->
    </section>  

  @push('page_script')

      @include('include.dataTableScripts')   

      <script src="{{ asset('js/pages/page/index.js') }}"></script>

  @endpush

       
@endsection