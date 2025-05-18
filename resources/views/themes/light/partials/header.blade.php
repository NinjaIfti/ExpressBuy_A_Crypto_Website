<!-- Header top section start -->

<style>
    .offer-banner-seciton .offer-overlay {
        background: linear-gradient({{hex2rgba(basicControl()->primary_color, 0.8)}}, {{hex2rgba(basicControl()->primary_color, 0.8)}});
    }
</style>

@if(announcement()->status && session()->get('isCLoseAnnouncement') == null)

    <div class="offer-banner-seciton d-none d-lg-block">
        <button type="button" onclick="closeAnnouncement()" class="offer-close-btn">
            <i class="fa-regular fa-xmark"></i>
        </button>
        <div class="container h-100">
            <div class="row h-100">
                <div class="col-12 gap-3 justify-content-center align-items-center d-flex">
                    {!! announcement()->announcement_text !!}
                    @if(announcement()->btn_display)
                        <a href="{{announcement()->btn_link}}" class="offer-btn">{{announcement()->btn_name}}</a>
                    @endif
                </div>
            </div>
        </div>
        <div class="offer-overlay"></div>
    </div>
@endif

{{--Mobile Version--}}
@if(announcement()->status && session()->get('isCLoseAnnouncement') == null)
    <div class="mobile-offer-banner d-lg-none">
        <button type="button" onclick="closeAnnouncement()" class="offer-close-btn">
            <i class="fa-regular fa-xmark"></i>
        </button>
        <div class="gap-3 justify-content-center align-items-center d-flex flex-column">
            {!! announcement()->announcement_text !!}
            @if(announcement()->btn_display)
                <a href="{{announcement()->btn_link}}" class="offer-btn">{{announcement()->btn_name}}</a>
            @endif
        </div>
        <div class="mobile-offer-banner-inner">
        </div>
    </div>
@endif
<!-- Header top section end -->

<!-- Nav section start -->
<nav class="navbar sticky-top navbar-expand-lg transparent">
    <div class="container">
        <a class="navbar-brand logo" href="{{url('/')}}"><img
                src="{{getFile(basicControl()->logo_driver,basicControl()->logo)}}" alt="..." id="logoSet"></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar"
                aria-controls="offcanvasNavbar">
            <i class="fa-light fa-list"></i>
        </button>
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbar">
            <div class="offcanvas-header">
                <a class="navbar-brand" href="{{url('/')}}"><img class="logo"
                                                                 src="{{getFile(basicControl()->logo_driver,basicControl()->logo)}}"
                                                                 alt="..." id="logoSetMobile"></a>
                <button type="button" class="cmn-btn-close btn-close" data-bs-dismiss="offcanvas"
                        aria-label="Close"><i class="fa-light fa-arrow-right"></i></button>
            </div>
            <div class="offcanvas-body align-items-center justify-content-between">
                <ul class="navbar-nav ms-auto">
                    {!! renderHeaderMenu(getHeaderMenuData()) !!}
                </ul>
            </div>
        </div>
        <div class="nav-right">
            <ul class="custom-nav">
                @guest
                    <li class="nav-item">
                        <a class="nav-link login-btn" href="{{ route('login') }}"><i
                                class="login-icon fa-light fa-right-to-bracket"></i><span
                                class="d-none d-md-block">@lang('Login')</span></a>
                    </li>
                @endguest
                @auth
                    <li class="nav-item">
                        <div class="profile-box">
                            <div class="profile">
                                <img src="{{getFile(auth()->user()->image_driver, auth()->user()->image)}}"
                                     class="img-fluid"
                                     alt="...">
                            </div>
                            <ul class="user-dropdown">
                                <li>
                                    <a href="{{route('user.dashboard')}}"> <i
                                            class="fal fa-university"></i> @lang('Dashboard') </a>
                                </li>
                                <li>
                                    <a href="{{route('user.ticket.list')}}"> <i
                                            class="fal fa-user-headset"></i> @lang('Support')
                                    </a>
                                </li>
                                <li>
                                    <a href="{{route('user.profile')}}"> <i
                                            class="fal fa-user-cog"></i> @lang('Account Settings')
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fal fa-sign-out-alt"></i>
                                        <span>@lang('Sign Out')</span>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                              class="d-none">
                                            @csrf
                                        </form>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endauth

                @if(basicControl()->changeable_mode == 1 )
                    <li>
                        <a id="toggle-btn" class="nav-link d-flex toggle-btn">
                            <i class="fa-regular fa-moon" id="moon"></i>
                            <i class="fa-regular fa-sun-bright" id="sun"></i>
                        </a>
                    </li>
                @endif

            </ul>
        </div>
    </div>
</nav>
<script>
    'use strict'

    function closeAnnouncement() {
        $.ajax({
            type: 'GET',
            url: "{{route('closeAnnouncement')}}",
            success: function (data) {
                window.location.href = data.url;
            },
            error: function () {
            }
        });
    }
</script>
<!-- Nav section end -->
