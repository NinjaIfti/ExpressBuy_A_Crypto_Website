@extends('admin.layouts.app')
@section('page_title', __('PWA'))
@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-sm mb-2 mb-sm-0">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-no-gutter">
                            <li class="breadcrumb-item">
                                <a class="breadcrumb-link" href="javascript:void(0)">@lang('Dashboard')
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">@lang('Settings')</li>
                            <li class="breadcrumb-item active" aria-current="page">@lang('PWA')</li>
                        </ol>
                    </nav>
                    <h1 class="page-header-title">@lang('PWA')</h1>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">

                <div class="  alert alert-soft-dark " role="alert">
                    <div class="alert-box d-flex flex-wrap align-items-center">
                        <div class="flex-shrink-0">
                            <img class="avatar avatar-xl"
                                 src="{{ asset('assets/admin/img/oc-megaphone.svg') }}"
                                 alt="Image Description" data-hs-theme-appearance="default">
                            <img class="avatar avatar-xl"
                                 src="{{ asset('assets/admin/img/oc-megaphone-light.svg') }}"
                                 alt="Image Description" data-hs-theme-appearance="dark">
                        </div>

                    </div>
                </div>

            </div>

            <div class="col-lg-3">
                @include('admin.control_panel.components.sidebar', ['settings' => config('generalsettings.settings'), 'suffix' => 'Settings'])
            </div>
            <div class="col-lg-9" id="basic_control">
                <div class="d-grid gap-3 gap-lg-5">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title h4">@lang('PWA Config')</h2>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.pwa.create') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="row mb-3">
                                    <div class="col-sm-6">
                                        <label for="siteTitleLabel" class="form-label">@lang('App Name')</label>
                                        <input type="text"
                                               class="form-control  @error('name') is-invalid @enderror"
                                               name="name" id="name"
                                               placeholder="@lang("name")" aria-label="@lang("App Name")"
                                               autocomplete="off"
                                               value="{{ old('name', config('laravelpwa.manifest.name')) }}">
                                        @error('name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-sm-6">
                                        <label for="baseCurrencyLabel" class="form-label">@lang('Short Name')</label>
                                        <input type="text"
                                               class="form-control  @error('short_name') is-invalid @enderror"
                                               name="short_name"
                                               id="baseCurrencyLabel" autocomplete="off"
                                               placeholder="@lang("Short Name")" aria-label="@lang("Short Name")"
                                               value="{{ old('short_name',config('laravelpwa.manifest.short_name')) }}">
                                        @error('short_name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-6">
                                        <label for="background_color"
                                               class="form-label">@lang('Background Color')</label>
                                        <input type="color"
                                               class="form-control color-form-input @error('background_color') is-invalid @enderror"
                                               name="background_color"
                                               id="background_color"
                                               placeholder="Primary Color" aria-label="Background Color"
                                               value="{{ old('background_color',config('laravelpwa.manifest.background_color')) }}">
                                        @error('background_color')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-sm-6">
                                        <label for="theme_color"
                                               class="form-label">@lang('Theme Color')</label>
                                        <input type="color"
                                               class="form-control color-form-input @error('theme_color') is-invalid @enderror"
                                               name="theme_color"
                                               id="theme_color"
                                               placeholder="Theme Color"
                                               aria-label="Theme Color"
                                               value="{{ old('theme_color',config('laravelpwa.manifest.theme_color')) }}">
                                        @error('theme_color')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-6">
                                        <label for="display" class="form-label">@lang('Display')</label>
                                        <div class="tom-select-custom">
                                            <select
                                                class="js-select form-select @error('display') is-invalid @enderror"
                                                id="display" name="display">
                                                <option
                                                    value="fullscreen" @selected(old('display',config('laravelpwa.manifest.display')) == 'fullscreen' )>@lang('Fullscreen')</option>
                                                <option
                                                    value="standalone" @selected(old('display',config('laravelpwa.manifest.display')) == 'standalone' )>@lang('Standalone')</option>
                                                <option
                                                    value="minimal-ui" @selected(old('display',config('laravelpwa.manifest.display')) == 'minimal-ui' )>@lang('Minimal Ui')</option>
                                                <option
                                                    value="browser" @selected(old('display',config('laravelpwa.manifest.display')) == 'browser' )>@lang('Browser')</option>
                                            </select>
                                        </div>
                                        @error('display')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-sm-6">
                                        <label for="display" class="form-label">@lang('Status Bar')</label>
                                        <div class="tom-select-custom">
                                            <select
                                                class="js-select form-select @error('status_bar') is-invalid @enderror"
                                                id="status_bar" name="status_bar">
                                                <option
                                                    value="default" @selected(old('status_bar',config('laravelpwa.manifest.status_bar')) == 'default' )>@lang('Default')</option>
                                                <option
                                                    value="black" @selected(old('status_bar',config('laravelpwa.manifest.status_bar')) == 'black' )>@lang('Black')</option>
                                                <option
                                                    value="black-translucent" @selected(old('status_bar',config('laravelpwa.manifest.status_bar')) == 'black-translucent' )>@lang('Black Translucent')</option>
                                            </select>
                                        </div>
                                        @error('display')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label class="col-form-label">@lang('Icon')</label>
                                        <label class="form-check form-check-dashed" for="logoUploader">
                                            <img id="logoImg"
                                                 class="avatar avatar-xl avatar-4x3 avatar-centered h-100 mb-2 object-fit-contain"
                                                 src="{{ collect(config('laravelpwa.manifest.icons'))->first()['path'] }}"
                                                 alt="@lang("Logo")"
                                                 data-hs-theme-appearance="default">

                                            <img id="logoImg"
                                                 class="avatar avatar-xl avatar-4x3 avatar-centered h-100 mb-2 object-fit-contain"
                                                 src="{{ collect(config('laravelpwa.manifest.icons'))->first()['path'] }}"
                                                 alt="@lang("Logo")" data-hs-theme-appearance="dark">
                                            <span class="d-block mb-3">@lang("Browse your file here")</span>
                                            <input type="file" class="js-file-attach-logo form-check-input"
                                                   name="icon" id="logoUploader"
                                                   data-hs-file-attach-options='{
                                                      "textTarget": "#logoImg",
                                                      "mode": "image",
                                                      "targetAttr": "src",
                                                      "allowTypes": [".png", ".jpeg", ".jpg", ".svg"]
                                                   }'>
                                        </label>
                                        @error("logo")
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="col-form-label">@lang('Splash')</label>
                                        <label class="form-check form-check-dashed" for="faviconUploader">
                                            <img id="faviconImg"
                                                 class="avatar avatar-xl avatar-4x3 avatar-centered h-100 mb-2 object-fit-contain"
                                                 src="{{ collect(config('laravelpwa.manifest.splash'))->first() }}"
                                                 alt="@lang("Favicon")"
                                                 data-hs-theme-appearance="default">

                                            <img id="faviconImg"
                                                 class="avatar avatar-xl avatar-4x3 avatar-centered h-100 mb-2 object-fit-contain"
                                                 src="{{ collect(config('laravelpwa.manifest.splash'))->first() }}"
                                                 alt="@lang("Favicon")" data-hs-theme-appearance="dark">
                                            <span class="d-block mb-3">@lang("Browse your file here")</span>
                                            <input type="file" class="js-file-attach-favicon form-check-input"
                                                   name="splash" id="faviconUploader"
                                                   data-hs-file-attach-options='{
                                                      "textTarget": "#faviconImg",
                                                      "mode": "image",
                                                      "targetAttr": "src",
                                                      "allowTypes": [".png", ".jpeg", ".jpg", ".svg"]
                                                   }'>
                                        </label>
                                        @error("favicon")
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="d-flex justify-content-start">
                                    <button type="submit" class="btn btn-primary">@lang('Save changes')</button>
                                </div>
                            </form>
                        </div>
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
    <script src="{{ asset("assets/admin/js/hs-file-attach.min.js") }}"></script>
@endpush

@push('script')
    <script>
        'use strict';
        $( document ).ready(function() {
            HSCore.components.HSTomSelect.init('.js-select', {
                maxOptions: 500
            })
            new HSFileAttach('.js-file-attach-logo', {
                textTarget: "#logoImg"
            });
            new HSFileAttach('.js-file-attach-favicon', {
                textTarget: "#faviconImg"
            });
        })
    </script>
@endpush
