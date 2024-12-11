@section('title', '회원가입')
<x-pondol-common::app-bare header="pondol-auth::partials.front-header">
<section>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <h2 class="title">회원가입</h2>

        <div class="card mt-5">
          <form method="POST" action="{{ route('register') }}" style="width: 100%;">
            @csrf
            <div class="card-body">
              
              <div class="mt-1">
                <a href='/auth/social/github/redirect' class="btn btn-light">Github</a>
                <a href='/auth/social/google/redirect' class="btn btn-light">Google</a>
                <a href='/auth/social/naver/redirect' class="btn btn-light">Naver</a>
                <a href='/auth/social/kakao/redirect' class="btn btn-light">KaKao</a>
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
            <x-pondol::validation-fail.first />
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
@section ('styles')
@parent
<style>
  .input-group-text { width: 45px}
</style>
@endsection

@section ('scripts')
@parent
<script>
$(function(){
{{-- 
  @if ($errors->any())
  showToaster({title: '알림', message: '{{$errors->first()}}'});
  @endif
  --}}

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
    ROUTE.ajaxroute('get', 
    {route: 'validation.email', segments:[email]}, 
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
</x-pondol-common::app-bare>