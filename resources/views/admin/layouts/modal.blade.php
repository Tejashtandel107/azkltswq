<?php 
    $modalsize = isset($modalsize) ? $modalsize : '';
?>
<div class="modal fade" id="modalmain" role="dialog" tabindex="-1">
    <div class="modal-dialog {{$modalsize}}">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@yield('modaltitle')</h5>
                <button type="button" class="close" aria-hidden="true" data-dismiss="modal">×</button>
            </div>
            @yield('modelcontent')
        </div>
    </div>
</div>
