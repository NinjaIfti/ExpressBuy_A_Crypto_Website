<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
        <li class="nav-item">
            <a class="nav-link {{menuActive('user.dashboard')}}" href="{{route('user.dashboard')}}">
                <i class="fa-light fa-grid"></i>
                <span>@lang('Dashboard')</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{menuActive(['user.exchangeList','user.exchangeDetails'])}} sidebar-trigger" 
               href="javascript:void(0);" onclick="openSidebarAndNavigate('{{route('user.exchangeList')}}', 'exchange')">
                <i class="fa-light fal fa-exchange"></i>
                <span>@lang('Exchange')</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{menuActive(['user.buyList','user.buyDetails'])}} sidebar-trigger" 
               href="javascript:void(0);" onclick="openSidebarAndNavigate('{{route('user.buyList')}}', 'buy')">
                <i class="fa-light fal fa-wallet"></i>
                <span>@lang('Buy')</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{menuActive(['user.sellList','user.sellDetails'])}} sidebar-trigger" 
               href="javascript:void(0);" onclick="openSidebarAndNavigate('{{route('user.sellList')}}', 'sell')">
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

<!-- JavaScript to handle sidebar slider activation -->
<script>
    // Function to open sidebar and navigate to a page
    function openSidebarAndNavigate(url, tabType) {
        // Store which tab should be activated in localStorage
        localStorage.setItem('openSidebarTab', tabType);
        
        // Navigate to the URL
        window.location.href = url;
    }
    
    // Check if we should open the sidebar on page load
    document.addEventListener('DOMContentLoaded', function() {
        const tabToOpen = localStorage.getItem('openSidebarTab');
        
        if (tabToOpen) {
            // Clear the storage to prevent it from opening on subsequent page loads
            localStorage.removeItem('openSidebarTab');
            
            // Wait for everything to be loaded
            setTimeout(function() {
                // First, make sure sidebar is visible
                const sidebar = document.querySelector('.custom-sidebar');
                if (sidebar) {
                    sidebar.style.display = 'block';
                    sidebar.style.opacity = '1';
                    sidebar.style.visibility = 'visible';
                    
                    console.log('Opening sidebar with tab:', tabToOpen);
                    
                    // Click the appropriate tab
                    if (tabToOpen === 'exchange' && document.getElementById('sidebar-pills-exchange-tab')) {
                        document.getElementById('sidebar-pills-exchange-tab').click();
                    } else if (tabToOpen === 'buy' && document.getElementById('sidebar-pills-Buy-tab')) {
                        document.getElementById('sidebar-pills-Buy-tab').click();
                    } else if (tabToOpen === 'sell' && document.getElementById('sidebar-pills-Sell-tab')) {
                        document.getElementById('sidebar-pills-Sell-tab').click();
                    }
                    
                    // Try to make the wallet button visible too if it exists
                    const walletBtn = document.getElementById('showAssetsBtn');
                    if (walletBtn) {
                        walletBtn.click();
                    }
                }
            }, 500); // Give the page a bit more time to fully initialize
        }
    });
</script>
