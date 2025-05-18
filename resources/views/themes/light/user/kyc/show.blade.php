@extends($theme.'layouts.user')
@section('page_title',__('KYC Form'))
@section('content')
    <div class="section dashboard">
        <div class="row">
            @include($theme.'user.profile.profileNav')
            <form action="{{route('user.kyc.verification.submit',$kyc->id)}}" method="post"
                  enctype="multipart/form-data">
                @csrf
                <div class="account-settings-profile-section">
                    <div class="card">
                        @if($kyc->kycPosition() == 'verified')
                            <div class="card-header border-0 text-start text-md-center">
                                <h5 class="card-title">@lang('KYC Information')</h5>
                                <p class="text-success">@lang('Your kyc is verified')</p>
                            </div>
                        @else
                            <div class="card-header border-0 text-start text-md-center">
                                <h5 class="card-title">@lang('KYC Information')</h5>
                                <p>@lang('Verify your process instantly.')</p>
                            </div>
                            <div class="card-body pt-0">
                                <div class="row">
                                    <div class="col-md-8 mx-auto">
                                        <div class="row g-4">
                                            @if($kyc->input_form)
                                                @foreach($kyc->input_form as $k => $v)
                                                    @if($v->type == "text")
                                                        <div class="col-12">
                                                            <label
                                                                class="form-label">{{trans($v->field_label)}} @if($v->validation == 'required')
                                                                    *
                                                                @endif</label>
                                                            <input type="text" name="{{$k}}"
                                                                   class="form-control"
                                                                   @if($v->validation == "required") required @endif>
                                                            @if ($errors->has($k))
                                                                <span
                                                                    class="text-danger">{{ trans($errors->first($k)) }}</span>
                                                            @endif
                                                        </div>
                                                    @elseif($v->type == "number")
                                                        <div class="col-12">
                                                            <label
                                                                class="form-label">{{trans($v->field_label)}} @if($v->validation == 'required')
                                                                    *
                                                                @endif</label>
                                                            <input type="number" name="{{$k}}"
                                                                   class="form-control"
                                                                   @if($v->validation == "required") required @endif>
                                                            @if ($errors->has($k))
                                                                <span
                                                                    class="text-danger">{{ trans($errors->first($k)) }}</span>
                                                            @endif
                                                        </div>

                                                    @elseif($v->type == "date")
                                                        <div class="col-12">
                                                            <label
                                                                class="form-label">{{trans($v->field_label)}} @if($v->validation == 'required')
                                                                    *
                                                                @endif</label>
                                                            <input type="date" name="{{$k}}"
                                                                   class="form-control"
                                                                   @if($v->validation == "required") required @endif>
                                                            @if ($errors->has($k))
                                                                <span
                                                                    class="text-danger">{{ trans($errors->first($k)) }}</span>
                                                            @endif
                                                        </div>

                                                    @elseif($v->type == "textarea")
                                                        <div class="col-12">
                                                            <label
                                                                class="form-label"><strong>{{trans($v->field_label)}} @if($v->validation == 'required')
                                                                        *
                                                                    @endif
                                                                </strong></label>
                                                            <textarea name="{{$k}}" class="form-control" rows="3"
                                                                      @if($v->validation == "required") required @endif></textarea>
                                                            @if ($errors->has($k))
                                                                <span
                                                                    class="text-danger">{{ trans($errors->first($k)) }}</span>
                                                            @endif
                                                        </div>
                                                    @elseif($v->type == "file")

                                                        <div class="col-12">
                                                            <div class="profile-details-section mt-0">
                                                                <label
                                                                    class="form-label">{{trans($v->field_label)}} @if($v->validation == 'required')
                                                                        *
                                                                    @endif</label>
                                                                <div class="d-flex gap-3 align-items-center">
                                                                    <div class="image-area">
                                                                        <img src="{{getFile('local','dummy')}}"
                                                                             alt="..." class="img-profile-view h-100">
                                                                    </div>
                                                                    <div class="btn-area">
                                                                        <div class="btn-area-inner d-flex">
                                                                            <div class="cmn-file-input">
                                                                                <label for="formFile"
                                                                                       class="form-label cmn-btn">
                                                                                    @lang('Select')
                                                                                    {{$v->field_label}}</label>
                                                                                <input
                                                                                    class="form-control file-upload-input"
                                                                                    type="file"
                                                                                    name="{{$k}}" accept="image/*"
                                                                                    @if($v->validation == "required") required
                                                                                    @endif
                                                                                    id="formFile">
                                                                            </div>
                                                                        </div>
                                                                        <small>@lang('Allowed JPG, jpeg or PNG. Max size of 2048K')</small>
                                                                    </div>
                                                                    @if ($errors->has($k))
                                                                        <br>
                                                                        <span
                                                                            class="text-danger">{{ __($errors->first($k)) }}</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>

                                                    @endif
                                                @endforeach
                                            @endif
                                            <div class="btn-area">
                                                <button type="submit" class="cmn-btn">@lang('submit')</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('extra_scripts')
    <script>
        'use strict'
        $(document).ready(function () {
            $(document).on('change', '.file-upload-input', function () {
                let _this = $(this);
                let reader = new FileReader();
                reader.readAsDataURL(this.files[0]);
                reader.onload = function (e) {
                    $('.img-profile-view').attr('src', e.target.result);
                }
            });
        })
    </script>
@endpush
