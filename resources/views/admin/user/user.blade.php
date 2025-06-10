@extends('admin.layouts.app')

@section('plugin-css')
    <link rel="stylesheet" href="{{ asset('assets/app/vendors/formvalidation/formValidation.min.css') }}">
@endsection
@section('page-css')
   	<link rel="stylesheet" href="{{ asset('assets/admin/css/user.css') }}">
@endsection

@section('pagetitle',$pagetitle)

@section('pagecontent')
    <!-- Page Heading Breadcrumbs -->
	@include('admin.layouts.breadcrumbs')
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
	                                <div class="user-profile upload-profile">
	                                    <img class="img-circle profile-img" src="{{$auth_user->photo}}" alt="image" width="90" height="90">
	                                </div>
	                                <div>
	                                    <h4>{{$auth_user->fullname}}</h4>
	                                    <div class="mb-1" style="color: #747474">
	                                        <span class="mr-1">{{$auth_user->email}}</span>
	                                    </div>
	                                    <div class="mb-3" style="color: #747474">
	                                        <span class="badge badge-blue">{{$user_role_lables[$auth_user->role_id]['display_name'] }}</span>
	                                    </div>
	                                </div>
	                            </div>
	                        </div>
						</div>
					@if(isset($user))
						<form method="POST" action="{{ route('admin.profile.update', $user->user_id) }}" id="user-form" enctype="multipart/form-data" autocomplete="off">
							@method('PATCH')
					@else
						<form method="POST" action="{{ route('admin.profile.store') }}" id="user-form" autocomplete="off">
					@endif
							@csrf
							<div class="ibox-body">
								<div class="form-group mb-4 row">
									<label for="firstname" class="col-sm-3 col-form-label">First Name</label>
									<div class="col-sm-9">
										<input type="text" name="firstname" id="firstname" class="form-control" placeholder="First Name" value="{{ $user->firstname ?? '' }}">
									</div>
								</div>
								<div class="form-group mb-4 row">
									<label for="lastname" class="col-sm-3 col-form-label">Last Name</label>
									<div class="col-sm-9">
										<input type="text" name="lastname" class="form-control" placeholder="Last Name" value="{{ $user->lastname ?? '' }}" >
									</div>
								</div>
								<div class="form-group mb-4 row">
									<label for="email" class="col-sm-3 col-form-label">Email</label>
									<div class="col-sm-9">
										<input type="text" name="email" id="email" class="form-control" placeholder="Email" value="{{ $user->email ?? '' }}">
									</div>
								</div>
								<div class="form-group mb-4 row">
									<label for="username" class="col-sm-3 col-form-label">Username</label>
									<div class="input-group col-sm-9">
										<span class="input-group-addon"><i class="fa fa-user"></i></span>
										<input type="text" name="username" id="username" class="form-control" placeholder="User Name" value="{{ $user->username ?? '' }}" readonly>
									</div>
								</div>
								<div class="form-group row">
									<label for="photo" class="col-sm-3 col-form-label">Photo</label>
									<div class="input-group col-sm-9">
										<input type="file" name="photo" id="photo" placeholder="Photo" {{ isset($user) ? '' : 'required' }} />
									</div>
								</div>
							</div>
							<div class="ibox-footer">
								<button class="btn btn-info mr-2" type="submit" id="profileBtn">Submit</button>
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
						<form action="{{ route('admin.user.changepassword', $user->user_id) }}" method="POST" id="reset-password-form" name="reset-password-form" autocomplete="off">
							@csrf
							<div class="ibox-body">
								<div class="form-group mb-4">
									<label for="oldpassword">Old Password</label>
									<div class="input-group">
										<input type="password" name="oldpassword" id="oldpassword" class="form-control" placeholder="Enter Old Password"/>
									</div>
									</div>
								<div class="form-group mb-4">
									<label for="password">New Password</label>
									<input type="password" name="password" id="password" class="form-control" placeholder="Enter New Password" maxlength="15"/>
								</div>
								<div class="form-group">
									<label for="password_confirmation">Re-enter Password</label>
									<input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Enter Re-enter Password" maxlength="15"/>
								</div>
							</div>
							<div class="ibox-footer">
								<button class="btn btn-info" type="submit" id="submitbtn">Change Password</button>
							</div>
						</form>
					</div>	
				</div>
			</div>
			<div class="row">
				<div class="col-md-8" id="company-info">
					<div class="ibox">
						<div id="notify"></div>
					@if(isset($company))
						<form method="POST" action="{{ route('admin.updatecompanyinfo', $company->id) }}" enctype="multipart/form-data" id="company-form">
							@method('PATCH')
							@csrf
					@else
						<form method="POST" action="{{ route('admin.storecompanyinfo') }}" enctype="multipart/form-data" id="company-form">
							@csrf
					@endif
					@php
						if(isset($company) && $company->count()>0){
							$company = json_decode($company->value);
						}
					@endphp
							<div class="ibox-head">
								<div class="ibox-title">Company Information</div>
							</div>
							<div class="ibox-body">
								<div class="form-group mb-4 row">
									<label for="companyname" class="col-sm-3 col-form-label">Company Name</label>
									<div class="col-sm-9">
									<input type="text" name="companyname" class="form-control" id="companyname" placeholder="Company Name" value="{{ $company->companyname ?? '' }}">
									</div>
								</div>
								<div class="form-group mb-4 row">
									<label for="gstnumber" class="col-sm-3 col-form-label">GST Number</label>
									<div class="col-sm-9">
									<input type="text" name="gstnumber" id="gstnumber" class="form-control" placeholder="GST Number" value="{{ $company->gstnumber ?? '' }}">
									</div>
								</div>
								<div class="form-group mb-4 row">
									<label for="address" class="col-sm-3 col-form-label">Address</label>
									<div class="col-sm-9">
									<textarea name="address" id="address" class="form-control" placeholder="Address" rows="4">{{ $company->address ?? '' }}</textarea>
									</div>
								</div>
								<div class="form-group mb-4 row">
									<label for="phone" class="col-sm-3 col-form-label">Phone</label>
									<div class="col-sm-9">
										<div class="input-group">
											<span class="input-group-addon"><i class="fa fa-phone"></i></span>
											<input type="text" name="phone" id="phone" class="form-control" placeholder="Phone" maxlength="15" value="{{ $company->phone ?? '' }}">
										</div>
									</div>
								</div>
								<div class="form-group mb-4 row">
									<label for="gods_quotes" class="col-sm-3 col-form-label">Gods Quotes</label>
									<div class="col-sm-9">
										<input type="text" name="gods_quotes" id="gods_quotes" class="form-control" placeholder="Gods Quotes" value="{{ $company->gods_quotes ?? '' }}">
									</div>
								</div>
								<div class="form-group row">
									<label for="logo" class="col-sm-3 col-form-label">Logo</label>
									<div class="flexbox col-sm-9">
										<div class="flexbox-b flexwrap justify-content-center">
											<div class="upload-profile">
												<img class="file-upload img-circle profile-img company-img" src="{{(isset($company->logo) ? \Helper::getProfileImg($company->logo) : \Helper::getProfileImg(''))}}" alt="Company Logo" width="90" height="90">
											</div>
										</div>
										<div class="input-group pl-5">
											<input type="file" name="logo" class="form-control text-center" placeholder="logo" />
										</div>
									</div>
								</div>
							</div>
							<div class="ibox-footer">
								<button class="btn btn-info mr-2" type="submit" id="submitDetails">Submit</button>
							</div>
					  </form>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
@section('plugin-scripts')
   	<script src="{{ asset('assets/app/vendors/formvalidation/formValidation.min.js') }}"></script>
	<script src="{{ asset('assets/app/vendors/formvalidation/framework/bootstrap4.min.js') }}"></script>
@endsection

@section('page-scripts')
    <script src="{{ asset('assets/admin/js/user/edit.min.js') }}"></script>
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
