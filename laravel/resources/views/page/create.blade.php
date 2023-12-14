@extends('layouts.admin')
@section('title') Create Page @endsection
@section('content')

   <!-- Basic multiple Column Form section start -->
                <section id="multiple-column-form">
                    <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{route('user.home')}}">Home</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="{{route('page.index')}}">Page</a>
                                    </li>
                                    <li class="breadcrumb-item active">Create Page
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
       
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Create Page</h4>
                                </div>
                                <div class="card-body">
                                  <form method="POST" action="{{ route('page.store') }}" autocomplete="off">
                                    @csrf
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="mb-1">
                                                    <label class="form-label" for="title">Title <span class="text-danger asteric-sign">&#42;</span></label>
                                                      <input id="title" type="text" class="form-control {{ $errors->has('title') ? ' is-invalid' : '' }}" name="title" value="{{ old('title') }}" placeholder="Full Name">
                                                        @if ($errors->has('title'))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('title') }}</strong>
                                                            </span>
                                                        @endif
                                                   
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-12">
                                                <div class="mb-1">
                                                    <label class="form-label" for="page_type">Page Type <span class="text-danger asteric-sign">&#42;</span></label>
                                                    <select class="form-control {{ $errors->has('page_type') ? ' is-invalid' : '' }}" id="page_type" name="page_type">
                                                    <option value="">Select Page Type</option>
                                                    @foreach($pageTypes as $key=>$value)
                                                       <option value="{{$key}}"{{old('page_type') == $key?"selected":""}}>{{$value}}</option>
                                                    @endforeach
                                                   </select>
                                                    @if ($errors->has('page_type'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('page_type') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-12">
                                                <div class="mb-1">
                                                    <label class="form-label" for="description">description </label>
                                                    <textarea id="description" name="description" rows="10" class="form-control {{ $errors->has('description') ? ' is-invalid' : '' }}" placeholder="Page Content"></textarea>
                                                    @if ($errors->has('description'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('description') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                           
                                         
                                            <div class="col-12">
                                                <button type="Submit" class="btn btn-primary me-1">Submit</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- Basic Floating Label Form section end -->



@push('page_script')
<script src="https://cdn.ckeditor.com/4.15.0/standard/ckeditor.js"></script>
<!-- <script src="{{ asset('vendor/unisharp/laravel-ckeditor/ckeditor.js') }}"></script> -->

    <script>
  
    CKEDITOR.replace( 'description', {
        filebrowserUploadUrl: "{{route('upload', ['_token' => csrf_token() ])}}",
        filebrowserUploadMethod: 'form'
    });
</script>
@endpush


@endsection