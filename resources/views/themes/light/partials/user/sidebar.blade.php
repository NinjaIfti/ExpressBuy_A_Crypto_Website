<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
        <li class="nav-item">
            <a class="nav-link {{menuActive('user.dashboard')}}" href="{{route('user.dashboard')}}">
                <i class="fa-light fa-grid"></i>
                <span>@lang('Dashboard')</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{menuActive(['user.exchangeList','user.exchangeDetails'])}}"
               href="{{route('user.exchangeList')}}" 
               onclick="openCalcAndNavigate(event, '{{route('user.exchangeList')}}', 'exchange')">
                <i class="fa-light fal fa-exchange"></i>
                <span>@lang('Exchange')</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{menuActive(['user.buyList','user.buyDetails'])}}" 
               href="{{route('user.buyList')}}"
               onclick="openCalcAndNavigate(event, '{{route('user.buyList')}}', 'buy')">
                <i class="fa-light fal fa-wallet"></i>
                <span>@lang('Buy')</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{menuActive(['user.sellList','user.sellDetails'])}}" 
               href="{{route('user.sellList')}}"
               onclick="openCalcAndNavigate(event, '{{route('user.sellList')}}', 'sell')">
                <i class="fa-light fal fa-tags"></i>
                <span>@lang('Sell')</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{menuActive('tracking')}}" href="{{route('tracking')}}">
                <i class="fa-light fal fa-location-check"></i>
                <span>@lang('Tracking')</span>
            </a>
        </li>


        <li class="nav-item">
            <a class="nav-link {{menuActive(['user.fund.index'])}}"
               href="{{route('user.fund.index')}}">
                <i class="fa-light fal fa-spinner"></i>
                <span>@lang('Payment Request')</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{menuActive(['user.transaction.index'])}}" href="{{route('user.transaction.index')}}">
                <i class="fa-light fal fa-stream"></i>
                <span>@lang('Transaction')</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{menuActive(['user.referral'])}}" href="{{route('user.referral')}}">
                <i class="fa-light fal fa-line-chart"></i>
                <span>@lang('Referral')</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{menuActive(['user.referral.bonus'])}}" href="{{route('user.referral.bonus')}}">
                <i class="fa-light fal fa-gift"></i>
                <span>@lang('Referral Bonus')</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{menuActive(['user.ticket.list','user.ticket.create','user.ticket.store','user.ticket.view'])}}"
               href="{{route('user.ticket.list')}}">
                <i class="fa-light fal fa-user-headset"></i>
                <span>@lang('Support Ticket')</span>
            </a>
        </li>
    </ul>

</aside>

<script>
    function openCalcAndNavigate(event, url, operation) {
        // Prevent default link behavior
        event.preventDefault();
        
        console.log('DIRECT CLICK: Opening calculator for ' + operation);
        
        // Open calculator
        const sidebarContent = document.querySelector('.sidebar-content');
        if (sidebarContent) {
            sidebarContent.classList.add('active');
            sidebarContent.style.display = 'block';
            sidebarContent.style.opacity = '1';
            sidebarContent.style.visibility = 'visible';
        }
        
        // Select the right tab with delay
        setTimeout(function() {
            if (operation === 'exchange') {
                const tab = document.getElementById('sidebar-pills-exchange-tab');
                if (tab) tab.click();
            } else if (operation === 'buy') {
                const tab = document.getElementById('sidebar-pills-Buy-tab');
                if (tab) tab.click();
            } else if (operation === 'sell') {
                const tab = document.getElementById('sidebar-pills-Sell-tab');
                if (tab) tab.click();
            }
            
            // Navigate to the intended page
            setTimeout(function() {
                window.location.href = url;
            }, 100);
        }, 300);
    }
</script>
