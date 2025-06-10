@extends('web.layouts.login.layout')

@section('pagetitle')
Reset Password
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
            <span class="auth-head-icon"><i class="la la-user"></i></span>
        </div>
        <form class="ibox-body" method="POST" action="{{ route('password.request') }}">
            {{ csrf_field() }}
            <input type="hidden" name="token" value="{{ $token }}">
            <h4 class="font-strong text-center mb-5">Reset Password</h4>
            <div class="form-group mb-4{{ $errors->has('email') ? ' has-error' : '' }}">
                <input class="form-control form-control-line" type="email" name="email" placeholder="Email" value="{{ request()->input('email', old('email')) }}" autofocus>
            @if ($errors->has('email'))
                <small class="error">
                    {{ $errors->first('email') }}
                </small>
            @endif
            </div>

            <div class="form-group mb-4{{ $errors->has('password') ? ' has-error' : '' }}">
                <input class="form-control form-control-line" type="password" name="password" placeholder="Password">
            @if ($errors->has('password'))
                <small class="error">
                    {{ $errors->first('password') }}
                </small>
            @endif
            </div>

            <div class="form-group mb-4{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                <input class="form-control form-control-line" type="password" name="password_confirmation" placeholder="Confirm Password">
            @if ($errors->has('password_confirmation'))
                <small class="error">
                    {{ $errors->first('password_confirmation') }}
                </small>
            @endif
            </div>
            <div class="text-center mb-4">
                <button class="btn btn-primary btn-rounded btn-block">LOGIN</button>
            </div>
        </form>
    </div>
@endsection
