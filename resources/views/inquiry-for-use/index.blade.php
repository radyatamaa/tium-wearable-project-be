@php
$pageTitle = config('app.lang') != 'en' ? __('이용문의') : __('Inquiry for use');
@endphp

@extends('layouts.app', ['page' => $pageTitle, 'pageSlug' => 'inquiry_for_use'])

@section('content-fluid')
<!-- filters -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-primary d-flex justify-content-between"
                style="--header-background-color: #1E3142; background-color: var(--header-background-color);padding: 5px;">
                <div class="header-hospital-filter-container">
                    <div class="header-hospital-filter-row">
                        <form id="filter-form" method="GET" action="/inquiry-for-use"
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
                </div>
            </div>
            <div class="card-body">
                <div class="styled-table-container">
                    <table class="styled-table">
                        <thead>
                            <tr>
                                <th class="text-center">{{ config('app.lang') != 'en' ? __('선택') : __('Select'); }}</th>
                                <th>{{ config('app.lang') != 'en' ? __('번호') : __('Number'); }}</th>
                                <th>{{ config('app.lang') != 'en' ? __('아이디') : __('Username'); }}</th>
                                <th>{{ config('app.lang') != 'en' ? __('업체기관명') : __('Organization'); }}</th>
                                <th>{{ config('app.lang') != 'en' ? __('사용자 형태') : __('User Type'); }}</th>
                                <th>{{ config('app.lang') != 'en' ? __('문의자명') : __('Inquirer'); }}</th>
                                <th>{{ config('app.lang') != 'en' ? __('제목') : __('Title'); }}</th>
                                <th>{{ config('app.lang') != 'en' ? __('처리상태') : __('Status'); }}</th>
                                <th>{{ config('app.lang') != 'en' ? __('공지등록일') : __('Registration Date'); }}</th>
                                <th>{{ config('app.lang') != 'en' ? __('답변처리자') : __('Responder'); }}</th>
                                <th class="text-center">{{ config('app.lang') != 'en' ? __('관리') : __('Manage'); }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($inquiries as $inquiry)
                            <tr>
                                <td class="text-center"><input type="checkbox" class="inquiry-for-use-checkbox"
                                        data-id="{{ $inquiry['id'] }}" value="{{ $inquiry->id }}"></td>
                                <td>{{ $loop->iteration + ($inquiries->currentPage() - 1) * $inquiries->perPage() }}
                                </td>
                                <td>{{ $inquiry['username']; }}</td>
                                <td>{{ $inquiry['organization']; }}</td>
                                <td>{{ $inquiry['user_type']; }}</td>
                                <td>{{ $inquiry['inquirer']; }}</td>
                                <td>{{ $inquiry['title']; }}</td>
                                <td>{{ $inquiry['status']; }}</td>
                                <td>{{ $inquiry['registration_date']; }}</td>
                                <td>{{ $inquiry['responder']; }}</td>
                                <td class="text-center">
                                    <button class="btn btn-primary btn-sm edit-button" type="button"
                                        data-id="{{ $inquiry['id'] }}">
                                        {{ config('app.lang') != 'en' ? __('등록정보수정') : __('Edit Information'); }}
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center">
                    {{ $inquiries->links('layouts.pagination') }}
                </div>
            </div>
        </div>
    </div>
</div>
<!--  -->

<!-- forms -->
<div class="row">
    <div class="col-md-12">
        <div class="card mb-3" style="background-color: #2c3e50; color: #FFFFFF; display:none;" id="inquiry-form">
            <div class="card-body">
                <!-- card header -->
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between">
                            <h5 class="title" style="color: #FFFFFF;">
                                {{ config('app.lang') != 'en' ? __('이용문의 정보') : __('Inquiry Information'); }}</h5>
                            <div>
                                <button class="btn btn-info btn-sm" type="button" id="list-button">
                                    {{ config('app.lang') != 'en' ? __('목록') : __('List'); }}
                                </button>
                                <button class="btn btn-primary mx-2 btn-sm" type="button" id="save-button">
                                    {{ config('app.lang') != 'en' ? __('저장') : __('Save'); }}
                                </button>
                                <button class="btn btn-warning mx-2 btn-sm" type="button" id="answer-processing-button">
                                    {{ config('app.lang') != 'en' ? __('답변처리') : __('Answer processing'); }}
                                </button>
                                <button class="btn btn-danger btn-sm" type="button" id="delete-button">
                                    {{ config('app.lang') != 'en' ? __('삭제') : __('Delete'); }}
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
                                    {{ config('app.lang') != 'en' ? __('이용문의 현황') : __('Inquiry Status'); }}</h5>
                            </div>
                            <div
                                class="card-body-custom-1 flex-fill d-flex flex-column justify-content-left align-items-left w-100">
                                <div class="row mb-3 w-100 justify-content-center">
                                    <label for="unconfirmed" class="col-sm-3 col-form-label" style="color: #FFFFFF;">
                                        {{ config('app.lang') != 'en' ? __('미확인') : __('Unconfirmed'); }}
                                    </label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control"
                                            style="background-color: #FFFFFF; color: #34495e;" value="XX 개" disabled>
                                    </div>
                                </div>
                                <div class="row mb-3 w-100 justify-content-center">
                                    <label for="inProgress" class="col-sm-3 col-form-label" style="color: #FFFFFF;">
                                        {{ config('app.lang') != 'en' ? __('접수중') : __('In Progress'); }}
                                    </label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control"
                                            style="background-color: #FFFFFF; color: #34495e;" value="XX 개" disabled>
                                    </div>
                                </div>
                                <div class="row mb-3 w-100 justify-content-center">
                                    <label for="completed" class="col-sm-3 col-form-label" style="color: #FFFFFF;">
                                        {{ config('app.lang') != 'en' ? __('답변완료') : __('Completed'); }}
                                    </label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control"
                                            style="background-color: #FFFFFF; color: #34495e;" value="XX 개" disabled>
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
                                    {{config('app.lang') != 'en' ? __('기본정보') : __('Basic Information'); }}</h5>
                            </div>
                            <div
                                class="card-body-custom-1 flex-fill d-flex flex-column justify-content-left align-items-left w-100">
                                <input type="hidden" id="inquiryId">
                                <div class="row mb-3">
                                    <label for="inquiryDate" class="col-sm-2 col-form-label form-label-custom">
                                        {{config('app.lang') != 'en' ? __('문의일자') : __('Inquiry Date'); }}
                                    </label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control form-control-custom" id="inquiryDate"
                                            placeholder="YYYY-MM-DD" disabled>
                                    </div>
                                    <label for="title" class="col-sm-2 col-form-label form-label-custom">
                                        {{config('app.lang') != 'en' ? __('제목') : __('Title'); }}
                                    </label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control form-control-custom" id="title"
                                            value="제목 제목 제목 제목">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="status" class="col-sm-2 col-form-label form-label-custom">
                                        {{config('app.lang') != 'en' ? __('처리상태') : __('Status'); }}
                                    </label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control form-control-custom" id="status"
                                            value="접수중">
                                    </div>
                                    <label for="content" class="col-sm-2 col-form-label form-label-custom">
                                        {{config('app.lang') != 'en' ? __('문의내용') : __('Content'); }}
                                    </label>
                                    <div class="col-sm-4">
                                        <textarea class="form-control form-control-custom" id="content"
                                            placeholder="{{config('app.lang') != 'en' ? __('문의 내용을 입력해 주세요.') : __('Please enter the content.'); }}"
                                            rows="3"></textarea>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="responseDate" class="col-sm-2 col-form-label form-label-custom">
                                        {{config('app.lang') != 'en' ? __('답변일자') : __('Response Date'); }}
                                    </label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control form-control-custom" id="responseDate"
                                            value="-" disabled>
                                    </div>
                                    <label for="responseContent" class="col-sm-2 col-form-label form-label-custom">
                                        {{config('app.lang') != 'en' ? __('답변내용') : __('Response Content'); }}
                                    </label>
                                    <div class="col-sm-4">
                                        <textarea class="form-control form-control-custom" id="responseContent"
                                            placeholder="{{config('app.lang') != 'en' ? __('답변을 입력해 주세요.') : __('Please enter the response.'); }}"
                                            rows="3"></textarea>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="responder" class="col-sm-2 col-form-label form-label-custom">
                                        {{config('app.lang') != 'en' ? __('답변처리자') : __('Responder'); }}
                                    </label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control form-control-custom" id="responder"
                                            value="-" disabled>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="userType" class="col-sm-2 col-form-label form-label-custom">
                                        {{config('app.lang') != 'en' ? __('사용자 형태') : __('User Type'); }}
                                    </label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control form-control-custom" id="userType"
                                            value="의료기관">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="organization" class="col-sm-2 col-form-label form-label-custom">
                                        {{config('app.lang') != 'en' ? __('업체(기관)명') : __('Organization'); }}
                                    </label>
                                    <div class="col-sm-2">
                                        <input type="text" class="form-control form-control-custom" id="organization"
                                            value="가나다서울병원">
                                    </div>

                                    <div class="col-sm-2">
                                        <button class="btn btn-info btn-sm w-100" type="button">
                                            {{config('app.lang') != 'en' ? __('회원정보보기') : __('View Member Info'); }}
                                        </button>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="inquirerName" class="col-sm-2 col-form-label form-label-custom">
                                        {{config('app.lang') != 'en' ? __('문의자명') : __('Inquirer Name'); }}
                                    </label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control form-control-custom" id="inquirerName"
                                            value="홍길동">
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
document.addEventListener('DOMContentLoaded', function() {
    const editButtons = document.querySelectorAll('.edit-button');
    const saveButton = document.getElementById('save-button');
    const deleteButton = document.getElementById('delete-button');
    const inquiryForm = document.getElementById('inquiry-form');
    const listButton = document.getElementById('list-button');

    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            fetch(`/inquiry-for-use/${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('inquiryId').value = data.id;
                    document.getElementById('inquiryDate').value = data.registration_date;
                    document.getElementById('title').value = data.title;
                    document.getElementById('status').value = data.status;
                    document.getElementById('content').value = data.inquiry_details;
                    document.getElementById('responseDate').value = data.response_date;
                    document.getElementById('responseContent').value = data
                        .response_details;
                    document.getElementById('responder').value = data.responder;
                    document.getElementById('userType').value = data.user_type;
                    document.getElementById('organization').value = data.organization;
                    document.getElementById('inquirerName').value = data.inquirer;
                    inquiryForm.style.display = 'block';

                    window.scrollTo({
                        top: document.body.scrollHeight,
                        behavior: 'smooth'
                    });
                });
        });
    });

    saveButton.addEventListener('click', function() {
        const id = document.getElementById('inquiryId').value;
        const formData = {
            title: document.getElementById('title').value,
            status: document.getElementById('status').value,
            inquiry_details: document.getElementById('content').value,
            response_date: document.getElementById('responseDate').value,
            response_details: document.getElementById('responseContent').value,
            responder: document.getElementById('responder').value,
            user_type: document.getElementById('userType').value,
            organization: document.getElementById('organization').value,
            inquirer: document.getElementById('inquirerName').value
        };

        fetch(`/inquiry-for-use/update/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content')
                },
                body: JSON.stringify(formData)
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

    deleteButton.addEventListener('click', function() {
        const id = document.getElementById('inquiryId').value;
        if (!id) return;

        // Show the confirmation modal
        $('#deletedConfirmationPopUpModal').modal('show');

        // Add a one-time event listener for the confirm button
        document.getElementById('confirmed').addEventListener('click', function confirmHandler() {
            fetch(`/inquiry-for-use/delete/${id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            .getAttribute(
                                'content')
                    },
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

    listButton.addEventListener('click', function() {
        inquiryForm.style.display = 'none';
    });
});

// Select/Deselect all checkboxes
document.getElementById('select-all').addEventListener('click', function() {
    document.querySelectorAll('.inquiry-for-use-checkbox').forEach(checkbox => checkbox.checked = true);
});

document.getElementById('deselect-all').addEventListener('click', function() {
    document.querySelectorAll('.inquiry-for-use-checkbox').forEach(checkbox => checkbox.checked = false);
});

// Delete selected inquiries
document.getElementById('delete-selected').addEventListener('click', function() {
    const selectedIds = [];
    document.querySelectorAll('.inquiry-for-use-checkbox:checked').forEach(checkbox => {
        selectedIds.push(checkbox.value);
    });

    if (selectedIds.length > 0) {
        // Show the confirmation modal
        $('#deletedConfirmationPopUpModal').modal('show');

        // Add a one-time event listener for the confirm button
        document.getElementById('confirmed').addEventListener('click', function confirmHandler() {
            fetch('/inquiry-for-use/bulk-delete', {
                    method: 'POST',
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
                        $('#successPopupModal').modal('show');
                    } else {
                        $('#errorFailedPopupModal').modal('show');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    $('#errorFailedPopupModal').modal('show');
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