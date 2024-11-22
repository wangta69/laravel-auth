@section('title', '일반환경설정')
<x-pondol-auth::admin :path="['환경설정', '회원가입 설정']">
<div class="p-3 mb-4 bg-light rounded-3">
  <h2 class="fw-bold">일반환경설정</h2>

  <div class="card">
    <div class="card-body">
      <div>회원과 관련한 다양한 설정을 변경할 수 있습니다.</div>
    </div><!-- .card-body -->
  </div><!-- .card -->
</div>

<div class="card">
  <form name="user-form">
    @csrf
    @method('PUT')
    <div class="card-body">
      <div class="input-group">
        <label for='name' class=' col-4 col-xl-2 col-form-label'>회원 활성</label>
        <select class="form-select" name="activate">
          <option value="auto" @if($user['activate'] == "auto") selected @endif>회원가입시</option>
          <option value="email" @if($user['activate'] == "email") selected @endif>이메일 인증시</option>
          <option value="admin" @if($user['activate'] == "admin") selected @endif>관리자 별도 인증</option>
        </select>
      </div>
      <div class="input-group mt-1">
        <label class="col-form-label col-4 col-xl-2">가입단계</label>
        <select class="form-select" name="step">
          @foreach($templates['user'] as $v)
          <option value="{{$v}}" @if($v == $user['template']['user']) selected @endif>{{$v}}</option>
          @endforeach
        </select>
      </div>
      <div class="input-group mt-1">
        <label class="col-form-label col-4 col-xl-2">User template</label>
        <select class="form-select" name="t_user">
          @foreach($templates['user'] as $v)
          <option value="{{$v}}" @if($v == $user['template']['user']) selected @endif>{{$v}}</option>
          @endforeach
        </select>
      </div>

      <div class="input-group mt-1">
        <label class="col-form-label col-4 col-xl-2">Mail template</label>
        <select class="form-select" name="t_mail">
          @foreach($templates['mail'] as $v)
          <option value="{{$v}}" @if($v == $user['template']['mail']) selected @endif>{{$v}}</option>
          @endforeach
        </select>
      </div>

      <div class="input-group mt-1">
        <label class="col-form-label col-4 col-xl-2">2차인증</label>
        <select class="form-select" name="second">
          @foreach($templates['mail'] as $v)
          <option value="{{$v}}" @if($v == $user['template']['mail']) selected @endif>{{$v}}</option>
          @endforeach
        </select>
      </div>
      <div class="input-group mt-1">
        <label class="col-form-label col-4 col-xl-2">회원가입 포인트</label>
        <input type="number" name="r_point" value=""  class="form-control">
      </div>
      <div class="input-group mt-1">
        <label class="col-form-label col-4 col-xl-2">로그인 지급 포인트</label>
        <input type="number" name="l_point" value=""  class="form-control">
      </div>
    </div> <!-- .card-body -->

    <div class="card-footer text-end">
      <!-- <button type="submit"class="btn btn-primary">적용</button> -->
      <button type="button"class="btn btn-primary act-update-user">적용</button>
    </div> <!-- .card-footer -->
  </form>
</div><!-- .card -->

@section('scripts')
@parent
<script>
$(function(){
  $(".act-update-user").on('click', function(){
    ROUTE.ajaxroute('put', 
    {route: 'auth.admin.config.update', data:$("form[name='user-form']").serializeObject()}, 
    function(resp) {
      if(resp.error) {
        showToaster({title: '알림', message: resp.error});
      } else {
        showToaster({title: '알림', message: '처리되었습니다.', alert: false});
      }
    })
  })
})
</script>
@endsection
</x-pondol-auth::admin>