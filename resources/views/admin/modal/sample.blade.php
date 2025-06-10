@extends('admin.layouts.modal')

@section('modaltitle')
Sample Demo
@endsection

@section('modelcontent')
    <div class="modal-body">
        <div class="text-center mt-3 mb-4"><i class="ti-lock timeout-icon"></i></div>
        <div class="text-center h4 mb-3">Set Auto Logout</div>
        <p class="text-center mb-4">You are about to be signed out due to inactivity.<br>Select after how many minutes of inactivity you log out of the system.</p>
        <div id="timeout-reset-box" style="display:none;">
            <div class="form-group text-center">
                <button class="btn btn-danger btn-fix btn-air" id="timeout-reset">Deactivate</button>
            </div>
        </div>
    </div>
@endsection
