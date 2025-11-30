@php
$pageTitle = config('app.lang') != 'en' ? __('로케이션 관리') : __('detailed-location-management');
@endphp

@extends('layouts.app', ['page' => $pageTitle, 'pageSlug' => 'detailed_location_management'])

@section('content-fluid')
<!-- header -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-primary d-flex justify-content-between align-items-center p-2"
                style="--header-background-color: #324D65; background-color: var(--header-background-color);">
                <div class="header-hospital-filter-container">
                    <div class="header-hospital-filter-row">
                        <form id="filter-form" method="GET"
                            action="/hospital/settings/location-management/detailed-location"
                            class="d-flex justify-content-between w-100">
                            <div class="header-hospital-filter-left">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false" style="background-color:#4E6880">
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
                                <div class="btn-group">
                                    <div class="date-picker-container" style="background-color:#4E6880">
                                        <i class="fas fa-calendar-alt" onclick="toggleCalendar('calendar-1')"></i>
                                        <input type="text" id="datepicker-1" value="YYYY-MM-DD" name="start_date_range">
                                        <div class="calendar" id="calendar-1"></div>
                                    </div>
                                </div>
                                <div class="btn-group">
                                    <div class="date-picker-container" style="background-color:#4E6880">
                                        <i class="fas fa-calendar-alt" onclick="toggleCalendar('calendar-2')"></i>
                                        <input type="text" id="datepicker-2" value="YYYY-MM-DD" name="end_date_range">
                                        <div class="calendar" id="calendar-2"></div>
                                    </div>
                                </div>

                            </div>
                            <div class="header-hospital-filter-right">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false" style="background-color:#4E6880"
                                        id="searchByButton">
                                        @if (config('app.lang') != 'en')
                                        {{ __('자세한 위치 이름') }}
                                        @else
                                        {{ __('detailed Location Name') }}
                                        @endif
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="#" data-value="detailed_location_name">
                                            @if (config('app.lang') != 'en')
                                            {{ __('자세한 위치 이름') }}
                                            @else
                                            {{ __('detailed Location Name') }}
                                            @endif
                                        </a>
                                        <a class="dropdown-item" href="#" data-value="detailed_location_code">
                                            @if (config('app.lang') != 'en')
                                            {{ __('상세 위치 코드') }}
                                            @else
                                            {{ __('detailed Location Code') }}
                                            @endif
                                        </a>
                                    </div>
                                </div>
                                <input type="hidden" name="search_by" id="searchBy">
                                <div class="search-container" style="background-color:#4E6880;margin-bottom:5px">
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
            <div class="card-body-custom-1 flex-fill d-flex flex-column justify-content-left align-items-left w-100"
                style="background-color:#1E3142">
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
                    <button class="btn btn-primary btn-sm" type="button" data-toggle="modal"
                        data-target="#modalDetailedLocation">
                        {{ config('app.lang') != 'en' ? __('등록') : __('Registration') }}
                    </button>
                    <button class="btn btn-primary btn-danger btn-sm" type="button" id="delete-selected">
                        @if (config('app.lang') != 'en')
                        {{ __('삭제') }}
                        @else
                        {{ __('Elimination') }}
                        @endif
                    </button>
                    <button class="btn btn-primary btn-info btn-sm" type="button" id="export-excel"
                        onclick="exportExcel('{{ url('hospital/settings/location-management/detailed-location/export') }}')">
                        @if (config('app.lang') != 'en')
                        {{ __('엑셀 다운로드') }}
                        @else
                        {{ __('Download Excel') }}
                        @endif
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<!--  -->

<!-- form -->
<div class="row">
    <!-- table -->
    <div class="col-md-6">
        <div class="card" style="background-color:#1E3142;min-height:518px;text-align: center;">
            <div class="card-body">
                <div class="styled-table-container">
                    <table class="styled-table">
                        <thead>
                            <tr>
                                <th class="text-center">
                                    {{ config('app.lang') != 'en' ? __('선택') : __('Choice') }}
                                </th>
                                <th>
                                    {{ config('app.lang') != 'en' ? __('번호') : __('Number') }}
                                </th>
                                <th>
                                    {{ config('app.lang') != 'en' ? __('자세한 위치 이름') : __('Location Name') }}
                                </th>
                                <th>
                                    {{ config('app.lang') != 'en' ? __('상세 위치 코드') : __('Location Code') }}
                                </th>
                                <th>
                                    {{ config('app.lang') != 'en' ? __('사용 여부') : __('Usage Status') }}
                                </th>
                                <th>
                                    {{ config('app.lang') != 'en' ? __('등록일자') : __('Registration Date') }}
                                </th>
                                <th>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($datas as $location)
                            <tr>
                                <td><input type="checkbox" class="checkbox-click" value="{{ $location->id }}"></td>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $location->location_name }}</td>
                                <td>{{ $location->location_code }}</td>
                                <td>{{ $location->usage_status ? '사용' : '미사용' }}</td>
                                <td>{{ $location->registration_date }}</td>
                                <td class="td-actions">
                                    <button class="btn btn-warning btn-sm" type="button"
                                        onclick="viewDetails({{ $location->id }})">
                                        {{ config('app.lang') != 'en' ? __('세부 사항') : __('Detail') }}
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center">
                    {{ $datas->links('layouts.pagination') }}
                </div>
            </div>
        </div>
    </div>


    <!-- detailed information section -->
    <div class="col-md-6">
        <div class="card flex-fill d-flex flex-column" style="background-color: #1E3142; color: #FFFFFF;">
            <div class="card-header w-100 text-center" style="background-color: #324D65; color: #FFFFFF;">
                <h5 class="title">
                    {{ config('app.lang') != 'en' ? __('상세정보') : __('Detailed Information'); }}
                </h5>
            </div>
            <div class="card-body-custom-1 flex-fill d-flex flex-column justify-content-left align-items-left w-100"
                style="background-color:#1E3142;min-height:448px;">
                <!-- detailed information -->
                <div class="content-section active" id="selected-user-form" style="display:none">
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <h5 class="title" style="color: #FFFFFF;">
                                    {{ config('app.lang') != 'en' ? __('기본정보') : __('Basic Information') }}
                                </h5>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="patient-management-container">
                                <div class="patient-management-body">
                                    <div class="patient-info-column">
                                        <table class="patient-info-table">
                                            <tbody id="location-details">
                                                <!-- Details will be loaded here dynamically -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="not-selected-user-form">
                    <div class="card-body-custom-1 flex-fill d-flex flex-column justify-content-left align-items-left w-100"
                        style="background-color:#1E3142;min-height:448px;text-align: center;">
                        <div class="error-tium-icon">
                            <img src="{{ asset('black') }}/icons/general/error.svg" alt="warning">
                        </div>
                        <p class="error-tium-message">
                            {{ config('app.lang') != 'en' ? __('데이터 기록이 없습니다.') : __('There are no data records.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Add/Edit Account -->
<div class="modal fade" id="modalDetailedLocation">
    <div class="modal-dialog modal-lg" style="max-width:800px">
        <div class="modal-content modal-hospital-content">

            <!-- Modal Header -->
            <div class="modal-header"
                style="background-color:#3CBAFF;padding:20px 24px;align-items: center;justify-content: center;position: relative;">
                <h4 class="modal-title" style="color:#FFFFFF">
                    {{ config('app.lang') != 'en' ? __('상세 로케이션 등록') : __('Register detailed location') }}</h4>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <div class="text-center" style="margin-bottom:20px">
                    <h4 class="modal-title" style="color:#FFFFFF">
                        {{ config('app.lang') != 'en' ? __('기본정보') : __('Basic Information') }}</h4>
                </div>
                <div class="modal-hospital-form-row">
                    <label
                        for="date-registration-modal">{{ config('app.lang') != 'en' ? __('등록일자') : __('Date of registration') }}</label>
                    <input type="text" id="date-registration-modal" placeholder="YYYY-MM-DD" readonly>
                </div>
                <div class="modal-hospital-form-row">
                    <label
                        for="location-name-modal">{{ config('app.lang') != 'en' ? __('상세 로케이션명') : __('Location Name') }}</label>
                    <input type="text" id="location-name-modal">
                </div>
                <div class="modal-hospital-form-row">
                    <label
                        for="location-code-modal">{{ config('app.lang') != 'en' ? __('상세 로케이션코드') : __('Location Code') }}</label>
                    <input type="text" id="location-code-modal">
                </div>
                <div class="modal-hospital-form-row">
                    <label
                        for="usage-status-modal">{{ config('app.lang') != 'en' ? __('사용여부') : __('Usage Status') }}</label>
                    <select id="usage-status-modal">
                        <option>{{ config('app.lang') != 'en' ? __('선택') : __('Select') }}</option>
                        <option value="1">{{ config('app.lang') != 'en' ? __('사용') : __('Active') }}</option>
                        <option value="0">{{ config('app.lang') != 'en' ? __('미사용') : __('Inactive') }}</option>
                    </select>
                </div>
                <div class="modal-hospital-form-row">
                    <label
                        for="sub-location-id-modal">{{ config('app.lang') != 'en' ? __('하위 로케이션 설정') : __('Sub Location') }}</label>
                    <select id="sub-location-id-modal">
                        <option>{{ config('app.lang') != 'en' ? __('선택') : __('Select') }}</option>
                        @foreach($subLocations as $subLocation)
                        <option value="{{$subLocation->id}}">{{$subLocation->location_name}}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-primary modal-hospital-btn-storage" style="padding:10px 160px"
                    onclick="saveDetailedLocationModal()">{{ config('app.lang') != 'en' ? __('저장') : __('Save') }}</button>
                <button type="button" class="btn btn-info modal-hospital-btn-cancellation" style="padding:10px 160px"
                    data-dismiss="modal">{{ config('app.lang') != 'en' ? __('취소') : __('Cancel') }}</button>
            </div>

        </div>
    </div>
</div>

<script>
document.getElementById('select-all').addEventListener('click', function() {
    document.querySelectorAll('.checkbox-click').forEach(checkbox => checkbox.checked = true);
});

document.getElementById('deselect-all').addEventListener('click', function() {
    document.querySelectorAll('.checkbox-click').forEach(checkbox => checkbox.checked = false);
});

document.addEventListener('DOMContentLoaded', function() {
    var dateRegistrationInput = document.getElementById('date-registration-modal');
    var today = new Date();
    var day = String(today.getDate()).padStart(2, '0');
    var month = String(today.getMonth() + 1).padStart(2, '0'); // January is 0!
    var year = today.getFullYear();
    dateRegistrationInput.value = year + '-' + month + '-' + day;

    const navItems = document.querySelectorAll('.nav-bar-custom .nav-item');
    const sections = document.querySelectorAll('.content-section');

    navItems.forEach(item => {
        item.addEventListener('click', function() {
            // Remove active class from all nav items
            navItems.forEach(nav => nav.classList.remove('active'));
            // Add active class to the clicked nav item
            item.classList.add('active');

            // Hide all sections
            sections.forEach(section => section.classList.remove('active'));
            // Show the targeted section
            const target = item.getAttribute('data-target');
            document.getElementById(target).classList.add('active');
        });
    });
});

function saveDetailedLocationModal() {
    const dateRegistration = document.getElementById('date-registration-modal').value;
    const locationName = document.getElementById('location-name-modal').value;
    const locationCode = document.getElementById('location-code-modal').value;
    const usageStatus = document.getElementById('usage-status-modal').value;
    const subLocationId = document.getElementById('sub-location-id-modal').value;

    fetch('/hospital/settings/location-management/detailed-location', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                registration_date: dateRegistration,
                location_name: locationName,
                location_code: locationCode,
                usage_status: usageStatus,
                sub_location_id: subLocationId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                $('#successPopupModal').modal('show');
            } else {
                $('#errorFailedPopupModal').modal('show');
            }
        })
        .catch(error => console.error('Error:', error));
}

function viewDetails(id) {
    fetch('/hospital/settings/location-management/detailed-location/' + id)
        .then(response => response.json())
        .then(data => {
            const location = data.location;
            const detailsHtml = `
            <tr>
                <td>{{ config('app.lang') != 'en' ? __('상세 로케이션명') : __('Location Name') }}</td>
                <td style="background-color:#1E3142;color:#FFFFFF">${location.location_name}</td>
            </tr>
            <tr>
                <td>{{ config('app.lang') != 'en' ? __('상세 로케이션코드') : __('Location Code') }}</td>
                <td style="background-color:#1E3142;color:#FFFFFF">${location.location_code}</td>
            </tr>
            <tr>
                <td>{{ config('app.lang') != 'en' ? __('사용여부') : __('Usage Status') }}</td>
                <td style="background-color:#1E3142;color:#FFFFFF">${location.usage_status ? '사용' : '미사용'}</td>
            </tr>
            <tr>
                <td>{{ config('app.lang') != 'en' ? __('등록일자') : __('Registration Date') }}</td>
                <td style="background-color:#1E3142;color:#FFFFFF">${location.registration_date}</td>
            </tr>
        `;
            document.getElementById('location-details').innerHTML = detailsHtml;
            document.getElementById('selected-user-form').style.display = 'block';
            document.getElementById('not-selected-user-form').style.display = 'none';
        })
        .catch(error => console.error('Error:', error));
}

document.getElementById('delete-selected').addEventListener('click', function() {
    const selectedIds = Array.from(document.querySelectorAll('.checkbox-click:checked')).map(cb => cb
        .value);

    if (selectedIds.length > 0) {
        // Show the confirmation modal
        $('#deletedConfirmationPopUpModal').modal('show');

        // Add a one-time event listener for the confirm button
        document.getElementById('confirmed').addEventListener('click', function confirmHandler() {
            fetch('/hospital/settings/location-management/detailed-location', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        ids: selectedIds
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        $('#successPopupModal').modal('show');
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
@endsection