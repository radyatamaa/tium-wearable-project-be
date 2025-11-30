@php
$pageTitle = config('app.lang') != 'en' ? __('대시보드') : __('Dashboard');
@endphp

@extends('layouts.app', ['page' => $pageTitle, 'pageSlug' => 'dashboard'])

@section('content-fluid')
<!-- header -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body-custom-1 flex-fill d-flex flex-column justify-content-left align-items-left w-100">
                <div class="custom-container-dashboard" style="display: flex;width: 100%;gap: 1em;height:80px">
                    <div class="custom-info-box custom-bg-blue">
                        <span class="custom-text">
                            {{ config('app.lang') != 'en' ? __('입원 대상자 현황') : __('Status of hospitalized patients'); }}
                        </span>
                        <span class="custom-icon"><img src="{{ asset('black') }}/icons/dashboard/human.svg"></span>
                        <span class="custom-number" id="inpatientCountDay">{{$inpatientCountDay}} /
                            {{$inpatientCount}}</span>
                    </div>
                    <div class="custom-info-box custom-bg-purple">
                        <span class="custom-text">
                            {{ config('app.lang') != 'en' ? __('퇴원 대상자 현황
                                (당일)') : __('Status of discharge recipients
                                (the very day)'); }}
                        </span>
                        <span class="custom-icon"><img src="{{ asset('black') }}/icons/dashboard/human.svg"></span>
                        <span class="custom-number" id="dischargeCountDay">{{$dischargeCountDay}} /
                            {{$dischargeCount}}</span>
                    </div>
                    <div class="custom-info-box custom-bg-light-blue">
                        <span class="custom-text">
                            {{ config('app.lang') != 'en' ? __('긴급 알림 누적
                                (당일)') : __('Accumulate Emergency Notifications
                                (the very day)'); }}
                        </span>
                        <span class="custom-icon"><img src="{{ asset('black') }}/icons/dashboard/human.svg"></span>
                        <span class="custom-number" id="emergencyCountDay"></span>
                    </div>
                    <div class="custom-info-box custom-bg-light-purple">
                        <span class="custom-text">
                            {{ config('app.lang') != 'en' ? __('환자 호출 누적
                                (당일)') : __('cumulative number of patients call
                                (the very day)'); }}
                        </span>
                        <span class="custom-icon"><img src="{{ asset('black') }}/icons/dashboard/human.svg"></span>
                        <span class="custom-number" id="patientCallDay"></span>
                    </div>
                    <div class="custom-info-box custom-bg-green">
                        <span class="custom-text">
                            {{ config('app.lang') != 'en' ? __('기타 통계항목') : __('Other statistical items'); }}
                        </span>
                        <span class="custom-icon"><img src="{{ asset('black') }}/icons/dashboard/human.svg"></span>
                        <span class="custom-number">3 / 10</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<!--  -->
<div class="row">
    <!-- first card -->
    <div class="col-md-6">
        <div class="card flex-fill d-flex flex-column" style="background-color: #1E3142; color: #FFFFFF;">
            <div class="card-header w-100 text-left" style="background-color: #324D65; color: #FFFFFF;">
                <h5 class="title">
                    {{ config('app.lang') != 'en' ? __('긴급알림') : __('emergency notification'); }}
                    <span class="setting-general-purpose-refresh-icon" id="reset" onclick="refreshPageEmergencies()">
                        <img src="{{ asset('black') }}/icons/general/refresh.svg">
                    </span>
                </h5>
            </div>
            <div id="emergency-container"
                class="card-body-custom-1 flex-fill d-flex flex-column justify-content-left align-items-left w-100"
                style="background-color:#1E3142;min-height:52vh;max-height:52vh; overflow-y:auto;">
                <!-- Content will be dynamically loaded here -->
            </div>
        </div>
    </div>
    <!--  -->

    <!-- second card -->
    <div class="col-md-6">
        <div class="card flex-fill d-flex flex-column" style="background-color: #1E3142; color: #FFFFFF;">
            <div class="card-header w-100 text-left" style="background-color: #324D65; color: #FFFFFF;">
                <h5 class="title">
                    {{ config('app.lang') != 'en' ? __('환자호출') : __('call from a patient'); }}
                    <span class="setting-general-purpose-refresh-icon" id="reset"
                        onclick="refreshPageloadPatientCalls()">
                        <img src="{{ asset('black') }}/icons/general/refresh.svg">
                    </span>
                </h5>
            </div>
            <div id="patient-call-container"
                class="card-body-custom-1 flex-fill d-flex flex-column justify-content-left align-items-left w-100"
                style="background-color:#1E3142;min-height:52vh;max-height:52vh; overflow-y:auto;">
                <!-- Content will be dynamically loaded here -->
            </div>
        </div>
    </div>
    <!--  -->

</div>

<script>
let inactivityTimeout;

// Fungsi yang akan dipanggil untuk memeriksa aktivitas pengguna
function resetInactivityTimeout() {
    // Jika ada timeout yang sudah diatur sebelumnya, hapus timeout tersebut
    clearTimeout(inactivityTimeout);

    // Setel timeout untuk memanggil getChart setelah 3 menit (180000 milidetik) tidak ada aktivitas
    inactivityTimeout = setTimeout(function() {
        refreshPageEmergencies();
        refreshPageloadPatientCalls();
        loadSummaryCount();
    }, 180000); // 3 menit
}
// Menambahkan event listener untuk mendeteksi aktivitas pengguna
document.addEventListener('mousemove', resetInactivityTimeout);
document.addEventListener('mousedown', resetInactivityTimeout);
document.addEventListener('keypress', resetInactivityTimeout);
document.addEventListener('touchmove', resetInactivityTimeout);

let emergencyPage = 1;
let patientCallPage = 1;
let isLoadingEmergencies = false;
let isLoadingPatientCalls = false;

function refreshPageEmergencies() {
    window.location.reload();
    // loadSummaryCount();
    // const emergencyContainer = document.getElementById('emergency-container');
    // emergencyContainer.innerHTML = ''
    // emergencyPage = 1;
    // isLoadingEmergencies = false;
    // loadEmergencies(emergencyPage);
}

function refreshPageloadPatientCalls() {
    window.location.reload();
    // loadSummaryCount();
    // const patientCallContainer = document.getElementById('patient-call-container');
    // patientCallContainer.innerHTML = ''
    // patientCallPage = 1;
    // isLoadingPatientCalls = false;
    // loadPatientCalls(patientCallPage);
}


document.addEventListener('DOMContentLoaded', function() {


    loadEmergencies(emergencyPage);
    loadPatientCalls(patientCallPage);
    loadSummaryCount();

    const emergencyContainer = document.getElementById('emergency-container');
    const patientCallContainer = document.getElementById('patient-call-container');

    emergencyContainer.addEventListener('scroll', () => {
        if (emergencyContainer.scrollTop + emergencyContainer.clientHeight >= emergencyContainer
            .scrollHeight) {
            if (!isLoadingEmergencies) {
                emergencyPage++;
                loadEmergencies(emergencyPage);
            }
        }
    });

    patientCallContainer.addEventListener('scroll', () => {
        if (patientCallContainer.scrollTop + patientCallContainer.clientHeight >= patientCallContainer
            .scrollHeight) {
            if (!isLoadingPatientCalls) {
                patientCallPage++;
                loadPatientCalls(patientCallPage);
            }
        }
    });
});

function loadEmergencies(page) {
    isLoadingEmergencies = true;
    fetch(`/hospital/home/emergency?page=${page}&limit=10`)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('emergency-container');

            if (data.datas.data.length === 0 && page === 1) {
                showNoDataMessage(container,
                    `{{ config('app.lang') != 'en' ? __('데이터 기록이 없습니다.') : __('There are no data records.') }}`
                );
            } else {
                data.datas.data.forEach(emg => {
                    const card = createEmergencyElement(emg);
                    container.appendChild(card);
                });
            }
            isLoadingEmergencies = false;
        })
        .catch(error => {
            console.error('Error loading emergencies:', error);
            isLoadingEmergencies = false;
        });
}

function loadPatientCalls(page) {
    isLoadingPatientCalls = true;
    fetch(`/hospital/home/patient-call?page=${page}&limit=10`)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('patient-call-container');

            if (data.datas.data.length === 0 && page === 1) {
                showNoDataMessage(container,
                    `{{ config('app.lang') != 'en' ? __('데이터 기록이 없습니다.') : __('There are no data records.') }}`
                );
            } else {
                data.datas.data.forEach(pcall => {
                    const card = createPatientCallElement(pcall);
                    container.appendChild(card);
                });
            }
            isLoadingPatientCalls = false;
        })
        .catch(error => {
            console.error('Error loading patient calls:', error);
            isLoadingPatientCalls = false;
        });
}

function showNoDataMessage(container, message) {
    container.innerHTML = `
        <div class="card-body-custom-1 flex-fill d-flex flex-column justify-content-left align-items-left w-100"
            style="background-color:#1E3142;min-height:40vh;text-align: center">
            <div class="error-tium-icon">
                <img src="{{ asset('black') }}/icons/general/error.svg" alt="warning">
            </div>
            <p class="error-tium-message">${message}</p>
        </div>
    `;
}

function createEmergencyElement(emg) {
    const card = document.createElement('div');
    card.className = 'patient-info-card mt-2';

    let genderContent =
        `- <i class="patient-info-card-icon"></i>`

    if (emg.patient_hospital) {
        if (emg.patient_hospital.gender === 'Man' || emg.patient_hospital.gender === 'M') {
            genderContent =
                ` <i class="patient-info-card-icon"><img src="{{ asset('black') }}/icons/general/men.svg"></i>`;
        } else if (emg.patient_hospital.gender === 'Woman' || emg.patient_hospital.gender === 'F') {
            genderContent =
                ` <i class="patient-info-card-icon"><img src="{{ asset('black') }}/icons/general/women.svg"></i>`;
        }
    }

    const alertDetailsArray = emg.alert_details.split(',');
    const alertTypeArray = emg.alert_type.split(',');

    const alertDetailsHTML = alertDetailsArray.map((detail, index) => {
        const alertType = alertTypeArray[index];
        const alertClass = alertType === 'caution' ? 'patient-info-card-caution' : 'patient-info-card-danger';
        const padding = index === 0 ? '' : 'padding-left: 3em;';
        return `
            <div class="patient-info-card-row patient-info-card-condition-row" style="${padding}">
                <span class="patient-info-card-condition">
                    ${index === 0 ? '{{ config('app.lang') != 'en' ? __('주증상: ') : __('Main symptom: ') }}' : ''}
                    ${detail}
                    <span class="${alertClass}">
                        ${alertType === 'caution' ? '{{ config('app.lang') != 'en' ? __('주의') : __('Caution') }}' : '{{ config('app.lang') != 'en' ? __('위험') : __('Danger') }}'}
                    </span>
                </span>
            </div>`;
    }).join('');

    card.innerHTML = `
        <div class="patient-info-card-column patient-info-card-first-column">
            <div class="patient-info-card-row patient-info-card-name-row">
                <span class="patient-info-card-name">${emg.patient_name}</span>
                <div class="patient-info-card-row" style="margin-right:0px;margin-left:120px">
                    <span class="patient-info-card-gender">{{ config('app.lang') != 'en' ? __('성별: ') : __('Gender: ')}}${genderContent}</span>
                    <div class="patient-info-card-vertical-divider"></div>
                    <span class="patient-info-card-age">{{ config('app.lang') != 'en' ? __('나이: ') : __('Age: ') }} ${emg.patient_hospital ? emg.patient_hospital.age : '-'}</span>
                </div>
            </div>
            ${alertDetailsHTML}
        </div>
        <div class="patient-info-card-column patient-info-card-middle-column">
            <div class="patient-info-card-row patient-info-card-condition-row">
                <span class="patient-info-card-room">{{ config('app.lang') != 'en' ? __('입원실: ') : __('Room: ') }} ${emg.ward}</span>
            </div>
            <div class="patient-info-card-row patient-info-card-condition-row">
                <span class="patient-info-card-time">{{ config('app.lang') != 'en' ? __('알림 발생시간: ') : __('Alert time: ') }} ${formatTime(emg.created_at)}</span>
            </div>
        </div>
        <div class="patient-info-card-column patient-info-card-right-column">
            <a href="#" class="patient-info-card-confirm" onclick="showModal1(${emg.id})">{{ config('app.lang') != 'en' ? __('호출 확인') : __('Check notifications') }}</a>
        </div>`;
    return card;
}

function createPatientCallElement(pcall) {
    const card = document.createElement('div');
    card.className = 'patient-info-card mt-2';

    let genderContent =
        `- <i class="patient-info-card-icon"></i>`

    if (pcall.patient_hospital) {
        if (pcall.patient_hospital.gender === 'Man' || pcall.patient_hospital.gender === 'M') {
            genderContent =
                ` <i class="patient-info-card-icon"><img src="{{ asset('black') }}/icons/general/men.svg"></i>`;
        } else if (pcall.patient_hospital.gender === 'Woman' || pcall.patient_hospital.gender === 'F') {
            genderContent =
                ` <i class="patient-info-card-icon"><img src="{{ asset('black') }}/icons/general/women.svg"></i>`;
        }
    }

    card.innerHTML = `
        <div class="patient-info-card-column patient-info-card-first-column">
            <div class="patient-info-card-row patient-info-card-name-row">
                <span class="patient-info-card-name">${pcall.patient_name}</span>
                <div class="patient-info-card-row" style="margin-right:0px;margin-left:120px">
                    <span class="patient-info-card-gender">{{ config('app.lang') != 'en' ? __('성별: ') : __('Gender: ')}}${genderContent}</span>
                    <div class="patient-info-card-vertical-divider"></div>
                    <span class="patient-info-card-age">{{ config('app.lang') != 'en' ? __('나이: ') : __('Age: ') }} ${pcall.patient_hospital ? pcall.patient_hospital.age : '-'}</span>
                </div>
            </div>
            <div class="patient-info-card-row patient-info-card-condition-row">
                <span class="patient-info-card-condition">
                    {{ config('app.lang') != 'en' ? __('주증상: 환자에게 전화하다') : __('Main symptom: Call a patient') }}
                    <span class="patient-info-card-low">${pcall.call_type}</span>
                </span>
            </div>
        </div>
        <div class="patient-info-card-column patient-info-card-middle-column">
            <div class="patient-info-card-row patient-info-card-condition-row">
                <span class="patient-info-card-room">{{ config('app.lang') != 'en' ? __('입원실: ') : __('Room: ') }} ${pcall.ward}</span>
            </div>
            <div class="patient-info-card-row patient-info-card-condition-row">
                <span class="patient-info-card-time">{{ config('app.lang') != 'en' ? __('알림 발생시간: ') : __('Notification time: ') }} ${formatTime(pcall.created_at)}</span>
            </div>
        </div>
        <div class="patient-info-card-column patient-info-card-right-column">
            <a href="#" class="patient-info-card-confirm" onclick="showModal2(${pcall.id})">{{ config('app.lang') != 'en' ? __('호출 확인') : __('Call Confirmation') }}</a>
        </div>`;
    return card;
}


function loadSummaryCount() {
    fetch(`/hospital/home/summary-count`)
        .then(response => response.json())
        .then(data => {
            const emergencyCountDay = document.getElementById('emergencyCountDay').innerText =
                `${data.emergencyCountDay} / ${data.emergencyCount}`;
            const patientCallDay = document.getElementById('patientCallDay').innerText =
                `${data.patientCallDay} / ${data.patientCallCount}`;
        })
        .catch(error => {
            console.error('Error loading emergencies:', error);
        });
}
</script>



@endsection