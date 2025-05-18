@extends($theme . 'layouts.calculation')
@section('title',trans('Initiate Payment'))
@section('content')
    <section class="calculator-details-section">
        <div class="container">
            <div class="row g-xl-5 g-4">
                @include($theme.'partials.exchange-module.exchange-leftbar',['progress' => '50','check' => 3])
                <div class="col-lg-6 order-1 order-lg-2">
                    <div class="transaction-card">
                        <h3 class="title">@lang("Send funds to the address below")</h3>
                        <div class="transaction-table">
                            <div class="table-row">
                                <div class="item"><span>@lang("Amount")</span></div>
                                <div class="item">
                                    <h6> {{rtrim(rtrim($exchangeRequest->send_amount, 0), '.')}} {{optional($exchangeRequest->sendCurrency)->code}}</h6>
                                </div>
                            </div>
                            <div class="table-row">
                                <div class="item"><span>{{basicControl()->site_title}} @lang("address") ({{optional($exchangeRequest->sendCurrency)->code}})</span>
                                </div>
                                <div class="item">
                                    <h6 id="senderAddress">{{$exchangeRequest->admin_wallet}} </h6>
                                    <small
                                        class="highlight">{{optional($exchangeRequest->sendCurrency)->currency_name}}</small>
                                    <div class="btn-area">
                                        <button type="button" onclick="copyCryptoAddress()" class="cmn-btn"><i
                                                class="fa-light fa-copy"></i><span
                                                class="d-none d-sm-block">Copy address</span></button>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="alert-message mt-20">
                            <i class="fa-solid fa-circle-info fa-rotate-180"></i>
                            <span>@lang("Please note that you can send funds to the address above only once.")</span>
                        </div>
                        @if($isButtonShow)
                            <form class="request-found-form" method="POST"
                                  action="{{route('exchangeInitPayment',$exchangeRequest->utr)}}">
                                @csrf
                                <button type="submit" class="cmn-btn w-100">@lang('Payment Done')</button>
                            </form>
                        @endif
                    </div>
                </div>
                <div class="col-lg-3 order-3 order-lg-3">
                    <div class="deadline-timer-section">
                        <div class="countdown-area">
                            <h6><i class="fa-light fa-clock"></i></h6>
                            <h6 id="countdown1"></h6>
                        </div>
                        <span>@lang("Time left to send") {{rtrim(rtrim($exchangeRequest->send_amount, 0), '.')}} {{optional($exchangeRequest->sendCurrency)->code}}</span>
                        <hr>
                        <h6 id="tranId">{{$exchangeRequest->utr}} <i class="fa-regular fa-copy"
                                                                     onclick="copyTranId()"></i></h6>
                        <span>@lang("Transaction ID")</span>
                    </div>
                    <div class="transaction-summery">
                        <h4 class="title">@lang("Trade details")</h4>
                        <div class="transaction-item-container">
                            <div class="item">
                                <span>@lang("You send")</span>
                                <h6>{{rtrim(rtrim($exchangeRequest->send_amount, 0), '.')}} {{optional($exchangeRequest->sendCurrency)->code}}</h6>
                            </div>
                            <div class="item">
                                <span>@lang("Exchange rate")</span>
                                <h6>
                                    1 {{optional($exchangeRequest->sendCurrency)->code}} {{$exchangeRequest->rate_type == 'floating' ? '~':'='}} {{$exchangeRequest->exchange_rate}} {{optional($exchangeRequest->getCurrency)->code}}</h6>
                            </div>
                            <div class="item">
                                <span>@lang("Service fee") {{rtrim(rtrim(optional($exchangeRequest->getCurrency)->service_fee, 0), '.')}}{{optional($exchangeRequest->getCurrency)->service_fee_type == 'percent' ?'%':''}}</span>
                                <h6>{{rtrim(rtrim($exchangeRequest->service_fee, 0), '.')}} {{optional($exchangeRequest->getCurrency)->code}}</h6>
                            </div>
                            <div class="item">
                                <span>@lang("Network fee") {{rtrim(rtrim(optional($exchangeRequest->getCurrency)->network_fee, 0), '.')}}{{optional($exchangeRequest->getCurrency)->network_fee_type == 'percent' ?'%':''}}</span>
                                <h6>{{rtrim(rtrim($exchangeRequest->network_fee, 0), '.')}} {{optional($exchangeRequest->getCurrency)->code}}</h6>
                            </div>

                            <div class="item">
                                <span>@lang("You get")</span>
                                <h6>{{$exchangeRequest->rate_type == 'floating' ? '~':''}} {{rtrim(rtrim($exchangeRequest->final_amount, 0), '.')}} {{optional($exchangeRequest->getCurrency)->code}}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('js-lib')
    <script src="{{asset($themeTrue.'js/jquery.countdown.min.js')}}"></script>
    <script src="{{asset($themeTrue.'js/moment.min.js')}}"></script>
    <script src="{{asset($themeTrue.'js/moment-timezone-with-data.min.js')}}"></script>
@endpush

@push('extra_scripts')
    <script>
        'use strict';
        var expireTime = moment.tz("{{ $exchangeRequest->expire_time }}", "{{ config('app.timezone') }}");
        $('#countdown1').countdown(expireTime.toDate(), function (event) { // Year/month/date
            $(this).html(event.strftime('<div class="single-coundown"><h5>%H :</h5></div><div class="single-coundown"><h5>%M :</h5></div><div class="single-coundown"><h5>%S</h5></div>'));
        });

        function getStatus() {
            axios.get("{{route('exchangeGetStatus',$exchangeRequest->utr)}}")
                .then(function (response) {
                    if (parseInt(response.data.exchangeRequest.status) === 2) {
                        window.location.href = response.data.route;
                    }
                    if (parseInt(response.data.exchangeRequest.status) === 4) {
                        window.location.href = response.data.route;
                    }
                })
                .catch(function (error) {

                });
        }

        setInterval(getStatus, 60000);

        function copyCryptoAddress() {
            var textToCopy = document.getElementById('senderAddress').innerText;
            var tempTextArea = document.createElement('textarea');
            tempTextArea.value = textToCopy;
            document.body.appendChild(tempTextArea);
            tempTextArea.select();
            document.execCommand('copy');
            document.body.removeChild(tempTextArea);
            Notiflix.Notify.success('Text copied to clipboard: ' + textToCopy);
        }

        function copyTranId() {
            var textToCopy = document.getElementById('tranId').innerText;
            var tempTextArea = document.createElement('textarea');
            tempTextArea.value = textToCopy;
            document.body.appendChild(tempTextArea);
            tempTextArea.select();
            document.execCommand('copy');
            document.body.removeChild(tempTextArea);
            Notiflix.Notify.success('Text copied to clipboard: ' + textToCopy);
        }
    </script>

@endpush
