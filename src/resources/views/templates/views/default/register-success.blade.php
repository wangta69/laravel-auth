@section('title', '회원가입완료')
<x-pondol-common::app-bare header="pondol-auth::partials.front-header">
  <section>
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-8">
          <div class="card mt-5">
            <div class="card-header">
              <p class="mini-title">{{ Config::get('app.name') }}</p>
              <h3 class="title">로그인</h3>
            </div>
          
            <div class="card-body">
              회원가입이 정상적으로 처리되었습니다.
            </div> <!-- .card-bod -->
            <div class="card-footer text-end">
              <a class="btn btn-primary" href="/">Home</a> 
              @guest
              <a class="btn btn-primary" href="{{ route('login') }}">Login</a>
              @endguest
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