@extends($theme.'layouts.user')
@section('page_title',trans('Referral Bonus'))
@section('content')
    <div class="card mt-50">
        <div class="card-body">
            <div class="cmn-table">
                <div class="table-responsive overflow-visible">
                    <table class="table align-middle table-striped">
                        <thead>
                        <tr>
                            <th scope="col">@lang('SL')</th>
                            <th scope="col">@lang('Bonus From')</th>
                            <th scope="col">@lang('Amount')</th>
                            <th scope="col">@lang('Type')</th>
                            <th scope="col">@lang('Level')</th>
                            <th scope="col">@lang('Remarks')</th>
                            <th scope="col">@lang('Date')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(count($referrals) > 0)
                            @foreach($referrals as $key => $value)
                                <tr>
                                    <td data-label="@lang('SL')">{{$referrals->firstItem()+$key}}</td>
                                    <td data-label="@lang('Bonus From')">{{ $value->bonusBy->username}}</td>
                                    <td data-label="@lang('Amount')" class="text-success">{{currencyPosition($value->amount)}}</td>
                                    <td data-label="@lang('Type')">{{trans(snake2Title($value->commission_type))}}</td>
                                    <td data-label="@lang('Level')">@lang('Level') {{$value->level}}</td>
                                    <td data-label="@lang('Remarks')">{{trans($value->remarks)}}</td>
                                    <td data-label="@lang('Date')"> {{ dateTime($value->created_at)}} </td>
                                </tr>
                            @endforeach
                        @else
                            @include('empty')
                        @endif
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
    {{ $referrals->appends($_GET)->links($theme.'partials.user.pagination') }}

@endsection




