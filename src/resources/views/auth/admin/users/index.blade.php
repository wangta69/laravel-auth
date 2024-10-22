@extends('auth.admin.layouts.main')
@section('title', '회원정보')
@section('content')
@include('auth.admin.layouts.main-top', ['path'=>['회원', '회원정보']])
<div class="p-3 mb-4 bg-light rounded-3">
  <h2 class="fw-bold">회원정보</h2>

  <div class="card">
    <div class="card-body">
      <div>회원정보 확인 및 수정이 가능합니다.</div>
    </div><!-- .card-body -->
  </div><!-- .card -->
</div>


<form method="get" action="{{ route('auth.admin.users') }}" id="search-form" >
  <div class="card mt-1 p-2">
    <div class="card-body">
      <div class="row">

        <div class="col-6">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="active[]" value="9" @if (is_array(request()->active) && in_array("9", request()->active)) checked @endif>
              <label class="form-check-label">탈퇴</label>
            </div>
   
            <div class="input-group">
              <select class="form-select" name="sk">
                <option value="users.email" @if( request()->get('sk') == 'users.email') selected="selected" @endif >이메일</option>
                <option value="users.name" @if( request()->get('sk') == 'users.name') selected="selected" @endif >이름</option>
                <option value="users.id" @if( request()->get('sk') == 'users.id') selected="selected" @endif >아이디</option>
                <option value="users.mobile" @if( request()->get('sk') == 'users.mobile') selected="selected" @endif >핸드폰</option>
              </select>
              <input type="text" name="sv" value="{{ request()->sv}}" placeholder="검색어를 입력해주세요." class="form-control">
              <button class="btn btn-success btn-serch-keyword">검색</button>
            </div>
        </div>

        <div class="ps-5 col-6">
          <div class="input-group mb-1">
            <button type="button" class="btn btn-light act-set-date" user-attr-term="0">오늘</button>
            <button type="button" class="btn btn-light act-set-date" user-attr-term="6">7일</button>
            <button type="button" class="btn btn-light act-set-date" user-attr-term="14">15일</button>
            <button type="button" class="btn btn-light act-set-date" user-attr-term="29">1개월</button>
            <button type="button" class="btn btn-light act-set-date" user-attr-term="179">6개월</button>
          </div>

          <div class="input-group">
            <input type="text" name="from_date" class="form-control" id="from-date" value="{{ request()->from_date}}" readonly>
            <i class="fa fa-calendar from-calendar input-group-text"></i>
            <span class="col-1 text-center">∼</span>
            <input type="text" name="to_date" class="form-control" id="to-date" value="{{ request()->to_date}}" readonly>
            <i class="fa fa-calendar to-calendar input-group-text"></i>
            <button class="btn btn-success btn-serch-date">조회</button>
          </div>
        </div>

      </div> <!-- .row  -->
    </div><!-- .card-body -->
  </div><!-- .card -->
</form>


<div class="text-end mt-2">
    검색 기간내:  회원 수 {{ number_format($users->total())}}명
</div>
<div class="card mt-1">
  <div class="card-body">
    <table class="table table-borderless table-striped listTable">
      <col width="*">
      <col width="*">
      <col width="*">
      <col width="*">
      <col width="*">
      <col width="*">
      <thead>
        <tr>
          <th class="text-center">
            @sortablelink('id',  'Id',['page' => $users->currentPage()])
          </th>
          <th class="text-center">
            @sortablelink('email',  '이메일', ['page' => $users->currentPage()])
          </th>
          <th class="text-center">
            @sortablelink('name',  '회원명',['page' => $users->currentPage()])
          </th>
          <th class="text-center">
            @sortablelink('point',  '포인트', ['page' => $users->currentPage()])
          </th>
          <th class="text-center">
            @sortablelink('logined_at',  '최근 방문일',['page' => $users->currentPage()])
          </th>
          <th class="text-center">
            가입일
          </th>
          <th class="text-center">
            @sortablelink('active',  '상태', ['page' => $users->currentPage()])
          </th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $user)
        <tr user-attr-id="{{ $user->id }}">
          <td class="text-center">{{ $user->id }}</td>
          <td class="text-center">{{ $user->email }}</a></td>
          <td class="text-center">{{ $user->name }} <a onclick="win_user('{{ route('auth.admin.user', $user->id) }}')"><i class="fas fa-search"></i></a></td>


          <td class="text-right">{{ number_format($user->point) }}</td>
          <td class="text-center">@if ($user->logined_at) {{ date("Y-m-d H:i", strtotime($user->logined_at)) }} @endif</td>
          <td class="text-center">{{ date("Y-m-d H:i", strtotime($user->created_at)) }}</td>

          <td class="text-center" style="cursor:pointer;" >
            <select class="form-select act-change-active" style="width: 100px;">
              <option value="0" @if ($user->active == '0') selected @endif>대기중</option>
              <option value="1" @if ($user->active == '1') selected @endif>인가됨</option>
              <option value="2" @if ($user->active == '2') selected @endif>차단됨</option>
              <option value="8" @if ($user->active == '8') selected @endif>탈퇴 신청</option>
              <option value="9" @if ($user->active == '9') selected @endif>탈퇴</option>
            </select>
            @if($user->deleted_at)
                {{ $user->deleted_at }}
            @endif
          </td>

          <!-- <td class="text-center">
              <a href="/admin/user/login/{{ $user->id }}"><i class="fas fa-sign-in-alt" style="cursor: pointer;" alt="sign in" title="sign in"></i></a>
          </td> -->

        </tr>
        @empty
        <tr>
          <td colspan="7" class="text-center">
            디스플레이할 데이타가 없습니다.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div><!-- .card-body -->
  <div  class="card-footer">
    {{ $users->links("pagination::bootstrap-4") }}
  </div><!-- .card-footer -->
</div><!-- .card -->
@endsection

@section('styles')
@parent
@endsection
@section('scripts')
@parent
<script>
$(function(){
  $('.act-change-active').on('change', function(){
    var user_id = $(this).parents('tr').attr('user-attr-id');
    var active = $(this).val();

    ROUTE.ajaxroute('put', 
    {route: 'auth.admin.active.update', segments: [user_id, active]}, 
    function(resp) {
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
