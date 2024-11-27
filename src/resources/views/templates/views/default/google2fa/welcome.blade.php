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
              <option value="">Two-Factor Authentication</option>
            </select>
          </div> <!-- .card-body -->

          <div class="card-footer text-end">
            @if (Auth::user()->google2fa_secret)
            <a href="{{ route('2fa.disable') }}" class="btn btn-warning">Disable 2FA</a>
            @else
            <a href="{{ route('2fa.enable') }}" class="btn btn-primary">Enable 2FA</a>
            @endif

          </div>

        </div>
      </div>
    </div>
  </div>
</section>
</x-pondol-common::app-bare>

