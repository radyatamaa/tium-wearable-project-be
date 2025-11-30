@php
$pageTitle = config('app.lang') != 'en' ? __('대시보드') : __('Dashboard');
@endphp

@extends('layouts.app', ['page' => $pageTitle, 'pageSlug' => 'dashboard'])

@section('content-fluid')
<div class="row">
    <div class="col-md-12">
        <div class="home-public-welfare-container">
            <!-- user management -->
            <div class="home-public-welfare-card" id="left-card">
                <div class="home-public-welfare-card-header">
                    <div class="title-card">
                        {{ config('app.lang') != 'en' ? __('사용자관리') : __('User Management') }}
                        <span class="home-public-welfare-card-header-refresh-icon ml-2" id="reset"
                            onclick="refreshPageloadUsersManagement()">
                            <img src="{{ asset('black') }}/icons/general/refresh.svg">
                        </span>
                    </div>
                </div>
                <div class="home-public-welfare-card-content" id="userManagementContainer">
                    <div class="row">
                        <div class="col">
                            <div class="status-card">
                                <div class="status-card-body subscription">
                                    <div class="status-info">
                                        <span>{{ config('app.lang') != 'en' ? __('구독 상태 (일/전체)') : __('Subscription status (The day/whole)') }}</span>
                                        <span id="totalUsersToday"> 👤 {{ $totalUsersToday }} / {{ $totalUsers }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- User Managmenet will be loaded from javascript -->
                </div>
            </div>
            <!--  -->

            <div class="home-public-welfare-resizer" id="resizer"></div>
            <!-- patient call -->
            <div class="home-public-welfare-card" id="right-card">
                <div class="home-public-welfare-card-header">
                    <div class="title-card">
                        {{ config('app.lang') != 'en' ? __('통화 알림') : __('Call Alert') }}
                        <span class="home-public-welfare-card-header-refresh-icon ml-2" id="reset"
                            onclick="refreshPageloadPatientCalls()">
                            <img src="{{ asset('black') }}/icons/general/refresh.svg">
                        </span>
                    </div>
                </div>
                <div class="home-public-welfare-card-content" id="callAlertContainer">
                    <!-- Call alerts will be loaded here -->
                </div>
            </div>

            <!--  -->

        </div>
    </div>
</div>
<script>
let userMPage = 1;
let patientCallPage = 1;
let isLoadingUsers = false;
let isLoadingPatientCalls = false;
let isStopScrollUserM = false;

function refreshPage() {
    window.location.reload();
}
document.addEventListener('DOMContentLoaded', function() {
    const resizer = document.getElementById('resizer');
    const leftCard = document.getElementById('left-card');
    const rightCard = document.getElementById('right-card');

    let isResizing = false;

    resizer.addEventListener('mousedown', function(e) {
        isResizing = true;

        document.addEventListener('mousemove', resizeCards);
        document.addEventListener('mouseup', stopResizing);
    });

    function resizeCards(e) {
        if (!isResizing) return;

        const containerWidth = leftCard.parentNode.offsetWidth;
        const newLeftWidth = e.clientX;
        const newRightWidth = containerWidth - e.clientX - resizer.offsetWidth;

        if (newLeftWidth > 100 && newRightWidth > 100) {
            leftCard.style.width = newLeftWidth + 'px';
            rightCard.style.width = newRightWidth + 'px';
        }
    }

    function stopResizing() {
        isResizing = false;
        document.removeEventListener('mousemove', resizeCards);
        document.removeEventListener('mouseup', stopResizing);
    }


    loadMoreCallData(patientCallPage);

    const patientCallContainer = document.getElementById('callAlertContainer');
    patientCallContainer.addEventListener('scroll', () => {
        console.log('patientCallContainer.scrollTop + patientCallContainer.clientHeight',
            patientCallContainer.scrollTop + patientCallContainer.clientHeight)
        console.log('patientCallContainer.scrollHeight', (patientCallContainer.scrollHeight - 1))
        if (
            patientCallContainer.scrollTop + patientCallContainer.clientHeight >= (patientCallContainer
                .scrollHeight - 1)) {
            if (!isLoadingPatientCalls) {
                patientCallPage++;
                loadMoreCallData(patientCallPage);
            }
        }
    });

    loadMoreUserManagementData(userMPage);
    const userManagementContainer = document.getElementById('userManagementContainer');
    userManagementContainer.addEventListener('scroll', () => {
        console.log('userManagementContainer.scrollTop + userManagementContainer.clientHeight',
            userManagementContainer.scrollTop + userManagementContainer.clientHeight)
        console.log('userManagementContainer.scrollHeight', (userManagementContainer.scrollHeight - 1))
        if (
            userManagementContainer.scrollTop + userManagementContainer.clientHeight >= (
                userManagementContainer
                .scrollHeight - 1)) {
            if (!isLoadingUsers && !isStopScrollUserM) {
                userMPage++;
                loadMoreUserManagementData(userMPage);
            }
        }
    });
})
</script>

<script>
let inactivityTimeout;

// Fungsi yang akan dipanggil untuk memeriksa aktivitas pengguna
function resetInactivityTimeout() {
    // Jika ada timeout yang sudah diatur sebelumnya, hapus timeout tersebut
    clearTimeout(inactivityTimeout);

    // Setel timeout untuk memanggil getChart setelah 3 menit (180000 milidetik) tidak ada aktivitas
    inactivityTimeout = setTimeout(function() {
        refreshPageloadPatientCalls();
        refreshPageloadUsersManagement();
    }, 180000); // 3 menit
}
// Menambahkan event listener untuk mendeteksi aktivitas pengguna
document.addEventListener('mousemove', resetInactivityTimeout);
document.addEventListener('mousedown', resetInactivityTimeout);
document.addEventListener('keypress', resetInactivityTimeout);
document.addEventListener('touchmove', resetInactivityTimeout);


function refreshPageloadPatientCalls() {
    const patientCallContainer = document.getElementById('callAlertContainer');
    patientCallContainer.innerHTML = ''
    patientCallPage = 1;
    isLoadingPatientCalls = false;
    loadMoreCallData(patientCallPage);
}

function refreshPageloadUsersManagement() {
    const userManagementContainer = document.getElementById('userManagementContainer');
    userManagementContainer.innerHTML = `<div class="row">
                        <div class="col">
                            <div class="status-card">
                                <div class="status-card-body subscription">
                                    <div class="status-info">
                                        <span>{{ config('app.lang') != 'en' ? __('구독 상태 (일/전체)') : __('Subscription status (The day/whole)') }}</span>
                                        <span id="totalUsersToday">👤 {{ $totalUsersToday }} / {{ $totalUsers }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`
    userMPage = 1;
    isLoadingUsers = false;
    isStopScrollUserM = false;
    loadMoreUserManagementData(userMPage);
}


// Function to load more call alert data
function loadMoreCallData(page) {
    isLoadingPatientCalls = true;
    const callContainer = document.getElementById('callAlertContainer');

    fetch(`/public-welfare/home/patient-call?page=${page}&limit=10`)
        .then(response => response.json())
        .then(data => {
            // Append new data to the container
            data.datas.data.forEach(call => {

                const card = createPatientCallElement(call);
                callContainer.appendChild(card);
                // callContainer.insertAdjacentHTML('beforeend', callCard);
            });

            isLoadingPatientCalls = false;
        })
        .catch(error => {
            console.error('Error loading patient calls:', error);
            isLoadingPatientCalls = false;
        });
}

function createPatientCallElement(call) {
    const card = document.createElement('div');
    card.className = 'patient-info-card mt-2';

    let genderContent =
        `- <i class="patient-info-card-icon"></i>`

    if (call.gender === 'Man' || call.gender === 'M') {
        genderContent =
            ` <i class="patient-info-card-icon"><img src="{{ asset('black') }}/icons/general/men.svg"></i>`;
    } else if (call.gender === 'Woman' || call.gender === 'F') {
        genderContent =
            ` <i class="patient-info-card-icon"><img src="{{ asset('black') }}/icons/general/women.svg"></i>`;
    }

    card.innerHTML = `
        <div class="patient-info-card-column patient-info-card-first-column"
                            style="padding-right:100px">
                            <div class="patient-info-card-row patient-info-card-name-row">
                                <span class="patient-info-card-name">${call.name}</span>
                                <div class="patient-info-card-row" style="margin-right:0px;margin-left:160px">
                                    <span class="patient-info-card-gender">{{ config('app.lang') != 'en' ? __('성별: ') : __('Gender: ')}}${genderContent}</span>
                                    <div class="patient-info-card-vertical-divider"></div>
                                    <span
                                        class="patient-info-card-age">{{ config('app.lang') != 'en' ? __('나이: ') : __('Age: ')  }}${call.age}</span>
                                </div>
                            </div>
                            <div class="patient-info-card-row patient-info-card-condition-row">
                                <span
                                    class="patient-info-card-condition">{{ config('app.lang') != 'en' ? __('주증상: ') : __('Main symptom: ')}}${call.main_symptom}
                            </div>
                        </div>
                        <div class="patient-info-card-column patient-info-card-middle-column">
                            <div class="patient-info-card-row patient-info-card-condition-row">
                                <span
                                    class="patient-info-card-room">{{ config('app.lang') != 'en' ? __('연락처: ') : __('Contact: ') }}
                                    ${call.contact ?? '-'}</span>
                            </div>
                            <div class="patient-info-card-row patient-info-card-condition-row">
                                <span
                                    class="patient-info-card-time">{{ config('app.lang') != 'en' ? __('소속: ') : __('Affiliations: ') }}
                                    ${call.affiliations ?? '-'}</span>
                            </div>
                        </div>
                        <div class="patient-info-card-column patient-info-card-right-column">
                            <a href="#" class="patient-info-card-confirm" onclick="showModal2(${call.id})">
                            {{ config('app.lang') != 'en' ? __('호출확인') : __('Call Confirmation') }}</a>
                        </div>`;
    return card;
}

function loadMoreUserManagementData(page) {
    isLoadingUsers = true;
    const userManagementContainer = document.getElementById('userManagementContainer');

    fetch(`/public-welfare/home/user-management?page=${page}&limit=20`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('totalUsersToday').innerText =
                `👤 ${data.totalUsersToday} / ${data.totalUsers}`;
            if (data.datas.data.length === 0) {
                isStopScrollUserM = true;
            }
            const card = createUserManagementRows(data.datas.data);
            userManagementContainer.appendChild(card);

            isLoadingUsers = false;
        })
        .catch(error => {
            console.error('Error loading users:', error);
            isLoadingUsers = false;
        });
}

function createUserManagementRows(users) {
    const row = document.createElement('div');
    row.className = 'row';

    const leftCol = document.createElement('div');
    leftCol.className = 'col-md-6';

    const rightCol = document.createElement('div');
    rightCol.className = 'col-md-6';

    users.forEach((user, index) => {
        const card = createUserManagementElement(user);

        if ((index + 1) % 2 != 0) {
            leftCol.appendChild(card);
        } else {
            rightCol.appendChild(card);
        }
    });

    row.appendChild(leftCol);
    row.appendChild(rightCol);

    return row;
}

function createUserManagementElement(user) {
    const card = document.createElement('div');
    card.className = 'patient-info-card mt-2';
    card.style.minWidth = '180px';

    let genderContent = `- <i class="patient-info-card-icon"></i>`;

    if (user.gender === 'Man' || user.gender === 'M') {
        genderContent = `<i class="patient-info-card-icon"><img src="{{ asset('black') }}/icons/general/men.svg"></i>`;
    } else if (user.gender === 'Woman' || user.gender === 'F') {
        genderContent =
            `<i class="patient-info-card-icon"><img src="{{ asset('black') }}/icons/general/women.svg"></i>`;
    }

    card.innerHTML = `
        <div class="patient-info-card-column patient-info-card-first-column" style="border-right:0px">
            <div class="patient-info-card-row patient-info-card-name-row">
                <span class="patient-info-card-name" style="color:#3CBAFF">${user.name}</span>
                <div class="patient-info-card-row" style="margin-right:0px;margin-left:100px">
                    <span class="patient-info-card-gender">{{ config('app.lang') != 'en' ? __('성별: ') : __('Gender: ')}}${genderContent}</span>
                    <div class="patient-info-card-vertical-divider"></div>
                    <span class="patient-info-card-age">{{ config('app.lang') != 'en' ? __('나이: ') : __('Age: ') }}${user.age}</span>
                </div>
            </div>
            <div class="patient-info-card-row patient-info-card-condition-row">
                <span class="patient-info-card-condition">{{ config('app.lang') != 'en' ? __('소속 :') : __('Affiliations:') }}
                    <span>${user.affiliations || '-'}</span></span>
            </div>
        </div>
    `;

    return card;
}
</script>
@endsection