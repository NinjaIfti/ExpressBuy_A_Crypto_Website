<script>
    // Create a namespace for homepage calculator to avoid global conflicts
    const HomepageExchange = (function() {
        'use strict';
        // Set the base URL for image paths
        const baseUrl = "{{url('/')}}";
        let activeTab = "exchange";
        let activeSendCurrency = "";
        let activeGetCurrency = "";
        
        // Function to ensure image paths are absolute and correct
        function fixImagePath(path) {
            if (!path) return "{{asset(config('filelocation.default'))}}";
            
            // If the path is already absolute, return it
            if (path.startsWith('http://') || path.startsWith('https://')) {
                return path;
            }
            
            // Get the base URL from the data attribute
            const baseUrl = document.head.getAttribute('data-base_url') || "{{url('/')}}";
            
            // Remove any leading slashes
            const cleanPath = path.replace(/^\/+/, '');
            
            // Check if path contains "assets/upload"
            if (cleanPath.includes('assets/upload')) {
                return `${baseUrl}/${cleanPath}`;
            } else {
                // Handle case where we just have the filename
                return `${baseUrl}/assets/upload/${cleanPath}`;
            }
        }
        
        // Initialize the calculator
        function init() {
            console.log('Homepage Exchange module initializing');
            
            // Check if the homepage exchange calculator exists
            if (!document.getElementById('showLoader')) {
                console.log('No homepage exchange calculator found');
                return;
            }
            
            Notiflix.Block.dots('#showLoader', {
                backgroundColor: loaderColor,
            });
            getExchangeCurrency();
            
            // Add filter functions to window for modal search
            window.filterItems = function(inputId) {
                const input = document.getElementById(inputId);
                const filter = input.value.toUpperCase();
                const div = document.getElementById('show-send');
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
            };
            
            window.filterItems2 = function(inputId) {
                const input = document.getElementById(inputId);
                const filter = input.value.toUpperCase();
                const div = document.getElementById('show-get');
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
            };
            
            // Event bindings - ensure we target only homepage elements
            $(document).on("keyup", "#send", function () {
                let sendAmount = $(this).val();
                getCalculation(sendAmount);
            });

            $(document).on("keyup", "#receive", function () {
                let getAmount = $(this).val();
                sendCalculation(getAmount);
            });

            $(document).on("click", "#swapBtn", function () {
                // Only target the homepage swap button
                if ($(this).closest('#showLoader').length > 0) {
                    let sendAmount = $("#receive").val();
                    $("#send").val(sendAmount);
                    getCalculation(sendAmount);
                }
            });

            $(document).on("click", ".sendModal", function () {
                // Only target homepage modal elements
                if ($(this).closest('.home-calculator-modal').length > 0) {
                    activeSendCurrency = $(this).data('res');
                    setSendCurrency(activeSendCurrency);
                    let sendAmount = $("#send").val();
                    getCalculation(sendAmount);
                    $('#calculator-modal').modal('hide');

                    $('.sendModal').find('.right-side').empty();
                    $(this).find('.right-side').html('<i class="fa-sharp fa-solid fa-circle-check"></i>');
                }
            });

            $(document).on("click", ".getModal", function () {
                // Only target homepage modal elements
                if ($(this).closest('.home-calculator-modal').length > 0) {
                    activeGetCurrency = $(this).data('res');
                    setGetCurrency(activeGetCurrency);
                    let sendAmount = $("#send").val();
                    getCalculation(sendAmount);
                    $('#calculator-modal2').modal('hide');

                    $('.getModal').find('.right-side').empty();
                    $(this).find('.right-side').html('<i class="fa-sharp fa-solid fa-circle-check"></i>');
                }
            });
            
            $(document).on("click", "#pills-exchange-tab", function () {
                // Only target homepage tabs
                if ($(this).closest('#pills-tab').length > 0) {
                    Notiflix.Block.dots('#showLoader', {
                        backgroundColor: loaderColor,
                    });

                    let formSubmitRoute = "{{route('exchangeRequest')}}";
                    $("#submitFormId").attr("action", formSubmitRoute);

                    activeTab = 'exchange';
                    let route = "{{route("getExchangeCurrency")}}";
                    getExchangeCurrency(route);
                    $("#submitBtn").text("Exchange Now");
                }
            });

            $(document).on("click", "#pills-Buy-tab", function () {
                // Only target homepage tabs
                if ($(this).closest('#pills-tab').length > 0) {
                    Notiflix.Block.dots('#showLoader', {
                        backgroundColor: loaderColor,
                    });

                    let formSubmitRoute = "{{route('buyRequest')}}";
                    $("#submitFormId").attr("action", formSubmitRoute);

                    activeTab = 'buy';
                    let route = "{{route("getBuyCurrency")}}";
                    getExchangeCurrency(route);
                    $("#submitBtn").text("Buy Now");
                }
            });

            $(document).on("click", "#pills-Sell-tab", function () {
                // Only target homepage tabs
                if ($(this).closest('#pills-tab').length > 0) {
                    Notiflix.Block.dots('#showLoader', {
                        backgroundColor: loaderColor,
                    });

                    let formSubmitRoute = "{{route('sellRequest')}}";
                    $("#submitFormId").attr("action", formSubmitRoute);

                    activeTab = 'sell';
                    let route = "{{route("getSellCurrency")}}";
                    getExchangeCurrency(route);
                    $("#submitBtn").text("Sell Now");
                }
            });
            
            // Slick slider initialization if needed
            $(document).ready(function () {
                if ($('.autoplay').length > 0) {
                    $('.autoplay').slick({
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        fade: true,
                        autoplay: true,
                        arrows: false,
                        autoplaySpeed: 2000,
                    });
                }
            });

            // Announcement click handlers
            $(document).on("click", ".announceClass", function () {
                let announceBodyShow = $('#announceBodyShow');
                announceBodyShow.html('');
                let heading = $(this).data('heading');
                let des = $(this).data('des');
                announceBodyShow.html(`<h4>${heading}</h4> ${des}`)
            });
        }

        function getCalculation(sendAmount) {
            $("#exchangeMessage").text('');
            $("#submitBtn").attr('disabled', false);

            let sendMinLimit = activeSendCurrency.min_send;
            let sendMaxLimit = activeSendCurrency.max_send;
            let sendCode = activeSendCurrency.code;
            let sendUsdRate = activeSendCurrency.usd_rate;
            let getUsdRate = activeGetCurrency.usd_rate;
            let getAmount = getAmountCal(sendAmount, sendUsdRate, getUsdRate);
            $("#receive").val(getAmount);

            if (parseFloat(sendAmount) < parseFloat(sendMinLimit)) {
                $("#submitBtn").attr('disabled', true);
                $("#exchangeMessage").text(`Min is ${sendMinLimit} ${sendCode}`);
            }

            if (parseFloat(sendAmount) > parseFloat(sendMaxLimit)) {
                $("#submitBtn").attr('disabled', true);
                $("#exchangeMessage").text(`Max is ${sendMaxLimit} ${sendCode}`);
            }
        }

        function getAmountCal(sendAmount, sendUsdRate, getUsdRate) {
            if (activeTab == 'exchange') {
                return (sendAmount * sendUsdRate / getUsdRate).toFixed(8);
            } else if (activeTab == 'buy') {
                return (sendAmount * sendUsdRate / getUsdRate).toFixed(8);
            } else if (activeTab == 'sell') {
                return (sendAmount * sendUsdRate / getUsdRate).toFixed(2);
            }
        }

        function sendCalculation(getAmount) {
            $("#exchangeMessage").text('');
            $("#submitBtn").attr('disabled', false);

            let sendMinLimit = activeSendCurrency.min_send;
            let sendMaxLimit = activeSendCurrency.max_send;
            let sendCode = activeSendCurrency.code;
            let sendUsdRate = activeSendCurrency.usd_rate;
            let getUsdRate = activeGetCurrency.usd_rate;
            let sendAmount = sendAmountCal(getAmount, sendUsdRate, getUsdRate);
            $("#send").val(sendAmount);

            if (parseFloat(sendAmount) < parseFloat(sendMinLimit)) {
                $("#submitBtn").attr('disabled', true);
                $("#exchangeMessage").text(`Min is ${sendMinLimit} ${sendCode}`);
            }

            if (parseFloat(sendAmount) > parseFloat(sendMaxLimit)) {
                $("#submitBtn").attr('disabled', true);
                $("#exchangeMessage").text(`Max is ${sendMaxLimit} ${sendCode}`);
            }
        }

        function sendAmountCal(getAmount, sendUsdRate, getUsdRate) {
            if (activeTab == 'exchange') {
                return (getAmount * getUsdRate / sendUsdRate).toFixed(8);
            } else if (activeTab == 'buy') {
                return (getAmount * getUsdRate / sendUsdRate).toFixed(2);
            } else if (activeTab == 'sell') {
                return (getAmount * getUsdRate / sendUsdRate).toFixed(8);
            }
        }

        function getExchangeCurrency(route = "{{route("getExchangeCurrency")}}") {
            axios.get(route)
                .then(function (response) {
                    Notiflix.Block.remove('#showLoader');
                    activeSendCurrency = response.data.selectedSendCurrency;
                    activeGetCurrency = response.data.selectedGetCurrency;
                    setSendCurrency(activeSendCurrency);
                    setGetCurrency(activeGetCurrency);
                    showSend(response.data.sendCurrencies);
                    showGet(response.data.getCurrencies);
                    $("#send").val((response.data.initialSendAmount).toFixed(2));
                    getCalculation(response.data.initialSendAmount);
                })
                .catch(function (error) {
                    console.error('Error fetching exchange currency data:', error);
                });
        }

        function showSend(currencies) {
            $('#show-send').html(``);
            let options = "";
            for (let i = 0; i < currencies.length; i++) {
                let isChecked = (i === 0) ? '<i class="fa-sharp fa-solid fa-circle-check"></i>' : '';
                const currencyImage = currencies[i].image_path || currencies[i].image;
                const fixedImagePath = fixImagePath(currencyImage);
                
                // Store the fixed path in the currency object
                currencies[i].fixed_image_path = fixedImagePath;
                
                options += `<div class="item sendModal" data-res='${JSON.stringify(currencies[i])}'>
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
            $('#show-send').append(options);
        }

        function setSendCurrency(currency) {
            const fixedPath = currency.fixed_image_path || fixImagePath(currency.image_path || currency.image);
            $('#showSendImage').attr('src', fixedPath);
            $('#showSendCode').text(currency.code);
            $('#showSendName').text(currency.name);
            $("input[name='exchangeSendCurrency']").val(currency.id);
        }

        function setGetCurrency(currency) {
            const fixedPath = currency.fixed_image_path || fixImagePath(currency.image_path || currency.image);
            $('#showGetImage').attr('src', fixedPath);
            $('#showGetCode').text(currency.code);
            $('#showGetName').text(currency.name);
            $("input[name='exchangeGetCurrency']").val(currency.id);
        }

        function showGet(currencies) {
            $('#show-get').html(``);
            let options = "";
            for (let i = 0; i < currencies.length; i++) {
                let isChecked = (i === 0) ? '<i class="fa-sharp fa-solid fa-circle-check"></i>' : '';
                const currencyImage = currencies[i].image_path || currencies[i].image;
                const fixedImagePath = fixImagePath(currencyImage);
                
                // Store the fixed path in the currency object
                currencies[i].fixed_image_path = fixedImagePath;
                
                options += `<div class="item getModal" data-res='${JSON.stringify(currencies[i])}'>
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
            $('#show-get').append(options);
        }
        
        // Return public methods
        return {
            init: init
        };
    })();

    // Initialize the homepage exchange module
    document.addEventListener('DOMContentLoaded', function() {
        HomepageExchange.init();
    });
</script>
