@php
$pageTitle = config('app.lang') != 'en' ? __('서비스/직위설정') : __('Service/Job title setting');
@endphp

@extends('layouts.app', ['page' => $pageTitle, 'pageSlug' => 'setting_job_title'])

@section('content-fluid')
<div class="row">
    <div class="col-md-12">
        <div class="card mb-3" style="background-color: #2c3e50; color: #FFFFFF;">
            <div class="card-body">
                <!-- card header -->
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between">
                            <h5 class="title" style="color: #FFFFFF;">
                                {{ config('app.lang') != 'en' ? __('기본정보') : __('Basic Information') }}
                            </h5>
                            <div>
                                <button id="saveButton" class="btn btn-primary btn-sm" type="button"
                                    style="color: #fff;" onclick="save()">
                                    {{ config('app.lang') != 'en' ? __('저장') : __('Save') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!--  -->

                <!-- card body -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="card flex-fill d-flex flex-column"
                            style="background-color: #324D65; color: #FFFFFF;">
                            <div class="card-header w-100 text-left" style="background-color: #869CAF; color: #FFFFFF;">
                                <h5 class="title">
                                    {{ config('app.lang') != 'en' ? __('서비스 별 직위/직책현황') : __('Service/Job title status') }}
                                </h5>
                            </div>

                            <div class="card-body-custom-1 flex-fill d-flex flex-column justify-content-left align-items-left w-100"
                                id="job-title-status-container">
                                @foreach($status_by_service_list as $index => $statusByService)
                                <div class="row mb-3 w-100 justify-content-center"
                                    name="column-of-job-title-status-by-service">
                                    <label for="status_by_service" class="col-sm-4 col-form-label"
                                        style="color: #FFFFFF;">
                                        {{$statusByService['service_classification_desc']}}
                                    </label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control"
                                            style="background-color: #1E3142; color: #FFFFFF;"
                                            id="{{'status_by_service_' . $index}}" placeholder="XX"
                                            value="{{$statusByService['status_by_service']}}" readonly>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card flex-fill d-flex flex-column"
                            style="background-color: #324D65; color: #FFFFFF;">
                            <div class="card-header w-100 text-left" style="background-color: #869CAF; color: #FFFFFF;">
                                <h5 class="title">
                                    {{ config('app.lang') != 'en' ? __('서비스 항목설정') : __('Service Item Settings') }}
                                </h5>
                            </div>
                            <div
                                class="card-body-custom-1 flex-fill d-flex flex-column justify-content-center align-items-center w-100">
                                <div class="card flex-fill d-flex flex-column"
                                    style="background-color: #324D65; color: #FFFFFF;">
                                    <div class="card-header w-100 text-center"
                                        style="background-color: #869CAF; color: #FFFFFF;">
                                        <h5 class="title">
                                            {{ config('app.lang') != 'en' ? __('서비스 구분') : __('Service Classification') }}
                                        </h5>
                                    </div>
                                    <div class="card-body-custom-1 flex-fill d-flex flex-column justify-content-left align-items-left w-100"
                                        style="background-color:#1E3142">
                                        <div class="row text-center" id="service_classification_desc">
                                            @if (count($service_classification_desc_list) > 0)
                                            @foreach($service_classification_desc_list as $service_classification_desc)
                                            <div class="col-md-2">
                                                <div class="dashed-box">{{$service_classification_desc}}</div>
                                            </div>
                                            @endforeach
                                            @endif
                                        </div>
                                        <div class="d-flex justify-content-between mt-3">
                                            <input type="text" id="service_classification_desc_text"
                                                class="form-control w-75"
                                                style="background-color: #FFFFFF; color: #34495e; margin-right: 10px; margin-top: 6px;"
                                                placeholder="{{ config('app.lang') != 'en' ? __('구분값을 입력해 주세요.') : __('Please enter a classification value.') }}">
                                            <button class="btn btn-primary ms-2" style="margin-right: 10px;"
                                                onclick="addSeparationValue()">
                                                {{ config('app.lang') != 'en' ? __('구분 값 추가') : __('Add Classification Value') }}
                                            </button>
                                            <button class="btn btn-info ms-2" onclick="deleteSeparationValue()">
                                                {{ config('app.lang') != 'en' ? __('구분 값 삭제') : __('Delete Classification Value') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @foreach($job_title_list as $index => $job_title)
                <div class="row card-job-title-setting" name="card-job-title-setting">
                    <div class="col-md-8 ml-auto">
                        <div class="card flex-fill d-flex flex-column"
                            style="background-color: #324D65; color: #FFFFFF;">
                            <div class="card-header w-100 text-left" style="background-color: #869CAF; color: #FFFFFF;">
                                <h5 class="title">
                                    {{ config('app.lang') != 'en' ? __('직위 / 직책 설정') : __('Job Title / Position Settings') }}
                                </h5>
                            </div>
                            <div
                                class="card-body-custom-1 flex-fill d-flex flex-column justify-content-center align-items-center w-100">
                                <div class="row mb-3 w-100 justify-content-center">
                                    <label for="serviceType1" class="col-sm-4 col-form-label" style="color: #FFFFFF;">
                                        {{ config('app.lang') != 'en' ? __('서비스 구분') : __('Service Classification') }}
                                    </label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="{{'serviceType' . $index}}"
                                            value="{{$job_title['service_classification_desc']}}" disabled>
                                    </div>
                                </div>
                                <div class="row mb-3 w-100 justify-content-center">
                                    <label for="job_title" class="col-sm-4 col-form-label" style="color: #FFFFFF;">
                                        {{ config('app.lang') != 'en' ? __('직위') : __('Job Title') }}
                                    </label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="{{'job_title' . $index}}"
                                            value="{{ $job_title['job_title'] }}"
                                            placeholder="{{ config('app.lang') != 'en' ? __('데이터를 구분하려면 ","를 사용하십시오.') : __('Use "," to separate data.') }}">
                                    </div>
                                </div>
                                <div class="row mb-3 w-100 justify-content-center">
                                    <label for="position" class="col-sm-4 col-form-label" style="color: #FFFFFF;">
                                        {{ config('app.lang') != 'en' ? __('직책') : __('Position') }}
                                    </label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="{{'position' . $index}}"
                                            value="{{ $job_title['position'] }}"
                                            placeholder="{{ config('app.lang') != 'en' ? __('데이터를 구분하려면 ","를 사용하십시오.') : __('Use "," to separate data.') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        <!--  -->
    </div>
</div>

<script>
let index = `{{count($status_by_service_list)}}`;

function addSeparationValue() {
    const value = document.getElementById('service_classification_desc_text').value;
    if (value) {
        // Create new dashed box
        const newBox = document.createElement('div');
        newBox.className = 'col-md-2';
        newBox.innerHTML = `<div class="dashed-box">${value}</div>`;

        // Append the new box to the container
        const container = document.getElementById('service_classification_desc');
        container.appendChild(newBox);

        // Add corresponding status input
        const newStatusColumn = document.createElement('div');
        newStatusColumn.className = 'row mb-3 w-100 justify-content-center';
        newStatusColumn.setAttribute('name', 'column-of-job-title-status-by-service');
        newStatusColumn.innerHTML = `
            <label for="status_by_service_${index}" class="col-sm-4 col-form-label" style="color: #FFFFFF;">${value}</label>
            <div class="col-sm-8">
                <input type="text" class="form-control" style="background-color: #1E3142; color: #FFFFFF;" id="status_by_service_${index}" placeholder="XX" value="0/0" readonly>
            </div>
        `;
        document.getElementById('job-title-status-container').appendChild(newStatusColumn);

        // Add corresponding job title card
        const newJobTitleCard = document.createElement('div');
        newJobTitleCard.className = 'row card-job-title-setting';
        newJobTitleCard.setAttribute('name', 'card-job-title-setting');
        newJobTitleCard.innerHTML = `
            <div class="col-md-8 ml-auto">
                <div class="card flex-fill d-flex flex-column" style="background-color: #324D65; color: #FFFFFF;">
                    <div class="card-header w-100 text-left" style="background-color: #869CAF; color: #FFFFFF;">
                        <h5 class="title">
                            {{ config('app.lang') != 'en' ? __('직위 / 직책 설정') : __('Job Title / Position Settings') }}
                        </h5>
                    </div>
                    <div class="card-body-custom-1 flex-fill d-flex flex-column justify-content-center align-items-center w-100">
                        <div class="row mb-3 w-100 justify-content-center">
                            <label for="serviceType${index}" class="col-sm-4 col-form-label" style="color: #FFFFFF;">
                                {{ config('app.lang') != 'en' ? __('서비스 구분') : __('Service Classification') }}
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="serviceType${index}" value="${value}" disabled>
                            </div>
                        </div>
                        <div class="row mb-3 w-100 justify-content-center">
                            <label for="job_title${index}" class="col-sm-4 col-form-label" style="color: #FFFFFF;">
                                {{ config('app.lang') != 'en' ? __('직위') : __('Job Title') }}
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="job_title${index}" value="" placeholder="{{ config('app.lang') != 'en' ? __('데이터를 구분하려면 ","를 사용하십시오.') : __('Use "," to separate data.') }}">                               
                            </div>
                        </div>
                        <div class="row mb-3 w-100 justify-content-center">
                            <label for="position${index}" class="col-sm-4 col-form-label" style="color: #FFFFFF;">
                                {{ config('app.lang') != 'en' ? __('직책') : __('Position') }}
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="position${index}" value="" placeholder="{{ config('app.lang') != 'en' ? __('데이터를 구분하려면 ","를 사용하십시오.') : __('Use "," to separate data.') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        const lastRow = document.querySelector('.card-body > .row:last-child');
        lastRow.insertAdjacentElement('afterend', newJobTitleCard);

        index++;

        // Scroll to the bottom of the page
        window.scrollTo({
            top: document.body.scrollHeight,
            behavior: 'smooth'
        });
    }
    document.getElementById('service_classification_desc_text').value = '';
}


const statusByServiceList = @json($status_by_service_list);
const jobTitleList = @json($job_title_list);
const deletedIds = [];

function deleteSeparationValue() {
    const dashedBoxes = document.querySelectorAll('.dashed-box');
    for (let i = dashedBoxes.length - 1; i >= 0; i--) {
        const box = dashedBoxes[i];
        const textContent = box.textContent.trim();

        if (textContent !== '+') {
            // Check
            // if the value exists in the backend data
            const isBackendData = statusByServiceList.some(item => item.service_classification_desc === textContent) ||
                jobTitleList.some(item => item.service_classification_desc === textContent);
            if (isBackendData) {
                document.getElementById('error-message-pop-up').innerText =
                    `{{ config('app.lang') != 'en' ? __('저장된 데이터는 삭제할 수 없습니다') : __('Data that has been saved cannot be deleted') }}`
                $('#errorFailedWithMessagePopupModal').modal('show');

            } else {
                box.parentElement.remove(); // Remove the dashed box

                // Remove the corresponding status column
                const statusColumns = document.getElementsByName('column-of-job-title-status-by-service');
                if (statusColumns.length > 0) {
                    statusColumns[statusColumns.length - 1].remove();
                    deletedIds.push(textContent);
                    index--;
                }

                // Remove the corresponding job title card
                const jobTitleCards = document.getElementsByName('card-job-title-setting');
                if (jobTitleCards.length > 0) {
                    jobTitleCards[jobTitleCards.length - 1].remove();
                }
            }
            break;
        }
    }
}

function save() {
    const dashedBoxes = document.querySelectorAll('.dashed-box');
    const newDatas = [];
    for (let i = dashedBoxes.length - 1; i >= 0; i--) {
        const box = dashedBoxes[i];
        const textContent = box.textContent.trim();

        const isBackendData = statusByServiceList.some(item => item.service_classification_desc === textContent) ||
            jobTitleList.some(item => item.service_classification_desc === textContent);
        if (!isBackendData) {
            newDatas.push(textContent);
        }
    }

    // Show the confirmation modal
    if (newDatas.length > 0) {
        const newDatasString = newDatas.join(', ')
        const firstMessage =
            `{{ config('app.lang') != 'en' ? __('새 데이터 ') : __('Once the new data ') }}"${newDatasString}"`
        const scMessage =
            `{{ config('app.lang') != 'en' ? __('가 저장되면 나중에 삭제할 수 없습니다. 데이터를 저장하시겠습니까?') : __(' is saved, it cannot be deleted later. Would you like to save your data?') }}`
        document.getElementById('confirmation-save-message-pop-up').innerText = `${firstMessage}${scMessage}`
    } else {
        document.getElementById('confirmation-save-message-pop-up').innerText =
            `{{ config('app.lang') != 'en' ? __('데이터를 저장하시겠습니까?') : __('Would you like to save your data?') }}`

    }
    $('#savedConfirmationWithMessagePopUpModal').modal('show');

    const formData = new FormData();

    // Collect status_by_service data
    document.querySelectorAll('[id^=status_by_service_]').forEach((input, index) => {
        formData.append(`status_by_service_${index}`, input.value);
    });

    // Get values as array from service_classification_desc
    const serviceClassificationArray = [];
    document.querySelectorAll('#service_classification_desc .dashed-box').forEach((box) => {
        serviceClassificationArray.push(box.textContent.trim());
    });
    formData.append('service_classification_desc', JSON.stringify(serviceClassificationArray));
    formData.append('service_classification_deleted', JSON.stringify(deletedIds));

    // Collect job titles and positions
    document.querySelectorAll('.card-job-title-setting').forEach((card, index) => {
        formData.append(`job_title_${index}`, card.querySelector(`#job_title${index}`).value);
        formData.append(`position_${index}`, card.querySelector(`#position${index}`).value);
    });

    // Add a one-time event listener for the confirm button
    document.getElementById('confirmed-saved-with-message').addEventListener('click', function confirmHandler() {
        fetch('/setting-job-title', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content')
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    $('#successPopupModal').modal('show');
                } else {
                    $('#errorFailedPopupModal').modal('show');
                }
            })
            .catch(error => console.error('Error:', error))
            .finally(() => {
                // Remove the event listener after the click is handled
                document.getElementById('confirmed-saved-with-message').removeEventListener('click',
                    confirmHandler);
                // Hide the confirmation modal
                $('#savedConfirmationWithMessagePopUpModal').modal('hide');
            });
    }, {
        once: true
    });
}
</script>
@endsection