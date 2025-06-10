<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} : @yield('pagetitle')</title>
    <!-- GLOBAL MAINLY STYLES-->
    <link rel="stylesheet" href="{{ asset('/assets/app/vendors/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/app/vendors/line-awesome/css/line-awesome.min.css') }}">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('/assets/app/vendors/themify-icons/css/themify-icons.css') }}">
	<link rel="stylesheet" href="{{ asset('/assets/app/vendors/animate.css/animate.min.css') }}">
    <!-- PLUGINS STYLES-->
	@yield('plugin-css')
    <!-- THEME STYLES-->
    <link rel="stylesheet" href="{{ asset('/assets/admin/css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/admin/css/themes/gradient-blue.css') }}">
    <!-- PAGE LEVEL STYLES-->
	@yield('page-css')
    <style>
        body {
            background-repeat: no-repeat;
            background-size: cover;
            /*background-color: #18c5a9;*/
            background-image: linear-gradient(45deg,#35c9ff 0,#69f0ae 100%)!important;
        }
        .cover {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            /*background-color: rgba(117, 54, 230, .1);*/
            background-image: linear-gradient(45deg,#35c9ff 0,#69f0ae 100%)!important;
        }
        .login-content {
            max-width: 400px;
            margin: 100px auto 50px;
        }
        .auth-head-icon {
            position: relative;
            height: 60px;
            width: 60px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            background-color: #fff;
            color: #5c6bc0;
            box-shadow: 0 5px 20px #d6dee4;
            border-radius: 50%;
            transform: translateY(-50%);
            z-index: 2;
        }
    </style>
</head>
<body>
    <div class="cover"></div>
