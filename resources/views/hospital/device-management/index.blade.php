@php
$pageTitle = config('app.lang') != 'en' ? __('디바이스 관리') : __('Device Management');
@endphp

@extends('layouts.app', ['page' => $pageTitle, 'pageSlug' => 'device_management'])

@section('content-fluid')
<!-- header -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-primary d-flex justify-content-between align-items-center p-2"
                style="--header-background-color: #324D65; background-color: var(--header-background-color);">
                <div class="header-hospital-filter-container">
                    <div class="header-hospital-filter-row">
                        <form id="filter-form" method="GET" action="/hospital/device-management"
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
                                        {{ __('장치 코드') }}
                                        @else
                                        {{ __('Device Code') }}
                                        @endif
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="#" data-value="device_code">
                                            @if (config('app.lang') != 'en')
                                            {{ __('장치 코드') }}
                                            @else
                                            {{ __('Device Code') }}
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
                        data-target="#modalHospital">
                        {{ config('app.lang') != 'en' ? __('기기등록') : __('Device Registration') }}
                    </button>
                    <button class="btn btn-primary btn-danger btn-sm" type="button" id="delete-selected">
                        @if (config('app.lang') != 'en')
                        {{ __('삭제') }}
                        @else
                        {{ __('Elimination') }}
                        @endif
                    </button>
                    <button class="btn btn-primary btn-info btn-sm" type="button" id="export-excel"
                        onclick="exportExcel('{{ url('hospital/device-management/export') }}')">
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
                                    {{ config('app.lang') != 'en' ? __('로케이션') : __('Location') }}
                                </th>
                                <th>
                                    {{ config('app.lang') != 'en' ? __('기기코드') : __('Device Code') }}
                                </th>
                                <th>
                                    {{ config('app.lang') != 'en' ? __('사용 상태') : __('Usage Status') }}
                                </th>
                                <th>
                                    {{ config('app.lang') != 'en' ? __('환자명') : __('Patient Name') }}
                                </th>
                                <th>
                                    {{ config('app.lang') != 'en' ? __('기기등록일') : __('Device Registration Date') }}
                                </th>
                                <th>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($datas as $data)
                            <tr>
                                <td><input type="checkbox" class="checkbox-click" value="{{ $data->id }}"></td>
                                <td>{{ $loop->iteration + ($datas->currentPage() - 1) * $datas->perPage() }}</td>
                                <td>{{ $data->detailedLocation ? $data->detailedLocation->location_name : ''}}</td>
                                <td>{{ $data->device_code}}</td>
                                <td>{{ $data->usage_status ? '사용' : '미사용' }}</td>
                                <td>{{ $data->patientHospital ? $data->patientHospital->name : '-'}}</td>
                                <td>{{ $data->date_registration }}</td>
                                <td class="td-actions">
                                    <button class="btn btn-warning btn-sm" type="button"
                                        onclick="viewDetails({{$data->id}})">
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
    <!--  -->

    <!-- form -->
    <div class="col-md-6">
        <div class="card flex-fill d-flex flex-column" style="background-color: #1E3142; color: #FFFFFF;">
            <div class="card-header w-100 text-center" style="background-color: #324D65; color: #FFFFFF;">
                <h5 class="title">
                    {{ config('app.lang') != 'en' ? __('상세정보') : __('Detailed Information'); }}
                </h5>
                <!-- Navbar -->
                <div class="col-md-12 mt-3">
                    <div class="card mb-3" style="background-color: #192734; color: #FFFFFF;">
                        <div class="nav-bar-custom" style="background-color: #192734;">
                            <div class="nav-item active" data-target="deviceInformation">
                                <div class="title">
                                    {{ config('app.lang') != 'en' ? __('디바이스정보') : __('Device Information') }}</div>
                            </div>
                            <div class="nav-item" data-target="targetInformation">
                                <div class="title">
                                    {{ config('app.lang') != 'en' ? __('대상자정보') : __('Target Information') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--  -->
            </div>
            <div class="card-body-custom-1 flex-fill d-flex flex-column justify-content-left align-items-left w-100"
                style="background-color:#1E3142;min-height:448px;">
                <!-- device information -->
                <div class="content-section active" id="deviceInformation">
                    <!-- card header -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <h5 class="title" style="color: #FFFFFF;">
                                    {{ config('app.lang') != 'en' ? __('기본정보') : __('Basic Information') }}
                                </h5>
                            </div>
                        </div>
                    </div>
                    <!--  -->

                    <div class="row">
                        <div class="col-md-12">
                            <div class="patient-management-container">
                                <div class="patient-management-body">
                                    <div class="patient-info-column">
                                        <table class="patient-info-table">
                                            <tbody>
                                                <tr>
                                                    <td>{{ config('app.lang') != 'en' ? __('로케이션') : __('Location') }}
                                                    </td>
                                                    <td style="background-color:#1E3142;color:#FFFFFF"></td>
                                                </tr>
                                                <tr>
                                                    <td>{{ config('app.lang') != 'en' ? __('기기코드') : __('Device Code') }}
                                                    </td>
                                                    <td style="background-color:#1E3142;color:#FFFFFF"></td>
                                                </tr>
                                                <tr>
                                                    <td>{{ config('app.lang') != 'en' ? __('사용 상태') : __('Usage Status') }}
                                                    </td>
                                                    <td style="background-color:#1E3142;color:#FFFFFF"></td>
                                                </tr>
                                                <tr>
                                                    <td>{{ config('app.lang') != 'en' ? __('사용자명') : __('User Name') }}
                                                    </td>
                                                    <td style="background-color:#1E3142;color:#FFFFFF"></td>
                                                </tr>
                                                <tr>
                                                    <td>{{ config('app.lang') != 'en' ? __('기기등록일') : __('Device Registration Date') }}
                                                    </td>
                                                    <td style="background-color:#1E3142;color:#FFFFFF"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--  -->

                <!-- target information -->
                <div class="content-section" id="targetInformation">
                    <!-- card header -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <h5 class="title" style="color: #FFFFFF;">
                                    {{ config('app.lang') != 'en' ? __('기본정보') : __('Target Information') }}
                                </h5>
                            </div>
                        </div>
                    </div>
                    <!--  -->

                    <div class="row">
                        <div class="col-md-12">
                            <div class="patient-management-container">
                                <div class="patient-management-body">
                                    <div class="patient-info-column">
                                        <table class="patient-info-table">
                                            <tbody id="details">
                                                <tr>
                                                    <td>{{ config('app.lang') != 'en' ? __('대상자명') : __('Patient Name') }}
                                                    </td>
                                                    <td style="background-color:#1E3142;color:#FFFFFF"></td>
                                                    <td style="background-color:#324D65;color:#FFFFFF">
                                                        {{ config('app.lang') != 'en' ? __('입원실') : __('Ward') }}</td>
                                                    <td style="background-color:#1E3142;color:#FFFFFF"></td>
                                                </tr>
                                                <tr>
                                                    <td>{{ config('app.lang') != 'en' ? __('등록번호') : __('Registration Number') }}
                                                    </td>
                                                    <td style="background-color:#1E3142;color:#FFFFFF"></td>
                                                    <td style="background-color:#324D65;color:#FFFFFF">
                                                        {{ config('app.lang') != 'en' ? __('주치의') : __('Doctor in Charge') }}
                                                    </td>
                                                    <td style="background-color:#1E3142;color:#FFFFFF"></td>
                                                </tr>
                                                <tr>
                                                    <td>{{ config('app.lang') != 'en' ? __('성별') : __('Gender') }}</td>
                                                    <td style="background-color:#1E3142;color:#FFFFFF"></td>
                                                    <td style="background-color:#324D65;color:#FFFFFF">
                                                        {{ config('app.lang') != 'en' ? __('담당간호사') : __('Nurse in Charge') }}
                                                    </td>
                                                    <td style="background-color:#1E3142;color:#FFFFFF"></td>
                                                </tr>
                                                <tr>
                                                    <td>{{ config('app.lang') != 'en' ? __('나이') : __('Age') }}</td>
                                                    <td style="background-color:#1E3142;color:#FFFFFF"></td>
                                                    <td style="background-color:#324D65;color:#FFFFFF">
                                                        {{ config('app.lang') != 'en' ? __('진단명') : __('Diagnosis') }}
                                                    </td>
                                                    <td style="background-color:#1E3142;color:#FFFFFF"></td>
                                                </tr>
                                                <tr>
                                                    <td>{{ config('app.lang') != 'en' ? __('생년월일') : __('Birthdate') }}
                                                    </td>
                                                    <td style="background-color:#1E3142;color:#FFFFFF"></td>
                                                    <td style="background-color:#324D65;color:#FFFFFF">
                                                        {{ config('app.lang') != 'en' ? __('스마트워치코드') : __('Smartwatch Code') }}
                                                    </td>
                                                    <td style="background-color:#1E3142;color:#FFFFFF"></td>
                                                </tr>
                                                <tr>
                                                    <td>{{ config('app.lang') != 'en' ? __('신장 (cm)') : __('Height (cm)') }}
                                                    </td>
                                                    <td style="background-color:#1E3142;color:#FFFFFF"></td>
                                                    <td style="background-color:#324D65;color:#FFFFFF">
                                                        {{ config('app.lang') != 'en' ? __('연락처') : __('Contact') }}
                                                    </td>
                                                    <td style="background-color:#1E3142;color:#FFFFFF"></td>
                                                </tr>
                                                <tr>
                                                    <td>{{ config('app.lang') != 'en' ? __('체중 (kg)') : __('Weight (kg)') }}
                                                    </td>
                                                    <td style="background-color:#1E3142;color:#FFFFFF"></td>
                                                    <td style="background-color:#324D65;color:#FFFFFF">
                                                        {{ config('app.lang') != 'en' ? __('비상연락처 1') : __('Emergency Contact 1') }}
                                                    </td>
                                                    <td style="background-color:#1E3142;color:#FFFFFF"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--  -->
            </div>
        </div>
    </div>
    <!--  -->
</div>
<!--  -->

<!-- Modal for Add/Edit Account -->
<div class="modal fade" id="modalHospital">
    <div class="modal-dialog modal-lg" style="max-width:800px">
        <div class="modal-content modal-hospital-content">

            <!-- Modal Header -->
            <div class="modal-header"
                style="background-color:#3CBAFF;padding:20px 24px;align-items: center;justify-content: center;position: relative;">
                <h4 class="modal-title" style="color:#FFFFFF">
                    {{ config('app.lang') != 'en' ? __('하위 로케이션 등록') : __('Register a sublocation') }}</h4>
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
                    <label for="location-modal">{{ config('app.lang') != 'en' ? __('로케이션') : __('Location') }}</label>
                    <select id="location-modal" class="required-input">
                        <option>{{ config('app.lang') != 'en' ? __('선택') : __('Select') }}</option>
                        @foreach($locations as $location)
                        <option value="{{$location->id}}">{{ $location->location_name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-hospital-form-row">
                    <label
                        for="device-code-modal">{{ config('app.lang') != 'en' ? __('기기코드') : __('Device Code') }}</label>
                    <input type="text" id="device-code-modal" class="required-input">
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-primary modal-hospital-btn-storage" style="padding:10px 160px"
                    onclick="saveSubLocationModal()">{{ config('app.lang') != 'en' ? __('저장') : __('Save') }}</button>
                <button type="button" class="btn btn-info modal-hospital-btn-cancellation" style="padding:10px 160px"
                    data-dismiss="modal">{{ config('app.lang') != 'en' ? __('취소') : __('Cancel') }}</button>
            </div>

        </div>
    </div>
</div>
<script>
// Select/Deselect all checkboxes
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


    document.getElementById('delete-selected').addEventListener('click', function() {
        deleteSelected();
    });
});

document.addEventListener('DOMContentLoaded', function() {
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
})

function saveSubLocationModal() {
    if (!globalValidateInputForModal('modalHospital')) {
        document.getElementById('error-message-pop-up').innerText =
            `{{ config('app.lang') != 'en' ? __('필수 입력란을 모두 작성해 주세요.') : __('Please fill out all required fields.') }}`
        $('#errorFailedWithMessagePopupModal').modal('show');
        return
    }
    const dateRegistration = document.getElementById('date-registration-modal').value;
    const location_id = document.getElementById('location-modal').value;
    const device_code = document.getElementById('device-code-modal').value;

    fetch('/hospital/device-management', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                registration_date: dateRegistration,
                location_id: location_id,
                device_code: device_code
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                $('#successPopupModal').modal('show');
            } else {
                if (data.message) {
                    document.getElementById('error-message-pop-up').innerText = data.message
                    $('#errorFailedWithMessagePopupModal').modal('show');
                } else {
                    $('#errorFailedPopupModal').modal('show');
                }
            }
        })
        .catch(error => console.error('Error:', error));
}

let lang = `{{config('app.lang') != 'en' ? 'ko' : 'en';}}`

function viewDetails(deviceId) {
    fetch('/hospital/device-management/' + deviceId)
        .then(response => response.json())
        .then(data => {
            const resp = data.data;
            // <td style="background-color:#1E3142;color:#FFFFFF">${resp.top_location.location_name} > ${resp.sub_location.location_name} > ${resp.detailed_location.location_name}</td>

            // Update device information
            const deviceInfoHtml = `
                <tr>
                    <td>${getTranslation('로케이션', 'Location')}</td>
                    <td style="background-color:#1E3142;color:#FFFFFF">${resp.detailed_location ? resp.detailed_location.location_name : '-'}</td>
                </tr>
                <tr>
                    <td>${getTranslation('기기코드', 'Device Code')}</td>
                    <td style="background-color:#1E3142;color:#FFFFFF">${resp.device_code}</td>
                </tr>
                <tr>
                    <td>${getTranslation('사용 상태', 'Usage Status')}</td>
                    <td style="background-color:#1E3142;color:#FFFFFF">${resp.usage_status === 1 ? getTranslation('사용', 'In Use') : getTranslation('미사용', 'Not In Use')}</td>
                </tr>
                <tr>
                    <td>${getTranslation('사용자명', 'User Name')}</td>
                    <td style="background-color:#1E3142;color:#FFFFFF">${resp.user_name || '-'}</td>
                </tr>
                <tr>
                    <td>${getTranslation('기기등록일', 'Device Registration Date')}</td>
                    <td style="background-color:#1E3142;color:#FFFFFF">${resp.date_registration}</td>
                </tr>
            `;
            document.querySelector('#deviceInformation .patient-info-table tbody').innerHTML = deviceInfoHtml;

            // Update target information
            const targetInfoHtml = `
                <tr>
                    <td>${getTranslation('대상자명', 'Patient Name')}</td>
                    <td style="background-color:#1E3142;color:#FFFFFF">${resp.patient_hospital ? resp.patient_hospital.name : '-'}</td>
                    <td style="background-color:#324D65;color:#FFFFFF">${getTranslation('입원실', 'Ward')}</td>
                    <td style="background-color:#1E3142;color:#FFFFFF">${resp.patient_hospital && resp.detailed_location ? resp.detailed_location.location_name : '-'}</td>
                </tr>
                <tr>
                    <td>${getTranslation('등록번호', 'Registration Number')}</td>
                    <td style="background-color:#1E3142;color:#FFFFFF">${resp.patient_hospital ? resp.patient_hospital.patient_code: '-'}</td>
                    <td style="background-color:#324D65;color:#FFFFFF">${getTranslation('주치의', 'Doctor in Charge')}</td>
                    <td style="background-color:#1E3142;color:#FFFFFF">${resp.patient_hospital ? resp.patient_hospital.doctor_in_charge : '-'}</td>
                </tr>
                <tr>
                    <td>${getTranslation('성별', 'Gender')}</td>
                    <td style="background-color:#1E3142;color:#FFFFFF">-</td>
                    <td style="background-color:#324D65;color:#FFFFFF">${getTranslation('담당간호사', 'Nurse in Charge')}</td>
                    <td style="background-color:#1E3142;color:#FFFFFF">${resp.patient_hospital ? resp.patient_hospital.nurse_in_charge: '-'}</td>
                </tr>
                <tr>
                    <td>${getTranslation('나이', 'Age')}</td>
                    <td style="background-color:#1E3142;color:#FFFFFF">${resp.patient_hospital ? resp.patient_hospital.age: '-'}</td>
                    <td style="background-color:#324D65;color:#FFFFFF">${getTranslation('진단명', 'Diagnosis')}</td>
                    <td style="background-color:#1E3142;color:#FFFFFF">${resp.patient_hospital ? resp.patient_hospital.diagnostic_name: '-'}</td>
                </tr>
                <tr>
                    <td>${getTranslation('생년월일', 'Birthdate')}</td>
                    <td style="background-color:#1E3142;color:#FFFFFF">${resp.patient_hospital ? resp.patient_hospital.date_of_birth: '-'}</td>
                    <td style="background-color:#324D65;color:#FFFFFF">${getTranslation('스마트워치코드', 'Smartwatch Code')}</td>
                    <td style="background-color:#1E3142;color:#FFFFFF">${resp.patient_hospital ? resp.patient_hospital.device_code : '-'}</td>
                </tr>
                <tr>
                    <td>${getTranslation('신장 (cm)', 'Height (cm)')}</td>
                    <td style="background-color:#1E3142;color:#FFFFFF">${resp.patient_hospital ? resp.patient_hospital.height_cm: '-'}</td>
                    <td style="background-color:#324D65;color:#FFFFFF">${getTranslation('연락처', 'Contact')}</td>
                    <td style="background-color:#1E3142;color:#FFFFFF">${resp.patient_hospital ? resp.patient_hospital.contact_information: '-'}</td>
                </tr>
                <tr>
                    <td>${getTranslation('체중 (kg)', 'Weight (kg)')}</td>
                    <td style="background-color:#1E3142;color:#FFFFFF">${resp.patient_hospital ? resp.patient_hospital.weight_kg: '-'}</td>
                    <td style="background-color:#324D65;color:#FFFFFF">${getTranslation('비상연락처 1', 'Emergency Contact 1')}</td>
                    <td style="background-color:#1E3142;color:#FFFFFF">${resp.patient_hospital ? resp.patient_hospital.emergency_contact_1: '-'}</td>
                </tr>
            `;
            document.querySelector('#targetInformation .patient-info-table tbody').innerHTML = targetInfoHtml;
        })
        .catch(error => console.error('Error:', error));
}

function getTranslation(ko, en) {
    return lang !== 'en' ? ko : en;
}

document.getElementById('delete-selected').addEventListener('click', function() {
    const selectedIds = Array.from(document.querySelectorAll('.checkbox-click:checked')).map(cb => cb
        .value);

    if (selectedIds.length > 0) {
        // Show the confirmation modal
        $('#deletedConfirmationPopUpModal').modal('show');

        // Add a one-time event listener for the confirm button
        document.getElementById('confirmed').addEventListener('click', function confirmHandler() {
            fetch('/hospital/device-management', {
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
                    if (data.success) {
                        $('#successPopupModal').modal('show');
                    } else {
                        if (data.message) {
                            document.getElementById('error-message-pop-up').innerText = data.message
                            $('#errorFailedWithMessagePopupModal').modal('show');
                        } else {
                            $('#errorFailedPopupModal').modal('show');
                        }
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