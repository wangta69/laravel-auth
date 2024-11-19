@section('title', 'Reset Password')
<x-pondol-common::app-bare header="pondol-auth::partials.front-header">
<section>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card mt-5">
          <div class="card-header">
            <p class="mini-title">{{ Config::get('app.name') }}</p>
            <h3 class="title">패스워드 초기화</h3>
          </div>
          <div class="card-body">
            <form method="POST" action="{{ route('password.update') }}">
              @csrf
              <!-- Password Reset Token -->
              <input type="hidden" name="token" value="{{ $request->route('token') }}">

              <!-- Email Address -->
              <div class="form-floating mb-3">
                <input id="email" class="form-control" type="email" name="email" value="{{old('email', $request->email)}}" required autofocus />
                <label for="email">이메일</label>
              </div>

              <!-- Password -->
              <div class="form-floating mb-3">
                <input id="password" class="form-control" type="password" name="password" required />
                <label for="password"> 패스워드</label>
              </div>

              <!-- Confirm Password -->
              <div class="form-floating mb-3">
                <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required />
                <label for="password_confirmation">Confirm Password</label>
              </div>
              <x-pondol::validation-fail.first />
              <div class="d-flex justify-content-end  mt-4">
                  <button type="submit" class="btn btn-primary">
                      {{ __('Reset Password') }}
                  </button>
              </div>
            </form>
          </div><!-- .card-body>
        </card>
      </div><!-- .col-md-8 -->
    </div><!-- .row.justify-content-center" -->
  </div><!-- .container -->
</section>
</x-pondol-common::app-bare>