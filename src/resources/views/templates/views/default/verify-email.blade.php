@section('title', 'Email verification')
<x-pondol-common::app-bare header="pondol-auth::partials.front-header">
  <section>
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-8">
          <h2 class="title">Email verification</h2>

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
            </div> <!-- .card-body -->
            <div class="card-footer d-flex justify-content-end">
                <form method="POST" action="{{ route('verification.send') }}">
                  @csrf
                    <button class="btn btn-info me-1">
                      {{ __('Resend Verification Email') }}
                    </button>
                </form>

                <a href="{{ route('logout') }}" class="btn btn-primary">
                  {{ __('Log Out') }}
                </a>

              </div>
            </div> <!-- .card-body -->
          </div><!-- .card -->
        </div><!-- col-lg-6-->
      </div><!-- row justify-content-center -->
    </div><!-- .container -->
  </section>
  </x-pondol-common::app-bare>
