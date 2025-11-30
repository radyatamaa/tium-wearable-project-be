@php
$pageTitle = config('app.lang') != 'en' ? __('세부분야 설정') : __('Detailed Field Setting');
@endphp

@extends('layouts.app', ['page' => $pageTitle, 'pageSlug' => 'detailed_field_setting'])

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
                                    {{ config('app.lang') != 'en' ? __('서비스 별 세부분야') : __('Detailed Field by Service') }}
                                </h5>
                            </div>
                            <div
                                class="card-body-custom-1 flex-fill d-flex flex-column justify-content-left align-items-left w-100">
                                @foreach($detailed_field_by_services as $index => $detailed_field_by_service)
                                <div class="row mb-3 w-100 justify-content-center">
                                    <label for="inputMedical" class="col-sm-4 col-form-label" style="color: #FFFFFF;">
                                        {{$detailed_field_by_service['service_classification_desc']}}
                                    </label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control"
                                            id="detailed_field_by_service__{{$index}}" placeholder="XX 개"
                                            style="background-color: #1E3142; color: #FFFFFF;"
                                            value="{{ $detailed_field_by_service['detailed_field_by_service'] }}"
                                            readonly>
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
                                    {{ config('app.lang') != 'en' ? __('서비스 별 세부항목 설정') : __('Detailed Item Settings by Service') }}
                                </h5>
                            </div>
                            <div
                                class="card-body-custom-1 flex-fill d-flex flex-column justify-content-left align-items-left w-100">
                                @foreach($setting_detailed_items as $index => $setting_detailed_item)
                                <div class="row mb-1 w-100 justify-content-center">
                                    <label for="serviceType1" class="col-sm-4 col-form-label" style="color: #FFFFFF;">
                                        {{ config('app.lang') != 'en' ? __('서비스 구분') : __('Service Classification') }}
                                    </label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control"
                                            id="service_classification_desc_{{$index}}"
                                            value="{{$setting_detailed_item['service_classification_desc']}}" disabled>
                                    </div>
                                </div>
                                <div class="row mb-4 w-100 justify-content-center">
                                    <label for="medical_subject_{{$index}}" class="col-sm-4 col-form-label"
                                        style="color: #FFFFFF;">
                                        {{ config('app.lang') != 'en' ? __('진료과목') : __('Medical Subject') }}
                                    </label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="medical_subject_{{$index}}"
                                            value="{{$setting_detailed_item['medical_subject']}}"
                                            placeholder="{{ config('app.lang') != 'en' ? __('데이터를 구분하려면 ","를 사용하십시오.') : __('Use "," to separate data.') }}">
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <!--  -->
            </div>
        </div>
        <!--  -->
    </div>
</div>
<script>
function save() {
    const formData = new FormData();

    const countOfForm = document.querySelectorAll('[id^=service_classification_desc]').length;

    document.querySelectorAll('[id^=medical_subject]').forEach((input, index) => {
        formData.append(`medical_subject_${index}`, input.value);
    });

    document.querySelectorAll('[id^=service_classification_desc]').forEach((input, index) => {
        formData.append(`service_classification_desc_${index}`, input.value);
    });

    document.querySelectorAll('[id^=detailed_field_by_service]').forEach((input, index) => {
        formData.append(`detailed_field_by_service_${index}`, input.value);
    });
    console.log(formData);
    formData.append(`count_of_form`, countOfForm);

    fetch('/setting-detailed-field', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
        .catch(error => console.error('Error:', error));
}
</script>
@endsection