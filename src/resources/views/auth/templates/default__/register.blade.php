@extends('auth.app')
@section('title', '회원가입')
@section('content')
<section>
  <div class="container body mt-5">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <h2 class="title">회원가입</h2>

        <div class="card">
          <form method="POST" name="register-form" action="{{ route('register') }}">
            @csrf
            @foreach($agreements as $k=>$v)
              <input type="hidden" name="{{$k}}" value="{{$v}}">
            @endforeach
            
            <div class="card-body">
              <div class="mt-1">
              서비스 이용을 위해 필수 계정정보를 입력해주세요.
              </div>

                <div class="mb-2">
                  <a href='/auth/social/github/redirect' class="btn btn-light">Github</a>
                  <a href='/auth/social/google/redirect' class="btn btn-light">Google</a>
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
                  <span class="input-group-text"><i class="fa-solid fa-square-phone"></i></span>
                  <input type="text" id="mobile" name="mobile" value="{{ old('mobile') }}" placeholder="연락처" class="form-control" onkeyup="this.value = add_phone_hyphen(this.value)"/>
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
<style>
  form i {
    width: 20px;
  }
</style>

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
    {route: 'auth.validation.email', segments:[email]}, 
    function(resp) {
      if(resp.error) {
        showToaster({title: '알림', message: resp.error});
      } else {
        showToaster({title: '알림', message: '사용가능한 이메일입니다.', alert: false});
      }
    })
  })

  $("#check-all").click(function(){
    $(".necessary-check").prop('checked', $(this).is(":checked"));
  })

})
</script>
@endsection
