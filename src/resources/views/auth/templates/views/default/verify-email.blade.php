@extends('auth.templates.layouts.default.front')
@section('title', '회원가입')
@section('content')
<section>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-6">
        <h2 class="title">회원가입</h2>

        <div class="card">
          <div class="card-body">
            <div class="mb-4 text-sm text-gray-600">
              {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
            </div>

            @if (session('status') == 'verification-link-sent')
            <div class="mb-4 font-medium text-sm text-green-600">
              {{ __('A new verification link has been sent to the email address you provided during registration.') }}
            </div>
            @endif

            <div class="mt-4 flex items-center justify-between">
              <form method="POST" action="{{ route('verification.send') }}">
                @csrf

                <div>
                  <button class="btn btn-info">
                    {{ __('Resend Verification Email') }}
                  </button>
                </div>
              </form>

              <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button type="submit" class="btn btn-primary">
                  {{ __('Log Out') }}
                </button>
              </form>
            </div>
          </div> <!-- .card-body -->
        </div><!-- .card -->
      </div><!-- col-lg-6-->
    </div><!-- row justify-content-center -->
  </div><!-- .container -->
</section>
@endsection
