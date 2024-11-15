
<ul class="nav nav-tabs mb-1">
  <li class="nav-item"><a  class="nav-link  {{ request()->routeIs(['auth.admin.user*']) ? 'active' : '' }}" href="{{ route('auth.admin.user', [$id]) }}">회원정보</a></li>
  {{--<li class="nav-item @if (Request::is('admin/account/*')) on @endif"><a href="/admin/user/accounts/{{$id}}">계좌정보</a></li>--}}
  {{--<li class=" @if (Request::is('admin/points/*')) on @endif"><a href="{{ route('admin.points.user', [$id]) }}">자산정보</a></li>--}}
  {{--<li class=" @if (Request::is('admin/players/*')) on @endif"><a href="{{ route('admin.players.user', [$id]) }}">참여정보</a></li>--}}
  {{--<li class=" @if (Request::is('admin/memos/*')) on @endif"><a href="{{ route('admin.memo.user', [$id]) }}">쪽지</a></li>--}}
  {{--<li class=" @if (Request::is('admin/user/login-history*')) on @endif"><a href="{{ route('admin.login-history.user', [$id]) }}">방문정보</a></li>--}}
    <!-- <li>포인트</li> -->
</ul>
