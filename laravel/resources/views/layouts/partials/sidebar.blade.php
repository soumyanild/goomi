
<!-- BEGIN: Main Menu-->
<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item me-auto"><a class="navbar-brand" href="{{route('user.home')}}"><span class="brand-logo">
                <img src="{{asset('images/logo.png')}}">
                       </span>
                    <h2 class="brand-text">{{config('app.name')}}</h2>
                </a></li>
            <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pe-0" data-bs-toggle="collapse"><i class="d-block d-xl-none text-primary toggle-icon font-medium-4" data-feather="x"></i><i class="d-none d-xl-block collapse-toggle-icon font-medium-4  text-primary" data-feather="disc" data-ticon="disc"></i></a></li>
        </ul>
    </div>
    <div class="shadow-bottom"></div>
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            <li class=" nav-item {{request()->is('/') || request()->is('production')|| request()->is('cutting')?'active':''}}">
                <a class="d-flex align-items-center" href="{{ route('user.home') }}"><i data-feather="home"></i><span class="menu-title text-truncate" data-i18n="Dashboards">Dashboard</span></a>
            </li>

                <li class=" nav-item {{request()->is('users') || request()->is('users/*')?'active':''}}">
                    <a class="d-flex align-items-center" href="{{route('users.index')}}"><i data-feather="user"></i><span class="menu-title text-truncate" data-i18n="Users">Users</span></a>
                </li>

              <!--   <li class=" nav-item {{request()->is('role') || request()->is('role/*')?'active':''}}">
                    <a class="d-flex align-items-center" href="{{route('role.index')}}"><i data-feather="grid"></i><span class="menu-title text-truncate" data-i18n="Kanban">Roles</span></a>
                </li> -->

                {{-- <li class=" nav-item {{request()->is('page') || request()->is('page/*')?'active':''}}">
                    <a class="d-flex align-items-center" href="{{route('page.index')}}"><i data-feather="file"></i><span class="menu-title text-truncate" data-i18n="Page">Pages</span></a>
                </li>

                <li class=" nav-item {{request()->is('email-queue') || request()->is('email-queue/*')?'active':''}}">
                    <a class="d-flex align-items-center" href="{{route('email-queue.index')}}"><i class="fas fa-envelope"></i><span class="menu-title text-truncate" data-i18n="email-queue">Email Queue</span></a>
                </li> --}}

        
            
        </ul>
    </div>
</div>
<!-- END: Main Menu