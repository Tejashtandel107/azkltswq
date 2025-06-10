@extends('admin.layouts.modal')

@section('modaltitle')
    {{ $modaltitle }}
@endsection

@section('modelcontent')
<form action="{{ route('admin.customers.savemodal') }}" method="POST" id="add_customer_form">
    @csrf
    <div class="modal-body">
        <div id="modalnotify"></div>
        <div class="example-grid">
            <div class="form-group mb-4 {{ $errors->has('companyname') ? ' has-error' : '' }}">
                <label for="companyname">Company Name</label>
                <input type="text" name="companyname" class="form-control" placeholder="Company Name" required>
                @if ($errors->has('companyname'))
                    <small class="error">{{ $errors->first('companyname') }}</small>
                @endif
            </div>            
            <div class="form-group mb-4">
                <label for="phone">Phone</label>
                <div class="input-group mb-2 date">
                    <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                    <input type="text" name="phone" class="form-control" placeholder="Phone" maxlength="15">
                </div>    
            </div>
            <div class="form-group mb-4">
                <label for="contact_person">Contact Person</label>
                <input type="text" name="contact_person" class="form-control" placeholder="Contact Person" maxlength="255">
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
    <script src="{{ asset('/assets/admin/js/modal/customers/create.js') }}"></script>
@endsection
