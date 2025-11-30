@php
$pageTitle = config('app.lang') != 'en' ? __('의료진관리') : __('staff-management');
@endphp

@extends('layouts.app', ['page' => $pageTitle, 'pageSlug' => 'staff_management'])

@section('content-fluid')
<!-- header -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-primary d-flex justify-content-between align-items-center p-2"
                style="--header-background-color: #324D65; background-color: var(--header-background-color);">
                <div class="header-hospital-filter-container">
                    <div class="header-hospital-filter-row">
                        <form id="filter-form" method="GET" action="/hospital/settings/staff-management"
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
                                        {{ __('의료진명') }}
                                        @else
                                        {{ __('Medical Staff Name') }}
                                        @endif
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="#" data-value="name">
                                            @if (config('app.lang') != 'en')
                                            {{ __('의료진명') }}
                                            @else
                                            {{ __('Medical Staff Name') }}
                                            @endif
                                        </a>
                                        <a class="dropdown-item" href="#" data-value="job_title">
                                            @if (config('app.lang') != 'en')
                                            {{ __('직무구분') }}
                                            @else
                                            {{ __('Job Title') }}
                                            @endif
                                        </a>
                                        <a class="dropdown-item" href="#" data-value="employee_number">
                                            @if (config('app.lang') != 'en')
                                            {{ __('사번') }}
                                            @else
                                            {{ __('Employee Number') }}
                                            @endif
                                        </a>
                                        <a class="dropdown-item" href="#" data-value="gender">
                                            @if (config('app.lang') != 'en')
                                            {{ __('성별') }}
                                            @else
                                            {{ __('Gender') }}
                                            @endif
                                        </a>
                                        <a class="dropdown-item" href="#" data-value="age">
                                            @if (config('app.lang') != 'en')
                                            {{ __('나이') }}
                                            @else
                                            {{ __('Age') }}
                                            @endif
                                        </a>
                                        <a class="dropdown-item" href="#" data-value="department">
                                            @if (config('app.lang') != 'en')
                                            {{ __('소속과목') }}
                                            @else
                                            {{ __('Department') }}
                                            @endif
                                        </a>
                                        <a class="dropdown-item" href="#" data-value="contact1">
                                            @if (config('app.lang') != 'en')
                                            {{ __('연락처') }}
                                            @else
                                            {{ __('Contact') }}
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
                    <button class="btn btn-primary btn-sm" type="button" data-toggle="modal" onclick="create()">
                        {{ config('app.lang') != 'en' ? __('신규등록') : __('Staff Registration') }}
                    </button>
                    <button class="btn btn-primary btn-danger btn-sm" type="button" id="delete-selected">
                        @if (config('app.lang') != 'en')
                        {{ __('삭제') }}
                        @else
                        {{ __('Elimination') }}
                        @endif
                    </button>
                    <button class="btn btn-primary btn-info btn-sm" type="button" id="export-excel"
                        onclick="exportExcel('{{ url('hospital/settings/staff-management/export') }}')">
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
                                    {{ config('app.lang') != 'en' ? __('의료진명') : __('Medical Staff Name') }}
                                </th>
                                <th>
                                    {{ config('app.lang') != 'en' ? __('직무구분') : __('Job Title') }}
                                </th>
                                <th>
                                    {{ config('app.lang') != 'en' ? __('사번') : __('Employee Number') }}
                                </th>
                                <th>
                                    {{ config('app.lang') != 'en' ? __('성별') : __('Gender') }}
                                </th>
                                <th>
                                    {{ config('app.lang') != 'en' ? __('나이') : __('Age') }}
                                </th>
                                <th>
                                    {{ config('app.lang') != 'en' ? __('소속과목') : __('Department') }}
                                </th>
                                <th>
                                    {{ config('app.lang') != 'en' ? __('연락처') : __('Contact') }}
                                </th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($staffs as $staff)
                            <tr>
                                <td><input type="checkbox" class="account-checkbox" value="{{ $staff->id }}"></td>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $staff->name }}</td>
                                <td>{{ $staff->job_title }}</td>
                                <td>{{ $staff->employee_number }}</td>
                                <td>{{ $staff->gender }}</td>
                                <td>{{ $staff->age }}</td>
                                <td>{{ $staff->department }}</td>
                                <td>{{ $staff->contact1 }}</td>
                                <td class="td-actions">
                                    <button class="btn btn-warning btn-sm" type="button"
                                        onclick="viewDetails({{ $staff->id }})">
                                        {{ config('app.lang') != 'en' ? __('세부 사항') : __('Detail') }}
                                    </button>
                                </td>
                                <td class="td-actions">
                                    <button class="btn btn-warning btn-sm" type="button"
                                        onclick="edit({{ $staff->id }})">
                                        {{ config('app.lang') != 'en' ? __('편집') : __('Edit') }}
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center">
                    {{ $staffs->links('layouts.pagination') }}
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
            </div>
            <div class="card-body-custom-1 flex-fill d-flex flex-column justify-content-left align-items-left w-100"
                style="background-color:#1E3142;min-height:448px;">
                <!-- detailed information -->
                <div class="content-section active" id="selected-user-form" style="display:none">
                    <!-- card header -->
                    <div class="row">
                        <div class="col-md-2">
                            <div class="profile-picture" style="width:80px;height:80px;margin-left:20px">
                                <img src="{{ asset('black') }}/img/default-avatar.png" alt="Profile" class="profile-img"
                                    id="profileImage">
                            </div>
                        </div>
                        <div class="col-10">
                            <div class="d-flex justify-content-between" style="margin-top:35px">
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
                                                    <td>{{ config('app.lang') != 'en' ? __('이름') : __('Name') }}</td>
                                                    <td id="name-detail" style="background-color:#1E3142;color:#FFFFFF">
                                                    </td>
                                                    <td style="background-color:#324D65;color:#FFFFFF">
                                                        {{ config('app.lang') != 'en' ? __('나이') : __('Age') }}</td>
                                                    <td id="age-detail" style="background-color:#1E3142;color:#FFFFFF">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{ config('app.lang') != 'en' ? __('직무구분') : __('Job Title') }}
                                                    </td>
                                                    <td id="job_title-detail"
                                                        style="background-color:#1E3142;color:#FFFFFF"></td>
                                                    <td style="background-color:#324D65;color:#FFFFFF">
                                                        {{ config('app.lang') != 'en' ? __('성별') : __('Gender') }}</td>
                                                    <td id="gender-detail"
                                                        style="background-color:#1E3142;color:#FFFFFF"></td>
                                                </tr>
                                                <tr>
                                                    <td>{{ config('app.lang') != 'en' ? __('소속과목') : __('Department') }}
                                                    </td>
                                                    <td id="department-detail"
                                                        style="background-color:#1E3142;color:#FFFFFF"></td>
                                                    <td style="background-color:#324D65;color:#FFFFFF">
                                                        {{ config('app.lang') != 'en' ? __('주소') : __('Address') }}</td>
                                                    <td id="address-detail"
                                                        style="background-color:#1E3142;color:#FFFFFF"></td>
                                                </tr>
                                                <tr>
                                                    <td>{{ config('app.lang') != 'en' ? __('직위') : __('Position') }}
                                                    </td>
                                                    <td id="position-detail"
                                                        style="background-color:#1E3142;color:#FFFFFF"></td>
                                                    <td style="background-color:#324D65;color:#FFFFFF">
                                                        {{ config('app.lang') != 'en' ? __('연락처1') : __('Contact 1') }}
                                                    </td>
                                                    <td id="contact1-detail"
                                                        style="background-color:#1E3142;color:#FFFFFF"></td>
                                                </tr>
                                                <tr>
                                                    <td>{{ config('app.lang') != 'en' ? __('사번') : __('Employee Number') }}
                                                    </td>
                                                    <td id="employee_number-detail"
                                                        style="background-color:#1E3142;color:#FFFFFF"></td>
                                                    <td style="background-color:#324D65;color:#FFFFFF">
                                                        {{ config('app.lang') != 'en' ? __('연락처2') : __('Contact 2') }}
                                                    </td>
                                                    <td id="contact2-detail"
                                                        style="background-color:#1E3142;color:#FFFFFF"></td>
                                                </tr>
                                                <tr>
                                                    <td>{{ config('app.lang') != 'en' ? __('생년월일') : __('Birthdate') }}
                                                    </td>
                                                    <td id="birthdate-detail"
                                                        style="background-color:#1E3142;color:#FFFFFF"></td>
                                                    <td style="background-color:#324D65;color:#FFFFFF">
                                                        {{ config('app.lang') != 'en' ? __('이메일') : __('Email') }}</td>
                                                    <td id="email-detail"
                                                        style="background-color:#1E3142;color:#FFFFFF"></td>
                                                </tr>
                                                <tr>
                                                    <td>{{ config('app.lang') != 'en' ? __('아이디') : __('ID') }}
                                                    </td>
                                                    <td id="id-detail" style="background-color:#1E3142;color:#FFFFFF">
                                                    </td>
                                                    <td style="background-color:#324D65;color:#FFFFFF">
                                                        {{ config('app.lang') != 'en' ? __('비밀번호') : __('Password') }}
                                                    </td>
                                                    <td id="detail-password"
                                                        style="background-color:#1E3142;color:#FFFFFF"> **********</td>
                                                </tr>
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
                <!--  -->
            </div>
        </div>
    </div>
    <!--  -->
</div>


<!-- Modal for Add/Edit Account -->
<div class="modal fade" id="modalHospital">
    <div class="modal-dialog modal-lg"
        style="max-width:800px;display: flex;align-items: flex-start; justify-content: center; min-height: 120vh;">
        <div class="modal-content modal-hospital-content">

            <!-- Modal Header -->
            <div class="modal-header"
                style="background-color:#3CBAFF;padding:20px 24px;align-items: center;justify-content: center;position: relative;">
                <h4 class="modal-title" style="color:#FFFFFF">
                    {{ config('app.lang') != 'en' ? __('의료진 등록') : __('Staff Registration') }}</h4>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <div class="text-center" style="margin-bottom:20px">
                    <h4 class="modal-title" style="color:#FFFFFF">
                        {{ config('app.lang') != 'en' ? __('기본정보') : __('Basic Information') }}</h4>
                </div>
                <div class="modal-hospital-form-row">
                    <input type="hidden" id="data_id" value="">
                    <label
                        for="registration-date-modal">{{ config('app.lang') != 'en' ? __('입사일자') : __('Registration Date') }}</label>
                    <input type="text" id="registration-date-modal" readonly>
                    <label for="birthdate-modal">{{ config('app.lang') != 'en' ? __('생년월일') : __('Birthdate') }}</label>
                    <input type="date" id="birthdate-modal">
                </div>
                <div class="modal-hospital-form-row">
                    <label
                        for="photo-modal">{{ config('app.lang') != 'en' ? __('사진 (jpeg, png)') : __('Photo (jpeg, png)') }}</label>
                    <input type="file" id="photo-modal" accept="image/jpeg, image/png">
                    <label for="age-modal">{{ config('app.lang') != 'en' ? __('나이') : __('Age') }}</label>
                    <input type="number" id="age-modal">
                </div>
                <div class="modal-hospital-form-row">
                    <label
                        for="name-modal">{{ config('app.lang') != 'en' ? __('의료진명') : __('Medical Staff Name') }}</label>
                    <input type="text" id="name-modal">
                    <label for="gender-modal">{{ config('app.lang') != 'en' ? __('성별') : __('Gender') }}</label>
                    <select id="gender-modal">
                        <option value="M">{{ config('app.lang') != 'en' ? __('남성') : __('Male') }}</option>
                        <option value="F">{{ config('app.lang') != 'en' ? __('여성') : __('Female') }}</option>
                    </select>
                </div>
                <div class="modal-hospital-form-row">
                    <label for="job_title-modal">{{ config('app.lang') != 'en' ? __('직무구분') : __('Job Title') }}</label>
                    <select id="job_title-modal">
                        <option>{{ config('app.lang') != 'en' ? __('선택') : __('Select') }}</option>
                        @foreach($jobTitles as $jobTitle)
                        <option value="{{ $jobTitle }}">{{ $jobTitle }}</option>
                        @endforeach
                    </select>
                    <label
                        for="department-modal">{{ config('app.lang') != 'en' ? __('소속과목') : __('Department') }}</label>
                    <select id="department-modal">
                        <option>{{ config('app.lang') != 'en' ? __('선택') : __('Select') }}</option>
                        @foreach($departments as $department)
                        <option value="{{ $department }}">{{ $department }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-hospital-form-row">
                    <label for="position-modal">{{ config('app.lang') != 'en' ? __('직위') : __('Position') }}</label>
                    <select id="position-modal">
                        <option>{{ config('app.lang') != 'en' ? __('선택') : __('Select') }}</option>
                        <option value="직원">{{ config('app.lang') != 'en' ? __('직원') : __('Staff') }}</option>
                        <option value="의사">{{ config('app.lang') != 'en' ? __('의사') : __('doctor') }}</option>
                        <option value="간호사">{{ config('app.lang') != 'en' ? __('간호사') : __('nurse') }}</option>
                    </select>
                    <label for="contact1-modal">{{ config('app.lang') != 'en' ? __('연락처1') : __('Contact 1') }}</label>
                    <input type="text" id="contact1-modal">
                </div>
                <div class="modal-hospital-form-row">
                    <label
                        for="employee_number-modal">{{ config('app.lang') != 'en' ? __('사번') : __('Employee Number') }}</label>
                    <input type="text" id="employee_number-modal">
                    <label for="contact2-modal">{{ config('app.lang') != 'en' ? __('연락처2') : __('Contact 2') }}</label>
                    <input type="text" id="contact2-modal">
                </div>
                <div class="modal-hospital-form-row">
                    <label for="id-modal">{{ config('app.lang') != 'en' ? __('아이디') : __('ID') }}</label>
                    <input type="text" id="id-modal">
                    <label for="password-modal">{{ config('app.lang') != 'en' ? __('비밀번호') : __('Password') }}</label>
                    <input type="password" id="password-modal">
                </div>
                <div class="modal-hospital-form-row wide">
                    <label for="email-modal">{{ config('app.lang') != 'en' ? __('이메일') : __('Email') }}</label>
                    <input type="email" id="email-modal" style="width: calc(100% - 40px);">
                </div>
                <div class="modal-hospital-form-row wide">
                    <label for="address-modal">{{ config('app.lang') != 'en' ? __('주소') : __('Address') }}</label>
                    <input type="text" id="address-modal" style="width: calc(100% - 40px);">
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-primary modal-hospital-btn-storage" style="padding:10px 160px"
                    onclick="saveStaffModal()">{{ config('app.lang') != 'en' ? __('저장') : __('Save') }}</button>
                <button type="button" class="btn btn-info modal-hospital-btn-cancellation" style="padding:10px 160px"
                    data-dismiss="modal">{{ config('app.lang') != 'en' ? __('취소') : __('Cancel') }}</button>
            </div>

        </div>
    </div>
</div>
<!--  -->
<script>
// Select/Deselect all checkboxes
document.getElementById('select-all').addEventListener('click', function() {
    document.querySelectorAll('.account-checkbox').forEach(checkbox => checkbox.checked = true);
});

document.getElementById('deselect-all').addEventListener('click', function() {
    document.querySelectorAll('.account-checkbox').forEach(checkbox => checkbox.checked = false);
});
document.addEventListener('DOMContentLoaded', function() {
    var dateRegistrationInput = document.getElementById('registration-date-modal');
    var today = new Date();
    var day = String(today.getDate()).padStart(2, '0');
    var month = String(today.getMonth() + 1).padStart(2, '0'); // January is 0!
    var year = today.getFullYear();
    dateRegistrationInput.value = year + '-' + month + '-' + day;


    document.getElementById('delete-selected').addEventListener('click', function() {
        deleteSelected();
    });
});
document.addEventListener("DOMContentLoaded", function() {
    const fileUpload = document.getElementById('fileUpload');
    fileUpload.addEventListener('change', function(event) {
        const output = document.getElementById('profileImage');
        output.src = URL.createObjectURL(event.target.files[0]);
        output.onload = function() {
            URL.revokeObjectURL(output.src) // free memory
        }
    });
});

function viewDetails(id) {
    fetch(`/hospital/settings/staff-management/${id}`)
        .then(response => response.json())
        .then(data => {
            var account = data.account;
            getFile('{{ asset("storage") }}/' + data.photo_profile, document.getElementById('profileImage'))
            document.getElementById('id-detail').innerText = data.user_id;
            document.getElementById('name-detail').innerText = data.name;
            document.getElementById('age-detail').innerText = data.age;
            document.getElementById('job_title-detail').innerText = data.job_title;
            document.getElementById('gender-detail').innerText = data.gender;
            document.getElementById('department-detail').innerText = data.department;
            document.getElementById('address-detail').innerText = data.address;
            document.getElementById('position-detail').innerText = data.position;
            document.getElementById('contact1-detail').innerText = data.contact1;
            document.getElementById('employee_number-detail').innerText = data.employee_number;
            document.getElementById('contact2-detail').innerText = data.contact2;
            document.getElementById('email-detail').innerText = data.email;
            document.getElementById('birthdate-detail').innerText = data.birthdate;


            document.getElementById('selected-user-form').style.display = 'block';
            document.getElementById('not-selected-user-form').style.display = 'none';
        })
        .catch(error => console.error('Error fetching account details:', error));
}

function clearFormFields() {
    // Collect form data
    // document.getElementById('registration-date-modal').value = data.registration_date;
    document.getElementById('birthdate-modal').value = '';
    // document.getElementById('photo-modal').files[0];
    document.getElementById('age-modal').value = '';
    document.getElementById('name-modal').value = '';
    document.getElementById('id-modal').value = '';
    document.getElementById('contact1-modal').value = '';
    document.getElementById('employee_number-modal').value = '';
    document.getElementById('contact2-modal').value = '';
    document.getElementById('address-modal').value = '';
    document.getElementById('email-modal').value = '';
    document.getElementById('password-modal').value = '';
    document.getElementById('data_id').value = '';

    setSelectValue('job_title-modal', '');
    setSelectValue('gender-modal', '');
    setSelectValue('department-modal', '');
    setSelectValue('position-modal', '');
}

function create() {
    clearFormFields()
    $('#modalHospital').modal('show');
}

function edit(id) {
    clearFormFields()
    fetch(`/hospital/settings/staff-management/${id}`)
        .then(response => response.json())
        .then(data => {
            const patient = data.data;

            // Collect form data
            // document.getElementById('registration-date-modal').value = data.registration_date;
            document.getElementById('birthdate-modal').value = data.birthdate;
            // document.getElementById('photo-modal').files[0];
            document.getElementById('age-modal').value = data.age;
            document.getElementById('name-modal').value = data.name;
            document.getElementById('id-modal').value = data.user_id;
            document.getElementById('contact1-modal').value = data.contact1;
            document.getElementById('employee_number-modal').value = data.employee_number;
            document.getElementById('contact2-modal').value = data.contact2;
            document.getElementById('address-modal').value = data.address;
            document.getElementById('email-modal').value = data.email;
            document.getElementById('password-modal').value = '**********';

            setSelectValue('job_title-modal', data.job_title);
            setSelectValue('gender-modal', data.gender);
            setSelectValue('department-modal', data.department);
            setSelectValue('position-modal', data.position);

            document.getElementById('data_id').value = id;

            $('#modalHospital').modal('show');
        })
        .catch(error => console.error('Error fetching data:', error));
}

function saveStaffModal() {
    // Collect form data
    const registrationDate = document.getElementById('registration-date-modal').value;
    const birthdate = document.getElementById('birthdate-modal').value;
    const photo = document.getElementById('photo-modal').files[0];
    const age = document.getElementById('age-modal').value;
    const name = document.getElementById('name-modal').value;
    const userId = document.getElementById('id-modal').value;
    const gender = document.getElementById('gender-modal').value;
    const jobTitle = document.getElementById('job_title-modal').value;
    const department = document.getElementById('department-modal').value;
    const position = document.getElementById('position-modal').value;
    const contact1 = document.getElementById('contact1-modal').value;
    const employeeNumber = document.getElementById('employee_number-modal').value;
    const contact2 = document.getElementById('contact2-modal').value;
    const address = document.getElementById('address-modal').value;
    const email = document.getElementById('email-modal').value;
    const password = document.getElementById('password-modal').value;

    // Create form data object
    const formData = new FormData();
    formData.append('registration_date', registrationDate);
    formData.append('birthdate', birthdate);
    formData.append('photo', photo);
    formData.append('age', age);
    formData.append('name', name);
    formData.append('gender', gender);
    formData.append('job_title', jobTitle);
    formData.append('department', department);
    formData.append('position', position);
    formData.append('contact1', contact1);
    formData.append('employee_number', employeeNumber);
    formData.append('contact2', contact2);
    formData.append('address', address);
    formData.append('email', email);
    formData.append('user_id', userId);
    formData.append('password', password);

    const dataID = document.getElementById('data_id').value;

    let url = '/hospital/settings/staff-management'
    let method = 'POST'
    if (dataID) {
        url = url + '/update/' + dataID
    }
    fetch(url, {
            method: method,
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}' // Add CSRF token if required by your server
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Handle success response
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
        .catch(error => console.error('Error saving staff:', error));
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.classList.remove('show');
    modal.style.display = 'none';
    document.body.classList.remove('modal-open');
    const backdrop = document.querySelector('.modal-backdrop');
    if (backdrop) {
        backdrop.parentNode.removeChild(backdrop);
    }
}

function deleteSelected() {
    var selectedIds = Array.from(document.querySelectorAll('.account-checkbox:checked')).map(cb => cb.value);
    if (selectedIds.length > 0) {
        // Show the confirmation modal
        $('#deletedConfirmationPopUpModal').modal('show');

        // Add a one-time event listener for the confirm button
        document.getElementById('confirmed').addEventListener('click', function confirmHandler() {
            fetch('/hospital/settings/staff-management', {
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
                        $('#successDeletedPopupModal').modal('show');
                    } else {
                        $('#errorFailedPopupModal').modal('show');
                    }

                })
                .catch(error => console.error('Error deleting accounts:', error))
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

}


document.addEventListener("DOMContentLoaded", function() {
    const birthdateInput = document.getElementById("birthdate-modal");
    const ageInput = document.getElementById("age-modal");

    birthdateInput.addEventListener("change", function() {
        const birthdate = new Date(this.value);
        const today = new Date();
        let age = today.getFullYear() - birthdate.getFullYear();
        const monthDiff = today.getMonth() - birthdate.getMonth();
        const dayDiff = today.getDate() - birthdate.getDate();

        // Adjust age if the birthday has not yet occurred this year
        if (monthDiff < 0 || (monthDiff === 0 && dayDiff < 0)) {
            age--;
        }

        ageInput.value = age > 0 ? age : 0;
    });
});
</script>
@endsection