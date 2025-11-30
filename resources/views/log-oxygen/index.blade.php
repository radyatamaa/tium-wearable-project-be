@php
$pageTitle = config('app.lang') != 'en' ? __('혈압 기록') : __('Blood Pressure Log');
@endphp

@extends('layouts.app', ['page' => $pageTitle, 'pageSlug' => 'log_oxygen'])

@section('content-fluid')
<!-- filters -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-primary d-flex justify-content-between"
                style="--header-background-color: #1E3142; background-color: var(--header-background-color);padding: 5px;">
                <div class="header-hospital-filter-container">
                    <div class="header-hospital-filter-row">
                        <form id="filter-form" method="GET" action="/log-oxygen"
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
                                        {{ __('스마트워치코드') }}
                                        @else
                                        {{ __('Device Address') }}
                                        @endif
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="#" data-value="title">
                                            @if (config('app.lang') != 'en')
                                            {{ __('스마트워치코드') }}
                                            @else
                                            {{ __('Device Address') }}
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
                                <th>{{ config('app.lang') != 'en' ? __('사용자/환자') : __('User/Patient'); }}</th>
                                <th>{{ config('app.lang') != 'en' ? __('스마트워치코드') : __('Smartwatch Code'); }}</th>
                                <th>{{ config('app.lang') != 'en' ? __('일련번호 게이트웨이') : __('Serial Number Gateway'); }}
                                </th>
                                <th>{{ config('app.lang') != 'en' ? __('기록기') : __('Recorder At'); }}</th>
                                <th>{{ config('app.lang') != 'en' ? __('생성') : __('Created At'); }}</th>
                                <th>{{ config('app.lang') != 'en' ? __('게이트웨이') : __('Gateway'); }}</th>
                                <th>{{ config('app.lang') != 'en' ? __('산소') : __('Oxygen'); }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($datas as $data)
                            <tr>
                                <td class="text-center"><input type="checkbox" class="cscenter-checkbox"
                                        data-id="{{ $data['id'] }}" value="{{ $data['id'] }}"></td>
                                <td>{{ $loop->iteration + ($datas->currentPage() - 1) * $datas->perPage() }}
                                </td>

                                @if($data['userCustomer'])
                                <td>{{ $data['userCustomer']['name']; }}</td>
                                @elseif($data['patient'])
                                <td>{{ $data['patient']['name']; }}</td>
                                @else
                                <td>-알 수 없음-</td>
                                @endif
                                <td>{{ $data['device_address']; }}</td>
                                @if(isset($data['serial_number_gateway']))
                                <td>{{ $data['serial_number_gateway'] == null ? '-' : $data['serial_number_gateway']; }}
                                </td>
                                @else
                                <td>-</td>
                                @endif
                                <td>{{ $data['recorded_at']; }}</td>
                                <td>{{ $data['recorded_at']; }}</td>
                                @if(isset($data['is_coming_from_gateway']))
                                <td>{{ $data['is_coming_from_gateway'] == '1' ? 'Y' : 'N'; }}</td>
                                @else
                                <td>N</td>
                                @endif
                                <td>{{ $data['saturation']; }}</td>
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
<!--  -->
@endsection