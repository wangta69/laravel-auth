@section('title', '정보변경')
<x-pondol-common::app-bare header="pondol-auth::partials.front-header">
<section>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <h2 class="title">정보변경</h2>
        <form method="POST" action="{{ route('user.edit') }}" style="width: 100%;">
        @method('PUT')  
        @csrf
          
          <div class="card mt-5">
            <div class="card-body">
            정보변경
              <hr class="hr" />


              <div class="form-floating mt-1">
                <input type="text" name="email" id="inputEmail" value="{{ old('email', $user->email) }}" readonly disabled class="form-control"/>
                <label for="inputEmail">Email address</label>
              </div>

              <div class="form-floating mt-1">
                <input type="text" id="inputName" name="name" value="{{ old('name', $user->name) }}" class="form-control"/>
                <label for="inputName">Your name</label>
              </div>
              <hr class="hr" />
              <div class="form-floating mt-1">
                <input type="password" id="inputPassword" name="password" value="" class="form-control"/>
                <label for="inputPassword">Please enter your current password</label>
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