@section('title', '회원탈퇴')
<x-pondol-common::app-bare header="pondol-auth::partials.front-header">
<section>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card mt-5">
          <div class="card-header">
            <p class="mini-title">{{ Config::get('app.name') }}</p>
            <h3 class="title">회원탈퇴</h3>
          </div>
          <form method="POST" action="{{ route('cancel.account') }}" onsubmit="return checkConfirm()">
            @csrf
            @method('DELETE')
            <div class="card-body">

              <p>탈퇴를 원하시면 현재 패스워드를 입력하시고 "회원탈퇴" 버튼을 클릭해 주세요 </p>

              <div class="form-floating mb-3">
                <input class="form-control" id="inputPassword" type="password" name="password" value="" placeholder="Password">
                <label for="inputPassword">Password</label>
                <x-pondol::validation-fail.has field="password" />
              </div>
            </div> <!-- .card-body -->
            <div class="card-footer text-end">
              <button type="submit" class="btn btn-primary">회원탈퇴</button>
              @if ( session('error'))
              <span class="invalid-feedback" role="alert" style="display: block;">
                {{ session('error') }}
              </span>
              @endif
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

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
</x-pondol-common::app-bare>