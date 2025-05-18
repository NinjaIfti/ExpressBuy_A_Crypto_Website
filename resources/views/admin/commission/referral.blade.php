@extends('admin.layouts.app')
@section('page_title',__('Referral Commission'))
@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-sm mb-2 mb-sm-0">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-no-gutter">
                            <li class="breadcrumb-item"><a class="breadcrumb-link"
                                                           href="javascript:void(0);">@lang('Dashboard')</a></li>
                            <li class="breadcrumb-item active" aria-current="page">@lang('Referral Commission')</li>
                        </ol>
                    </nav>
                    <h1 class="page-header-title">@lang('Referral Commission')</h1>
                </div>
            </div>
        </div>
        <div class="alert alert-soft-dark mb-4" role="alert">
            <div class="d-flex align-items-baseline">
                <div class="flex-shrink-0">
                    <i class="bi-info-circle me-1"></i>
                </div>

                <div class="flex-grow-1 ms-2">@lang('Please note, The amount should be added to the crypto wallet based on the equivalent fiat exchange rate.')</div>
            </div>
        </div>
        <div class="row d-flex justify-content-center">
            <div class="col-md-5">
                <div class="card">
                    <div class="card-body">
                        <div class="container">
                            <form action="{{route('admin.commission.status')}}" method="post">
                                @csrf
                                <div class="list-group-item mb-4">
                                    <div class="d-flex">
                                        <div class="flex-grow-1 ms-3">
                                            <div class="row align-items-center">
                                                <div class="col-sm mb-2 mb-sm-0">
                                                    <h5 class="mb-0">@lang('Deposit Commission')</h5>
                                                    <p class="fs-5 text-body mb-0">@lang('A referrer earns a commission when their referred user deposits.')</p>
                                                </div>
                                                <div class="col-sm-auto d-flex align-items-center">
                                                    <div class="form-check form-switch form-switch-google">
                                                        <input type="hidden" name="deposit_commission" value="0">
                                                        <input class="form-check-input" name="deposit_commission"
                                                               type="checkbox" id="deposit_commission"
                                                               value="1" @checked(basicControl()->deposit_commission)>
                                                        <label class="form-check-label"
                                                               for="deposit_commission"></label>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="list-group-item mb-4">
                                    <div class="d-flex">
                                        <div class="flex-grow-1 ms-3">
                                            <div class="row align-items-center">
                                                <div class="col-sm mb-2 mb-sm-0">
                                                    <h5 class="mb-0">@lang('Exchange Commission')</h5>
                                                    <p class="fs-5 text-body mb-0">@lang('A referrer earns a commission when their referred user exchange.')</p>
                                                </div>
                                                <div class="col-sm-auto d-flex align-items-center">
                                                    <div class="form-check form-switch form-switch-google">
                                                        <input type="hidden" name="exchange_commission" value="0">
                                                        <input class="form-check-input" name="exchange_commission"
                                                               type="checkbox" id="exchange_commission"
                                                               value="1" @checked(basicControl()->exchange_commission)>
                                                        <label class="form-check-label"
                                                               for="exchange_commission"></label>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary mt-2 ">@lang('Save Changes')</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card mt-5">
                    <div class="card-header">
                        <h5 class="card-header-title">@lang('Registration Bonus') </h5>
                    </div>
                    <div class="card-body">
                        <div class="container">
                            <form action="{{route('admin.registration.bonus.update')}}" method="post">
                                @csrf

                                <div class="row mb-4">
                                    <div class="col-sm-12">
                                        <label for="registration_bonus_amount"
                                               class="form-label">@lang('Registration Bonus Amount')</label>
                                        <div class="input-group">
                                            <input type="number"
                                                   class="form-control @error('registration_bonus_amount') is-invalid @enderror"
                                                   name="registration_bonus_amount"
                                                   id="registration_bonus_amount" autocomplete="off"
                                                   placeholder="@lang("Registration Bonus Amount")"
                                                   aria-label="@lang("Currency Symbol")"
                                                   value="{{ old('registration_bonus_amount',$basicControl->registration_bonus_amount) }}"
                                                   step="0.01">
                                            <span class="input-group-text">{{$basicControl->currency_symbol}} </span>
                                        </div>
                                        @error('registration_bonus_amount')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <div class="col-sm-12">
                                        <label for="referral_user_bonus_amount"
                                               class="form-label">@lang('Referral User Bonus Amount')</label>
                                        <div class="input-group">
                                            <input type="number"
                                                   class="form-control @error('referral_user_bonus_amount') is-invalid @enderror"
                                                   name="referral_user_bonus_amount"
                                                   id="referral_user_bonus_amount" autocomplete="off"
                                                   placeholder="@lang("Referral User Bonus Amount")"
                                                   aria-label="@lang("Currency Symbol")"
                                                   value="{{ old('referral_user_bonus_amount',$basicControl->referral_user_bonus_amount) }}"
                                                   step="0.01">
                                            <span class="input-group-text">{{$basicControl->currency_symbol}} </span>
                                        </div>
                                        @error('referral_user_bonus_amount')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row  mb-4">
                                    <div class="col-12">
                                        <div class="list-group-item">
                                            <div class="d-flex">
                                                <div class="flex-grow-1 ms-3">
                                                    <div class="row align-items-center">
                                                        <div class="col-sm mb-2 mb-sm-0">
                                                            <h5 class="mb-0">@lang('Registration Bonus')</h5>
                                                            <p class="fs-5 text-body mb-0">@lang('Turn on this button to activate the registration bonus system.')</p>
                                                        </div>
                                                        <div class="col-sm-auto d-flex align-items-center">
                                                            <div class="form-check form-switch form-switch-google">
                                                                <input type="hidden" name="registration_bonus"
                                                                       value="0">
                                                                <input class="form-check-input"
                                                                       name="registration_bonus" type="checkbox"
                                                                       id="registration_bonus"
                                                                       value="1" @checked(basicControl()->registration_bonus)>
                                                                <label class="form-check-label"
                                                                       for="registration_bonus"></label>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary mt-2 ">@lang('Save Changes')</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card mt-5">
                    <div class="card-header">
                        <h4 class="card-header-title">
                            @lang('Deposit Bonus')
                        </h4>
                    </div>
                    <div class="card-body">
                        <!-- Table -->
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th scope="col" class="text-center">@lang('Level')</th>
                                <th scope="col" class="text-center">@lang('Bonus')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($referrals->where('commission_type','deposit_commission') as $item)
                                <tr>
                                    <th scope="row" class="text-center">@lang('LEVEL')# {{ $item->level }}</th>
                                    <td class="text-center">{{ $item->commission.' '.$item->amount_type}}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="100%" class="text-center">@lang('No Data Found')</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                        <!-- End Table -->
                    </div>
                </div>
                <div class="card mt-5">
                    <div class="card-header">
                        <h4 class="card-header-title">
                            @lang('Exchange Bonus')
                        </h4>
                    </div>
                    <div class="card-body">
                        <!-- Table -->
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th scope="col" class="text-center">@lang('Level')</th>
                                <th scope="col" class="text-center">@lang('Bonus')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($referrals->where('commission_type','exchange_commission') as $item)
                                <tr>
                                    <th scope="row" class="text-center">@lang('LEVEL')# {{ $item->level }}</th>
                                    <td class="text-center">{{ $item->commission.' '.$item->amount_type}}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="100%" class="text-center">@lang('No Data Found')</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                        <!-- End Table -->
                    </div>
                </div>

            </div>
            <div class="col-md-7">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('admin.referral.commission.store')}}" method="post">
                            @method('post')
                            @csrf
                            <div class="row">
                                <div class="col-md-5">
                                    <!-- Select -->
                                    <label class="mb-1">@lang('Select commission type')</label>
                                    <div class="tom-select-custom">
                                        <select class="js-select form-select" autocomplete="off" name="commission_type"
                                                id="commissionType"
                                                data-hs-tom-select-options='{
                                                          "placeholder": "Select Type",
                                                          "hideSearch": true
                                                        }'>
                                            <option value="">@lang('Select Type')</option>
                                            <option value="deposit_commission">@lang('Deposit Bonus')</option>
                                            <option value="exchange_commission">@lang('Exchange Commission')</option>
                                        </select>
                                    </div>
                                    <!-- End Select -->
                                </div>
                                <div class="col-md-5">
                                    <div class="mb-3">
                                        <label class="mb-1">@lang('Number Of Level')</label>
                                        <input type="text" id="NumberOfLevel" class="form-control"
                                               placeholder="e.g : 10">
                                    </div>
                                </div>
                                <div class="col-md-2 mt-4">
                                    <button type="button" class="btn btn-primary generateBtn">@lang('Generate')</button>
                                </div>


                                <div class="elementContainer  mt-5" id="elementContainer">

                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('css-lib')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/tom-select.bootstrap5.css') }}">
@endpush


@push('js-lib')
    <script src="{{ asset('assets/admin/js/tom-select.complete.min.js') }}"></script>
@endpush

@push('script')
    <script>
        (function () {
            HSCore.components.HSTomSelect.init('.js-select')
        })();

        $(document).on('click', '.generateBtn', function () {
            let numberOfLevel = Number($('#NumberOfLevel').val());
            let type = $('#commissionType').val();
            if (!type) {
                Notiflix.Notify.failure('Please select commission type');
                return;
            }
            if (!numberOfLevel) {
                Notiflix.Notify.failure('Please enter Number of level');
                return;
            }

            let markup = '';
            for (let i = 0; i < parseInt(numberOfLevel); i++) {
                let currencySymbol = '{{basicControl()->currency_symbol}}';
                markup += `<div class="row">
                                    <div class="col-md-5">
                                        <div class="input-group mb-3 ">
                                            <span class="input-group-text" id="basic-addon1">LEVEL</span>
                                            <input type="text" class="form-control" name="level[]" value="${i + 1}" aria-label="Username" aria-describedby="basic-addon1" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group mb-3">
                                            <input type="number" class="form-control" name="commission[]" placeholder="Level Bonus" aria-label="Recipient's username" aria-describedby="basic-addon2" step="0.01">
                                            <!-- Select -->
                                            <div class="tom-select-custom">
                                                <select class="js-select form-select" autocomplete="off" name="amount_type[]"
                                                        data-hs-tom-select-options='{

                                                          "hideSearch": true
                                                        }'>
                                                   <option value="%">%</option>
                                                    <option value="${currencySymbol}">${currencySymbol}</option>
                                                </select>
                                            </div>
                                            <!-- End Select -->
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-white deleteBtn"><i class="bi-trash"></i></button>
                                    </div>
                                </div>`;
            }

            markup += `<div class="submit-btn">
                                        <button class="btn btn-primary" type="submit">Submit</button>
                                    </div>`
            $('#elementContainer').html(markup);

        })

        $(document).on('click', '.deleteBtn', function () {
            $(this).closest('.row').remove();
        })

    </script>

    @if ($errors->any())
        @php
            $collection = collect($errors->all());
            $errors = $collection->unique();
        @endphp
        <script>
            "use strict";
            @foreach ($errors as $error)
            Notiflix.Notify.failure('{{$error}}');
            @endforeach
        </script>
    @endif
@endpush





