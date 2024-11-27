@section('title', '회원등록')
<x-dynamic-component 
  :component="config('pondol-auth.component.admin.layout')" 
  :path="['회원관리', '회원등록']"> 
<div class="p-3 mb-4 bg-light rounded-3">
  <h2 class="fw-bold">회원등록</h2>

  <div class="card">
    <div class="card-body">
      <div>회원을 추가하실 수 있습니다..</div>
    </div><!-- .card-body -->
  </div><!-- .card -->
</div>

<div id="content">

  <div class="card m-t-15">
    <div class="card-header">
      회원 등록
    </div>
    {{ Form::open(['route'=>['auth.admin.user.create'],'method' => 'post','class'=>'form-horizontal form-label-left']) }}
    <div class="card-body card-block">
      <div class="row form-group">
        <div class="col col-md-2 formInnerBox">
          <label class="form-control-label">
            이메일
            <span class="required">*</span>
          </label>
        </div>
        <div class="col-md-4 formInnerBox">
          <input id="email" type="text" class="form-control @if($errors->has('email')) parsley-error @endif"
              name="email" value="{{ old('email') }}" required>
          @if($errors->has('email'))
            <ul class="parsley-errors-list filled">
              @foreach($errors->get('email') as $error)
                <li class="parsley-required">{{ $error }}</li>
              @endforeach
            </ul>
          @endif
        </div>
        <div class="col col-md-2 formInnerBox">
          <label class="form-control-label">
            회원명
            <span class="required">*</span>
          </label>
        </div>
        <div class="col-md-4 formInnerBox">
          <input id="name" type="text" class="form-control  @if($errors->has('name')) parsley-error @endif"
              name="name" value="{{ old('name') }}" required>
          @if($errors->has('name'))
            <ul class="parsley-errors-list filled">
              @foreach($errors->get('name') as $error)
                <li class="parsley-required">{{ $error }}</li>
              @endforeach
            </ul>
          @endif
        </div>
    </div>

    <div class="row form-group">
      <div class="col col-md-2 formInnerBox">
        <label class="form-control-label">
          패스워드
        </label>
      </div>
      <div class="col-md-4 formInnerBox">
          <input id="password" type="password" class="form-control @if($errors->has('password')) parsley-error @endif"
              name="password">
          @if($errors->has('password'))
          <ul class="parsley-errors-list filled">
            @foreach($errors->get('password') as $error)
              <li class="parsley-required">{{ $error }}</li>
            @endforeach
          </ul>
          @endif
        </div>
        <div class="col col-md-2 formInnerBox">
          <label class="form-control-label">
            패스워드 확인
          </label>
        </div>
        <div class="col-md-4 formInnerBox">
          <input id="password-confirmation" type="password" class="form-control" name="password_confirmation">
        </div>
      </div>

      <div class="row form-group">
        <div class="col col-md-2 formInnerBox m-0">
          <label class="form-control-label">
            등급
          </label>
        </div>
        <div class="col-md-4">
          @foreach($roles as $key=>$role)
            <input type="radio" id="roles-{{ $key}}" name="roles[]" value="{{ $role->id }}"> <label for="roles-{{ $key}}">{{ $role->display }}</label>
          @endforeach
        </div>
      </div>
    </div>
    <div class="card-footer">
      <a href="{{ URL::previous() }}" type="reset" class="btn btn-danger btn-sm">
        <i class="fa fa-ban"></i> 취소
      </a>
      <button type="submit" class="btn btn-primary btn-sm">
        <i class="fa fa-check-circle"></i> 저장
      </button>
    </div>
    {{ Form::close() }}
  </div>
</div>
</x-dynamic-component>
