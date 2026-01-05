<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="keywords" content="">
        <meta name="author" content="">
        <meta name="robots" content="">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('constant.app_name') }}</title>
        
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/img/favicon.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/apple-touch-icon.png') }}">
        
        <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/animate.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/fontawesome.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/all.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/toast.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/summernote.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('custom.css') }}">
        <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    </head>
    <body>
        <div class="main-wrapper">
            <div class="header">
                <div class="main-header">
                    <div class="header-left active">
                        <a href="{{ route('admin.dashboard') }}" class="logo logo-normal">
                            <img src="{{ asset('assets/img/logo.svg') }}" alt="Img">
                        </a>
                        <a href="{{ route('admin.dashboard') }}" class="logo logo-normal">
                            <img src="{{ asset('assets/img/logo-white.svg') }}" alt="Img">
                        </a>
                        <a href="{{ route('admin.dashboard') }}" class="logo logo-normal">
                            <img src="{{ asset('assets/img/logo-small.png') }}" alt="Img">
                        </a>
                    </div>
                    <a id="mobile_btn" class="mobile_btn" href="#sidebar">
                        <span class="bar-icon">
                            <span></span>
                            <span></span>
                            <span></span>
                        </span>
                    </a>
                    <ul class="nav user-menu">
                        <!-- Search -->
                        <li class="nav-item nav-searchinputs" style="visibility: hidden;">
                            <div class="top-nav-search">
                                <a href="javascript:void(0);" class="responsive-search">
                                    <i class="fa fa-search"></i>
                                </a>
                            </div>
                        </li>
                        <!-- /Search -->
                        <li class="nav-item nav-item-box">
                            
                        </li>
                        <li class="nav-item dropdown has-arrow main-drop profile-nav">
                            <a href="javascript:void(0);" class="nav-link userset" data-bs-toggle="dropdown">
                                <span class="user-info p-0">
                                    <span class="user-letter">
                                        <img src="{{ asset('assets/img/profiles/avator1.jpg') }}" alt="Img" class="img-fluid">
                                    </span>
                                </span>
                            </a>
                            <div class="dropdown-menu menu-drop-user">
                                <div class="profileset d-flex align-items-center">
                                    <!-- <span class="user-img me-2">
                                        <img src="/assets/img/profiles/avator1.jpg" alt="Img">
                                    </span> -->
                                    <div>
                                        <h6 class="fw-medium">{{ Auth::user()->name }}</h6>
                                        <p>{{ Auth::user()->email }}</p>
                                    </div>
                                </div>
                                <hr class="my-2">
                                <a class="dropdown-item logout pb-0" href="{{ route('admin.logout') }}"><i class="ti ti-logout me-2"></i>Logout</a>
                            </div>
                        </li>
                    </ul>
                    <div class="dropdown mobile-user-menu">
                        <a href="javascript:void(0);" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"
                            aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="{{ route('admin.logout') }}">Logout</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="sidebar" id="sidebar">
                <div class="sidebar-logo">
                    <a href="{{ route('admin.dashboard') }}" class="logo logo-normal">
                        <img src="{{ asset('assets/img/logo.svg') }}" alt="Img">
                    </a>
                    <a href="{{ route('admin.dashboard') }}" class="logo logo-white">
                        <img src="{{ asset('assets/img/slps-logo.svg') }}" alt="Img">
                    </a>
                    <a href="{{ route('admin.dashboard') }}" class="logo-small">
                        <img src="{{ asset('assets/img/slps-logo.svg') }}" alt="Img">
                    </a>
                    <a id="toggle_btn" href="javascript:void(0);">
                        <i data-feather="chevrons-left" class="feather-16"></i>
                    </a>
                </div>
                <div class="modern-profile p-3 pb-0"></div>
                <div class="sidebar-inner slimscroll">
                    <div id="sidebar-menu" class="sidebar-menu">
                        <ul>
                            <li class="submenu-open">
                                <h6 class="submenu-hdr">Main</h6>
                                <ul id="main_menu_list">
                                    <li><a href="{{ route('admin.dashboard') }}"><i data-feather="box"></i><span>Dashboard</span></a></li>
                                </ul>
                            </li>
                            <li class="submenu-open">
                                <h6 class="submenu-hdr">Other</h6>
                                <ul id="main_menu_list">
                                    <li><a href="{{ url('pdfs') }}"><i data-feather="box"></i><span>PDF List</span></a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="page-wrapper">
                <div class="content">
                    @yield('content')
                </div>
            </div>
        </div>
        <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
        <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('assets/js/feather.min.js') }}"></script>
        <script src="{{ asset('assets/js/jquery.slimscroll.min.js') }}"></script>
        <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('assets/js/toast.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs4.min.js"></script>
        <script src="{{ asset('assets/js/script.js') }}"></script>
        <script src="{{ asset('assets/js/summernote.js') }}"></script>
        <script src="{{ asset('custom.js') }}"></script>
        <script>
            $(document).ready(function(){
                $("#groom_side").change(function(){
                    $(this).parent().trigger("submit");
                });
            });
            function rand(min, max) {
                return Math.floor(Math.random() * (max - min + 1)) + min;
            }
            function show_toast(title,msg,type,second = 3000)
            {
                $.toast({
                    heading: title,
                    text: msg,
                    showHideTransition: 'fade',
                    icon: type,
                    position: 'top-right',
                    hideAfter: second
                });
            }
        </script>
    </body>
</html>