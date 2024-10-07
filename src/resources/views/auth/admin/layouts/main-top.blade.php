<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">

    <button type="button" id="sidebarCollapse" class="btn btn-info">
      <i class="fas fa-align-left"></i>
      <span></span>
    </button>
    <!-- <button class="btn btn-dark d-inline-block d-lg-none ml-auto" type="button" data-bs-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <i class="fas fa-align-justify"></i>
    </button> -->
     <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="nav navbar-nav ml-auto">
        @foreach($path as $k => $v)
        <li class="nav-item">
          <a class="nav-link"> @if($k != 0 ) > @endif {{$v}}</a>
        </li>
        @endforeach
        <!-- <li class="nav-item">
          <a class="nav-link" href="#">Page</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Page</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Page</a>
        </li> -->
      </ul>
    </div>
  </div>
</nav>