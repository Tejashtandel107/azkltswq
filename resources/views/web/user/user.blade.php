@extends('web.layouts.app')

@section('plugin-css')
    <link rel="stylesheet" href="{{ asset('/assets/app/vendors/formvalidation/formValidation.min.css') }}">
@endsection
@section('page-css')
    <link rel="stylesheet" href="{{ asset('/assets/web/css/user.css') }}">
@endsection

@section('pagetitle',$pagetitle)

@section('pagecontent')
    <!-- Page Heading Breadcrumbs -->
	@include('web.layouts.breadcrumbs')
	<?php
	    $application_name=config('app.name');
	    $auth_user = (Auth::check()) ? Auth::user () : null;
	    $user_role_lables = config('constant.USER_ROLE_LABELS');
	?>
    <div class="page-content fade-in-up">
		<div class="content">
			<div class="row">
				<div class="col-md-8" id="profile-info">
					<div class="ibox">
						<div class="ibox-head">
							<div class="ibox-title">Profile Information</div>
						</div>
						<div id="notify"></div>
						<div class="ibox-body">
							<div class="flexbox">
	                            <div class="flexbox-b flexwrap justify-content-center">
	                                <div class="user-profile">
	                                	
	                                    <img class="img-circle profile-img" src="{{$auth_user->photo}}" alt="image" width="110" height="110">
	                                </div>
	                                <div>
	                                    <h4>{{$auth_user->fullname}}</h4>
	                                    <div class="mb-1" style="color: #747474">
	                                        <span class="mr-1">{{$auth_user->email}}</span>
	                                    </div>
	                                </div>
	                            </div>
	                        </div>
						</div>
					@if(isset($user))
						<form method="POST" enctype="multipart/form-data" action="{{ route('user.profile.update', $user->user_id) }}" id="user-form">
							@method('PATCH')							
					@else
						<form method="POST" action="{{ route('user.profile.store') }}" id="item-form">
					@endif
							@csrf
							<div class="ibox-body">
								<div class="form-group mb-4 row">
									<label for="email" class="col-sm-3 col-form-label">Email</label>
									<div class="col-sm-9">
										<div class="input-group">
											<span class="input-group-addon"><i class="ti-email"></i></span>
											<input type="text" name="email" class="form-control" placeholder="Email" value="{{  ($user->email ?? '') }}">
										</div>
										@if ($errors->has('email'))
											<small class="error">{{ $errors->first('email') }}</small>
										@endif  
									</div>
								</div>
								<div class="form-group mb-4 row">
									<label for="username" class="col-sm-3 col-form-label">Username</label>
									<div class="col-sm-9">
										<div class="input-group">
											<span class="input-group-addon"><i class="fa fa-user"></i></span>
											<input type="text" name="username" class="form-control" placeholder="User Name" value="{{($user->username ?? '') }}" readonly>
										</div>
									</div>
								</div>
							</div>
							<div class="ibox-footer">
								<button class="btn btn-info mr-2" type="submit" id="profilesubmitbtn">Submit</button>
							</div>
						</form>
					</div>
			</div>
			<div class="col-md-4" id="reset-password">
				<div class="ibox">
					<div class="ibox-head">
						<div class="ibox-title">Update Password</div>
					</div>
					<div id="notify"></div>
					<form action="{{ route('user.changepassword', $user->user_id) }}" method="POST" id="reset-password-form" name="reset-password-form" autocomplete="off">
    					@csrf
						<div class="ibox-body">
							<div class="form-group mb-4">
								<label for="oldpassword">Old Password</label>
								<div class="input-group">
									<input type="password" name="oldpassword" class="form-control" placeholder="Enter Old Password" />
								</div>    
							</div>
							<div class="form-group mb-4">
								<label for="newpassword">New Password</label>
								<input type="password" name="password" class="form-control" placeholder="Enter New Password" maxlength="15" />
							</div>
							<div class="form-group">
								<label for="reenter Password">Re-enter Password</label>
								<input type="password" name="password_confirmation" class="form-control" placeholder="Enter Re-enter Password" maxlength="15" />
							</div>
						</div>
						<div class="ibox-footer">
							<button class="btn btn-info" type="submit" id="submitbtn">Change Password</button>
						</div>
					</form>
				</div>	
			</div>
		</div>				
	</div>
</div>
@endsection
@section('plugin-scripts')
    <script src="{{ asset('/assets/app/vendors/formvalidation/formValidation.min.js') }}"></script>
    <script src="{{ asset('/assets/app/vendors/formvalidation/framework/bootstrap4.min.js') }}"></script>
@endsection

@section('page-scripts')
    <script src="{{ asset('/assets/web/js/user/edit.min.js') }}"></script>
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
