@extends($theme . 'layouts.user')
@section('page_title',trans('Deposit Crypto'))
@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="tracking-card">
                <div class="icon-area mx-auto text-center">
                    <i class="fab fa-codepen"></i>
                </div>
                <h6 class="mb-10">@lang("Please click the Generate button to create a deposit address and send crypto to it")</h6>
                <form class="search-box2">
                    <input type="text" value="" class="form-control addressShowInput"
                           id="search-box2" readonly
                           placeholder="@lang("{$crypto->code} Address will display here")">
                    <button type="button" class="search-btn2" id="generateBtn">@lang("Generate")</button>
                    <button type="button" onclick="copyAddress(0)" class="search-btn2 d-none"
                            id="copyBtn">@lang("Copy")</button>
                </form>
            </div>
        </div>

        <div class="col-md-6">
            <div class="tracking-card">
                <h6 class="mb-10">@lang("Recent 10 Generated List - {$crypto->name} ($crypto->code)")</h6>
                @if(count($addresses) > 0)
                    @foreach($addresses as $address)
                        <div class="d-flex justify-content-between">
                            <span type="text" id="search-box2" class="form-control mb-2">
                                {{$address->wallet_address}} -
                                @if($address->status == 1)
                                    @lang('Complete')
                                @elseif($address->status == 2)
                                    @lang('Pending')
                                @elseif($address->status == 3)
                                    @lang('Cancel')
                                @endif
                            </span>
                        </div>
                    @endforeach
                @else
                    <div class="text-center">
                        @include('empty')
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if($cryptoMethod->code == 'manual')
        <div class="row d-none" id="manualCompleteSec">
            <div class="col-md-6">
                <div class="tracking-card">
                    <h6 class="mb-10">{{$cryptoMethod->field_name}}</h6>
                    <form class="search-box2" action="{{route('user.depositConfirm')}}" method="POST">
                        @csrf
                        <input type="text" value="" name="proof" class="form-control"
                               id="search-box2" required>
                        <input type="hidden" name="crypto_wallet_id" value="">
                        <button type="submit" class="search-btn2">@lang("Complete")</button>
                    </form>
                </div>
            </div>
        </div>
    @endif

@endsection
@push('extra_scripts')
    <script>
        'use strict';
        var code = "{{$crypto->code}}";
        $(document).on("click", "#generateBtn", function () {
            Notiflix.Loading.standard('Generated');
            axios.post("{{route('user.addressGenerate')}}", {
                code: code,
            })
                .then(response => {
                    Notiflix.Loading.remove();
                    let res = response.data;
                    if (res.status === 'success') {
                        $('.addressShowInput').val(res.address);
                        $('[name="crypto_wallet_id"]').val(res.crypto_wallet_id);
                        btnChange();
                    }
                })
                .catch(error => {
                    Notiflix.Loading.remove();
                    console.error('Error:', error);
                });
        });

        function btnChange() {
            $('#generateBtn').addClass('d-none');
            $('#copyBtn').removeClass('d-none');
            $('#manualCompleteSec').removeClass('d-none');
        }

        function copyAddress(index) {
            var inputElements = document.querySelectorAll('.addressShowInput');
            if (inputElements.length > index) {
                var inputElement = inputElements[index];

                if (inputElement.value) {
                    inputElement.select();
                    inputElement.setSelectionRange(0, 99999);
                    document.execCommand('copy');

                    Notiflix.Notify.success('Text copied to clipboard: ' + inputElement.value);
                } else {
                    Notiflix.Notify.failure('No address to copy!');
                }
            } else {
                Notiflix.Notify.failure('Invalid element index!');
            }
        }
    </script>
@endpush
