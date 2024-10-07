@extends('auth.app')

@section('body_class', '')

@section('page')

  @include('auth.templates.layouts.default.front-header')
  @yield('content')
  @include('auth.templates.layouts.default.front-footer')

  <!-- toaseer box start -->
  <div class="bg-body-secondary position-relative bd-example-toasts rounded-3">
    <div class="toast-container  position-fixed bottom-0 end-0 p-3" id="toast-container">
    </div>
  </div>
  <!-- toaseer box end -->
  <!-- toaseer toast-placement start -->
  <div id="toast-placement">
  <!--   <div class="toast toast-placement" role="status" aria-live="polite" aria-atomic="true" > -->
    <div class="toast toast-placement" role="alert" aria-live="assertive" aria-atomic="true" >
      <div class="toast-header">
        <strong class="me-auto"></strong>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>

      <div class="toast-body">
        
      </div>
      <div class="toast-footer text-end d-none">
        <button class="btn btn-sm act-toast-to">바로가기</button>
      </div>
    </div>
  </div>
  <!-- toaseer toast-placement end -->

@stop

@section('styles')
@parent

@endsection

@section('scripts')
@parent

<script>
function documentLoaded() {
  if ($("body").height() > $(window).height()) {
    $('footer').removeClass('fixed-bottom');
  }
}
document.addEventListener("DOMContentLoaded", documentLoaded);
</script>
@endsection
