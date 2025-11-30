@php
    $pageTitle = config('app.lang') != 'en' ? __('대시보드') : __('Patient Management');
@endphp

@extends('layouts.app', ['page' => $pageTitle, 'pageSlug' => 'patient_management'])

@section('content-fluid')
<div class="setting-general-purpose-container">
    <div class="custom-container">
        <div class="custom-card custom-left-card" id="left-card" style="max-width: 50%">
            <div class="row">
                <div class="col-md-12">
                    <div class="setting-general-purpose-card"
                        style="margin-left:0px;max-width:8000px;margin-right: 0px;">
                        <div class="setting-general-purpose-card-content">
                            <div class="setting-general-purpose-card-title">
                                {{ config('app.lang') != 'en' ? __('사용자정보') : __('User Information') }}
                            </div>
                            <div class="setting-general-purpose-card-actions">
                                <span class="setting-general-purpose-refresh-icon" id="reset"
                                    onclick="refreshPage()"><img
                                        src="{{ asset('black') }}/icons/general/refresh.svg"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Navbar -->
                <div class="col-md-12 mt-3">
                    <div class="card mb-3" style="background-color: #192734; color: #FFFFFF;">
                        <div class="nav-bar-custom" style="background-color: #192734;">
                            <div class="nav-item active" data-target="basicInformation">
                                <div class="title">
                                    {{ config('app.lang') != 'en' ? __('기본정보') : __('Basic Information') }}
                                </div>
                            </div>
                            <div class="nav-item" data-target="bodyMeasurementInformation">
                                <div class="title">
                                    {{ config('app.lang') != 'en' ? __('신체측정정보') : __('Body Measurement Information') }}
                                </div>
                            </div>
                            <div class="nav-item" data-target="measurementInformation"
                                onclick="fetchMeasurementGeneralPurposeHistory({{$id}})">
                                <div class="title">
                                    {{ config('app.lang') != 'en' ? __('측정정보이력') : __('Measurement Information') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Patient Info -->
                <div class="col-md-12 content-section active" id="basicInformation">
                    <div class="patient-management-container">
                        <div class="patient-management-body" style="margin-left:-20px;margin-right:-25px">
                            <div class="patient-info-column">
                                <table class="patient-info-table">
                                    <tbody>
                                        <tr>
                                            <td>{{ config('app.lang') != 'en' ? __('사용자명') : __('Name') }}</td>
                                            <td>{{$data['basic_information']['name']}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ config('app.lang') != 'en' ? __('성별') : __('Gender') }}</td>
                                            <td>{{$data['basic_information']['gender']}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ config('app.lang') != 'en' ? __('신장(cm)') : __('Height(cm)') }}</td>
                                            <td>{{$data['basic_information']['height']}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ config('app.lang') != 'en' ? __('스마트워치코드') : __('Smartwatch Code') }}
                                            </td>
                                            <td>{{$data['basic_information']['smartwatch_code']}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="patient-info-column">
                                <table class="patient-info-table">
                                    <tbody>
                                        <tr>
                                            <td>{{ config('app.lang') != 'en' ? __('나이') : __('Age') }}</td>
                                            <td>{{$data['basic_information']['age']}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ config('app.lang') != 'en' ? __('체중(kg)') : __('Weight(kg)') }}</td>
                                            <td>{{$data['basic_information']['weight']}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ config('app.lang') != 'en' ? __('연락처') : __('Contact') }}</td>
                                            <td>{{$data['basic_information']['contact']}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ config('app.lang') != 'en' ? __('비상연락처') : __('Emergency Contact') }}
                                            </td>
                                            <td>{{$data['basic_information']['emergency_contact']}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- Save Button -->
                        <div class="text-center">
                            <button type="button" class="btn btn-primary" id="emergency-button"
                                style="padding:10px 105px">{{ config('app.lang') != 'en' ? __('긴급알림이력') : __('Emergency Notifcation History') }}</button>
                            <button type="button" class="btn btn-primary" id="patient-call-button"
                                style="padding:10px 105px">{{ config('app.lang') != 'en' ? __('환자호출이력') : __('Patient Call History') }}</button>
                        </div>
                    </div>
                </div>
                <!-- Body Measurement Information -->
                <div class="col-md-12 content-section" id="bodyMeasurementInformation">
                    @include('general-purpose.patient-management.body-measurement-information')
                </div>

                <!-- Measurement Information -->
                <div class="col-md-12 content-section" id="measurementInformation">
                    @include('general-purpose.patient-management.measurement-information-history')
                </div>

            </div>
        </div>
        <div class="custom-resizer" id="resizer"></div>
        <!-- emergency call history -->
        <div class="custom-card custom-right-card" id="right-card-emg" style="display:none;min-width: 50%">
            <div class="setting-general-purpose-navbar" style="min-width:104%;margin-top:-20px">
                <div class="setting-general-purpose-navbar-item setting-general-purpose-navbar-item-active">
                    {{ config('app.lang') != 'en' ? __('긴급알림이력') : __('Emergency Notification History') }}
                </div>
            </div>
            <div class="smartwatch-search-container" style="margin-top:20px">
                <div class="smartwatch-search-top">
                    <div class="smartwatch-search-input-container">
                        <input type="text" class="smartwatch-search-input" id="searchInputEmg" name="search"
                            placeholder="{{ config('app.lang') != 'en' ? __('긴급알림이력을 실시간으로 확인할 수 있습니다.') : __('You can check the history of emergency notification in real time.') }}">
                    </div>
                    <button type="button" class="smartwatch-search-button"
                        id="searchButtonEmg">{{ config('app.lang') != 'en' ? __('검색') : __('Search') }}<i
                            class="fas fa-search"></i></button>
                </div>
                <div class="smartwatch-search-bottom">
                    <div class="btn-group-left">
                        <div class="btn-group">
                            <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false" style="background-color:#4E6880"
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
                                    onclick="toggleCalendar('calendar-gp-emergency-history-1')"></i>
                                <input type="text" id="datepicker-gp-emergency-history-1" value="YYYY-MM-DD"
                                    name="start_date_range">
                                <div class="calendar" id="calendar-gp-emergency-history-1"></div>
                            </div>
                        </div>
                        <div class="btn-group">
                            <div class="date-picker-container" style="background-color:#4E6880">
                                <i class="fas fa-calendar-alt"
                                    onclick="toggleCalendar('calendar-gp-emergency-history-2')"></i>
                                <input type="text" id="datepicker-gp-emergency-history-2" value="YYYY-MM-DD"
                                    name="end_date_range">
                                <div class="calendar" id="calendar-gp-emergency-history-2"></div>
                            </div>
                        </div>
                    </div>
                    <button class="smartwatch-refresh-button" onclick="refreshPageEmergenyHistory()"><img
                            src="{{ asset('black') }}/icons/general/refresh.svg"></button>
                </div>

            </div>
            <div class="styled-table-general-purpose-container">
                <table class="styled-table-general-purpose" style="border-spacing:-10px-15px;" id="detailsTable">
                    <thead>
                        <tr>
                            <th>{{ config('app.lang') != 'en' ? __('알림번호') : __('Alert Number') }}</th>
                            <th>{{ config('app.lang') != 'en' ? __('알림시간') : __('Alert Time') }}</th>
                            <th>{{ config('app.lang') != 'en' ? __('소속') : __('Affiliations') }}</th>
                            <th>{{ config('app.lang') != 'en' ? __('사용자명') : __('User Name') }}</th>
                            <th>{{ config('app.lang') != 'en' ? __('구분') : __('Category') }}</th>
                            <th>{{ config('app.lang') != 'en' ? __('내용') : __('Content') }}</th>
                            <th>{{ config('app.lang') != 'en' ? __('확인시간') : __('Confirmation Time') }}</th>
                            <th>{{ config('app.lang') != 'en' ? __('조치시간') : __('Action Time') }}</th>
                        </tr>
                    </thead>
                    <tbody id="detailsBodyEmg">
                        <!-- AJAX-loaded content will go here -->
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center" id="paginationLinksEmg">
                <!-- AJAX-loaded pagination links will go here -->
            </div>
        </div>
        <!--  -->

        <!-- patient call history -->
        <div class="custom-card custom-right-card" id="right-card-pc" style="display:none;min-width: 50%">
            <div class="setting-general-purpose-navbar" style="min-width:104%;margin-top:-20px">
                <div class="setting-general-purpose-navbar-item setting-general-purpose-navbar-item-active">
                    {{ config('app.lang') != 'en' ? __('환자호출이력') : __('Patient Call History') }}
                </div>
            </div>
            <div class="smartwatch-search-container" style="margin-top:20px">
                <div class="smartwatch-search-top">
                    <div class="smartwatch-search-input-container">
                        <input type="text" class="smartwatch-search-input" id="searchInputPc" name="search"
                            placeholder="{{ config('app.lang') != 'en' ? __('환자호출이력 실시간으로 확인할 수 있습니다.') : __('You can check the history of patient notification in real time.') }}">
                    </div>
                    <button type="button" class="smartwatch-search-button"
                        id="searchButtonPc">{{ config('app.lang') != 'en' ? __('검색') : __('Search') }}<i
                            class="fas fa-search"></i></button>
                </div>
                <div class="smartwatch-search-bottom">
                    <div class="btn-group-left">
                        <div class="btn-group">
                            <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false" style="background-color:#4E6880"
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
                                    onclick="toggleCalendar('calendar-gp-patient-call-history-1')"></i>
                                <input type="text" id="datepicker-gp-patient-call-history-1" value="YYYY-MM-DD"
                                    name="start_date_range">
                                <div class="calendar" id="calendar-gp-patient-call-history-1"></div>
                            </div>
                        </div>
                        <div class="btn-group">
                            <div class="date-picker-container" style="background-color:#4E6880">
                                <i class="fas fa-calendar-alt"
                                    onclick="toggleCalendar('calendar-gp-patient-call-history-2')"></i>
                                <input type="text" id="datepicker-gp-patient-call-history-2" value="YYYY-MM-DD"
                                    name="end_date_range">
                                <div class="calendar" id="calendar-gp-patient-call-history-2"></div>
                            </div>
                        </div>
                    </div>
                    <button class="smartwatch-refresh-button" onclick="refreshPagePatientCallHistory()"><img
                            src="{{ asset('black') }}/icons/general/refresh.svg"></button>
                </div>

            </div>
            <div class="styled-table-general-purpose-container">
                <table class="styled-table-general-purpose" style="border-spacing:-10px-15px;" id="detailsTable">
                    <thead>
                        <tr>
                            <th>{{ config('app.lang') != 'en' ? __('알림번호') : __('Alert Number') }}</th>
                            <th>{{ config('app.lang') != 'en' ? __('알림시간') : __('Alert Time') }}</th>
                            <th>{{ config('app.lang') != 'en' ? __('소속') : __('Affiliations') }}</th>
                            <th>{{ config('app.lang') != 'en' ? __('사용자명') : __('User Name') }}</th>
                            <th>{{ config('app.lang') != 'en' ? __('구분') : __('Category') }}</th>
                            <th>{{ config('app.lang') != 'en' ? __('내용') : __('Content') }}</th>
                            <th>{{ config('app.lang') != 'en' ? __('확인시간') : __('Confirmation Time') }}</th>
                            <th>{{ config('app.lang') != 'en' ? __('조치시간') : __('Action Time') }}</th>
                        </tr>
                    </thead>
                    <tbody id="detailsBodyPc">
                        <!-- AJAX-loaded content will go here -->
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center" id="paginationLinksPc">
                <!-- AJAX-loaded pagination links will go here -->
            </div>
        </div>
        <!--  -->
    </div>
</div>

<script>
    let userCustomerId = `{{$data['basic_information']['user_customer_id']}}`;

    function refreshPage() {
        window.location.reload();
    }
    document.addEventListener('DOMContentLoaded', function () {
        // const tabs = document.querySelectorAll('.nav-item');
        // const sections = document.querySelectorAll('.col-md-12');
        const dropdownButton = document.getElementById('dropdownButton');
        const dropdownContent = document.getElementById('dropdownContent');
        const measurementTitle = document.querySelector('.measurement-title');
        const resizer = document.getElementById('resizer');
        const leftCard = document.getElementById('left-card');
        const rightCardEmg = document.getElementById('right-card-emg');
        const rightCardPc = document.getElementById('right-card-pc');
        let isResizing = false;

        const initialData = @json($data['body_measurement_information_chart']);

        const navItems = document.querySelectorAll('.nav-bar-custom .nav-item');
        const sections = document.querySelectorAll('.content-section');

        navItems.forEach(item => {
            item.addEventListener('click', function () {
                // Remove active class from all nav items
                navItems.forEach(nav => nav.classList.remove('active'));
                // Add active class to the clicked nav item
                item.classList.add('active');

                // Hide all sections
                sections.forEach(section => section.classList.remove('active'));
                // Show the targeted section
                const target = item.getAttribute('data-target');
                if (target === 'bodyMeasurementInformation') {
                    updateChart('temperature');
                }
                document.getElementById(target).classList.add('active');
            });
        });

        dropdownContent.querySelectorAll('a').forEach(item => {
            item.addEventListener('click', function (e) {
                e.preventDefault();
                const measurementType = this.getAttribute('data-measurement');
                measurementTitle.innerText = this.innerText;
                // Call your function to update the chart based on the selected measurement type
                updateChart(measurementType);
            });
        });

        function updateChart(measurementType) {

            const data = {
                temperature: initialData.map(item => ({
                    x: new Date(item.recorded_at),
                    y: parseFloat(item.body_temperature)
                })),
                heart_rate: initialData.map(item => ({
                    x: new Date(item.recorded_at),
                    y: parseFloat(item.heart_rate)
                })),
                respiratory_rate: initialData.map(item => ({
                    x: new Date(item.recorded_at),
                    y: parseFloat(item.respiratory_rate)
                })),
                blood_pressure: initialData.map(item => ({
                    x: new Date(item.recorded_at),
                    y: parseFloat(item.blood_pressure)
                })),
                oxygen_saturation: initialData.map(item => ({
                    x: new Date(item.recorded_at),
                    y: parseFloat(item.oxygen_saturation)
                })),
                ecg: initialData.map(item => ({
                    x: new Date(item.recorded_at),
                    y: parseFloat(item.ecg)
                }))
            };
            const chartData = data[measurementType];

            const chart = new CanvasJS.Chart("chartContainer", {
                animationEnabled: true,
                backgroundColor: "#192734", // Chart background color
                theme: "dark2",
                title: {
                    text: measurementTitle.innerText
                },
                axisX: {
                    valueFormatString: "HH:mm",
                },
                axisY: {
                    title: measurementTitle.innerText,
                    includeZero: false,
                    suffix: measurementType === 'temperature' ? "°C" : "",
                    gridColor: "#3D5264"
                },
                data: [{
                    type: "line",
                    xValueFormatString: "HH:mm",
                    yValueFormatString: "##.##",
                    dataPoints: chartData,
                    color: "#3CBAFF",
                    markerColor: "#3CBAFF"
                }]
            });
            chart.render();
        }

        resizer.addEventListener('mousedown', function (e) {
            isResizing = true;
            document.addEventListener('mousemove', onMouseMove);
            document.addEventListener('mouseup', onMouseUp);
        });

        function onMouseMove(e) {
            if (!isResizing) return;
            const offsetRight = document.body.offsetWidth - (e.clientX);
            leftCard.style.flexGrow = '0';
            leftCard.style.width = e.clientX + 'px';
            if (rightCardEmg.style.display === 'block') {
                rightCardEmg.style.flexGrow = '0';
                rightCardEmg.style.width = offsetRight + 'px';
            } else if (rightCardPc.style.display === 'block') {
                rightCardPc.style.flexGrow = '0';
                rightCardPc.style.width = offsetRight + 'px';
            }
        }

        function onMouseUp() {
            isResizing = false;
            document.removeEventListener('mousemove', onMouseMove);
            document.removeEventListener('mouseup', onMouseUp);
        }

        const emergencyButton = document.getElementById('emergency-button');
        const patientCallButton = document.getElementById('patient-call-button');

        document.getElementById('searchButtonEmg').addEventListener('click', function () {
            const searchValue = document.getElementById('searchInputEmg').value;
            loadDetailsEmergencyHistoryUser(searchValue, 1, 10); // Load details with search parameter
        });

        document.getElementById('searchButtonPc').addEventListener('click', function () {
            const searchValue = document.getElementById('searchInputPc').value;
            loadDetailsPatientCallHistoryUser(searchValue, 1, 10); // Load details with search parameter
        });

        emergencyButton.addEventListener('click', function (e) {
            e.preventDefault();
            loadDetailsEmergencyHistoryUser();
            rightCardEmg.style.display = 'block';
            rightCardPc.style.display = 'none';
        });

        patientCallButton.addEventListener('click', function (e) {
            e.preventDefault();
            loadDetailsPatientCallHistoryUser();
            rightCardPc.style.display = 'block';
            rightCardEmg.style.display = 'none';
        });

        document.addEventListener("click", function (event) {
            if (event.target.matches(".pagination a")) {
                event.preventDefault();
                const url = new URL(event.target.href);
                const page = url.searchParams.get("page");
                const limit = document.querySelector('.pagination-limit .btn').innerText.match(/\d+/)[0];

                if (rightCardEmg.style.display === 'block') {
                    loadDetailsEmergencyHistoryUser('', page, limit);
                } else if (rightCardPc.style.display === 'block') {
                    loadDetailsPatientCallHistoryUser('', page, limit);
                }


            }
        });
    });

    function loadDetailsEmergencyHistoryUser(search = '', page = 1, limit = 10) {
        const startDate = document.getElementById("datepicker-gp-emergency-history-1").value;
        const endDate = document.getElementById("datepicker-gp-emergency-history-2").value;
        let url =
            `/general-purpose/emergency-history/user_id/${userCustomerId}?page=${page}&limit=${limit}&start_date_range=${startDate}&end_date_range=${endDate}`;
        if (search) {
            url += `&search=${encodeURIComponent(search)}`;
        }

        fetch(url)
            .then(response => response.json())
            .then(data => {
                const detailsBodyEmg = document.getElementById('detailsBodyEmg');
                detailsBodyEmg.innerHTML = '';

                data.data.data.forEach((report, index) => {
                    const row = document.createElement('tr');

                    let assortmentButton = ``;
                    const assortmentArray = report.assortment.split(',');
                    assortmentArray.forEach(assortment => {
                        if (assortment === 'danger') {
                            assortmentButton +=
                                `<button class="status-button danger" disabled>위험</button>`;
                        } else if (assortment === 'caution' || assortment == 'warning') {
                            assortmentButton +=
                                `<button class="status-button caution" disabled>주의</button>`;
                        } else {
                            assortmentButton +=
                                `<button class="status-button normal" disabled>정상</button>`;
                        }
                    })

                    row.innerHTML = `
                        <td>${(data.data.current_page - 1) * data.data.per_page + index + 1}</td>
                        <td>${report.notification_time}</td>
                        <td>${report.affiliations}</td>
                        <td>${report.name}</td>
                        <td>${assortmentButton}</td>
                        <td>${report.main_symptom ?? ''}</td>
                        <td>${report.confirmation_time ?? ''}</td>
                        <td>${report.action_time ?? ''}</td>
                    `;
                    detailsBodyEmg.appendChild(row);
                });

                createPaginationWithArg(search, data.data, limit, 'loadDetailsEmergencyHistoryUser',
                    'paginationLinksEmg');
            });
    }

    function loadDetailsPatientCallHistoryUser(search = '', page = 1, limit = 10) {
        const startDate = document.getElementById("datepicker-gp-patient-call-history-1").value;
        const endDate = document.getElementById("datepicker-gp-patient-call-history-2").value;
        let url =
            `/general-purpose/patient-call-history/user_id/${userCustomerId}?page=${page}&limit=${limit}&start_date_range=${startDate}&end_date_range=${endDate}`;
        if (search) {
            url += `&search=${encodeURIComponent(search)}`;
        }

        fetch(url)
            .then(response => response.json())
            .then(data => {
                const detailsBodyPc = document.getElementById('detailsBodyPc');
                detailsBodyPc.innerHTML = '';

                data.data.data.forEach((report, index) => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${(data.data.current_page - 1) * data.data.per_page + index + 1}</td>
                        <td>${report.notification_time}</td>
                        <td>${report.affiliations}</td>
                        <td>${report.name}</td>
                        <td><button class="status-button danger" disabled>${report.main_symptom}</button></td>
                        <td>${report.content ?? ''}</td>
                        <td>${report.confirmation_time ?? ''}</td>
                        <td>${report.action_time ?? ''}</td>
                    `;
                    detailsBodyPc.appendChild(row);
                });

                createPaginationWithArg(search, data.data, limit, 'loadDetailsPatientCallHistoryUser',
                    'paginationLinksPc');
            });
    }


    function refreshPageEmergenyHistory() {
        document.getElementById('full_term').innerText = `{{ config('app.lang') != 'en' ? __('전체 기간') : __('Full term') }}`
        document.getElementById('datepicker-gp-emergency-history-1').value = 'YYYY-MM-DD';
        document.getElementById('datepicker-gp-emergency-history-2').value = 'YYYY-MM-DD';
        loadDetailsEmergencyHistoryUser();
    }

    function refreshPagePatientCallHistory() {
        document.getElementById('full_term').innerText = `{{ config('app.lang') != 'en' ? __('전체 기간') : __('Full term') }}`
        document.getElementById('datepicker-gp-patient-call-history-1').value = 'YYYY-MM-DD';
        document.getElementById('datepicker-gp-patient-call-history-2').value = 'YYYY-MM-DD';
        loadDetailsPatientCallHistoryUser();
    }
</script>
@endsection