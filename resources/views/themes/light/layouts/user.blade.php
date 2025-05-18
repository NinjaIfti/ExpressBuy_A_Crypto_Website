<!DOCTYPE html>
<html lang="en" @if(session()->get('rtl') == 1) dir="rtl" @endif />
<head data-base_url="{{url('/')}}" data-theme="{{basicControl()->default_mode??'dark'}}"
      data-changeable_mode="{{basicControl()->changeable_mode??0}}"
      data-light_logo="{{ getFile(basicControl()->logo_driver,basicControl()->logo) }}"
      data-dark_logo="{{ getFile(basicControl()->dark_logo_driver,basicControl()->dark_logo) }}">
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="{{ getFile($basicControl->favicon_driver, $basicControl->favicon) }}" rel="icon">
    <title>@yield('page_title') | {{basicControl()->site_title}}</title>
    @include($theme.'partials.user.styles')

    @include($theme.'partials.loader-js')
</head>
<body class="">
@include($theme.'partials.loader')
@include($theme.'partials.user.topbar')

@include($theme.'partials.wallet-balance')

@include($theme.'partials.user.mobileNav')
@include($theme.'partials.user.sidebar')

<main id="main" class="main">
    <div class="pagetitle">
        <h3>@yield('page_title')</h3>
    </div>
    @section('content')
    @show
</main>
@include($theme.'partials.user.footer')


@include($theme.'partials.user.scripts')

<script>
    function currencyPosition(amount) {
        var currencyPosition = @json(basicControl()->is_currency_position);
        var has_space_between_currency_and_amount = @json(basicControl()->has_space_between_currency_and_amount);
        var currency_symbol = @json(basicControl()->currency_symbol);
        var base_currency = @json(basicControl()->base_currency);
        amount = parseFloat(amount).toFixed(2);
        if (currencyPosition === 'left' && has_space_between_currency_and_amount) {
            return currency_symbol + '  ' + amount;
        } else if (currencyPosition === 'left' && !has_space_between_currency_and_amount) {
            return currency_symbol + ' ' + amount;
        } else if (currencyPosition === 'right' && has_space_between_currency_and_amount) {
            return amount + '  ' + base_currency;
        } else {
            return amount + '  ' + base_currency;
        }
    }
</script>

@include($theme.'partials.user.flash-message')

@include('plugins')
</body>
</html>

