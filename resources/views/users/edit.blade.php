@php
$pageTitle = config('app.lang') != 'en' ? __('사용자관리') : __('User Management');
@endphp

@extends('layouts.app', ['page' => $pageTitle, 'pageSlug' => 'user_management'])

@section('content-fluid')
<div class="row">
    <!-- card of information -->
    <div class="col-md-12">
        <div class="card mb-3" style="background-color: #2c3e50; color: #FFFFFF;">
            <div class="card-body">
                <!-- card header -->
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between">
                            <h5 class="title" style="color: #FFFFFF;">
                                {{ config('app.lang') != 'en' ? __('가입정보') : __('Subscription Information') }}
                            </h5>
                            <div>
                                <button id="list" class="btn btn-info btn-sm" type="button" style="color: #fff;"
                                    onclick="redirectToUserList('{{ route('user.index') }}')">
                                    {{ config('app.lang') != 'en' ? __('목록') : __('List') }}
                                </button>
                                <button id="save" class="btn btn-primary btn-sm" type="button" style="color: #fff;"
                                    onclick="submitForm();">
                                    {{ config('app.lang') != 'en' ? __('저장') : __('Save') }}
                                </button>
                                <button id="delete" class="btn btn-danger btn-sm" type="button" style="color: #fff;"
                                    onclick="deleteUser('{{ $user->id }}')">
                                    {{ config('app.lang') != 'en' ? __('삭제') : __('Delete') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!--  -->

                <div class="row d-flex align-items-stretch">
                    <!-- card of profile -->
                    <div class="col-md-4 d-flex">
                        <div class="card card-user flex-fill d-flex flex-column"
                            style="background-color: #324D65; color: #FFFFFF;">
                            <div
                                class="card-body-custom-1 flex-fill d-flex flex-column justify-content-center align-items-center text-center">
                                <div class="profile-picture">

                                    <img src="{{ $user->photo_profile != null ? asset('storage/' . $user->photo_profile) : asset('black/img/default-avatar.png') }}"
                                        alt="Profile" class="profile-img" id="profileImage">
                                    <label for="fileUpload" class="edit-icon">
                                        <input type="file" id="fileUpload" accept="image/jpeg, image/png"
                                            onchange="loadFile(event)">
                                        <img src="{{ asset('black') }}/icons/general/pencil.svg">
                                    </label>
                                </div>
                                <div class="col-md-1"></div>
                                <div>
                                    <h2 class="title">{{ $user->nickname }}</h2>
                                </div>
                                <div class="location-component">
                                    <span class="icon"><img
                                            src="{{ asset('black') }}/icons/general/location.svg"></span>
                                    @if(config('app.lang') != 'en')
                                    <span class="text">
                                        {{ $user->subscriptionInformation ? $user->subscriptionInformation->address : '' }},
                                        {{ $user->subscriptionInformation ? $user->subscriptionInformation->full_address : '' }}

                                    </span>
                                    @else
                                    <span
                                        class="text">{{ $user->subscriptionInformation ? $user->subscriptionInformation->full_address : '' }},
                                        {{ $user->subscriptionInformation ? $user->subscriptionInformation->address : '' }}
                                    </span>
                                    @endif

                                </div>
                            </div>
                        </div>
                    </div>
                    <!--  -->

                    <!-- card of receive message & document -->
                    <div class="col-md-4 d-flex flex-column">
                        <div class="card flex-fill d-flex flex-column"
                            style="background-color: #324D65; color: #FFFFFF;">
                            <div class="card-header text-left" style="background-color: #869CAF; color: #FFFFFF;">
                                <h5 class="title">
                                    {{ config('app.lang') != 'en' ? __('메시지 수신') : __('Receive Messages') }}
                                    <span style="color: #FC565D;">*</span>
                                </h5>
                            </div>
                            <div
                                class="card-body-custom-1 flex-fill d-flex flex-column justify-content-center align-items-center">
                                <div class="row mb-3 w-100 justify-content-center">
                                    <label for="inputEmail" class="col-sm-4 col-form-label"
                                        style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('이메일') : __('Email') }}
                                        <span style="color: #FC565D;">*</span></label>
                                    <div class="col-sm-8 d-flex align-items-center">
                                        <div class="component">
                                            <span
                                                class="label-input">{{ config('app.lang') != 'en' ? __('수신') : __('Receive') }}</span>
                                            <label class="switch">
                                                <input class="required-input" type="checkbox" id="inputEmail"
                                                    name="inputEmail"
                                                    {{ $user && $user->receive_email ? 'checked' : '' }}
                                                    onchange="handleEmailChange(event)">
                                                <span class="slider"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3 w-100 justify-content-center">
                                    <label for="inputSms" class="col-sm-4 col-form-label"
                                        style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('SMS') : __('SMS') }}</label>
                                    <div class="col-sm-8 d-flex align-items-center">
                                        <div class="component">
                                            <span
                                                class="label-input">{{ config('app.lang') != 'en' ? __('수신') : __('Receive') }}</span>
                                            <label class="switch">
                                                <input type="checkbox" id="inputSms" name="inputSms"
                                                    {{ $user && $user->receive_sms ? 'checked' : '' }}
                                                    onchange="handleSMSChange(event)">
                                                <span class="slider"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card flex-fill d-flex flex-column mt-3"
                            style="background-color: #324D65; color: #FFFFFF;">
                            <div class="card-header text-left" style="background-color: #869CAF; color: #FFFFFF;">
                                <h5 class="title">
                                    {{ config('app.lang') != 'en' ? __('데이터 문서출력') : __('Data Document Output') }}
                                    <span style="color: #FC565D;">*</span>
                                </h5>
                            </div>
                            <div
                                class="card-body-custom-1 flex-fill d-flex flex-column justify-content-center align-items-center">
                                <div class="row mb-3 w-100 justify-content-center">
                                    <label for="inputDataOutput" class="col-sm-4 col-form-label"
                                        style="color: #FFFFFF;">PDF
                                        <span style="color: #FC565D;">*</span></label>
                                    <div class="col-sm-8 d-flex align-items-center">
                                        <div class="component">
                                            <span
                                                class="label-input">{{ config('app.lang') != 'en' ? __('사용') : __('Use') }}</span>
                                            <label class="switch">
                                                <input class="required-input" type="checkbox" id="inputDataOutput"
                                                    name="inputDataOutput"
                                                    {{ $user && $user->data_output ? 'checked' : '' }}
                                                    onchange="handleDataOutputPdf(event)">
                                                <span class="slider"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--  -->

                    <!-- card of member status -->
                    <div class="col-md-4 d-flex">
                        <div class="card flex-fill d-flex flex-column"
                            style="background-color: #324D65; color: #FFFFFF;">
                            <div class="card-header w-100 text-left" style="background-color: #869CAF; color: #FFFFFF;">
                                <h5 class="title">{{ config('app.lang') != 'en' ? __('회원현황') : __('Member Status') }}
                                    <span style="color: #FC565D;">*</span>
                                </h5>
                            </div>
                            <div
                                class="card-body-custom-1 flex-fill d-flex flex-column justify-content-center align-items-center w-100">
                                <div class="row mb-3 w-100 justify-content-center">
                                    <label for="inputFieldOfHelp" class="col-sm-4 col-form-label"
                                        style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('도움분야') : __('Field of Help') }}</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control numeric-input" id="inputFieldOfHelp"
                                            name="field_of_help" style="background-color: #1E3142; color: #FFFFFF;"
                                            placeholder="{{ config('app.lang') != 'en' ? 'XX 개' : 'XX items' }}"
                                            value="{{ $user->MemberStatus ? $user->MemberStatus->field_of_help : '' }}"
                                            readonly>

                                    </div>
                                </div>
                                <div class="row mb-3 w-100 justify-content-center">
                                    <label for="inputWorker" class="col-sm-4 col-form-label"
                                        style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('근로자') : __('Workers') }}</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control form-control-custom numeric-input"
                                            id="inputWorker" name="number_of_workers"
                                            style="background-color: #1E3142; color: #FFFFFF;"
                                            placeholder="{{ config('app.lang') != 'en' ? 'XX 명' : 'XX people' }}"
                                            value="{{ $user->MemberStatus ? $user->MemberStatus->worker : '' }}"
                                            readonly>
                                    </div>
                                </div>
                                <div class="row mb-3 w-100 justify-content-center">
                                    <label for="inputServiceOfUse" class="col-sm-4 col-form-label"
                                        style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('이용서비스') : __('Service of Use') }}</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control"
                                            style="background-color: #1E3142; color: #FFFFFF;" id="inputServiceOfUse"
                                            name="inputServiceOfUse"
                                            placeholder="{{ config('app.lang') != 'en' ? '베이직' : 'Basic' }}"
                                            value="{{ $user->MemberStatus ? $user->MemberStatus->service_of_use : '' }}"
                                            readonly>
                                    </div>
                                </div>
                                <div class="row mb-3 w-100 justify-content-center">
                                    <label for="inputLatestPaymentDate" class="col-sm-4 col-form-label"
                                        style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('최근 결제일') : __('Latest Payment Date') }}
                                        <span style="color: #FC565D;">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="date" class="form-control required-input"
                                            style="background-color: #FFFFFF; color: #1E3142;"
                                            id="inputLatestPaymentDate" name="latest_payment_date"
                                            placeholder="YYYY-MM-DD"
                                            value="{{ $user->MemberStatus ? $user->MemberStatus->last_payment_date : '' }}">
                                    </div>
                                </div>
                                <div class="row mb-3 w-100 justify-content-center">
                                    <label for="inputAnotherPaymentDate" class="col-sm-4 col-form-label"
                                        style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('다른 결제일') : __('Another Payment Date') }}
                                        <span style="color: #FC565D;">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="date" class="form-control required-input"
                                            style="background-color: #FFFFFF; color: #1E3142;"
                                            id="inputAnotherPaymentDate" name="another_payment_date"
                                            placeholder="YYYY-MM-DD"
                                            value="{{ $user->MemberStatus ? $user->MemberStatus->another_payment_date : '' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--  -->
                </div>
            </div>
        </div>
    </div>
    <!--  -->

    <!-- card of navbar -->
    <div class="col-md-12">
        <div class="card mb-3" style="background-color: #1E3142; color: #FFFFFF;">
            <div class="nav-bar-custom">
                <div class="nav-item active" data-target="subscriptionInfo">
                    <div class="title">{{ config('app.lang') != 'en' ? __('가입정보') : __('Subscription Information') }}
                        <span style="color: #FC565D;">*</span>
                    </div>
                </div>
                <div class="nav-item" data-target="personInCharge">
                    <div class="title">
                        {{ config('app.lang') != 'en' ? __('담당자 정보') : __('Person in Charge Information') }}
                        <span style="color: #FC565D;">*</span>
                    </div>
                </div>
                <div class="nav-item" data-target="additionalInfo">
                    <div class="title">{{ config('app.lang') != 'en' ? __('부가정보') : __('Additional Information') }}
                        <span style="color: #FC565D;">*</span>
                    </div>
                </div>
                <div class="nav-item" data-target="serviceSubscriptionInfo">
                    <div class="title">
                        {{ config('app.lang') != 'en' ? __('서비스 가입정보') : __('Service Subscription Information') }}
                        <span style="color: #FC565D;">*</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--  -->

    <!-- card of subscription information form bottom 1-->
    <div id="subscriptionInfo" class="col-md-12 content-section active">
        <form id="editForm" method="POST" action="{{ route('user.update', $user->id) }}">
            @csrf
            @method('PUT')
            <div class="card mb-3" style="background-color: #2c3e50; color: #FFFFFF;">
                <div class="card-body">
                    <div class="col-md-12 d-flex">
                        <div class="card flex-fill d-flex flex-column"
                            style="background-color: #324D65; color: #FFFFFF;">
                            <div class="card-header w-100 text-left" style="background-color: #869CAF; color: #FFFFFF;">
                                <h5 class="title">
                                    {{ config('app.lang') != 'en' ? __('가입정보') : __('Subscription Information') }}
                                </h5>
                            </div>
                            <div
                                class="card-body-custom-1 flex-fill d-flex flex-column justify-content-rigth align-items-right w-100">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="row mb-3 w-100 justify-content-center">
                                            <label for="membershipDate" class="col-sm-4 col-form-label"
                                                style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('회원가입일') : __('Membership Registration Date') }}</label>
                                            <div class="col-sm-8">
                                                <input type="date" class="form-control"
                                                    style="background-color: #1E3142; color: #FFFFFF;"
                                                    id="membershipDate" name="membership_registration_date"
                                                    placeholder="YYYY-MM-DD"
                                                    value="{{ $user->subscriptionInformation ? $user->subscriptionInformation->membership_registration_date : '' }}"
                                                    readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-3 w-100 justify-content-center">
                                            <label for="serviceStartDate" class="col-sm-4 col-form-label"
                                                style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('서비스시작일') : __('Service Start Date') }}</label>
                                            <div class="col-sm-8">
                                                <input type="date" class="form-control"
                                                    style="background-color: #1E3142; color: #FFFFFF;"
                                                    id="serviceStartDate" name="service_start_date"
                                                    placeholder="YYYY-MM-DD"
                                                    value="{{ $user->subscriptionInformation ? $user->subscriptionInformation->service_start_date : '' }}"
                                                    readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-3 w-100 justify-content-center">
                                            <label for="expirationDate" class="col-sm-4 col-form-label"
                                                style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('서비스만료일') : __('Service Expiration Date') }}</label>
                                            <div class="col-sm-8">
                                                <input type="date" class="form-control"
                                                    style="background-color: #1E3142; color: #FFFFFF;"
                                                    id="expirationDate" name="service_expiration_date"
                                                    placeholder="YYYY-MM-DD"
                                                    value="{{ $user->subscriptionInformation ? $user->subscriptionInformation->service_expiration_date : '' }}"
                                                    readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-3 w-100 justify-content-center">
                                            <label for="userId" class="col-sm-4 col-form-label"
                                                style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('아이디') : __('User ID') }}</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control required-input"
                                                    style="background-color: #1E3142; color: #FFFFFF;" id="userId"
                                                    name="user_id" placeholder="flywork" value="{{ $user->user_id }}"
                                                    readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-3 w-100 justify-content-center">
                                            <label for="password" class="col-sm-4 col-form-label"
                                                style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('비밀번호') : __('Password') }}
                                                <span style="color: #FC565D;">*</span></label>
                                            <div class="col-sm-8">
                                                <input type="password" class="form-control required-input"
                                                    style="background-color: #FFFFFF; color: #1E3142;" id="password"
                                                    name="password"
                                                    placeholder="{{ config('app.lang') != 'en' ? __('8-20자리의 숫자, 영문각각 1개 이상 포함') : __('8-20 characters with at least 1 number and 1 letter') }}"
                                                    value="{{ $user->password }}">
                                            </div>
                                        </div>
                                        <div class="row mb-3 w-100 justify-content-center">
                                            <label for="companyName" class="col-sm-4 col-form-label"
                                                style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('업체기관명') : __('Company Name') }}
                                                <span style="color: #FC565D;">*</span></label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control required-input"
                                                    style="background-color: #FFFFFF; color: #1E3142;" id="companyName"
                                                    name="organization_name"
                                                    placeholder="{{ config('app.lang') != 'en' ? '가나다서울병원' : 'ABC Seoul Hospital' }}"
                                                    value="{{ $user->subscriptionInformation ? $user->subscriptionInformation->organization_name : '' }}">
                                            </div>
                                        </div>
                                        <div class="row mb-3 w-100 justify-content-center">
                                            <label for="address" class="col-sm-4 col-form-label"
                                                style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('주소') : __('Address') }}
                                                <span style="color: #FC565D;">*</span></label>
                                            <div class="col-sm-8">
                                                <div class="input-group">
                                                    <input type="text" class="form-control required-input"
                                                        style="background-color: #1E3142; color: #FFFFFF;"
                                                        id="postalCodeAddress" name="postalCodeAddress"
                                                        value="{{ $user->subscriptionInformation ? $user->subscriptionInformation->postal_code : '' }}"
                                                        readonly>
                                                    <div class="input-group">
                                                        <button class="btn btn-info" type="button"
                                                            onclick="execDaumPostcode()">{{ config('app.lang') != 'en' ? __('우편번호 찾기') : __('Find Postal Code') }}</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3 w-100 justify-content-center">
                                            <div class="col-sm-8 offset-sm-4">
                                                <textarea class="styled-textarea required-input" name="address"
                                                    id="address" style="background-color: #1E3142; color: #FFFFFF;"
                                                    readonly>{{ $user->subscriptionInformation ? $user->subscriptionInformation->address : '' }}</textarea>
                                            </div>
                                        </div>
                                        <div class="row mb-3 w-100 justify-content-center">
                                            <div class="col-sm-8 offset-sm-4">
                                                <input type="text" class="form-control required-input"
                                                    style="background-color: #FFFFFF; color: #1E3142;" id="fullAddress"
                                                    name="full_address"
                                                    value="{{ $user->subscriptionInformation ? $user->subscriptionInformation->full_address : '' }}">
                                            </div>
                                        </div>
                                        <div class="row mb-3 w-100 justify-content-center">
                                            <label for="representativeName" class="col-sm-4 col-form-label"
                                                style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('대표자명') : __('Representative Name') }}
                                                <span style="color: #FC565D;">*</span></label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control required-input"
                                                    style="background-color: #FFFFFF; color: #1E3142;"
                                                    id="representativeName" name="name_of_representative"
                                                    placeholder="{{ config('app.lang') != 'en' ? '김길동' : 'Kim Gil-dong' }}"
                                                    value="{{ $user->subscriptionInformation ? $user->subscriptionInformation->name_of_representative : '' }}">
                                            </div>
                                        </div>
                                        <div class="row mb-3 w-100 justify-content-center">
                                            <label for="workers" class="col-sm-4 col-form-label"
                                                style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('근로자수(명)') : __('Number of Workers') }}
                                                <span style="color: #FC565D;">*</span></label>
                                            <div class="col-sm-8">
                                                <input type="number"
                                                    class="form-control form-control-custom numeric-input required-input"
                                                    style="background-color: #FFFFFF; color: #1E3142;" id="workers"
                                                    name="number_of_workers"
                                                    placeholder="{{ config('app.lang') != 'en' ? '150' : '150' }}"
                                                    value="{{ $user->subscriptionInformation ? $user->subscriptionInformation->number_of_workers : '' }}"
                                                    onchange="handleWorkersChange()" oninput="handleWorkersChange()">
                                            </div>
                                        </div>
                                        <div class="row mb-3 w-100 justify-content-center">
                                            <label for="homepage" class="col-sm-4 col-form-label"
                                                style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('홈페이지') : __('Homepage') }}
                                                <span style="color: #FC565D;">*</span></label>
                                            <div class="col-sm-8">
                                                <input type="url" class="form-control required-input"
                                                    style="background-color: #FFFFFF; color: #1E3142;" id="homepage"
                                                    name="homepage" placeholder="www.flywork.co.kr"
                                                    value="{{ $user->subscriptionInformation ? $user->subscriptionInformation->homepage : '' }}">
                                            </div>
                                        </div>
                                        <div class="row mb-3 w-100 justify-content-center">
                                            <label for="hrUse" class="col-sm-4 col-form-label"
                                                style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('인력관리 사용유무') : __('HR Management Usage') }}
                                                <span style="color: #FC565D;">*</span></label>
                                            <div class="col-sm-8">
                                                <select class="form-control required-input"
                                                    style="background-color: #869CAF; color: #FFFFFF;" id="hrUse"
                                                    name="hr_management_usage">
                                                    <option value="미사용"
                                                        {{ $user->subscriptionInformation && $user->subscriptionInformation->hr_management_usage == '미사용' ? 'selected' : '' }}>
                                                        {{ config('app.lang') != 'en' ? __('미사용') : __('Not Use') }}
                                                    </option>
                                                    <option value="사용"
                                                        {{ $user->subscriptionInformation && $user->subscriptionInformation->hr_management_usage == '사용' ? 'selected' : '' }}>
                                                        {{ config('app.lang') != 'en' ? __('사용') : __('Use') }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row mb-3 w-100 justify-content-center">
                                            <label for="faxNumber" class="col-sm-4 col-form-label"
                                                style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('팩스번호') : __('Fax Number') }}
                                                <span style="color: #FC565D;">*</span></label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control numeric-input required-input"
                                                    style="background-color: #FFFFFF; color: #1E3142;" id="faxNumber"
                                                    name="fax_number" placeholder="023334568"
                                                    value="{{ $user->subscriptionInformation ? $user->subscriptionInformation->fax_number : '' }}">
                                            </div>
                                        </div>
                                        <div class="row mb-3 w-100 justify-content-center">
                                            <label for="fieldOfHelp" class="col-sm-4 col-form-label"
                                                style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('도움분야') : __('Field of Help') }}
                                                <span style="color: #FC565D;">*</span></label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control numeric-input required-input"
                                                    style="background-color: #869CAF; color: #FFFFFF;" id="fieldOfHelp"
                                                    name="field_of_help"
                                                    placeholder="{{ config('app.lang') != 'en' ? '선택 (0)' : 'Select (0)' }}"
                                                    readonly
                                                    value="{{ $user->subscriptionInformation ? $user->subscriptionInformation->field_of_help : '' }}">
                                            </div>
                                        </div>
                                        <div class="row mb-3 w-100 justify-content-center">
                                            <label for="selectedSubjects" class="col-sm-4 col-form-label"
                                                style="color: #FFFFFF;"></label>
                                            <div class="col-sm-8">
                                                <textarea class="styled-textarea required-input"
                                                    name="selected_subjects" id="selectedSubjects"
                                                    style="background-color: #1E3142; color: #FFFFFF;"
                                                    readonly>{{ $user->subscriptionInformation ? $user->subscriptionInformation->selected_subjects : '' }}</textarea>
                                            </div>
                                        </div>
                                        <div class="row mb-3 w-100 justify-content-center">
                                            <label for="availableSubjects" class="col-sm-4 col-form-label"
                                                style="color: #FFFFFF;"></label>
                                            <div class="col-sm-8">
                                                <div class="d-flex flex-wrap">
                                                    @foreach ($subjects as $subject)
                                                    <button type="button" onclick="selectSubject(this)"
                                                        class="btn {{in_array($subject, $selected_subjects) ? 'btn-primary' : 'btn-info'}} m-1">{{ $subject }}</button>
                                                    @endforeach
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
        </form>
    </div>
    <!--  -->

    <!-- card of the person in charge form bottom 2-->
    <div id="personInCharge" class="col-md-12 content-section">
        <div class="card mb-3" style="background-color: #2c3e50; color: #FFFFFF;">
            <div class="card-body">
                <div class="col-md-12 d-flex">
                    <div class="card flex-fill d-flex flex-column" style="background-color: #324D65; color: #FFFFFF;">
                        <div class="card-header w-100 text-left" style="background-color: #869CAF; color: #FFFFFF;">
                            <h5 class="title">
                                {{ config('app.lang') != 'en' ? __('담당자 정보') : __('Person in Charge Information') }}
                            </h5>
                        </div>
                        <div
                            class="card-body-custom-1 flex-fill d-flex flex-column justify-content-rigth align-items-right w-100">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row mb-3 w-100 justify-content-center">
                                        <label for="name" class="col-sm-4 col-form-label"
                                            style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('담당자명') : __('Name') }}
                                            <span style="color: #FC565D;">*</span></label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control required-input" id="name" name="name"
                                                placeholder="{{ config('app.lang') != 'en' ? '김길동' : 'Kim Gil-dong' }}"
                                                value="{{ $user && $user->personInChargeInformation ? $user->personInChargeInformation->name : '' }}">
                                        </div>
                                    </div>
                                    <div class="row mb-3 w-100 justify-content-center">
                                        <label for="position" class="col-sm-4 col-form-label"
                                            style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('직위') : __('Position') }}
                                            <span style="color: #FC565D;">*</span></label>
                                        <div class="col-sm-8">
                                            <select class="form-control required-input"
                                                style="background-color: #869CAF; color: #FFFFFF;" id="position"
                                                name="position">
                                                <option value="Manager"
                                                    {{ $user && $user->personInChargeInformation && $user->personInChargeInformation->position == '과장' ? 'selected' : '' }}>
                                                    {{ config('app.lang') != 'en' ? __('과장') : __('Manager') }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-3 w-100 justify-content-center">
                                        <label for="jobTitle" class="col-sm-4 col-form-label"
                                            style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('직책') : __('Job Title') }}
                                            <span style="color: #FC565D;">*</span></label>
                                        <div class="col-sm-8">
                                            <select class="form-control required-input"
                                                style="background-color: #869CAF; color: #FFFFFF;" id="jobTitle"
                                                name="job_title">
                                                <option value="Team Leader"
                                                    {{ $user && $user->personInChargeInformation && $user->personInChargeInformation->job_title == '팀장' ? 'selected' : '' }}>
                                                    {{ config('app.lang') != 'en' ? __('팀장') : __('Team Leader') }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-3 w-100 justify-content-center">
                                        <label for="department" class="col-sm-4 col-form-label"
                                            style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('소속부서') : __('Department') }}
                                            <span style="color: #FC565D;">*</span></label>
                                        <div class="col-sm-8">
                                            <select class="form-control required-input"
                                                style="background-color: #869CAF; color: #FFFFFF;" id="department"
                                                name="department">
                                                <option value="IT Department"
                                                    {{ $user && $user->personInChargeInformation && $user->personInChargeInformation->department == '전산부' ? 'selected' : '' }}>
                                                    {{ config('app.lang') != 'en' ? __('전산부') : __('IT Department') }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row mb-3 w-100 justify-content-center">
                                        <label for="officePhone" class="col-sm-4 col-form-label"
                                            style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('직통번호') : __('Office Phone') }}
                                            <span style="color: #FC565D;">*</span></label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control required-input" id="officePhone"
                                                name="direct_number" placeholder="023334567"
                                                value="{{ $user && $user->personInChargeInformation ? $user->personInChargeInformation->direct_number : '' }}">
                                        </div>
                                    </div>
                                    <div class="row mb-3 w-100 justify-content-center">
                                        <label for="mobilePhone" class="col-sm-4 col-form-label"
                                            style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('휴대폰번호') : __('Mobile Phone') }}
                                            <span style="color: #FC565D;">*</span></label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control required-input" id="mobilePhone"
                                                name="cell_phone_number" placeholder="01012345678"
                                                value="{{ $user && $user->personInChargeInformation ? $user->personInChargeInformation->cell_phone_number : '' }}">
                                        </div>
                                    </div>
                                    <div class="row mb-3 w-100 justify-content-center">
                                        <div class="col-sm-8 offset-sm-4">
                                            <div class="form-check text-left">
                                                <label class="form-check-label">
                                                    <input class="form-check-input" type="checkbox" id="smsConsent"
                                                        name="receive_sms"
                                                        {{ $user && $user->personInChargeInformation && $user->personInChargeInformation->receive_sms ? 'checked' : '' }}
                                                        onchange="handleSMSChange(event)">
                                                    <span class="form-check-sign"></span>
                                                    {{ config('app.lang') != 'en' ? __('SMS 수신에 동의합니다.') : __('I agree to receive SMS') }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3 w-100 justify-content-center">
                                        <label for="email" class="col-sm-4 col-form-label"
                                            style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('이메일') : __('Email') }}
                                            <span style="color: #FC565D;">*</span></label>
                                        <div class="col-sm-8">
                                            <input type="email" class="form-control required-input" id="email"
                                                name="email" placeholder="flywork@flywork.co.kr"
                                                value="{{ $user && $user->personInChargeInformation ? $user->personInChargeInformation->email : '' }}">
                                        </div>
                                    </div>
                                    <div class="row mb-3 w-100 justify-content-center">
                                        <div class="col-sm-8 offset-sm-4">
                                            <div class="form-check text-left">
                                                <label class="form-check-label">
                                                    <input class="form-check-input required-input" type="checkbox"
                                                        id="emailConsent" name="receive_email"
                                                        {{ $user && $user->personInChargeInformation && $user->personInChargeInformation->receive_email ? 'checked' : '' }}
                                                        onchange="handleEmailChange(event)">
                                                    <span class="form-check-sign"></span>
                                                    {{ config('app.lang') != 'en' ? __('이메일 수신에 동의합니다.') : __('I agree to receive emails') }}
                                                </label>
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
    </div>
    <!--  -->

    <!-- card of additional information -->
    <div id="additionalInfo" class="col-md-12 content-section">
        <div class="card mb-3" style="background-color: #2c3e50; color: #ecf0f1;">
            <div class="card-body">
                <div class="col-md-12 d-flex">
                    <div class="card flex-fill d-flex flex-column" style="background-color: #324D65; color: #ecf0f1;">
                        <div class="card-header w-100 text-left" style="background-color: #869CAF; color: #ecf0f1;">
                            <h5 class="title">
                                {{ config('app.lang') != 'en' ? __('부가정보') : __('Additional Information') }}
                            </h5>
                        </div>
                        <div
                            class="card-body-custom-1 flex-fill d-flex flex-column justify-content-left align-items-left w-100">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row mb-3 w-100 justify-content-center">
                                        <label for="businessNumber" class="col-sm-4 col-form-label"
                                            style="color: #ecf0f1;">{{ config('app.lang') != 'en' ? __('사업자번호') : __('Business Number') }}
                                            <span style="color: #FC565D;">*</span></label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control required-input" id="businessNumber"
                                                name="business_number" placeholder="1234567890"
                                                value="{{ $user && $user->additionalInformation ? $user->additionalInformation->business_number : '' }}">
                                        </div>
                                    </div>
                                    <div class="row mb-3 w-100 justify-content-center">
                                        <label for="corporateNumber" class="col-sm-4 col-form-label"
                                            style="color: #ecf0f1;">{{ config('app.lang') != 'en' ? __('법인번호') : __('Corporate Number') }}
                                            <span style="color: #FC565D;">*</span></label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control required-input" id="corporateNumber"
                                                name="corporate_number" placeholder="1234567890"
                                                value="{{ $user && $user->additionalInformation ? $user->additionalInformation->corporate_number : '' }}">
                                        </div>
                                    </div>
                                    <div class="row mb-3 w-100 justify-content-center">
                                        <label for="attachedFile" class="col-sm-4 col-form-label"
                                            style="color: #ecf0f1;">
                                            {{ config('app.lang') != 'en' ? __('첨부파일') : __('The attached file') }}
                                        </label>
                                        <div class="col-sm-8">
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="attachedFile"
                                                    id="attachedFile" style="background-color: #1E3142; color: #FFFFFF;"
                                                    readonly>
                                                <input type="file" id="fileInput" style="display: none;" multiple>
                                                <div class="input-group">
                                                    <button id="fileButton"
                                                        class="btn btn-info">{{ config('app.lang') != 'en' ? __('파일첨부') : __('File attachment') }}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3 w-100 justify-content-center">
                                        <div class="col-sm-8 offset-sm-4">
                                            <div class="file-attachment-user-file-preview" id="filePreview"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row mb-3 w-100 justify-content-center">
                                        <label for="typeOfBusiness" class="col-sm-4 col-form-label"
                                            style="color: #ecf0f1;">{{ config('app.lang') != 'en' ? __('업종') : __('Type of Business') }}
                                            <span style="color: #FC565D;">*</span></label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control required-input" id="typeOfBusiness"
                                                name="type_of_business"
                                                placeholder="{{ config('app.lang') != 'en' ? '의료서비스' : 'Medical Service' }}"
                                                value="{{ $user && $user->additionalInformation ? $user->additionalInformation->type_of_business : '' }}">
                                        </div>
                                    </div>
                                    <div class="row mb-3 w-100 justify-content-center">
                                        <label for="businessType" class="col-sm-4 col-form-label"
                                            style="color: #ecf0f1;">{{ config('app.lang') != 'en' ? __('업태') : __('Business Type') }}
                                            <span style="color: #FC565D;">*</span></label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control required-input" id="businessType"
                                                name="business_type"
                                                placeholder="{{ config('app.lang') != 'en' ? '의료서비스공급' : 'Medical Service Provider' }}"
                                                value="{{ $user && $user->additionalInformation ? $user->additionalInformation->business_type : '' }}">
                                        </div>
                                    </div>
                                    <div class="row mb-3 w-100 justify-content-center">
                                        <label for="emailReceivingInvoice" class="col-sm-4 col-form-label"
                                            style="color: #ecf0f1;">{{ config('app.lang') != 'en' ? __('세금계산서 수신이메일') : __('Email Receiving Tax Invoice') }}
                                            <span style="color: #FC565D;">*</span></label>
                                        <div class="col-sm-8">
                                            <input type="email" class="form-control required-input"
                                                id="emailReceivingInvoice" name="email_receiving_tax_invoice"
                                                placeholder="flywork@flywork.co.kr"
                                                value="{{ $user && $user->additionalInformation ? $user->additionalInformation->email_receiving_tax_invoice : '' }}">
                                        </div>
                                    </div>
                                    <div class="row mb-3 w-100 justify-content-center">
                                        <div class="col-sm-8 offset-sm-4">
                                            <div class="form-check text-left">
                                                <label class="form-check-label">
                                                    <input class="form-check-input required-input" type="checkbox"
                                                        id="emailConsent" name="receive_email"
                                                        {{ $user && $user->additionalInformation && $user->additionalInformation->receive_email ? 'checked' : '' }}>
                                                    <span class="form-check-sign"></span>
                                                    {{ config('app.lang') != 'en' ? __('이메일 수신에 동의합니다.') : __('I agree to receive emails') }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3 w-100 justify-content-center">
                                        <label for="dataDocumentOutput" class="col-sm-4 col-form-label"
                                            style="color: #ecf0f1;">{{ config('app.lang') != 'en' ? __('데이터 문서출력 (PDF)') : __('Data Document Output (PDF)') }}</label>
                                        <div class="col-sm-8">
                                            <select class="form-control" id="dataDocumentOutput"
                                                name="data_document_output"
                                                onchange="handleDataOutputPdfSelected(event)">
                                                <option
                                                    value="{{ config('app.lang') != 'en' ? __('미사용') : __('Not Use') }}" {{ $user && $user->additionalInformation && ($user->additionalInformation->data_document_output == '미사용' ||
    $user->additionalInformation->data_document_output == 'Not Use') ? 'selected' : '' }}>
                                                    {{ config('app.lang') != 'en' ? __('미사용') : __('Not Use') }}
                                                </option>
                                                <option value="{{ config('app.lang') != 'en' ? __('사용') : __('Use') }}" {{ $user && $user->additionalInformation && ($user->additionalInformation->data_document_output == '사용' ||
    $user->additionalInformation->data_document_output == 'Use') ? 'selected' : '' }}>
                                                    {{ config('app.lang') != 'en' ? __('사용') : __('Use') }}
                                                </option>
                                            </select>
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

    <!-- card of service subscription information  -->
    <div id="serviceSubscriptionInfo" class="col-md-12 content-section">
        <div class="card mb-3" style="background-color: #2c3e50; color: #ecf0f1;">
            <div class="card-body">
                <div class="col-md-12 d-flex">
                    <div class="card flex-fill d-flex flex-column" style="background-color: #324D65; color: #ecf0f1;">
                        <div class="card-header w-100 text-left" style="background-color: #869CAF; color: #ecf0f1;">
                            <h5 class="title">
                                {{ config('app.lang') != 'en' ? __('서비스 가입정보') : __('Service Subscription Information') }}
                            </h5>
                        </div>
                        <div
                            class="card-body-custom-1 flex-fill d-flex flex-column justify-content-left align-items-left w-100">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row mb-3 w-100">
                                        <label for="subscriptionService" class="col-sm-4 col-form-label"
                                            style="color: #ecf0f1;">{{ config('app.lang') != 'en' ? __('가입서비스') : __('Subscription Service') }}</label>
                                        <div class="col-sm-8">
                                            <select class="form-control"
                                                style="background-color: #1E3142; color: #FFFFFF;"
                                                id="subscriptionService" name="subscription_service" disabled>
                                                @foreach ($serviceUsageSettings as $serviceUsageSetting)
                                                <option value="{{ $serviceUsageSetting->service_classification}}"
                                                    {{ $user && $user->serviceSubscriptionInformation &&
                                                    $user->serviceSubscriptionInformation->subscription_service == $serviceUsageSetting->service_classification ? 'selected' : '' }}>
                                                    {{ $serviceUsageSetting->service_classification_desc }}
                                                </option>
                                                @endforeach
                                            </select>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-3 w-100">
                                        <label for="startDate" class="col-sm-4 col-form-label"
                                            style="color: #ecf0f1;">{{ config('app.lang') != 'en' ? __('사용시작일') : __('Start Date of Use') }}</label>
                                        <div class="col-sm-8">
                                            <div class="input-group">
                                                <input type="date" class="form-control" id="startDate"
                                                    name="start_date_of_use" placeholder="YYYY-MM-DD"
                                                    value="{{ $user && $user->serviceSubscriptionInformation ? $user->serviceSubscriptionInformation->start_date_of_use : '' }}"
                                                    style="background-color: #1E3142; color: #FFFFFF;" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3 w-100">
                                        <label for="endDate" class="col-sm-4 col-form-label"
                                            style="color: #ecf0f1;">{{ config('app.lang') != 'en' ? __('사용만료일') : __('Expiration Date') }}</label>
                                        <div class="col-sm-8">
                                            <div class="input-group">
                                                <input type="date" class="form-control" id="endDate"
                                                    name="expiration_date" placeholder="YYYY-MM-DD"
                                                    value="{{ $user && $user->serviceSubscriptionInformation ? $user->serviceSubscriptionInformation->expiration_date : '' }}"
                                                    style="background-color: #1E3142; color: #FFFFFF;" readonly>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row mb-3 w-100">
                                        <label for="userCount" class="col-sm-4 col-form-label"
                                            style="color: #ecf0f1;">{{ config('app.lang') != 'en' ? __('서비스 사용자수(명)') : __('Number of Service Users') }}</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="userCount"
                                                name="number_of_service_users"
                                                placeholder="{{ config('app.lang') != 'en' ? '100~150' : '100-150' }}"
                                                value="{{ $user && $user->serviceSubscriptionInformation ? $user->serviceSubscriptionInformation->number_of_service_users : '' }}"
                                                style="background-color: #1E3142; color: #FFFFFF;" readonly>
                                        </div>
                                    </div>
                                    <div class="row mb-3 w-100">
                                        <label for="usagePeriod" class="col-sm-4 col-form-label"
                                            style="color: #ecf0f1;">{{ config('app.lang') != 'en' ? __('사용기간') : __('Usage Period') }}</label>
                                        <div class="col-sm-8">
                                            <select class="form-control"
                                                style="background-color: #1E3142; color: #FFFFFF;" id="usagePeriod"
                                                name="usage_period_months" disabled>
                                                <option value="12"
                                                    {{ $user && $user->serviceSubscriptionInformation && $user->serviceSubscriptionInformation->usage_period_months == 12 ? 'selected' : '' }}>
                                                    {{ config('app.lang') != 'en' ? __('12개월') : __('12 months') }}
                                                </option>
                                                <option value="6"
                                                    {{ $user && $user->serviceSubscriptionInformation && $user->serviceSubscriptionInformation->usage_period_months == 6 ? 'selected' : '' }}>
                                                    {{ config('app.lang') != 'en' ? __('6개월') : __('6 months') }}
                                                </option>
                                            </select>

                                        </div>
                                    </div>
                                    <div class="row mb-3 w-100">
                                        <label for="serviceFee" class="col-sm-4 col-form-label"
                                            style="color: #ecf0f1;">{{ config('app.lang') != 'en' ? __('서비스 이용료(부가세별도)') : __('Service Fee (Excluding VAT)') }}</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-custom numeric-input"
                                                id="serviceFee" name="service_fee"
                                                style="background-color: #1E3142; color: #FFFFFF;"
                                                placeholder="{{ config('app.lang') != 'en' ? '5,000,000원' : '5,000,000 KRW' }}"
                                                value="{{ $user && $user->serviceSubscriptionInformation ? $user->serviceSubscriptionInformation->service_fee : '' }}"
                                                readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <button class="btn btn-primary w-100" data-toggle="modal"
                                        data-target="#subscriptionModal">{{ config('app.lang') != 'en' ? __('사용기간 연장') : __('Extend Usage Period') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--  -->

    <!-- modal service subscription -->
    <!-- The Modal -->
    <div class="modal fade" id="subscriptionModal">
        <div class="modal-dialog modal-lg" style="max-width:500px;margin-top: -50px;">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title" style="color:#FFFFFF">
                        {{ config('app.lang') != 'en' ? __('사용기간 연장') : __('Extend Usage Period') }}
                    </h4>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <div class="row mb-3">
                        <label for="subscriptionServiceModal" class="col-sm-4 col-form-label"
                            style="color:#FFFFFF">{{ config('app.lang') != 'en' ? __('연장 가입서비스') : __('Extended Subscription Service') }}
                            <span style="color: #FC565D;">*</span></label>
                        <div class="col-sm-8">
                            <select class="form-control required-input-modal" id="subscriptionServiceModal"
                                name="subscription_service" style="background-color: #869CAF; color: #FFFFFF;"
                                onchange="handleSubscriptionServiceSelected(event)">
                                @foreach ($serviceUsageSettings as $serviceUsageSetting)
                                <option value="{{ $serviceUsageSetting->service_classification}}">
                                    {{ $serviceUsageSetting->service_classification_desc }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="usagePeriodModal" class="col-sm-4 col-form-label"
                            style="color:#FFFFFF">{{ config('app.lang') != 'en' ? __('연장 사용기간') : __('Extended Usage Period') }}
                            <span style="color: #FC565D;">*</span></label>
                        <div class="col-sm-8">
                            <select class="form-control required-input-modal" id="usagePeriodModal"
                                name="usage_period_months" style="background-color: #869CAF; color: #FFFFFF;"
                                onchange="handleUsagePeriodSelected(event)">
                                <option value="12">
                                    {{ config('app.lang') != 'en' ? __('12개월') : __('12 months') }}
                                </option>
                                <option value="6">
                                    {{ config('app.lang') != 'en' ? __('6개월') : __('6 months') }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="startDateModal" class="col-sm-4 col-form-label"
                            style="color:#FFFFFF">{{ config('app.lang') != 'en' ? __('연장 사용시작일') : __('Extended Start Date of Use') }}
                            <span style="color: #FC565D;">*</span></label>
                        <div class="col-sm-8">
                            <input type="date" class="form-control required-input-modal" id="startDateModal"
                                name="start_date_of_use" placeholder="YYYY-MM-DD" onchange="handleChangeStartDate()">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="endDateModal" class="col-sm-4 col-form-label"
                            style="color:#FFFFFF">{{ config('app.lang') != 'en' ? __('연장 사용만료일') : __('Extended Expiration Date') }}</label>
                        <div class="col-sm-8">
                            <input type="date" class="form-control" id="endDateModal" name="expiration_date"
                                placeholder="YYYY-MM-DD" style="background-color: #1E3142; color: #FFFFFF;" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="serviceFeeModal" class="col-sm-4 col-form-label"
                            style="color:#FFFFFF">{{ config('app.lang') != 'en' ? __('서비스 이용료 (부가세별도)') : __('Service Fee (Excluding VAT)') }}</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control form-control-custom numeric-input"
                                id="serviceFeeModal" name="service_fee"
                                placeholder="{{ config('app.lang') != 'en' ? '5,000,000원' : '5,000,000 KRW' }}"
                                style="background-color: #1E3142; color: #FFFFFF;"
                                value="{{$serviceUsageSettings[0]->fees_used_year}}" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="userCountModal" class="col-sm-4 col-form-label"
                            style="color:#FFFFFF">{{ config('app.lang') != 'en' ? __('서비스 사용자수(명)') : __('Number of Service Users') }}</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control numeric-input" id="userCountModal"
                                name="number_of_service_users"
                                placeholder="{{ config('app.lang') != 'en' ? '100~150' : '100-150' }}"
                                style="background-color: #1E3142; color: #FFFFFF;"
                                value="{{$serviceUsageSettings[0]->number_of_user_from . '-' . $serviceUsageSettings[0]->number_of_user_to}}"
                                readonly>
                        </div>
                    </div>

                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary"
                        onclick="submitExtensionForm();">{{ config('app.lang') != 'en' ? __('저장') : __('Save') }}</button>
                    <button type="button" class="btn btn-info"
                        data-dismiss="modal">{{ config('app.lang') != 'en' ? __('취소') : __('Cancel') }}</button>
                </div>

            </div>
        </div>
    </div>
    <!--  -->
</div>

<script>
function handleChangeStartDate() {
    const startDate = document.getElementById('startDateModal').value;
    const expiryDate = calculateExpiryDate(startDate, usagePeriod);
    document.getElementById('endDateModal').value = expiryDate;
    // document.getElementById('expirationDate').value = expiryDate;
    // document.getElementById('serviceStartDate').value = startDate;
}

function handleUsagePeriodSelected(event) {
    const value = event.target.value;
    usagePeriod = parseInt(value);
    calculateServiceFee();
    resetFields();
}


let usagePeriod = 12
let serviceClassification = `{{$serviceUsageSettings[0]->service_classification}}`
let serviceClassificationYearly = `{{$serviceUsageSettings[0]->fees_used_year}}`
let serviceClassificationMonthly = `{{$serviceUsageSettings[0]->usage_fee_monthly}}`

function handleSubscriptionServiceSelected(event) {
    const serviceUsageSettings = @json($serviceUsageSettings);
    const value = event.target.value;
    serviceClassification = value

    var serviceUsageSetting = serviceUsageSettings.filter(function(el) {
        return el.service_classification == serviceClassification;
    });
    serviceClassificationYearly = serviceUsageSetting[0].fees_used_year;
    serviceClassificationMonthly = serviceUsageSetting[0].usage_fee_monthly;

    document.getElementById('userCountModal').value =
        `${serviceUsageSetting[0].number_of_user_from}-${serviceUsageSetting[0].number_of_user_to}`;
    // document.getElementById('inputServiceOfUse').value = serviceUsageSetting[0].service_classification_desc
    calculateServiceFee();
    resetFields();
}

function calculateServiceFee() {
    if (isMultipleOf12(usagePeriod)) {
        document.getElementById('serviceFeeModal').value = serviceClassificationYearly * (usagePeriod / 12)
    } else {
        document.getElementById('serviceFeeModal').value = serviceClassificationMonthly * usagePeriod
    }
}

function resetFields() {
    document.getElementById('startDateModal').value = '';
    document.getElementById('endDateModal').value = '';
    // document.getElementById('expirationDate').value = '';
    // document.getElementById('serviceStartDate').value = '';
}

function handleDataOutputPdf(event) {

    document.getElementById('inputDataOutput').checked = event.target.checked
    if (event.target.checked) {
        document.getElementById('dataDocumentOutput').value = `{{ config('app.lang') != 'en' ? __('사용') : __('Use') }}`
    } else {
        document.getElementById('dataDocumentOutput').value =
            `{{ config('app.lang') != 'en' ? __('미사용') : __('Not Use') }}`
    }
}

function handleDataOutputPdfSelected(event) {
    const value = event.target.value;
    if (value === 'Use' || value === '사용') {
        document.getElementById('inputDataOutput').checked = true
    } else {
        document.getElementById('inputDataOutput').checked = false
    }
}

function handleEmailChange(event) {
    document.getElementById('inputEmail').checked = event.target.checked
    document.getElementById('emailConsent').checked = event.target.checked
}

function handleSMSChange(event) {
    document.getElementById('inputSms').checked = event.target.checked
    document.getElementById('smsConsent').checked = event.target.checked
}

function handleWorkersChange() {
    const workersValue = document.getElementById('workers').value;
    document.getElementById('inputWorker').value = workersValue +
        `{{ config('app.lang') != 'en' ? __(' 명') : __(' people') }}`
}

function selectSubject(button) {
    const inputFieldOfHelp = document.getElementById('inputFieldOfHelp');
    const selectedSubjects = document.getElementById('selectedSubjects');
    const label = document.getElementById('fieldOfHelp');
    const subjectText = button.innerText;
    let subjectsArray = selectedSubjects.value ? selectedSubjects.value.split(' | ') : [];

    if (button.classList.contains('btn-info')) {
        button.classList.remove('btn-info');
        button.classList.add('btn-primary');
        subjectsArray.push(subjectText);
    } else {
        button.classList.remove('btn-primary');
        button.classList.add('btn-info');
        subjectsArray = subjectsArray.filter(subject => subject !== subjectText);
    }

    selectedSubjects.value = subjectsArray.join(' | ');
    selectedSubjects.style.border = '';

    label.value = `{{ config('app.lang') != 'en' ? __('선택') : __('Select') }} (${subjectsArray.length})`;
    label.style.border = '';

    inputFieldOfHelp.value = `${subjectsArray.length} {{ config('app.lang') != 'en' ? __('개') : __('items') }}`;
}

function submitForm() {
    if (!globalValidateInput()) {
        document.getElementById('error-message-pop-up').innerText =
            `{{ config('app.lang') != 'en' ? __('필수 입력란을 모두 작성해 주세요.') : __('Please fill out all required fields.') }}`
        $('#errorFailedWithMessagePopupModal').modal('show');
        return
    }

    // Get the current URL
    let currentUrl = window.location.href;
    console.log('Current URL:', currentUrl);

    // Create a URL object from the current URL
    let urlObject = new URL(currentUrl);

    // Split the pathname by '/'
    let pathSegments = urlObject.pathname.split('/');

    // Get the last segment
    let userId = pathSegments[pathSegments.length - 1];
    const formData = new FormData(document.getElementById('editForm'));

    // Collecting required data
    const nicknameElement = document.getElementById('nickname');
    const inputEmailElement = document.getElementById('inputEmail');
    const inputSmsElement = document.getElementById('inputSms');
    const inputDataOutputElement = document.getElementById('inputDataOutput');
    const inputfullAddressElement = document.getElementById('fullAddress');

    if (nicknameElement) formData.append('nickname', nicknameElement.value);
    if (inputEmailElement) formData.append('receive_email', inputEmailElement.checked ? '1' : '0'); // change int
    if (inputSmsElement) formData.append('receive_sms', inputSmsElement.checked ? '1' : '0'); // change int
    if (inputDataOutputElement) formData.append('data_output', inputDataOutputElement.checked ? '1' :
        '0'); // change int


    if (inputfullAddressElement) formData.append('fullAddress', inputfullAddressElement.value);

    //Member Status
    const inputFieldOfHelpElement = document.getElementById('inputFieldOfHelp');
    if (inputFieldOfHelpElement && inputFieldOfHelpElement.value) {
        formData.append('member_status[field_of_help]',
            inputFieldOfHelpElement.value);
    }
    const inputWorkerElement = document.getElementById('inputWorker');
    if (inputWorkerElement && inputWorkerElement.value) {
        formData.append('member_status[worker]',
            inputWorkerElement.value);
    }
    const inputServiceOfUseElement = document.getElementById('inputServiceOfUse');
    if (inputServiceOfUseElement && inputServiceOfUseElement.value) {
        formData.append('member_status[service_of_use]',
            inputServiceOfUseElement.value);
    }
    const inputLatestPaymentDateElement = document.getElementById('inputLatestPaymentDate');
    if (inputLatestPaymentDateElement && inputLatestPaymentDateElement.value) {
        formData.append('member_status[last_payment_date]',
            inputLatestPaymentDateElement.value);
    }
    const inputAnotherPaymentDateElement = document.getElementById('inputAnotherPaymentDate');
    if (inputAnotherPaymentDateElement && inputAnotherPaymentDateElement.value) {
        formData.append('member_status[another_payment_date]',
            inputAnotherPaymentDateElement.value);
    }
    //

    // Collecting optional data
    const membershipDateElement = document.getElementById('membershipDate');
    if (membershipDateElement && membershipDateElement.value) {
        formData.append('subscription_information[membership_registration_date]', membershipDateElement.value);
    }
    const startDateElement = document.getElementById('serviceStartDate');
    if (startDateElement && startDateElement.value) {
        formData.append('subscription_information[service_start_date]', startDateElement.value);
    }
    const expirationDateElement = document.getElementById('expirationDate');
    if (expirationDateElement && expirationDateElement.value) {
        formData.append('subscription_information[service_expiration_date]', expirationDateElement.value);
    }

    const userIdElement = document.getElementById('userId');
    if (userIdElement && userIdElement.value) {
        formData.append('user_id', userIdElement.value);
    }
    const passwordElement = document.getElementById('password');
    if (passwordElement && passwordElement.value) {
        if (passwordElement.value !== '**********') {
            const checkvalidatePassword = validatePassword(passwordElement.value, '', false);
            if (checkvalidatePassword !== true) {
                return
            }
        }
        formData.append('password', passwordElement.value);
    }
    const companyNameElement = document.getElementById('companyName');
    if (companyNameElement && companyNameElement.value) {
        formData.append('subscription_information[organization_name]', companyNameElement.value);
    }
    const addressElement = document.getElementById('address');
    if (addressElement && addressElement.value) {
        formData.append('subscription_information[address]', addressElement.value);
    }
    const postalCodeAddressElement = document.getElementById('postalCodeAddress');
    if (postalCodeAddressElement && postalCodeAddressElement.value) {
        formData.append('subscription_information[postal_code]', postalCodeAddressElement.value);
    }

    const representativeNameElement = document.getElementById('representativeName');
    if (representativeNameElement && representativeNameElement.value) {
        formData.append('subscription_information[name_of_representative]', representativeNameElement.value);
    }
    const workersElement = document.getElementById('workers');
    if (workersElement && workersElement.value) {
        formData.append('subscription_information[number_of_workers]', workersElement.value);
    }
    const homepageElement = document.getElementById('homepage');
    if (homepageElement && homepageElement.value) {
        formData.append('subscription_information[homepage]', homepageElement.value);
    }
    const hrUseElement = document.getElementById('hrUse');
    if (hrUseElement && hrUseElement.value) {
        formData.append('subscription_information[hr_management_usage]', hrUseElement.value);
    }
    //Add faxnumberElement
    const faxnumberElement = document.getElementById('faxNumber');
    if (faxnumberElement && faxnumberElement.value) {
        formData.append('subscription_information[fax_number]', faxnumberElement.value);
    }
    //


    const nameElement = document.getElementById('name');
    console.log(nameElement.value)
    if (nameElement && nameElement.value) {
        formData.append('person_in_charge_information[name]', nameElement.value);
    }
    const jobTitleElement = document.getElementById('jobTitle');
    if (jobTitleElement && jobTitleElement.value) {
        formData.append('person_in_charge_information[job_title]', jobTitleElement.value);
    }
    const positionElement = document.getElementById('position');
    if (positionElement && positionElement.value) {
        formData.append('person_in_charge_information[position]', positionElement.value);
    }
    const departmentElement = document.getElementById('department');
    if (departmentElement && departmentElement.value) {
        formData.append('person_in_charge_information[department]', departmentElement.value);
    }
    const officePhoneElement = document.getElementById('officePhone');
    if (officePhoneElement && officePhoneElement.value) {
        formData.append('person_in_charge_information[direct_number]', officePhoneElement.value);
    }
    const mobilePhoneElement = document.getElementById('mobilePhone');
    if (mobilePhoneElement && mobilePhoneElement.value) {
        formData.append('person_in_charge_information[cell_phone_number]', mobilePhoneElement.value);
    }
    const emailElement = document.getElementById('email');
    if (emailElement && emailElement.value) {
        formData.append('person_in_charge_information[email]', emailElement.value);
    }
    const emailConsentElement = document.getElementById('emailConsent');
    if (emailConsentElement) formData.append('person_in_charge_information[receive_email]', emailConsentElement
        .checked ? '1' : '0');
    const smsConsentElement = document.getElementById('smsConsent');
    if (smsConsentElement) formData.append('person_in_charge_information[receive_sms]', smsConsentElement.checked ?
        '1' : '0');

    const businessNumberElement = document.getElementById('businessNumber');
    if (businessNumberElement && businessNumberElement.value) {
        formData.append('additional_information[business_number]', businessNumberElement.value);
    }
    const corporateNumberElement = document.getElementById('corporateNumber');
    if (corporateNumberElement && corporateNumberElement.value) {
        formData.append('additional_information[corporate_number]', corporateNumberElement.value);
    }
    const typeOfBusinessElement = document.getElementById('typeOfBusiness');
    if (typeOfBusinessElement && typeOfBusinessElement.value) {
        formData.append('additional_information[type_of_business]', typeOfBusinessElement.value);
    }
    const businessTypeElement = document.getElementById('businessType');
    if (businessTypeElement && businessTypeElement.value) {
        formData.append('additional_information[business_type]', businessTypeElement.value);
    }
    const emailReceivingInvoiceElement = document.getElementById('emailReceivingInvoice');
    if (emailReceivingInvoiceElement && emailReceivingInvoiceElement.value) {
        formData.append('additional_information[email_receiving_tax_invoice]', emailReceivingInvoiceElement.value);
    }
    if (emailConsentElement) formData.append('additional_information[receive_email]', emailConsentElement.checked ?
        '1' : '0');
    const dataDocumentOutputElement = document.getElementById('dataDocumentOutput');
    if (dataDocumentOutputElement && dataDocumentOutputElement.value) {
        formData.append('additional_information[data_document_output]', dataDocumentOutputElement.value);
    }

    const subscriptionServiceElement = document.getElementById('subscriptionService');
    if (subscriptionServiceElement && subscriptionServiceElement.value) {
        formData.append('service_subscription_information[subscription_service]', subscriptionServiceElement.value);
    }
    const startDateElement2 = document.getElementById('startDate');
    if (startDateElement2 && startDateElement2.value) {
        formData.append('service_subscription_information[start_date_of_use]', startDateElement2.value);
    }
    const endDateElement = document.getElementById('endDate');
    if (endDateElement && endDateElement.value) {
        formData.append('service_subscription_information[expiration_date]', endDateElement.value);
    }
    const userCountElement = document.getElementById('userCount');
    if (userCountElement && userCountElement.value) {
        formData.append('service_subscription_information[number_of_service_users]', userCountElement.value);
    }
    const usagePeriodElement = document.getElementById('usagePeriod');
    if (usagePeriodElement && usagePeriodElement.value) {
        formData.append('service_subscription_information[usage_period_months]', usagePeriodElement.value);
    }
    const serviceFeeElement = document.getElementById('serviceFee');
    if (serviceFeeElement && serviceFeeElement.value) {
        formData.append('service_subscription_information[service_fee]', serviceFeeElement.value);
    }

    const fileUpload = document.getElementById('fileUpload');
    if (fileUpload && fileUpload.files.length > 0) {
        formData.append('fileUpload', fileUpload.files[0]);
    }

    allFiles.forEach((file, index) => {
        formData.append('fileAttachments[]', file);
    });

    fetch(`/user/{{ $user->id }}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-HTTP-Method-Override': 'PUT'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                $('#successUserPopupModal').modal('show');
            } else {
                $('#errorFailedPopupModal').modal('show');
            }
        })
        .catch(error => console.error('Error:', error));
}

function submitExtensionForm() {
    if (!globalValidateInputWithUniqueName('required-input-modal')) {
        document.getElementById('error-message-pop-up').innerText =
            `{{ config('app.lang') != 'en' ? __('필수 입력란을 모두 작성해 주세요.') : __('Please fill out all required fields.') }}`
        $('#errorFailedWithMessagePopupModal').modal('show');
        return
    }

    const subscriptionServiceModal = document.getElementById('subscriptionServiceModal');
    const usagePeriodModal = document.getElementById('usagePeriodModal');
    const startDateModal = document.getElementById('startDateModal');
    const endDateModal = document.getElementById('endDateModal');
    const serviceFeeModal = document.getElementById('serviceFeeModal');
    const userCountModal = document.getElementById('userCountModal');


    document.getElementById('subscriptionService').value = subscriptionServiceModal.value;
    document.getElementById('usagePeriod').value = usagePeriodModal.value;
    document.getElementById('startDate').value = startDateModal.value;
    document.getElementById('endDate').value = endDateModal.value;
    document.getElementById('serviceFee').value = serviceFeeModal.value;
    document.getElementById('userCount').value = userCountModal.value;

    // subscription information
    document.getElementById('expirationDate').value = endDateModal.value;
    document.getElementById('serviceStartDate').value = startDateModal.value;

    // member status 
    document.getElementById('inputServiceOfUse').value = subscriptionServiceModal.value

    document.getElementById('success-message-pop-up').innerText =
        `{{ config('app.lang') != 'en' ? __('기간 연장 가입이 성공했습니다. 데이터를 저장하기 전에 데이터 서비스 가입 정보를 확인하고 가입이 올바른지 확인한 후 데이터를 저장할 수 있습니다.') : __('extend period subscription Success ,Before saving data, check your data service subscription information and make sure your subscription in correctly ,then can save the data.') }}`
    $('#successWithMessagePopupModal').modal('show');

    $('#successWithMessagePopupModal').on('hidden.bs.modal', function() {
        $('#successWithMessagePopupModal').modal('hide');
        $('#subscriptionModal').modal('hide');
    });
}



function redirectToUserList(url) {
    window.location.href = url;
}

function loadFile(event) {
    const output = document.getElementById('profileImage');
    output.src = URL.createObjectURL(event.target.files[0]);
    output.onload = function() {
        URL.revokeObjectURL(output.src) // free memory
    }
}
document.addEventListener("DOMContentLoaded", function() {
    const navItems = document.querySelectorAll('.nav-bar-custom .nav-item');
    const sections = document.querySelectorAll('.content-section');

    navItems.forEach(item => {
        item.addEventListener('click', function() {
            // Remove active class from all nav items
            navItems.forEach(nav => nav.classList.remove('active'));
            // Add active class to the clicked nav item
            item.classList.add('active');

            // Hide all sections
            sections.forEach(section => section.classList.remove('active'));
            // Show the targeted section
            const target = item.getAttribute('data-target');
            document.getElementById(target).classList.add('active');
        });
    });
});

function findPostalCode() {
    const postalCode = document.getElementById('postalCodeAddress').value;
    if (postalCode) {
        fetch(`/user/postal-code/${postalCode}`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    const lang = `{{config('app.lang')}}`
                    data = data.data
                    if (lang === 'en') {
                        document.getElementById('address').value =
                            `${data.district_name_en}, ${data.master_data_city.city_name_en}`
                    } else {
                        document.getElementById('address').value =
                            `${data.master_data_city.city_name}, ${data.district_name}`
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }
}
</script>

<script>
const allFiles = [];
let existingFiles = [];
@if($file_attachments)
existingFiles = @json($file_attachments);
@endif
document.getElementById('fileButton').addEventListener('click', function() {
    document.getElementById('fileInput').click();
});

document.getElementById('fileInput').addEventListener('change', function() {
    const fileInput = document.getElementById('fileInput');
    const filePreview = document.getElementById('filePreview');

    Array.from(fileInput.files).forEach(file => {
        allFiles.push(file);
    });

    renderFilePreviews();
});

function renderFilePreviews() {
    const filePreview = document.getElementById('filePreview');
    filePreview.innerHTML = ''; // Clear previous previews

    let rowDiv;

    // Render existing files
    existingFiles.forEach((file, index) => {
        if (index % 2 === 0) {
            rowDiv = document.createElement('div');
            rowDiv.style.display = 'flex';
            rowDiv.style.justifyContent = 'center';
            rowDiv.style.width = '100%';
            filePreview.appendChild(rowDiv);
        }

        const card = document.createElement('div');
        card.classList.add('file-attachment-user-card');

        const cardBody = document.createElement('div');
        cardBody.classList.add('file-attachment-user-card-body');

        // Check if the file is an image
        if (file.path && file.path.match(/\.(jpeg|jpg|gif|png)$/)) {
            const img = document.createElement('img');
            img.src = `{{asset('storage')}}/` + file.path; // Assume 'file.path' contains the URL to the image
            img.style.maxWidth = '100px'; // Adjust as necessary
            img.style.maxHeight = '100px'; // Adjust as necessary
            cardBody.appendChild(img);
        } else {
            cardBody.textContent = file.original_name;
        }

        const closeButton = document.createElement('button');
        closeButton.classList.add('file-attachment-user-close-btn');
        closeButton.textContent = 'x';
        closeButton.addEventListener('click', function() {
            existingFiles.splice(index, 1);
            renderFilePreviews();
        });

        card.appendChild(closeButton);
        card.appendChild(cardBody);
        rowDiv.appendChild(card);
    });

    // Render new files
    allFiles.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            if ((index + existingFiles.length) % 2 === 0) {
                rowDiv = document.createElement('div');
                rowDiv.style.display = 'flex';
                rowDiv.style.justifyContent = 'center';
                rowDiv.style.width = '100%';
                filePreview.appendChild(rowDiv);
            }

            const card = document.createElement('div');
            card.classList.add('file-attachment-user-card');

            const cardBody = document.createElement('div');
            cardBody.classList.add('file-attachment-user-card-body');

            // Check if the file is an image
            if (file.type.startsWith('image/')) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.maxWidth = '100px'; // Adjust as necessary
                img.style.maxHeight = '100px'; // Adjust as necessary
                cardBody.appendChild(img);
            } else {
                cardBody.textContent = file.name;
            }

            const closeButton = document.createElement('button');
            closeButton.classList.add('file-attachment-user-close-btn');
            closeButton.textContent = 'x';
            closeButton.addEventListener('click', function() {
                allFiles.splice(index, 1);
                renderFilePreviews();
            });

            card.appendChild(closeButton);
            card.appendChild(cardBody);
            rowDiv.appendChild(card);
        };
        reader.readAsDataURL(file);
    });
}

renderFilePreviews()
</script>

<script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script>
function execDaumPostcode() {
    new daum.Postcode({
        oncomplete: function(data) {
            // 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

            // 도로명 주소의 노출 규칙에 따라 주소를 표시한다.
            // 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
            var roadAddr = data.roadAddress; // 도로명 주소 변수
            var extraRoadAddr = ''; // 참고 항목 변수

            // 법정동명이 있을 경우 추가한다. (법정리는 제외)
            // 법정동의 경우 마지막 문자가 "동/로/가"로 끝난다.
            if (data.bname !== '' && /[동|로|가]$/g.test(data.bname)) {
                extraRoadAddr += data.bname;
            }
            // 건물명이 있고, 공동주택일 경우 추가한다.
            if (data.buildingName !== '' && data.apartment === 'Y') {
                extraRoadAddr += (extraRoadAddr !== '' ? ', ' + data.buildingName : data.buildingName);
            }
            // 표시할 참고항목이 있을 경우, 괄호까지 추가한 최종 문자열을 만든다.
            if (extraRoadAddr !== '') {
                extraRoadAddr = ' (' + extraRoadAddr + ')';
            }

            // 우편번호와 주소 정보를 해당 필드에 넣는다.
            document.getElementById('postalCodeAddress').value = data.zonecode;
            document.getElementById('postalCodeAddress').style.border = '';
            document.getElementById("address").value = roadAddr;
            document.getElementById("address").style.border = '';

            if (data.jibunAddress) {
                document.getElementById("address").value = roadAddr + ' ,' + data.jibunAddress;
            }
            // 참고항목 문자열이 있을 경우 해당 필드에 넣는다.
            if (roadAddr !== '') {
                document.getElementById("fullAddress").value = extraRoadAddr;
                document.getElementById("fullAddress").style.border = '';
            } else {
                document.getElementById("fullAddress").value = '';
            }

            // var guideTextBox = document.getElementById("guide");
            // // 사용자가 '선택 안함'을 클릭한 경우, 예상 주소라는 표시를 해준다.
            // if (data.autoRoadAddress) {
            //     var expRoadAddr = data.autoRoadAddress + extraRoadAddr;
            //     guideTextBox.innerHTML = '(예상 도로명 주소 : ' + expRoadAddr + ')';
            //     guideTextBox.style.display = 'block';

            // } else if (data.autoJibunAddress) {
            //     var expJibunAddr = data.autoJibunAddress;
            //     guideTextBox.innerHTML = '(예상 지번 주소 : ' + expJibunAddr + ')';
            //     guideTextBox.style.display = 'block';
            // } else {
            //     guideTextBox.innerHTML = '';
            //     guideTextBox.style.display = 'none';
            // }
        }
    }).open();
}

function deleteUser(id) {
    $('#deletedConfirmationPopUpModal').modal('show');
    document.getElementById('confirmed').addEventListener('click', function confirmHandler() {
        fetch('/user/delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    user_ids: [id]
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    $('#successUserPopupModal').modal('show');
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
</script>@endsection