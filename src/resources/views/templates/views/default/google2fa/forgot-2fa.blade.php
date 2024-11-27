@section('title', '2FA Reset')
<x-pondol-common::app-bare header="pondol-auth::partials.front-header">
<section>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card mt-5">
          <div class="card-header">
            <p class="mini-title">{{ Config::get('app.name') }}</p>
            <h3 class="title">2FA Reset</h3>
          </div>
          <form action="{{ route('2fa.cleare') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">
            <div class="card-body">
              
              <div class="form-group row">
                <label for="email_address" class="col-md-4 col-form-label text-md-right">E-Mail Address</label>
                <div class="col-md-6">
                  <input type="text" id="email_address" class="form-control" name="email" required autofocus>
                  <x-pondol::validation-fail.has field="email" />
                  @if (session()->get('status'))
                  <span class="text-success">{{ session()->get('status') }}</span>
                  @endif
                </div>
              </div>

              
            </div><!-- .card-body -->
            <div class="card-footer text-end">
              <button type="submit" class="btn btn-primary">
                  2fa reset
              </button>
            </div>
          </form>
        </div> <!--  .card -->
      </div>
    </div>
  </div>
</section>
</x-pondol-common::app-bare>