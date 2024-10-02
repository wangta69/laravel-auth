@extends('market.templates.'.config('market.template').'.layouts.blank')
@section('content')


<div class="container">


  <form method="POST" action="{{ route('register') }}" style="width: 100%;">
    @csrf
    <div class="input-group mt-1">
      <div class="form-floating flex-grow-1">
        <input type="text" class="form-control" name="email" value="{{ old('email') }}" id="name" placeholder="name@example.com">
        <label for='name' class='col-sm-2 control-label'>Email address</label>
        
      </div>
      <button class="btn btn-warning"><i class="fa fa-envelope"></i></button>
    </div>

    <div class="input-group mt-1">
      <div class="form-floating flex-grow-1">
        <input type="text" class="form-control" name="password" value="{{ old('email') }}" id="password" placeholder="name@example.com">
        <label for='password' class='col-sm-2 control-label'>Password</label>
        
      </div>
      <button class="btn btn-warning"><i class="fa fa-lock"></i></button>
    </div>

    <div class="btnHome">
      <button type="button" name="button" class="btn">
          확인
      </button>
    </div>

  </form>

</div>
@endsection

@section ('styles')
@parent
@endsection


@section ('scripts')
@parent
@endsection
