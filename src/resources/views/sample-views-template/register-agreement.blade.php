@extends('market.templates.layouts.'.config('pondol-market.template.layout.theme').'.front')
@section('title', '회원가입 > 약관동의')
@section('content')
<section>
  <div class="container body mt-5">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <h2 class="title">약관동의</h2>

          <form name="regist_agree_form">
            <div class="form-check form-check-inline mt-4">
              <input type="checkbox" class="form-check-input" id="terms-of-use" 
                name="aggree_terms_of_use" value="1" 
                @if(old('aggree_terms_of_use')) checked @endif>
              <label class="form-check-label" for="terms-of-use">이용약관 동의 (필수)</label>
            </div>

            <div class="overflow-auto mt-2">
                {!! $termsOfUse !!}
            </div>

            <div class="form-check form-check-inline mt-4">
              <input type="checkbox" class="form-check-input" id="collection-and-use-of-personal-information" 
              name="privacy_policy" value="1" 
              @if(old('privacy_policy')) checked @endif>
              <label class="form-check-label" for="collection-and-use-of-personal-information">개인정보 수집 및 이용동의 (필수)</label>
            </div>

            <div class="overflow-auto mt-2">
              {!! $privacyPolicy !!}
            </div>

              
            <div class="text-end mt-2 mb-4">

              <button type="button" class="btn btn-light" onclick="window.location.href='{{url()->previous()}}'">
                  취소
              </button>
              <button type="button" class="btn btn-primary act-store-agreement">
                  다음
              </button>
            </form>
          </div><!-- .card-footer -->
      </div><!-- col-lg-6-->
    </div><!-- row justify-content-center -->
  </div><!-- .container -->
</section>

<!-- Modal -->
@endsection

@section ('styles')
@parent
<style>
.overflow-auto {
  height: 300px;
  border: 1px solid #dee2e6;
}
</style>
@endsection


@section ('scripts')
@parent
<script>


$(function(){

  // @if ($errors->any())
  // showToaster({title: '알림', message: '{{$errors->first()}}'});
  // @endif


  // $("#check-all").on('click', function(){
  //   var checked = $(this).is(":checked");
  //   $('.act-check-aggrement').each(function() {
  //     $(this).prop('checked', checked);
  //   });
  // })

  $(".act-store-agreement").on('click', function(){
    ROUTE.ajaxroute('post', 
    {route: 'market.register.agreement', data: $('form[name=regist_agree_form]').serializeObject()}, 
    function(resp) {
      if(resp.error) {
        showToaster({title: '알림', message: resp.error});
      } else {
        window.location.href=resp.next;
        // location.reload();
      }
    })
  })

})
</script>
@endsection
