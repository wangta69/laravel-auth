<x-pondol-common::app>
<div class="wrapper">
  <div class="container-fluid">
  {{ $slot }}
  </div><!--. container -->
</div>


@section('scripts')
<script src="/pondol/auth/admin.js"></script>
@endsection
</x-pondol-common::app>
