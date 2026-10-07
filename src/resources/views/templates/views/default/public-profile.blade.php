@section('title', $user->name . '님의 프로필')
<x-pondol-common::app-bare header="pondol-auth::partials.front-header">
    <section>
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <h2 class="title text-center mb-4">프로필</h2>

                    <div class="card shadow-sm border-0">
                        <div class="card-body text-center p-4">
                            {{-- 1. 아바타 영역 --}}
                            <div class="mb-3 d-flex justify-content-center">
                                @if (!empty($avatar))
                                    <img src="{{ $avatar }}" alt="{{ $user->name }}"
                                        class="rounded-circle img-thumbnail"
                                        style="width: 100px; height: 100px; object-fit: cover;"
                                        referrerpolicy="no-referrer">
                                @else
                                    <div class="rounded-circle bg-light text-secondary d-flex align-items-center justify-content-center border"
                                        style="width: 100px; height: 100px; font-size: 36px; font-weight: bold;">
                                        <i class="fa fa-user text-muted"></i>
                                    </div>
                                @endif
                            </div>

                            {{-- 2. 이름 및 역할(Role) 배지 --}}
                            <h4 class="fw-bold mb-2">{{ $user->name }}</h4>
                            @if (config('pondol-auth.public_profile.show_roles', true))
                                <div class="mb-3">
                                    @foreach ($roles as $role)
                                        <span class="badge bg-secondary">{{ $role }}</span>
                                    @endforeach
                                </div>
                            @endif

                            <hr class="hr my-3" />

                            {{-- 3. 공개 정보 목록 --}}
                            <div class="text-start">
                                {{-- 가입일 (설정에 따라 노출) --}}
                                @if (config('pondol-auth.public_profile.show_joined_at', true))
                                    <div class="input-group mt-2">
                                        <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        <input type="text" class="form-control bg-light"
                                            value="가입일: {{ $user->created_at ? $user->created_at->format('Y-m-d') : '-' }}"
                                            readonly>
                                    </div>
                                @endif

                                {{-- 이메일 (설정에 따라 마스킹 노출) --}}
                                @if (config('pondol-auth.public_profile.show_email', false))
                                    <div class="input-group mt-2">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        <input type="text" class="form-control bg-light"
                                            value="{{ \Illuminate\Support\Str::mask($user->email, '*', 3, -4) }}"
                                            readonly>
                                    </div>
                                @endif
                            </div>

                        </div><!-- .card-body -->

                        {{-- 4. 본인이 자기 공개 프로필을 조회한 경우 내 정보 변경 버튼 노출 --}}
                        @if (Auth::check() && Auth::id() === $user->id)
                            <div class="card-footer text-end bg-white border-top-0 pt-0">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('user.edit') }}">
                                    내 정보 수정
                                </a>
                            </div>
                        @endif

                    </div><!-- .card -->
                </div><!-- col-md-6 -->
            </div><!-- row justify-content-center -->
        </div><!-- .container -->
    </section>

    @section('styles')
        @parent
        <style>
            .input-group-text {
                width: 45px;
                justify-content: center;
            }
        </style>
    @endsection

</x-pondol-common::app-bare>
