@section('title', '대쉬보드')
<x-dynamic-component 
  :component="config('pondol-auth.component.admin.layout')" 
  :path="['대쉬보드']"> 
<div class="row">
  <div class="col-6">
    <div class="card">
      <div class="card-header">
        <span>최근 가입 회원</span>
      </div><!-- .card-header -->
      <div class="card-body">
        <table class="table">
          <col width="*">
          <col width="120px">
          @forelse ($users as $user)
          <tr>
            <td>{{ $user->name }} ({{ $user->email }}) <span onclick="win_user('{{ route('auth.admin.user', $user->id) }}')"><i class="fas fa-search"></i></span></td>  
            <td>{{ date('m-d H:m', strtotime($user->created_at)) }}</td>
          </tr>
          @empty
          <tr>
            <td colspan="2">최근 가입된 회원이 존재하지 않습니다.</td>
          </tr>
          @endforelse
        </table>
      </div><!-- .card-body -->
      <div class="card-footer text-end">
        <a href="{{ route('auth.admin.users') }}" class="btn btn-primary btn-sm">더 보기</a>
      </div><!-- .card-footer -->
    </div><!-- .card -->
  </div>

</div><!-- .row -->
<div class="line"></div>





@section('styles')
  @parent
@endsection

@section('scripts')
  @parent
  
@endsection
</x-dynamic-component>