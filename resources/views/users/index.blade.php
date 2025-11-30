@php
$pageTitle = config('app.lang') != 'en' ? __('사용자관리') : __('User Management');
@endphp

@extends('layouts.app', ['page' => $pageTitle, 'pageSlug' => 'user_management'])

@section('content-fluid')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-primary d-flex justify-content-between align-items-center p-2"
                style="--header-background-color: #324D65; background-color: var(--header-background-color);">
                <div class="me-3">
                    @if (config('app.lang') != 'en')
                    {{ __('전체 (' . $users->total() . ')') }}
                    @else
                    {{ __('Total (' . $users->total() . ')') }}
                    @endif
                </div>
                <div>
                    <button class="btn btn-dark btn-sm" type="button" disabled>
                        @if (config('app.lang') != 'en')
                        {{ __('검색 총 ' . $users->total() . '개') }}
                        @else
                        {{ __('A total of ' . $users->total() . ' search') }}
                        @endif
                    </button>
                </div>
            </div>
            <div class="card-header card-header-primary d-flex justify-content-between"
                style="--header-background-color: #1E3142; background-color: var(--header-background-color);padding: 5px;">
                <div class="header-hospital-filter-container">
                    <div class="header-hospital-filter-row">
                        <form id="filter-form" method="GET" action="/user" class="d-flex justify-content-between w-100">
                            <div class="header-hospital-filter-left">

                                <div class="btn-group">
                                    <div class="date-picker-container">
                                        <i class="fas fa-calendar-alt" onclick="toggleCalendar('calendar-1')"></i>
                                        <input type="text" id="datepicker-1" value="YYYY-MM-DD" name="start_date_range">
                                        <div class="calendar" id="calendar-1"></div>
                                    </div>
                                </div>
                                <div class="btn-group">
                                    <div class="date-picker-container">
                                        <i class="fas fa-calendar-alt" onclick="toggleCalendar('calendar-2')"></i>
                                        <input type="text" id="datepicker-2" value="YYYY-MM-DD" name="end_date_range">
                                        <div class="calendar" id="calendar-2"></div>
                                    </div>
                                </div>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                        {{ config('app.lang') != 'en' ? __('전체 기간') : __('Full term') }}
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="#"
                                            onclick="updateDatePickers(1)">{{ config('app.lang') != 'en' ? __('1 개월') : __('1 month') }}</a>
                                        <a class="dropdown-item" href="#"
                                            onclick="updateDatePickers(3)">{{ config('app.lang') != 'en' ? __('3 개월') : __('3 months') }}</a>
                                        <a class="dropdown-item" href="#"
                                            onclick="updateDatePickers(6)">{{ config('app.lang') != 'en' ? __('6 개월') : __('6 months') }}</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="#"
                                            onclick="updateDatePickers(12)">{{ config('app.lang') != 'en' ? __('일년') : __('1 year') }}</a>
                                    </div>
                                </div>
                            </div>
                            <div class="header-hospital-filter-right">
                                <button class="btn btn-warning btn-sm" type="button"
                                    style="padding: 10px 16px;margin-bottom:10px" onclick="execDaumPostcode()">
                                    @if (config('app.lang') != 'en')
                                    {{ __('지역 정보로 찾기') }}
                                    @else
                                    {{ __('Find By location info') }}
                                    @endif
                                </button>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false" id="searchByButton">
                                        @if (config('app.lang') != 'en')
                                        {{ __('아이디') }}
                                        @else
                                        {{ __('ID') }}
                                        @endif
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="#" data-value="user_id">
                                            @if (config('app.lang') != 'en')
                                            {{ __('아이디') }}
                                            @else
                                            {{ __('ID') }}
                                            @endif
                                        </a>
                                        <a class="dropdown-item" href="#" data-value="name">
                                            @if (config('app.lang') != 'en')
                                            {{ __('업체(기관)명') }}
                                            @else
                                            {{ __('Organization Name') }}
                                            @endif
                                        </a>
                                    </div>
                                </div>
                                <input type="hidden" name="search_by" id="searchBy">
                                <div class="search-container" style="margin-bottom:5px">
                                    <input type="text" class="search-input"
                                        placeholder="{{ config('app.lang') != 'en' ? '검색어를 입력해 주세요' : 'Please enter a search term' }}"
                                        name="search" id="searchInput">
                                    <button type="submit" class="search-button">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-primary d-flex justify-content-between"
                style="--header-background-color: #1E3142;">
                <form id="filter-form-top" method="GET" action="/user" class="d-flex justify-content-between w-100">
                    <div>
                        <button class="btn btn-warning btn-sm" type="button" id="select-all">
                            @if (config('app.lang') != 'en')
                            {{ __('전체 선택') }}
                            @else
                            {{ __('Full selection') }}
                            @endif
                        </button>
                        <button class="btn btn-primary btn-info btn-sm" type="button" id="deselect-all">
                            @if (config('app.lang') != 'en')
                            {{ __('전체 해제') }}
                            @else
                            {{ __('All of') }}
                            @endif
                        </button>
                        <button class="btn btn-primary btn-danger btn-sm" type="button" id="delete-selected">
                            @if (config('app.lang') != 'en')
                            {{ __('삭제') }}
                            @else
                            {{ __('Elimination') }}
                            @endif
                        </button>
                        <a href="{{ route('user.create') }}" class="btn btn-primary btn-sm" type="button">
                            @if (config('app.lang') != 'en')
                            {{ __('사용자 등록') }}
                            @else
                            {{ __('User Registration') }}
                            @endif
                        </a>
                    </div>
                    <div>
                        <!-- <button class="btn btn-info btn-sm" type="button">
                            @if (config('app.lang') != 'en')
                                {{ __('선택 회원 이메일 보내기') }}
                            @else
                                {{ __('Send elective member email') }}
                            @endif
                        </button>
                        <button class="btn btn-info btn-sm" type="button">
                            @if (config('app.lang') != 'en')
                                {{ __('선택 회원 SMS 보내기') }}
                            @else
                                {{ __('Send select member SMS') }}
                            @endif
                        </button> -->
                        <button class="btn btn-warning btn-sm" type="button" id="view-expiration-30-days">
                            @if (config('app.lang') != 'en')
                            {{ __('30일내 사용만료 보기') }}
                            @else
                            {{ __('View expiration within 30 days') }}
                            @endif
                        </button>
                        <div class="btn-group">
                            <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false" id="usage_status">
                                @if (config('app.lang') != 'en')
                                {{ __('이용상태 전체') }}
                                @else
                                {{ __('Usage Status All') }}
                                @endif
                            </button>
                            <div class="dropdown-menu">
                                @foreach ($serviceUsageSettings as $serviceUsageSetting)
                                <a class="dropdown-item usage-status" href="#"
                                    data-status="{{$serviceUsageSetting->service_classification}}">
                                    {{ $serviceUsageSetting->service_classification_desc }}
                                </a>
                                @endforeach
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item usage-status" href="#" data-status="all">
                                    @if (config('app.lang') != 'en')
                                    {{ __('모두') }}
                                    @else
                                    {{ __('All') }}
                                    @endif
                                </a>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="postalCodeAddress" id="postalCodeAddress">
                    <input type="hidden" name="address" id="address">
                    <input type="hidden" name="fullAddress" id="fullAddress">
                </form>
            </div>
            <div class="card-body">
                <div class="styled-table-container">
                    <table class="styled-table">
                        <thead>
                            <tr>
                                <th class="text-center" style="min-width:50px">
                                    {{ config('app.lang') != 'en' ? __('선택') : __('Choice') }}
                                </th>
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
                            @foreach($users as $user)
                            <tr>
                                <td class="text-center"><input type="checkbox" class="user-checkbox"
                                        value="{{ $user->id }}"></td>
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
                                        style="padding: 5px 12px; color: white;  cursor: pointer;"
                                        onclick="remoteManagement({{$user->id}})">
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

<!-- modal location -->
<div class="modal fade" id="localInfoModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background-color:#1E3142">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title" style="color:#FFFFFF;background-color:#4E6880">
                    {{ config('app.lang') != 'en' ? __('지역 정보로 찾기') : __('Find by Local Information') }}
                </h4>
            </div>

            <!-- Modal Body -->
            <div class="modal-body" style="background-color:#1E3142">
                <div class="header-hospital-filter-container">
                    <div class="header-hospital-filter-row">
                        <div class="header-hospital-filter-left">
                            <div class="btn-group">
                                <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false" id="city">
                                    @if (config('app.lang') != 'en')
                                    {{ __('도시') }}
                                    @else
                                    {{ __('City') }}
                                    @endif
                                </button>
                                <div class="dropdown-menu" style="max-height: 200px; overflow: auto;">
                                    @foreach ($cities as $city)
                                    <a class="dropdown-item" href="#" data-value="{{$city->id}}"
                                        onclick="fetchDistrict({{$city->id}})">
                                        @if (config('app.lang') != 'en')
                                        {{ $city->city_name }}
                                        @else
                                        {{ $city->city_name_en }}
                                        @endif
                                    </a>
                                    @endforeach
                                </div>
                            </div>
                            <div class="btn-group" style="display:none" id="district_btn">
                                <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false" id="district">
                                    @if (config('app.lang') != 'en')
                                    {{ __('구역') }}
                                    @else
                                    {{ __('District') }}
                                    @endif
                                </button>
                                <div class="dropdown-menu" style="max-height: 200px; overflow: auto;"
                                    id="dropdown_district">
                                </div>
                            </div>
                        </div>
                        <div class="header-hospital-filter-right">
                            <!-- Optional: Add any additional filter buttons here -->
                        </div>
                    </div>
                </div>

                <div class="text-center my-5" id="location-placeholder" style="display: block;">
                    <span class="icon"><img src="{{ asset('black') }}/icons/general/find-by-location-info.svg"></span>
                    <h3 style="margin-top:15px">
                        {{ config('app.lang') != 'en' ? __('지역 정보를 선택해 주세요.') : __('Please select your local information.') }}
                    </h3>
                </div>

                <div class="results-container" style="display: none;" id="results-container">
                    <h4>{{ config('app.lang') != 'en' ? __('총') : __('A total of') }} <span id="result-count"
                            style="color:#3CBAFF">0</span>
                        {{ config('app.lang') != 'en' ? __('곳의 의료기관이 검색되었습니다.') : __('medical institutions have been searched.') }}
                    </h4>
                    <div class="result-list d-flex flex-wrap" id="result-list">
                        <!-- Result items will be dynamically inserted here -->
                    </div>
                    <div class="pagination-container" style="margin-top:50px;margin-bottom:-50px">
                        <div class="pagination-info" id="pagination-info"></div>
                        <nav data-pagination>
                            <ul class="pagination justify-content-center" id="pagination">
                                <!-- Pagination items will be dynamically inserted here -->
                            </ul>
                        </nav>
                        <div class="pagination-limit">

                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer" style="background-color:#1E3142;margin-top:50px">
                <!-- Optional: Add footer buttons here -->
            </div>
        </div>
    </div>
</div>



<!--  -->
<script>
function redirectToUserDetails(url) {
    window.location.href = url;
}

// Select/Deselect all checkboxes
document.getElementById('select-all').addEventListener('click', function() {
    document.querySelectorAll('.user-checkbox').forEach(checkbox => checkbox.checked = true);
});

document.getElementById('deselect-all').addEventListener('click', function() {
    document.querySelectorAll('.user-checkbox').forEach(checkbox => checkbox.checked = false);
});

// Delete selected users
document.getElementById('delete-selected').addEventListener('click', function() {
    const selectedUserIds = Array.from(document.querySelectorAll('.user-checkbox:checked')).map(cb => cb.value);
    if (selectedUserIds.length > 0) {
        // Show the confirmation modal
        $('#deletedConfirmationPopUpModal').modal('show');
        document.getElementById('confirmed').addEventListener('click', function confirmHandler() {
            fetch('/user/delete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        user_ids: selectedUserIds
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        $('#successDeletedPopupModal').modal('show');
                    } else {
                        $('#errorFailedPopupModal').modal('show');
                    }
                })
                .catch(error => console.error('Error:', error))
                .finally(() => {
                    // Remove the event listener after the click is handled
                    document.getElementById('confirmed').removeEventListener('click',
                        confirmHandler);
                    // Hide the confirmation modal
                    $('#deletedConfirmationPopUpModal').modal('hide');
                });
        }, {
            once: true
        });
    }
});
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

    // Toggle usage status filter on dropdown item click and submit the form
    const usageStatusItems = document.querySelectorAll('.usage-status');
    usageStatusItems.forEach(function(item) {
        item.addEventListener('click', function(event) {
            event.preventDefault();
            const url = new URL(window.location.href);
            const params = new URLSearchParams(url.search);

            if (params.get('usage_status') === this.getAttribute('data-status')) {
                params.delete('usage_status');
            } else {
                params.set('usage_status', this.getAttribute('data-status'));
            }

            url.search = params.toString();
            window.location.href = url.toString();
        });
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

<!-- find location info -->
<script>
const language = `{{config('app.lang')}}`

function fetchDistrict(id) {
    document.getElementById('district').innerText = `{{ config('app.lang') != 'en' ? __('구역') : __('District') }}`;
    const dropdownDistrict = document.getElementById('dropdown_district');

    document.getElementById('location-placeholder').style.display = 'block';
    document.getElementById('results-container').style.display = 'none';

    dropdownDistrict.innerHTML = '<a class="dropdown-item" href="#"></a>';
    fetch(`user/district/${id}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                data.data.forEach(dst => {
                    let districtName = language != 'en' ? dst.district_name : dst.district_name_en;
                    districtName += ` (${dst.postal_code})`;
                    dropdownDistrict.innerHTML +=
                        `<a class="dropdown-item" href="#" data-value="${dst.id}" onclick="fetchUserGeneralPurpose('${dst.postal_code}','${districtName}')">${districtName}</a>`;

                });

                if (data.data.length == 0) {
                    dropdownDistrict.innerHTML = '<a class="dropdown-item" href="#"></a>';
                }

                document.getElementById('district_btn').style = 'display:block';
            }
        })
        .catch(error => console.error('Error:', error));
}

const itemsPerPage = 24;
let currentPage = 1;

function fetchUserGeneralPurpose(postal_code, districtName, limit = itemsPerPage, page = 1) {
    document.getElementById('district').innerText = districtName;
    fetch(`{{ route('user.getUserList') }}?limit=${limit}&page=${page}&postal_code=${postal_code}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log(data); // Log the data to check if it is fetched correctly
            if (data.success) {
                renderResults(data.data, postal_code, districtName);
            }
        })
        .catch(error => console.error('Error:', error));
}

function renderResults(data, postal_code, districtName) {
    const resultList = document.getElementById('result-list');
    const resultCount = document.getElementById('result-count');
    const paginationInfo = document.getElementById('pagination-info');
    const totalItems = data.total;
    const totalPages = data.last_page;
    const paginatedItems = data.data;
    currentPage = data.current_page;

    resultList.innerHTML = '';
    const unknown = language != 'en' ? '알려지지 않은' : 'unknown';
    paginatedItems.forEach(item => {
        const button = document.createElement('button');
        button.className = 'btn btn-info btn-sm m-1';
        button.textContent = item.organization_name || unknown;

        button.onclick = function() {
            selectedUserGeneralPurpose(item.id);
        };
        resultList.appendChild(button);
    });

    resultCount.textContent = totalItems;
    document.getElementById('location-placeholder').style.display = 'none';
    document.getElementById('results-container').style.display = 'block';

    renderPagination(totalPages, currentPage, itemsPerPage, postal_code, districtName);
}

function renderPagination(totalPages, currentPage, limit, postal_code, districtName) {
    const paginationEl = document.getElementById('pagination');
    paginationEl.innerHTML = '';

    const prevPage = currentPage > 1 ? currentPage - 1 : 1;
    const nextPage = currentPage < totalPages ? currentPage + 1 : totalPages;

    const prevLink = currentPage === 1 ?
        '<a href="#" class="disabled"><i class="ion-chevron-left"></i></a>' :
        `<a href="#" onclick="fetchUserGeneralPurpose('${postal_code}', '${districtName}', ${limit}, ${prevPage})"><i class="ion-chevron-left"></i></a>`;

    paginationEl.innerHTML += `<li>${prevLink}</li>`;

    for (let i = 1; i <= totalPages; i++) {
        const pageLink = i === currentPage ?
            `<li class="current"><a href="#">${i}</a></li>` :
            `<li><a href="#" onclick="fetchUserGeneralPurpose('${postal_code}', '${districtName}', ${limit}, ${i})">${i}</a></li>`;
        paginationEl.innerHTML += pageLink;
    }

    const nextLink = currentPage === totalPages ?
        '<a href="#" class="disabled"><i class="ion-chevron-right"></i></a>' :
        `<a href="#" onclick="fetchUserGeneralPurpose('${postal_code}', '${districtName}', ${limit}, ${nextPage})"><i class="ion-chevron-right"></i></a>`;

    paginationEl.innerHTML += `<li>${nextLink}</li>`;
}

function selectedUserGeneralPurpose(id) {
    window.location.href = `/user/${id}`
}
</script>

<script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script>
function execDaumPostcode() {
    new daum.Postcode({
        oncomplete: function(data) {
            // 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

            // 도로명 주소의 노출 규칙에 따라 주소를 표시한다.
            // 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
            var roadAddr = data.roadAddress; // 도로명 주소 변수
            var extraRoadAddr = ''; // 참고 항목 변수

            // 법정동명이 있을 경우 추가한다. (법정리는 제외)
            // 법정동의 경우 마지막 문자가 "동/로/가"로 끝난다.
            if (data.bname !== '' && /[동|로|가]$/g.test(data.bname)) {
                extraRoadAddr += data.bname;
            }
            // 건물명이 있고, 공동주택일 경우 추가한다.
            if (data.buildingName !== '' && data.apartment === 'Y') {
                extraRoadAddr += (extraRoadAddr !== '' ? ', ' + data.buildingName : data.buildingName);
            }
            // 표시할 참고항목이 있을 경우, 괄호까지 추가한 최종 문자열을 만든다.
            if (extraRoadAddr !== '') {
                extraRoadAddr = ' (' + extraRoadAddr + ')';
            }

            // 우편번호와 주소 정보를 해당 필드에 넣는다.
            document.getElementById('postalCodeAddress').value = data.zonecode;
            document.getElementById("address").value = roadAddr;

            if (data.jibunAddress) {
                document.getElementById("address").value = roadAddr + ' ,' + data.jibunAddress;
            }
            // 참고항목 문자열이 있을 경우 해당 필드에 넣는다.
            if (roadAddr !== '') {
                document.getElementById("fullAddress").value = extraRoadAddr;
            } else {
                document.getElementById("fullAddress").value = '';
            }

            // var guideTextBox = document.getElementById("guide");
            // // 사용자가 '선택 안함'을 클릭한 경우, 예상 주소라는 표시를 해준다.
            // if (data.autoRoadAddress) {
            //     var expRoadAddr = data.autoRoadAddress + extraRoadAddr;
            //     guideTextBox.innerHTML = '(예상 도로명 주소 : ' + expRoadAddr + ')';
            //     guideTextBox.style.display = 'block';

            // } else if (data.autoJibunAddress) {
            //     var expJibunAddr = data.autoJibunAddress;
            //     guideTextBox.innerHTML = '(예상 지번 주소 : ' + expJibunAddr + ')';
            //     guideTextBox.style.display = 'block';
            // } else {
            //     guideTextBox.innerHTML = '';
            //     guideTextBox.style.display = 'none';
            // }
        },
        onclose: function(state) {
            //state는 우편번호 찾기 화면이 어떻게 닫혔는지에 대한 상태 변수 이며, 상세 설명은 아래 목록에서 확인하실 수 있습니다.
            if (state === 'FORCE_CLOSE') {
                console.log(state)

            } else if (state === 'COMPLETE_CLOSE') {
                var form = document.getElementById('filter-form-top');
                form.submit();
            }
        }
    }).open();
}
</script>
@endsection