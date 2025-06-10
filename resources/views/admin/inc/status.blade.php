@if(isset($status))
  @if($status==config('constant.status.active'))
      <span class="badge badge-blue">Yes</span>
  @else
      <span class="badge badge-danger">No</span>
  @endif
@endif
@if(isset($default))
  @if($default==config('constant.default.enabled'))
      <span class="badge badge-blue">Yes</span>
    @else
      <span class="badge badge-danger">No</span>
    @endif
@endif
