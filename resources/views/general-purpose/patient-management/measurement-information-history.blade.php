<div class="measurement-info">
    <div class="measurement-header" style="background-color:#192734;margin-bottom:5px;margin-top:-5px;padding: 10px;">
        <div class="header-hospital-filter-container">
            <div class="header-hospital-filter-row">
                <form id="filter-form" method="GET" action="/admin-management"
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
                                <input type="text" id="datepicker-1" value="{{date('Y-m-d', strtotime('-1 month'))}}"
                                    name="start_date_range">
                                <div class="calendar" id="calendar-1"></div>
                            </div>
                        </div>
                        <div class="btn-group">
                            <div class="date-picker-container" style="background-color:#4E6880">
                                <i class="fas fa-calendar-alt" onclick="toggleCalendar('calendar-2')"></i>
                                <input type="text" id="datepicker-2" value="{{date('Y-m-d')}}" name="end_date_range">
                                <div class="calendar" id="calendar-2"></div>
                            </div>
                        </div>
                    </div>
                    <div class="header-hospital-filter-right">
                        <div class="btn-group">
                            <button class="btn btn-warning btn-sm" type="button"
                                onclick="fetchMeasurementGeneralPurposeHistory({{$id}})">
                                {{ config('app.lang') != 'en' ? __('검색') : __('Search') }}
                            </button>
                        </div>
                        <div class="btn-group"> <button class="btn btn-primary btn-sm" type="button"
                                onclick="ExportFile({{$id}})">
                                {{ config('app.lang') != 'en' ? __('출력') : __('Output') }}
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>

    </div>
    <div class="styled-table-general-white-purpose-container">
        <table class="styled-table-general-white-purpose" style="border-spacing:-10px-15px;">
            <thead>
                <tr>
                    <th>{{ config('app.lang') != 'en' ? __('측정시간') : __('Measurement time') }}</th>
                    <th>{{ config('app.lang') != 'en' ? __('체온') : __('Body temperature') }}</th>
                    <th>{{ config('app.lang') != 'en' ? __('심박수') : __('Heart rate') }}</th>
                    <th>{{ config('app.lang') != 'en' ? __('호흡수') : __('Respiratory rate') }}</th>
                    <th>{{ config('app.lang') != 'en' ? __('혈압') : __('Blood pressure') }}</th>
                    <th>{{ config('app.lang') != 'en' ? __('산소') : __('Oxygen') }}</th>
                    <th>{{ config('app.lang') != 'en' ? __('심전도') : __('ECG') }}</th>
                </tr>
            </thead>
            <tbody id="bodyMeasurementTableBody">
                <!-- Rows will be inserted here from fetchMeasurementGeneralPurposeHistory -->
            </tbody>
        </table>
    </div>
</div>


<script>
function fetchMeasurementGeneralPurposeHistory(customerId) {

    const startDate = document.getElementById("datepicker-1").value;
    const endDate = document.getElementById("datepicker-2").value;
    const tbody = document.getElementById('bodyMeasurementTableBody');
    tbody.innerHTML = '';

    fetch('/general-purpose/patient-management/history/' + customerId +
            `?start_date_range=${startDate}&end_date_range=${endDate}&format_time=Y-m-d H:i`)
        .then(response => response.json())
        .then(data => {
            Object.values(data).forEach(e => { // Loop through the object values
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${e.time}</td>
                    <td>${e.body_temperature != null ? e.body_temperature : 0}</td>
                    <td>${e.heart_rate != null ? e.heart_rate : 0}</td>
                    <td>${e.respiratory_rate != null ? e.respiratory_rate : 0}</td>
                    <td>${e.blood_pressure != null ? e.blood_pressure : 0}</td>
                    <td>${e.oxygen_saturation != null ? e.oxygen_saturation : 0}</td>
                    <td>${e.ecg != null ? e.ecg : 0}</td>
                `;

                tbody.appendChild(row);
            });
        })
        .catch(error => console.error('Error:', error));
}

function ExportFile(customerId) {

    const startDate = document.getElementById("datepicker-1").value;
    const endDate = document.getElementById("datepicker-2").value;

    window.location.href = '/general-purpose/patient-management/history/download-excel/' + customerId +
        `?start_date_range=${startDate}&end_date_range=${endDate}&format_time=Y-m-d H:i`
}
</script>