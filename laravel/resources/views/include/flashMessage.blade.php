@if (session('success_message'))
<div class="alert alert-success alert-block">
        <strong>{{ session('success_message') }}</strong>
          <button type="button" class="close" data-dismiss="alert">×</button> 

</div>
@endif


@if (session('error_message'))
<div class="alert alert-danger alert-block">
        <strong>{{ session('error_message') }}</strong>
        <button type="button" class="close" data-dismiss="alert">×</button> 
</div>
@endif


@if (session('warning_message'))
<div class="alert alert-warning alert-block">
  <button type="button" class="close" data-dismiss="alert">×</button> 
  <strong>{{ session('warning_message') }}</strong>
</div>
@endif


@if (session('info_message'))
<div class="alert alert-info alert-block">
  <button type="button" class="close" data-dismiss="alert">×</button> 
  <strong>{{ session('info_message') }}</strong>
</div>
@endif

@if (session('imported_data_messages') && is_array(session('imported_data_messages')) && count(session('imported_data_messages')) > 0)
  <div class="alert alert-info alert-block">
    <ol>
      @foreach(session('imported_data_messages') as $key => $message)
        @if($key == 0)
          <h4 class="text-info mt-1 mb-1">{{$message}}</h4>
        @else
          <li><strong>{{ $message }}</strong></li>
        @endif
      @endforeach
    </ol>
    <button type="button" class="close" data-dismiss="alert">×</button>
  </div>
@endif


<!-- @if ($errors->any())
<div class="alert alert-danger">
  Please check the form below for errors 
    <button type="button" class="close" data-dismiss="alert">×</button> 

</div>
@endif -->