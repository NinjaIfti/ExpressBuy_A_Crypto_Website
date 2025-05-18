<!-- Navbar Vertical -->
<aside
    class="js-navbar-vertical-aside navbar navbar-vertical-aside navbar-vertical navbar-vertical-fixed navbar-expand-xl navbar-vertical-aside-initialized
    {{in_array(session()->get('themeMode'), [null, 'auto'] )?  'navbar-dark bg-dark ' : 'navbar-light bg-white'}}">
    <div class="navbar-vertical-container">
        <div class="navbar-vertical-footer-offset">
            <!-- Logo -->
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}" aria-label="{{ $basicControl->site_title }}">
                <img class="navbar-brand-logo navbar-brand-logo-auto"
                     src="{{ getFile(in_array(session()->get('themeMode'),['auto',null])?$basicControl->admin_dark_mode_logo_driver : $basicControl->admin_logo_driver, in_array(session()->get('themeMode'),['auto',null])?$basicControl->admin_dark_mode_logo:$basicControl->admin_logo, true) }}"
                     alt="{{ $basicControl->site_title }} Logo"
                     data-hs-theme-appearance="default">

                <img class="navbar-brand-logo"
                     src="{{ getFile($basicControl->admin_dark_mode_logo_driver, $basicControl->admin_dark_mode_logo, true) }}"
                     alt="{{ $basicControl->site_title }} Logo"
                     data-hs-theme-appearance="dark">
                <img class="navbar-brand-logo-mini"
                     src="{{ getFile($basicControl->favicon_driver, $basicControl->favicon, true) }}"
                     alt="{{ $basicControl->site_title }} Logo"
                     data-hs-theme-appearance="default">
                <img class="navbar-brand-logo-mini"
                     src="{{ getFile($basicControl->favicon_driver, $basicControl->favicon, true) }}"
                     alt="Logo"
                     data-hs-theme-appearance="dark">
            </a>
            <!-- End Logo -->

            <!-- Navbar Vertical Toggle -->
            <button type="button" class="js-navbar-vertical-aside-toggle-invoker navbar-aside-toggler">
                <i class="bi-arrow-bar-left navbar-toggler-short-align"
                   data-bs-template='<div class="tooltip d-none d-md-block" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
                   data-bs-toggle="tooltip"
                   data-bs-placement="right"
                   title="Collapse">
                </i>
                <i
                    class="bi-arrow-bar-right navbar-toggler-full-align"
                    data-bs-template='<div class="tooltip d-none d-md-block" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
                    data-bs-toggle="tooltip"
                    data-bs-placement="right"
                    title="Expand"
                ></i>
            </button>
            <!-- End Navbar Vertical Toggle -->


            <!-- Content -->
            <div class="navbar-vertical-content">
                <div id="navbarVerticalMenu" class="nav nav-pills nav-vertical card-navbar-nav">

                    <div class="nav-item">
                        <a class="nav-link {{ menuActive(['admin.dashboard']) }}"
                           href="{{ route('admin.dashboard') }}">
                            <i class="bi-house-door nav-icon"></i>
                            <span class="nav-link-title">@lang("Dashboard")</span>
                        </a>
                    </div>


                    <span class="dropdown-header mt-3">@lang('Currencies')</span>
                    <small class="bi-three-dots nav-subtitle-replacer"></small>
                    <div class="nav-item">
                        <a class="nav-link {{ menuActive(['admin.cryptoList','admin.cryptoCreate','admin.cryptoEdit']) }}"
                           href="{{ route('admin.cryptoList') }}" data-placement="left">
                            <i class="fab fa-bitcoin nav-icon"></i>
                            <span class="nav-link-title">@lang("Crypto")</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link {{ menuActive(['admin.fiatList','admin.fiatCreate','admin.fiatEdit']) }}"
                           href="{{ route('admin.fiatList') }}" data-placement="left">
                            <i class="fa-light fad fa-badge-dollar nav-icon"></i>
                            <span class="nav-link-title">@lang("Fiat")</span>
                        </a>
                    </div>


                    <span class="dropdown-header mt-3">@lang('API Setting')</span>
                    <small class="bi-three-dots nav-subtitle-replacer"></small>
                    <div class="nav-item">
                        <a class="nav-link {{ menuActive(['admin.cryptoMethodList','admin.cryptoMethodEdit','admin.cryptoMethodSetAddress']) }}"
                           href="{{ route('admin.cryptoMethodList') }}" data-placement="left">
                            <i class="fa-light fas fa-cogs nav-icon"></i>
                            <span class="nav-link-title">@lang("Crypto Methods")</span>
                        </a>
                    </div>

                    <span class="dropdown-header mt-3">@lang('Manage Commission')</span>
                    <small class="bi-three-dots nav-subtitle-replacer"></small>
                    <div class="nav-item">
                        <a class="nav-link {{ menuActive(['admin.referral.commission']) }}"
                           href="{{ route('admin.referral.commission') }}" data-placement="left">
                            <i class="fa-duotone fa-user-gear nav-icon"></i>
                            <span class="nav-link-title">@lang("Referral")</span>
                        </a>
                    </div>


                    <span class="dropdown-header mt-3">@lang('Transaction History')</span>
                    <small class="bi-three-dots nav-subtitle-replacer"></small>

                    <div class="nav-item" {{ menuActive(['admin.exchangeList','admin.exchangeView'], 3) }}>
                        <a class="nav-link dropdown-toggle collapsed" href="#navbarExchangeRequestMenu"
                           role="button"
                           data-bs-toggle="collapse" data-bs-target="#navbarExchangeRequestMenu"
                           aria-expanded="false"
                           aria-controls="navbarExchangeRequestMenu">
                            <i class="fas fa-exchange-alt nav-icon"></i>
                            <span class="nav-link-title">@lang("Exchange")</span>
                        </a>
                        <div id="navbarExchangeRequestMenu"
                             class="nav-collapse collapse {{ menuActive(['admin.exchangeList','admin.exchangeView'], 2) }}"
                             data-bs-parent="#navbarExchangeRequestMenu">
                            <a class="nav-link {{ request()->query('type') == 'all'? 'active' : '' }}"
                               href="{{ route('admin.exchangeList').'?type=all' }}">
                                @lang('All Exchange')
                            </a>
                            <a class="nav-link {{ request()->query('type') == 'pending'? 'active' : '' }}"
                               href="{{ route('admin.exchangeList').'?type=pending' }}">
                                @lang('Pending Exchange')
                            </a>
                            <a class="nav-link {{ request()->query('type') == 'complete'? 'active' : '' }}"
                               href="{{ route('admin.exchangeList').'?type=complete' }}">
                                @lang('Complete Exchange')
                            </a>
                            <a class="nav-link {{ request()->query('type') == 'cancel'? 'active' : '' }}"
                               href="{{ route('admin.exchangeList').'?type=cancel' }}">
                                @lang('Cancel Exchange')
                            </a>
                            <a class="nav-link {{ request()->query('type') == 'refund'? 'active' : '' }}"
                               href="{{ route('admin.exchangeList').'?type=refund' }}">
                                @lang('Refund Exchange')
                            </a>
                        </div>
                    </div>
                    <div class="nav-item" {{ menuActive(['admin.buyList','admin.buyView'], 3) }}>
                        <a class="nav-link dropdown-toggle collapsed" href="#navbarBuyRequestMenu"
                           role="button"
                           data-bs-toggle="collapse" data-bs-target="#navbarBuyRequestMenu"
                           aria-expanded="false"
                           aria-controls="navbarBuyRequestMenu">
                            <i class="fas fa-wallet nav-icon"></i>
                            <span class="nav-link-title">@lang("Buy")</span>
                        </a>
                        <div id="navbarBuyRequestMenu"
                             class="nav-collapse collapse {{ menuActive(['admin.buyList','admin.buyView'], 2) }}"
                             data-bs-parent="#navbarBuyRequestMenu">
                            <a class="nav-link {{ request()->query('type') == 'all'? 'active' : '' }}"
                               href="{{ route('admin.buyList').'?type=all' }}">
                                @lang('All Buy')
                            </a>
                            <a class="nav-link {{ request()->query('type') == 'pending'? 'active' : '' }}"
                               href="{{ route('admin.buyList').'?type=pending' }}">
                                @lang('Pending Buy')
                            </a>
                            <a class="nav-link {{ request()->query('type') == 'complete'? 'active' : '' }}"
                               href="{{ route('admin.buyList').'?type=complete' }}">
                                @lang('Complete Buy')
                            </a>
                            <a class="nav-link {{ request()->query('type') == 'cancel'? 'active' : '' }}"
                               href="{{ route('admin.buyList').'?type=cancel' }}">
                                @lang('Cancel Buy')
                            </a>
                            <a class="nav-link {{ request()->query('type') == 'refund'? 'active' : '' }}"
                               href="{{ route('admin.buyList').'?type=refund' }}">
                                @lang('Refund Buy')
                            </a>
                        </div>
                    </div>

                    <div class="nav-item" {{ menuActive(['admin.sellList','admin.sellView'], 3) }}>
                        <a class="nav-link dropdown-toggle collapsed" href="#navbarSellRequestMenu"
                           role="button"
                           data-bs-toggle="collapse" data-bs-target="#navbarSellRequestMenu"
                           aria-expanded="false"
                           aria-controls="navbarSellRequestMenu">
                            <i class="fas fa-tags nav-icon"></i>
                            <span class="nav-link-title">@lang("Sell")</span>
                        </a>
                        <div id="navbarSellRequestMenu"
                             class="nav-collapse collapse {{ menuActive(['admin.sellList','admin.sellView'], 2) }}"
                             data-bs-parent="#navbarSellRequestMenu">
                            <a class="nav-link {{ request()->query('type') == 'all'? 'active' : '' }}"
                               href="{{ route('admin.sellList').'?type=all' }}">
                                @lang('All Sell')
                            </a>
                            <a class="nav-link {{ request()->query('type') == 'pending'? 'active' : '' }}"
                               href="{{ route('admin.sellList').'?type=pending' }}">
                                @lang('Pending Sell')
                            </a>
                            <a class="nav-link {{ request()->query('type') == 'complete'? 'active' : '' }}"
                               href="{{ route('admin.sellList').'?type=complete' }}">
                                @lang('Complete Sell')
                            </a>
                            <a class="nav-link {{ request()->query('type') == 'cancel'? 'active' : '' }}"
                               href="{{ route('admin.sellList').'?type=cancel' }}">
                                @lang('Cancel Sell')
                            </a>
                            <a class="nav-link {{ request()->query('type') == 'refund'? 'active' : '' }}"
                               href="{{ route('admin.sellList').'?type=refund' }}">
                                @lang('Refund Sell')
                            </a>
                        </div>
                    </div>

                    <div class="nav-item">
                        <a class="nav-link {{ menuActive(['admin.transaction']) }}"
                           href="{{ route('admin.transaction') }}" data-placement="left">
                            <i class="bi bi-send nav-icon"></i>
                            <span class="nav-link-title">@lang("Transaction")</span>
                        </a>
                    </div>

                    <div class="nav-item">
                        <a class="nav-link {{ menuActive(['admin.commissions']) }}"
                           href="{{ route('admin.commissions') }}" data-placement="left">
                            <i class="fa-solid fa-dollar-sign nav-icon"></i>
                            <span class="nav-link-title">@lang("Commission")</span>
                        </a>
                    </div>

                    <div class="nav-item">
                        <a class="nav-link {{ menuActive(['admin.deposit.log']) }}"
                           href="{{ route('admin.deposit.log') }}" data-placement="left">
                            <i class="fas fa-window-restore nav-icon"></i>
                            <span class="nav-link-title">@lang("Deposit Log")</span>
                        </a>
                    </div>

                    <div class="nav-item">
                        <a class="nav-link {{ menuActive(['admin.payment.log']) }}"
                           href="{{ route('admin.payment.log') }}" data-placement="left">
                            <i class="bi bi-credit-card-2-front nav-icon"></i>
                            <span class="nav-link-title">@lang("Payment Log")</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link {{ menuActive(['admin.payment.pending']) }}"
                           href="{{ route('admin.payment.pending') }}" data-placement="left">
                            <i class="bi bi-cash nav-icon"></i>
                            <span class="nav-link-title">@lang("Payment Request")</span>
                        </a>
                    </div>

                    <span class="dropdown-header mt-3"> @lang("Ticket Panel")</span>
                    <small class="bi-three-dots nav-subtitle-replacer"></small>
                    <div class="nav-item">
                        <a class="nav-link dropdown-toggle {{ menuActive(['admin.ticket'], 3) }}"
                           href="#navbarVerticalTicketMenu"
                           role="button"
                           data-bs-toggle="collapse"
                           data-bs-target="#navbarVerticalTicketMenu"
                           aria-expanded="false"
                           aria-controls="navbarVerticalTicketMenu">
                            <i class="fa-light fa-headset nav-icon"></i>
                            <span class="nav-link-title">@lang("Support Ticket")</span>
                        </a>
                        <div id="navbarVerticalTicketMenu"
                             class="nav-collapse collapse {{ menuActive(['admin.ticket'], 2) }}"
                             data-bs-parent="#navbarVerticalTicketMenu">
                            <a class="nav-link {{ request()->is('admin/tickets/all') ? 'active' : '' }}"
                               href="{{ route('admin.ticket', 'all') }}">@lang("All Tickets")
                            </a>
                            <a class="nav-link {{ request()->is('admin/tickets/answered') ? 'active' : '' }}"
                               href="{{ route('admin.ticket', 'answered') }}">@lang("Answered Ticket")</a>
                            <a class="nav-link {{ request()->is('admin/tickets/replied') ? 'active' : '' }}"
                               href="{{ route('admin.ticket', 'replied') }}">@lang("Replied Ticket")</a>
                            <a class="nav-link {{ request()->is('admin/tickets/closed') ? 'active' : '' }}"
                               href="{{ route('admin.ticket', 'closed') }}">@lang("Closed Ticket")</a>
                        </div>
                    </div>

                    <span class="dropdown-header mt-3">@lang('Subscribers')</span>
                    <small class="bi-three-dots nav-subtitle-replacer"></small>
                    <div class="nav-item">
                        <a class="nav-link {{ menuActive(['admin.subscriber.index','admin.subscriber.mail']) }}"
                           href="{{ route('admin.subscriber.index') }}" data-placement="left">
                            <i class="fa-light fa-mail-bulk nav-icon"></i>
                            <span class="nav-link-title">@lang("Subscriber")</span>
                        </a>
                    </div>

                    <span class="dropdown-header mt-3"> @lang('Kyc Management')</span>
                    <small class="bi-three-dots nav-subtitle-replacer"></small>
                    <div class="nav-item">
                        <a class="nav-link {{ menuActive(['admin.kyc.form.list']) }}"
                           href="{{ route('admin.kyc.form.list') }}" data-placement="left">
                            <i class="bi-stickies nav-icon"></i>
                            <span class="nav-link-title">@lang('KYC Setting')</span>
                        </a>
                    </div>

                    <div class="nav-item" {{ menuActive(['admin.kyc.list'], 3) }}>
                        <a class="nav-link dropdown-toggle collapsed" href="#navbarVerticalKycRequestMenu"
                           role="button"
                           data-bs-toggle="collapse" data-bs-target="#navbarVerticalKycRequestMenu"
                           aria-expanded="false"
                           aria-controls="navbarVerticalKycRequestMenu">
                            <i class="bi bi-person-lines-fill nav-icon"></i>
                            <span class="nav-link-title">@lang("KYC Request")</span>
                        </a>
                        <div id="navbarVerticalKycRequestMenu"
                             class="nav-collapse collapse {{ menuActive(['admin.kyc.list'], 2) }}"
                             data-bs-parent="#navbarVerticalKycRequestMenu">
                            <a class="nav-link {{ Request::is('admin/kyc/pending') ? 'active' : '' }}"
                               href="{{ route('admin.kyc.list', 'pending') }}">
                                @lang('Pending KYC')
                            </a>
                            <a class="nav-link {{ Request::is('admin/kyc/approve') ? 'active' : '' }}"
                               href="{{ route('admin.kyc.list', 'approve') }}">
                                @lang('Approved KYC')
                            </a>
                            <a class="nav-link {{ Request::is('admin/kyc/rejected') ? 'active' : '' }}"
                               href="{{ route('admin.kyc.list', 'rejected') }}">
                                @lang('Rejected KYC')
                            </a>
                        </div>
                    </div>

                    <span class="dropdown-header mt-3"> @lang("User Panel")</span>
                    <small class="bi-three-dots nav-subtitle-replacer"></small>

                    <div class="nav-item">
                        <a class="nav-link dropdown-toggle {{ menuActive(['admin.users'], 3) }}"
                           href="#navbarVerticalUserPanelMenu"
                           role="button"
                           data-bs-toggle="collapse"
                           data-bs-target="#navbarVerticalUserPanelMenu"
                           aria-expanded="false"
                           aria-controls="navbarVerticalUserPanelMenu">
                            <i class="bi-people nav-icon"></i>
                            <span class="nav-link-title">@lang('User Management')</span>
                        </a>
                        <div id="navbarVerticalUserPanelMenu"
                             class="nav-collapse collapse {{ menuActive(['admin.users'], 2) }}"
                             data-bs-parent="#navbarVerticalUserPanelMenu">
                            <a class="nav-link {{ menuActive(['admin.users']) }}" href="{{ route('admin.users') }}">
                                @lang('All User')
                            </a>
                            <a class="nav-link {{ menuActive(['admin.mail.all.user']) }}"
                               href="{{ route("admin.mail.all.user") }}">@lang('Mail To Users')</a>
                        </div>
                    </div>


                    <span class="dropdown-header mt-3"> @lang('SETTINGS PANEL')</span>
                    <small class="bi-three-dots nav-subtitle-replacer"></small>
                    <div class="nav-item">
                        <a class="nav-link {{ menuActive(['admin.settings']) }}"
                           href="{{ route('admin.settings') }}" data-placement="left">
                            <i class="bi bi-gear nav-icon"></i>
                            <span class="nav-link-title">@lang('Control Panel')</span>
                        </a>
                    </div>

                    <div class="nav-item">
                        <a class="nav-link {{ menuActive(['admin.announcement']) }}"
                           href="{{ route('admin.announcement') }}" data-placement="left">
                            <i class="fal fa-bullhorn nav-icon"></i>
                            <span class="nav-link-title">@lang('Announcement')</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a class="nav-link {{ menuActive(['admin.coinAnnounceList','admin.coinAnnounceCreate','admin.coinAnnounceEdit']) }}"
                           href="{{ route('admin.coinAnnounceList') }}" data-placement="left">
                            <i class="fal fa-scroll nav-icon"></i>
                            <span class="nav-link-title">@lang('Coin Announce')</span>
                        </a>
                    </div>

                    <div
                        class="nav-item {{ menuActive(['admin.payment.methods', 'admin.deposit.manual.index',
                         'admin.fiatSendGatewayIndex'.'admin.fiatSendGatewayCreate','admin.fiatSendGatewayEdit'], 3) }}">
                        <a class="nav-link dropdown-toggle"
                           href="#navbarVerticalGatewayMenu"
                           role="button"
                           data-bs-toggle="collapse"
                           data-bs-target="#navbarVerticalGatewayMenu"
                           aria-expanded="false"
                           aria-controls="navbarVerticalGatewayMenu">
                            <i class="bi-briefcase nav-icon"></i>
                            <span class="nav-link-title">@lang('Payment Setting')</span>
                        </a>
                        <div id="navbarVerticalGatewayMenu"
                             class="nav-collapse collapse {{ menuActive(['admin.payment.methods', 'admin.deposit.manual.index',
                             'admin.fiatSendGatewayIndex','admin.fiatSendGatewayCreate','admin.fiatSendGatewayEdit'], 2) }}"
                             data-bs-parent="#navbarVerticalGatewayMenu">
                            <a class="nav-link {{ menuActive(['admin.payment.methods']) }}"
                               href="{{ route('admin.payment.methods') }}">@lang('Payment Gateway')</a>
                            <a class="nav-link {{ menuActive(['admin.deposit.manual.index']) }}"
                               href="{{ route('admin.deposit.manual.index') }}">@lang('Manual Gateway')</a>

                            <a class="nav-link {{ menuActive(['admin.fiatSendGatewayIndex']) }}"
                               href="{{ route('admin.fiatSendGatewayIndex') }}">@lang('Fiat Send Gateway')</a>
                        </div>
                    </div>


                    <span class="dropdown-header mt-3">@lang("Themes Settings")</span>
                    <small class="bi-three-dots nav-subtitle-replacer"></small>
                    <div id="navbarVerticalThemeMenu">
                        <div class="nav-item">
                            <a class="nav-link {{ menuActive(['admin.page.index']) }}"
                               href="{{ route('admin.page.index', basicControl()->theme) }}"
                               data-placement="left">
                                <i class="fa-light fa-list nav-icon"></i>
                                <span class="nav-link-title">@lang('Pages')</span>
                            </a>
                        </div>

                        <div class="nav-item">
                            <a class="nav-link" href="{{ route('admin.manage.menu') }}" data-placement="left">
                                <i class="bi-folder2-open nav-icon"></i>
                                <span class="nav-link-title">@lang('Manage Menu')</span>
                            </a>
                        </div>
                    </div>

                    @php
                        $segments = request()->segments();
                        $last  = end($segments);
                    @endphp
                    <div class="nav-item">
                        <a class="nav-link dropdown-toggle {{ menuActive(['admin.manage.content', 'admin.manage.content.multiple', 'admin.content.item.edit*'], 3) }}"
                           href="#navbarVerticalContentsMenu"
                           role="button" data-bs-toggle="collapse"
                           data-bs-target="#navbarVerticalContentsMenu" aria-expanded="false"
                           aria-controls="navbarVerticalContentsMenu">
                            <i class="fa-light fa-section nav-icon"></i>
                            <span class="nav-link-title">@lang('Manage Content')</span>
                        </a>
                        <div id="navbarVerticalContentsMenu"
                             class="nav-collapse collapse {{ menuActive(['admin.manage.content', 'admin.manage.content.multiple', 'admin.content.item.edit*'], 2) }}"
                             data-bs-parent="#navbarVerticalContentsMenu">
                            @foreach(array_diff(array_keys(config('contents')), ['message','content_media']) as $name)
                                <div class="talk-nav-link">
                                    <a class="nav-link {{($last == $name) ? 'active' : '' }}"
                                       href="{{ route('admin.manage.content', $name) }}">@lang(snake2Title(stringToTitle($name)))</a>
                                    @php
                                        $previewImg = @asset(config('contents')[$name]['preview']);
                                    @endphp
                                    @if($previewImg)
                                        <div class="talk-nav-link-icon">
                                            <a href="{{$previewImg}}"
                                               data-fancybox="gallery"><i class="fa-light fa-eye"></i></a>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    @foreach(collect(config('generalsettings.settings')) as $key => $setting)
                        <div class="nav-item d-none">
                            <a class="nav-link  {{ isMenuActive($setting['route']) }}"
                               href="{{ getRoute($setting['route'], $setting['route_segment'] ?? null) }}">
                                <i class="{{$setting['icon']}} nav-icon"></i>
                                <span class="nav-link-title">{{ __(getTitle($key.' '.'Settings')) }}</span>
                            </a>
                        </div>
                    @endforeach

                    <span class="dropdown-header mt-4"> @lang('Application Panel')</span>
                    <small class="bi-three-dots nav-subtitle-replacer"></small>
                    <div class="nav-item">
                        <a class="nav-link"
                           href="{{ route('clear') }}" data-placement="left">
                            <i class="fas fa-sync nav-icon"></i>
                            <span class="nav-link-title">@lang('Cache Clear')</span>
                        </a>
                    </div>

                </div>

                <div class="navbar-vertical-footer">
                    <ul class="navbar-vertical-footer-list">
                        <li class="navbar-vertical-footer-list-item">
                            <span class="dropdown-header">@lang('Version 2.0')</span>
                        </li>
                        <li class="navbar-vertical-footer-list-item">
                            <div class="dropdown dropup">
                                <button type="button" class="btn btn-ghost-secondary btn-icon rounded-circle"
                                        id="selectThemeDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                                        data-bs-dropdown-animation></button>
                                <div class="dropdown-menu navbar-dropdown-menu navbar-dropdown-menu-borderless"
                                     aria-labelledby="selectThemeDropdown">
                                    <a class="dropdown-item" href="javascript:void(0)" data-icon="bi-moon-stars"
                                       data-value="auto">
                                        <i class="bi-moon-stars me-2"></i>
                                        <span class="text-truncate"
                                              title="Auto (system default)">@lang("Default")</span>
                                    </a>
                                    <a class="dropdown-item" href="javascript:void(0)" data-icon="bi-brightness-high"
                                       data-value="default">
                                        <i class="bi-brightness-high me-2"></i>
                                        <span class="text-truncate"
                                              title="Default (light mode)">@lang("Light Mode")</span>
                                    </a>
                                    <a class="dropdown-item active" href="javascript:void(0)" data-icon="bi-moon"
                                       data-value="dark">
                                        <i class="bi-moon me-2"></i>
                                        <span class="text-truncate" title="Dark">@lang("Dark Mode")</span>
                                    </a>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</aside>




