<?php
$application_name = config('app.name');
$auth_user = (Auth::check()) ? Auth::user() : null;
$user_role_lables = config('constant.USER_ROLE_LABELS');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="author" content="">
    <title>@yield('pagetitle') : {{ $application_name }}</title>
    <!-- GLOBAL MAINLY STYLES-->
<link rel="stylesheet" href="{{ asset('/assets/app/vendors/bootstrap/dist/css/bootstrap.min.css') }}">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
<link rel="stylesheet" href="{{ asset('/assets/app/vendors/line-awesome/css/line-awesome.min.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/app/vendors/themify-icons/css/themify-icons.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/app/vendors/toastr/toastr.min.css') }}">
<link rel="stylesheet" href="{{ asset('https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css') }}">
<!-- PLUGINS STYLES-->
@yield('plugin-css')
<!-- THEME STYLES-->
<link rel="stylesheet" href="{{ asset('/assets/admin/css/main.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/admin/css/app.css') }}">
<!-- PAGE LEVEL STYLES-->
    @yield('page-css')
</head>
<body class="fixed-navbar">
<div class="page-wrapper">
    <!-- START HEADER-->
    <header class="header">
        <div class="page-brand">
            <a href="{{route('admin.home')}}">
                <span class="brand">{{ config('constant.APP_NAME_SHORT') }}</span>
                <span class="brand-mini">{{ config('constant.APP_NAME_SHORT') }}</span>
            </a>
        </div>
        <div class="flexbox flex-1">
            <!-- START TOP-LEFT TOOLBAR-->
            <ul class="nav navbar-toolbar">
                <li>
                    <a class="nav-link sidebar-toggler js-sidebar-toggler" href="javascript:;">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </a>
                </li>
            </ul>
            <!-- END TOP-LEFT TOOLBAR-->

            <!-- START TOP-RIGHT TOOLBAR-->
            <ul class="nav navbar-toolbar">
                <li class="dropdown dropdown-user">
                    <a class="nav-link dropdown-toggle link" data-toggle="dropdown">
                        <span>{{$auth_user->username}}</span>
                        <img src="{{$auth_user->photo}}" alt="image" style="width:50px;height: 50px;" />
                    </a>
                    <div class="dropdown-menu dropdown-arrow dropdown-menu-right admin-dropdown-menu">
                        <div class="dropdown-arrow"></div>
                        <div class="dropdown-header">
                            <div class="admin-profile">
                                <img width="100%" height="100%" class="img-circle profile-img" src="{{$auth_user->photo}}" alt="image">
                            </div>
                            <div>
                                <h5 class="font-strong text-white">{{$auth_user->fullname}}</h5>
                                <div>
                                    <span class="admin-badge mr-3">{{ $user_role_lables[$auth_user->role_id]['display_name'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="admin-menu-features px-3">
                            <div class="d-flex justify-content-between w-100">
                                 <div class="">
                                    <a class="dropdown-item" href="{{route('admin.profile.edit',$auth_user->user_id)}}"><i class="fa fa-user"></i>Profile</a>
                                 </div>   
                                 <div>
                                    <a class="dropdown-item d-flex align-items-center" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();document.getElementById('logout-form').submit();">Logout<i
                                                class="ti-shift-right ml-2 font-20"></i></a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                 </div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
            <!-- END TOP-RIGHT TOOLBAR-->
        </div>
    </header>
    <!-- END HEADER-->
    <!-- START SIDEBAR-->
    <nav class="page-sidebar" id="sidebar">
        <div id="sidebar-collapse">
            <ul class="side-menu metismenu">
                <li>
                    <a {!!(isset($menuChild) && $menuChild=='dashboard') ? "class='active'" : '' !!} href="{{ route('admin.home') }}">
                        <i class="sidebar-item-icon fas fa-tachometer-alt"></i>
                        <span class="nav-label">Dashboard</span>
                    </a>
                </li>
                <li {!!(isset($menuParent) && $menuParent=='items') ? " class='active'" : '' !!}>
                    <a href="#">
                        <i class="sidebar-item-icon fas fa-box-open"></i>
                        <span class="nav-label">Items</span><i class="fa fa-angle-left arrow"></i>
                    </a>
                    <ul class="nav-2-level collapse">
                        <li>
                            <a {!!(isset($menuChild) && $menuChild=='additem') ? "class='active'" : '' !!} href="{{route('admin.items.create')}}">Add
                                New Item</a>
                        </li>
                        <li>
                            <a {!!(isset($menuChild) && $menuChild=='items') ? "class='active'" : '' !!} href="{{route('admin.items.index')}}">Items</a>
                        </li>
                        <li>
                            <a {!!(isset($menuChild) && $menuChild=='item-markas') ? "class='active'" : '' !!} href="{{route('admin.item-marka.index')}}">Markas</a>
                        </li>
                    </ul>
                </li>
                <li {!!(isset($menuParent) && $menuParent=='customers') ? " class='active'" : '' !!}>
                    <a href="#">
                        <i class="sidebar-item-icon fas fa-users"></i>
                        <span class="nav-label">Users</span><i class="fa fa-angle-left arrow"></i>
                    </a>
                    <ul class="nav-2-level collapse">                        
                        <li>
                            <a {!!(isset($menuChild) && $menuChild=='customers') ? "class='active'" : '' !!} href="{{ route('admin.customers.index') }}">Customers</a>
                        </li>
                    @if($auth_user->role_id == config('constant.ROLE_SUPER_ADMIN_ID'))
                        <li>
                            <a {!!(isset($menuChild) && $menuChild=='admins') ? "class='active'" : '' !!} href="{{ route('admin.admins') }}">Admins</a>
                        </li>
                    @endif  
                    </ul>
                </li>
                <li {!!(isset($menuParent) && $menuParent=='inwards') ? " class='active'" : '' !!}>
                    <a href="#">
                        <i class="sidebar-item-icon fas fa-truck-loading"></i>
                        <span class="nav-label">Inward</span><i class="fa fa-angle-left arrow"></i>
                    </a>
                    <ul class="nav-2-level collapse">                        
                        <li>
                            <a {!!(isset($menuChild) && $menuChild=='inward') ? "class='active'" : '' !!} href="{{ route('admin.inwards.create') }}">Add New</a>
                        </li>
                        <li>
                            <a {!!(isset($menuChild) && $menuChild=='allinward') ? "class='active'" : '' !!} href="{{ route('admin.inwards.index') }}">Inwards</a>
                        </li>
                    </ul>
                </li>
                <li {!!(isset($menuParent) && $menuParent=='outwards') ? " class='active'" : '' !!}>
                    <a href="#">
                        <i class="sidebar-item-icon fas fa-truck"></i>
                        <span class="nav-label">Outward</span><i class="fa fa-angle-left arrow"></i>
                    </a>
                    <ul class="nav-2-level collapse">                        
                        <li>
                            <a {!!(isset($menuChild) && $menuChild=='outward') ? "class='active'" : '' !!} href="{{ route('admin.outwards.create') }}">Add New</a>
                        </li>
                        <li>
                            <a {!!(isset($menuChild) && $menuChild=='alloutward') ? "class='active'" : '' !!} href="{{ route('admin.outwards.index') }}">Outwards</a>
                        </li>
                    </ul>
                </li>
            
                <li {!!(isset($menuParent) && $menuParent=='reports') ? " class='active'" : '' !!}>
                    <a href="#">
                        <i class="sidebar-item-icon fas fa-chart-line"></i>
                        <span class="nav-label">Reports</span><i class="fa fa-angle-left arrow"></i>
                    </a>
                    <ul class="nav-2-level collapse">
                        <li>
                            <a {!!(isset($menuChild) && $menuChild=='full-ledger') ? "class='active'" : '' !!} href="{{ route('admin.reports.full-ledger.show') }}">Stocks Report</a>
                        </li>
                        <li>
                            <a {!!(isset($menuChild) && $menuChild=='stock-report') ? "class='active'" : '' !!} href="{{ route('admin.reports.stock-report.show') }}">Storage Capacity</a>
                        </li>
                        <li>
                            <a {!!(isset($menuChild) && $menuChild=='insuarnce-report') ? "class='active'" : '' !!} href="{{ route('admin.reports.insurance-report.show') }}">Insurance</a>
                        </li>
                    </ul>
                </li>
                @if($auth_user->role_id == config('constant.ROLE_SUPER_ADMIN_ID'))
                   <li {!!(isset($menuParent) && $menuParent=='trash') ? " class='active'" : '' !!}>
                    <a href="#">
                        <i class="sidebar-item-icon fas fa-trash"></i>
                        <span class="nav-label">Trash</span><i class="fa fa-angle-left arrow"></i>
                    </a>
                    <ul class="nav-2-level collapse">
                        <li>
                            <a {!!(isset($menuChild) && $menuChild=='trashorders') ? "class='active'" : '' !!} href="{{ route('admin.trash.orders') }}">Orders</a>
                        </li>
                    </ul>
                </li>
            @endif
            </ul>
            <div class="sidebar-footer">
                <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i class="ti-power-off"></i></a>
            </div>
        </div>
    </nav>
    <!-- END SIDEBAR-->
    <div class="content-wrapper">
        <!-- START PAGE CONTENT-->    <!-- START PAGE CONTENT-->
