@section('title', '로그인')
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
            <a href='/auth/social/github/redirect' class="btn btn-light">Github</a>
            <a href='/auth/social/google/redirect' class="btn btn-light">Google</a>
            <a href='/auth/social/naver/redirect' class="btn btn-light">Naver</a>
            <a href='/auth/social/kakao/redirect' class="btn btn-light">KaKao</a>

            <form method="POST" action="{{ route('login') }}" style="width: 100%;">
                @csrf
            <div class="">
            <div class="form-floating mb-3">
              <input class="form-control" id="inputEmail" type="email" name="email"  value="{{ old('email') }}" placeholder="name@example.com">
              <label for="inputEmail">Email address</label>
              <x-pondol::validation-fail.has field="email" />
            </div>

            <div class="form-floating mb-3">
              <input class="form-control" id="inputPassword" type="password" name="password" value="" placeholder="Password">
              <label for="inputPassword">Password</label>
              <x-pondol::validation-fail.has field="password" />
            </div>
            
            <div class="form-check mb-3">
              <input class="form-check-input" id="inputRememberPassword" type="checkbox" value="">
              <label class="form-check-label" for="inputRememberPassword">Remember Password</label>
            </div>
            <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
              <a class="small" href="{{ route('password.request') }}">Forgot Password?</a>

              <button type="submit" class="btn btn-primary">로그인</button>
              <x-pondol::validation-fail.custom />
            </div>
            </form>
          </div>
          @if($f) 
          <h3>비회원 주문 확인</h3>
          <div class="card-body">
              * 주문번호를 입력하세요

              <form method="get" action="{{ route('market.mypage.order') }}" style="width: 100%;">
              <div class="">
              <div>
              <label>주문번호</label>
                  <input class="form-control" type="text" name="o_id"  value="{{ old('o_id') }}" placeholder="">
              </div>
              <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                  <button type="submit" class="btn btn-primary">주문확인</button>
              </div>
              </form>
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</section>
</x-pondol-common::app-bare>