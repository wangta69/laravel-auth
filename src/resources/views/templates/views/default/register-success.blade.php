@section('title', '회원가입')
<x-pondol-common::app-bare header="pondol-auth::partials.front-header">
  <section>
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-6">
          <h2 class="title">회원가입 완료</h2>
          <div class="card">
            <div class="card-body">
              회원가입이 정상적으로 처리되었습니다.
            </div> <!-- .card-bod -->
            <div class="card-footer">
              <a class="btn btn-primary" href="/">Home</a> 
              <a class="btn btn-primary" href="{{ route('login') }}">Login</a>
            </div><!-- .card-footer -->
          </div> <!-- .card -->
        </div><!-- . class="col-lg-6" -->
      </div> <!--class="row justify-content-center" -->
    </div><!-- .container -->
  </section>

@section ('styles')
@parent
<link href="{{ asset('/assets/front/css/register.css') }}" rel="stylesheet">
@endsection

</x-pondol-common::app-bare>