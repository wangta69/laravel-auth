@section('title', '패스워드변경')
<x-pondol-common::app-bare header="pondol-auth::partials.front-header">
<section>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <h2 class="title">패스워드변경</h2>
        <form method="POST" action="{{ route('user.change-password') }}" style="width: 100%;">
        @method('PUT')  
        @csrf
          
          <div class="card mt-5">
            <div class="card-body">
            패스워드변경
              <hr class="hr" />


              <div class="form-floating mt-1">
                <input type="password" name="password" id="inputPassword" value="" class="form-control"/>
                <label for="inputPassword">New password</label>
              </div>

              <div class="form-floating mt-1">
                <input type="password" id="inputPasswordConfirmation" name="password_confirmation" value="" class="form-control"/>
                <label for="inputPasswordConfirmation">Confirm password</label>
              </div>
              <hr class="hr" />
              <div class="form-floating mt-1">
                <input type="password" id="inputCurrentPassword" name="current_password" value="" class="form-control"/>
                <label for="inputCurrentPassword">current password</label>
              </div>

            </div><!-- .card-body -->
            <x-pondol::validation-fail.first />
            <div class="card-footer text-end">
              <button type="submit" class="btn btn-primary">
                변경하기
              </button>
            </div><!-- .card-footer -->
          </form>
        </div><!-- .card -->
      </div><!-- col-lg-6-->
    </div><!-- row justify-content-center -->
  </div><!-- .container -->
</section>
@section ('styles')
@parent
<style>
  .input-group-text { width: 45px}
</style>
@endsection

</x-pondol-common::app-bare>