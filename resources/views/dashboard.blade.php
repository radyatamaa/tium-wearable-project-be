@php
$pageTitle = config('app.lang') != 'en' ? __('대시보드') : __('Dashboard');
@endphp

@extends('layouts.app', ['page' => $pageTitle, 'pageSlug' => 'dashboard'])

@section('content-fluid')
<!-- header -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-primary d-flex justify-content-between align-items-center p-2"
                style="--header-background-color: #324D65; background-color: var(--header-background-color);">
                <div class="me-3">
                    @if (config('app.lang') != 'en')
                    {{ __('운영현황') }}
                    @else
                    {{ __('Operational status') }}
                    @endif
                </div>
                @if($lastNoticeNotification)
                <div class="notification-card" style="cursor: pointer;" onclick="window.location.href='/notice'">
                    <span class="icon"><img src="{{ asset('black') }}/icons/general/bell.svg"></i></span>
                    <span class="title">
                        {{ Str::limit($lastNoticeNotification->announcement_title, 23, '...') }}
                    </span>
                    <span class="date">
                        {{ $lastNoticeNotification->registration_date }}
                    </span>
                </div>
                @endif
            </div>
            <div class="custom-container-dashboard">
                @foreach($countDashboardServices as $countDashboardService)
                <div class="custom-info-box {{$countDashboardService['color']}}"
                    style="width:{{$cardDashboardWidth}}%;cursor: pointer;" id="{{$countDashboardService['id']}}"
                    {{$countDashboardService['link']}} ? onclick="location.href='{{$countDashboardService['link']}}'"
                    : ''>
                    <span class="custom-icon"><img src="{{ asset('black') . $countDashboardService['icon'] }}"></span>
                    <span class="custom-text">
                        {{ $countDashboardService['name'] }}
                    </span>
                    <span class="custom-number">{{$countDashboardService['count']}}</span>
                </div>
                @endforeach

                <!-- <div class="custom-info-box custom-bg-green">
                    <span class="custom-icon"><img
                            src="{{ asset('black') }}/icons/dashboard/unprocessed-inquiries.svg"></span>
                    <span class="custom-text">
                        {{ config('app.lang') != 'en' ? __('미처리 문의') : __('Unprocessed Inquiries'); }}
                    </span>
                    <span class="custom-number">12</span>
                </div> -->
            </div>
        </div>
    </div>
</div>
<!--  -->
<!-- table -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-primary d-flex justify-content-between"
                style="--header-background-color: #1E3142;">
                <h5 class="title">{{ config('app.lang') != 'en' ? __('최근 가입 고객사') : __('Recent Customers'); }}</h5>
            </div>
            <div class="card-body">
                <div class="styled-table-container">
                    <table class="styled-table">
                        <thead>
                            <tr>
                                <th style="min-width:50px">
                                    {{ config('app.lang') != 'en' ? __('번호') : __('Number') }}
                                </th>
                                <th style="min-width:50px">
                                    {{ config('app.lang') != 'en' ? __('아이디') : __('ID') }}
                                </th>
                                <th style="min-width:100px">
                                    {{ config('app.lang') != 'en' ? __('업체(기관)명') : __('Organization Name') }}
                                </th>
                                <th style="min-width:100px">
                                    {{ config('app.lang') != 'en' ? __('주소') : __('Address') }}
                                </th>
                                <th style="min-width:50px">
                                    {{ config('app.lang') != 'en' ? __('담당자명') : __('Name Of Person In Charge') }}
                                </th>
                                <th style="min-width:90px">
                                    {{ config('app.lang') != 'en' ? __('이용서비스') : __('Service For Use') }}
                                </th>
                                <th style="min-width:110px">
                                    {{ config('app.lang') != 'en' ? __('사용시작일') : __('Start Date Of Use') }}
                                </th>
                                <th style="min-width:110px">
                                    {{ config('app.lang') != 'en' ? __('사용만료일') : __('Expired Date') }}
                                </th>
                                <th class="text-right" style="min-width:110px">
                                    {{ config('app.lang') != 'en' ? __('회원가입일') : __('Date Of Membership Registration') }}
                                </th>
                                <th class="text-right" style="min-width:270px">
                                    {{ config('app.lang') != 'en' ? __('행동') : __('Management') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                            <tr>
                                <td>{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                                <td>{{ $user->user_id }}</td>
                                <td>{{ $user->subscriptionInformation ? $user->subscriptionInformation->organization_name : '' }}
                                </td>
                                <td>{{ $user->subscriptionInformation ? $user->subscriptionInformation->address : ''}}
                                </td>
                                <td>{{ $user->personInChargeInformation ? $user->personInChargeInformation->name : '' }}
                                </td>
                                <td>{{ $user->serviceSubscriptionInformation ? $user->serviceSubscriptionInformation->subscription_service : ''}}
                                </td>
                                <td>{{ $user->subscriptionInformation ? $user->subscriptionInformation->service_start_date : ''}}
                                </td>
                                <td>{{ $user->serviceSubscriptionInformation ? $user->serviceSubscriptionInformation->expiration_date : ''}}
                                </td>
                                <td>{{ $user->serviceSubscriptionInformation ? $user->serviceSubscriptionInformation->start_date_of_use : ''}}
                                </td>
                                <td class="td-actions text-right">
                                    <button class="btn btn-warning btn-sm" type="button"
                                        onclick="remoteUser({{$user->id}})">
                                        {{ config('app.lang') != 'en' ? __('사용자용') : __('For users') }}
                                    </button>
                                    <button class="btn btn-info btn-sm" type="button"
                                        onclick="remoteManagement({{$user->id}})"
                                        style="padding: 5px 12px; color: white;  cursor: pointer;">
                                        {{ config('app.lang') != 'en' ? __('관리용') : __('For management') }}
                                    </button>
                                    <button class="btn btn-primary btn-sm" type="button"
                                        style="padding: 5px 12px; color: white;  cursor: pointer;"
                                        onclick="redirectToUserDetails('{{ route('user.index') . '/' . $user->id }}')">
                                        {{ config('app.lang') != 'en' ? __('회원정보관리') : __('Member information management') }}
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center">
                    {{ $users->links('layouts.pagination') }}
                </div>
            </div>
        </div>
    </div>
</div>
<!--  -->

<script>
function redirectToUserDetails(url) {
    window.location.href = url;
}
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let isDateRangeFilterActive = false;

    // Toggle date range filter on button click and submit the form
    document.getElementById('view-expiration-30-days').addEventListener('click', function() {
        const url = new URL(window.location.href);
        const params = new URLSearchParams(url.search);

        if (isDateRangeFilterActive) {
            params.delete('expired_start_date');
            params.delete('expired_end_date');
            isDateRangeFilterActive = false;
        } else {
            const currentDate = new Date();
            const startDate = new Date(currentDate);
            // startDate.setDate(currentDate.getDate() - 30);
            const endDate = new Date(currentDate);
            endDate.setDate(currentDate.getDate() + 30);

            const startDateFormatted = startDate.toISOString().split('T')[0];
            const endDateFormatted = endDate.toISOString().split('T')[0];

            params.set('expired_start_date', startDateFormatted);
            params.set('expired_end_date', endDateFormatted);
            isDateRangeFilterActive = true;
        }

        url.search = params.toString();
        window.location.href = url.toString();
    });
});
</script>

<script>
function remoteUser(id) {
    fetch(`user/auto-login-for-user/${id}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.data.user_type === 'MEDICAL_INSTITUTION') {
                    window.location.href = `/hospital/login?key=${data.data.key}`;
                } else if (data.data.user_type === 'INDUSTRIAL_SCENE') {
                    window.location.href = `/general-purpose/login?key=${data.data.key}`;
                } else if (data.data.user_type === 'PUBLIC_WELFARE') {
                    window.location.href = `/public-welfare/login?key=${data.data.key}`;
                } else {
                    window.location.href = `/general-purpose/login?key=${data.data.key}`;
                }
            } else {
                $('#errorFailedPopupModal').modal('show');
            }
        })
        .catch(error => console.error('Error:', error));

}

function remoteManagement(id) {
    fetch(`user/auto-login-for-management/${id}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.data.user_type === 'MEDICAL_INSTITUTION') {
                    window.location.href = `/hospital/login?key=${data.data.key}`;
                } else if (data.data.user_type === 'INDUSTRIAL_SCENE') {
                    window.location.href = `/general-purpose/login?key=${data.data.key}`;
                } else if (data.data.user_type === 'PUBLIC_WELFARE') {
                    window.location.href = `/public-welfare/login?key=${data.data.key}`;
                } else {
                    window.location.href = `/general-purpose/login?key=${data.data.key}`;
                }
            } else {
                $('#errorFailedPopupModal').modal('show');
            }
        })
        .catch(error => console.error('Error:', error));
}
</script>
@endsection