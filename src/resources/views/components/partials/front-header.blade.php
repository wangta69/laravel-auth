
<div class="container d-flex justify-content-between font-size-12" id="header-top">
  <div></div>
  @guest
  <menu>
    <li><a href="{{ route('login') }}" class="nav-link" href="{{ route('login') }}">로그인</a></li>
    <li><a href="{{ route('register') }}" class="nav-link">회원가입</a></li>
  </menu>
  @else
  <menu>
    <li> <a class="nav-link""><b>{{Auth::user()->name}}</b>님</a></li>
    <li><a class="nav-link" href="{{ route('logout') }}">로그아웃</a></li>     
  </menu>
  @endif
</div>

@section('styles')
@parent
<style>
#header-top {
  padding: 5px;
}
#header-top menu {
  position: relative;
}

#header-top menu li {
  position: relative;
  float: left;
  list-style: none;
  padding-right: 9px;
}
</style>
@endsection

