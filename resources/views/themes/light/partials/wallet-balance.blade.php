@php
    $announces = \App\Models\CoinAnnounce::where('status',1)->get();
    $basePath = url('/');
@endphp

<!-- Load the sidebar calculator CSS -->
<link rel="stylesheet" href="/assets/themes/light/css/sidebar-calculator.css">

<!-- Custom fix for modals -->
<style>
    .sidebar-calculator-modal {
        z-index: 9999 !important;
    }
    
    .sidebar-calculator-modal .modal-header {
        align-items: center;
        padding: 1rem;
    }
    
    .sidebar-calculator-modal .modal-header .cmn-btn-close {
        position: absolute;
        top: 10px;
        right: 15px;
    }
    
    /* Fix for slider appearing above modal */
    #sidebar-exampleModal {
        z-index: 9998 !important;
    }
    
    /* Ensure modals are above all other elements */
    .modal-backdrop {
        z-index: 9990 !important;
    }
    
    /* Prevent sidebar from being hidden */
    .custom-sidebar {
        z-index: 9980 !important;
        display: block !important;
        position: fixed !important;
    }
    
    /* Ensure modal backdrops don't hide the sidebar */
    body.modal-open .custom-sidebar {
        display: block !important;
        opacity: 1 !important;
        visibility: visible !important;
    }
</style>

<div class="custom-sidebar">
    <div class="sidebar-content" style="max-height: 90vh; overflow-y: auto; padding-right: 5px;">
        <!-- <div class="wallet-box">
            <h4 class="mb-20">@lang('Assets')</h4>
            <div class="d-flex justify-content-between gap-4 mb-30">
                <div>
                    <h5 class="mb-0">@lang('USD')</h5>
                    <small>@lang('Market Value')</small>
                </div>
                <div class="text-end">
                    <h5 class="mb-0" id="totalUsdValue">$0</h5>
                    <small>@lang('Total Balance')</small>
                </div>
            </div>
            <div class="wallet-item-container" id="showAssetsBalance">

            </div>
        </div> -->

        <div class="mt-4 mb-4">
            <div class="calculator-section w-100">
                <form class="calculator" action="{{route('exchangeRequest')}}" method="POST"
                      id="sidebarFormId">
                    @csrf
                    
                    <div class="calculator-body">
                        <div class="cmn-tabs">
                            <ul class="nav nav-pills" id="sidebar-pills-tab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="sidebar-pills-exchange-tab"
                                            data-bs-toggle="pill" data-bs-target="#sidebar-pills-exchange" type="button"
                                            role="tab" aria-controls="sidebar-pills-exchange"
                                            aria-selected="true">@lang("Exchange")
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="sidebar-pills-Buy-tab" data-bs-toggle="pill"
                                            data-bs-target="#sidebar-pills-exchange" type="button" role="tab"
                                            aria-controls="sidebar-pills-exchange" aria-selected="false">@lang("Buy")
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="sidebar-pills-Sell-tab" data-bs-toggle="pill"
                                            data-bs-target="#sidebar-pills-exchange" type="button" role="tab"
                                            aria-controls="sidebar-pills-exchange" aria-selected="false">@lang("Sell")
                                    </button>
                                </li>
                            </ul>
                        </div>
                        <div>
                            <div class="tab-content" id="sidebar-pills-tabContent">
                                <div class="tab-pane fade show active" id="sidebar-pills-exchange" role="tabpanel"
                                     aria-labelledby="sidebar-pills-exchange-tab" tabindex="0">
                                    <!-- Custom Exchange UI for sidebar -->
                                    <div class="row" id="sidebar-showLoader">
                                        <div class="col-12">
                                            <div class="input-amount-box" id="sidebar-inputAmountBox">
                                                <span class="linear-gradient"></span>
                                                <div class="input-amount-wrapper">
                                                    <label class="form-label mb-2">@lang('You send')</label>
                                                    <div class="input-amount-box-inner"
                                                         id="sidebar-inputAmountBoxInner">
                                                        <a href="#" class="icon-area" data-bs-toggle="modal"
                                                           data-bs-target="#sidebar-calculator-modal">
                                                            <img class="img-flag" id="sidebar-showSendImage"
                                                                 src=""
                                                                 alt="...">
                                                        </a>
                                                        <div class="text-area w-100">
                                                            <div class="d-flex gap-3 justify-content-between">
                                                                <a href="#"
                                                                   class="d-flex align-items-center gap-1"
                                                                   data-bs-toggle="modal"
                                                                   data-bs-target="#sidebar-calculator-modal">
                                                                    <div class="title" id="sidebar-showSendCode"></div>
                                                                    <i class="fa-regular fa-angle-down"></i>
                                                                </a>
                                                                <input type="text"
                                                                       name="exchangeSendAmount" id="sidebar-send" placeholder="@lang('You send')"
                                                                       onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')" required>
                                                                <input type="hidden" name="exchangeSendCurrency" value="">
                                                            </div>
                                                            <div class="d-flex justify-content-between">
                                                                <div class="sub-title" id="sidebar-showSendName"></div>
                                                                <div class="fw-500 text-danger" id="sidebar-exchangeMessage"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="swap-area">
                                                <div class="swap-icon" id="sidebar-swapBtn">
                                                    <i class="fa-regular fa-arrow-up-arrow-down"></i>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-12">
                                            <div class="input-amount-box" id="sidebar-inputAmountBox2">
                                                <span class="linear-gradient"></span>
                                                <div class="input-amount-wrapper">
                                                    <label class="form-label mb-2">@lang("You get")</label>
                                                    <div class="input-amount-box-inner"
                                                         id="sidebar-inputAmountBoxInner2">
                                                        <a href="#" class="icon-area" data-bs-toggle="modal"
                                                           data-bs-target="#sidebar-calculator-modal2">
                                                            <img class="img-flag" id="sidebar-showGetImage"
                                                                 src=""
                                                                 alt="...">
                                                        </a>
                                                        <div class="text-area w-100">
                                                            <div class="d-flex gap-3 justify-content-between">
                                                                <a href="#"
                                                                   class="d-flex align-items-center gap-1"
                                                                   data-bs-toggle="modal"
                                                                   data-bs-target="#sidebar-calculator-modal2">
                                                                    <div class="title" id="sidebar-showGetCode"></div>
                                                                    <i class="fa-regular fa-angle-down"></i>
                                                                </a>
                                                                <input type="text"
                                                                       name="exchangeGetAmount" id="sidebar-receive" placeholder="@lang('You get')"
                                                                       onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')" required>
                                                                <input type="hidden" name="exchangeGetCurrency" value="">
                                                            </div>
                                                            <div class="d-flex justify-content-between">
                                                                <div class="sub-title" id="sidebar-showGetName"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="btn-area">
                                            <button type="submit" class="cmn-btn w-100" id="sidebar-submitBtn">@lang("Exchange now")</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if(auth()->check())
        <button type="button" class="custom-toggle-sidebar-btn" id="showAssetsBtn">
            <i class="fa-regular fa-wallet"></i>
        </button>
    @endif
</div>

<!-- Currency selection modals for sidebar -->
<!-- Coin Announce Modal Start-->
<div class="modal fade announcement-modal" id="sidebar-exampleModal" tabindex="-1" aria-labelledby="sidebar-exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 p-3 pb-0 justify-content-end">
                <button type="button" class="cmn-btn-close" data-bs-dismiss="modal" aria-label="Close"><i
                        class="fa-regular fa-xmark"></i></button>
            </div>
            <div class="modal-body text-center" id="sidebar-announceBodyShow">
            </div>
        </div>
    </div>
</div>
<!-- Coin Announce Modal End -->

<!-- Modal section start -->
<div class="modal fade sidebar-calculator-modal" id="sidebar-calculator-modal" data-bs-backdrop="static" data-bs-keyboard="false"
     tabindex="-1" aria-labelledby="sidebar-staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header flex-column">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="modal-title" id="sidebar-staticBackdropLabel">@lang('Select a currency')</h3>
                    <button type="button" class="cmn-btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa-light fa-xmark"></i>
                    </button>
                </div>
                <div class="search-box mt-10">
                    <input type="text" id="sidebar-search-input" onkeyup="sidebarWallet.filterItems('sidebar-search-input')" class="form-control"
                           placeholder="@lang('Search here')...">
                    <button type="submit" class="search-btn"><i class="far fa-search"></i></button>
                </div>
            </div>
            <div class="modal-body">
                <div id="sidebar-currency-list" class="currency-list">
                    <div id="sidebar-show-send">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade sidebar-calculator-modal" id="sidebar-calculator-modal2" data-bs-backdrop="static" data-bs-keyboard="false"
     tabindex="-1" aria-labelledby="sidebar-staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header flex-column">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="modal-title" id="sidebar-staticBackdropLabel2">Select a currency</h3>
                    <button type="button" class="cmn-btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa-light fa-xmark"></i>
                    </button>
                </div>
                <div class="search-box mt-10">
                    <input type="text" id="sidebar-search-input2" onkeyup="sidebarWallet.filterItems2('sidebar-search-input2')" class="form-control"
                           placeholder="Search here...">
                    <button type="submit" class="search-btn"><i class="far fa-search"></i></button>
                </div>
            </div>
            <div class="modal-body">
                <div id="sidebar-currency-list2" class="currency-list">
                    <div id="sidebar-show-get">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal section end -->

@push('extra_scripts')
<script>
    // Immediately executing script to check if we're on specific pages
    (function() {
        const path = window.location.pathname.toLowerCase();
        
        // Mark body as transaction page if on a transaction page
        if (path.includes('/sell-request/list') || 
            path.includes('/buy-request/list') || 
            path.includes('/exchange-request/list') ||
            path.includes('/user/buy') ||
            path.includes('/user/sell') ||
            path.includes('/user/exchange')) {
            document.body.classList.add('transaction-page');
        }
        
        // Buy pages
        if (path.includes('/buy-request/list') || path.includes('/user/buy')) {
            console.log('Immediate check: On buy page');
            // Set a flag in sessionStorage
            sessionStorage.setItem('forceOpenSidebar', 'buy');
            // Set a flag to keep sidebar open
            sessionStorage.setItem('keepSidebarOpen', 'true');
        }
        // Sell pages
        else if (path.includes('/sell-request/list') || path.includes('/user/sell')) {
            console.log('Immediate check: On sell page');
            // Set a flag in sessionStorage
            sessionStorage.setItem('forceOpenSidebar', 'sell');
            // Set a flag to keep sidebar open
            sessionStorage.setItem('keepSidebarOpen', 'true');
        }
        // Exchange pages
        else if (path.includes('/exchange-request/list') || path.includes('/user/exchange')) {
            console.log('Immediate check: On exchange page');
            // Set a flag in sessionStorage
            sessionStorage.setItem('forceOpenSidebar', 'exchange');
            // Set a flag to keep sidebar open
            sessionStorage.setItem('keepSidebarOpen', 'true');
        }
    })();

    // Create a namespace for wallet functionality to avoid conflicts
    const sidebarWallet = (function() {
        'use strict';
        
        // Private variables
        let totalUsdValue = 0;
        let sidebarActiveTab = "exchange";
        let sidebarActiveSendCurrency = "";
        let sidebarActiveGetCurrency = "";
        let keepSidebarOpen = false;
        let sidebarCloseOverridden = false;
        
        // Add direct page URL checking
        function handleDirectURLs() {
            const currentPath = window.location.pathname.toLowerCase();
            
            // Buy pages
            if (currentPath.includes('/buy-request/list') || currentPath.includes('/user/buy')) {
                console.log('Direct URL handler: buy page detected');
                keepSidebarOpen = true;
                setTimeout(function() {
                    $('#showAssetsBtn').trigger('click');
                    activateTab('buy');
                    ensureSidebarStaysOpen();
                }, 800);
                return true;
            }
            // Sell pages
            else if (currentPath.includes('/sell-request/list') || currentPath.includes('/user/sell')) {
                console.log('Direct URL handler: sell page detected');
                keepSidebarOpen = true;
                setTimeout(function() {
                    $('#showAssetsBtn').trigger('click');
                    activateTab('sell');
                    ensureSidebarStaysOpen();
                }, 800);
                return true;
            }
            // Exchange pages
            else if (currentPath.includes('/exchange-request/list') || currentPath.includes('/user/exchange')) {
                console.log('Direct URL handler: exchange page detected');
                keepSidebarOpen = true;
                setTimeout(function() {
                    $('#showAssetsBtn').trigger('click');
                    activateTab('exchange');
                    ensureSidebarStaysOpen();
                }, 800);
                return true;
            }
            
            return false;
        }
        
        // Function to ensure sidebar stays open
        function ensureSidebarStaysOpen() {
            // First check if we need to keep sidebar open
            const path = window.location.pathname.toLowerCase();
            if (path.includes('/sell-request/list') || 
                path.includes('/buy-request/list') || 
                path.includes('/exchange-request/list') ||
                path.includes('/user/buy') ||
                path.includes('/user/sell') ||
                path.includes('/user/exchange')) {
                
                keepSidebarOpen = true;
                
                // Mark the body to enable CSS rules
                document.body.classList.add('transaction-page');
                
                // Show the close button
                const closeWrapper = document.querySelector('.sidebar-close-wrapper');
                if (closeWrapper) {
                    closeWrapper.style.display = 'block';
                }
                
                // Set interval to check if sidebar is open
                const checkInterval = setInterval(function() {
                    if (!$('.sidebar-content').hasClass('active')) {
                        console.log('Sidebar closed, reopening...');
                        $('.sidebar-content').addClass('active');
                    }
                }, 500);
                
                // Save the interval ID to clear it when no longer needed
                window.sidebarCheckInterval = checkInterval;
                
                // Override the default toggle behavior to prevent closing
                if (!sidebarCloseOverridden) {
                    const originalClickHandler = $('#showAssetsBtn').prop('onclick');
                    
                    $('#showAssetsBtn').off('click').on('click', function(e) {
                        if (!keepSidebarOpen) {
                            // Normal toggle behavior
                            $('.sidebar-content').toggleClass('active');
                        } else {
                            // Only allow opening, not closing
                            $('.sidebar-content').addClass('active');
                        }
                    });
                    
                    sidebarCloseOverridden = true;
                }
                
                // Allow explicit closing with a close button if needed
                $('.sidebar-close-btn').off('click').on('click', function() {
                    $('.sidebar-content').removeClass('active');
                    if (window.sidebarCheckInterval) {
                        clearInterval(window.sidebarCheckInterval);
                    }
                    keepSidebarOpen = false;
                });
            }
        }
        
        // Base functions for assets display
        function initAssets() {
            var route = "{{route('user.cryptoDeposit')}}";

            async function fetchData() {
                try {
                    const response = await axios.get('{{route('getAssetsBalance')}}');
                    if (response.data.status === 'success') {
                        let wallets = response.data.wallets;
                        let html = "";

                        wallets.forEach(wallet => {
                            let walletRoute = route + '/' + wallet.crypto_currency.code;
                            html += `<div class="wallet-item">
                    <div class="left-side">
                        <a href="${walletRoute}" class="deposit-btn" title="@lang('Deposit')"
                        ><i class="fa-regular fa-arrow-up"></i></a>
                    </div>
                    <div class="middle-side">
                        <div class="img-box">
                            <img src="${wallet.crypto_currency.image_path}" alt="..."/>
                        </div>
                        <div>
                            <h5 class="mb-0">${wallet.crypto_currency.code}</h5>
                            <small>${wallet.crypto_currency.currency_name}</small>
                        </div>
                    </div>
                    <div class="right-side">
                        <h5 class="mb-0">${wallet.balance}</h5>
                        <small>$${dollarEquivalent(wallet.balance, wallet.crypto_currency.usd_rate)}</small>
                    </div>
                </div>`;
                        });

                        $('#showAssetsBalance').html(html);
                        totalUsdCount();
                    }
                } catch (error) {
                    console.error('Error fetching assets balance:', error);
                }
            }

            function dollarEquivalent(amount, rate) {
                if (!amount || !rate) return "0";
                let usdValue = (parseFloat(amount) * parseFloat(rate)).toFixed(2);
                totalUsdValue += parseFloat(usdValue);

                return usdValue;
            }

            function totalUsdCount() {
                $('#totalUsdValue').text(`$${(totalUsdValue).toFixed(2)}`)
            }

            fetchData();
        }
        
        // Function to fix image paths
        function fixImagePath(path) {
            if (!path) return "{{asset(config('filelocation.default'))}}";
            
            // If the path is already absolute, return it
            if (path.startsWith('http://') || path.startsWith('https://')) {
                return path;
            }
            
            // Remove any leading slashes
            const cleanPath = path.replace(/^\/+/, '');
            
            // Check if path contains "assets/upload"
            if (cleanPath.includes('assets/upload')) {
                return `{{$basePath}}/${cleanPath}`;
            } else {
                // Handle case where we just have the filename
                return `{{$basePath}}/assets/upload/${cleanPath}`;
            }
        }
        
        // Exchange functionality
        function initExchange() {
            // Only initialize if the exchange sidebar exists
            if (!document.getElementById('sidebar-showLoader')) {
                return;
            }
            
            Notiflix.Block.dots('#sidebar-showLoader', {
                backgroundColor: loaderColor,
            });
            
            sidebarGetExchangeCurrency();
            
            // Event handlers specifically for sidebar exchange
            $('.custom-sidebar').on("keyup", "#sidebar-send", function() {
                let sendAmount = $(this).val();
                sidebarGetCalculation(sendAmount);
            });
            
            $('.custom-sidebar').on("keyup", "#sidebar-receive", function() {
                let getAmount = $(this).val();
                sidebarSendCalculation(getAmount);
            });
            
            $('.custom-sidebar').on("click", "#sidebar-swapBtn", function() {
                let sendAmount = $("#sidebar-receive").val();
                $("#sidebar-send").val(sendAmount);
                sidebarGetCalculation(sendAmount);
            });
            
            // Currency selection in modals
            $(document).on("click", ".sidebar-sendModal", function(e) {
                // Only proceed if this click is from our sidebar modals
                if (!$(this).closest('.sidebar-calculator-modal').length) {
                    return;
                }
                e.stopPropagation();
                
                sidebarActiveSendCurrency = $(this).data('res');
                sidebarSetSendCurrency(sidebarActiveSendCurrency);
                let sendAmount = $("#sidebar-send").val();
                sidebarGetCalculation(sendAmount);
                
                // Update checkmarks
                $('.sidebar-sendModal .right-side').empty();
                $(this).find('.right-side').html('<i class="fa-sharp fa-solid fa-circle-check"></i>');
                
                // Close modal without affecting other parts of the page
                try {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('sidebar-calculator-modal'));
                    if (modal) {
                        modal.hide();
                    }
                } catch (error) {
                    console.error('Modal close error:', error);
                }
            });
            
            $(document).on("click", ".sidebar-getModal", function(e) {
                // Only proceed if this click is from our sidebar modals
                if (!$(this).closest('.sidebar-calculator-modal').length) {
                    return;
                }
                e.stopPropagation();
                
                sidebarActiveGetCurrency = $(this).data('res');
                sidebarSetGetCurrency(sidebarActiveGetCurrency);
                let sendAmount = $("#sidebar-send").val();
                sidebarGetCalculation(sendAmount);
                
                // Update checkmarks
                $('.sidebar-getModal .right-side').empty();
                $(this).find('.right-side').html('<i class="fa-sharp fa-solid fa-circle-check"></i>');
                
                // Close modal without affecting other parts of the page
                try {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('sidebar-calculator-modal2'));
                    if (modal) {
                        modal.hide();
                    }
                } catch (error) {
                    console.error('Modal close error:', error);
                }
            });
            
            // Tab click handlers
            $('.custom-sidebar').on("click", "#sidebar-pills-exchange-tab", function() {
                Notiflix.Block.dots('#sidebar-showLoader', {
                    backgroundColor: loaderColor,
                });
                
                let formSubmitRoute = "{{route('exchangeRequest')}}";
                $("#sidebarFormId").attr("action", formSubmitRoute);
                
                sidebarActiveTab = 'exchange';
                let route = "{{route('getExchangeCurrency')}}";
                sidebarGetExchangeCurrency(route);
                $("#sidebar-submitBtn").text("Exchange Now");
            });
            
            $('.custom-sidebar').on("click", "#sidebar-pills-Buy-tab", function() {
                Notiflix.Block.dots('#sidebar-showLoader', {
                    backgroundColor: loaderColor,
                });
                
                let formSubmitRoute = "{{route('buyRequest')}}";
                $("#sidebarFormId").attr("action", formSubmitRoute);
                
                sidebarActiveTab = 'buy';
                let route = "{{route('getBuyCurrency')}}";
                sidebarGetExchangeCurrency(route);
                $("#sidebar-submitBtn").text("Buy Now");
            });
            
            $('.custom-sidebar').on("click", "#sidebar-pills-Sell-tab", function() {
                Notiflix.Block.dots('#sidebar-showLoader', {
                    backgroundColor: loaderColor,
                });
                
                let formSubmitRoute = "{{route('sellRequest')}}";
                $("#sidebarFormId").attr("action", formSubmitRoute);
                
                sidebarActiveTab = 'sell';
                let route = "{{route('getSellCurrency')}}";
                sidebarGetExchangeCurrency(route);
                $("#sidebar-submitBtn").text("Sell Now");
            });
        }
        
        function sidebarGetExchangeCurrency(route = "{{route('getExchangeCurrency')}}") {
            axios.get(route)
                .then(function (response) {
                    Notiflix.Block.remove('#sidebar-showLoader');
                    sidebarActiveSendCurrency = response.data.selectedSendCurrency;
                    sidebarActiveGetCurrency = response.data.selectedGetCurrency;
                    sidebarSetSendCurrency(sidebarActiveSendCurrency);
                    sidebarSetGetCurrency(sidebarActiveGetCurrency);
                    sidebarShowSend(response.data.sendCurrencies);
                    sidebarShowGet(response.data.getCurrencies);
                    $("#sidebar-send").val((response.data.initialSendAmount).toFixed(2));
                    sidebarGetCalculation(response.data.initialSendAmount);
                })
                .catch(function (error) {
                    console.error('Error fetching exchange currency data:', error);
                });
        }
        
        // Show currency lists in modals
        function sidebarShowSend(currencies) {
            $('#sidebar-show-send').html(``);
            let options = "";
            for (let i = 0; i < currencies.length; i++) {
                const currencyImage = currencies[i].image_path || currencies[i].image;
                const fixedImagePath = fixImagePath(currencyImage);
                
                let isChecked = (i === 0) ? '<i class="fa-sharp fa-solid fa-circle-check"></i>' : '';
                
                // Store the fixed path in the currency object
                currencies[i].fixed_image_path = fixedImagePath;
                
                options += `<div class="item sidebar-sendModal" data-res='${JSON.stringify(currencies[i])}'>
                            <div class="left-side">
                                <div class="img-area">
                                    <img class="img-flag" src="${fixedImagePath}" alt="${currencies[i].code}">
                                </div>
                                <div class="text-area">
                                    <div class="title">${currencies[i].code}</div>
                                    <div class="sub-title">${currencies[i].name}</div>
                                </div>
                            </div>
                            <div class="right-side">${isChecked}</div>
                        </div>`;
            }
            $('#sidebar-show-send').append(options);
        }
        
        function sidebarShowGet(currencies) {
            $('#sidebar-show-get').html(``);
            let options = "";
            for (let i = 0; i < currencies.length; i++) {
                const currencyImage = currencies[i].image_path || currencies[i].image;
                const fixedImagePath = fixImagePath(currencyImage);
                
                let isChecked = (i === 0) ? '<i class="fa-sharp fa-solid fa-circle-check"></i>' : '';
                
                // Store the fixed path in the currency object
                currencies[i].fixed_image_path = fixedImagePath;
                
                options += `<div class="item sidebar-getModal" data-res='${JSON.stringify(currencies[i])}'>
                            <div class="left-side">
                                <div class="img-area">
                                    <img class="img-flag" src="${fixedImagePath}" alt="${currencies[i].code}">
                                </div>
                                <div class="text-area">
                                    <div class="title">${currencies[i].code}</div>
                                    <div class="sub-title">${currencies[i].name}</div>
                                </div>
                            </div>
                            <div class="right-side">${isChecked}</div>
                        </div>`;
            }
            $('#sidebar-show-get').append(options);
        }
        
        // Set the selected currencies
        function sidebarSetSendCurrency(currency) {
            const fixedPath = currency.fixed_image_path || fixImagePath(currency.image_path || currency.image);
            $('#sidebar-showSendImage').attr('src', fixedPath);
            $('#sidebar-showSendCode').text(currency.code);
            $('#sidebar-showSendName').text(currency.name);
            $(".custom-sidebar input[name='exchangeSendCurrency']").val(currency.id);
        }
        
        function sidebarSetGetCurrency(currency) {
            const fixedPath = currency.fixed_image_path || fixImagePath(currency.image_path || currency.image);
            $('#sidebar-showGetImage').attr('src', fixedPath);
            $('#sidebar-showGetCode').text(currency.code);
            $('#sidebar-showGetName').text(currency.name);
            $(".custom-sidebar input[name='exchangeGetCurrency']").val(currency.id);
        }
        
        // Calculation functions
        function sidebarGetCalculation(sendAmount) {
            $("#sidebar-exchangeMessage").text('');
            $("#sidebar-submitBtn").attr('disabled', false);
            
            let sendMinLimit = sidebarActiveSendCurrency.min_send;
            let sendMaxLimit = sidebarActiveSendCurrency.max_send;
            let sendCode = sidebarActiveSendCurrency.code;
            let sendUsdRate = sidebarActiveSendCurrency.usd_rate;
            let getUsdRate = sidebarActiveGetCurrency.usd_rate;
            let getAmount = sidebarGetAmountCal(sendAmount, sendUsdRate, getUsdRate);
            $("#sidebar-receive").val(getAmount);
            
            if (parseFloat(sendAmount) < parseFloat(sendMinLimit)) {
                $("#sidebar-submitBtn").attr('disabled', true);
                $("#sidebar-exchangeMessage").text(`Min is ${sendMinLimit} ${sendCode}`);
            }
            
            if (parseFloat(sendAmount) > parseFloat(sendMaxLimit)) {
                $("#sidebar-submitBtn").attr('disabled', true);
                $("#sidebar-exchangeMessage").text(`Max is ${sendMaxLimit} ${sendCode}`);
            }
        }
        
        function sidebarGetAmountCal(sendAmount, sendUsdRate, getUsdRate) {
            if (sidebarActiveTab == 'exchange') {
                return (sendAmount * sendUsdRate / getUsdRate).toFixed(8);
            } else if (sidebarActiveTab == 'buy') {
                return (sendAmount * sendUsdRate / getUsdRate).toFixed(8);
            } else if (sidebarActiveTab == 'sell') {
                return (sendAmount * sendUsdRate / getUsdRate).toFixed(2);
            }
        }
        
        function sidebarSendCalculation(getAmount) {
            $("#sidebar-exchangeMessage").text('');
            $("#sidebar-submitBtn").attr('disabled', false);
            
            let sendMinLimit = sidebarActiveSendCurrency.min_send;
            let sendMaxLimit = sidebarActiveSendCurrency.max_send;
            let sendCode = sidebarActiveSendCurrency.code;
            let sendUsdRate = sidebarActiveSendCurrency.usd_rate;
            let getUsdRate = sidebarActiveGetCurrency.usd_rate;
            let sendAmount = sidebarSendAmountCal(getAmount, sendUsdRate, getUsdRate);
            $("#sidebar-send").val(sendAmount);
            
            if (parseFloat(sendAmount) < parseFloat(sendMinLimit)) {
                $("#sidebar-submitBtn").attr('disabled', true);
                $("#sidebar-exchangeMessage").text(`Min is ${sendMinLimit} ${sendCode}`);
            }
            
            if (parseFloat(sendAmount) > parseFloat(sendMaxLimit)) {
                $("#sidebar-submitBtn").attr('disabled', true);
                $("#sidebar-exchangeMessage").text(`Max is ${sendMaxLimit} ${sendCode}`);
            }
        }
        
        function sidebarSendAmountCal(getAmount, sendUsdRate, getUsdRate) {
            if (sidebarActiveTab == 'exchange') {
                return (getAmount * getUsdRate / sendUsdRate).toFixed(8);
            } else if (sidebarActiveTab == 'buy') {
                return (getAmount * getUsdRate / sendUsdRate).toFixed(2);
            } else if (sidebarActiveTab == 'sell') {
                return (getAmount * getUsdRate / sendUsdRate).toFixed(8);
            }
        }
        
        // Modal filtering functions
        function filterItems(inputId) {
            const input = document.getElementById(inputId);
            const filter = input.value.toUpperCase();
            const div = document.getElementById('sidebar-show-send');
            const items = div.getElementsByClassName('item');
            
            for (let i = 0; i < items.length; i++) {
                const title = items[i].querySelector('.title');
                const subtitle = items[i].querySelector('.sub-title');
                const txtValue = title.textContent + subtitle.textContent;
                
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    items[i].style.display = "";
                } else {
                    items[i].style.display = "none";
                }
            }
        }
        
        function filterItems2(inputId) {
            const input = document.getElementById(inputId);
            const filter = input.value.toUpperCase();
            const div = document.getElementById('sidebar-show-get');
            const items = div.getElementsByClassName('item');
            
            for (let i = 0; i < items.length; i++) {
                const title = items[i].querySelector('.title');
                const subtitle = items[i].querySelector('.sub-title');
                const txtValue = title.textContent + subtitle.textContent;
                
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    items[i].style.display = "";
                } else {
                    items[i].style.display = "none";
                }
            }
        }
        
        // Initialize modals safely
        function initModals() {
            setTimeout(function() {
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const calculatorModal = document.getElementById('sidebar-calculator-modal');
                    const calculatorModal2 = document.getElementById('sidebar-calculator-modal2');
                    
                    if (calculatorModal) {
                        new bootstrap.Modal(calculatorModal);
                        console.log('Sidebar calculator modal 1 initialized');
                    }
                    
                    if (calculatorModal2) {
                        new bootstrap.Modal(calculatorModal2);
                        console.log('Sidebar calculator modal 2 initialized');
                    }
                }
            }, 1000);
        }
        
        // Initialize everything
        function init() {
            initAssets();
            initExchange();
            initModals();
            
            // Toggle sidebar on button click
            $(document).on('click', '#showAssetsBtn', function() {
                $('.sidebar-content').toggleClass('active');
            });
        }
        
        // Return public methods and properties
        return {
            init: init,
            filterItems: filterItems,
            filterItems2: filterItems2,
            activateTab: activateTab
        };
    })();

    // Initialize the wallet sidebar
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM fully loaded, checking URL:', window.location.pathname);
        const path = window.location.pathname.toLowerCase();
        
        // Special case for transaction pages
        if (path.includes('/sell-request/list')) {
            console.log('Sell request list page detected, forcing sidebar open');
            openTransactionSidebar('sell');
        }
        else if (path.includes('/buy-request/list')) {
            console.log('Buy request list page detected, forcing sidebar open');
            openTransactionSidebar('buy');
        }
        else if (path.includes('/exchange-request/list')) {
            console.log('Exchange request list page detected, forcing sidebar open');
            openTransactionSidebar('exchange');
        }
        
        // Helper function for opening sidebar
        function openTransactionSidebar(type) {
            setTimeout(function() {
                $('#showAssetsBtn').trigger('click');
                sidebarWallet.activateTab(type);
            }, 300);
        }
        
        // Check for stored operation immediately and open sidebar if needed
        const storedOperation = sessionStorage.getItem('sidebarOperation');
        if (storedOperation) {
            console.log('Found stored operation:', storedOperation);
            sessionStorage.removeItem('sidebarOperation');
            
            // Force open the sidebar immediately
            setTimeout(function() {
                $('#showAssetsBtn').trigger('click');
                sidebarWallet.activateTab(storedOperation);
            }, 500);
        }
        
        // Initialize the wallet functionality
        sidebarWallet.init();
        
        // Ensure we don't interfere with homepage calculator
        if (typeof HomepageExchange !== 'undefined' && typeof HomepageExchange.init === 'function') {
            console.log('HomepageExchange module detected, making sure it runs');
            setTimeout(function() {
                HomepageExchange.init();
            }, 500);
        }
        
        // Enhance sidebar links to open calculator when clicked
        enhanceSidebarLinks();
    });

    // Add window.onload handler as another fallback
    window.onload = function() {
        console.log('Window loaded, checking URL path:', window.location.pathname);
        const path = window.location.pathname.toLowerCase();
        
        // Check if on a transaction page
        const isTransactionPage = path.includes('/sell-request/list') || 
                                 path.includes('/buy-request/list') || 
                                 path.includes('/exchange-request/list') ||
                                 path.includes('/user/buy') ||
                                 path.includes('/user/sell') ||
                                 path.includes('/user/exchange');
        
        // Set a global variable to indicate we're on a transaction page
        window.isTransactionPage = isTransactionPage;
        
        // Buy pages
        if (path.includes('/buy-request/list') || path.includes('/user/buy')) {
            console.log('Window load: buy page detected');
            openTransactionSidebar('buy');
            keepSidebarOpenOnPage();
        }
        // Sell pages
        else if (path.includes('/sell-request/list') || path.includes('/user/sell')) {
            console.log('Window load: sell page detected');
            openTransactionSidebar('sell');
            keepSidebarOpenOnPage();
        }
        // Exchange pages
        else if (path.includes('/exchange-request/list') || path.includes('/user/exchange')) {
            console.log('Window load: exchange page detected');
            openTransactionSidebar('exchange');
            keepSidebarOpenOnPage();
        }
        
        // Helper function to open sidebar with correct tab
        function openTransactionSidebar(type) {
            setTimeout(function() {
                const sidebarBtn = document.getElementById('showAssetsBtn');
                if (sidebarBtn && typeof sidebarBtn.click === 'function') {
                    sidebarBtn.click();
                }
                
                if (sidebarWallet && typeof sidebarWallet.activateTab === 'function') {
                    sidebarWallet.activateTab(type);
                }
            }, 1000);
        }
        
        // Helper function to ensure sidebar stays open
        function keepSidebarOpenOnPage() {
            // Add class to body
            document.body.classList.add('transaction-page');
            
            // Show the close button
            const closeWrapper = document.querySelector('.sidebar-close-wrapper');
            if (closeWrapper) {
                closeWrapper.style.display = 'block';
            }
            
            // Add click handler for close button
            const closeBtn = document.querySelector('.sidebar-close-btn');
            if (closeBtn) {
                closeBtn.addEventListener('click', function() {
                    // Allow user to explicitly close the sidebar
                    const sidebar = document.querySelector('.sidebar-content');
                    if (sidebar) {
                        sidebar.classList.remove('active');
                    }
                    
                    // Disable the auto-open feature
                    window.isTransactionPage = false;
                    document.body.classList.remove('transaction-page');
                    closeWrapper.style.display = 'none';
                });
            }
            
            // Set an interval to check sidebar state
            const sidebarCheckInterval = setInterval(function() {
                // Only keep checking if we're still on transaction page mode
                if (!window.isTransactionPage) {
                    clearInterval(sidebarCheckInterval);
                    return;
                }
                
                const sidebar = document.querySelector('.sidebar-content');
                if (sidebar && !sidebar.classList.contains('active')) {
                    console.log('Sidebar closed, reopening...');
                    sidebar.classList.add('active');
                }
            }, 1000);
            
            // Store the interval ID to clear it if needed
            window.sidebarCheckInterval = sidebarCheckInterval;
            
            // Intercept any scripts that might close the sidebar
            const originalRemoveClass = Element.prototype.removeClass;
            if (originalRemoveClass && !window.classRemovalIntercepted) {
                Element.prototype.removeClass = function(className) {
                    if (this.classList.contains('sidebar-content') && 
                        className === 'active' && 
                        window.isTransactionPage) {
                        console.log('Intercepted attempt to close sidebar');
                        return this;
                    }
                    return originalRemoveClass.apply(this, arguments);
                };
                window.classRemovalIntercepted = true;
            }
            
            // Also override the jQuery removeClass if available
            if (window.jQuery) {
                const originalJQueryRemoveClass = jQuery.fn.removeClass;
                if (originalJQueryRemoveClass && !window.jQueryClassRemovalIntercepted) {
                    jQuery.fn.removeClass = function(className) {
                        if (this.hasClass('sidebar-content') && 
                            className === 'active' && 
                            window.isTransactionPage) {
                            console.log('Intercepted jQuery attempt to close sidebar');
                            return this;
                        }
                        return originalJQueryRemoveClass.apply(this, arguments);
                    };
                    window.jQueryClassRemovalIntercepted = true;
                }
            }
        }
    };
    
    // Function to force open the sidebar directly without relying on click events
    window.forceOpenSidebar = function(type) {
        console.log('Force opening sidebar with direct class application');
        $('.sidebar-content').addClass('active');
        
        // Make sure it's visible with important styles
        $('.sidebar-content').attr('style', 'display: block !important; opacity: 1 !important; visibility: visible !important; z-index: 9999 !important;');
        
        // Select the appropriate tab
        if (type === 'exchange') {
            $('#sidebar-pills-exchange-tab').click();
        } else if (type === 'buy') {
            $('#sidebar-pills-Buy-tab').click();
        } else if (type === 'sell') {
            $('#sidebar-pills-Sell-tab').click();
        }
    }
    
    // Function to enhance sidebar links to also open calculator
    function enhanceSidebarLinks() {
        console.log('Enhancing sidebar links to also open calculator');
        
        // Define a function to detect operation type from URL
        function getOperationFromUrl(url) {
            url = url.toLowerCase();
            if (url.includes('exchange')) return 'exchange';
            if (url.includes('buy')) return 'buy';
            if (url.includes('sell')) return 'sell';
            return null;
        }
        
        // Find all sidebar navigation links for Exchange, Buy, and Sell
        const sidebarLinks = document.querySelectorAll('.sidebar a.nav-link');
        sidebarLinks.forEach(function(link) {
            const href = link.getAttribute('href');
            const operation = getOperationFromUrl(href);
            
            // Only enhance Buy/Sell/Exchange links
            if (operation) {
                // Store the original URL
                link.setAttribute('data-original-href', href);
                
                // Add click handler
                link.addEventListener('click', function(e) {
                    // Prevent default navigation
                    e.preventDefault();
                    
                    console.log('Sidebar link clicked:', operation);
                    
                    // Store the operation and URL
                    sessionStorage.setItem('sidebarOperation', operation);
                    sessionStorage.setItem('pendingNavigation', href);
                    
                    // Open the calculator with the appropriate tab
                    setTimeout(function() {
                        window.forceOpenSidebar(operation);
                        
                        // Navigate to the original URL after opening calculator
                        setTimeout(function() {
                            window.location.href = href;
                        }, 100);
                    }, 50);
                });
                
                // Mark as enhanced
                link.classList.add('sidebar-trigger');
            }
        });
    }
    
    // Debug element existence and properties
    $(document).ready(function() {
        console.log('Debug info - Elements check:');
        console.log('showAssetsBtn exists:', $('#showAssetsBtn').length);
        console.log('showAssetsBtn is visible:', $('#showAssetsBtn').is(':visible'));
        console.log('sidebar-content exists:', $('.sidebar-content').length);
        console.log('sidebar-content has active class:', $('.sidebar-content').hasClass('active'));
        console.log('sidebar-content CSS display:', $('.sidebar-content').css('display'));
        console.log('sidebar-content CSS visibility:', $('.sidebar-content').css('visibility'));
        console.log('sidebar-content CSS opacity:', $('.sidebar-content').css('opacity'));
        console.log('sidebar-pills-exchange-tab exists:', $('#sidebar-pills-exchange-tab').length);
        console.log('sidebar-pills-Buy-tab exists:', $('#sidebar-pills-Buy-tab').length);
        console.log('sidebar-pills-Sell-tab exists:', $('#sidebar-pills-Sell-tab').length);
        
        // Try opening with longer delay
        setTimeout(function() {
            console.log('Attempting to force open sidebar after long delay (3s)');
            window.forceOpenSidebar('exchange');
        }, 3000);
    });
</script>
@endpush