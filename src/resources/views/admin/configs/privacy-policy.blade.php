@section('title', '개인정보 수집 및 허용')
<x-pondol-auth::admin :path="['환경설정', '개인정보 수집 및 허용']">
<div class="p-3 mb-4 bg-light rounded-3">
  <h2 class="fw-bold">개인정보 수집 및 허용 설정</h2>

  <div class="card">
    <div class="card-body">
      <div>"개인정보 수집 및 허용"을 변경할 수 있습니다.</div>
    </div><!-- .card-body -->
  </div><!-- .card -->
</div>

<div class="card">
  <form name="user-form">
    @csrf
    @method('PUT')
    <input type="hidden" name="key" value="{{$key}}">
    <div class="card-body">
      <div class="row mt-1">
        <div class="col-2">
          <label class='form-label'>개인정보 수집 및 이용</label>
        </div>

        <div class="col-10">
         @include ('editor::default', ['name'=>'value', 'id'=>'privacy-policy', 'value'=>$msg, 'attr'=>['class'=>'form-control']])
        </div>

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
    {route: 'auth.admin.config.agreement', data:$("form[name='user-form']").serializeObject()}, 
    function(resp) {
      console.log('resp');
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