@extends('admin.layouts.modal')

@section('modaltitle')
    {{ $modaltitle }}
@endsection

@section('modelcontent')
<form method="POST" action="{{ route('admin.items.savemodal') }}" id="add_item_form">
    @csrf
    <div class="modal-body">
        <div id="modalnotify"></div>
        <div class="example-grid">
            <div class="form-group mb-4{{ $errors->has('name') ? ' has-error' : '' }}">
                <label for="name">Item Name</label>
               <input type="text" name="name" class="form-control" placeholder="Item Name" required>
                @if ($errors->has('name'))
                    <small class="error">{{ $errors->first('name') }}</small>
                @endif
            </div>
            <div class="form-group mb-4">
                <label for="description">Description</label>
                <textarea name="description" class="form-control" rows="5" placeholder="Item Description"></textarea>
            </div>
            <div class="form-group mb-0{{ $errors->has('isactive') ? ' has-error' : '' }}">
                <label>Enable</label>
                <div>
                    <label class="radio radio-inline radio-info">
                        <input type="radio" name="isactive" value="1" checked>
                        <span class="input-span"></span>Yes
                    </label>
                    <label class="radio radio-inline radio-info">
                        <input type="radio" name="isactive" value="0">
                        <span class="input-span"></span>No
                    </label>
                </div>
                @if ($errors->has('isactive'))
                    <small class="error">{{ $errors->first('isactive') }}</small>
                @endif
            </div>
        </div>    
    </div>
    <div class="modal-footer">
        <input type="submit" value="Submit" class="btn btn-primary" id="submitbtn">
        <a class="btn btn-secondary" data-dismiss="modal">Cancel</a >
    </div>
</form>
    <script src="{{ asset('/assets/app/js/plugin/jquery.form.min.js') }}"></script>
    <script src="{{ asset('/assets/admin/js/modal/items/create.js') }}"></script>
@endsection
