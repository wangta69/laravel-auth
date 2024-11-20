@section('title', '회원정보')
<x-pondol-common::app-bare header="pondol-auth::partials.front-header">
<section>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <h2 class="title">회원정보</h2>

        <div class="card mt-5">
          <div class="card-body">
            프로파일
            <hr class="hr" />
            <div class="input-group mt-1">
              <span class="input-group-text"><i aria-hidden="true" class="fas fa-envelope"></i></span>
              <input type="text" name="email" value="{{ old('email', $user->email) }}" readonly class="form-control"/>
            </div>

            <div class="input-group mt-1">
              <span class="input-group-text"><i class="fa fa-user-tag"></i></span>
              <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" readonly class="form-control"/>
            </div>
          </div><!-- .card-body -->

          <div class="card-footer text-end">
            <a class="btn btn-primary" href="{{route('user.edit')}}">
              정보변경
            </a>
          </div><!-- .card-footer -->

        </div><!-- .card -->
      </div><!-- col-md-8-->
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