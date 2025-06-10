@extends('web.layouts.login.layout')

@section('pagetitle')
Login
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
        .login-content{
            max-width: 500px  !important;
        }
    </style>
@endsection

@section('pagecontent')
    <div class="ibox login-content">
        <div class="text-center">
            <span class="auth-head-icon"><i class="la la-user"></i></span>
        </div>
        <div class="ibox-body">
            <div id="notify"></div>

            <form id="login-form" method="POST" action="{{ route('login') }}">
                {{ csrf_field() }}
                <h4 class="font-strong text-center mb-5">LOG IN</h4>
                <div class="form-group mb-4{{ $errors->has('email') ? ' has-error' : '' }}">
                    <input class="form-control form-control-line" type="text" name="email" placeholder="Email or Username" value="{{ old('email') }}" autofocus>
                @if ($errors->has('email'))
                    <small class="error">{{ $errors->first('email') }}</small>
                @endif
                </div>
                <div class="form-group mb-4{{ $errors->has('password') ? ' has-error' : '' }}">
                    <input class="form-control form-control-line" type="password" name="password" placeholder="Password">
                    @if ($errors->has('password'))
                        <small class="error">{{ $errors->first('password') }}</small>
                    @endif
                </div>
                <div class="flexbox mb-5">
                    <span>
                        <label class="ui-switch switch-icon mr-2 mb-0">
                            <input type="checkbox" name="remember" @checked(old('remember'))>
                            <span></span>
                        </label>Remember
                    </span>
                    <a class="text-primary" href="{{ route('password.request') }}">Forgot password?</a>
                </div>
                <div class="text-center mb-4">
                    <button class="btn btn-primary btn-rounded btn-block" id="loginbtn">LOGIN</button>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('plugin-scripts')
    <script src="{{ asset('/assets/app/vendors/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('/assets/app/js/plugin/jquery.form.min.js') }}"></script>
    <script src="{{ asset('/assets/app/vendors/formvalidation/formValidation.min.js') }}"></script>
    <script src="{{ asset('/assets/app/vendors/formvalidation/framework/bootstrap4.min.js') }}"></script>

@endsection
@section('page-scripts')
    <script src="{{URL::asset('/assets/web/js/login.js')}}"></script>
@endsection

