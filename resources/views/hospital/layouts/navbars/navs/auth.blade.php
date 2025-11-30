<nav class="navbar fixed-top navbar-expand-lg">
    <div class="container-fluid">
        <div class="navbar-wrapper">
            <a class="navbar-brand" href="/hospital/home" id="home-link">
                <img src="{{ asset('black') }}/icons/general/logo-navbar.svg">
            </a>

            <div class="dropdown exclude-dropdown">
                <a href="#" class="navbar-brand dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false" id="patient-care-link">
                    <div class="title">{{ config('app.lang') != 'en' ? __('환자관리') : __('Patient care') }}</div>
                </a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ route('hospital.patient_management.inpatients') }}">
                        {{ config('app.lang') != 'en' ? __('입원환자') : __('Hospitalized patient') }}
                    </a>
                    <a class="dropdown-item" href="{{ route('hospital.patient_management.discharged') }}">
                        {{ config('app.lang') != 'en' ? __('퇴원환자') : __('Discharged Patient') }}
                    </a>
                </div>
            </div>

            <div class="dropdown exclude-dropdown">
                <a href="#" class="navbar-brand dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false" id="history-management-link">
                    <div class="title">{{ config('app.lang') != 'en' ? __('이력관리') : __('History Management') }}</div>
                </a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ route('hospital.history.emergency_alerts') }}">
                        {{ config('app.lang') != 'en' ? __('긴급알림이력') : __('Emergency notification history') }}
                    </a>
                    <a class="dropdown-item" href="{{ route('hospital.history.patient_calls') }}">
                        {{ config('app.lang') != 'en' ? __('환자호출이력') : __('Patient call history') }}
                    </a>
                </div>
            </div>

            @if(Auth::guard('hospital')->check() || Auth::guard('hospitalStaff')->check())
            <div class="dropdown exclude-dropdown">
                <a href="#" class="navbar-brand dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false" id="device-management-link">
                    <div class="title">{{ config('app.lang') != 'en' ? __('디바이스관리') : __('Device Management') }}</div>
                </a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ route('hospital.device_management') }}">
                        {{ config('app.lang') != 'en' ? __('스마트워치') : __('Smartwatch') }}
                    </a>
                </div>
            </div>

            @if(Auth::guard('hospital')->check())
            <div class="dropdown exclude-dropdown">
                <a href="#" class="navbar-brand dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false" id="manage-settings-link">
                    <div class="title">{{ config('app.lang') != 'en' ? __('설정관리') : __('Manage Settings') }}</div>
                </a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ route('hospital.settings.location-management.top-location') }}">
                        {{ config('app.lang') != 'en' ? __('상위 로케이션') : __('Top Locations') }}
                    </a>
                    <a class="dropdown-item" href="{{ route('hospital.settings.location-management.sub-location') }}">
                        {{ config('app.lang') != 'en' ? __('하위 로케이션') : __('A sublocation') }}
                    </a>
                    <a class="dropdown-item"
                        href="{{ route('hospital.settings.location-management.detailed-location') }}">
                        {{ config('app.lang') != 'en' ? __('상세 로케이션') : __('Detailed location') }}
                    </a>
                    <a class="dropdown-item" href="{{ route('hospital.settings.account_management') }}">
                        {{ config('app.lang') != 'en' ? __('계정관리') : __('Account Management') }}
                    </a>
                    <a class="dropdown-item" href="{{ route('hospital.staff_management') }}">
                        {{ config('app.lang') != 'en' ? __('의료진 관리') : __('Staff Management') }}
                    </a>
                </div>
            </div>
            @endif


            @endif
        </div>


        <div class="collapse navbar-collapse" id="navigation">
            <ul class="navbar-nav ml-auto">
                <div class="container-fluid mt-4">
                    <div class="notification-general-purpose"
                        style="display: flex; align-items: center; justify-content: space-between; white-space: nowrap;">
                        <i><img src="{{ asset('black') }}/icons/general/bell-2.svg"></i>
                        <span
                            style="flex-grow: 1; text-align: right;">{{ config('app.lang') != 'en' ? __('긴급') : __('Emergency') }}
                            0 / <span class="emergency-count">0</span></span>
                    </div>
                </div>
                <div class="container-fluid mt-4">
                    <div class="notification-general-purpose"
                        style="display: flex; align-items: center; justify-content: space-between; white-space: nowrap;">
                        <i><img src="{{ asset('black') }}/icons/general/call.svg"></i>
                        <span
                            style="flex-grow: 1; text-align: right;">{{ config('app.lang') != 'en' ? __('호출') : __('Call') }}
                            0 / <span class="call-count">0</span></span>
                    </div>
                </div>

                @php
                if (Auth::guard('hospital')->check()) {
                $user = Auth::guard('hospital')->user();
                $userGenPurpose = \App\Models\UserGeneralPurpose::where('id', $user->user_general_purpose_id)
                ->with(['subscriptionInformation'])
                ->first();
                $organization_name = $userGenPurpose->subscriptionInformation->organization_name;
                $name_of_representative = $userGenPurpose->subscriptionInformation->name_of_representative;
                } else if (Auth::guard('hospitalStaff')->check()) {
                $user = Auth::guard('hospitalStaff')->user();
                $userGenPurpose = \App\Models\UserGeneralPurpose::where('id', $user->user_general_purpose_id)
                ->with(['subscriptionInformation'])
                ->first();
                $organization_name = $userGenPurpose->subscriptionInformation->organization_name;
                $name_of_representative = $userGenPurpose->subscriptionInformation->name_of_representative;
                }

                @endphp

                <div class="btn-group" style="margin-right:10px">
                    <button type="button" class="btn btn-warning btn-sm" style="border-radius:8px">
                        {{ $organization_name }}
                    </button>
                </div>
                <div class="btn-group" style="margin-right:10px">
                    <button type="button" class="btn btn-warning btn-sm" style="border-radius:8px">
                        {{ $name_of_representative }}
                    </button>
                </div>

                <!-- <div class="btn-group" style="margin-right:10px">
                    <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        @if (config('app.lang') != 'en')
                        {{ __('전체 층') }}
                        @else
                        {{ __('The entire floor') }}
                        @endif
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="#">
                            @if (config('app.lang') != 'en')
                            {{ __('전체 층') }}
                            @else
                            {{ __('The entire floor') }}
                            @endif
                        </a>
                        <a class="dropdown-item" href="#">
                            @if (config('app.lang') != 'en')
                            {{ __('1층') }}
                            @else
                            {{ __('the first floor') }}
                            @endif
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">
                            @if (config('app.lang') != 'en')
                            {{ __('2층') }}
                            @else
                            {{ __('second floor') }}
                            @endif
                        </a>
                    </div>
                </div>
                <div class="btn-group" style="margin-right:10px">
                    <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        @if (config('app.lang') != 'en')
                        {{ __('전체 병동') }}
                        @else
                        {{ __('an entire ward') }}
                        @endif
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="#">
                            @if (config('app.lang') != 'en')
                            {{ __('전체 병동') }}
                            @else
                            {{ __('an entire ward') }}
                            @endif
                        </a>
                        <a class="dropdown-item" href="#">
                            @if (config('app.lang') != 'en')
                            {{ __('A병동') }}
                            @else
                            {{ __('Ward A') }}
                            @endif
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">
                            @if (config('app.lang') != 'en')
                            {{ __('B병동') }}
                            @else
                            {{ __('Ward B') }}
                            @endif
                        </a>
                    </div>
                </div> -->
                <li class="dropdown nav-item exclude-dropdown">
                    <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
                        <div class="photo">
                            <img src="{{ asset('black') }}/img/default-avatar.png" alt="{{ __('Profile Photo') }}">
                        </div>
                        <b class="caret d-none d-lg-block d-xl-block"></b>
                        <p class="d-lg-none">{{ config('app.lang') != 'en' ? __('로그아웃') : __('Log out'); }}</p>
                    </a>
                    <ul class="dropdown-menu dropdown-navbar">
                        <li class="nav-link">
                            <a href="{{ route('general-purpose.logout') }}" class="nav-item dropdown-item"
                                onclick="event.preventDefault();  document.getElementById('logout-form').submit();">{{ config('app.lang') != 'en' ? __('로그아웃') : __('Log out'); }}</a>
                        </li>
                    </ul>
                </li>
                <li class="separator d-lg-none"></li>
            </ul>
        </div>
    </div>
</nav>
<div class="modal modal-search fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="searchModal"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <input type="text" class="form-control" id="inlineFormInputGroup" placeholder="{{ __('SEARCH') }}">
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                    <i class="tim-icons icon-simple-remove"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- modal change password-->
<div class="modal-change-password" id="changePasswordModal" tabindex="-1" role="dialog">
    <div class="modal-change-password-modal-dialog" role="document">
        <div class="modal-change-password-modal-content">
            <div class="modal-change-password-modal-header">
                <h5 class="modal-change-password-modal-title">
                    {{ config('app.lang') != 'en' ? __('비밀번호변경') : __('Change Password') }}
                </h5>
            </div>
            <div class="modal-change-password-modal-body">
                <form id="changePasswordForm">
                    @csrf
                    <div class="smartwatch-form-group">
                        <label for="password">{{ config('app.lang') != 'en' ? __('비밀번호') : __('Password') }}</label>
                        <input type="password" class="form-control" id="password" name="password"
                            placeholder="{{ config('app.lang') != 'en' ? __('8-20자리의 숫자, 영문각각 1개 이상 포함') : __('8-20 characters with at least 1 number and 1 letter') }}"
                            required>
                    </div>
                    <div class="smartwatch-form-group">
                        <label
                            for="password_confirmation">{{ config('app.lang') != 'en' ? __('비밀번호확인') : __('Confirm Password') }}</label>
                        <input type="password" class="form-control" id="password_confirmation"
                            name="password_confirmation"
                            placeholder="{{ config('app.lang') != 'en' ? __('8-20자리의 숫자, 영문각각 1개 이상 포함') : __('8-20 characters with at least 1 number and 1 letter') }}"
                            required>
                    </div>
                    <div style="display: flex;justify-content: space-between;">
                        <button type="submit" class="btn btn-primary" onclick="changePassword('submit')"
                            style=" width: 45%;">{{ config('app.lang') != 'en' ? __('확인') : __('Confirm') }}</button>
                        <button type="button" class="btn btn-info" onclick="changePassword('hide')"
                            style="width: 45%;background-color:#324D65"
                            data-dismiss="modal">{{ config('app.lang') != 'en' ? __('취소') : __('Cancel') }}</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>


<!-- modal notif emergency -->
<div class="modal fade" id="alertConfirmation1Modal">
    <div class="modal-dialog modal-sm" style="max-width:500px;">
        <div class="modal-content modal-dashboard-alarm-content">
            <div class="modal-header modal-dashboard-alarm-header" style="background-color:#3CBAFF;text-align:center">
                <h4 class="modal-title modal-dashboard-alarm-title" style="color:#FFFFFF;text-align:center">
                    {{ config('app.lang') != 'en' ? __('알림확인') : __('Alert Confirmation') }}
                </h4>
            </div>
            <div class="modal-body modal-dashboard-alarm-body">
                <div class="text-center" style="background-color:#192734;padding:20px;">
                    <img src="{{ asset('black') }}/icons/dashboard/alarm.png" alt="Alert Icon"
                        style="width:100px;height:100px;margin-top:20px" />
                    <p class="modal-dashboard-alarm-text" style="color:#FFFFFF">
                        {{ config('app.lang') != 'en' ? __('긴급 알림') : __('Emergency notification') }}
                    </p>
                    <div id="dynamic-content">
                        <!-- Konten dinamis akan dimasukkan di sini oleh JavaScript -->
                    </div>
                </div>
                <div class="d-flex justify-content-around modal-dashboard-alarm-button-group">
                    <button class="btn btn-info modal-dashboard-alarm-btn-confirm"
                        style="background-color:#4E6880;color:white;" onclick="showFormModal1()">
                        {{ config('app.lang') != 'en' ? __('확인') : __('Confirm') }}
                    </button>
                    <button class="btn btn-info modal-dashboard-alarm-btn-cancel" data-dismiss="modal"
                        style="color:white;">
                        {{ config('app.lang') != 'en' ? __('취소') : __('Cancel') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<!--  -->

<!-- modal notif call patient -->
<div class="modal fade" id="alertConfirmation2Modal">
    <div class="modal-dialog modal-sm" style="max-width:500px;">
        <div class="modal-content modal-dashboard-alarm-content">
            <div class="modal-header modal-dashboard-alarm-header" style="background-color:#3CBAFF;text-align:center">
                <h4 class="modal-title modal-dashboard-alarm-title" style="color:#FFFFFF;text-align:center">
                    {{ config('app.lang') != 'en' ? __('알림확인') : __('Alert Confirmation') }}
                </h4>
            </div>
            <div class="modal-body modal-dashboard-alarm-body">
                <div class="text-center" style="background-color:#192734;padding:20px;">
                    <img src="{{ asset('black') }}/icons/dashboard/call.png" alt="Alert Icon"
                        style="width:100px;height:100px;margin-top:20px" />
                    <p class="modal-dashboard-alarm-text" style="color:#FFFFFF;margin-top:20px;">
                        {{ config('app.lang') != 'en' ? __('환자 호출') : __('Patient Call') }}
                    </p>
                    <!-- <p class="text-danger modal-dashboard-alarm-text-danger" style="color:#FF6C36;">
                        {{ config('app.lang') != 'en' ? __('산소포화도 94% 이하') : __('Oxygen Saturation below 94%') }}
                    </p> -->
                    <p class="modal-dashboard-alarm-text" style="color:#FFFFFF;margin-bottom:20px;"
                        id="text-call-patient">
                        {{ config('app.lang') != 'en' ? __('503호 홍길동 님의 알림을 확인처리 하겠습니까?') : __('Do you want to confirm the alert for Room 503, Hong Gil-dong?') }}
                    </p>
                </div>
                <div class="d-flex justify-content-around modal-dashboard-alarm-button-group">
                    <button class="btn btn-info modal-dashboard-alarm-btn-confirm"
                        style="background-color:#4E6880;color:white;" onclick="showFormModal2()">
                        {{ config('app.lang') != 'en' ? __('확인') : __('Confirm') }}
                    </button>
                    <button class="btn btn-info modal-dashboard-alarm-btn-cancel" data-dismiss="modal"
                        style="color:white;">
                        {{ config('app.lang') != 'en' ? __('취소') : __('Cancel') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<!--  -->

<!-- modal form emergency -->
<div class="modal fade" id="alertFormModal1">
    <div class="modal-dialog modal-lg" style="max-width:800px;margin-top:-80px;">
        <div class="modal-content modal-hospital-content">

            <!-- Modal Header -->
            <div class="modal-header"
                style="background-color:#3CBAFF;padding:20px 24px;align-items: center;justify-content: center;position: relative;">
                <h4 class="modal-title" style="color:#FFFFFF">
                    {{ config('app.lang') != 'en' ? __('조치 내용 작성') : __('Action Details') }}
                </h4>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <div class="modal-hospital-form-row">
                    <div class="patient-info-card mt-2" style="width:100%">
                        <div class="patient-info-card-column patient-info-card-first-column">
                            <div class="patient-info-card-row patient-info-card-name-row">
                                <span class="patient-info-card-name" id="patient-name-emg"></span>
                                <div class="patient-info-card-row" style="margin-right:0px;margin-left:120px">
                                    <span
                                        class="patient-info-card-gender">{{ config('app.lang') != 'en' ? __('성별: ') : __('Gender: ') }}
                                        <span id="gender-emg">123</span>
                                        <i class="patient-info-card-icon" id="icon-gender-emg"></i></span>
                                    <div class="patient-info-card-vertical-divider"></div>
                                    <span
                                        class="patient-info-card-age">{{ config('app.lang') != 'en' ? __('나이: ') : __('Age: ') }}
                                        <span id="age-emg">123</span></span>
                                </div>
                            </div>
                            <div id="main-symptom-emg-text">
                                <!-- html from javascript -->
                            </div>

                        </div>
                        <div class="patient-info-card-column patient-info-card-middle-column">
                            <div class="patient-info-card-row patient-info-card-condition-row">
                                <span
                                    class="patient-info-card-room">{{ config('app.lang') != 'en' ? __('입원실: ') : __('Room: ') }}
                                    <span id="room-emg">123</span></span>
                            </div>
                            <div class="patient-info-card-row patient-info-card-condition-row">
                                <span
                                    class="patient-info-card-time">{{ config('app.lang') != 'en' ? __('알림 발생시간: ') : __('Alert time: ') }}
                                    <span id="alert-time-emg">123</span></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-hospital-form-row">
                    <label>{{ config('app.lang') != 'en' ? __('주치의') : __('Primary Doctor') }}</label>
                    <input type="text" id="primary-doctor-emg" readonly>
                    <label>{{ config('app.lang') != 'en' ? __('담당간호사') : __('Nurse in Charge') }}</label>
                    <input type="text" id="nurse-in-charge-emg" readonly>
                </div>
                <div class="modal-hospital-form-row wide">
                    <label>{{ config('app.lang') != 'en' ? __('진료과목') : __('Department') }}</label>
                    <input type="text" id="department-emg" style="width: calc(100% - 40px);" readonly>
                </div>
                <div class="modal-hospital-form-row wide">
                    <div class="textarea-container">
                        <div class="textarea-header">
                            {{ config('app.lang') != 'en' ? __('원인 및 조치내용 (서술)') : __('Cause and Action Details (Narrative)') }}
                        </div>
                        <textarea class="styled-textarea required-input"></textarea>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-primary modal-hospital-btn-storage"
                    style="background-color:#4E6880;color:white;padding:10px 160px" onclick="showConfirmationModal1()">
                    {{ config('app.lang') != 'en' ? __('저장') : __('Save') }}
                </button>
                <button type="button" class="btn btn-info modal-hospital-btn-cancellation"
                    style="color:white;padding:10px 160px" data-dismiss="modal">
                    {{ config('app.lang') != 'en' ? __('취소') : __('Cancel') }}
                </button>
            </div>

        </div>
    </div>
</div>
<!--  -->

<!-- modal form call patient -->
<div class="modal fade" id="alertFormModal2">
    <div class="modal-dialog modal-lg" style="max-width:800px">
        <div class="modal-content modal-hospital-content">

            <!-- Modal Header -->
            <div class="modal-header"
                style="background-color:#3CBAFF;padding:20px 24px;align-items: center;justify-content: center;position: relative;">
                <h4 class="modal-title" style="color:#FFFFFF">
                    {{ config('app.lang') != 'en' ? __('조치 내용 작성') : __('Action Details') }}
                </h4>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <div class="modal-hospital-form-row">
                    <div class="patient-info-card mt-2" style="width:100%">
                        <div class="patient-info-card-column patient-info-card-first-column">
                            <div class="patient-info-card-row patient-info-card-name-row">
                                <span class="patient-info-card-name" id="patient-name"></span>
                                <div class="patient-info-card-row" style="margin-right:0px;margin-left:120px">
                                    <span
                                        class="patient-info-card-gender">{{ config('app.lang') != 'en' ? __('성별: ') : __('Gender: ') }}
                                        <span id="gender">123</span>
                                        <i class="patient-info-card-icon" id="icon-gender"></i></span>
                                    <div class="patient-info-card-vertical-divider"></div>
                                    <span
                                        class="patient-info-card-age">{{ config('app.lang') != 'en' ? __('나이: ') : __('Age: ') }}
                                        <span id="age">123</span></span>
                                </div>
                            </div>
                            <div class="patient-info-card-row patient-info-card-condition-row">
                                <span
                                    class="patient-info-card-condition">{{ config('app.lang') != 'en' ? __('주증상: ') : __('Main symptom: ') }}
                                    <span class="patient-info-card-low" id="main-symptom">123</span></span>
                            </div>
                        </div>
                        <div class="patient-info-card-column patient-info-card-middle-column">
                            <div class="patient-info-card-row patient-info-card-condition-row">
                                <span
                                    class="patient-info-card-room">{{ config('app.lang') != 'en' ? __('입원실: ') : __('Room: ') }}
                                    <span id="room">123</span></span>
                            </div>
                            <div class="patient-info-card-row patient-info-card-condition-row">
                                <span
                                    class="patient-info-card-time">{{ config('app.lang') != 'en' ? __('알림 발생시간: ') : __('Alert time: ') }}
                                    <span id="alert-time">123</span></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-hospital-form-row">
                    <label>{{ config('app.lang') != 'en' ? __('주치의') : __('Primary Doctor') }}</label>
                    <input type="text" id="primary-doctor" readonly>
                    <label>{{ config('app.lang') != 'en' ? __('담당간호사') : __('Nurse in Charge') }}</label>
                    <input type="text" id="nurse-in-charge" readonly>
                </div>
                <div class="modal-hospital-form-row wide">
                    <label>{{ config('app.lang') != 'en' ? __('진료과목') : __('Department') }}</label>
                    <input type="text" id="department" style="width: calc(100% - 40px);" readonly>
                </div>
                <div class="modal-hospital-form-row wide">
                    <div class="textarea-container">
                        <div class="textarea-header">
                            {{ config('app.lang') != 'en' ? __('원인 및 조치내용 (서술)') : __('Cause and Action Details (Narrative)') }}
                        </div>
                        <textarea class="styled-textarea required-input" id="cause_and_actions"></textarea>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-primary modal-hospital-btn-storage"
                    style="background-color:#4E6880;color:white;padding:10px 160px" onclick="showConfirmationModal2()">
                    {{ config('app.lang') != 'en' ? __('저장') : __('Save') }}
                </button>
                <button type="button" class="btn btn-info modal-hospital-btn-cancellation"
                    style="color:white;padding:10px 160px" data-dismiss="modal">
                    {{ config('app.lang') != 'en' ? __('취소') : __('Cancel') }}
                </button>
            </div>

        </div>
    </div>
</div>
<!--  -->

<!-- modal confirmation emergency -->
<div class="modal fade" id="alertConfirmation3Modal">
    <div class="modal-dialog modal-sm" style="max-width:500px;">
        <div class="modal-content modal-dashboard-alarm-content">
            <div class="modal-header modal-dashboard-alarm-header" style="background-color:#3CBAFF;text-align:center">
                <h4 class="modal-title modal-dashboard-alarm-title" style="color:#FFFFFF;text-align:center">
                    {{ config('app.lang') != 'en' ? __('알림확인') : __('Alert Confirmation') }}
                </h4>
            </div>
            <div class="modal-body modal-dashboard-alarm-body">
                <div class="text-center" style="background-color:#192734;padding:20px;">
                    <p class="modal-dashboard-alarm-text" style="color:#FFFFFF;margin-top:20px;">
                        {{ config('app.lang') != 'en' ? __('조치내용을 작성완료 처리 하시겠습니까?') : __('Are you sure you want to complete the action?') }}
                    </p>
                    <p class="text-danger modal-dashboard-alarm-text-danger" style="color:#FF6C36;">
                        {{ config('app.lang') != 'en' ? __('작성완료 처리 후 내용수정이 불가합니다.') : __('It is not possible to edit the content after completion.') }}
                    </p>
                </div>
                <div class="d-flex justify-content-around modal-dashboard-alarm-button-group">
                    <button class="btn btn-info modal-dashboard-alarm-btn-confirm"
                        style="background-color:#4E6880;color:white;" onclick="submitAlert()">
                        {{ config('app.lang') != 'en' ? __('확인') : __('Confirm') }}
                    </button>
                    <button class="btn btn-info modal-dashboard-alarm-btn-cancel" data-dismiss="modal"
                        style="color:white;">
                        {{ config('app.lang') != 'en' ? __('취소') : __('Cancel') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<!--  -->

<!-- modal confirmation call patient -->
<div class="modal fade" id="alertConfirmation4Modal">
    <div class="modal-dialog modal-sm" style="max-width:500px;">
        <div class="modal-content modal-dashboard-alarm-content">
            <div class="modal-header modal-dashboard-alarm-header" style="background-color:#3CBAFF;text-align:center">
                <h4 class="modal-title modal-dashboard-alarm-title" style="color:#FFFFFF;text-align:center">
                    {{ config('app.lang') != 'en' ? __('알림확인') : __('Alert Confirmation') }}
                </h4>
            </div>
            <div class="modal-body modal-dashboard-alarm-body">
                <div class="text-center" style="background-color:#192734;padding:20px;">
                    <p class="modal-dashboard-alarm-text" style="color:#FFFFFF;margin-top:20px;">
                        {{ config('app.lang') != 'en' ? __('조치내용을 작성완료 처리 하시겠습니까?') : __('Are you sure you want to complete the action?') }}
                    </p>
                    <p class="text-danger modal-dashboard-alarm-text-danger" style="color:#FF6C36;">
                        {{ config('app.lang') != 'en' ? __('작성완료 처리 후 내용수정이 불가합니다.') : __('It is not possible to edit the content after completion.') }}
                    </p>
                </div>
                <div class="d-flex justify-content-around modal-dashboard-alarm-button-group">
                    <button class="btn btn-info modal-dashboard-alarm-btn-confirm"
                        style="background-color:#4E6880;color:white;" onclick="submitPatientCall()">
                        {{ config('app.lang') != 'en' ? __('확인') : __('Confirm') }}
                    </button>
                    <button class="btn btn-info modal-dashboard-alarm-btn-cancel" data-dismiss="modal"
                        style="color:white;">
                        {{ config('app.lang') != 'en' ? __('취소') : __('Cancel') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<!--  -->

<script>
function fetchData() {
    fetch('/hospital/nav-data')
        .then(response => response.json())
        .then(data => {
            // Misalkan API mengembalikan objek dengan properti emergencyCount dan callCount
            // const emergencyCount = data.emergencyCount;
            // const callCount = data.callCount;
            data = data.data
            const emergencyCount = data.emergencyNeedConfirmCount; // nilai pertama
            const totalEmergencyCount = data.emergencyCompletedConfirmCount; // nilai kedua
            const callCount = data.patientCallNeedConfirmCount; // nilai pertama
            const totalCallCount = data.patientCallCompletedConfirmCount; // nilai kedua

            // Update elemen HTML dengan data yang diambil
            const emergencyElement = document.querySelector('.emergency-count');
            emergencyElement.textContent = totalEmergencyCount;
            emergencyElement.previousSibling.textContent =
                `{{ config('app.lang') != 'en' ? __('긴급') : __('Emergency') }} ${emergencyCount} / `;

            const callElement = document.querySelector('.call-count');
            callElement.textContent = totalCallCount;
            callElement.previousSibling.textContent =
                `{{ config('app.lang') != 'en' ? __('호출') : __('Call') }} ${callCount} / `;

        })
        .catch(error => {
            console.error('Error fetching data:', error);
        });
}
document.addEventListener('DOMContentLoaded', function() {
    fetchData()
});
</script>

<script>
function changePassword(action) {
    const changePasswordModal = document.getElementById('changePasswordModal');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');

    if (action === 'show') {
        changePasswordModal.style.display = 'block';
    } else if (action === 'hide') {
        changePasswordModal.style.display = 'none';
    } else if (action === 'submit') {
        event.preventDefault();
        // Check if passwords match
        const check = validatePassword(passwordInput.value, confirmPasswordInput.value);
        if (check !== true) {
            return
        }

        const formData = new FormData();
        formData.append('password', passwordInput.value);
        formData.append('password_confirmation', confirmPasswordInput.value);
        formData.append('_token', document.querySelector('input[name="_token"]').value);

        fetch('/general-purpose/change-account-password', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('error-message-pop-up').innerText = data.message
                    $('#errorFailedWithMessagePopupModal').modal('show');
                    changePasswordModal.style.display = 'none';
                } else {
                    $('#errorFailedPopupModal').modal('show');
                }
            })
            .catch(error => console.error('Error:', error));
    }
}
</script>
<!--  -->

<!--  handle notif emergency & call patient -->
<script>
let emergencyId = null
let patientCallId = null
const langApp = `{{ config('app.lang') }}`;

function showModal1(id) {
    emergencyId = id
    fetch(`/hospital/history/emergency-alerts/${id}`)
        .then(response => response.json())
        .then(data => {
            data = data.data
            // Fill in the input fields with the data

            let gender = '-'
            let iconGender = ``
            if (data.patient_hospital) {
                if (data.patient_hospital.gender === 'Man' || data.patient_hospital.gender === 'M') {
                    gender = `{{ config('app.lang') != 'en' ? __('남성 ') : __('Man')}}`
                    iconGender = `<img src="{{ asset('black') }}/icons/general/men.svg"></i>`
                } else if (data.patient_hospital.gender === 'Woman' || data.patient_hospital.gender === 'F') {
                    gender = `{{ config('app.lang') != 'en' ? __('여성 ') : __('Woman')}}`
                    iconGender = `<img src="{{ asset('black') }}/icons/general/women.svg"></i>`
                }
            }

            document.getElementById('patient-name-emg').innerText = data.patient_name;
            document.getElementById('gender-emg').innerText = gender;
            document.getElementById('icon-gender-emg').innerHTML = iconGender;
            document.getElementById('age-emg').innerText = data.patient_hospital ? data.patient_hospital.age : '-';
            document.getElementById('room-emg').innerText = data.ward;
            document.getElementById('alert-time-emg').innerText = data.alert_occurrence_time;
            document.getElementById('primary-doctor-emg').value = data.doctor_in_charge;
            document.getElementById('nurse-in-charge-emg').value = data.nurse_in_charge;
            document.getElementById('department-emg').value = '-';

            const messageTextEmergency = langApp !== 'en' ?
                `${data.ward}호 ${data.patient_name} 님의 알림을 확인처리 하겠습니까?` :
                `Do you want to confirm the alert for Room ${data.ward}, ${data.patient_name}?`;

            let mainSymptomEmgText = ``
            let dynamicContentHtml = ``
            const alertDetails = data.alert_details.split(',')
            const alertTypes = data.alert_type.split(',')
            alertDetails.forEach((alertDetail, index) => {
                let alertType = '';
                if (alertTypes[index]) {
                    if (alertTypes[index] === 'caution') {
                        if (langApp !== 'en') {
                            alertType = '주의'
                        } else {
                            alertType = 'Caution'
                        }
                    } else {
                        if (langApp !== 'en') {
                            alertType = '위험'
                        } else {
                            alertType = 'Danger'
                        }
                    }
                }
                if (index === 0) {
                    mainSymptomEmgText = mainSymptomEmgText + `
                                <div class="patient-info-card-row patient-info-card-condition-row">
                                    <span class="patient-info-card-condition">
                                    {{ config('app.lang') != 'en' ? __('주증상: ') : __('Main symptom: ') }} ${alertDetail}
                                        <span class="patient-info-card-${alertTypes[index]}">${alertType}</span></span>
                                </div>`
                } else {
                    mainSymptomEmgText = mainSymptomEmgText + `
                                <div class="patient-info-card-row patient-info-card-condition-row">
                                    <span class="patient-info-card-condition" style="padding-left: {{ config('app.lang') != 'en' ? '3em' : '8em' }};">
                                    ${alertDetail}
                                        <span class="patient-info-card-${alertTypes[index]}">${alertType}</span></span>
                                </div>`
                }



                dynamicContentHtml = dynamicContentHtml +
                    `<p class="modal-dashboard-alarm-text-${alertTypes[index]}">${alertDetail}</p>`
            })

            document.getElementById('main-symptom-emg-text').innerHTML = mainSymptomEmgText
            dynamicContentHtml = dynamicContentHtml +
                `<p class="modal-dashboard-alarm-text" style="color:#FFFFFF;margin-bottom:20px;">${messageTextEmergency}</p>`
            document.getElementById('dynamic-content').innerHTML = dynamicContentHtml;

            // play sound 
            playSound(`{{ asset('black/sounds/mixkit-bell-notification-933.wav') }}`).then(() => {}).catch((
                error) => {});

            // Show the form modal
            $('#alertConfirmation1Modal').modal('show');
        })
        .catch(error => console.error('Error:', error));
}

function showModal2(id) {
    patientCallId = id
    fetch(`/hospital/history/patient-calls/${id}`)
        .then(response => response.json())
        .then(data => {
            data = data.data
            // Fill in the input fields with the data
            let gender = '-'
            let iconGender = ``
            if (data.patient_hospital) {
                if (data.patient_hospital.gender === 'Man' || data.patient_hospital.gender === 'M') {
                    gender = `{{ config('app.lang') != 'en' ? __('남성 ') : __('Man')}}`
                    iconGender = `<img src="{{ asset('black') }}/icons/general/men.svg"></i>`
                } else if (data.patient_hospital.gender === 'Woman' || data.patient_hospital.gender === 'F') {
                    gender = `{{ config('app.lang') != 'en' ? __('여성 ') : __('Woman')}}`
                    iconGender = `<img src="{{ asset('black') }}/icons/general/women.svg"></i>`
                }
            }

            document.getElementById('patient-name').innerText = data.patient_name;
            document.getElementById('gender').innerText = gender;
            document.getElementById('icon-gender').innerHTML = iconGender;
            document.getElementById('age').innerText = data.patient_hospital ? data.patient_hospital.age : '-';
            document.getElementById('main-symptom').innerText = data.call_details;
            document.getElementById('room').innerText = data.ward;
            document.getElementById('alert-time').innerText = data.call_occurrence_time;
            document.getElementById('primary-doctor').value = data.doctor_in_charge;
            document.getElementById('nurse-in-charge').value = data.nurse_in_charge;
            document.getElementById('department').value = '-';

            const message = langApp != 'en' ? `${data.ward}호 ${data.patient_name} 님의 알림을 확인처리 하겠습니까?` :
                `Do you want to confirm the alert for Room ${data.ward}, ${data.patient_name}?`;

            document.getElementById('text-call-patient').innerHTML = message

            // play sound 
            playSound(`{{ asset('black/sounds/mixkit-bell-notification-933.wav') }}`).then(() => {}).catch((
                error) => {});

            // Show the form modal
            $('#alertConfirmation2Modal').modal('show');
        })
        .catch(error => console.error('Error:', error));
}

function showFormModal1() {
    $('#alertConfirmation1Modal').modal('hide');
    $('#alertFormModal1').modal('show');
}

function showFormModal2() {
    $('#alertConfirmation2Modal').modal('hide');
    $('#alertFormModal2').modal('show');
}

function showConfirmationModal1() {
    if (!globalValidateInputForModal('alertFormModal1')) {
        document.getElementById('error-message-pop-up').innerText =
            `{{ config('app.lang') != 'en' ? __('세부 정보를 삽입하십시오') : __('Please insert your details') }}`
        $('#errorFailedWithMessagePopupModal').modal('show');
        return
    }
    $('#alertFormModal1').modal('hide');
    $('#alertConfirmation3Modal').modal('show');
}

function showConfirmationModal2() {
    if (!globalValidateInputForModal('alertFormModal2')) {
        document.getElementById('error-message-pop-up').innerText =
            `{{ config('app.lang') != 'en' ? __('세부 정보를 삽입하십시오') : __('Please insert your details') }}`
        $('#errorFailedWithMessagePopupModal').modal('show');
        return
    }
    $('#alertFormModal2').modal('hide');
    $('#alertConfirmation4Modal').modal('show');
}



function submitAlert() {
    // Gather form data
    let causeAndActionDetails = document.querySelector('.styled-textarea').value;

    // Create the payload
    let data = {
        cause_and_actions: causeAndActionDetails
    };

    // Send the data to the API
    fetch('/hospital/history/emergency-alerts/content/' + emergencyId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            $('#successPopupModal').modal('show');
        })
        .catch(error => console.error('Error:', error));
}

function submitPatientCall() {
    // Gather form data
    let causeAndActionDetails = document.getElementById('cause_and_actions').value;

    // Create the payload
    let data = {
        cause_and_actions: causeAndActionDetails
    };

    // Send the data to the API
    fetch('/hospital/history/patient-calls/content/' + patientCallId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            $('#successPopupModal').modal('show');
        })
        .catch(error => console.error('Error:', error));
}
</script>

<script>
let socket;
let reconnectDelay = 5000; // 5 detik sebelum reconnect
let pingInterval;

// Fungsi untuk menginisialisasi WebSocket
function initWebSocket() {
    socket = new WebSocket(`{{config('app.ws.notification')}}`);

    // Event: WebSocket Terhubung
    socket.addEventListener('open', function() {
        console.log('✅ WebSocket connected.');

        // Kirim pesan inisialisasi
        socket.send(JSON.stringify({
            type: 'init',
            data: 'Client connected'
        }));

        // Mulai interval ping setiap 25 detik
        if (pingInterval) clearInterval(pingInterval);
        pingInterval = setInterval(() => {
            if (socket.readyState === WebSocket.OPEN) {
                socket.send(JSON.stringify({
                    type: "ping"
                }));
            }
        }, 25000);
    });

    // Event: Menerima pesan dari server
    socket.addEventListener('message', function(event) {
        console.log('📩 Message from server: ', event.data);
        let payload = JSON.parse(event.data);
        payload = payload.data;

        const {
            title,
            body
        } = payload.notification;
        const soundUrl = payload.data && payload.data.custom_sound ?
            payload.data.custom_sound :
            `{{ asset('black/sounds/mixkit-bell-notification-933.wav') }}`;

        if (soundUrl && userInteracted) {
            playSoundAndShowModal(soundUrl, title, body);
        } else if (soundUrl) {
            pendingNotification = {
                soundUrl,
                title,
                body
            };
            showModal(title, body);
        } else {
            showModal(title, body);
        }
    });

    // Event: WebSocket Terputus (Coba Reconnect)
    socket.addEventListener('close', function() {
        console.warn('⚠️ WebSocket closed. Reconnecting in 5 seconds...');
        clearInterval(pingInterval);
        setTimeout(initWebSocket, reconnectDelay);
    });

    // Event: Error WebSocket
    socket.addEventListener('error', function(error) {
        console.error('❌ WebSocket Error:', error);
        socket.close(); // Tutup koneksi agar bisa mencoba reconnect
    });
}

// Jalankan WebSocket pertama kali
initWebSocket();

// Fungsi untuk menyimpan token dengan WebSocket
function saveToken(token) {
    let data = {
        fcm_token: token,
        notification_type: 'PUBLIC_WELFARE'
    };

    if (socket.readyState === WebSocket.OPEN) {
        socket.send(JSON.stringify({
            type: 'saveToken',
            data: data
        }));
    } else {
        console.error('⚠️ WebSocket is not open');
    }
}

// Notifikasi & Interaksi User
let userInteracted = true;
let pendingNotification = null;

document.addEventListener('click', () => {
    userInteracted = true;
    if (pendingNotification) {
        playSoundAndShowModal(pendingNotification.soundUrl, pendingNotification.title, pendingNotification
            .body);
        pendingNotification = null;
    }
});

function playSound(url) {
    const audio = new Audio(url);
    return audio.play().then(() => {
        console.log('🔊 Playing notification sound');
    }).catch((error) => {
        console.error('❌ Failed to play notification sound:', error);
    });
}

function playSoundAndShowModal(url, title, body) {
    showModal(title, body);
}

function showModal(title, body) {
    if (title === 'CALL_PATIENT_HOSPITAL') {
        showModal2(body);
        fetchData();
    } else if (title === 'EMERGENCY_HOSPITAL') {
        showModal1(body);
        fetchData();
    }
}

window.addEventListener('focus', () => {
    if (pendingNotification) {
        playSound(pendingNotification.soundUrl).then(() => {
            pendingNotification = null;
        });
    }
});
</script>

<!--  -->