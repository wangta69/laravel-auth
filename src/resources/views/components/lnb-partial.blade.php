{{-- src/resources/views/components/lnb-partial.blade.php --}}
<li>
    <a href="#member-sub-menu" data-bs-toggle="collapse"
        aria-expanded="{{ request()->routeIs(['auth.admin.users*']) ? 'true' : 'false' }}" class="dropdown-toggle">
        <i class="fa-solid fa-user"></i>
        회원관리
    </a>
    <ul class="collapse list-unstyled {{ request()->routeIs(['auth.admin.user*']) ? 'show' : '' }}" id="member-sub-menu">
        <li class="{{ request()->routeIs(['auth.admin.users*']) ? 'current-page' : '' }}">
            <a href="{{ route('auth.admin.users') }}">회원</a>
        </li>
        <li class="{{ request()->routeIs(['auth.admin.user.create*']) ? 'current-page' : '' }}">
            <a href="{{ route('auth.admin.user.create') }}">회원등록</a>
        </li>
    </ul>
</li>
<li>
    <a href="#member-config-sub-menu" data-bs-toggle="collapse"
        aria-expanded="{{ request()->routeIs(['auth.admin.config*']) ? 'true' : 'false' }}" class="dropdown-toggle">
        <i class="fa-solid fa-gear"></i>
        회원 환경설정
    </a>
    <ul class="collapse list-unstyled {{ request()->routeIs(['auth.admin.config*']) ? 'show' : '' }}"
        id="member-config-sub-menu">
        <li class="{{ request()->routeIs(['auth.admin.config']) ? 'current-page' : '' }}">
            <a href="{{ route('auth.admin.config') }}">일반환경설정</a>
        </li>
        <li class="{{ request()->routeIs(['auth.admin.config.agreement.termsofuse']) ? 'current-page' : '' }}">
            <a href="{{ route('auth.admin.config.agreement.termsofuse') }}">이용약관</a>
        </li>
        <li class="{{ request()->routeIs(['auth.admin.config.agreement.privacypolicy']) ? 'current-page' : '' }}">
            <a href="{{ route('auth.admin.config.agreement.privacypolicy') }}">개인정보 수집 및 허용</a>
        </li>
    </ul>
</li>
