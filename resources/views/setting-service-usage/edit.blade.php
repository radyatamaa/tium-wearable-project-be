@php
    $pageTitle = config('app.lang') != 'en' ? __('서비스 이용설정') : __('Service usage settings');
@endphp

@extends('layouts.app', ['page' => $pageTitle, 'pageSlug' => 'service_usage_settings'])

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
                                <button id="save" class="btn btn-primary btn-sm" type="button" style="color: #fff;"
                                    onclick="save()">
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
                                    {{ config('app.lang') != 'en' ? __('서비스 이용요금') : __('Service Usage Fee') }}
                                </h5>
                            </div>
                            <div class="card-body-custom-1 flex-fill d-flex flex-column justify-content-left align-items-left w-100"
                                id="service-usage-fee-container">
                                @foreach($service_usage_fee_list as $index => $service_usage_fee)
                                    <div class="row mb-3 w-100 justify-content-center"
                                        name="column-of-by-service-usage-fee">
                                        <label for="service_classification_desc" class="col-sm-4 col-form-label"
                                            style="color: #FFFFFF;">
                                            {{$service_usage_fee['service_classification_desc']}}
                                        </label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-custom numeric-input"
                                                id="service_usage_fee_{{ $index }}" placeholder="XX,XXX 원"
                                                style="background-color: #1E3142; color: #FFFFFF;"
                                                value="{{$service_usage_fee['service_usage_fee']}} 원" readonly>
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
                                    {{ config('app.lang') != 'en' ? __('서비스 구분설정') : __('Service Classification Settings') }}
                                </h5>
                            </div>
                            <div
                                class="card-body-custom-1 flex-fill d-flex flex-column justify-content-left align-items-left w-100">
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

                <!-- Service range and fee settings -->
                @foreach($scope_and_usage_fee_list as $index => $scope_and_usage_fee)
                    <div class="row card-cope-usage-fee" name="card-cope-usage-fee">
                        <div class="col-md-8 ml-auto">
                            <div class="card">
                                <div class="card-header w-100 text-left" style="background-color: #869CAF; color: #FFFFFF;">
                                    <h5 class="title">
                                        {{ config('app.lang') != 'en' ? __('서비스 범위 및 이용요금 설정') : __('Service Range and Usage Fee Settings') }}
                                    </h5>
                                </div>
                                <div
                                    class="card-body-custom-1 flex-fill d-flex flex-column justify-content-left align-items-left w-100">
                                    <div class="mb-3 row">
                                        <label for="service_classification_desc_{{$index}}" class="col-sm-2 col-form-label">
                                            {{ config('app.lang') != 'en' ? __('서비스구분') : __('Service Classification') }}
                                        </label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" id="service_classification_desc"
                                                value="{{$scope_and_usage_fee['service_classification_desc']}}" disabled>
                                        </div>
                                        <label for="usage_fee_monthly_{{$index}}" class="col-sm-2 col-form-label">
                                            {{ config('app.lang') != 'en' ? __('이용요금 (원/월간)') : __('Usage Fee (₩/Monthly)') }}
                                        </label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control form-control-custom numeric-input"
                                                id="usage_fee_monthly_{{$index}}"
                                                value="{{$scope_and_usage_fee['usage_fee_monthly']}}">
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label for="number_of_user_from_{{$index}}" class="col-sm-2 col-form-label">
                                            {{ config('app.lang') != 'en' ? __('사용인원(명)') : __('Number of Users') }}
                                        </label>
                                        <div class="col-sm-2">
                                            <input type="text" class="form-control form-control-custom numeric-input"
                                                id="number_of_user_from_{{$index}}"
                                                value="{{$scope_and_usage_fee['number_of_user_from']}}">
                                        </div>
                                        <label for="number_of_user_to" class="col-form-label"
                                            style="margin-left: -7px;">~</label>
                                        <div class="col-sm-2">
                                            <input type="text" class="form-control form-control-custom numeric-input"
                                                id="number_of_user_to_{{$index}}"
                                                value="{{$scope_and_usage_fee['number_of_user_to']}}">
                                        </div>
                                        <label for="fees_used_year_{{$index}}" class="col-sm-2 col-form-label">
                                            {{ config('app.lang') != 'en' ? __('이용요금 (원/년간)') : __('Usage Fee (₩/Yearly)') }}
                                        </label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control form-control-custom numeric-input"
                                                id="fees_used_year_{{$index}}"
                                                value="{{$scope_and_usage_fee['fees_used_year']}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach


                <!--  -->
            </div>
        </div>
        <!--  -->
    </div>
</div>

<script>
    let index = '{{count($service_usage_fee_list)}}';

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
            newStatusColumn.setAttribute('name', 'column-of-by-service-usage-fee');
            newStatusColumn.innerHTML = `
            <label for="service_usage_fee_${index}" class="col-sm-4 col-form-label" style="color: #FFFFFF;">${value}</label>
            <div class="col-sm-8">
                <input type="text" class="form-control form-control-custom numeric-input" id="service_usage_fee_${index}" placeholder="XX,XXX 원" style="background-color: #1E3142; color: #FFFFFF;" value="0 원" readonly>
            </div>
        `;
            document.getElementById('service-usage-fee-container').appendChild(newStatusColumn);

            // Add corresponding job title card
            const newJobTitleCard = document.createElement('div');
            newJobTitleCard.className = 'row card-cope-usage-fee';
            newJobTitleCard.setAttribute('name', 'card-cope-usage-fee');
            newJobTitleCard.innerHTML = `
            <div class="col-md-8 ml-auto">
                <div class="card">
                    <div class="card-header w-100 text-left" style="background-color: #869CAF; color: #FFFFFF;">
                        <h5 class="title">{{ config('app.lang') != 'en' ? __('서비스 범위 및 이용요금 설정') : __('Service Range and Usage Fee Settings') }}</h5>
                    </div>
                    <div class="card-body-custom-1 flex-fill d-flex flex-column justify-content-left align-items-left w-100">
                        <div class="mb-3 row">
                            <label for="service_classification_desc_${index}" class="col-sm-2 col-form-label">{{ config('app.lang') != 'en' ? __('서비스구분') : __('Service Classification') }}</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="service_classification_desc_${index}" value="${value}" disabled>
                            </div>
                            <label for="usage_fee_monthly_${index}" class="col-sm-2 col-form-label">{{ config('app.lang') != 'en' ? __('이용요금 (원/월간)') : __('Usage Fee (₩/Monthly)') }}</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control form-control-custom numeric-input" id="usage_fee_monthly_${index}" value="">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="number_of_user_from_${index}" class="col-sm-2 col-form-label">{{ config('app.lang') != 'en' ? __('사용인원(명)') : __('Number of Users') }}</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control form-control-custom numeric-input" id="number_of_user_from_${index}" value="">
                            </div>
                            <label for="number_of_user_to_${index}" class="col-form-label" style="margin-left: -7px;">~</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control form-control-custom numeric-input" id="number_of_user_to_${index}" value="">
                            </div>
                            <label for="fees_used_year_${index}" class="col-sm-2 col-form-label">{{ config('app.lang') != 'en' ? __('이용요금 (원/년간)') : __('Usage Fee (₩/Yearly)') }}</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control form-control-custom numeric-input" id="fees_used_year_${index}" value="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
            const lastRow = document.querySelector('.card-body > .row:last-child');
            lastRow.insertAdjacentElement('afterend', newJobTitleCard);

            // Attach event listeners to the newly added inputs
            const numericInputs = newJobTitleCard.querySelectorAll('.numeric-input');
            numericInputs.forEach(function (input) {
                input.addEventListener('input', function () {
                    validateNumber(input);
                });
            });

            index++;

            // Scroll to the bottom of the page
            window.scrollTo({
                top: document.body.scrollHeight,
                behavior: 'smooth'
            });
        }
        document.getElementById('service_classification_desc_text').value = '';
    }
    const serviceUsageFeeList = @json($service_usage_fee_list);
    const scopeAndUsageFeeList = @json($scope_and_usage_fee_list);
    const deletedIds = [];

    function deleteSeparationValue() {
        const dashedBoxes = document.querySelectorAll('.dashed-box');
        for (let i = dashedBoxes.length - 1; i >= 0; i--) {
            const box = dashedBoxes[i];
            const textContent = box.textContent.trim();

            if (textContent !== '+') {
                // Check if the value exists in the backend data
                const isBackendData = serviceUsageFeeList.some(item => item.service_classification_desc === textContent) ||
                    scopeAndUsageFeeList.some(item => item.service_classification_desc === textContent);
                if (isBackendData) {
                    document.getElementById('error-message-pop-up').innerText =
                        `{{ config('app.lang') != 'en' ? __('저장된 데이터는 삭제할 수 없습니다') : __('Data that has been saved cannot be deleted') }}`
                    $('#errorFailedWithMessagePopupModal').modal('show');
                } else {
                    box.parentElement.remove(); // Remove the dashed box

                    // Remove the corresponding status column
                    const statusColumns = document.getElementsByName('column-of-by-service-usage-fee');
                    if (statusColumns.length > 0) {
                        statusColumns[statusColumns.length - 1].remove();
                        deletedIds.push(textContent);
                        index--;
                    }

                    // Remove the corresponding job title card
                    const jobTitleCards = document.getElementsByName('card-cope-usage-fee');
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
            const isBackendData = serviceUsageFeeList.some(item => item.service_classification_desc === textContent) ||
                scopeAndUsageFeeList.some(item => item.service_classification_desc === textContent);
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
        document.querySelectorAll('[id^=service_usage_fee_]').forEach((input, index) => {
            formData.append(`service_usage_fee_${index}`, input.value);
        });

        // Get values as array from service_classification_desc
        const serviceClassificationArray = [];
        document.querySelectorAll('#service_classification_desc .dashed-box').forEach((box) => {
            serviceClassificationArray.push(box.textContent.trim());
        });
        formData.append('service_classification_desc', JSON.stringify(serviceClassificationArray));
        formData.append('service_classification_deleted', JSON.stringify(deletedIds));

        // Collect job titles and positions
        document.querySelectorAll('.card-cope-usage-fee').forEach((card, index) => {
            formData.append(`number_of_user_from_${index}`, card.querySelector(`#number_of_user_from_${index}`)
                .value);
            formData.append(`number_of_user_to_${index}`, card.querySelector(`#number_of_user_to_${index}`).value);
            formData.append(`usage_fee_monthly_${index}`, card.querySelector(`#usage_fee_monthly_${index}`).value);
            formData.append(`fees_used_year_${index}`, card.querySelector(`#fees_used_year_${index}`).value);
        });

        // Add a one-time event listener for the confirm button
        document.getElementById('confirmed-saved-with-message').addEventListener('click', function confirmHandler() {
            fetch('/setting-service-usage', {
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