@php
$pageTitle = config('app.lang') != 'en' ? __('환자호출이력') : __('patient-calls');
@endphp

@extends('layouts.app', ['page' => $pageTitle, 'pageSlug' => 'patient_calls'])

@section('content-fluid')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-primary d-flex justify-content-between align-items-center p-2"
                style="--header-background-color: #324D65; background-color: var(--header-background-color);">
                <div class="header-hospital-filter-container">
                    <div class="header-hospital-filter-row">
                        <form id="filter-form" method="GET" action="/hospital/history/patient-calls"
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
                                        {{ __('대상자명') }}
                                        @else
                                        {{ __('Patient Name') }}
                                        @endif
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="#" data-value="name">
                                            @if (config('app.lang') != 'en')
                                            {{ __('대상자명') }}
                                            @else
                                            {{ __('Patient Name') }}
                                            @endif
                                        </a>
                                        <a class="dropdown-item" href="#" data-value="alarm_time">
                                            @if (config('app.lang') != 'en')
                                            {{ __('알림 발생 일시') }}
                                            @else
                                            {{ __('Alarm Occurrence Time') }}
                                            @endif
                                        </a>
                                        <a class="dropdown-item" href="#" data-value="alarm_details">
                                            @if (config('app.lang') != 'en')
                                            {{ __('알림 상세') }}
                                            @else
                                            {{ __('Alarm Details') }}
                                            @endif
                                        </a>
                                        <a class="dropdown-item" href="#" data-value="confirmation_time">
                                            @if (config('app.lang') != 'en')
                                            {{ __('알림 확인 일시') }}
                                            @else
                                            {{ __('Alarm Confirmation Time') }}
                                            @endif
                                        </a>
                                        <a class="dropdown-item" href="#" data-value="ward">
                                            @if (config('app.lang') != 'en')
                                            {{ __('입원실') }}
                                            @else
                                            {{ __('Ward') }}
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
                    <button class="btn btn-primary btn-danger btn-sm" type="button" id="delete-selected">
                        @if (config('app.lang') != 'en')
                        {{ __('삭제') }}
                        @else
                        {{ __('Elimination') }}
                        @endif
                    </button>
                    <button class="btn btn-primary btn-info btn-sm" type="button" id="export-excel"
                        onclick="exportExcel('{{ url('/hospital/history/patient-calls/export') }}')">
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
                                    {{ config('app.lang') != 'en' ? __('대상자명') : __('Patient Name') }}
                                </th>
                                <th>
                                    {{ config('app.lang') != 'en' ? __('알림 발생 일시') : __('Alarm Occurrence Time') }}
                                </th>
                                <th>
                                    {{ config('app.lang') != 'en' ? __('알림 상세') : __('Alarm Details') }}
                                </th>
                                <th>
                                    {{ config('app.lang') != 'en' ? __('알림 확인 일시') : __('Alarm Confirmation Time') }}
                                </th>
                                <th>
                                    {{ config('app.lang') != 'en' ? __('입원실') : __('Ward') }}
                                </th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($datas as $data)
                            <tr>
                                <td><input type="checkbox" class="checkbox-click" value="{{ $data->id }}"></td>
                                <td>{{ $loop->iteration + ($datas->currentPage() - 1) * $datas->perPage() }}</td>
                                <td>{{ $data->patient_name }}</td>
                                <td>{{ $data->call_occurrence_time }}</td>
                                <td>{{ $data->call_details }}</td>
                                <td>{{ $data->call_confirmation_time }}</td>
                                <td>{{ $data->ward }}</td>
                                <td class="td-actions">
                                    <button class="btn btn-warning btn-sm" type="button"
                                        onclick="viewDetails('{{$data->id}}' , '{{$data->patient_id}}')">
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
                    {{ config('app.lang') != 'en' ? __('상세정보') : __('Detailed Information') }}
                </h5>
                <!-- Navbar -->
                <div class="col-md-12 mt-3">
                    <div class="card mb-3" style="background-color: #192734; color: #FFFFFF;">
                        <div class="nav-bar-custom" style="background-color: #192734;">
                            <div class="nav-item active" data-target="emergencyAlertInfo">
                                <div class="title">
                                    {{ config('app.lang') != 'en' ? __('긴급알림정보') : __('Emergency Alert Information') }}
                                </div>
                            </div>
                            <div class="nav-item" data-target="patientInfo">
                                <div class="title">
                                    {{ config('app.lang') != 'en' ? __('대상자정보') : __('Patient Information') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--  -->
            </div>
            <div class="card-body-custom-1 flex-fill d-flex flex-column justify-content-left align-items-left w-100"
                style="background-color:#1E3142;min-height:448px;">
                <!-- emergency alert information -->
                <div class="content-section active" id="emergencyAlertInfo">
                    <!-- card header -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <h5 class="title" style="color: #FFFFFF;">
                                    {{ config('app.lang') != 'en' ? __('기본정보') : __('Basic Information') }}
                                </h5>
                                <div>
                                    <button class="btn btn-info btn-sm" type="button" data-toggle="modal"
                                        data-target="#emergencyHistoryModal">
                                        {{ config('app.lang') != 'en' ? __('긴급알림이력') : __('Emergency notification history') }}
                                    </button>
                                    <button class="btn btn-info btn-sm" type="button" data-toggle="modal"
                                        data-target="#nurseCallHistoryModal">
                                        {{ config('app.lang') != 'en' ? __('간호사호출이력') : __('Nurse call history') }}
                                    </button>
                                </div>
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
                                                    <td>{{ config('app.lang') != 'en' ? __('알림 종류') : __('Alert Type') }}
                                                    </td>
                                                    <td style="background-color:#1E3142;color:#FFFFFF"></td>
                                                    <td style="background-color:#324D65;color:#FFFFFF">
                                                        {{ config('app.lang') != 'en' ? __('알림 상세') : __('Alert Details') }}
                                                    </td>
                                                    <td style="background-color:#1E3142;color:#FFFFFF"></td>
                                                </tr>
                                                <tr>
                                                    <td>{{ config('app.lang') != 'en' ? __('알림 발생시간') : __('Alert Occurrence Time') }}
                                                    </td>
                                                    <td style="background-color:#1E3142;color:#FFFFFF"></td>
                                                    <td style="background-color:#324D65;color:#FFFFFF">
                                                        {{ config('app.lang') != 'en' ? __('환자분 성명') : __('Patient Name') }}
                                                    </td>
                                                    <td style="background-color:#1E3142;color:#FFFFFF"></td>
                                                </tr>
                                                <tr>
                                                    <td style="background-color:#324D65;color:#FFFFFF">
                                                        {{ config('app.lang') != 'en' ? __('병실호수') : __('Ward Number') }}
                                                    </td>
                                                    <td style="background-color:#1E3142;color:#FFFFFF"></td>
                                                    <td style="background-color:#324D65;color:#FFFFFF">
                                                        {{ config('app.lang') != 'en' ? __('병실호수') : __('Ward Number') }}
                                                    </td>
                                                    <td style="background-color:#1E3142;color:#FFFFFF"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <table class="patient-info-table mt-3" id="doctor-info">
                                            <tbody>
                                                <tr>
                                                    <td>{{ config('app.lang') != 'en' ? __('주치의') : __('Doctor in Charge') }}
                                                    </td>
                                                    <td style="background-color:#1E3142;color:#FFFFFF"></td>
                                                </tr>
                                                <tr>
                                                    <td>{{ config('app.lang') != 'en' ? __('담당간호사') : __('Nurse in Charge') }}
                                                    </td>
                                                    <td style="background-color:#1E3142;color:#FFFFFF"></td>
                                                </tr>
                                                <tr>
                                                    <td>{{ config('app.lang') != 'en' ? __('조치의료인') : __('Medical Staff') }}
                                                    </td>
                                                    <td style="background-color:#1E3142;color:#FFFFFF"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div class="form-group mt-3">
                                            <label
                                                for="reason">{{ config('app.lang') != 'en' ? __('원인 및 조치내용 (서술)') : __('Cause and Actions (Description)') }}</label>
                                            <textarea class="form-control" id="reason" rows="5"
                                                style="background-color:#1E3142;color:#FFFFFF;"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--  -->

                <!-- target information -->
                <div class="content-section" id="patientInfo">
                    <!-- card header -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <h5 class="title" style="color: #FFFFFF;">
                                    {{ config('app.lang') != 'en' ? __('기본정보') : __('Basic Information') }}
                                </h5>
                                <div>
                                    <button class="btn btn-info btn-sm" type="button" data-toggle="modal"
                                        data-target="#emergencyHistoryModal">
                                        {{ config('app.lang') != 'en' ? __('긴급알림이력') : __('Emergency notification history') }}
                                    </button>
                                    <button class="btn btn-info btn-sm" type="button" data-toggle="modal"
                                        data-target="#nurseCallHistoryModal">
                                        {{ config('app.lang') != 'en' ? __('간호사호출이력') : __('Nurse call history') }}
                                    </button>
                                </div>
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
                                                    <td>{{ config('app.lang') != 'en' ? __('대상자명') : __('Patient Name') }}
                                                    </td>
                                                    <td style="background-color:#1E3142;color:#FFFFFF"></td>
                                                    <td style="background-color:#324D65;color:#FFFFFF">
                                                        {{ config('app.lang') != 'en' ? __('입원실') : __('Ward') }}</td>
                                                    <td style="background-color:#1E3142;color:#FFFFFF"></td>
                                                </tr>
                                                <tr>
                                                    <td>{{ config('app.lang') != 'en' ? __('등록번호') : __('Patient Code') }}
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
                                                    <td>{{ config('app.lang') != 'en' ? __('생년월일') : __('Date of Birth') }}
                                                    </td>
                                                    <td style="background-color:#1E3142;color:#FFFFFF"></td>
                                                    <td style="background-color:#324D65;color:#FFFFFF">
                                                        {{ config('app.lang') != 'en' ? __('스마트워치코드') : __('Smartwatch Code') }}
                                                    </td>
                                                    <td style="background-color:#1E3142;color:#FFFFFF"></td>
                                                </tr>
                                                <tr>
                                                    <td>{{ config('app.lang') != 'en' ? __('신장(cm)') : __('Height (cm)') }}
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

<!-- modal emergency history -->
<div class="modal fade" id="emergencyHistoryModal">
    <div class="modal-dialog modal-lg"
        style="max-width:800px;display: flex;align-items: flex-start; justify-content: center; min-height: 100vh;">
        <div class="modal-content modal-hospital-content">
            <!-- Modal Header -->
            <div class="modal-header"
                style="background-color:#3CBAFF;padding:20px 24px;align-items: center;justify-content: center;position: relative;">
                <h4 class="modal-title" style="color:#FFFFFF">
                    {{ config('app.lang') != 'en' ? __('긴급 알림 이력') : __('Emergency Alert History') }}
                </h4>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <div class="styled-table-container">
                    <table class="styled-table">
                        <thead>
                            <tr>
                                <th class="text-center" style="background-color:#111A23;border:none;">
                                    {{ config('app.lang') != 'en' ? __('번호') : __('Number') }}
                                </th>
                                <th style="background-color:#111A23;border:none;">
                                    {{ config('app.lang') != 'en' ? __('알림발생일시') : __('Alert Date') }}
                                </th>
                                <th style="background-color:#111A23;border:none;">
                                    {{ config('app.lang') != 'en' ? __('알림 상세') : __('Alert Detail') }}
                                </th>
                                <th style="background-color:#111A23;border:none;">
                                    {{ config('app.lang') != 'en' ? __('알림확인일시') : __('Alert Confirmation Date') }}
                                </th>
                                <th style="background-color:#111A23;border:none;">
                                    {{ config('app.lang') != 'en' ? __('조치의뢰인') : __('Requested by') }}
                                </th>
                                <th style="background-color:#111A23;border:none;">
                                    {{ config('app.lang') != 'en' ? __('조치완료일시') : __('Completion Date') }}
                                </th>
                                <th style="background-color:#111A23;border:none;">
                                    {{ config('app.lang') != 'en' ? __('조치내용') : __('Action Details') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody id="table-body">
                            <!-- Data will be injected here -->
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center" id="pagination-container">
                    <!-- Pagination will be injected here -->
                </div>
            </div>
        </div>
    </div>
</div>
<!--  -->

<!-- modal detail emergency history -->
<div class="modal fade" id="detailEmergencyHistoryModal">
    <div class="modal-dialog modal-lg details-emergency-pop-up-modal-dialog"
        style="max-width:800px;display: flex;align-items: flex-start; justify-content: center; min-height: 100vh;">
        <div class="modal-content details-emergency-pop-up-modal-content">
            <!-- Modal Header -->
            <!-- <div class="modal-header details-emergency-pop-up-modal-header">
                <h4 class="modal-title">
                    {{ config('app.lang') != 'en' ? __('조치내용') : __('Action Details') }}
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div> -->

            <!-- Modal Body -->
            <div class="details-emergency-pop-up-styled-details-container" id="details-container">
                <!-- Header Fields -->
                <table class="details-emergency-pop-up-table">
                    <tr>
                        <td>{{ config('app.lang') != 'en' ? __('주치의') : __('Primary Doctor') }}</td>
                        <td><input type="text" id="primary-doctor" class="details-emergency-pop-up-input" readonly>
                        </td>
                        <td>{{ config('app.lang') != 'en' ? __('담당간호사') : __('Charge Nurse') }}</td>
                        <td><input type="text" id="charge-nurse" class="details-emergency-pop-up-input" readonly>
                        </td>
                        <td>{{ config('app.lang') != 'en' ? __('조치의뢰인') : __('Requested by') }}</td>
                        <td><input type="text" id="requested-by" class="details-emergency-pop-up-input" readonly>
                        </td>
                    </tr>
                </table>

                <!-- Detailed Information -->
                <div class="details-emergency-pop-up-textarea-container">
                    <label
                        for="details-description">{{ config('app.lang') != 'en' ? __('원인 및 조치내용 (서술)') : __('Cause and Action Details (Description)') }}</label>
                    <textarea id="details-description" class="details-emergency-pop-up-textarea" readonly></textarea>
                </div>
            </div>
        </div>
    </div>
</div>
<!--  -->


<!-- modal call patient history -->
<div class="modal fade" id="nurseCallHistoryModal">
    <div class="modal-dialog modal-lg"
        style="max-width:800px;display: flex;align-items: flex-start; justify-content: center; min-height: 100vh;">
        <div class="modal-content modal-hospital-content">
            <!-- Modal Header -->
            <div class="modal-header"
                style="background-color:#3CBAFF;padding:20px 24px;align-items: center;justify-content: center;position: relative;">
                <h4 class="modal-title" style="color:#FFFFFF">
                    {{ config('app.lang') != 'en' ? __('간호사 호출 이력') : __('Nurse Call History') }}
                </h4>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <div class="styled-table-container">
                    <table class="styled-table">
                        <thead>
                            <tr>
                                <th class="text-center" style="background-color:#111A23;border:none;">
                                    {{ config('app.lang') != 'en' ? __('번호') : __('Number') }}
                                </th>
                                <th style="background-color:#111A23;border:none;">
                                    {{ config('app.lang') != 'en' ? __('알림발생일시') : __('Alert Date') }}
                                </th>
                                <th style="background-color:#111A23;border:none;">
                                    {{ config('app.lang') != 'en' ? __('알림 상세') : __('Alert Detail') }}
                                </th>
                                <th style="background-color:#111A23;border:none;">
                                    {{ config('app.lang') != 'en' ? __('알림확인일시') : __('Alert Confirmation Date') }}
                                </th>
                                <th style="background-color:#111A23;border:none;">
                                    {{ config('app.lang') != 'en' ? __('조치의뢰인') : __('Requested by') }}
                                </th>
                                <th style="background-color:#111A23;border:none;">
                                    {{ config('app.lang') != 'en' ? __('조치완료일시') : __('Completion Date') }}
                                </th>
                                <th style="background-color:#111A23;border:none;">
                                    {{ config('app.lang') != 'en' ? __('조치내용') : __('Action Details') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody id="table-body-nurse">
                            <!-- Data will be injected here -->
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center" id="pagination-container-nurse">
                    <!-- Pagination will be injected here -->
                </div>
            </div>
        </div>
    </div>
</div>
<!--  -->

<!-- modal detail call patient history -->
<div class="modal fade" id="detailNurseCallHistoryModal">
    <div class="modal-dialog modal-lg details-emergency-pop-up-modal-dialog"
        style="max-width:800px;display: flex;align-items: flex-start; justify-content: center; min-height: 100vh;">
        <div class="modal-content details-emergency-pop-up-modal-content">
            <!-- Modal Header -->
            <!-- <div class="modal-header details-emergency-pop-up-modal-header">
                <h4 class="modal-title">
                    {{ config('app.lang') != 'en' ? __('조치내용') : __('Action Details') }}
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div> -->

            <!-- Modal Body -->
            <div class="details-emergency-pop-up-styled-details-container" id="details-container">
                <!-- Header Fields -->
                <table class="details-emergency-pop-up-table">
                    <tr>
                        <td>{{ config('app.lang') != 'en' ? __('주치의') : __('Primary Doctor') }}</td>
                        <td><input type="text" id="primary-doctor-nurse" class="details-emergency-pop-up-input"
                                readonly>
                        </td>
                        <td>{{ config('app.lang') != 'en' ? __('담당간호사') : __('Charge Nurse') }}</td>
                        <td><input type="text" id="charge-nurse-nurse" class="details-emergency-pop-up-input" readonly>
                        </td>
                        <td>{{ config('app.lang') != 'en' ? __('조치의뢰인') : __('Requested by') }}</td>
                        <td><input type="text" id="requested-by-nurse" class="details-emergency-pop-up-input" readonly>
                        </td>
                    </tr>
                </table>

                <!-- Detailed Information -->
                <div class="details-emergency-pop-up-textarea-container">
                    <label
                        for="details-description">{{ config('app.lang') != 'en' ? __('원인 및 조치내용 (서술)') : __('Cause and Action Details (Description)') }}</label>
                    <textarea id="details-description-nurse" class="details-emergency-pop-up-textarea"
                        readonly></textarea>
                </div>
            </div>
        </div>
    </div>
</div>
<!--  -->
<script>
let inactivityTimeout;

// Fungsi yang akan dipanggil untuk memeriksa aktivitas pengguna
function resetInactivityTimeout() {
    // Jika ada timeout yang sudah diatur sebelumnya, hapus timeout tersebut
    clearTimeout(inactivityTimeout);

    // Setel timeout untuk memanggil getChart setelah 3 menit (180000 milidetik) tidak ada aktivitas
    inactivityTimeout = setTimeout(function() {
        refreshPage();
        // Memulai interval untuk memanggil getChart setiap menit jika tidak ada aktivitas
        setInterval(getChart, 180000);
    }, 180000); // 3 menit
}
// Menambahkan event listener untuk mendeteksi aktivitas pengguna
document.addEventListener('mousemove', resetInactivityTimeout);
document.addEventListener('mousedown', resetInactivityTimeout);
document.addEventListener('keypress', resetInactivityTimeout);
document.addEventListener('touchmove', resetInactivityTimeout);
resetInactivityTimeout();

function refreshPage() {
    window.location.reload();
}
document.getElementById('select-all').addEventListener('click', function() {
    document.querySelectorAll('.checkbox-click').forEach(checkbox => checkbox.checked = true);
});

document.getElementById('deselect-all').addEventListener('click', function() {
    document.querySelectorAll('.checkbox-click').forEach(checkbox => checkbox.checked = false);
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

let lang = `{{config('app.lang') != 'en' ? 'ko' : 'en';}}`
let patient_id;

function viewDetails(id, patient_id) {
    fetchEmergencyHistory(patient_id);
    fetchNurseCallHistory(patient_id);
    fetch('/hospital/history/patient-calls/' + id)
        .then(response => response.json())
        .then(data => {
            const resp = data.data;

            // Update emergency alert information
            const emergencyAlertInfoHtml = `
                <tr>
                    <td>${getTranslation('알림 종류', 'Alert Type')}</td>
                    <td style="background-color:#1E3142;color:#FFFFFF">${resp.call_type}</td>
                    <td style="background-color:#324D65;color:#FFFFFF">${getTranslation('알림 상세', 'Alert Details')}</td>
                    <td style="background-color:#1E3142;color:#FFFFFF">${resp.call_details}</td>
                </tr>
                <tr>
                    <td>${getTranslation('알림 발생시간', 'Alert Occurrence Time')}</td>
                    <td style="background-color:#1E3142;color:#FFFFFF">${resp.call_occurrence_time}</td>
                    <td style="background-color:#324D65;color:#FFFFFF">${getTranslation('환자분 성명', 'Patient Name')}</td>
                    <td style="background-color:#1E3142;color:#FFFFFF">${resp.patient_name}</td>
                </tr>
                <tr>
                    <td style="background-color:#324D65;color:#FFFFFF">${getTranslation('병실호수', 'Ward Number')}</td>
                    <td style="background-color:#1E3142;color:#FFFFFF">${resp.ward}</td>
                    <td style="background-color:#324D65;color:#FFFFFF">${getTranslation('병실호수', 'Ward Number')}</td>
                    <td style="background-color:#1E3142;color:#FFFFFF">${resp.ward}</td>
                </tr>
            `;
            document.querySelector('#emergencyAlertInfo .patient-info-table tbody').innerHTML =
                emergencyAlertInfoHtml;

            const doctorHtml = `                
                                <tr>
                    <td>${getTranslation('주치의', 'Doctor in Charge')}</td>
                    <td style="background-color:#1E3142;color:#FFFFFF">${resp.doctor_in_charge}</td>
                </tr>
                <tr>
                    <td>${getTranslation('담당간호사', 'Nurse in Charge')}</td>
                    <td style="background-color:#1E3142;color:#FFFFFF">${resp.nurse_in_charge}</td>
                </tr>
                <tr>
                    <td>${getTranslation('조치의료인', 'Medical Staff')}</td>
                    <td style="background-color:#1E3142;color:#FFFFFF">${resp.medical_staff}</td>
                </tr>
            `;

            document.querySelector('#doctor-info tbody').innerHTML =
                doctorHtml;

            document.getElementById('reason').innerText = resp.cause_and_actions;

            // Update target information
            const targetInfoHtml = `
                <tr>
                    <td>${getTranslation('대상자명', 'Patient Name')}</td>
                    <td style="background-color:#1E3142;color:#FFFFFF">${resp.patient_hospital.name}</td>
                    <td style="background-color:#324D65;color:#FFFFFF">${getTranslation('입원실', 'Ward')}</td>
                    <td style="background-color:#1E3142;color:#FFFFFF">${resp.ward}</td>
                </tr>
                <tr>
                    <td>${getTranslation('등록번호', 'Registration Number')}</td>
                    <td style="background-color:#1E3142;color:#FFFFFF">${resp.patient_hospital.patient_code}</td>
                    <td style="background-color:#324D65;color:#FFFFFF">${getTranslation('주치의', 'Doctor in Charge')}</td>
                    <td style="background-color:#1E3142;color:#FFFFFF">${resp.patient_hospital.doctor_in_charge}</td>
                </tr>
                <tr>
                    <td>${getTranslation('성별', 'Gender')}</td>
                    <td style="background-color:#1E3142;color:#FFFFFF">-</td>
                    <td style="background-color:#324D65;color:#FFFFFF">${getTranslation('담당간호사', 'Nurse in Charge')}</td>
                    <td style="background-color:#1E3142;color:#FFFFFF">${resp.patient_hospital.nurse_in_charge}</td>
                </tr>
                <tr>
                    <td>${getTranslation('나이', 'Age')}</td>
                    <td style="background-color:#1E3142;color:#FFFFFF">-</td>
                    <td style="background-color:#324D65;color:#FFFFFF">${getTranslation('진단명', 'Diagnosis')}</td>
                    <td style="background-color:#1E3142;color:#FFFFFF">${resp.patient_hospital.diagnostic_name}</td>
                </tr>
                <tr>
                    <td>${getTranslation('생년월일', 'Birthdate')}</td>
                    <td style="background-color:#1E3142;color:#FFFFFF">${resp.patient_hospital.date_of_birth}</td>
                    <td style="background-color:#324D65;color:#FFFFFF">${getTranslation('스마트워치코드', 'Smartwatch Code')}</td>
                    <td style="background-color:#1E3142;color:#FFFFFF">${resp.patient_hospital.device_code}</td>
                </tr>
                <tr>
                    <td>${getTranslation('신장 (cm)', 'Height (cm)')}</td>
                    <td style="background-color:#1E3142;color:#FFFFFF">${resp.patient_hospital.height_cm}</td>
                    <td style="background-color:#324D65;color:#FFFFFF">${getTranslation('연락처', 'Contact')}</td>
                    <td style="background-color:#1E3142;color:#FFFFFF">${resp.patient_hospital.contact_information}</td>
                </tr>
                <tr>
                    <td>${getTranslation('체중 (kg)', 'Weight (kg)')}</td>
                    <td style="background-color:#1E3142;color:#FFFFFF">${resp.patient_hospital.weight_kg}</td>
                    <td style="background-color:#324D65;color:#FFFFFF">${getTranslation('비상연락처 1', 'Emergency Contact 1')}</td>
                    <td style="background-color:#1E3142;color:#FFFFFF">${resp.patient_hospital.emergency_contact_1}</td>
                </tr>
            `;
            document.querySelector('#patientInfo .patient-info-table tbody').innerHTML = targetInfoHtml;
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
            fetch('/hospital/history/patient-calls', {
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
function fetchEmergencyHistory(patient_id, page = 1, limit = 10) {
    const apiEndpoint = '/hospital/patient-management/inpatients/emergency-history';

    const translations = {
        'Number': langApp !== 'en' ? '번호' : 'Number',
        'Alert Date': langApp !== 'en' ? '알림발생일시' : 'Alert Date',
        'Alert Detail': langApp !== 'en' ? '알림 상세' : 'Alert Detail',
        'Alert Confirmation Date': langApp !== 'en' ? '알림확인일시' : 'Alert Confirmation Date',
        'Patient Name': langApp !== 'en' ? '환자 이름' : 'Patient Name',
        'Completion Date': langApp !== 'en' ? '완료 일시' : 'Completion Date',
        'View Details': langApp !== 'en' ? '상세보기' : 'View Details',
    };

    function translate(text) {
        return translations[text] || text;
    }

    fetch(`${apiEndpoint}/${patient_id}?limit=${limit}&page=${page}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update the table
                const tableBody = document.getElementById('table-body');
                tableBody.innerHTML = '';
                data.datas.data.forEach((item, index) => {
                    const row = `<tr>
                        <td>${(data.datas.current_page - 1) * data.datas.per_page + index + 1}</td>
                        <td>${item.alert_occurrence_time}</td>
                        <td>${item.alert_details}</td>
                        <td>${item.alert_confirmation_time ? item.alert_confirmation_time : '-'}</td>
                        <td>${item.patient_name}</td>
                        <td>${item.completed_time ? item.completed_time : '-'}</td>
                        <td class="td-actions">
                            <button class="btn btn-link btn-sm" type="button" onclick="viewDetailsEmergencyHistory(${item.id})">
                                ${translate('View Details')}
                            </button>
                        </td>
                    </tr>`;
                    tableBody.insertAdjacentHTML('beforeend', row);
                });

                createPaginationWithArg(patient_id, data.datas, limit, 'fetchEmergencyHistory',
                    'pagination-container');
            } else {
                console.error('Failed to fetch data');
            }
        })
        .catch(error => console.error('Error:', error));
}

function viewDetailsEmergencyHistory(itemId) {
    const apiEndpoint = `/hospital/patient-management/inpatients/emergency-history/detail/${itemId}`;

    fetch(apiEndpoint, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('primary-doctor').value = data.item.doctor_in_charge;
                // document.getElementById('diagnostic').value = data.item.diagnostic;
                document.getElementById('charge-nurse').value = data.item.nurse_in_charge;
                document.getElementById('requested-by').value = data.item.patient_name;
                document.getElementById('details-description').value = data.item.cause_and_actions;

                $('#detailEmergencyHistoryModal').modal('show');
            } else {
                console.error('Failed to fetch details');
            }
        })
        .catch(error => console.error('Error:', error));
}

function fetchNurseCallHistory(patient_id, page = 1, limit = 10) {
    const apiEndpoint = '/hospital/patient-management/inpatients/patient-call-history';

    const translations = {
        'Number': langApp !== 'en' ? '번호' : 'Number',
        'Alert Date': langApp !== 'en' ? '알림발생일시' : 'Alert Date',
        'Alert Detail': langApp !== 'en' ? '알림 상세' : 'Alert Detail',
        'Alert Confirmation Date': langApp !== 'en' ? '알림확인일시' : 'Alert Confirmation Date',
        'Patient Name': langApp !== 'en' ? '환자 이름' : 'Patient Name',
        'Completion Date': langApp !== 'en' ? '완료 일시' : 'Completion Date',
        'View Details': langApp !== 'en' ? '상세보기' : 'View Details',
    };

    function translate(text) {
        return translations[text] || text;
    }

    fetch(`${apiEndpoint}/${patient_id}?limit=${limit}&page=${page}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update the table
                const tableBody = document.getElementById('table-body-nurse');
                tableBody.innerHTML = '';
                data.datas.data.forEach((item, index) => {
                    const row = `<tr>
                        <td>${(data.datas.current_page - 1) * data.datas.per_page + index + 1}</td>
                        <td>${item.call_occurrence_time}</td>
                        <td>${item.call_details}</td>
                        <td>${item.call_confirmation_time ? item.call_confirmation_time : '-'}</td>
                        <td>${item.patient_name}</td>
                        <td>${item.completed_time ? item.completed_time : '-'}</td>
                        <td class="td-actions">
                            <button class="btn btn-link btn-sm" type="button" onclick="viewDetailsNurseCallHistoryHistory(${item.id})">
                                ${translate('View Details')}
                            </button>
                        </td>
                    </tr>`;
                    tableBody.insertAdjacentHTML('beforeend', row);
                });

                createPaginationWithArg(patient_id, data.datas, limit, 'fetchNurseCallHistory',
                    'pagination-container-nurse');
            } else {
                console.error('Failed to fetch data');
            }
        })
        .catch(error => console.error('Error:', error));
}

function viewDetailsNurseCallHistoryHistory(itemId) {
    const apiEndpoint = `/hospital/patient-management/inpatients/patient-call-history/detail/${itemId}`;

    fetch(apiEndpoint, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('primary-doctor-nurse').value = data.item.doctor_in_charge;
                // document.getElementById('diagnostic').value = data.item.diagnostic;
                document.getElementById('charge-nurse-nurse').value = data.item.nurse_in_charge;
                document.getElementById('requested-by-nurse').value = data.item.patient_name;
                document.getElementById('details-description-nurse').value = data.item.cause_and_actions;

                $('#detailNurseCallHistoryModal').modal('show');
            } else {
                console.error('Failed to fetch details');
            }
        })
        .catch(error => console.error('Error:', error));
}
</script>
@endsection