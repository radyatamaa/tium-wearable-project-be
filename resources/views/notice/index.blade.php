@php
$pageTitle = config('app.lang') != 'en' ? __('공지사항') : __('Notice');
@endphp

@extends('layouts.app', ['page' => $pageTitle, 'pageSlug' => 'notice'])

@section('content-fluid')
<!-- filters -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-primary d-flex justify-content-between"
                style="--header-background-color: #1E3142; background-color: var(--header-background-color);padding: 5px;">
                <div class="header-hospital-filter-container">
                    <div class="header-hospital-filter-row">
                        <form id="filter-form" method="GET" action="/notice"
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
                                        {{ __('제목') }}
                                        @else
                                        {{ __('Title') }}
                                        @endif
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="#" data-value="title">
                                            @if (config('app.lang') != 'en')
                                            {{ __('제목') }}
                                            @else
                                            {{ __('Title') }}
                                            @endif
                                        </a>
                                        <a class="dropdown-item" href="#" data-value="status">
                                            @if (config('app.lang') != 'en')
                                            {{ __('전시상태') }}
                                            @else
                                            {{ __('Status') }}
                                            @endif
                                        </a>
                                        <a class="dropdown-item" href="#" data-value="admin_level">
                                            @if (config('app.lang') != 'en')
                                            {{ __('관리자등급') }}
                                            @else
                                            {{ __('Admin Level') }}
                                            @endif
                                        </a>
                                        <a class="dropdown-item" href="#" data-value="admin_id">
                                            @if (config('app.lang') != 'en')
                                            {{ __('관리자아이디') }}
                                            @else
                                            {{ __('Admin ID') }}
                                            @endif
                                        </a>
                                        <a class="dropdown-item" href="#" data-value="admin_name">
                                            @if (config('app.lang') != 'en')
                                            {{ __('관리자명') }}
                                            @else
                                            {{ __('Admin Name') }}
                                            @endif
                                        </a>
                                        <a class="dropdown-item" href="#" data-value="registration_date">
                                            @if (config('app.lang') != 'en')
                                            {{ __('공지등록일') }}
                                            @else
                                            {{ __('Registration Date') }}
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
                    <button class="btn btn-primary btn-danger btn-sm" type="button" id="delete-selected">
                        @if (config('app.lang') != 'en')
                        {{ __('삭제') }}
                        @else
                        {{ __('Elimination') }}
                        @endif
                    </button>
                    <a href="javascript:void(0);" onclick="createNotice()" class="btn btn-primary btn-sm" type="button">
                        @if (config('app.lang') != 'en')
                        {{ __('공지 등록') }}
                        @else
                        {{ __('Register Notice') }}
                        @endif
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="styled-table-container">
                    <table class="styled-table">
                        <thead>
                            <tr>
                                <th class="text-center">
                                    {{ config('app.lang') != 'en' ? __('선택') : __('Choice') }}
                                </th>
                                <th>
                                    {{ config('app.lang') != 'en' ? __('번호') : __('Number') }}
                                </th>
                                <th>
                                    {{ config('app.lang') != 'en' ? __('제목') : __('Title') }}
                                </th>
                                <th>
                                    {{ config('app.lang') != 'en' ? __('전시상태') : __('Status') }}
                                </th>
                                <th>
                                    {{ config('app.lang') != 'en' ? __('관리자등급') : __('Admin Level') }}
                                </th>
                                <th>
                                    {{ config('app.lang') != 'en' ? __('관리자아이디') : __('Admin ID') }}
                                </th>
                                <th>
                                    {{ config('app.lang') != 'en' ? __('관리자명') : __('Admin Name') }}
                                </th>
                                <th>
                                    {{ config('app.lang') != 'en' ? __('공지등록일') : __('Registration Date') }}
                                </th>
                                <th class="text-right">
                                    {{ config('app.lang') != 'en' ? __('관리') : __('Management') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($notices as $notice)
                            <tr data-id="{{ $notice->id }}">
                                <td class="text-center"><input type="checkbox" class="notice-checkbox"></td>
                                <td>{{ $loop->iteration + ($notices->currentPage() - 1) * $notices->perPage() }}</td>
                                <td>{{ $notice->announcement_title }}</td>
                                <td>{{ $notice->status }}</td>
                                <td>{{ $notice->admin_level }}</td>
                                <td>{{ $notice->admin_id }}</td>
                                <td>{{ $notice->admin_name }}</td>
                                <td>{{ $notice->registration_date }}</td>
                                <td class=" td-actions text-right">
                                    <button class="btn btn-primary btn-sm" type="button"
                                        onclick="editNotice({{ $notice->id }})">
                                        {{ config('app.lang') != 'en' ? __('등록정보수정') : __('Edit Information') }}
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center">
                    {{ $notices->links('layouts.pagination') }}
                </div>
            </div>
        </div>
    </div>
</div>
<!--  -->

<!-- forms -->
<div class="row">
    <div class="col-md-12">
        <div class="card mb-3" style="background-color: #2c3e50; color: #FFFFFF; display:none;"
            id="notice-form-container">
            <div class="card-body">
                <!-- card header -->
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between">
                            <h5 class="title" style="color: #FFFFFF;">
                                {{ config('app.lang') != 'en' ? __('공지사항 정보') : __('Notice Information') }}
                            </h5>
                            <div>
                                <button class="btn btn-info btn-sm" type="button" onclick="hideForm()">
                                    {{ config('app.lang') != 'en' ? __('목록') : __('List') }}
                                </button>
                                <button class="btn btn-primary mx-2 btn-sm" type="button" id="save-notice">
                                    {{ config('app.lang') != 'en' ? __('저장') : __('Save') }}
                                </button>
                                <button class="btn btn-danger btn-sm" type="button" id="delete-notice"
                                    style="display:none;">
                                    {{ config('app.lang') != 'en' ? __('삭제') : __('Delete') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!--  -->

                <div class="row">
                    <div class="col-md-4">
                        <div class="card flex-fill d-flex flex-column"
                            style="background-color: #324D65; color: #FFFFFF;">
                            <div class="card-header w-100 text-left" style="background-color: #869CAF; color: #FFFFFF;">
                                <h5 class="title">
                                    {{ config('app.lang') != 'en' ? __('서비스 이용요금') : __('Service Usage Fee') }}
                                </h5>
                            </div>
                            <div
                                class="card-body-custom-1 flex-fill d-flex flex-column justify-content-left align-items-left w-100">
                                <div class="row mb-3 w-100 justify-content-center">
                                    <label for="serviceUsageFeeTheEntire" class="col-sm-3 col-form-label"
                                        style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('전체') : __('Total') }}</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="serviceUsageFeeTheEntire"
                                            style="background-color: #FFFFFF; color: #34495e;">
                                    </div>
                                </div>
                                <div class="row mb-3 w-100 justify-content-center">
                                    <label for="serviceUsageFeeExhibitOn" class="col-sm-3 col-form-label"
                                        style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('전시중') : __('On Display') }}</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="serviceUsageFeeExhibitOn"
                                            style="background-color: #FFFFFF; color: #34495e;">
                                    </div>
                                </div>
                                <div class="row mb-3 w-100 justify-content-center">
                                    <label for="serviceUsageFeeSuspensionOfExhibition" class="col-sm-3 col-form-label"
                                        style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('전시되지 않음') : __('Stopped Display') }}</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control"
                                            id="serviceUsageFeeSuspensionOfExhibition"
                                            style="background-color: #FFFFFF; color: #34495e;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card flex-fill d-flex flex-column"
                            style="background-color: #324D65; color: #FFFFFF;">
                            <div class="card-header w-100 text-left" style="background-color: #869CAF; color: #FFFFFF;">
                                <h5 class="title">기본정보</h5>
                            </div>
                            <div
                                class="card-body-custom-1 flex-fill d-flex flex-column justify-content-left align-items-left w-100">
                                <div class="row mb-3">
                                    <label for="registrationDate"
                                        class="col-sm-2 col-form-label form-label-custom">공지등록일</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control form-control-custom"
                                            id="registrationDate" placeholder="YYYY-MM-DD" disabled>
                                    </div>
                                    <label for="status" class="col-sm-2 col-form-label form-label-custom">전시상태</label>
                                    <div class="col-sm-4">
                                        <select class="form-control" id="status"
                                            style="background-color: #869CAF; color: #324D65;">
                                            <!-- show -->
                                            <option value="전시중">전시중</option>
                                            <!-- hide -->
                                            <option value="표시되지 않음">표시되지 않음</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="adminLevel"
                                        class="col-sm-2 col-form-label form-label-custom">관리자등급</label>
                                    <div class="col-sm-4">
                                        <select class="form-control" id="adminLevel"
                                            style="background-color: #869CAF; color: #324D65;">
                                            <!-- General Administrator -->
                                            <option value="일반관리자">일반관리자</option>
                                            <!-- middle administrator -->
                                            <option value="중간 관리자">중간 관리자</option>
                                            <!-- Final Administrator -->
                                            <option value="중간 관리자">중간 관리자</option>
                                        </select>
                                    </div>
                                    <label for="announcementTitle"
                                        class="col-sm-2 col-form-label form-label-custom">공지제목</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control form-control-custom"
                                            id="announcementTitle" placeholder="공지 제목을 입력해 주세요.">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="adminID"
                                        class="col-sm-2 col-form-label form-label-custom">관리자아이디</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control form-control-custom" id="adminID"
                                            value="flywork@flywork.co.kr" disabled>
                                    </div>
                                    <label for="announcementDetails"
                                        class="col-sm-2 col-form-label form-label-custom">공지내용</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control form-control-custom"
                                            id="announcementDetails" placeholder="공지 내용을 입력해 주세요.">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="adminName"
                                        class="col-sm-2 col-form-label form-label-custom">관리자명</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control form-control-custom" id="adminName"
                                            value="김길동" disabled>
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
function editNotice(id) {
    fetch(`/notice/${id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('registrationDate').value = data.registration_date;
            document.getElementById('status').value = data.status;
            document.getElementById('adminLevel').value = data.admin_level;
            document.getElementById('announcementTitle').value = data.announcement_title;
            document.getElementById('adminID').value = data.admin_id;
            document.getElementById('announcementDetails').value = data.Announcement_details;
            document.getElementById('adminName').value = data.admin_name;
            document.getElementById('serviceUsageFeeTheEntire').value = data.service_usage_fee_the_entire;
            document.getElementById('serviceUsageFeeExhibitOn').value = data.service_usage_fee_exhibit_on;
            document.getElementById('serviceUsageFeeSuspensionOfExhibition').value = data
                .service_usage_fee_suspension_of_exhibition;

            document.getElementById('notice-form-container').style.display = 'block';
            document.querySelector('[data-id]').dataset.id = id
            document.getElementById('delete-notice').style.display = 'block';

            // Scroll to the bottom of the page
            window.scrollTo({
                top: document.body.scrollHeight,
                behavior: 'smooth'
            });
        });
}

function hideForm() {
    document.getElementById('notice-form-container').style.display = 'none';
}

function createNotice() {

    // Clear form fields for new notice creation
    document.getElementById('status').value = '전시중'; // show
    document.getElementById('adminLevel').value = 'General Administrator';
    document.getElementById('announcementTitle').value = '';
    document.getElementById('adminID').value = '';
    document.getElementById('announcementDetails').value = '';
    document.getElementById('adminName').value = '';
    document.getElementById('serviceUsageFeeTheEntire').value = '';
    document.getElementById('serviceUsageFeeExhibitOn').value = '';
    document.getElementById('serviceUsageFeeSuspensionOfExhibition').value = '';

    // Set default registration date to now and hide the field
    const registrationDate = document.getElementById('registrationDate');
    const now = new Date();
    const formattedDate = now.toISOString().split('T')[0];
    registrationDate.value = formattedDate;
    registrationDate.style.display = 'block';

    // Enable other form fields
    document.getElementById('status').disabled = false;
    document.getElementById('adminLevel').disabled = false;
    document.getElementById('announcementTitle').disabled = false;
    document.getElementById('adminID').disabled = false;
    document.getElementById('announcementDetails').disabled = false;
    document.getElementById('adminName').disabled = false;
    document.getElementById('serviceUsageFeeTheEntire').disabled = false;
    document.getElementById('serviceUsageFeeExhibitOn').disabled = false;
    document.getElementById('serviceUsageFeeSuspensionOfExhibition').disabled = false;

    // Display the form
    document.getElementById('notice-form-container').style.display = 'block';
    document.querySelector('[data-id]').dataset.id = '';
    document.getElementById('delete-notice').style.display = 'none';

    // Scroll to the bottom of the page
    window.scrollTo({
        top: document.body.scrollHeight,
        behavior: 'smooth'
    });
}

document.getElementById('save-notice').addEventListener('click', function() {
    const id = document.querySelector('[data-id]').dataset.id;
    const method = id ? 'PUT' : 'POST';
    const url = id ? `/notice/${id}` : '/notice';
    const data = {
        status: document.getElementById('status').value,
        admin_level: document.getElementById('adminLevel').value,
        announcement_title: document.getElementById('announcementTitle').value,
        admin_id: document.getElementById('adminID').value,
        Announcement_details: document.getElementById('announcementDetails').value,
        admin_name: document.getElementById('adminName').value,
        service_usage_fee_the_entire: document.getElementById('serviceUsageFeeTheEntire').value,
        service_usage_fee_exhibit_on: document.getElementById('serviceUsageFeeExhibitOn').value,
        service_usage_fee_suspension_of_exhibition: document.getElementById(
            'serviceUsageFeeSuspensionOfExhibition').value,
    };

    fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                $('#successPopupModal').modal('show');
            } else {
                $('#errorFailedPopupModal').modal('show');
            }
        });
});

document.getElementById('delete-notice').addEventListener('click', function() {
    const id = document.querySelector('[data-id]').dataset.id;
    if (!id) return;

    // Show the confirmation modal
    $('#deletedConfirmationPopUpModal').modal('show');

    // Add a one-time event listener for the confirm button
    document.getElementById('confirmed').addEventListener('click', function confirmHandler() {
        fetch(`/notice/delete/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    $('#successPopupModal').modal('show');
                } else {
                    $('#errorFailedPopupModal').modal('show');
                }
            })
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

// Select/Deselect all checkboxes
document.getElementById('select-all').addEventListener('click', function() {
    document.querySelectorAll('.notice-checkbox').forEach(checkbox => checkbox.checked = true);
});

document.getElementById('deselect-all').addEventListener('click', function() {
    document.querySelectorAll('.notice-checkbox').forEach(checkbox => checkbox.checked = false);
});

document.getElementById('delete-selected').addEventListener('click', function() {
    const selectedIds = Array.from(document.querySelectorAll('.notice-checkbox:checked'))
        .map(checkbox => checkbox.closest('tr').dataset.id);

    if (selectedIds.length > 0) {
        // Show the confirmation modal
        $('#deletedConfirmationPopUpModal').modal('show');

        // Add a one-time event listener for the confirm button
        document.getElementById('confirmed').addEventListener('click', function confirmHandler() {
            fetch('/notice/bulk-delete', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            .getAttribute(
                                'content')
                    },
                    body: JSON.stringify({
                        ids: selectedIds
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        $('#successPopupModal').modal('show');
                    } else {
                        $('#errorFailedPopupModal').modal('show');
                    }
                })
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