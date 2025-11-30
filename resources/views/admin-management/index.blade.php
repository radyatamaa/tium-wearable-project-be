@php
$pageTitle = config('app.lang') != 'en' ? __('관리자관리') : __('Admin Management');
@endphp

@extends('layouts.app', ['page' => $pageTitle, 'pageSlug' => 'admin_management'])

@section('content-fluid')
<!-- filters -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-primary d-flex justify-content-between"
                style="--header-background-color: #1E3142; background-color: var(--header-background-color);padding: 5px;">
                <div class="header-hospital-filter-container">
                    <div class="header-hospital-filter-row">
                        <form id="filter-form" method="GET" action="/admin-management"
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
                                        {{ __('관리자등급') }}
                                        @else
                                        {{ __('Admin Level') }}
                                        @endif
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="#" data-value="administrator_level">
                                            @if (config('app.lang') != 'en')
                                            {{ __('관리자등급') }}
                                            @else
                                            {{ __('Admin Level') }}
                                            @endif
                                        </a>
                                        <a class="dropdown-item" href="#" data-value="user_id">
                                            @if (config('app.lang') != 'en')
                                            {{ __('아이디') }}
                                            @else
                                            {{ __('Username') }}
                                            @endif
                                        </a>
                                        <a class="dropdown-item" href="#" data-value="name">
                                            @if (config('app.lang') != 'en')
                                            {{ __('관리자명') }}
                                            @else
                                            {{ __('Admin Name') }}
                                            @endif
                                        </a>
                                        <a class="dropdown-item" href="#" data-value="position">
                                            @if (config('app.lang') != 'en')
                                            {{ __('직위') }}
                                            @else
                                            {{ __('Position') }}
                                            @endif
                                        </a>
                                        <a class="dropdown-item" href="#" data-value="phone_number">
                                            @if (config('app.lang') != 'en')
                                            {{ __('휴대폰번호') }}
                                            @else
                                            {{ __('Phone Number') }}
                                            @endif
                                        </a>
                                        <a class="dropdown-item" href="#" data-value="email">
                                            @if (config('app.lang') != 'en')
                                            {{ __('이메일') }}
                                            @else
                                            {{ __('Email') }}
                                            @endif
                                        </a>
                                        <a class="dropdown-item" href="#" data-value="last_login">
                                            @if (config('app.lang') != 'en')
                                            {{ __('최종로그인') }}
                                            @else
                                            {{ __('Last Login') }}
                                            @endif
                                        </a>
                                        <a class="dropdown-item" href="#" data-value="registration_date">
                                            @if (config('app.lang') != 'en')
                                            {{ __('관리자등록일') }}
                                            @else
                                            {{ __('Admin Registration Date') }}
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
        <div class="card mb-3" style="background-color: #2c3e50; color: #FFFFFF;">
            <div class="card-header card-header-primary d-flex justify-content-between"
                style="--header-background-color: #1E3142;">
                <div>
                    <button class="btn btn-warning btn-sm" type="button" id="select-all">
                        {{ config('app.lang') != 'en' ? __('전체 선택') : __('Select All'); }}
                    </button>
                    <button class="btn btn-info btn-sm" type="button" id="deselect-all">
                        {{ config('app.lang') != 'en' ? __('전체 해제') : __('Deselect All'); }}
                    </button>
                    <button class="btn btn-danger btn-sm" type="button" id="delete-selected">
                        {{ config('app.lang') != 'en' ? __('삭제') : __('Delete'); }}
                    </button>
                    <button class="btn btn-primary btn-sm" type="button" id="register-admin">
                        {{ config('app.lang') != 'en' ? __('관리자 등록') : __('Register Admin'); }}
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="styled-table-container">
                    <table class="styled-table">
                        <thead>
                            <tr>
                                <th class="text-center">{{ config('app.lang') != 'en' ? __('선택') : __('Select'); }}
                                </th>
                                <th>{{ config('app.lang') != 'en' ? __('번호') : __('Number'); }}</th>
                                <th>{{ config('app.lang') != 'en' ? __('관리자등급') : __('Admin Level'); }}</th>
                                <th>{{ config('app.lang') != 'en' ? __('아이디') : __('Username'); }}</th>
                                <th>{{ config('app.lang') != 'en' ? __('관리자명') : __('Admin Name'); }}</th>
                                <th>{{ config('app.lang') != 'en' ? __('직위') : __('Position'); }}</th>
                                <th>{{ config('app.lang') != 'en' ? __('휴대폰번호') : __('Phone Number'); }}</th>
                                <th>{{ config('app.lang') != 'en' ? __('이메일') : __('Email'); }}</th>
                                <th>{{ config('app.lang') != 'en' ? __('최종로그인') : __('Last Login'); }}</th>
                                <th>{{ config('app.lang') != 'en' ? __('관리자등록일') : __('Admin Registration Date'); }}
                                </th>
                                <th class="text-center">{{ config('app.lang') != 'en' ? __('관리') : __('Manage'); }}
                                </th>
                            </tr>
                        </thead>
                        <tbody id="admin-table-body">
                            @foreach ($admins as $admin)
                            @if($admin->user_id == '001')
                            @continue
                            @endif
                            <tr data-id="{{ $admin->id }}">
                                <td class="text-center"><input type="checkbox" class="admin-management-checkbox"
                                        value="{{ $admin['id'] }}"></td>
                                <td>{{ $loop->iteration + ($admins->currentPage() - 1) * $admins->perPage() }}</td>
                                <td>{{ $admin->administrator_level }}</td>
                                <td>{{ $admin->user_id }}</td>
                                <td>{{ $admin->name }}</td>
                                <td>{{ $admin->position }}</td>
                                <td>{{ $admin->phone_number }}</td>
                                <td>{{ $admin->email }}</td>
                                <td>{{ $admin->last_login}}</td>
                                <td>{{ $admin->registration_date }}</td>
                                <td class="text-center">
                                    <button class="btn btn-primary btn-sm edit-admin"
                                        onclick="edit({{ $admin['id'] }})">
                                        {{ config('app.lang') != 'en' ? __('등록정보수정') : __('Edit Information'); }}
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class=" d-flex justify-content-center">
                    {{ $admins->links('layouts.pagination') }}
                </div>
            </div>
        </div>
    </div>
</div>
<!--  -->

<!-- forms -->
<div class="row">
    <div class="col-md-12">
        <div class="card mb-3" style="background-color: #2c3e50; color: #FFFFFF;display:none;" id="user-form">
            <div class="card-body">
                <!-- card header -->
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between">
                            <h5 class="title" style="color: #FFFFFF;">
                                {{ config('app.lang') != 'en' ? __('관리자 정보') : __('Admin Information'); }}
                            </h5>
                            <div>
                                <button class="btn btn-info btn-sm" type="button" id="list-btn" onclick="hideForm()">
                                    {{ config('app.lang') != 'en' ? __('목록') : __('List'); }}
                                </button>
                                <button class=" btn btn-primary mx-2 btn-sm" type="button" id="save-btn">
                                    {{ config('app.lang') != 'en' ? __('저장') : __('Save'); }}
                                </button>
                                <button class="btn btn-danger btn-sm" type="button" id="delete-btn"
                                    style="display:none;">
                                    {{ config('app.lang') != 'en' ? __('삭제') : __('Delete'); }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!--  -->

                <div class="row">
                    <div class="col-md-4">
                        <div
                            class="card-body-custom-1 flex-fill d-flex flex-column justify-content-center align-items-center text-center">
                            <div class="profile-picture">
                                <img src="{{ asset('black') }}/img/default-avatar.png" alt="Profile" class="profile-img"
                                    id="profileImage">
                                <label for="fileUpload" class="edit-icon">
                                    <input type="file" id="fileUpload" accept="image/*" onchange="loadFile(event)">
                                    <img src="{{ asset('black') }}/icons/general/pencil.svg">
                                </label>
                            </div>
                            <div>
                                <h2 class="title">{{ auth()->user()->name }}</h2>
                            </div>
                            <div>
                                <h4 class="title">User ID</h2>
                            </div>
                        </div>
                        <div class="card stats-card mt-3">
                            <div class="card-header w-100 text-left" style="background-color: #869CAF; color: #FFFFFF;">
                                <h5 class="title">
                                    {{ config('app.lang') != 'en' ? __('이용문의 현황') : __('Inquiry Status'); }}</h5>
                            </div>
                            <div
                                class="card-body-custom-1 flex-fill d-flex flex-column justify-content-left align-items-left w-100">
                                <div class="row mb-3">
                                    <label
                                        class="col-sm-3 col-form-label form-label-custom">{{ config('app.lang') != 'en' ? __('미확인') : __('Unconfirmed'); }}</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control form-control-custom" value="XX 개"
                                            disabled>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label
                                        class="col-sm-3 col-form-label form-label-custom">{{ config('app.lang') != 'en' ? __('접수중') : __('In Progress'); }}</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control form-control-custom" value="XX 개"
                                            disabled>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label
                                        class="col-sm-3 col-form-label form-label-custom">{{ config('app.lang') != 'en' ? __('답변완료') : __('Completed'); }}</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control form-control-custom" value="XX 개"
                                            disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card flex-fill d-flex flex-column"
                            style="background-color: #324D65; color: #FFFFFF;">
                            <div class="card-header w-100 text-left" style="background-color: #869CAF; color: #FFFFFF;">
                                <h5 class="title">
                                    {{ config('app.lang') != 'en' ? __('기본정보') : __('Basic Information'); }}</h5>
                            </div>
                            <div
                                class="card-body-custom-1 flex-fill d-flex flex-column justify-content-left align-items-left w-100">
                                <div class="row mb-3">
                                    <label for="adminRegistrationDate"
                                        class="col-sm-2 col-form-label form-label-custom">
                                        {{ config('app.lang') != 'en' ? __('관리자등록일') : __('Admin Registration Date'); }}
                                    </label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control form-control-custom"
                                            id="adminRegistrationDate" value="{{date('y-m-d')}}" disabled>
                                    </div>
                                    <label for="adminName" class="col-sm-2 col-form-label form-label-custom">
                                        {{ config('app.lang') != 'en' ? __('관리자명') : __('Admin Name'); }}
                                    </label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control form-control-custom required-input"
                                            id="adminName" value="김길동">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="lastLogin" class="col-sm-2 col-form-label form-label-custom">
                                        {{ config('app.lang') != 'en' ? __('최종 로그인') : __('Last Login'); }}
                                    </label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control form-control-custom" id="lastLogin"
                                            value="YYYY-MM-DD" disabled>
                                    </div>
                                    <label for="position" class="col-sm-2 col-form-label form-label-custom">
                                        {{ config('app.lang') != 'en' ? __('직위') : __('Position'); }}
                                    </label>
                                    <div class="col-sm-4">
                                        <select class="form-control required-input"
                                            style="background-color: #869CAF; color: #324D65;" id="position">
                                            <option>대표</option>
                                            <option>이사</option>
                                            <option>과장</option>
                                            <option>사원</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="adminLevel" class="col-sm-2 col-form-label form-label-custom">
                                        {{ config('app.lang') != 'en' ? __('관리자등급') : __('Admin Level'); }}
                                    </label>
                                    <div class="col-sm-4">
                                        <select class="form-control required-input"
                                            style="background-color: #869CAF; color: #324D65;" id="adminLevel">
                                            <option>최고관리자</option>
                                            <option>일반관리자</option>
                                        </select>
                                    </div>
                                    <label for="phoneNumber" class="col-sm-2 col-form-label form-label-custom">
                                        {{ config('app.lang') != 'en' ? __('휴대폰번호') : __('Phone Number'); }}
                                    </label>
                                    <div class="col-sm-4">
                                        <input type="text"
                                            class="form-control form-control-custom numeric-input required-input"
                                            id="phoneNumber" value="01012345678">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="adminId" class="col-sm-2 col-form-label form-label-custom">
                                        {{ config('app.lang') != 'en' ? __('관리자아이디') : __('Admin ID'); }}
                                    </label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control form-control-custom required-input"
                                            id="adminId" value="flywork">
                                    </div>
                                    <label for="email" class="col-sm-2 col-form-label form-label-custom">
                                        {{ config('app.lang') != 'en' ? __('이메일') : __('Email'); }}
                                    </label>
                                    <div class="col-sm-4">
                                        <input type="email" class="form-control form-control-custom required-input"
                                            id="email" value="flywork@flywork.co.kr">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="password" class="col-sm-2 col-form-label form-label-custom">
                                        {{ config('app.lang') != 'en' ? __('비밀번호') : __('Password'); }}
                                    </label>
                                    <div class="col-sm-4">
                                        <input type="password" class="form-control form-control-custom required-input"
                                            id="password"
                                            placeholder="{{ config('app.lang') != 'en' ? __('8-20자리의 숫자, 영문각각 1개 이상 포함') : __('8-20 characters with at least 1 number and 1 letter') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--  -->

<script>
function hideForm() {
    document.getElementById('user-form').style.display = 'none';
}
// Select/Deselect all checkboxes
document.getElementById('select-all').addEventListener('click', function() {
    document.querySelectorAll('.admin-management-checkbox').forEach(checkbox => checkbox.checked = true);
});

document.getElementById('deselect-all').addEventListener('click', function() {
    document.querySelectorAll('.admin-management-checkbox').forEach(checkbox => checkbox.checked = false);
});

// Load File for Profile Picture
function loadFile(event) {
    var image = document.getElementById('profileImage');
    image.src = URL.createObjectURL(event.target.files[0]);
}

function clearId() {
    document.querySelector('[data-id]').dataset.id = ''
}
// Save Admin (Create or Update)
document.getElementById('save-btn').addEventListener('click', function() {
    if (!globalValidateInput()) {
        document.getElementById('error-message-pop-up').innerText =
            `{{ config('app.lang') != 'en' ? __('필수 입력란을 모두 작성해 주세요.') : __('Please fill out all required fields.') }}`
        $('#errorFailedWithMessagePopupModal').modal('show');
        return
    }
    // Show the confirmation modal
    $('#savedConfirmationPopUpModal').modal('show');

    let id = '';
    if (document.querySelector('[data-id]')) {
        id = document.querySelector('[data-id]').dataset.id;
    }
    const formData = new FormData();
    const password = document.getElementById('password').value;
    formData.append('user_id', document.getElementById('adminId').value);
    formData.append('name', document.getElementById('adminName').value);
    formData.append('administrator_level', document.getElementById('adminLevel').value);
    formData.append('position', document.getElementById('position').value);
    formData.append('phone_number', document.getElementById('phoneNumber').value);
    formData.append('email', document.getElementById('email').value);
    formData.append('password', password);
    formData.append('fileUpload', document.getElementById('fileUpload').files[0]);


    let url = id ? `/admin-management/${id}` : '/admin-management';
    let method = id ? 'PUT' : 'POST';

    let headers = {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    }
    if (method === 'PUT') {
        method = 'POST'
        headers = {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-HTTP-Method-Override': 'PUT'
        }
    }
    // Add a one-time event listener for the confirm button
    document.getElementById('confirmed-saved').addEventListener('click', function confirmHandler() {
        if (password != '**********') {
            const checkvalidatePassword = validatePassword(password,
                '',
                false);
            if (checkvalidatePassword !== true) {
                // Remove the event listener after the click is handled
                document.getElementById('confirmed-saved').removeEventListener('click',
                    confirmHandler);
                // Hide the confirmation modal
                $('#savedConfirmationPopUpModal').modal('hide');
                return
            }
        }

        fetch(url, {
                method: method,
                headers: headers,
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                console.log(data);
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
            .catch(error => console.error('Error:', error))
            .finally(() => {
                // Remove the event listener after the click is handled
                document.getElementById('confirmed-saved').removeEventListener('click',
                    confirmHandler);
                // Hide the confirmation modal
                $('#savedConfirmationPopUpModal').modal('hide');
            });
    }, {
        once: true
    });
});

// Add delete button click event
document.getElementById('delete-btn').addEventListener('click', function() {
    const id = document.querySelector('[data-id]').dataset.id;
    if (!id) return;

    // Show the confirmation modal
    $('#deletedConfirmationPopUpModal').modal('show');

    // Add a one-time event listener for the confirm button
    document.getElementById('confirmed').addEventListener('click', function confirmHandler() {
        fetch('/admin-management/delete/' + id, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log(data);
                if (data.success) {
                    $('#successDeletedPopupModal').modal('show');
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
});

// Edit Admin
function edit(id) {
    fetch(`/admin-management/${id}`)
        .then(response => response.json())
        .then(data => {
            // Populate form with admin data
            if (data.photo) {
                document.getElementById('profileImage').src = '{{ asset("storage") }}/' + data.photo;
            } else {
                document.getElementById('profileImage').src = `{{ asset('black') }}/img/default-avatar.png`;
            }
            document.getElementById('adminName').value = data.name;
            document.getElementById('adminLevel').value = data.administrator_level;
            document.getElementById('position').value = data.position;
            document.getElementById('phoneNumber').value = data.phone_number;
            document.getElementById('email').value = data.email;
            document.getElementById('adminId').value = data.user_id;
            document.getElementById('lastLogin').value = data.last_login;
            document.getElementById('adminRegistrationDate').value = data.registration_date;
            document.getElementById('password').value = '**********';
            document.getElementById('user-form').style.display = 'block';
            document.getElementById('delete-btn').style.display = 'block';
            document.querySelector('[data-id]').dataset.id = id

            window.scrollTo({
                top: document.body.scrollHeight,
                behavior: 'smooth'
            });
        })
        .catch(error => console.error('Error:', error));
}
document.querySelectorAll('.edit-admin').forEach(button => {
    button.addEventListener('click', function() {

    });
});

// Register Admin
document.getElementById('register-admin').addEventListener('click', function() {
    document.getElementById('delete-btn').style.display = 'none';
    // Clear form fields
    if (document.querySelector('[data-id]')) {
        document.querySelector('[data-id]').dataset.id = '';
    }
    document.getElementById('adminName').value = '';
    document.getElementById('adminLevel').value = '';
    document.getElementById('position').value = '';
    document.getElementById('phoneNumber').value = '';
    document.getElementById('email').value = '';
    document.getElementById('password').value = '';
    document.getElementById('adminId').value = '';
    document.getElementById('profileImage').src = `{{ asset('black') }}/img/default-avatar.png`;
    document.getElementById('user-form').style.display = 'block';
    const now = new Date();
    const formattedDate = now.toISOString().split('T')[0];
    document.getElementById('adminRegistrationDate').value = formattedDate;

    // Scroll to the bottom of the page
    window.scrollTo({
        top: document.body.scrollHeight,
        behavior: 'smooth'
    });
});


// Delete Selected Admins
document.getElementById('delete-selected').addEventListener('click', function() {
    let selectedIds = [];
    document.querySelectorAll('.admin-management-checkbox:checked').forEach(checkbox => {
        selectedIds.push(checkbox.value);
    });

    if (selectedIds.length > 0) {
        // Show the confirmation modal
        $('#deletedConfirmationPopUpModal').modal('show');

        // Add a one-time event listener for the confirm button
        document.getElementById('confirmed').addEventListener('click', function confirmHandler() {
            // Proceed with the delete request
            fetch('/admin-management/bulk-delete', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        ids: selectedIds
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    if (data.success) {
                        $('#successDeletedPopupModal').modal('show');
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
@endsection