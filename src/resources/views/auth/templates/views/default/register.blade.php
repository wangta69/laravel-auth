@extends('auth.templates.layouts.default.front')
@section('title', '회원가입')
@section('content')
<section>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-6">
        <h2 class="title">회원가입</h2>

        <div class="card mt-5">
          <form method="POST" action="{{ route('register') }}" style="width: 100%;">
            @csrf
            <div class="card-body">
              
              <div class="mt-1">
                <a href='/auth/social/github/redirect' class="btn btn-light">Github</a>
                <a href='/auth/social/google/redirect' class="btn btn-light">Google</a>
              </div>
              
              <hr class="hr" />


              <div class="mt-1">
              서비스 이용을 위해 필수 계정정보를 입력해주세요.
              </div>


              <div class="input-group mt-1">
                <span class="input-group-text"><i aria-hidden="true" class="fas fa-envelope"></i></span>
                <input type="text" name="email" value="{{ old('email') }}" placeholder="이메일" class="form-control"/>
                <button class="btn btn-secondary act-check-email" type="button">확인</button>
              </div>

              <div class="input-group mt-1">
                <span class="input-group-text"><i class="fa fa-user-tag"></i></span>
                <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="이름" class="form-control"/>
              </div>

              <div class="input-group mt-1">
                <span class="input-group-text"><i class="fa fa-unlock"></i></span>
                <input type="password" name="password" placeholder="비밀번호 (8자리이상)" class="form-control" />
              </div>

              <div class="input-group mt-1">
                <span class="input-group-text"><i class="fa fa-unlock"></i></span>
                <input type="password" name="password_confirmation" placeholder="비밀번호 확인" class="form-control"/>
              </div>
            </div><!-- .card-body -->
            <div class="card-footer text-end">
              <!-- <div class="alert alert-danger d-flex align-items-center" role="alert">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Warning:">
                  <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                </svg>
                <div>
                  An example alert with an icon
                </div>
              </div> -->
              <button type="submit" class="btn btn-primary">
                회원가입
              </button>
            </div><!-- .card-footer -->
          </form>
        </div><!-- .card -->
      </div><!-- col-lg-6-->
    </div><!-- row justify-content-center -->
  </div><!-- .container -->
</section>


@endsection

@section ('styles')
@parent

@endsection


@section ('scripts')
@parent
<script>


$(function(){

  @if ($errors->any())
  showToaster({title: '알림', message: '{{$errors->first()}}'});
  @endif


  $("#check-all").on('click', function(){
    var checked = $(this).is(":checked");
    $('.act-check-aggrement').each(function() {
      $(this).prop('checked', checked);
    });
  })
  $(".act-check-email").on('click', function(){
    var email = $("input[name=email]").val();
    if (!email) {
      return showToaster({title: '알림', message: '이메일을 입력해주세요'});
    }
    AUTH.ajaxroute('get', 
    {'name': 'validation.email', 'params[0]':email}, 
		{}, 
    function(resp) {
      if(resp.error) {
        showToaster({title: '알림', message: resp.error});
      } else {
        showToaster({title: '알림', message: '사용가능한 이메일입니다.', alert: false});
      }
    })
  })



})
</script>
@endsection
