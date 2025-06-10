@extends('admin.layouts.app')

@section('plugin-css')
    <link rel="stylesheet" href="{{ asset('/assets/app/vendors/formvalidation/formValidation.min.css') }}">
@endsection
@section('page-css')
    <style type="text/css">
        .flex-grow-1 {
            flex-grow: 1;
        }
    </style>
@endsection
@section('pagetitle',$pagetitle)

@section('pagecontent')
<!-- Page Heading Breadcrumbs -->
@include('admin.layouts.breadcrumbs')
<div class="page-content fade-in-up">
    <div class="row">
        <div class="{{(isset($customer)) ? 'col-lg-7' : 'col-lg-8'}}" id="customer-details">
            <div class="ibox ibox-fullheight">
                <div class="ibox-head">
                    <div class="ibox-title">Customer Information</div>
                </div>
            @if(isset($customer))
                <form method="POST" action="{{ route('admin.customers.update', $customer->customer_id) }}" id="customer-form" enctype="multipart/form-data">
                    @method('PATCH')
            @else
                <form method="POST" action="{{ route('admin.customers.store') }}" id="customer-form" enctype="multipart/form-data" autocomplete="off">                   
            @endif
                @csrf
                    <div class="ibox-body">
                        <div id="notify"></div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-4">
                                <label for="companyname">Company Name</label>
                                <input type="text" name="companyname" class="form-control" placeholder="Company Name" value="{{ $customer->companyname ?? '' }}">
                            </div> 
                            <div class="col-md-6 form-group mb-4">
                                <label for="contact_person">Contact Person</label>
                                <input type="text" name="contact_person" class="form-control" placeholder="Contact Person" value="{{ $customer->contact_person ?? '' }}">  
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-4 form-group">
                                <label for="phone">Phone</label>
                                <div class="input-group mb-2 date">
                                    <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                                    <input type="text" name="phone" value="{{($customer->phone) ?? '' }}" class="form-control" placeholder="Phone" maxlength="15">			
                                </div>    
                            </div>     
                            <div class="col-md-6 form-group mb-4">
                                <label for="gstnumber">GST Number</label>
                                <input type="text" name="gstnumber" value="{{($customer->gstnumber) ?? '' }}" class="form-control" placeholder="GST Number" maxlength="255">			                          
                            </div>    
                        </div>
                        <div class="form-group mb-4">
                            <label for="address">address</label>
                            <textarea name="address" class="form-control" placeholder="Address" rows="3">{{ old('address', $customer->address ?? '') }}</textarea>                      
                        </div>
                        <!-- <div class="form-group mb-4">
                            {{-- <label for="photo">photo</label> --}}
                                <label for="photo">photo</label>

                            <div>
                                {{-- {!! Form::file('photo',null, array('class' => 'form-control', 'placeholder' => 'Photo','required'=>true)) !!}                             --}}
                            </div>
                        </div> -->
                        <div class="form-group mb-4">
                            <label for="isactive">Enable</label>
                            @php
                                $isactive = isset($model) ? $model->isactive : config('constant.status.active');
                            @endphp
                            <div>
                                <label class="radio radio-info radio-inline">
                                    <input type="radio" name="isactive" value="{{ config('constant.status.active') }}" {{ $isactive == config('constant.status.active') ? 'checked' : '' }}>
                                    <span class="input-span"></span>Yes
                                </label>
                                <label class="radio radio-info radio-inline">
                                    <input type="radio" name="isactive" value="{{ config('constant.status.inactive') }}" {{ $isactive == config('constant.status.inactive') ? 'checked' : '' }}>
                                    <span class="input-span"></span>No
                                </label>
                            </div>
                        </div>
                        <div class="card centered">
                            <div class="card-body">
                                <div class="card-avatar mb-4">
                                    <img src="{{ isset($customer->photo)?$customer->photo: \Helper::getProfileImg('') }}" class="img-circle profile-img" alt="customer">                        
                                </div>
                                <div class="form-group">
                                    <input type="file" name="photo" placeholder="Photo">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ibox-footer">
                        <button type="submit" class="btn btn-info mr-2" id="submitbtn">Save</button>
                        <a href="{{route('admin.customers.index')}}" class="btn btn-secondary" data-dismiss="modal">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    @if(isset($customer))
        <div class="col-lg-5">
            <div class="ibox ibox-fullheight">
                <div class="ibox-head">
                    <div class="ibox-title">All Users</div>
                    <div>
                        <a class="btn btn-rounded btn-primary btn-air" href="{{ route('admin.user.create',$customer->customer_id) }}"><i class="ti-plus"></i>&nbsp;&nbsp;Create</a>
                    </div>
                </div>
                <div class="ibox-body">
                    <div id="deletenotify"></div>
                    <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 470px;">
                        <ul class="media-list media-list-divider mr-2 scroller" data-height="470px" style="overflow: hidden; width: auto; height: 470px;">
                    @if(isset($users) && ($users->count() > 0))
                        @foreach($users as $user)
                            <li class="media align-items-center"><a class="media-img" href="javascript:;"><img class="img-circle" src="{{$user->photo}}" alt="image" style="width: 54px;height: 54px;"></a>
                                <div class="media-body d-flex align-items-center">
                                    <div class="flex-1">
                                        <div>
                                            <span class="text-primary"><a href="{{ route('admin.user.edit',$user->user_id) }}"><b>{{$user->username}}</b></a></span>
                                            <span class="px-2">{{$user->fullname}}</span>
                                        </div>
                                        <small class="text-muted">{{$user->email}}</small>
                                    </div>
                                    <a href="{{ route('admin.user.edit',$user->user_id) }}" class="btn btn-sm btn-outline-info mb-1"><i class="la la-pencil"></i> Edit</a>&nbsp;
                                    <button class="btn btn-outline-danger btn-sm mb-1" onclick="deleteUser( '{{ route('admin.user.destroy',$user->user_id) }}','{{ $user->user_id }}' );" title="Delete">
                                        <i class="la la-trash"></i> Delete 
                                    </button> 
                                </div>
                            </li>
                        @endforeach      
                    @else
                        <li class="align-items-center">
                            <div class="alert alert-danger has-icon"><i class="fa fa-exclamation-circle alert-icon"></i> Sorry, no users found. </div>
                        </li>
                    @endif    
                        </ul>
                        <div class="slimScrollBar" style="background: rgb(113, 128, 143); width: 4px; position: absolute; top: 0px; opacity: 0.4; display: none; border-radius: 7px; z-index: 99; right: 1px; height: 416.008px;"></div>
                        <div class="slimScrollRail" style="width: 4px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; background: rgb(51, 51, 51); opacity: 0.9; z-index: 90; right: 1px;"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- <div class="col-md-3">
            <div class="card text-center has-cup card-air centered mb-4">
                <div class="card-cup"></div>
                <div class="card-body">
                    <div class="card-avatar mb-4">
                        <img src="{{ $customer->photo }}" class="img-circle" alt="customer">                        
                    </div>
                    <h5 class="card-title text-primary mb-1">{{ $customer->companyname }}</h5>
                    <div class="text-muted">{{ $customer->phone }}</div>
                </div>
            </div>
        </div> -->
        @endif    
    </div>
</div>
@endsection

@section('plugin-scripts')
    <script src="{{ asset('/assets/app/vendors/formvalidation/formValidation.min.js') }}"></script>
    <script src="{{ asset('/assets/app/vendors/formvalidation/framework/bootstrap4.min.js') }}"></script>
@endsection

@section('page-scripts')
    <script src="{{ asset('/assets/admin/js/customers/create.min.js') }}"></script>
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
