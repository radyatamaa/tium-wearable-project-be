@php
$pageTitle = config('app.lang') != 'en' ? __('환자 보고서') : __('Patient Report');
@endphp

@extends('layouts.app', ['page' => $pageTitle, 'pageSlug' => 'patient-report'])

@section('content-fluid')
<!-- filters -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-primary d-flex justify-content-between"
                style="--header-background-color: #1E3142; background-color: var(--header-background-color);padding: 5px;">
                <div class="header-hospital-filter-container">
                    <div class="header-hospital-filter-row">
                        <form id="filter-form" method="GET" action="/patient-report"
                            class="d-flex justify-content-between w-100">
                            <div class="header-hospital-filter-left">
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
                            </div>
                            <div class="header-hospital-filter-right">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false" id="searchByButton">
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
<!--  -->

<!-- table list -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-primary d-flex justify-content-between"
                style="--header-background-color: #1E3142;">
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
                </div>
            </div>
            <div class="card-body">
                <div class="styled-table-container">
                    <table class="styled-table">
                        <thead>
                            <tr>
                                <th class="text-center">{{ config('app.lang') != 'en' ? __('선택') : __('Select'); }}</th>
                                <th>{{ config('app.lang') != 'en' ? __('번호') : __('Number'); }}</th>
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
                                <th>
                                    {{ config('app.lang') != 'en' ? __('업체기관명') : __('Company Name') }}
                                </th>
                                <th>
                                    {{ config('app.lang') != 'en' ? __('대표자명') : __('Representative Name') }}
                                </th>
                                <th>{{ config('app.lang') != 'en' ? __('생성') : __('Created At'); }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($datas as $data)
                            <tr>
                                <td class="text-center"><input type="checkbox" class="cscenter-checkbox"
                                        data-id="{{ $data['id'] }}" value="{{ $data['id'] }}"></td>
                                <td>{{ $loop->iteration + ($datas->currentPage() - 1) * $datas->perPage() }}
                                </td>

                                <td>{{$data->name}}</td>
                                <td>{{$data->patient_code}}</td>
                                <td>{{$data->gender}}</td>
                                <td>{{$data->age}}</td>
                                <td>{{$data->hospital_room}}</td>
                                <td>{{$data->doctor_in_charge}}</td>
                                <td>{{$data->nurse_in_charge}}</td>
                                <td>{{$data->subscriptionInformationGeneralPurpose->organization_name}}</td>
                                <td>{{$data->subscriptionInformationGeneralPurpose->name_of_representative}}</td>
                                <td>{{$data->created_at}}</td>
                                <td class="td-actions">
                                    <button class="btn btn-warning btn-sm" type="button"
                                        onclick="downloadExport({{ $data->id }})">
                                        {{ config('app.lang') != 'en' ? __('다운로드 내보내기') : __('Export') }}
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
</div>

<!-- modal register -->
<div class="modal fade" id="patientExportModal">
    <div class="modal-dialog modal-lg"
        style="max-width:800px;display: flex;align-items: flex-start; justify-content: center; min-height: 150vh;">
        <div class="modal-content modal-hospital-content">

            <!-- Modal Header -->
            <div class="modal-header"
                style="background-color:#3CBAFF;padding:20px 24px;align-items: center;justify-content: center;position: relative;">
                <h4 class="modal-title" style="color:#FFFFFF">
                    {{ config('app.lang') != 'en' ? __('다운로드 내보내기 측정') : __('download export measurement') }}
                </h4>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <div class="text-center" style="margin-bottom:20px">
                    <h4 class="modal-title" style="color:#FFFFFF">
                        {{ config('app.lang') != 'en' ? __('날짜 범위를 선택하세요') : __('select date range') }}
                    </h4>
                </div>
                <div class="modal-hospital-form-row">
                    <label
                        for="start-date-modal">{{ config('app.lang') != 'en' ? __('시작 날짜') : __('start date') }}</label>
                    <div class="custom-date-input">
                        <input type="datetime-local" id="start-date-modal">
                    </div>
                    <label for="end-date-modal">{{ config('app.lang') != 'en' ? __('종료일') : __('end date') }}</label>
                    <div class="custom-date-input">
                        <input type="datetime-local" id="end-date-modal">
                    </div>
                </div>
                <div class="modal-hospital-form-row wide">
                    <label for="export-type">{{ config('app.lang') != 'en' ? __('수출 유형') : __('Export type') }}</label>
                    <select id="export-type" class="required-input" style="width: calc(100% - 40px);">
                        <option value="date_with_minute">분</option>
                        <option value="date">시간</option>
                    </select>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-primary modal-hospital-btn-storage" style="padding:10px 160px"
                    onclick="downloadExcelPerDateRange()">{{ config('app.lang') != 'en' ? __('내보내기') : __('Export') }}</button>
                <button type="button" class="btn btn-info modal-hospital-btn-cancellation" style="padding:10px 160px"
                    data-dismiss="modal">{{ config('app.lang') != 'en' ? __('취소') : __('Cancel') }}</button>
            </div>

        </div>
    </div>

</div>
<!--  -->

<script>
let patient_id = null;

function downloadExport(id) {
    patient_id = id
    $('#patientExportModal').modal('show');
}

function downloadExcelPerDateRange() {
    let startDate = document.getElementById('start-date-modal').value;
    let endDate = document.getElementById('end-date-modal').value;
    let exportType = document.getElementById('export-type').value;

    return exportExcel(`{{ url('patient-report/export-data') }}/` + patient_id +
        `?start_date_range=${startDate}&end_date_range=${endDate}&type=${exportType}`);
}
</script>
@endsection