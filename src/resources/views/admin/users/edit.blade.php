@section('title', '회원정보수정')
<x-pondol-auth::blank>
@include('pondol-auth::admin.users.tabs', ['id'=>$user->id])

<div class="card">
  <form method="POST" action="{{ route('auth.admin.user', [$user->id]) }}">
    @method('PUT')
    @csrf          
    <div class="card-header">
      <strong>{{$user->name}}</strong> 정보
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
            <p class="form-control-static">{{$user->id}}</p>
        </div>
      </div>


      <div class="row mt-1">
        <div class="col-2">
          <label class="form-label">이름</label>
        </div>
        <div class="col-4">
          <input type="text" name="name" value="{{$user->name}}" class="form-control">
        </div>
        <div class="col-2">
          <label class="form-label">연락처</label>
        </div>
        <div class="col-4">
          <input type="text" name="mobile" value="{{addHypenToMobile($user->mobile)}}" class="form-control" autocomplete="off">
        {{-- addHypenToMobile($user->mobile) --}}
        </div>
      </div>
      <div class="row mt-1">
        <div class="col-2">
          <label for="password" class="form-label">패스워드</label>
        </div>
        <div class="col-4">
          <input type="password" id="password" name="password" class="form-control" autocomplete="off">
        </div>
        <div class="col-2">
            <label for="password_confirmation" class="form-label">패스워드 확인</label>
        </div>
        <div class="col-4">
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
        </div>
      </div>

      <div class="row mt-1">
        <div class="col-2">
          <label class="form-label">권한</label>
        </div>
        <div class="col-4">
        @foreach($roles as $key=>$role)
          <input type="radio" id="roles-{{ $key}}" name="roles[]" value="{{ $role->id }}" @if($user->roles->find($role->id)) checked="checked" @endif > <label for="roles-{{ $key}}">{{ $role->name }}</label>
        @endforeach
        </div>
        <div class="col-2">
          <label class=" form-label"></label>
        </div>
        <div class="col-4">

        </div>
      </div>
    </div><!-- card-body -->
    <x-pondol::validation-fail.first />
    <div class="card-footer text-end">
      <button type="submit" class="btn btn-primary btn-sm">
        <i class="fa-regular fa-circle-check"></i> 변경
      </button>
      <a href="{{ URL::previous() }}" type="reset" class="btn btn-danger btn-sm">
        <i class="fa fa-ban"></i> 취소
      </a>
    </div>
  </form>
</div>
</x-pondol-auth::blank>