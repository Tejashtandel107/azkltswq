@extends('admin.layouts.app')

@section('pagetitle',$pagetitle)

@section('pagecontent')
    <!-- Page Heading Breadcrumbs -->
	@include('admin.layouts.breadcrumbs')
    <div class="page-content fade-in-up">
        <div class="ibox ibox-fullheight">
        @if(isset($item))
            <form method="POST" action="{{ route('admin.items.update', $item->item_id) }}" id="item-form">
                @method('PATCH')
        @else
            <form method="POST" action="{{ route('admin.items.store') }}" id="item-form">
        @endif
                @csrf
                <div class="ibox-body">
					<div id="notify"></div>
                    <div class="form-group mb-4{{ $errors->has('name') ? ' has-error' : '' }}">
                        <label for="name">Item Name</label>
                        <input type="text" name="name"  value="{{ old('name', $item->name ?? '') }}" class="form-control" placeholder="Item Name">			
					@if ($errors->has('name'))
	                    <small class="error">{{ $errors->first('name') }}</small>
		            @endif
                    </div>
                    <div class="form-group mb-4">
                        <label for="description">Description</label>
                        <textarea name="description" class="form-control" rows="5" placeholder="Item Description">{{ old('description', $item->description ?? '') }}</textarea>
                    </div>
                    <div class="form-group mb-0{{ $errors->has('name') ? ' has-error' : '' }}">
                        <label>Enable</label>
                        @php
                            $isactive = old('isactive', $item->isactive ?? 1);
                        @endphp
                        <div>
                            <label class="radio radio-inline radio-info">
                                <input type="radio" name="isactive" value="1" {{ $isactive == 1 ? 'checked' : '' }}>
                                <span class="input-span"></span>Yes
                            </label>
                            <label class="radio radio-inline radio-info">
                                <input type="radio" name="isactive" value="0" {{ $isactive == 0 ? 'checked' : '' }}>
                                <span class="input-span"></span>No
                            </label>
                        </div>
					@if ($errors->has('isactive'))
		                <small class="error">
                            {{ $errors->first('isactive') }}
		                </small>
		            @endif
                    </div>
                </div>
                <div class="ibox-footer">
                    <button class="btn btn-info mr-2" type="submit">Submit</button>
					<a href="{{ route('admin.items.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('page-scripts')
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
