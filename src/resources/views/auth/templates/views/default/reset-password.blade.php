@extends('auth.templates.layouts.default.front')
@section('title', '회원가입')
@section('content')
<section>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-6">

        <form method="POST" action="{{ route('password.update') }}">
          @csrf

          <!-- Password Reset Token -->
          <input type="hidden" name="token" value="{{ $request->route('token') }}">

          <!-- Email Address -->
          <div>
            <label for="email">이메일</label>

            <input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus />
          </div>

          <!-- Password -->
          <div class="mt-4">
            <label for="password"> 패스워드</label>

            <input id="password" class="block mt-1 w-full" type="password" name="password" required />
          </div>

          <!-- Confirm Password -->
          <div class="mt-4">
            <label for="password_confirmation">Confirm Password</label>

            <input id="password_confirmation" class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation" required />
          </div>

          <div class="flex items-center justify-end mt-4">
              <button type="submit" class="btn btn-primary">
                  {{ __('Reset Password') }}
              </button>
          </div>
        </form>
      </div><!-- .col-lg-6 -->
    </div><!-- .row.justify-content-center" -->
  </div><!-- .container -->
</section>
@endsection