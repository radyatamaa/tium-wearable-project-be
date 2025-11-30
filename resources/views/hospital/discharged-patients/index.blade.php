@php
$pageTitle = config('app.lang') != 'en' ? __('입원 환자') : __('discharged_patients');
@endphp

@extends('layouts.app', ['page' => $pageTitle, 'pageSlug' => 'discharged_patients'])

@section('content-fluid')
<!-- header -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-primary d-flex justify-content-between align-items-center p-2"
                style="--header-background-color: #324D65; background-color: var(--header-background-color);">
                <div class="header-hospital-filter-container">
                    <div class="header-hospital-filter-row">
                        <form id="filter-form" method="GET" action="/hospital/patient-management/discharged"
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
                                        <a class="dropdown-item" href="#" data-value="patient_code">
                                            @if (config('app.lang') != 'en')
                                            {{ __('대상자코드') }}
                                            @else
                                            {{ __('Patient Code') }}
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
                                        <a class="dropdown-item" href="#" data-value="ward">
                                            @if (config('app.lang') != 'en')
                                            {{ __('입원실') }}
                                            @else
                                            {{ __('Ward') }}
                                            @endif
                                        </a>
                                        <a class="dropdown-item" href="#" data-value="doctor_in_charge">
                                            @if (config('app.lang') != 'en')
                                            {{ __('주치의') }}
                                            @else
                                            {{ __('Doctor Name') }}
                                            @endif
                                        </a>
                                        <a class="dropdown-item" href="#" data-value="nurse_in_charge">
                                            @if (config('app.lang') != 'en')
                                            {{ __('담당간호사') }}
                                            @else
                                            {{ __('Nurse Name') }}
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
                    <button class="btn btn-dark btn-sm" type="button" id="return-inpatient-selected">
                        {{ config('app.lang') != 'en' ? __('입원환자로 복귀') : __('Return to inpatient') }}
                    </button>
                    <button class="btn btn-primary btn-danger btn-sm" type="button" id="delete-selected">
                        @if (config('app.lang') != 'en')
                        {{ __('삭제') }}
                        @else
                        {{ __('Elimination') }}
                        @endif
                    </button>

                    <button class="btn btn-primary btn-info btn-sm" type="button"
                        onclick="exportExcel('{{ url('hospital/patient-management/discharged/export') }}')">
                        @if (config('app.lang') != 'en')
                        {{ __('엑셀 다운로드') }}
                        @else
                        {{ __('Download Export') }}
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
                                    {{ config('app.lang') != 'en' ? __('대상자명') : __('Patient Name') }}
                                </th>
                                <th>
                                    {{ config('app.lang') != 'en' ? __('대상자코드') : __('Patient Code') }}
                                </th>
                                <th>
                                    {{ config('app.lang') != 'en' ? __('성별') : __('Gender') }}
                                </th>
                                <th>
                                    {{ config('app.lang') != 'en' ? __('나이') : __('Age') }}
                                </th>
                                <th>
                                    {{ config('app.lang') != 'en' ? __('입원실') : __('Ward') }}
                                </th>
                                <th>
                                    {{ config('app.lang') != 'en' ? __('주치의') : __('Doctor in Charge') }}
                                </th>
                                <th>
                                    {{ config('app.lang') != 'en' ? __('담당간호사') : __('Nurse in Charge') }}
                                </th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($datas as $data)
                            <tr>
                                <td><input type="checkbox" class="checkbox-click" value="{{ $data->id}}"></td>
                                <td>{{ $loop->iteration + ($datas->currentPage() - 1) * $datas->perPage() }}</td>
                                <td>{{$data->name}}</td>
                                <td>{{$data->patient_code}}</td>
                                <td>{{$data->gender}}</td>
                                <td>{{$data->age}}</td>
                                <td>{{$data->hospital_room}}</td>
                                <td>{{$data->doctor_in_charge}}</td>
                                <td>{{$data->nurse_in_charge}}</td>
                                <td class="td-actions">
                                    <button class="btn btn-warning btn-sm" type="button"
                                        onclick="viewDetails({{ $data->id }})">
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
                <!-- <div class="navbar-title-blue">305room 홍길동님 </div> -->
                <h5 class="title">
                    {{ config('app.lang') != 'en' ? __('대상자정보') : __('Target information
                        '); }}
                </h5>
                <!-- Navbar -->
                <div class="col-md-12 mt-3">
                    <div class="card mb-3" style="background-color: #192734; color: #FFFFFF;">
                        <div class="nav-bar-custom" style="background-color: #192734;">
                            <div class="nav-item active" data-target="basicInformation">
                                <div class="title">

                                    {{ config('app.lang') != 'en' ? __('기본정보') : __('Basic Information') }}</div>
                            </div>
                            <div class="nav-item" data-target="bodyMeasurementInformation" onclick="getChart()">
                                <div class="title">
                                    {{ config('app.lang') != 'en' ? __('신체 측정 정보') : __('Body Measurement Information') }}
                                </div>
                            </div>
                            <div class="nav-item" data-target="measurementKriteria">
                                <div class="title">
                                    {{ config('app.lang') != 'en' ? __('측정 기준 설정') : __('Measurement Kriteria') }}
                                </div>
                            </div>
                            <div class="nav-item" data-target="measurementInformation"
                                onclick="fetchGetHealthMeasurementHistory('blood_pressure')">
                                <div class="title">
                                    {{ config('app.lang') != 'en' ? __('측정 정보이력') : __('Measurement Information History') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--  -->
            </div>
            <div class="card-body-custom-1 flex-fill d-flex flex-column justify-content-left align-items-left w-100"
                style="background-color:#1E3142;min-height:448px;">
                <!-- basic information -->
                <div class="content-section active" id="basicInformation">
                    <!-- card header -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <h5 class="title" style="color: #FFFFFF;">
                                    {{ config('app.lang') != 'en' ? __('기본정보') : __('Basic Information') }}</h5>
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
                                        <table class="patient-info-table" id="patient-details">
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <!--  -->

                <!-- body measurement -->
                <div class="content-section" id="bodyMeasurementInformation">
                    <!-- card header -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <h5 class="title" style="color: #FFFFFF;">
                                    {{ config('app.lang') != 'en' ? __('신체 측정 정보') : __('Health Measurement Information') }}
                                </h5>
                                <!-- <div>
                                    <button class="btn btn-info btn-sm" type="button" onclick="hideForm()">
                                        {{ config('app.lang') != 'en' ? __('전체') : __('All') }}
                                    </button>
                                    <button class="btn btn-info btn-sm" type="button" onclick="hideForm()">
                                        {{ config('app.lang') != 'en' ? __('의사') : __('Doctor') }}
                                    </button>
                                    <button class="btn btn-info btn-sm" type="button" onclick="hideForm()">
                                        {{ config('app.lang') != 'en' ? __('간호사') : __('Nurse') }}
                                    </button>
                                </div> -->
                            </div>
                        </div>
                    </div>
                    <!--  -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card" style="background-color:#1E3142;">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div id="avgChart">
                                            <span style="font-size: 48px; color: #FFFFFF;" name="avgChartCount1"></span>
                                            <span style="font-size: 24px; color: #FFFFFF;" name="avgChartCount2"></span>
                                        </div>
                                        <div>
                                            <div class="btn-group">
                                                <button type="button" id="measurementType1"
                                                    class="btn btn-warning dropdown-toggle" data-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false"
                                                    style="background-color:#4E6880">
                                                    {{ config('app.lang') != 'en' ? __('혈압') : __('Blood Pressure') }}
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" onclick="measurementTime('blood_pressure')"
                                                        style="color:#FFFFFF">{{ config('app.lang') != 'en' ? __('혈압') : __('Blood Pressure') }}</a>
                                                    <a class="dropdown-item"
                                                        onclick="measurementTime('body_temperature')"
                                                        style="color:#FFFFFF">{{ config('app.lang') != 'en' ? __('체온') : __('Body temperature') }}</a>
                                                    <a class="dropdown-item" onclick="measurementTime('heart_rate')"
                                                        style="color:#FFFFFF">{{ config('app.lang') != 'en' ? __('심박수') : __('Heart rate') }}</a>
                                                    <!-- <a class="dropdown-item"
                                                        onclick="measurementTime('respiratory_rate')"
                                                        style="color:#FFFFFF">{{ config('app.lang') != 'en' ? __('호흡수') : __('Respiratory rate') }}</a> -->
                                                    <a class="dropdown-item"
                                                        onclick="measurementTime('oxygen_saturation')"
                                                        style="color:#FFFFFF">{{ config('app.lang') != 'en' ? __('산소') : __('Oxygen') }}</a>
                                                    <!-- <a class="dropdown-item" onclick="measurementTime('ecg')"
                                                        style="color:#FFFFFF">{{ config('app.lang') != 'en' ? __('심전도') : __('ECG') }}</a> -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="chart-container">
                                        <canvas id="chartCanvas"></canvas>

                                    </div>

                                    <!-- Table 1 -->
                                    <table class="vitals-table" id="vitalsTableBodyContainer">
                                        <tbody id="vitalsTableBody" style="width:100%">
                                            <!-- data vital stored from javascript -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--  -->

                <!-- set measurement criteria -->
                <div class="content-section" id="measurementKriteria">
                    <!-- card header -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <h5 class="title" style="color: #FFFFFF;">
                                    {{ config('app.lang') != 'en' ? __('측정정보 기준 값 설정') : __('Setting measurement information standard value') }}
                                </h5>
                                <!-- <div>
                                    <button class="btn btn-info btn-sm" type="button"
                                        onclick="resetSettingMeasurement()">
                                        {{ config('app.lang') != 'en' ? __('초기화') : __('reset') }}
                                    </button>
                                    <button class="btn btn-primary btn-sm" type="button"
                                        onclick="saveMeasurementSettings()">
                                        {{ config('app.lang') != 'en' ? __('저장') : __('save') }}
                                    </button>
                                </div> -->
                            </div>
                        </div>
                    </div>
                    <!--  -->

                    <div class="row">
                        <div class="col-md-12">
                            <table class="set-measurement-table">
                                <tbody>
                                    <tr>
                                        <td colspan="5" class="set-measurement-header"
                                            style="border:none;text-align:left">
                                            {{ config('app.lang') != 'en' ? __('혈압 (수축기)') : __('Blood Pressure (Systolic)') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="set-measurement-criteria normal">
                                            {{ config('app.lang') != 'en' ? __('양호') : __('Good health') }}
                                        </td>
                                        <td class="set-measurement">
                                            <input type="text" class="set-measurement-input"
                                                name="blood_pressure_systolic_good_min"
                                                placeholder="{{ config('app.lang') != 'en' ? __('낮은') : __('Low') }}" />
                                        </td>
                                        <td class="set-measurement-separator">-</td>
                                        <td class="set-measurement">
                                            <input type="text" class="set-measurement-input"
                                                name="blood_pressure_systolic_good_max"
                                                placeholder="{{ config('app.lang') != 'en' ? __('높은') : __('High') }}" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="set-measurement-criteria caution">
                                            {{ config('app.lang') != 'en' ? __('주의') : __('Caution') }}
                                        </td>
                                        <td class="set-measurement">
                                            <input type="text" class="set-measurement-input"
                                                name="blood_pressure_systolic_caution_min"
                                                placeholder="{{ config('app.lang') != 'en' ? __('낮은') : __('Low') }}" />
                                        </td>
                                        <td class="set-measurement-separator">-</td>
                                        <td class="set-measurement">
                                            <input type="text" class="set-measurement-input"
                                                name="blood_pressure_systolic_caution_max"
                                                placeholder="{{ config('app.lang') != 'en' ? __('높은') : __('High') }}" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="set-measurement-criteria danger">
                                            {{ config('app.lang') != 'en' ? __('위험') : __('Dangers') }}
                                        </td>
                                        <td class="set-measurement">
                                            <input type="text" class="set-measurement-input"
                                                name="blood_pressure_systolic_danger_min"
                                                placeholder="{{ config('app.lang') != 'en' ? __('낮은') : __('Low') }}" />
                                        </td>
                                        <td class="set-measurement-separator">-</td>
                                        <td class="set-measurement">
                                            <input type="text" class="set-measurement-input"
                                                name="blood_pressure_systolic_danger_max"
                                                placeholder="{{ config('app.lang') != 'en' ? __('높은') : __('High') }}" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="set-measurement-header"
                                            style="border:none;text-align:left">
                                            {{ config('app.lang') != 'en' ? __('혈압 (이완기)') : __('Blood Pressure (Diastolic)') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="set-measurement-criteria normal">
                                            {{ config('app.lang') != 'en' ? __('양호') : __('Good health') }}
                                        </td>
                                        <td class="set-measurement">
                                            <input type="text" class="set-measurement-input"
                                                name="blood_pressure_diastolic_good_min"
                                                placeholder="{{ config('app.lang') != 'en' ? __('낮은') : __('Low') }}" />
                                        </td>
                                        <td class="set-measurement-separator">-</td>
                                        <td class="set-measurement">
                                            <input type="text" class="set-measurement-input"
                                                name="blood_pressure_diastolic_good_max"
                                                placeholder="{{ config('app.lang') != 'en' ? __('높은') : __('High') }}" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="set-measurement-criteria caution">
                                            {{ config('app.lang') != 'en' ? __('주의') : __('Caution') }}
                                        </td>
                                        <td class="set-measurement">
                                            <input type="text" class="set-measurement-input"
                                                name="blood_pressure_diastolic_caution_min"
                                                placeholder="{{ config('app.lang') != 'en' ? __('낮은') : __('Low') }}" />
                                        </td>
                                        <td class="set-measurement-separator">-</td>
                                        <td class="set-measurement">
                                            <input type="text" class="set-measurement-input"
                                                name="blood_pressure_diastolic_caution_max"
                                                placeholder="{{ config('app.lang') != 'en' ? __('높은') : __('High') }}" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="set-measurement-criteria danger">
                                            {{ config('app.lang') != 'en' ? __('위험') : __('Dangers') }}
                                        </td>
                                        <td class="set-measurement">
                                            <input type="text" class="set-measurement-input"
                                                name="blood_pressure_diastolic_danger_min"
                                                placeholder="{{ config('app.lang') != 'en' ? __('낮은') : __('Low') }}" />
                                        </td>
                                        <td class="set-measurement-separator">-</td>
                                        <td class="set-measurement">
                                            <input type="text" class="set-measurement-input"
                                                name="blood_pressure_diastolic_danger_max"
                                                placeholder="{{ config('app.lang') != 'en' ? __('높은') : __('High') }}" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="set-measurement-header"
                                            style="border:none;text-align:left">
                                            {{ config('app.lang') != 'en' ? __('심박수') : __('Heart Rate') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="set-measurement-criteria normal">
                                            {{ config('app.lang') != 'en' ? __('양호') : __('Good health') }}
                                        </td>
                                        <td class="set-measurement">
                                            <input type="text" class="set-measurement-input"
                                                name="heart_rate_good_health_value1"
                                                placeholder="{{ config('app.lang') != 'en' ? __('낮은') : __('Low') }}" />
                                        </td>
                                        <td class="set-measurement-separator">-</td>
                                        <td class="set-measurement">
                                            <input type="text" class="set-measurement-input"
                                                name="heart_rate_good_health_value2"
                                                placeholder="{{ config('app.lang') != 'en' ? __('높은') : __('High') }}" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="set-measurement-criteria caution">
                                            {{ config('app.lang') != 'en' ? __('주의') : __('Caution') }}
                                        </td>
                                        <td class="set-measurement">
                                            <input type="text" class="set-measurement-input"
                                                name="heart_rate_caution_value1"
                                                placeholder="{{ config('app.lang') != 'en' ? __('낮은') : __('Low') }}" />
                                        </td>
                                        <td class="set-measurement-separator">-</td>
                                        <td class="set-measurement">
                                            <input type="text" class="set-measurement-input"
                                                name="heart_rate_caution_value2"
                                                placeholder="{{ config('app.lang') != 'en' ? __('높은') : __('High') }}" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="set-measurement-criteria danger">
                                            {{ config('app.lang') != 'en' ? __('위험') : __('Dangers') }}
                                        </td>
                                        <td class="set-measurement">
                                            <input type="text" class="set-measurement-input"
                                                name="heart_rate_dangers_value1"
                                                placeholder="{{ config('app.lang') != 'en' ? __('낮은') : __('Low') }}" />
                                        </td>
                                        <td class="set-measurement-separator">-</td>
                                        <td class="set-measurement">
                                            <input type="text" class="set-measurement-input"
                                                name="heart_rate_dangers_value2"
                                                placeholder="{{ config('app.lang') != 'en' ? __('높은') : __('High') }}" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="set-measurement-header"
                                            style="border:none;text-align:left">
                                            {{ config('app.lang') != 'en' ? __('산소포화도') : __('Oxygen Saturation') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="set-measurement-criteria normal">
                                            {{ config('app.lang') != 'en' ? __('양호') : __('Good health') }}
                                        </td>
                                        <td class="set-measurement">
                                            <input type="text" class="set-measurement-input"
                                                name="oxygen_saturation_good_health_value1"
                                                placeholder="{{ config('app.lang') != 'en' ? __('낮은') : __('Low') }}" />
                                        </td>
                                        <td class="set-measurement-separator">-</td>
                                        <td class="set-measurement">
                                            <input type="text" class="set-measurement-input"
                                                name="oxygen_saturation_good_health_value2"
                                                placeholder="{{ config('app.lang') != 'en' ? __('높은') : __('High') }}" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="set-measurement-criteria caution">
                                            {{ config('app.lang') != 'en' ? __('주의') : __('Caution') }}
                                        </td>
                                        <td class="set-measurement">
                                            <input type="text" class="set-measurement-input"
                                                name="oxygen_saturation_caution_value1"
                                                placeholder="{{ config('app.lang') != 'en' ? __('낮은') : __('Low') }}" />
                                        </td>
                                        <td class="set-measurement-separator">-</td>
                                        <td class="set-measurement">
                                            <input type="text" class="set-measurement-input"
                                                name="oxygen_saturation_caution_value2"
                                                placeholder="{{ config('app.lang') != 'en' ? __('높은') : __('High') }}" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="set-measurement-criteria danger">
                                            {{ config('app.lang') != 'en' ? __('위험') : __('Dangers') }}
                                        </td>
                                        <td class="set-measurement">
                                            <input type="text" class="set-measurement-input"
                                                name="oxygen_saturation_dangers_value1"
                                                placeholder="{{ config('app.lang') != 'en' ? __('낮은') : __('Low') }}" />
                                        </td>
                                        <td class="set-measurement-separator">-</td>
                                        <td class="set-measurement">
                                            <input type="text" class="set-measurement-input"
                                                name="oxygen_saturation_dangers_value2"
                                                placeholder="{{ config('app.lang') != 'en' ? __('높은') : __('High') }}" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="set-measurement-header"
                                            style="border:none;text-align:left">
                                            {{ config('app.lang') != 'en' ? __('체온') : __('Body Temperature') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="set-measurement-criteria normal">
                                            {{ config('app.lang') != 'en' ? __('양호') : __('Good health') }}
                                        </td>
                                        <td class="set-measurement">
                                            <input type="text" class="set-measurement-input"
                                                name="body_temperature_good_health_value1"
                                                placeholder="{{ config('app.lang') != 'en' ? __('낮은') : __('Low') }}" />
                                        </td>
                                        <td class="set-measurement-separator">-</td>
                                        <td class="set-measurement">
                                            <input type="text" class="set-measurement-input"
                                                name="body_temperature_good_health_value2"
                                                placeholder="{{ config('app.lang') != 'en' ? __('높은') : __('High') }}" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="set-measurement-criteria caution">
                                            {{ config('app.lang') != 'en' ? __('주의') : __('Caution') }}
                                        </td>
                                        <td class="set-measurement">
                                            <input type="text" class="set-measurement-input"
                                                name="body_temperature_caution_value1"
                                                placeholder="{{ config('app.lang') != 'en' ? __('낮은') : __('Low') }}" />
                                        </td>
                                        <td class="set-measurement-separator">-</td>
                                        <td class="set-measurement">
                                            <input type="text" class="set-measurement-input"
                                                name="body_temperature_caution_value2"
                                                placeholder="{{ config('app.lang') != 'en' ? __('높은') : __('High') }}" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="set-measurement-criteria danger">
                                            {{ config('app.lang') != 'en' ? __('위험') : __('Dangers') }}
                                        </td>
                                        <td class="set-measurement">
                                            <input type="text" class="set-measurement-input"
                                                name="body_temperature_dangers_value1"
                                                placeholder="{{ config('app.lang') != 'en' ? __('낮은') : __('Low') }}" />
                                        </td>
                                        <td class="set-measurement-separator">-</td>
                                        <td class="set-measurement">
                                            <input type="text" class="set-measurement-input"
                                                name="body_temperature_dangers_value2"
                                                placeholder="{{ config('app.lang') != 'en' ? __('높은') : __('High') }}" />
                                        </td>
                                    </tr>
                                    <!-- <tr>
                                        <td colspan="5" class="set-measurement-header"
                                            style="border:none;text-align:left">
                                            {{ config('app.lang') != 'en' ? __('호흡') : __('Respiratory') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="set-measurement-criteria normal">
                                            {{ config('app.lang') != 'en' ? __('양호') : __('Good health') }}
                                        </td>
                                        <td class="set-measurement">
                                            <input type="text" class="set-measurement-input"
                                                name="respiratory_good_health_value1"
                                                placeholder="{{ config('app.lang') != 'en' ? __('낮은') : __('Low') }}" />
                                        </td>
                                        <td class="set-measurement-separator">-</td>
                                        <td class="set-measurement">
                                            <input type="text" class="set-measurement-input"
                                                name="respiratory_good_health_value2"
                                                placeholder="{{ config('app.lang') != 'en' ? __('높은') : __('High') }}" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="set-measurement-criteria caution">
                                            {{ config('app.lang') != 'en' ? __('주의') : __('Caution') }}
                                        </td>
                                        <td class="set-measurement">
                                            <input type="text" class="set-measurement-input"
                                                name="respiratory_caution_value1"
                                                placeholder="{{ config('app.lang') != 'en' ? __('낮은') : __('Low') }}" />
                                        </td>
                                        <td class="set-measurement-separator">-</td>
                                        <td class="set-measurement">
                                            <input type="text" class="set-measurement-input"
                                                name="respiratory_caution_value2"
                                                placeholder="{{ config('app.lang') != 'en' ? __('높은') : __('High') }}" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="set-measurement-criteria danger">
                                            {{ config('app.lang') != 'en' ? __('위험') : __('Dangers') }}
                                        </td>
                                        <td class="set-measurement">
                                            <input type="text" class="set-measurement-input"
                                                name="respiratory_dangers_value1"
                                                placeholder="{{ config('app.lang') != 'en' ? __('낮은') : __('Low') }}" />
                                        </td>
                                        <td class="set-measurement-separator">-</td>
                                        <td class="set-measurement">
                                            <input type="text" class="set-measurement-input"
                                                name="respiratory_dangers_value2"
                                                placeholder="{{ config('app.lang') != 'en' ? __('높은') : __('High') }}" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="set-measurement-header"
                                            style="border:none;text-align:left">
                                            {{ config('app.lang') != 'en' ? __('심전도') : __('ECG') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="set-measurement-criteria normal">
                                            {{ config('app.lang') != 'en' ? __('양호') : __('Good health') }}
                                        </td>
                                        <td class="set-measurement">
                                            <input type="text" class="set-measurement-input" name="ecg_good_min"
                                                placeholder="{{ config('app.lang') != 'en' ? __('낮은') : __('Low') }}" />
                                        </td>
                                        <td class="set-measurement-separator">-</td>
                                        <td class="set-measurement">
                                            <input type="text" class="set-measurement-input" name="ecg_good_max"
                                                placeholder="{{ config('app.lang') != 'en' ? __('높은') : __('High') }}" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="set-measurement-criteria caution">
                                            {{ config('app.lang') != 'en' ? __('주의') : __('Caution') }}
                                        </td>
                                        <td class="set-measurement">
                                            <input type="text" class="set-measurement-input" name="ecg_caution_min"
                                                placeholder="{{ config('app.lang') != 'en' ? __('낮은') : __('Low') }}" />
                                        </td>
                                        <td class="set-measurement-separator">-</td>
                                        <td class="set-measurement">
                                            <input type="text" class="set-measurement-input" name="ecg_caution_max"
                                                placeholder="{{ config('app.lang') != 'en' ? __('높은') : __('High') }}" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="set-measurement-criteria danger">
                                            {{ config('app.lang') != 'en' ? __('위험') : __('Dangers') }}
                                        </td>
                                        <td class="set-measurement">
                                            <input type="text" class="set-measurement-input" name="ecg_danger_min"
                                                placeholder="{{ config('app.lang') != 'en' ? __('낮은') : __('Low') }}" />
                                        </td>
                                        <td class="set-measurement-separator">-</td>
                                        <td class="set-measurement">
                                            <input type="text" class="set-measurement-input" name="ecg_danger_max"
                                                placeholder="{{ config('app.lang') != 'en' ? __('높은') : __('High') }}" />
                                        </td>
                                    </tr> -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!--  -->

                <!-- Measurement Information History  -->
                <div class="content-section" id="measurementInformation">
                    <!-- card header -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <h5 class="title" style="color: #FFFFFF;">
                                    {{ config('app.lang') != 'en' ? __('측정 정보이력') : __('Measurement Information History') }}
                                </h5>
                            </div>
                        </div>
                    </div>
                    <!-- date and filter selection -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="btn-group">
                                        <div class="date-picker-container" style="background-color:#4E6880">
                                            <i class="fas fa-calendar-alt" onclick="toggleCalendar('calendar2-1')"></i>
                                            <input type="text" id="datepicker2-1"
                                                value="{{date('Y-m-d', strtotime('-1 month'))}}"
                                                name="start_date_range">
                                            <div class="calendar" id="calendar2-1"></div>
                                        </div>
                                    </div>
                                    <div class="btn-group">
                                        <div class="date-picker-container" style="background-color:#4E6880">
                                            <i class="fas fa-calendar-alt" onclick="toggleCalendar('calendar2-2')"></i>
                                            <input type="text" id="datepicker2-2" value="{{date('Y-m-d')}}"
                                                name="end_date_range">
                                            <div class="calendar" id="calendar2-2"></div>
                                        </div>
                                    </div>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-warning dropdown-toggle"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                            style="background-color:#4E6880">
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
                                        <button type="button" id="measurementType2"
                                            class="btn btn-warning dropdown-toggle" data-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false" style="background-color:#4E6880">
                                            {{ config('app.lang') != 'en' ? __('혈압') : __('Blood Pressure') }}
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item"
                                                onclick="fetchGetHealthMeasurementHistory('blood_pressure')"
                                                style="color:#FFFFFF">{{ config('app.lang') != 'en' ? __('혈압') : __('Blood Pressure') }}</a>
                                            <a class="dropdown-item"
                                                onclick="fetchGetHealthMeasurementHistory('body_temperature')"
                                                style="color:#FFFFFF">{{ config('app.lang') != 'en' ? __('체온') : __('Body temperature') }}</a>
                                            <a class="dropdown-item"
                                                onclick="fetchGetHealthMeasurementHistory('heart_rate')"
                                                style="color:#FFFFFF">{{ config('app.lang') != 'en' ? __('심박수') : __('Heart rate') }}</a>
                                            <!-- <a class="dropdown-item"
                                                onclick="fetchGetHealthMeasurementHistory('respiratory_rate')"
                                                style="color:#FFFFFF">{{ config('app.lang') != 'en' ? __('호흡수') : __('Respiratory rate') }}</a> -->
                                            <a class="dropdown-item"
                                                onclick="fetchGetHealthMeasurementHistory('oxygen_saturation')"
                                                style="color:#FFFFFF">{{ config('app.lang') != 'en' ? __('산소') : __('Oxygen') }}</a>
                                            <!-- <a class="dropdown-item" onclick="fetchGetHealthMeasurementHistory('ecg')"
                                                style="color:#FFFFFF">{{ config('app.lang') != 'en' ? __('심전도') : __('ECG') }}</a> -->
                                        </div>
                                    </div>
                                </div>

                                <div style="margin-bottom: 10px;">
                                    <div class="btn-group">
                                        <button class="btn btn-info btn-sm" style="border-radius:8px" type="button"
                                            onclick="downloadExcelPerDateRange()">
                                            {{ config('app.lang') != 'en' ? '엑셀' : 'Download' }}
                                            <br>
                                            {{ config('app.lang') != 'en' ? '다운로드' : 'Excel' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- measurement table -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card" style="background-color:#1E3142;">
                                <div class="card-body">
                                    <!-- Table 2 -->
                                    <table class="vitals-table" id="vitals-table-bodyContainer">
                                        <tbody id="vitals-table-body">
                                            <!-- fetchGetHealthMeasurementHistory -->
                                        </tbody>
                                    </table>

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
function resetDropdownMeasurementType1() {
    const defaultText = "{{ config('app.lang') != 'en' ? __('혈압') : __('Blood Pressure') }}";
    // Ambil tombol dropdown
    const dropdownButton = document.getElementById('measurementType1');

    // Set teks tombol ke nilai default
    dropdownButton.innerHTML = defaultText;
}

function resetDropdownMeasurementType2() {
    const defaultText = "{{ config('app.lang') != 'en' ? __('혈압') : __('Blood Pressure') }}";
    // Ambil tombol dropdown
    const dropdownButton = document.getElementById('measurementType2');

    // Set teks tombol ke nilai default
    dropdownButton.innerHTML = defaultText;
}

document.getElementById('select-all').addEventListener('click', function() {
    document.querySelectorAll('.checkbox-click').forEach(checkbox => checkbox.checked = true);
});

document.getElementById('deselect-all').addEventListener('click', function() {
    document.querySelectorAll('.checkbox-click').forEach(checkbox => checkbox.checked = false);
});

document.getElementById('return-inpatient-selected').addEventListener('click', function() {
    const selectedIds = Array.from(document.querySelectorAll('.checkbox-click:checked')).map(cb => cb
        .value);

    if (selectedIds.length > 0) {
        fetch('/hospital/patient-management/inpatients/return-inpatient', {
                method: 'POST',
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

function savePatientRegistrationModal() {
    const patientCode = document.getElementById('patient-code-modal').value;
    const name = document.getElementById('patient-name-modal').value;
    const birthDate = document.getElementById('birth-date-modal').value;
    const age = document.getElementById('age-modal').value;
    const heightCm = document.getElementById('height-modal').value;
    const weightKg = document.getElementById('weight-modal').value;
    const diagnosticName = document.getElementById('diagnosis-modal').value;
    const deviceCode = document.getElementById('smartwatch-code-modal').value;
    const contactInformation = document.getElementById('contact-modal').value;
    const emergencyContact1 = document.getElementById('emergency-contact1-modal').value;
    const emergencyContact2 = document.getElementById('emergency-contact2-modal').value;
    const toplocationId = document.getElementById('top-location-modal').value;
    const sublocationId = document.getElementById('sub-location-modal').value;
    const locationId = document.getElementById('location-modal').value;
    const doctorInChargeId = document.getElementById('attending-doctor-modal').value;
    const nurseInChargeId = document.getElementById('charge-nurse-modal').value;
    const hospitalRoom = document.getElementById('room-modal').value;
    const gender = document.getElementById('gender-modal').value;
    const patientId = document.getElementById('patient_id').value;

    let url = '/hospital/patient-management/inpatients'
    let method = 'POST'
    if (patientId) {
        url = url + '/' + patientId
        method = 'PUT'
    }
    fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                patient_code: patientCode,
                name: name,
                date_of_birth: birthDate,
                age: age,
                height_cm: heightCm,
                weight_kg: weightKg,
                diagnostic_name: diagnosticName,
                device_code: deviceCode,
                contact_information: contactInformation,
                emergency_contact_1: emergencyContact1,
                emergency_contact_2: emergencyContact2,
                top_location_id: toplocationId,
                sub_location_id: sublocationId,
                detailed_location_id: locationId,
                doctor_in_charge_id: doctorInChargeId,
                nurse_in_charge_id: nurseInChargeId,
                hospital_room: hospitalRoom,
                gender: gender,
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

let defaultValuesMeasurementSetting;
let patient_id;

function viewDetails(id) {
    patient_id = id
    fetchGetHealthMeasurementHistory(measuremenBodyHistoryTypeSelected)
    getChart();
    fetchEmergencyHistory(patient_id);
    fetchNurseCallHistory(patient_id);
    fetch('/hospital/patient-management/inpatients/' + id)
        .then(response => response.json())
        .then(data => {
            const patient = data.data;
            // const password = patient.user_customer ? patient.user_customer.password : ''
            const detailsHtml = `
            <tr>
                <td>{{ config('app.lang') != 'en' ? __('환자명') : __('Name') }}</td>
                <td style="background-color:#1E3142;color:#FFFFFF">${patient.name}</td>
                <td style="background-color:#324D65;color:#FFFFFF">{{ config('app.lang') != 'en' ? __('담당간호사') : __('Charge Nurse') }}</td>
                <td style="background-color:#1E3142;color:#FFFFFF">${patient.nurse_in_charge ? patient.nurse_in_charge.name : '-'}</td>
            </tr>
            <tr>
                <td>{{ config('app.lang') != 'en' ? __('환자코드') : __('Patient Code') }}</td>
                <td style="background-color:#1E3142;color:#FFFFFF">${patient.patient_code}</td>
                <td style="background-color:#324D65;color:#FFFFFF">{{ config('app.lang') != 'en' ? __('진단명') : __('Diagnosis') }}</td>
                <td style="background-color:#1E3142;color:#FFFFFF">${patient.diagnostic_name}</td>
            </tr>
            <tr>
                <td>{{ config('app.lang') != 'en' ? __('성별') : __('Gender') }}</td>
                <td style="background-color:#1E3142;color:#FFFFFF">${patient.gender}</td>
                <td style="background-color:#324D65;color:#FFFFFF">{{ config('app.lang') != 'en' ? __('스마트워치코드') : __('Smartwatch Code') }}</td>
                <td style="background-color:#1E3142;color:#FFFFFF">${patient.device_code}</td>
            </tr>
            <tr>
                <td>{{ config('app.lang') != 'en' ? __('나이') : __('Age') }}</td>
                <td style="background-color:#1E3142;color:#FFFFFF">${patient.age}</td>
                <td style="background-color:#324D65;color:#FFFFFF">{{ config('app.lang') != 'en' ? __('연락처') : __('Contact') }}</td>
                <td style="background-color:#1E3142;color:#FFFFFF">${patient.contact_information}</td>
            </tr>
            <tr>
                <td>{{ config('app.lang') != 'en' ? __('생년월일') : __('Birth Date') }}</td>
                <td style="background-color:#1E3142;color:#FFFFFF">${patient.date_of_birth}</td>
                <td style="background-color:#324D65;color:#FFFFFF">{{ config('app.lang') != 'en' ? __('비상연락처 1') : __('Emergency Contact 1') }}</td>
                <td style="background-color:#1E3142;color:#FFFFFF">${patient.emergency_contact_1}</td>
            </tr>
            <tr>
                <td>{{ config('app.lang') != 'en' ? __('신장 (cm)') : __('Height (cm)') }}</td>
                <td style="background-color:#1E3142;color:#FFFFFF">${patient.height_cm}</td>
                <td style="background-color:#324D65;color:#FFFFFF">{{ config('app.lang') != 'en' ? __('비상연락처 2') : __('Emergency Contact 2') }}</td>
                <td style="background-color:#1E3142;color:#FFFFFF">${patient.emergency_contact_2}</td>
            </tr>
            <tr>
                <td>{{ config('app.lang') != 'en' ? __('체중 (kg)') : __('Weight (kg)') }}</td>
                <td style="background-color:#1E3142;color:#FFFFFF">${patient.weight_kg}</td>
                <td style="background-color:#324D65;color:#FFFFFF">{{ config('app.lang') != 'en' ? __('입원일자') : __('Admission Date') }}</td>
                <td style="background-color:#1E3142;color:#FFFFFF">${patient.created_at}</td>
            </tr>
            <tr>
                <td>{{ config('app.lang') != 'en' ? __('입원실') : __('Room') }}</td>
                <td style="background-color:#1E3142;color:#FFFFFF">${patient.hospital_room}</td>
                <td style="background-color:#324D65;color:#FFFFFF">{{ config('app.lang') != 'en' ? __('퇴원일자') : __('Discharge Date') }}</td>
                <td style="background-color:#1E3142;color:#FFFFFF">${patient.discharge_date ? patient.discharge_date : '-'}</td>
            </tr>
            <tr>
                <td>{{ config('app.lang') != 'en' ? __('주치의') : __('Attending Doctor') }}</td>
                <td style="background-color:#1E3142;color:#FFFFFF">${patient.doctor_in_charge ? patient.doctor_in_charge.name : '-'}</td>
                <td style="background-color:#324D65;color:#FFFFFF">{{ config('app.lang') != 'en' ? __('아이디') : __('ID') }}</td>
                <td style="background-color:#1E3142;color:#FFFFFF">${patient.user_customer ? patient.user_customer.email : '-'}</td>
            </tr>
            <tr>
                <td>{{ config('app.lang') != 'en' ? __('비밀번호') : __('Password') }}</td>
                <td style="background-color:#1E3142;color:#FFFFFF">${patient.user_customer ? '**********' : '-'}</td>
                <td style="background-color:#324D65;color:#FFFFFF"></td>
                <td style="background-color:#1E3142;color:#FFFFFF"></td>
            </tr>
            `;
            document.getElementById('patient-details').innerHTML = detailsHtml;
            // document.getElementById('selected-user-form').style.display = 'block';
            // document.getElementById('not-selected-user-form').style.display = 'none';
            // getChart(data.chartData);

            // getHealthMeasurementHistory(data.patientMeasurementsInfo);
            defaultValuesMeasurementSetting = data.defaultValuesMeasurementSetting;
            getSettingMeasurement(data.measurementSettingPatient);

        })
        .catch(error => console.error('Error:', error));
}


function drawChart(data, type) {
    let measurementType = `{{ config('app.lang') != 'en' ? __('혈압') : __('Blood Pressure') }}`;
    if (type === 'blood_pressure') {
        measurementType = `{{ config('app.lang') != 'en' ? __('혈압') : __('Blood Pressure') }}`;
        const avg = data.avg_daily ? data.avg_daily.split('/') : ['-', '-'];
        avgChartMap(avg[0], avg[1], type, 'MMHG');
    } else if (type === 'body_temperature') {
        measurementType = `{{ config('app.lang') != 'en' ? __('체온') : __('Body temperature') }}`;
        avgChartMap(data.avg_daily, null, type, '°C');
    } else if (type === 'heart_rate') {
        measurementType = `{{ config('app.lang') != 'en' ? __('심박수') : __('Heart rate') }}`;
        avgChartMap(data.avg_daily, null, type, 'BPM');
    } else if (type === 'respiratory_rate') {
        measurementType = `{{ config('app.lang') != 'en' ? __('호흡수') : __('Respiratory rate') }}`;
        avgChartMap(data.avg_daily, null, type, '회');
    } else if (type === 'oxygen_saturation') {
        measurementType = `{{ config('app.lang') != 'en' ? __('산소') : __('Oxygen') }}`;
        avgChartMap(data.avg_daily, null, type, '%');
    } else if (type === 'ecg') {
        measurementType = `{{ config('app.lang') != 'en' ? __('심전도') : __('ECG') }}`;
        avgChartMap(data.avg_daily, null, type, 'BPM');
    }

    var canvas = document.getElementById('chartCanvas');
    var ctx = canvas.getContext('2d');

    // Set canvas width and height to match the container's size
    canvas.width = canvas.clientWidth;
    canvas.height = canvas.clientHeight;

    // Clear previous chart
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    const dataArray = Object.entries(data)
        .filter(([key, value]) => key !== 'avg_daily')
        .map(([key, value]) => ({
            time: key,
            data: value
        }));
    const timeArray = dataArray.map(entry => entry.time);

    var labels = timeArray;
    var values1 = [];
    var values2 = [];
    var label1 = '';
    var label2 = '';
    var colors1 = [];
    var colors2 = [];

    labels.forEach(function(interval) {
        var entry = data[interval];
        if (entry) {
            switch (type) {
                case 'blood_pressure':
                    values1.push(entry.systolic);
                    values2.push(entry.diastolic);
                    label1 = 'Systolic';
                    label2 = 'Diastolic';
                    colors1.push(entry.color_state_systolic);
                    colors2.push(entry.color_state_diastolic);
                    break;
                case 'heart_rate':
                    values1.push(entry.bpm);
                    label1 = 'Heart Rate';
                    colors1.push(entry.color_state);
                    break;
                case 'oxygen_saturation':
                    values1.push(entry.saturation);
                    label1 = 'Oxygen Saturation';
                    colors1.push(entry.color_state);
                    break;
                case 'body_temperature':
                    values1.push(entry.temperature);
                    label1 = 'Body Temperature';
                    colors1.push(entry.color_state);
                    break;
                case 'respiratory_rate':
                    values1.push(entry.rate);
                    label1 = 'Respiratory Rate';
                    colors1.push(entry.color_state);
                    break;
                case 'ecg':
                    values1.push(entry.bpm);
                    label1 = 'ECG';
                    colors1.push(entry.color_state);
                    break;
            }
        } else {
            values1.push(null);
            if (values2.length > 0) {
                values2.push(null);
            }
            colors1.push(null);
            if (colors2.length > 0) {
                colors2.push(null);
            }
        }
    });

    var yMin = 0;
    var yMax = Math.max(...values1.concat(values2).filter(val => val !== null));
    if (type === 'blood_pressure') {
        yMin = 50;
        yMax = 200;
    }

    var barWidth = 5; // Adjusted bar width
    var padding = 20; // Adjusted padding
    var leftPadding = 40; // Additional padding for the left
    var rightPadding = 40; // Additional padding for the right
    var bottomPadding = 60; // Additional padding for the bottom
    var chartHeight = canvas.height - padding - bottomPadding;
    var chartWidth = canvas.width - leftPadding - rightPadding;

    // Define global offset
    var globalOffset = type === 'blood_pressure' ? 5 : 10;

    // Draw background
    ctx.fillStyle = '#1E3142';
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    // Draw Y-axis labels and grid lines
    ctx.fillStyle = '#FFFFFF';
    ctx.strokeStyle = '#4E6880';
    ctx.font = '8px Arial'; // Adjusted font size
    for (var i = yMin; i <= yMax; i += 20) {
        var y = padding + chartHeight - ((i - yMin) / (yMax - yMin)) * chartHeight;
        ctx.fillText(i, leftPadding - 30, y + 5);
        ctx.beginPath();
        ctx.moveTo(leftPadding, y);
        ctx.lineTo(leftPadding + chartWidth, y);
        ctx.stroke();
    }

    // Draw X-axis labels
    ctx.font = '8px Arial'; // Adjusted font size
    labels.forEach(function(label, index) {
        var x = leftPadding + index * (chartWidth / (labels.length -
            1)); // Ensure labels are evenly distributed
        ctx.save();
        ctx.translate(x, canvas.height - bottomPadding + 20);
        ctx.rotate(-Math.PI / 4); // Rotate labels by 45 degrees
        ctx.fillText(label, 0, 0);
        ctx.restore();
    });

    // Draw bars
    function drawBars(values, colors, offset) {
        values.forEach(function(value, index) {
            if (value !== null) {
                var x = leftPadding + index * (chartWidth / (labels.length - 1)) - (barWidth / 2) +
                    offset + globalOffset; // Add globalOffset to shift bars
                var y = padding + chartHeight - ((value - yMin) / (yMax - yMin)) * chartHeight;
                ctx.fillStyle = colors[index] || '#FFFFFF'; // Default color if color is null
                ctx.fillRect(x, y, barWidth, ((value - yMin) / (yMax - yMin)) * chartHeight);
            }
        });
    }

    if (type === 'blood_pressure') {
        // Draw systolic and diastolic bars with 2px gap
        drawBars(values1, colors1, -barWidth - 1); // Draw systolic bars
        drawBars(values2, colors2, barWidth + 1); // Draw diastolic bars
    } else {
        drawBars(values1, colors1, 0);
        if (values2.length > 0) {
            drawBars(values2, colors2, barWidth + 2);
        }
    }
}

function fetchDataAndDrawChart(type) {
    fetch(`/hospital/patient-management/inpatients/body-measurment-info/${patient_id}?activity_type=${type}`)
        .then(response => response.json())
        .then(data => {
            drawChart(data[type], type);
        })
        .catch(error => console.error('Error fetching data:', error));
}

let currentPage = {
    vitals: 1,
    history: 1
}; // Menyimpan halaman per tabel
const pageSize = 10; // Jumlah data per request
let isFetching = {
    vitals: false,
    history: false
}; // Mencegah request ganda

let currentType = null;
let currentTypeHistory = null;

// 🚀 Fetch Data untuk Tabel Pertama (Vitals)
function fetchDataAndDrawChartTable(type, isLoadMore = false) {
    if (!isLoadMore || currentType !== type) {
        currentPage.vitals = 1; // Reset halaman jika bukan load more
        if (!isLoadMore) {
            // document.getElementById('vitalsTableBody').innerHTML = ''; // Hapus data lama
        }
        currentType = type;
    }

    if (isFetching.vitals) return;
    isFetching.vitals = true;

    fetch(
            `/hospital/patient-management/inpatients/body-measurment-info-table/${patient_id}?page=${currentPage.vitals}&size=${pageSize}&activity_type=${type}`
        )
        .then(response => response.json())
        .then(data => {
            getHealthMeasurementInfo(data[type], type, isLoadMore);
            currentPage.vitals++;
            isFetching.vitals = false;
        })
        .catch(error => {
            console.error('Error fetching data:', error);
            isFetching.vitals = false;
        });
}

let measuremenBodyTypeSelected = 'blood_pressure';
let measuremenBodyHistoryTypeSelected = 'blood_pressure';

function getChart() {
    console.log('getChart measuremenBodyTypeSelected', measuremenBodyTypeSelected)
    // resetDropdownMeasurementType1()
    resetDropdownMeasurementType2()
    // Initial fetch and draw chart for blood pressure
    fetchDataAndDrawChart(measuremenBodyTypeSelected);
    fetchDataAndDrawChartTable(measuremenBodyTypeSelected)
}

function measurementTime(type) {
    measuremenBodyTypeSelected = type;
    console.log('measurementTime measuremenBodyTypeSelected', measuremenBodyTypeSelected)

    fetchDataAndDrawChart(type);
    fetchDataAndDrawChartTable(type);
}

function avgChartMap(value1, value2, type, description) {
    const avgChart = document.getElementById('avgChart');
    removeElementsByName(`avgChartCount1`);
    removeElementsByName(`avgChartCount2`);

    value1 = parseFloat(value1).toFixed(1)
    if (value1 % 1 === 0) {
        value1 = parseInt(value1);
    }
    // Buat elemen span pertama
    const span1 = document.createElement('span');
    span1.style.fontSize = '48px';
    span1.style.color = '#FFFFFF';
    span1.setAttribute('name', 'avgChartCount1');
    span1.textContent = value1;

    // Buat elemen span kedua
    const span2 = document.createElement('span');
    span2.style.fontSize = '24px';
    span2.style.color = '#FFFFFF';
    span2.setAttribute('name', 'avgChartCount2');
    if (type == 'blood_pressure') {
        value2 = parseFloat(value2).toFixed(1)
        if (value2 % 1 === 0) {
            value2 = parseInt(value2);
        }
        span2.textContent = `/ ${value2} ` + description
    } else {
        span2.textContent = description
    }

    // Tambahkan kedua elemen span ke dalam container
    avgChart.appendChild(span1);
    avgChart.appendChild(span2);
}

// 🔹 Function untuk membuat baris tabel
function generateVitalRow(name, header, key, avg, highest, lowest, state) {
    const classState = state !== '' && state !== '-' ? state.toLowerCase() : 'default';
    return `
        <tr>
            <td class="vitals-header" name="${name}">${header} (${key})</td>
        </tr>
        <tr>
            <td class="vitals-data" name="${name}Average">{{ config('app.lang') != 'en' ? __('평균') : __('Average') }} ${avg}</td>
            <td class="vitals-data" name="${name}Highest">{{ config('app.lang') != 'en' ? __('최고') : __('Highest') }} ${highest}</td>
            <td class="vitals-data" name="${name}Lowest">{{ config('app.lang') != 'en' ? __('최저') : __('Lowest') }} ${lowest}</td>
            <td class="vitals-status ${classState}" name="${name}State">${translateState(state)}</td>
        </tr>
    `;
}

// 🔹 Function untuk menerjemahkan status (Good, Caution, Danger)
function translateState(state) {
    if (state === 'good') {
        return `{{ config('app.lang') != 'en' ? __('정상') : __('Good') }}`;
    } else if (state === 'caution') {
        return `{{ config('app.lang') != 'en' ? __('주의') : __('Caution') }}`;
    } else if (state === 'danger') {
        return `{{ config('app.lang') != 'en' ? __('위험') : __('Danger') }}`;
    }
    return '-';
}

function getHealthMeasurementInfo(data, type, isLoadMore = false) {
    const tbody = document.getElementById('vitalsTableBody');
    if (!isLoadMore || currentType !== type) {
        currentPage.vitals = 1;
        if (!isLoadMore) {
            tbody.innerHTML = '';
        }
    }

    if (data) {
        // Jika bukan loadMore atau type berubah, reset tabel dan halaman
        if (!isLoadMore || currentType !== type) {
            currentPage.vitals = 1;
            if (!isLoadMore) {
                // tbody.innerHTML = ''; // Hapus isi tabel hanya jika bukan load more
                let measurementType = `{{ config('app.lang') != 'en' ? __('혈압') : __('Blood Pressure') }}`;
                if (type === 'blood_pressure') {
                    measurementType = `{{ config('app.lang') != 'en' ? __('혈압') : __('Blood Pressure') }}`;
                    const avg = data.avg_daily ? data.avg_daily.split('/') : ['-', '-'];
                    // avgChartMap(avg[0], avg[1], type, 'MMHG');
                } else if (type === 'body_temperature') {
                    measurementType = `{{ config('app.lang') != 'en' ? __('체온') : __('Body temperature') }}`;
                    // avgChartMap(data.avg_daily, null, type, '°C');
                } else if (type === 'heart_rate') {
                    measurementType = `{{ config('app.lang') != 'en' ? __('심박수') : __('Heart rate') }}`;
                    // avgChartMap(data.avg_daily, null, type, 'BPM');
                } else if (type === 'respiratory_rate') {
                    measurementType = `{{ config('app.lang') != 'en' ? __('호흡수') : __('Respiratory rate') }}`;
                    avgChartMap(data.avg_daily, null, type, '회');
                } else if (type === 'oxygen_saturation') {
                    measurementType = `{{ config('app.lang') != 'en' ? __('산소') : __('Oxygen') }}`;
                    // avgChartMap(data.avg_daily, null, type, '%');
                } else if (type === 'ecg') {
                    measurementType = `{{ config('app.lang') != 'en' ? __('심전도') : __('ECG') }}`;
                    // avgChartMap(data.avg_daily, null, type, 'BPM');
                }
            }
            currentType = type;
        }

        let measurementType = `{{ config('app.lang') != 'en' ? __('혈압') : __('Blood Pressure') }}`;
        if (type === 'blood_pressure') {
            measurementType = `{{ config('app.lang') != 'en' ? __('혈압') : __('Blood Pressure') }}`;
            const avg = data.avg_daily ? data.avg_daily.split('/') : ['-', '-'];
            // avgChartMap(avg[0], avg[1], type, 'MMHG');
        } else if (type === 'body_temperature') {
            measurementType = `{{ config('app.lang') != 'en' ? __('체온') : __('Body temperature') }}`;
            // avgChartMap(data.avg_daily, null, type, '°C');
        } else if (type === 'heart_rate') {
            measurementType = `{{ config('app.lang') != 'en' ? __('심박수') : __('Heart rate') }}`;
            // avgChartMap(data.avg_daily, null, type, 'BPM');
        } else if (type === 'respiratory_rate') {
            measurementType = `{{ config('app.lang') != 'en' ? __('호흡수') : __('Respiratory rate') }}`;
            // avgChartMap(data.avg_daily, null, type, '회');
        } else if (type === 'oxygen_saturation') {
            measurementType = `{{ config('app.lang') != 'en' ? __('산소') : __('Oxygen') }}`;
            // avgChartMap(data.avg_daily, null, type, '%');
        } else if (type === 'ecg') {
            measurementType = `{{ config('app.lang') != 'en' ? __('심전도') : __('ECG') }}`;
            // avgChartMap(data.avg_daily, null, type, 'BPM');
        }

        // Iterate through the data and create new rows
        const keys = Object.keys(data);
        keys.forEach((key, i) => {
            if (key === 'avg_daily') return; // Skip avg_daily

            const value = data[key] || {};
            let average = value.avg || '-';
            let highest = value.highest || '-';
            let lowest = value.lowest || '-';
            let state = value.state || '-';
            let headerName = measurementType;

            if (type === 'blood_pressure') {
                // Buat dua baris untuk Systolic & Diastolic
                const systolicHeader =
                    `${headerName} {{ config('app.lang') != 'en' ? __('수축기 혈압') : __('systolic') }}`;
                const diastolicHeader =
                    `${headerName} {{ config('app.lang') != 'en' ? __('이완기 혈압') : __('Diastolic') }}`;

                // Append Systolic Data
                tbody.innerHTML += generateVitalRow(
                    `vitalsTable${i + 1}Header2`,
                    systolicHeader, key,
                    value.systolic, value.highest_systolic, value.lowest_systolic, value.state_systolic
                );

                // Append Diastolic Data
                tbody.innerHTML += generateVitalRow(
                    `vitalsTable${i + 1}Header`,
                    diastolicHeader, key,
                    value.diastolic, value.highest_diastolic, value.lowest_diastolic, value.state_diastolic
                );
            } else {
                // Append Data for Other Types
                tbody.innerHTML += generateVitalRow(
                    `vitalsTable${i + 1}Header`,
                    headerName, key,
                    average, highest, lowest, state
                );
            }
        });
    }
}


function getHealthMeasurementHistory(data, type, isLoadMore = false) {
    const tbody = document.getElementById('vitals-table-body');

    // Jika bukan loadMore atau type berubah, reset tabel dan halaman
    if (!isLoadMore || currentTypeHistory !== type) {
        currentPage.history = 1;
        if (!isLoadMore) {
            tbody.innerHTML = ''; // Hapus isi tabel hanya jika bukan load more
        }
        currentTypeHistory = type;
    }

    if (data) {


        // Define a mapping between the data keys and the table headers
        const vitalsMapping = {
            blood_pressure: `{{ config('app.lang') != 'en' ? __('혈압') : __('Blood Pressure') }}`,
            heart_rate: `{{ config('app.lang') != 'en' ? __('심박수') : __('Heart Rate') }}`,
            oxygen_saturation: `{{ config('app.lang') != 'en' ? __('산소') : __('Oxygen') }}`,
            body_temperature: `{{ config('app.lang') != 'en' ? __('온도') : __('Temperature') }}`,
            respiration: `{{ config('app.lang') != 'en' ? __('호흡') : __('Respiration') }}`,
            ecg: `{{ config('app.lang') != 'en' ? __('심전도') : __('ECG') }}`,
        };

        let measurementType = vitalsMapping[type] || vitalsMapping['blood_pressure'];

        // Iterate through the data and create new rows
        const keys = Object.keys(data);
        keys.forEach((key, i) => {
            if (key === 'avg_daily') return; // Skip avg_daily

            const value = data[key] || {};
            let average = value.avg || '-';
            let highest = value.highest || '-';
            let lowest = value.lowest || '-';
            let state = value.state || '-';
            let headerName = measurementType;

            if (type === 'blood_pressure') {
                // Buat dua baris untuk Systolic & Diastolic
                const systolicHeader =
                    `${headerName} {{ config('app.lang') != 'en' ? __('수축기 혈압') : __('systolic') }}`;
                const diastolicHeader =
                    `${headerName} {{ config('app.lang') != 'en' ? __('이완기 혈압') : __('Diastolic') }}`;

                // Append Systolic Data
                tbody.innerHTML += generateVitalRow(
                    `vitalsTable${i + 1}Header2`,
                    systolicHeader, key,
                    value.systolic, value.highest_systolic, value.lowest_systolic, value.state_systolic
                );

                // Append Diastolic Data
                tbody.innerHTML += generateVitalRow(
                    `vitalsTable${i + 1}Header`,
                    diastolicHeader, key,
                    value.diastolic, value.highest_diastolic, value.lowest_diastolic, value.state_diastolic
                );
            } else {
                // Append Data for Other Types
                tbody.innerHTML += generateVitalRow(
                    `vitalsTable${i + 1}Header`,
                    headerName, key,
                    average, highest, lowest, state
                );
            }
        });
    }
}


// 🚀 Fetch Data untuk Tabel Kedua (History)
function fetchGetHealthMeasurementHistory(type, isLoadMore = false) {
    measuremenBodyHistoryTypeSelected = type;

    if (!isLoadMore || currentTypeHistory !== type) {
        currentPage.history = 1; // Reset halaman jika bukan load more
        if (!isLoadMore) {
            // document.getElementById('vitals-table-body').innerHTML = ''; // Hapus data lama
        }
        currentTypeHistory = type;
    }

    if (isFetching.history) return;
    isFetching.history = true;

    const startDate = document.getElementById("datepicker2-1").value;
    const endDate = document.getElementById("datepicker2-2").value;

    fetch(
            `/hospital/patient-management/inpatients/body-measurment-info-table/${patient_id}?start_date_range=${startDate}&end_date_range=${endDate}&page=${currentPage.history}&size=${pageSize}&activity_type=${type}`
        )
        .then(response => response.json())
        .then(data => {
            getHealthMeasurementHistory(data[type], type, isLoadMore);
            currentPage.history++;
            isFetching.history = false;
        })
        .catch(error => {
            console.error('Error:', error);
            isFetching.history = false;
        });
}


// 🚀 Infinite Scroll untuk Tabel Vitals
document.getElementById('vitalsTableBody').parentNode.addEventListener('scroll', function() {
    if (this.scrollTop + this.clientHeight >= this.scrollHeight - 10 && !isFetching.vitals) {
        fetchDataAndDrawChartTable(currentType, true);
    }
});

// 🚀 Infinite Scroll untuk Tabel History
document.getElementById('vitals-table-body').parentNode.addEventListener('scroll', function() {
    if (this.scrollTop + this.clientHeight >= this.scrollHeight - 10 && !isFetching.history) {
        fetchGetHealthMeasurementHistory(currentTypeHistory, true);
    }
});

function resetSettingMeasurement() {
    setValues(defaultValuesMeasurementSetting);
}

function getSettingMeasurement(measurementSettingPatient) {
    setValues(measurementSettingPatient);
}

function saveMeasurementSettings() {
    const inputs = document.querySelectorAll('.set-measurement-input');
    const data = {
        patient_id: patient_id,
    };

    inputs.forEach(input => {
        data[input.name] = input.value;
    });

    fetch('/hospital/patient-management/inpatients/set-measurement-patient', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                $('#successPopupModal').modal('show');
            } else {
                $('#errorFailedPopupModal').modal('show');
            }
        })
        .catch(error => console.error('Error:', error));
}

function setValues(values) {
    for (const key in values) {
        if (values.hasOwnProperty(key)) {
            if (key.includes("good") || key.includes("danger") || key.includes("caution")) {
                document.querySelector(`input[name="${key}"]`).value = values[key];
            }
        }
    }
}
document.getElementById('delete-selected').addEventListener('click', function() {
    const selectedIds = Array.from(document.querySelectorAll('.checkbox-click:checked')).map(cb => cb
        .value);

    if (selectedIds.length > 0) {
        // Show the confirmation modal
        $('#deletedConfirmationPopUpModal').modal('show');

        // Add a one-time event listener for the confirm button
        document.getElementById('confirmed').addEventListener('click', function confirmHandler() {
            fetch('/hospital/patient-management/inpatients', {
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

function downloadExcelPerDateRange() {
    let startDate = document.getElementById('datepicker2-1').value;
    let endDate = document.getElementById('datepicker2-2').value;

    return exportExcel(`{{ url('hospital/patient-management/patient/export-data-vital-per-day') }}/` + patient_id +
        `?start_date_range=${startDate}&end_date_range=${endDate}&type=date`);
}
</script>

@endsection