@section('title', 'OTP-설정')
<x-pondol-common::app-bare header="pondol-auth::partials.front-header">

<section>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card mt-5">
          <div class="card-header">
            <p class="mini-title">{{ Config::get('app.name') }}</p>
            <h3 class="title">2FA OTP-설정</h3>
          </div>

          <div class="card-body">

            <select class="form-select" disabled>
              <option value="">2FA Secret Key</option>
            </select>
          </div> <!-- .card-body -->

          <div class="card-body text-center">
            <p>구글 OTP 앱을 여신 후 아래 이미지를 스캔하세요</p>
            <div class="pt-5 pb-5">
            {!! $image !!}
            </div>
            <p>구글 OTP 앱이 바코드를 인식하지 못하면 아래 숫자를 입력하세요</p>
            enter in the following number: <code>{{ $secret }}</code>
            <br /><br />
          </div> <!-- .card-body -->

          <div class="card-body">
              <div class="card-title">구글OTP 설치</div>

              <div class="panel-body">
                  안드로이드버젼 : "Google Authenticator" 검색 및 설치
                  <br />
                  IOS 버전 : "Google Authenticator" 검색 및 설치
              </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</section>
</x-pondol-common::app-bare>