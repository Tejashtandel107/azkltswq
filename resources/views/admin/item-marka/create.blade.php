@extends('admin.layouts.app')

@section('pagetitle',$pagetitle)
@section('plugin-css')
    <link rel="stylesheet" href="{{ asset('/assets/app/vendors/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/app/vendors/formvalidation/formValidation.min.css') }}">
@endsection

@section('pagecontent')
    <!-- Page Heading Breadcrumbs -->
	@include('admin.layouts.breadcrumbs')
    <div class="page-content fade-in-up">
        <div class="ibox ibox-fullheight">
        @if(isset($marka))
            <form method="POST" action="{{ route('admin.item-marka.update', $marka->marka_id) }}" id="marka-form">
                @method('PATCH')
        @else
            <form method="POST" action="{{ route('admin.item-marka.store') }}" id="marka-form">
        @endif
                @csrf
                <div class="ibox-body">
					<div id="notify"></div>
					<div class="form-group">
                        <label for="item_id">Item</label>
						<select name="item_id" id="item_id" class="form-control">
                            <option value="">Select Item</option>
                            @foreach($items as $id => $name)
                                <option value="{{ $id }}" 
                                    {{ (old('item_id') !== null ? old('item_id') : ($marka->item_id ?? null)) == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
						@if ($errors->has('item_id'))
			                <small class="error">{{ $errors->first('item_id') }}</small>
			            @endif
                    </div>
                    <div class="form-group mb-4{{ $errors->has('name') ? ' has-error' : '' }}">
                        <label for="name">Name</label>
                        <input type="text" name="name" value="{{ old('name', $marka->name ?? '') }}" class="form-control" placeholder="Marka name">			
                        @if ($errors->has('name'))
    		                <small class="error">{{ $errors->first('name') }}</small>
    		            @endif
                    </div>					
                </div>
                <div class="ibox-footer">
                    <button class="btn btn-info mr-2" type="submit" id="submitbtn">Submit</button>
					<a href="{{ route('admin.item-marka.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('plugin-scripts')
	<script src="{{ asset('/assets/app/vendors/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('/assets/app/vendors/formvalidation/formValidation.min.js') }}"></script>
    <script src="{{ asset('/assets/app/vendors/formvalidation/framework/bootstrap4.min.js') }}"></script>
@endsection

@section('page-scripts')
    <!-- <script src="{{ asset('/assets/admin/js/item-marka/create.js') }}"></script>	 -->
@endsection
