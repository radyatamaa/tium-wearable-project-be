@php
    $pageTitle = config('app.lang') != 'en' ? __('계정 관리') : __('Account Management');
@endphp

@extends('layouts.app', ['page' => $pageTitle, 'pageSlug' => 'account_management'])

@section('content-fluid')
<!-- header -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-primary d-flex justify-content-between align-items-center p-2"
                style="--header-background-color: #324D65; background-color: var(--header-background-color);">
                <div class="header-hospital-filter-container">
                    <div class="header-hospital-filter-row">
                        <form id="filter-form" method="GET" action="/hospital/settings/account-management"
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
                                            {{ __('아이디') }}
                                        @else
                                            {{ __('ID') }}
                                        @endif
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="#" data-value="id">
                                            @if (config('app.lang') != 'en')
                                                {{ __('아이디') }}
                                            @else
                                                {{ __('ID') }}
                                            @endif
                                        </a>
                                        <a class="dropdown-item" href="#" data-value="name">
                                            @if (config('app.lang') != 'en')
                                                {{ __('담당자명') }}
                                            @else
                                                {{ __('Person in Charge') }}
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
                        {{ config('app.lang') != 'en' ? __('전체 선택') : __('Full selection') }}
                    </button>
                    <button class="btn btn-primary btn-info btn-sm" type="button" id="deselect-all">
                        {{ config('app.lang') != 'en' ? __('전체 해제') : __('Deselect All') }}
                    </button>
                    <button class="btn btn-primary btn-sm" type="button" data-toggle="modal"
                        data-target="#modalHospital">
                        {{ config('app.lang') != 'en' ? __('신규등록') : __('Account Registration') }}
                    </button>
                    <button class="btn btn-primary btn-danger btn-sm" type="button" id="delete-selected">
                        {{ config('app.lang') != 'en' ? __('삭제') : __('Delete Selected') }}
                    </button>
                    <button class="btn btn-primary btn-info btn-sm" type="button" id="export-excel"
                        onclick="exportExcel('{{ url('hospital/settings/account-management/export') }}')">
                        {{ config('app.lang') != 'en' ? __('엑셀 다운로드') : __('Download Excel') }}
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
                                <th class="text-center"><input type="checkbox" id="select-all-checkbox"></th>
                                <th>{{ config('app.lang') != 'en' ? __('번호') : __('Number') }}</th>
                                <th>{{ config('app.lang') != 'en' ? __('아이디') : __('ID') }}</th>
                                <th>{{ config('app.lang') != 'en' ? __('담당자명') : __('Person in Charge') }}</th>
                                <th>{{ config('app.lang') != 'en' ? __('계정권한') : __('Account Permission') }}</th>
                                <th>{{ config('app.lang') != 'en' ? __('등록일자') : __('Registration Date') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($accounts as $account)
                                @if($account->user_id == '001')
                                    @continue
                                @endif
                                <tr>
                                    <td><input type="checkbox" class="account-checkbox" value="{{ $account->id }}"></td>
                                    <td>{{ $loop->iteration + ($accounts->currentPage() - 1) * $accounts->perPage() }}</td>
                                    <td>{{ $account->user_id }}</td>
                                    <td>{{ $account->name_person_in_charge }}</td>
                                    <td>{{ $account->permission }}</td>
                                    <td>{{ $account->created_at }}</td>
                                    <td class="td-actions">
                                        <button class="btn btn-warning btn-sm" type="button"
                                            onclick="viewDetails({{ $account->id }})">
                                            {{ config('app.lang') != 'en' ? __('세부 사항') : __('Detail') }}
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center">
                    {{ $accounts->links('layouts.pagination') }}
                </div>
            </div>
        </div>
    </div>

    <!-- form -->
    <div class="col-md-6">
        <div class="card flex-fill d-flex flex-column" style="background-color: #1E3142; color: #FFFFFF;">
            <div class="card-header w-100 text-center" style="background-color: #324D65; color: #FFFFFF;">
                <h5 class="title" id="form-title">
                    {{ config('app.lang') != 'en' ? __('상세정보') : __('Detailed Information') }}
                </h5>
            </div>
            <div class="card-body-custom-1 flex-fill d-flex flex-column justify-content-left align-items-left w-100"
                style="background-color:#1E3142;min-height:448px;">
                <!-- detailed information -->
                <div class="content-section active" id="selected-user-form" style="display:none">
                    <!-- card header -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <h5 class="title" style="color: #FFFFFF;">
                                    {{ config('app.lang') != 'en' ? __('기본정보') : __('Basic Information') }}
                                </h5>
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
                                                    <td>{{ config('app.lang') != 'en' ? __('아이디') : __('ID') }}</td>
                                                    <td id="detail-user_id"
                                                        style="background-color:#1E3142;color:#FFFFFF"></td>
                                                </tr>
                                                <tr>
                                                    <td>{{ config('app.lang') != 'en' ? __('비밀번호') : __('Password') }}
                                                    </td>
                                                    <td id="detail-password"
                                                        style="background-color:#1E3142;color:#FFFFFF">**********</td>
                                                </tr>
                                                <tr>
                                                    <td>{{ config('app.lang') != 'en' ? __('담당자명') : __('Person in Charge') }}
                                                    </td>
                                                    <td id="detail-name_person_in_charge"
                                                        style="background-color:#1E3142;color:#FFFFFF"></td>
                                                </tr>
                                                <tr>
                                                    <td>{{ config('app.lang') != 'en' ? __('계정권한') : __('Account Permission') }}
                                                    </td>
                                                    <td id="detail-permission"
                                                        style="background-color:#1E3142;color:#FFFFFF"></td>
                                                </tr>
                                                <tr>
                                                    <td>{{ config('app.lang') != 'en' ? __('담당자 직위') : __('Position') }}
                                                    </td>
                                                    <td id="detail-contact_person_position"
                                                        style="background-color:#1E3142;color:#FFFFFF"></td>
                                                </tr>
                                                <tr>
                                                    <td>{{ config('app.lang') != 'en' ? __('담당자 연락처') : __('Contact Number') }}
                                                    </td>
                                                    <td id="detail-contact_number"
                                                        style="background-color:#1E3142;color:#FFFFFF"></td>
                                                </tr>
                                                <tr>
                                                    <td>{{ config('app.lang') != 'en' ? __('담당자 이메일') : __('Email') }}
                                                    </td>
                                                    <td id="detail-email"
                                                        style="background-color:#1E3142;color:#FFFFFF"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>

                <div id="not-selected-user-form">
                    <div class="card-body-custom-1 flex-fill d-flex flex-column justify-content-left align-items-left w-100"
                        style="background-color:#1E3142;min-height:448px;text-align: center;">
                        <div class="error-tium-icon">
                            <img src="{{ asset('black') }}/icons/general/error.svg" alt="warning">
                        </div>
                        <p class="error-tium-message">
                            {{ config('app.lang') != 'en' ? __('데이터 기록이 없습니다.') : __('There are no data records.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Add/Edit Account -->
<div class="modal fade" id="modalHospital">
    <div class="modal-dialog modal-lg" style="max-width:800px">
        <div class="modal-content modal-hospital-content">

            <!-- Modal Header -->
            <div class="modal-header"
                style="background-color:#3CBAFF;padding:20px 24px;align-items: center;justify-content: center;position: relative;">
                <h4 class="modal-title" style="color:#FFFFFF">
                    {{ config('app.lang') != 'en' ? __('관리자 등록') : __('Administrator Registration') }}
                </h4>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <div class="text-center" style="margin-bottom:20px">
                    <h4 class="modal-title" style="color:#FFFFFF">
                        {{ config('app.lang') != 'en' ? __('기본정보') : __('Basic Information') }}
                    </h4>
                </div>
                <div class="modal-hospital-form-row">
                    <label
                        for="date-registration-modal">{{ config('app.lang') != 'en' ? __('등록일자') : __('Date of registration') }}</label>
                    <input type="text" id="date-registration-modal" placeholder="YYYY-MM-DD" readonly>
                    <label
                        for="account-permissions-modal">{{ config('app.lang') != 'en' ? __('계정권한') : __('Account Permissions') }}</label>
                    <select id="account-permissions-modal">
                        <option>{{ config('app.lang') != 'en' ? __('선택') : __('Select') }}</option>
                    </select>
                </div>
                <div class="modal-hospital-form-row">
                    <label for="id-modal">{{ config('app.lang') != 'en' ? __('아이디') : __('ID') }}</label>
                    <input type="text" id="id-modal">
                    <label
                        for="contact-person-position-modal">{{ config('app.lang') != 'en' ? __('담당자 직위') : __('Contact person position') }}</label>
                    <input type="text" id="contact-person-position-modal">
                </div>
                <div class="modal-hospital-form-row">
                    <label for="password-modal">{{ config('app.lang') != 'en' ? __('비밀번호') : __('Password') }}</label>
                    <input type="password" id="password-modal">
                    <label for="contact-modal">{{ config('app.lang') != 'en' ? __('연락처') : __('Contact') }}</label>
                    <input type="text" id="contact-modal">
                </div>
                <div class="modal-hospital-form-row">
                    <label
                        for="reenter-password-modal">{{ config('app.lang') != 'en' ? __('비밀번호 재입력') : __('Re-entering the password') }}</label>
                    <input type="password" id="reenter-password-modal">
                    <label for="email-modal">{{ config('app.lang') != 'en' ? __('이메일') : __('E-mail') }}</label>
                    <input type="email" id="email-modal">
                </div>
                <div class="modal-hospital-form-row wide">
                    <label
                        for="name-person-in-charge-modal">{{ config('app.lang') != 'en' ? __('담당자명') : __('Name of person in charge') }}</label>
                    <input type="text" id="name-person-in-charge-modal" style="width: calc(100% - 40px);">
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-primary modal-hospital-btn-storage" style="padding:10px 160px"
                    onclick="saveAccountModal()">{{ config('app.lang') != 'en' ? __('저장') : __('Storage') }}</button>
                <button type="button" class="btn btn-info modal-hospital-btn-cancellation" style="padding:10px 160px"
                    data-dismiss="modal">{{ config('app.lang') != 'en' ? __('취소') : __('Cancellation') }}</button>
            </div>

        </div>
    </div>
</div>

<script>
    // Select/Deselect all checkboxes
    document.getElementById('select-all').addEventListener('click', function () {
        document.querySelectorAll('.account-checkbox').forEach(checkbox => checkbox.checked = true);
    });

    document.getElementById('deselect-all').addEventListener('click', function () {
        document.querySelectorAll('.account-checkbox').forEach(checkbox => checkbox.checked = false);
    });
    document.addEventListener('DOMContentLoaded', function () {
        var dateRegistrationInput = document.getElementById('date-registration-modal');
        var today = new Date();
        var day = String(today.getDate()).padStart(2, '0');
        var month = String(today.getMonth() + 1).padStart(2, '0'); // January is 0!
        var year = today.getFullYear();
        dateRegistrationInput.value = year + '-' + month + '-' + day;

        document.getElementById('select-all-checkbox').addEventListener('change', function () {
            var checkboxes = document.querySelectorAll('.account-checkbox');
            for (var checkbox of checkboxes) {
                checkbox.checked = this.checked;
            }
        });

        document.getElementById('delete-selected').addEventListener('click', function () {
            deleteSelected();
        });
    });

    function viewDetails(id) {
        fetch(`/hospital/settings/account-management/${id}`)
            .then(response => response.json())
            .then(data => {
                var account = data.account;
                document.getElementById('detail-user_id').innerText = account.user_id;
                document.getElementById('detail-password').innerText = '**********';
                document.getElementById('detail-name_person_in_charge').innerText = account.name_person_in_charge;
                document.getElementById('detail-permission').innerText = account.permission;
                document.getElementById('detail-contact_person_position').innerText = account.contact_person_position;
                document.getElementById('detail-contact_number').innerText = account.contact_number;
                document.getElementById('detail-email').innerText = account.email;

                document.getElementById('selected-user-form').style.display = 'block';
                document.getElementById('not-selected-user-form').style.display = 'none';
            })
            .catch(error => console.error('Error fetching account details:', error));
    }

    function saveAccountModal() {
        var data = {
            user_id: document.getElementById('id-modal').value,
            password: document.getElementById('password-modal').value,
            name_person_in_charge: document.getElementById('name-person-in-charge-modal').value,
            permission: document.getElementById('account-permissions-modal').value,
            registration_date: document.getElementById('date-registration-modal').value,
            contact_person_position: document.getElementById('contact-person-position-modal').value,
            contact_number: document.getElementById('contact-modal').value,
            email: document.getElementById('email-modal').value,
        };

        fetch('/hospital/settings/account-management', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
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
            .catch(error => console.error('Error saving account:', error));
    }

    function deleteSelected() {
        var selectedIds = Array.from(document.querySelectorAll('.account-checkbox:checked')).map(cb => cb.value);
        if (selectedIds.length > 0) {
            // Show the confirmation modal
            $('#deletedConfirmationPopUpModal').modal('show');

            // Add a one-time event listener for the confirm button
            document.getElementById('confirmed').addEventListener('click', function confirmHandler() {
                fetch('/hospital/settings/account-management', {
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
                        if (data.success) {
                            $('#successDeletedPopupModal').modal('show');
                        } else {
                            $('#errorFailedPopupModal').modal('show');
                        }

                    })
                    .catch(error => console.error('Error deleting accounts:', error))
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

    }
</script>
@endsection