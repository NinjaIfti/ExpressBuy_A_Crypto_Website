@extends('admin.layouts.app')
@section('page_title',__('Create Crypto'))
@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-sm mb-2 mb-sm-0">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-no-gutter">
                            <li class="breadcrumb-item"><a class="breadcrumb-link"
                                                           href="javascript:void(0);">@lang('Dashboard')</a></li>
                            <li class="breadcrumb-item"><a class="breadcrumb-link"
                                                           href="javascript:void(0);">@lang('Create Crypto')</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">@yield('page_title')</li>
                        </ol>
                    </nav>
                    <h1 class="page-header-title">@yield('page_title')</h1>
                </div>
            </div>
        </div>
        @if($activeCryptoMethod->code == 'manual')
            <div class="row mx-4">
            <span class="text-danger"> <i class="fal fa-exclamation-triangle"></i> @lang("You are currently using manual crypto method after creating your crypto currency please add the corresponding crypto address here") <a
                    target="_blank"
                    href="{{ route('admin.cryptoMethodSetAddress').'?code=manual' }}">@lang("click here")</a> @lang("to enable seamless exchangeability.")</span>
            </div>
        @endif
        <div class="content container-fluid">
            <div class="row justify-content-lg-center">
                <div class="col-lg-12">
                    <div class="d-grid gap-3 gap-lg-5">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h4 class="card-title mt-2">@lang("Create Crypto")</h4>
                            </div>
                            <div class="card-body mt-2">
                                <form action="{{ route('admin.cryptoCreate') }}" method="post"
                                      enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label"
                                                       for="Name">@lang('Name')</label>
                                                <input type="text" class="form-control" name="name"
                                                       value="{{old('name')}}"
                                                       id="Name"
                                                       placeholder="@lang('eg. Bitcoin, Ethereum')"
                                                       aria-label="@lang('name')"
                                                       autocomplete="off">
                                                @error('name')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label"
                                                       for="Code">@lang('Code')</label>
                                                <input type="text" class="form-control" name="code"
                                                       value="{{old('code')}}"
                                                       id="Code"
                                                       placeholder="@lang('eg. BTC, ETH')"
                                                       aria-label="@lang('code')"
                                                       autocomplete="off">
                                                @error('code')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label"
                                                       for="Symbol">@lang('Symbol')</label>
                                                <input type="text" class="form-control" name="symbol"
                                                       value="{{old('symbol')}}"
                                                       id="Symbol"
                                                       placeholder="@lang('eg. ₿, Ξ')"
                                                       aria-label="@lang('symbol')"
                                                       autocomplete="off">
                                                @error('symbol')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label"
                                                       for="Rate">@lang('Exchange Rate')</label>
                                                <div class="input-group">
                                                    <span class="input-group-text rateCode"
                                                          id="basic-addon2">1 BTC=</span>
                                                    <input type="number" step="0.01" class="form-control" name="rate"
                                                           value="{{old('rate')}}"
                                                           placeholder="15"
                                                           aria-label="@lang('rate')"
                                                           autocomplete="off">
                                                    <span class="input-group-text"
                                                          id="basic-addon2">{{basicControl()->base_currency}}</span>
                                                </div>
                                                @error('rate')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label"
                                                       for="ServiceFee">@lang('Service Fee')</label>
                                                <div class="input-group">
                                                     <span class="input-group-text showCodeLabel"
                                                           id="basic-addon2">BTC</span>
                                                    <input type="text" class="form-control" name="service_fee"
                                                           value="{{old('service_fee')}}"
                                                           placeholder="15"
                                                           aria-label="@lang('service_fee')"
                                                           autocomplete="off">
                                                    <select class="form-select" id="inputGroupSelect03"
                                                            name="service_fee_type"
                                                            aria-label="Example select with button addon">
                                                        <option
                                                            value="percent" {{old('service_fee_type') == 'percent' ? 'selected':''}}>@lang('Percent')</option>
                                                        <option
                                                            value="flat" {{old('service_fee_type') == 'flat' ? 'selected':''}}>@lang('Flat')</option>
                                                    </select>
                                                </div>
                                                @error('service_fee')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label"
                                                       for="NetworkFee">@lang('Network Fee')</label>
                                                <div class="input-group">
                                                     <span class="input-group-text showCodeLabel"
                                                           id="basic-addon2">BTC</span>
                                                    <input type="text" class="form-control" name="network_fee"
                                                           value="{{old('network_fee')}}"
                                                           placeholder="15"
                                                           aria-label="@lang('network_fee')"
                                                           autocomplete="off">
                                                    <select class="form-select" id="inputGroupSelect03"
                                                            name="network_fee_type"
                                                            aria-label="Example select with button addon">
                                                        <option
                                                            value="percent" {{old('network_fee_type') == 'percent' ? 'selected':''}}>@lang('Percent')</option>
                                                        <option
                                                            value="flat" {{old('network_fee_type') == 'flat' ? 'selected':''}}>@lang('Flat')</option>
                                                    </select>
                                                </div>
                                                @error('network_fee')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                                @enderror
                                            </div>

                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label"
                                                       for="MinimumSend">@lang('Minimum Send')</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control"
                                                           name="min_send"
                                                           value="{{old('min_send')}}"
                                                           placeholder="0.0005"
                                                           aria-label="@lang('min_send')"
                                                           autocomplete="off">
                                                    <span class="input-group-text showCodeLabel"
                                                          id="basic-addon2">BTC</span>
                                                </div>
                                                @error('min_send')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label"
                                                       for="MaximumSend">@lang('Maximum Send')</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="max_send"
                                                           value="{{old('max_send')}}"
                                                           placeholder="2.00"
                                                           aria-label="@lang('max_send')"
                                                           autocomplete="off">
                                                    <span class="input-group-text showCodeLabel"
                                                          id="basic-addon2">BTC</span>
                                                </div>
                                                @error('max_send')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <label class="row form-check form-switch my-4"
                                                   for="status">
                                            <span class="col-8 col-sm-9 ms-0">
                                              <span class="d-block text-dark">@lang("Status")</span>
                                              <span
                                                  class="d-block fs-5">@lang("Display the status of the currency (active or inactive) prominently on the front page.")</span>
                                            </span>
                                                <span class="col-4 col-sm-3 text-end">
                                                    <input type="hidden" value="0" name="status"/>
                                                    <input
                                                        class="form-check-input @error('status') is-invalid @enderror"
                                                        type="checkbox" name="status"
                                                        id="status" value="1"
                                                        {{old('status') == '1' ? 'checked':''}}>
                                                </span>
                                                @error('status')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                                @enderror
                                            </label>

                                            <div class="mb-3">
                                                <label class="form-label">@lang('Choose Image')</label>
                                                <div class="col-md-4 mb-3 mb-md-0">
                                                    <label class="form-check form-check-dashed" for="logoUploader">
                                                        <img id="otherImg"
                                                             class="avatar avatar-xl avatar-4x3 avatar-centered h-100 mb-2"
                                                             src="{{ getFile('local','abc', true) }}"
                                                             alt="@lang("File Storage Logo")"
                                                             data-hs-theme-appearance="default">

                                                        <img id="otherImg"
                                                             class="avatar avatar-xl avatar-4x3 avatar-centered h-100 mb-2"
                                                             src="{{ getFile('local','abc', true) }}"
                                                             alt="@lang("File Storage Logo")"
                                                             data-hs-theme-appearance="dark">
                                                        <span class="d-block">@lang("Browse your file here")</span>
                                                        <input type="file" class="js-file-attach form-check-input"
                                                               name="image" id="logoUploader"
                                                               data-hs-file-attach-options='{
                                              "textTarget": "#otherImg",
                                              "mode": "image",
                                              "targetAttr": "src",
                                              "allowTypes": [".png", ".jpeg", ".jpg"]
                                           }'>
                                                    </label>
                                                    @error("image")
                                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                                <span
                                                    class="text-danger">@lang('Note: Image size should be ') {{config('filelocation.cryptoCurrency.size')}} @lang('for better resolution')</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-start">
                                        <button type="submit" class="btn btn-primary">@lang('Create')</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('css-lib')
@endpush

@push('js-lib')
    <script src="{{ asset("assets/admin/js/hs-file-attach.min.js") }}"></script>
@endpush

@push('script')
    <script>
        'use strict';
        $(document).ready(function () {
            new HSFileAttach('.js-file-attach')
        });

        $(document).on("keyup", "#Code", function () {
            let code = $(this).val();
            $('.rateCode').text(`1 ${code}=`)
            $('.showCodeLabel').text(`${code}`)
        });
    </script>
@endpush
