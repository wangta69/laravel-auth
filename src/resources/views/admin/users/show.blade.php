@section('title', '회원정보')
<x-pondol-auth::blank>
@include('pondol-auth::admin.users.tabs', ['id'=>$user->id])

<div class="card">
  <div class="card-header">
    <strong>{{ $user->name }}</strong> 회원정보
    {{--
    <img src="/assets/admin/images/note.png" width="15px" onclick="window_open('{{ route('admin.message.create', ['email'=>$user->email]) }}', 'message-write');">
    --}}
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-2">
        <label class="form-label">이메일</label>
      </div>
      <div class="col-4">
        <span>{{$user->email}}</span>
      </div>
      <div class="col-2">
        <label class="form-label">ID</label>
      </div>
      <div class="col-4">
        <span>{{$user->id}}</span>
      </div>
    </div>

    <div class="row mt-1">
      <div class="col-2">
        <label class="form-label">이름</label>
      </div>
      <div class="col-4">
        <span>{{$user->name}}</span>
      </div>
      <div class="col-2">
        <label class=" form-label">연락처</label>
      </div>
      <div class="col-4">
      {{addHypenToMobile($user->mobile)}}
      </div>
    </div>
    <div class="row mt-1">
      <div class="col-2">
        <label class="form-label">권한</label>
      </div>
      <div class="col-4">
        <span>{{ $user->roles->pluck('name')->implode(',') }}</span>
      </div>
      <div class="col-2">
        <label class=" form-label"></label>
      </div>
      <div class="col-4">

      </div>
    </div>

    <div class="row mt-1">
      <div class="col-2">
        <label class=" form-label">가입일</label>
      </div>
      <div class="col-4">
        <span>{{$user->created_at}}</span>
      </div>
      <div class="col-2">
        <label class=" form-label">변경일</label>
      </div>
      <div class="col-4">
        <span>{{$user->updated_at}}</span>
      </div>
    </div>
  </div><!-- card-body -->

  <div class="card-footer text-end">
    <!--
    <a href="{{ URL::previous() }}" class="btn btn-primary btn-sm">
        <i class="fa fa-dot-circle-o"></i> 리스트
    </a>
-->
    <a href="{{ route('auth.admin.user.edit', [$user->id]) }}" class="btn btn-danger btn-sm">
        <i class="fa fa-pencil-alt"></i> 수정
    </a>
  </div>

</div>

    </x-pondol-auth::blank>