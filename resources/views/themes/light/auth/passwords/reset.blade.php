@extends($theme.'layouts.login_register')
@section('title',__('Reset Password'))

@section('content')
    @include($theme.'auth.verifyImage')
    <section class="login-signup-page pt-0 pb-0 min-vh-100 h-100">
        <div class="container-fluid h-100">
            <div class="row min-vh-100">
                <div class="col-md-6 p-0 d-none d-md-block">
                    <div class="login-signup-thums h-100">
                        <div class="content-area">
                            <div class="logo-area mb-30">
                                <a href="{{url('/')}}">
                                    <img class="logo"
                                         src="{{getFile(basicControl()->dark_logo_driver,basicControl()->dark_logo)}}" alt="...">
                                </a>
                            </div>
                            <div class="middle-content">
                                <h3 class="section-title">@lang('Reset Password!')</h3>
                                <p>@lang('Initiate the password reset process effortlessly and securely to regain control of your account with just a few simple steps.')</p>
                            </div>

                            @include($theme.'auth.socialIcon')
                        </div>
                    </div>
                </div>
                <div class="col-md-6 p-0 d-flex justify-content-center flex-column">
                    <div class="login-signup-form">
                        <form action="{{ route('password.update') }}" method="post">
                            @csrf
                            <div class="section-header">
                                <h3>@lang('Reset Password!')</h3>
                                <div
                                    class="description">@lang('Initiate the password reset process effortlessly and securely to regain control of your account with just a few simple steps.')</div>
                            </div>
                            <div class="row g-4">
                                <input type="hidden" name="token" value="{{ $token }}">
                                <input type="hidden" name="email" value="{{ $email ?? old('email') }}">
                                <div class="col-12">
                                    <input type="password" name="password" value="{{ old('password') }}"
                                           class="form-control"
                                           id="exampleInputEmail1"
                                           placeholder="@lang('New Password')">
                                    @error('password')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <input type="password" name="password_confirmation"
                                           class="form-control"
                                           id="exampleInputEmail1"
                                           placeholder="@lang('Confirm New Password')">
                                    @error('password_confirmation')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>
                            <button type="submit" class="cmn-btn mt-30 w-100">@lang('Reset Password')</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
