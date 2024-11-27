@section('title', 'Registration')
<x-pondol-common::app-bare header="pondol-auth::partials.front-header">
<section>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card mt-5">
          <div class="card-header">
            <p class="mini-title">{{ Config::get('app.name') }}</p>
            <h3 class="title">2FA Login</h3>
          </div>

          <div class="card-body">
            <h4>One-Time Password</h4>
            <form method="POST" action="{{ route('2fa.validate') }}" >
                @csrf
            <div class="form-floating mb-3">
              <input type="number" class="form-control" name="totp" id="totp">
              <label for="totp">One-Time Password</label>
            </div>
            <x-pondol::validation-fail.first />
            <div class="d-flex align-items-center justify-content-between mt-4">
              <a class="small" href="{{ route('2fa.request') }}">Forgot 2fa?</a>
              <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-mobile-screen-button"></i> Validate
              </button>
            </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
</x-pondol-common::app-bare>
