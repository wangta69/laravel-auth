<nav id="sidebar">
  <div class="sidebar-header">
    <h3><a href="{{ route('auth.admin.dashboard') }}">OnStory</a></h3>
    <strong>ON</strong>
  </div>

  <ul class="list-unstyled components" id="navbar-sidebar">
    <li>
      <a href="#member-sub-menu" data-bs-toggle="collapse" 
        aria-expanded="{{ request()->routeIs(['auth.admin.users*']) ? 'true' : 'false' }}"
        class="dropdown-toggle">
          <i class="fa-solid fa-user"></i>
          회원관리
      </a>
      <ul class="collapse list-unstyled {{ request()->routeIs(['auth.admin.users*']) ? 'show' : '' }}" id="member-sub-menu">
        <li class="{{ request()->routeIs(['auth.admin.users*']) ? 'current-page' : '' }}">
          <a href="{{ route('auth.admin.users') }}">회원</a>
        </li>
      </ul>
    </li>

    
  </ul>
</nav>
