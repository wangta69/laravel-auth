@extends('auth.app')
@section('title', '회원탈퇴')
@section('content')
<section>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card mt-5">
          <div class="card-header">
            <p class="mini-title">{{ Config::get('app.name') }}</p>
            <h3 class="title">회원탈퇴 완료</h3>
          </div>

          <div class="card-body">

            <p>그동안 {{ Config::get('app.name') }}을 이용해 주셔서 감사합니다.</p>



          </div> <!-- .card-body -->
          <div class="card-footer text-end">
            <a class="btn btn-primary" href="{{ route('main') }}">쇼핑몰 메인</a>

          </div>

        </div>
      </div>
    </div>
  </div>
</section>

@endsection

@section ('styles')
@parent
@endsection

@section ('scripts')
@parent
<script>
function checkConfirm() {
  if(confirm('탈퇴시 회원님과 관련된 모든 데이타가 삭제됩니다.\n\n 탈퇴하시겠습니까?')) {
    return true;
  } else {
    return false;
  }
  
}
</script>
@endsection
