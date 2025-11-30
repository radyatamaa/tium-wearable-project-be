@php
$pageTitle = config('app.lang') != 'en' ? __('대시보드') : __('Emergency History');
@endphp

@extends('layouts.app', ['page' => $pageTitle, 'pageSlug' => 'emergency_history'])

@section('content-fluid')
<div class="setting-public-welfare-container">
    <div class="setting-public-welfare-navbar">
        <div class="setting-public-welfare-navbar-item setting-public-welfare-navbar-item-active"
            onclick="switchNav('smart-watch')">
            {{ config('app.lang') != 'en' ? __('긴급알림이력') : __('Emergency notification history') }}
        </div>
    </div>
    <div class="setting-public-welfare-content" id="smart-watch" style="width:100%;display:block;">
        <div class="custom-container">
            <div class="custom-card custom-left-card" id="left-card">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header card-header-primary d-flex justify-content-between"
                                style="--header-background-color: #1E3142;">
                                <div class="smartwatch-search-container">
                                    <div class="smartwatch-search-top">
                                        <form id="filter-form" method="GET" action="/public-welfare/emergency-history"
                                            class="d-flex justify-content-between w-100">
                                            <div class="smartwatch-search-input-container">
                                                <input type="text" class="smartwatch-search-input" id="searchInput"
                                                    name="search"
                                                    placeholder="{{ config('app.lang') != 'en' ? __('긴급알림이력을 실시간으로 확인할 수 있습니다.') : __('You can check the history of emergency notification in real time.') }}">
                                            </div>
                                            <button type="submit"
                                                class="smartwatch-search-button">{{ config('app.lang') != 'en' ? __('검색') : __('Search') }}<i
                                                    class="fas fa-search"></i></button>

                                        </form>
                                    </div>
                                    <div class="smartwatch-search-bottom">
                                        <form id="filter-form-pw-emg-h" method="GET"
                                            action="/public-welfare/emergency-history"
                                            class="d-flex justify-content-between w-100">
                                            <div class="btn-group-left">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-warning dropdown-toggle"
                                                        data-toggle="dropdown" aria-haspopup="true"
                                                        aria-expanded="false" style="background-color:#4E6880"
                                                        id="full_term">
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
                                                        <i class="fas fa-calendar-alt"
                                                            onclick="toggleCalendar('calendar-pw-page-emg-history-1')"></i>
                                                        <input type="text" id="datepicker-pw-page-emg-history-1"
                                                            value="YYYY-MM-DD" name="start_date_range">
                                                        <div class="calendar" id="calendar-pw-page-emg-history-1"></div>
                                                    </div>
                                                </div>
                                                <div class="btn-group">
                                                    <div class="date-picker-container" style="background-color:#4E6880">
                                                        <i class="fas fa-calendar-alt"
                                                            onclick="toggleCalendar('calendar-pw-page-emg-history-2')"></i>
                                                        <input type="text" id="datepicker-pw-page-emg-history-2"
                                                            value="YYYY-MM-DD" name="end_date_range">
                                                        <div class="calendar" id="calendar-pw-page-emg-history-2"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" class="smartwatch-refresh-button"
                                                onclick="refreshPage()"><img
                                                    src="{{ asset('black') }}/icons/general/refresh.svg"></button>
                                        </form>

                                    </div>


                                </div>
                            </div>
                            <div class="card-body">
                                <div class="styled-table-public-welfare-container">
                                    <table class="styled-table-public-welfare" style="border-spacing:-10px-15px;">
                                        <thead>
                                            <tr>
                                                <th>{{ config('app.lang') != 'en' ? __('알림번호') : __('Alert Number') }}
                                                </th>
                                                <th>{{ config('app.lang') != 'en' ? __('알림시간') : __('Alert Time') }}
                                                </th>
                                                <th>{{ config('app.lang') != 'en' ? __('소속') : __('Affiliations') }}
                                                </th>
                                                <th>{{ config('app.lang') != 'en' ? __('사용자명') : __('User Name') }}</th>
                                                <th>{{ config('app.lang') != 'en' ? __('구분') : __('Category') }}</th>
                                                <th>{{ config('app.lang') != 'en' ? __('내용') : __('Content') }}</th>
                                                <th>{{ config('app.lang') != 'en' ? __('확인시간') : __('Confirmation Time') }}
                                                </th>
                                                <th>{{ config('app.lang') != 'en' ? __('조치시간') : __('Action Time') }}
                                                </th>
                                                <th>{{ config('app.lang') != 'en' ? __('사용자정보') : __('User Information') }}
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($datas as $data)
                                            <tr>
                                                <td>{{ $loop->iteration + ($datas->currentPage() - 1) * $datas->perPage() }}
                                                </td>
                                                <td>{{ $data->notification_time }}</td>
                                                <td>{{$data->affiliations}}</td>
                                                <td>{{$data->name}}</td>
                                                <td>
                                                    @php
                                                    $assortmentArray = explode(',', $data->assortment)
                                                    @endphp

                                                    @foreach ($assortmentArray as $assortment)
                                                    @if($assortment == 'danger')
                                                    <button class="status-button danger" disabled>
                                                        위험</button>
                                                    @elseif(
                                                    $assortment == 'caution' || $assortment ==
                                                    'warning'
                                                    )
                                                    <button class="status-button caution" disabled>
                                                        주의</button>
                                                    @else
                                                    <button class="status-button normal" disabled>
                                                        정상</button>
                                                    @endif
                                                    @endforeach

                                                </td>
                                                <td>{{ $data->main_symptom }}
                                                </td>
                                                <td>{{$data->confirmation_time}}</td>
                                                <td>{{$data->action_time}}</td>
                                                <td class="td-actions">
                                                    <button class="btn btn-warning btn-sm" type="button">
                                                        <a href="/public-welfare/patient-management/{{$data->user_customer_id}}"
                                                            style="color:#ffffff">{{ config('app.lang') != 'en' ? __('사용자정보') : __('User Information') }}</a>

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
            </div>
        </div>
    </div>
</div>
<script>
function refreshPage() {
    let newPath = '/public-welfare/emergency-history';
    window.location.href = newPath;
}
</script>
@endsection