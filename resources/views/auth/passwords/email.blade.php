@extends('web.layouts.login.layout')

@section('pagetitle')
Forgot Password
@endsection
@section('plugin-css')
    <link rel="stylesheet" href="{{ asset('/assets/app/vendors/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/app/vendors/formvalidation/formValidation.min.css') }}">
@endsection
@section('page-css')
    <style type="text/css">
        small.error {
            font-size: 14px;
            color: #FFF;
            display: block;
            margin-top: 5px;
            background-color: #f87377;
            border-color: #f87377;
            position: relative;
            padding: .75rem 1.25rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
        }
        .alert h4{
            margin-bottom: 0px;
            font-size: 14px;
        }

    </style>
@endsection
@section('pagecontent')
    <div class="ibox login-content">
        <div class="text-center">
            <span class="auth-head-icon"><i class="la la-key"></i></span>
        </div>
        <form class="ibox-body pt-0" id="forgot-form" action="{{ route('password.email') }}" method="POST">
            {{ csrf_field() }}
            <h4 class="font-strong text-center mb-4">FORGOT PASSWORD</h4>
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
            <p class="mb-4">Enter your email address below and we'll send you password reset instructions.</p>
            <div class="form-group mb-4{{ $errors->has('email') ? ' has-error' : '' }}">
                <input class="form-control form-control-line" type="text" name="email" placeholder="Email" value="{{ old('email') }}">
                @if ($errors->has('email'))
                    <small class="error">{{ $errors->first('email') }}</small>
                @endif
            </div>
            <div class="flexbox mb-4">
                <span>&nbsp;</span>
                <a class="text-primary" href="{{ route('login') }}">
                    <span class="flexbox"><i class="la la-arrow-left"></i>&nbsp;Back to login</span>
                </a>
            </div>
            <div class="text-center">
                <button class="btn btn-primary btn-rounded btn-block btn-air">SUBMIT</button>
            </div>
        </form>
    </div>
@endsection
@section('plugin-scripts')
    <script src="{{ asset('/assets/app/vendors/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('/assets/app/js/plugin/jquery.form.min.js') }}"></script>
    
    <script src="{{ asset('/assets/app/js/plugin/jquery.notification.min.js') }}"></script>
    
    <script src="{{ asset('/assets/app/vendors/formvalidation/formValidation.min.js') }}"></script>
    <script src="{{ asset('/assets/app/vendors/formvalidation/framework/bootstrap4.min.js') }}"></script>

@endsection
@section('page-scripts')
    <script src="{{URL::asset('/assets/web/js/forgot-password.js')}}"></script>
@endsection
