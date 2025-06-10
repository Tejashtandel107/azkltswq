@extends('admin.layouts.modal')

@section('modaltitle')
    {{ $modaltitle }}
@endsection

@section('modelcontent')
<form method="POST" action="{{ route('admin.item-marka.savemodal') }}" id="add_marka_form">
    @csrf
    <div class="modal-body">
        <div id="modalnotify"></div>
        <div class="example-grid">
            <div class="form-group mb-4 {{ $errors->has('item_id') ? ' has-error' : '' }}">
                <label for="item_id">Item</label>
                <select name="item_id" class="form-control" required>
                    <option value="">Select Item</option>
                    @foreach($items as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
                @if ($errors->has('item_id'))
                    <small class="error">{{ $errors->first('item_id') }}</small>
                @endif
            </div>
            <div class="form-group mb-4 {{ $errors->has('name') ? ' has-error' : '' }}">
                <label for="name">Marka Name</label>
                <input type="text" name="name" class="form-control" placeholder="Marka Name" required>
                @if ($errors->has('name'))
                    <small class="error">{{ $errors->first('name') }}</small>
                @endif
            </div>
            <div class="form-group mb-4">
                <label for="description">Description</label>
                <textarea name="description" class="form-control" rows="5" placeholder="Marka Description"></textarea>
            </div>            
        </div>    
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary" id="submitbtn">Submit</button>
        <a class="btn btn-secondary" data-dismiss="modal">Cancel</a >
    </div>
</form>
    <script src="{{ asset('/assets/app/js/plugin/jquery.form.min.js') }}"></script>
    <script src="{{ asset('/assets/admin/js/modal/item-marka/create.js') }}"></script>
@endsection
