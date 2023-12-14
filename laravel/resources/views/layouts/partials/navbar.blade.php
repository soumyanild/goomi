<!-- BEGIN: Header-->
@php
    $userObj = auth()->user();
@endphp
<nav class="header-navbar navbar navbar-expand-lg align-items-center floating-nav navbar-light navbar-shadow container-xxl">
    <div class="navbar-container d-flex content">
        <div class="bookmark-wrapper d-flex align-items-center">
            <ul class="nav navbar-nav d-xl-none">
                <li class="nav-item">
                    <a class="nav-link menu-toggle" href="#"><i class="ficon" data-feather="menu"></i></a>
                </li>
            </ul>
        </div>
        <!-- <div class="bookmark-wrapper d-flex align-items-center">
            <ul class="nav navbar-nav">
                <li class="nav-item nav-search">
                    <div class="input-group input-group-merge ms-1 w-100 global_search">
                        <span class="input-group-text round"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search text-muted"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></span>
                        <input type="text" class="form-control round" id="global_search" placeholder="Click here to scan" aria-label="Search..." autofocus>
                    </div>
                </li>
            </ul>
        </div> -->
        <ul class="nav navbar-nav align-items-center ms-auto">
            <li class="nav-item dropdown dropdown-user">
                <a class="nav-link dropdown-toggle dropdown-user-link" id="dropdown-user" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <div class="user-nav d-sm-flex d-none">
                        <span class="user-name fw-bolder">{{$userObj->full_name ? $userObj->full_name : $userObj->first_name}}</span>
                        <span class="user-status">{{$userObj->getUserRole->title??''}}</span>
                    </div>
                    <span class="avatar">
                        <img class="round" src="{{$userObj->profile_image ? $userObj->profile_image : asset('images/theme/portrait/small/avatar-s-11.jpg')}}" alt="avatar" height="40" width="40">
                        <span class="avatar-status-online"></span>
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown-user">
                    <a class="dropdown-item" href="{{route('user.profile')}}"><i class="me-50" data-feather="user"></i> Profile</a>
                    <a class="dropdown-item" href="{{route('user.updateProfile')}}"><i class="me-50" data-feather="edit"></i> Update Profile</a>
                    <a class="dropdown-item" href="{{route('user.changePassword')}}"><i class="me-50" data-feather="key"></i> Change Password</a>
                    
                    <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="me-50" data-feather="power"></i> Logout</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </li>
        </ul>
    </div>
</nav>
<!-- END: Header-->