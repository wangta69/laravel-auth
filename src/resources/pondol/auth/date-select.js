// jQuery( document ).ready(function( $ ) {
  $(function(){
    // 날짜 검색
    $( "#from-date").datepicker({
      dateFormat: 'yy-mm-dd',
    });
  
    $(".from-calendar").on('click', function(){
      $( "#from-date" ).datepicker( "show" );
    })
  
    $( "#to-date").datepicker({
      dateFormat: 'yy-mm-dd',
    });
  
    $(".to-calendar").on('click', function(){
      $( "#to-date" ).datepicker( "show" );
    })
  
    $(".act-set-date").on('click', function(){
      var day = $(this).attr("user-attr-term");
      var settingDate = new Date();
  
      settingDate.setDate(settingDate.getDate()-day); //하루 전
      $( "#from-date" ).datepicker( "setDate", settingDate );
      $( "#to-date" ).datepicker( "setDate", new Date() );
    })
    // 기간설정 후 검색
    $(".btn-serch-date").on('click', function(){
      $("#search-form").trigger( "submit" );
    })
    // 키워드 검색
    $(".btn-serch-keyword").on('click', function(){
      $("#search-form").trigger( "submit" );
    })
  })
  