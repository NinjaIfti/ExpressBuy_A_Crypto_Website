@extends($theme.'layouts.user')
@section('page_title',__('Profile'))
@section('content')
    <div class="section dashboard">
        <div class="row">
            @include($theme.'user.profile.profileNav')
            <form method="post" action="{{ route('user.profile') }}" enctype="multipart/form-data">
                @csrf
                <div class="account-settings-profile-section">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">@lang('Profile Details')
                            </h5>
                            <div class="profile-details-section">
                                <div class="d-flex gap-3 align-items-center">
                                    <div class="image-area">
                                        <img src="{{getFile($userProfile->image_driver,$userProfile->image)}}"
                                             alt="{{auth()->user()->name}}"
                                             class="img-profile-view">
                                    </div>
                                    <div class="btn-area">
                                        <div class="btn-area-inner d-flex">
                                            <div class="cmn-file-input">
                                                <label for="formFile" class="form-label cmn-btn">@lang('Upload New
													Photo')</label>
                                                <input class="form-control file-upload-input" name="profile_picture"
                                                       type="file" id="formFile">
                                            </div>
                                            <button type="button" class="cmn-btn3 reset">@lang('reset')</button>
                                        </div>
                                        <small>@lang('Allowed JPG or PNG. Max size of 3 MB')</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0">

                            <div class="profile-form-section">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="firstname" class="form-label">@lang('Firstname')</label>
                                        <input type="text" name="firstname"
                                               placeholder="@lang('Your Firstname')"
                                               value="{{ old('firstname', $userProfile->firstname) }}"
                                               class="form-control"
                                               id="firstname" required>
                                        <div class="text-danger">@error('firstname') @lang($message) @enderror</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="lastname" class="form-label">@lang('Lastname')</label>
                                        <input type="text" name="lastname"
                                               placeholder="@lang('Your Lastname')"
                                               value="{{ old('lastname', $userProfile->lastname ) }}"
                                               class="form-control"
                                               id="lastname" required>
                                        <div
                                            class="text-danger">@error('lastname') @lang($message) @enderror</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="e-mail" class="form-label">@lang('Username')</label>
                                        <input type="text" name="username"
                                               placeholder="@lang('Username')"
                                               value="{{ old('username', $userProfile->username ) }}"
                                               class="form-control"
                                               id="e-mail" required>
                                        <div
                                            class="text-danger">@error('username') @lang($message) @enderror</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="organization" class="form-label">@lang('Email Address')</label>
                                        <input type="text" value="{{$userProfile->email}}"
                                               name=email class="form-control"
                                               id="organization" required>
                                        <div
                                            class="text-danger">@error('email') @lang($message) @enderror</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">@lang('Phone')</label>
                                        <input type="hidden" id="country" name="phone_code"
                                               value="{{$userProfile->phone_code}}">
                                        <input id="telephone" class="form-control"
                                               data-code="{{$userProfile->getPlainPhoneCode()}}" name="phone"
                                               value="{{$userProfile->phone}}" type="tel">
                                        <div class="text-danger">@error('phone') @lang($message) @enderror</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">@lang('Language')</label>
                                        <select class="cmn-select2" name="language">
                                            @foreach($languages as $language)
                                                <option
                                                    value="{{ $language->id }}" {{ old('language', $userProfile->language_id) == $language->id ? 'selected' : '' }}>
                                                    {{ __($language->name) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div
                                            class="text-danger">@error('language') @lang($message) @enderror</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">@lang('Time Zone')</label>
                                        <select class="cmn-select2" name="timezone">
                                            @foreach(timezone_identifiers_list() as $key => $value)
                                                <option
                                                    value="{{$value}}" {{  (old('timezone',$userProfile->timezone) == $value ? ' selected' : '') }}>{{ __($value) }}</option>
                                            @endforeach
                                        </select>
                                        <div
                                            class="text-danger">@error('timezone') @lang($message) @enderror</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="address" class="form-label">@lang('Address')</label>
                                        <textarea name="address" class="form-control"
                                                  id="address">{{ old('address', $userProfile->address) }}</textarea>
                                        <div
                                            class="text-danger">@error('address') @lang($message) @enderror</div>
                                    </div>
                                </div>
                                <div class="btn-area d-flex g-3">
                                    <button type="submit" class="cmn-btn">@lang('save changes')</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>

@endsection
@push('extra_scripts')
    <script>
        'use strict'
        $(document).on('change', '.file-upload-input', function () {
            let _this = $(this);
            let reader = new FileReader();
            reader.readAsDataURL(this.files[0]);
            reader.onload = function (e) {
                $('.img-profile-view').attr('src', e.target.result);
            }
        });

        $(document).on('click', '.reset', function () {
            let img = "{{asset(config('filelocation.default'))}}"
            $('.img-profile-view').attr('src', img);
        });

        $('.iti__country-list li').click(function () {
            $("#country").val($(this).data('dial-code'));
        })
    </script>
@endpush
