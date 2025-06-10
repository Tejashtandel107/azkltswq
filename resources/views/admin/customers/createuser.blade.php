
@extends('admin.layouts.app')

@section('plugin-css')
    <link rel="stylesheet" href="{{ asset('/assets/app/vendors/formvalidation/formValidation.min.css') }}">
@endsection
@section('page-css')
    <style type="text/css">
        .flex-grow-1 {
            flex-grow: 1;
        }
        .upload-profile img{
            max-width: 90px;
        }
    </style>
@endsection
@section('pagetitle',$pagetitle)

@section('pagecontent')
<!-- Page Heading Breadcrumbs -->
@include('admin.layouts.breadcrumbs')
<div class="page-content fade-in-up">
    <div class="row">
        <div class="{{(isset($user)) ? 'col-lg-8' : 'col-lg-12'}}" id="user-details">
            <div class="ibox ibox-fullheight">
                <div class="ibox-head">
                    <div class="ibox-title">Profile Information</div>
                </div>
            @if(isset($user))
                <form method="POST" action="{{ route('admin.user.update', $user->user_id) }}" enctype="multipart/form-data" id="user-form">
                    @method('PATCH')
            @else
                <form method="POST" action="{{ route('admin.user.store') }}" enctype="multipart/form-data" id="user-form" autocomplete="off">
            @endif
                    @csrf
                    <div class="ibox-body">
                        <div id="notify"></div>
                        <div class="row">
                            <div class="form-group col-md-6 col-12">                            
                                <label>Customer</label>
                                <select class="form-control" name="customer_id">
                                    <option value="">Select customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{$customer->customer_id}}" {{(isset($customer_id) && ($customer->customer_id == $customer_id)) ? 'selected="selected"' : ""}}>{{$customer->companyname}}</option>
                                @endforeach
                                </select>
                                @if ($errors->has('customer_id'))
                                    <small class="error">{{ $errors->first('customer_id') }}</small>
                                @endif
                            </div>        
                            <div class="col-md-6 mb-4 form-group">
                                <label for="username">Username</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="ti-user"></i></span>
                                    <input type="text" name="username" class="form-control" placeholder="User Name" value="{{ ($user->username) ?? '' }}"/>
                                </div>
                                <span class="help-block">The username can only consist of alphabetical, number and underscore.</span>
                                @if ($errors->has('username'))
                                    <small class="error">{{ $errors->first('username') }}</small>
                                @endif
                            </div>
                        </div>
                        
                        <!-- <input type="hidden" name="customer_id" value="{{isset($customer_id) ? $customer_id : null}}"> -->
                        <div class="row">
                            <div class="col-md-6 form-group mb-4">
                                <label for="firstname">First Name</label> 
                                <input type="text" name="firstname" class="form-control" placeholder="First Name" value="{{ ($user->firstname) ?? '' }}" />
                            </div>
                            <div class="col-md-6 form-group mb-4">
                                <label for="lastname">Last Name</label>
                                <input type="text" name="lastname" class="form-control" placeholder="Last Name" value="{{ ($user->lastname) ?? '' }}" />
                            </div>
                        </div>
                        <div class="form-group mb-4">
                            <label for="email">Email</label>
                            <div class="input-group mb-3">
                                <span class="input-group-addon"><i class="ti-email"></i></span>
                                <input type="email" name="email" class="form-control" placeholder="Email" value="{{ ($user->email) ?? '' }}" />
                            </div>
                            @if ($errors->has('email'))
                                <small class="error">{{ $errors->first('email') }}</small>
                            @endif
                        </div>
                    @if(!isset($user))
                        <div class="row">
                            <div class="col-md-6 orm-group mb-4">
                                <label for="newpassword">New Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Enter New Password" maxlength="15" value="{{ ($user->password) ?? '' }}"/>
                                @if ($errors->has('password'))
                                    <small class="error">{{ $errors->first('password') }}</small>
                                @endif
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="reenter Password">Re-enter Password</label>
                                <input type="password" name="password_confirmation" class="form-control" placeholder="Enter Re-enter Password" maxlength="15" />
                                @if ($errors->has('password_confirmation'))
                                    <span class="error">{{ $errors->first('password_confirmation') }}</span>
                                @endif
                            </div>
                        </div>
                    @endif
                        <div class="form-group">
                            <label for="photo">Photo</label>
                            <div class="flexbox">
                                <div class="flexbox-b flexwrap justify-content-center">
                                    <div class="upload-profile">
                                        <img class="file-upload img-circle profile-img" src="{{(isset($user) ? $user->photo : \Helper::getProfileImg(''))}}" alt="User Photo" width="90" height="90">
                                    </div>
                                </div>
                                <div class="input-group pl-5">
                                    <input type="file" name="photo" placeholder="Photo" />
                                    @if ($errors->has('photo'))
                                        <small class="error">{{ $errors->first('photo') }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-4">
                            <label for="isactive">Enable</label>
                            @php
                                $isactive = isset($user) ? $user->isactive : config('constant.status.active');
                            @endphp
                            <div>
                                <label class="radio radio-info radio-inline">
                                    <input type="radio" name="isactive" value="{{ config('constant.status.active') }}" {{ $isactive == config('constant.status.active') ? 'checked' : '' }}                             />
                                    <span class="input-span"></span>Yes
                                </label>
                                <label class="radio radio-info radio-inline">
                                    <input type="radio" name="isactive" value="{{ config('constant.status.inactive') }}" {{ $isactive == config('constant.status.inactive') ? 'checked' : '' }} />
                                    <span class="input-span"></span>No
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="ibox-footer">
                        <button type="submit" class="btn btn-info mr-2" id="submitbtn">Save</button>
                        <a href="{{route('admin.customers.edit',$customer_id)}}" class="btn btn-secondary" data-dismiss="modal">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    @if(isset($user))
        <div class="col-md-4" id="changepass">
            <div class="ibox">
                <div class="ibox-head">
                    <div class="ibox-title">Update Password</div>
                </div>
                <div id="notify"></div>
                <form method="POST" action="{{ route('admin.user.changepass', $user->user_id) }}" id="reset-password-form" name="reset-password-form" autocomplete="off">
                    @csrf
                    <div class="ibox-body">
                        <input type="hidden" name="user_id" value="{{isset($user) ? $user->user_id : null}}">
                        <div class="form-group mb-4">                         
                            <label for="oldpassword">Old Password</label>
                            <div class="input-group">
                                <input type="password" name="oldpassword" class="form-control" placeholder="Enter Old Password"/>
                            </div>    
                            @if ($errors->has('oldpassword'))
                                <small class="error">{{ $errors->first('oldpassword') }}</small>
                            @endif
                        </div>
                        <div class="form-group mb-4">
                            <label for="newpassword">New Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Enter New Password" maxlength="15"/>
                            @if ($errors->has('password'))
                                <small class="error">{{ $errors->first('password') }}</small>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="reenter Password">Re-enter Password</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Enter Re-enter Password" maxlength="15"/>
                            @if ($errors->has('password_confirmation'))
                                <span class="error">{{ $errors->first('password_confirmation') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="ibox-footer">
                        <button class="btn btn-info" type="submit" id="resetsubmit">Change Password</button>
                    </div>
                </form>
            </div>  
        </div>
    @endif    
    </div>
</div>
@endsection

@section('plugin-scripts')
    <script src="{{ asset('/assets/app/vendors/formvalidation/formValidation.min.js') }}"></script>
    <script src="{{ asset('/assets/app/vendors/formvalidation/framework/bootstrap4.min.js') }}"></script>
@endsection

@section('page-scripts')
    <script src="{{ asset('/assets/admin/js/customers/createuser.min.js') }}"></script>
    @if (session('type'))
        <script type="text/javascript">
    		@if(session('type')=="success")
    			$("#notify").notification({caption: "{{session('message')}}", sticky:false, type:'{{session('type')}}'});
    		@else
    			$("#notify").notification({caption: "{{session('message')}}", sticky:true, type:'{{session('type')}}'});
    		@endif

        </script>
    @endif
@endsection
