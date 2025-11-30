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
                                {{ config('app.lang') != 'en' ? __('가입정보') : __('Signup Information') }}
                            </h5>
                            <div>
                                <button id="list" class="btn btn-info btn-sm" type="button" style="color: #fff;"
                                    onclick="redirectToUserList('{{ route('user.index') }}')">
                                    {{ config('app.lang') != 'en' ? __('목록') : __('List') }}
                                </button>
                                <button id="save" class="btn btn-primary btn-sm" type="submit" style="color: #fff;"
                                    onclick="save()">
                                    {{ config('app.lang') != 'en' ? __('저장') : __('Save') }}
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
                                    <img src="{{ asset('black') }}/img/default-avatar.png" alt="Profile"
                                        class="profile-img" id="profileImage">
                                    <label for="fileUpload" class="edit-icon">
                                        <input type="file" id="fileUpload" accept="image/*" onchange="loadFile(event)">
                                        <img src="{{ asset('black') }}/icons/general/pencil.svg">
                                    </label>
                                </div>
                                <div class="col-md-1"></div>
                                <div>
                                    <div class="row mb-3 w-100 justify-content-center">
                                        <label for="nickname" class="col-sm-4 col-form-label"
                                            style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('이름') : __('Name') }}
                                            <span style="color: #FC565D;">*</span></label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control required-input" name="nickname"
                                                style="background-color: #FFFFFF; color: #1E3142;" id="nickname"
                                                placeholder="{{ config('app.lang') != 'en' ? __('XX 개') : __('XX items') }}"
                                                :invalid>
                                        </div>
                                    </div>
                                </div>
                                <!-- <div class="location-component">
                                    <span class="icon"><img
                                            src="{{ asset('black') }}/icons/general/location.svg"></span>
                                    <span
                                        class="text">{{ config('app.lang') != 'en' ? __('서울시 강남구 도곡동') : __('Dogok-dong, Gangnam-gu, Seoul') }}</span>
                                </div> -->
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
                                                    name="inputEmail" onchange="handleEmailChange(event)">
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
                                        style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('PDF') : __('PDF') }}
                                        <span style="color: #FC565D;">*</span></label>
                                    <div class="col-sm-8 d-flex align-items-center">
                                        <div class="component">
                                            <span
                                                class="label-input">{{ config('app.lang') != 'en' ? __('사용') : __('Use') }}</span>
                                            <label class="switch">
                                                <input class="required-input" type="checkbox" id="inputDataOutput"
                                                    name="inputDataOutput" onchange="handleDataOutputPdf(event)">
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
                                        <input type="text" class="form-control numeric-input" name="inputFieldOfHelp"
                                            style="background-color: #1E3142; color: #FFFFFF;" id="inputFieldOfHelp"
                                            placeholder="{{ config('app.lang') != 'en' ? __('XX 개') : __('XX items') }}"
                                            readonly>
                                    </div>
                                </div>
                                <div class="row mb-3 w-100 justify-content-center">
                                    <label for="inputWorker" class="col-sm-4 col-form-label"
                                        style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('근로자') : __('Worker') }}</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="inputWorker"
                                            style="background-color: #1E3142; color: #FFFFFF;" id="inputWorker"
                                            placeholder="{{ config('app.lang') != 'en' ? __('XX 명') : __('XX people') }}"
                                            readonly>
                                    </div>
                                </div>
                                <div class="row mb-3 w-100 justify-content-center">
                                    <label for="inputServiceOfUse" class="col-sm-4 col-form-label"
                                        style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('이용서비스') : __('Service of Use') }}</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="inputServiceOfUse"
                                            style="background-color: #1E3142; color: #FFFFFF;" id="inputServiceOfUse"
                                            placeholder="{{ config('app.lang') != 'en' ? __('베이직') : __('Basic') }}"
                                            value="{{$serviceUsageSettings[0]->service_classification_desc}}" readonly>
                                    </div>
                                </div>
                                <div class="row mb-3 w-100 justify-content-center">
                                    <label for="inputLatestPaymentDate" class="col-sm-4 col-form-label"
                                        style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('최근 결제일') : __('Latest Payment Date') }}
                                        <span style="color: #FC565D;">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="date" class="form-control required-input"
                                            name="inputLatestPaymentDate"
                                            style="background-color: #FFFFFF; color: #1E3142;"
                                            id="inputLatestPaymentDate" placeholder="YYYY-MM-DD">
                                    </div>
                                </div>
                                <div class="row mb-3 w-100 justify-content-center">
                                    <label for="inputAnotherPaymentDate" class="col-sm-4 col-form-label"
                                        style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('다른 결제일') : __('Another Payment Date') }}
                                        <span style="color: #FC565D;">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="date" class="form-control required-input"
                                            name="inputAnotherPaymentDate"
                                            style="background-color: #FFFFFF; color: #1E3142;"
                                            id="inputAnotherPaymentDate" placeholder="YYYY-MM-DD">
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

    <!-- car of navbar -->
    <div class="col-md-12">
        <div class="card mb-3" style="background-color: #1E3142; color: #FFFFFF;">
            <div class="nav-bar-custom">
                <div class="nav-item active" data-target="subscriptionInfo">
                    <div class="title">{{ config('app.lang') != 'en' ? __('가입정보') : __('Subscription Information') }}
                        <span style="color: #FC565D;">*</span>
                    </div>
                </div>
                <div class="nav-item" data-target="personInCharge">
                    <div class="title">{{ config('app.lang') != 'en' ? __('담당자 정보') : __('Person in Charge') }}
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
        <div class="card mb-3" style="background-color: #2c3e50; color: #FFFFFF;">
            <div class="card-body">
                <div class="col-md-12 d-flex">
                    <div class="card flex-fill d-flex flex-column" style="background-color: #324D65; color: #FFFFFF;">
                        <div class="card-header w-100 text-left" style="background-color: #869CAF; color: #FFFFFF;">
                            <h5 class="title">
                                {{ config('app.lang') != 'en' ? __('가입정보') : __('Signup Information') }}
                            </h5>
                        </div>
                        <div
                            class="card-body-custom-1 flex-fill d-flex flex-column justify-content-rigth align-items-right w-100">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row mb-3 w-100 justify-content-center">
                                        <label for="membershipDate" class="col-sm-4 col-form-label"
                                            style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('회원가입일') : __('Membership Date') }}</label>
                                        <div class="col-sm-8">
                                            <input type="date" class="form-control" name="membershipDate"
                                                style="background-color: #1E3142; color: #FFFFFF;" id="membershipDate"
                                                placeholder="YYYY-MM-DD" value="{{date('Y-m-d')}}" readonly>
                                        </div>
                                    </div>
                                    <div class="row mb-3 w-100 justify-content-center">
                                        <label for="serviceStartDate" class="col-sm-4 col-form-label"
                                            style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('서비스시작일') : __('Service Start Date') }}</label>
                                        <div class="col-sm-8">
                                            <input type="date" class="form-control" name="serviceStartDate"
                                                style="background-color: #1E3142; color: #FFFFFF;" id="serviceStartDate"
                                                placeholder="YYYY-MM-DD" readonly>
                                        </div>
                                    </div>
                                    <div class="row mb-3 w-100 justify-content-center">
                                        <label for="expirationDate" class="col-sm-4 col-form-label"
                                            style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('서비스만료일') : __('Service Expiration Date') }}</label>
                                        <div class="col-sm-8">
                                            <input type="date" class="form-control" name="expirationDate"
                                                style="background-color: #1E3142; color: #FFFFFF;" id="expirationDate"
                                                placeholder="YYYY-MM-DD" readonly>
                                        </div>
                                    </div>
                                    <div class="row mb-3 w-100 justify-content-center">
                                        <label for="userId" class="col-sm-4 col-form-label"
                                            style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('아이디') : __('User ID') }}
                                            <span style="color: #FC565D;">*</span></label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control required-input" name="userId"
                                                style="background-color: #FFFFFF; color: #1E3142;" id="userId"
                                                placeholder="{{ config('app.lang') != 'en' ? __('flywork') : __('flywork') }}">
                                        </div>
                                    </div>
                                    <div class="row mb-3 w-100 justify-content-center">
                                        <label for="password" class="col-sm-4 col-form-label"
                                            style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('비밀번호') : __('Password') }}
                                            <span style="color: #FC565D;">*</span></label>
                                        <div class="col-sm-8">
                                            <input type="password" class="form-control required-input" name="password"
                                                style="background-color: #FFFFFF; color: #1E3142;" id="password"
                                                placeholder="{{ config('app.lang') != 'en' ? __('8-20자리의 숫자, 영문각각 1개 이상 포함') : __('8-20 characters with at least 1 number and 1 letter') }}">
                                        </div>
                                    </div>
                                    <div class="row mb-3 w-100 justify-content-center">
                                        <label for="companyName" class="col-sm-4 col-form-label"
                                            style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('업체기관명') : __('Company Name') }}
                                            <span style="color: #FC565D;">*</span></label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control required-input" name="companyName"
                                                style="background-color: #FFFFFF; color: #1E3142;" id="companyName"
                                                placeholder="{{ config('app.lang') != 'en' ? __('가나다서울병원') : __('Ganada Seoul Hospital') }}">
                                        </div>
                                    </div>
                                    <div class="row mb-3 w-100 justify-content-center">
                                        <label for="address" class="col-sm-4 col-form-label"
                                            style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('주소') : __('Address') }}
                                            <span style="color: #FC565D;">*</span></label>
                                        <div class="col-sm-8">
                                            <div class="input-group">
                                                <input type="text" class="form-control required-input"
                                                    name="postalCodeAddress"
                                                    style="background-color: #1E3142; color: #FFFFFF;"
                                                    id="postalCodeAddress" readonly>
                                                <div class="input-group">
                                                    <button class="btn btn-info"
                                                        onclick="execDaumPostcode()">{{ config('app.lang') != 'en' ? __('우편번호 찾기') : __('Find Postal Code') }}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3 w-100 justify-content-center">
                                        <div class="col-sm-8 offset-sm-4">
                                            <textarea class="styled-textarea required-input" name="address" id="address"
                                                style="background-color: #1E3142; color: #FFFFFF;" readonly></textarea>

                                        </div>
                                    </div>
                                    <div class="row mb-3 w-100 justify-content-center">
                                        <div class="col-sm-8 offset-sm-4">
                                            <input type="text" class="form-control required-input" name="fullAddress"
                                                style="background-color: #FFFFFF; color: #1E3142;" id="fullAddress">
                                        </div>
                                    </div>
                                    <div class="row mb-3 w-100 justify-content-center">
                                        <label for="representativeName" class="col-sm-4 col-form-label"
                                            style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('대표자명') : __('Representative Name') }}
                                            <span style="color: #FC565D;">*</span></label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control required-input"
                                                name="representativeName"
                                                style="background-color: #FFFFFF; color: #1E3142;"
                                                id="representativeName"
                                                placeholder="{{ config('app.lang') != 'en' ? __('김길동') : __('Kim Gildong') }}">
                                        </div>
                                    </div>
                                    <div class="row mb-3 w-100 justify-content-center">
                                        <label for="workers" class="col-sm-4 col-form-label"
                                            style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('근로자수(명)') : __('Number of Workers') }}
                                            <span style="color: #FC565D;">*</span></label>
                                        <div class="col-sm-8">
                                            <input type="number"
                                                class="form-control form-control-custom numeric-input required-input"
                                                name="workers" style="background-color: #FFFFFF; color: #1E3142;"
                                                id="workers"
                                                placeholder="{{ config('app.lang') != 'en' ? __('150') : __('150') }}"
                                                onchange="handleWorkersChange()" oninput="handleWorkersChange()">
                                        </div>
                                    </div>
                                    <div class="row mb-3 w-100 justify-content-center">
                                        <label for="homepage" class="col-sm-4 col-form-label"
                                            style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('홈페이지') : __('Homepage') }}
                                            <span style="color: #FC565D;">*</span></label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control required-input" name="homepage"
                                                style="background-color: #FFFFFF; color: #1E3142;" id="homepage"
                                                placeholder="{{ config('app.lang') != 'en' ? __('www.flywork.co.kr') : __('www.flywork.co.kr') }}">
                                        </div>
                                    </div>
                                    <div class="row mb-3 w-100 justify-content-center">
                                        <label for="hrUse" class="col-sm-4 col-form-label"
                                            style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('인력관리 사용유무') : __('HR Management Usage') }}
                                            <span style="color: #FC565D;">*</span></label>
                                        <div class="col-sm-8">
                                            <select class="form-control required-input" name="hrUse"
                                                style="background-color: #869CAF; color: #FFFFFF;" id="hrUse">
                                                <option>{{ config('app.lang') != 'en' ? __('미사용') : __('Do not use') }}
                                                </option>
                                                <option>{{ config('app.lang') != 'en' ? __('사용') : __('Use') }}
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
                                                name="faxNumber" style="background-color: #FFFFFF; color: #1E3142;"
                                                id="faxNumber"
                                                placeholder="{{ config('app.lang') != 'en' ? __('023334568') : __('023334568') }}">
                                        </div>
                                    </div>
                                    <div class="row mb-3 w-100 justify-content-center">
                                        <label for="fieldOfHelp" class="col-sm-4 col-form-label"
                                            style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('도움분야') : __('Field of Help') }}
                                            <span style="color: #FC565D;">*</span></label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control numeric-input required-input"
                                                name="fieldOfHelp" style="background-color: #869CAF; color: #FFFFFF;"
                                                placeholder="{{ config('app.lang') != 'en' ? '선택 (0)' : 'Select (0)' }}"
                                                id="fieldOfHelp" readonly>
                                        </div>
                                    </div>
                                    <div class="row mb-3 w-100 justify-content-center">
                                        <label for="selectedSubjects" class="col-sm-4 col-form-label"
                                            style="color: #FFFFFF;"></label>
                                        <div class="col-sm-8">
                                            <textarea class="styled-textarea required-input" name="selectedSubjects"
                                                id="selectedSubjects" style="background-color: #1E3142; color: #FFFFFF;"
                                                readonly></textarea>
                                            <!-- <input type="text" class="form-control" name="selectedSubjects"
                                                style="background-color: #FFFFFF; color: #1E3142;" id="selectedSubjects"
                                                placeholder="" readonly> -->
                                        </div>
                                    </div>
                                    <div class="row mb-3 w-100 justify-content-center">
                                        <label for="availableSubjects" class="col-sm-4 col-form-label"
                                            style="color: #FFFFFF;"></label>
                                        <div class="col-sm-8">
                                            <div class="d-flex flex-wrap">
                                                @foreach ($subjects as $subject)
                                                <button type="button" class="btn btn-info m-1"
                                                    onclick="selectSubject(this)">{{ $subject }}</button>
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
                                            style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('담당자명') : __('Person in Charge Name') }}
                                            <span style="color: #FC565D;">*</span></label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control required-input" name="name" id="name"
                                                placeholder="{{ config('app.lang') != 'en' ? __('김길동') : __('Kim Gildong') }}">
                                        </div>
                                    </div>
                                    <div class="row mb-3 w-100 justify-content-center">
                                        <label for="position" class="col-sm-4 col-form-label"
                                            style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('직위') : __('Position') }}
                                            <span style="color: #FC565D;">*</span></label>
                                        <div class="col-sm-8">
                                            <select class="form-control required-input" name="position"
                                                style="background-color: #869CAF; color: #FFFFFF;" id="position">
                                                <option>{{ config('app.lang') != 'en' ? __('과장') : __('Manager') }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-3 w-100 justify-content-center">
                                        <label for="jobTitle" class="col-sm-4 col-form-label"
                                            style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('직책') : __('Job Title') }}
                                            <span style="color: #FC565D;">*</span></label>
                                        <div class="col-sm-8">
                                            <select class="form-control required-input" name="jobTitle"
                                                style="background-color: #869CAF; color: #FFFFFF;" id="jobTitle">
                                                <option>{{ config('app.lang') != 'en' ? __('팀장') : __('Team Leader') }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-3 w-100 justify-content-center">
                                        <label for="department" class="col-sm-4 col-form-label"
                                            style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('소속부서') : __('Department') }}
                                            <span style="color: #FC565D;">*</span></label>
                                        <div class="col-sm-8">
                                            <select class="form-control required-input" name="department"
                                                style="background-color: #869CAF; color: #FFFFFF;" id="department">
                                                <option>
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
                                            <input type="text" class="form-control required-input" name="officePhone"
                                                id="officePhone"
                                                placeholder="{{ config('app.lang') != 'en' ? __('023334567') : __('023334567') }}">
                                        </div>
                                    </div>
                                    <div class="row mb-3 w-100 justify-content-center">
                                        <label for="mobilePhone" class="col-sm-4 col-form-label"
                                            style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('휴대폰번호') : __('Mobile Phone') }}
                                            <span style="color: #FC565D;">*</span></label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control required-input" name="mobilePhone"
                                                id="mobilePhone"
                                                placeholder="{{ config('app.lang') != 'en' ? __('01012345678') : __('01012345678') }}">
                                        </div>
                                    </div>
                                    <div class="row mb-3 w-100 justify-content-center">
                                        <div class="col-sm-8 offset-sm-4">
                                            <div class="form-check text-left">
                                                <label class="form-check-label">
                                                    <input class="form-check-input" type="checkbox" name="smsConsent"
                                                        id="smsConsent" onchange="handleSMSChange(event)">
                                                    <span class="form-check-sign"></span>
                                                    {{ config('app.lang') != 'en' ? __('SMS 수신에 동의합니다.') : __('Agree to receive SMS') }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3 w-100 justify-content-center">
                                        <label for="email" class="col-sm-4 col-form-label"
                                            style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('이메일') : __('Email') }}
                                            <span style="color: #FC565D;">*</span></label>
                                        <div class="col-sm-8">
                                            <input type="email" class="form-control required-input" name="email"
                                                id="email"
                                                placeholder="{{ config('app.lang') != 'en' ? __('flywork@flywork.co.kr') : __('flywork@flywork.co.kr') }}">
                                        </div>
                                    </div>
                                    <div class="row mb-3 w-100 justify-content-center">
                                        <div class="col-sm-8 offset-sm-4">
                                            <div class="form-check text-left">
                                                <label class="form-check-label">
                                                    <input class="form-check-input required-input" type="checkbox"
                                                        name="emailConsent" id="emailConsent"
                                                        onchange="handleEmailChange(event)">
                                                    <span class="form-check-sign"></span>
                                                    {{ config('app.lang') != 'en' ? __('이메일 수신에 동의합니다.') : __('Agree to receive email') }}
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
                                            <input type="text" class="form-control required-input" name="businessNumber"
                                                id="businessNumber"
                                                placeholder="{{ config('app.lang') != 'en' ? __('1234567890') : __('1234567890') }}">
                                        </div>
                                    </div>
                                    <div class="row mb-3 w-100 justify-content-center">
                                        <label for="corporateNumber" class="col-sm-4 col-form-label"
                                            style="color: #ecf0f1;">{{ config('app.lang') != 'en' ? __('법인번호') : __('Corporate Number') }}
                                            <span style="color: #FC565D;">*</span></label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control required-input"
                                                name="corporateNumber" id="corporateNumber"
                                                placeholder="{{ config('app.lang') != 'en' ? __('1234567890') : __('1234567890') }}">
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
                                            <input type="text" class="form-control required-input" name="typeOfBusiness"
                                                id="typeOfBusiness"
                                                placeholder="{{ config('app.lang') != 'en' ? __('의료서비스') : __('Medical Service') }}">
                                        </div>
                                    </div>
                                    <div class="row mb-3 w-100 justify-content-center">
                                        <label for="businessType" class="col-sm-4 col-form-label"
                                            style="color: #ecf0f1;">{{ config('app.lang') != 'en' ? __('업태') : __('Business Type') }}
                                            <span style="color: #FC565D;">*</span></label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control required-input" name="businessType"
                                                id="businessType"
                                                placeholder="{{ config('app.lang') != 'en' ? __('의료서비스공급') : __('Medical Service Supply') }}">
                                        </div>
                                    </div>
                                    <div class="row mb-3 w-100 justify-content-center">
                                        <label for="emailReceivingInvoice" class="col-sm-4 col-form-label"
                                            style="color: #ecf0f1;">{{ config('app.lang') != 'en' ? __('세금계산서 수신이메일') : __('Invoice Email') }}
                                            <span style="color: #FC565D;">*</span></label>
                                        <div class="col-sm-8">
                                            <input type="email" class="form-control required-input"
                                                name="emailReceivingInvoice" id="emailReceivingInvoice"
                                                placeholder="{{ config('app.lang') != 'en' ? __('flywork@flywork.co.kr') : __('flywork@flywork.co.kr') }}">
                                        </div>
                                    </div>
                                    <div class="row mb-3 w-100 justify-content-center">
                                        <div class="col-sm-8 offset-sm-4">
                                            <div class="form-check text-left">
                                                <label class="form-check-label">
                                                    <input class="form-check-input required-input" type="checkbox"
                                                        name="emailConsent" id="emailConsent">
                                                    <span class="form-check-sign"></span>
                                                    {{ config('app.lang') != 'en' ? __('이메일 수신에 동의합니다.') : __('Agree to receive email') }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3 w-100 justify-content-center">
                                        <label for="dataDocumentOutput" class="col-sm-4 col-form-label"
                                            style="color: #ecf0f1;">{{ config('app.lang') != 'en' ? __('데이터 문서출력 (PDF)') : __('Data Document Output (PDF)') }}
                                        </label>
                                        <div class="col-sm-8">
                                            <select class="form-control" name="dataDocumentOutput"
                                                id="dataDocumentOutput" onchange="handleDataOutputPdfSelected(event)">
                                                <option>{{ config('app.lang') != 'en' ? __('미사용') : __('Not Use') }}
                                                </option>
                                                <option>{{ config('app.lang') != 'en' ? __('사용') : __('Use') }}
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
                                            style="color: #ecf0f1;">{{ config('app.lang') != 'en' ? __('가입서비스') : __('Subscription Service') }}
                                            <span style="color: #FC565D;">*</span></label>
                                        <div class="col-sm-8">
                                            <select class="form-control required-input" name="subscriptionService"
                                                style="background-color: #869CAF; color: #FFFFFF;"
                                                id="subscriptionService"
                                                onchange="handleSubscriptionServiceSelected(event)">
                                                @foreach ($serviceUsageSettings as $serviceUsageSetting)
                                                <option value="{{ $serviceUsageSetting->service_classification}}">
                                                    {{ $serviceUsageSetting->service_classification_desc }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-3 w-100">
                                        <label for="startDate" class="col-sm-4 col-form-label"
                                            style="color: #ecf0f1;">{{ config('app.lang') != 'en' ? __('사용시작일') : __('Start Date') }}
                                            <span style="color: #FC565D;">*</span></label>
                                        <div class="col-sm-8">
                                            <div class="input-group">
                                                <input type="date" class="form-control required-input" name="startDate"
                                                    id="startDate" onchange="handleChangeStartDate()"
                                                    placeholder="YYYY-MM-DD">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3 w-100">
                                        <label for="endDate" class="col-sm-4 col-form-label"
                                            style="color: #ecf0f1;">{{ config('app.lang') != 'en' ? __('사용만료일') : __('Expiration Date') }}</label>
                                        <div class="col-sm-8">
                                            <div class="input-group">
                                                <input type="date" class="form-control" name="endDate" id="endDate"
                                                    style="background-color: #1E3142; color: #FFFFFF;"
                                                    placeholder="YYYY-MM-DD" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row mb-3 w-100">
                                        <label for="userCount" class="col-sm-4 col-form-label"
                                            style="color: #ecf0f1;">{{ config('app.lang') != 'en' ? __('서비스 사용자수(명)') : __('Number of Users') }}</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-custom numeric-input"
                                                name="userCount" id="userCount"
                                                style="background-color: #1E3142; color: #FFFFFF;"
                                                placeholder="{{ config('app.lang') != 'en' ? __('100~150') : __('100~150') }}"
                                                value="{{$serviceUsageSettings[0]->number_of_user_from . '-' . $serviceUsageSettings[0]->number_of_user_to}}"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="row mb-3 w-100">
                                        <label for="usagePeriod" class="col-sm-4 col-form-label"
                                            style="color: #ecf0f1;">{{ config('app.lang') != 'en' ? __('사용기간') : __('Usage Period') }}
                                            <span style="color: #FC565D;">*</span></label>
                                        <div class="col-sm-8">
                                            <select class="form-control required-input" name="usagePeriod"
                                                style="background-color: #869CAF; color: #FFFFFF;" id="usagePeriod"
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
                                    <div class="row mb-3 w-100">
                                        <label for="serviceFee" class="col-sm-4 col-form-label"
                                            style="color: #ecf0f1;">{{ config('app.lang') != 'en' ? __('서비스 이용료(부가세별도)') : __('Service Fee (excluding VAT)') }}</label>
                                        <div class="col-sm-8">
                                            <input type="number" class="form-control form-control-custom numeric-input"
                                                name="serviceFee" step="0.01" id="serviceFee"
                                                style="background-color: #1E3142; color: #FFFFFF;"
                                                placeholder="{{ config('app.lang') != 'en' ? __('5,000,000원') : __('5,000,000 won') }}"
                                                value="{{$serviceUsageSettings[0]->fees_used_year}}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <!-- <div class="col-sm-12">
                                    <button class="btn btn-primary w-100" data-toggle="modal"
                                        data-target="#subscriptionModal">{{ config('app.lang') != 'en' ? __('사용기간 연장') : __('Extend Usage Period') }}</button>
                                </div> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--  -->

    <!-- modal service subscription -->
    <!-- <div class="modal fade" id="subscriptionModal">
        <div class="modal-dialog modal-lg" style="max-width:500px;margin-top: -50px;">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title" style="color:#FFFFFF">
                        {{ config('app.lang') != 'en' ? __('사용기간 연장') : __('Extend Usage Period') }}
                    </h4>
                </div>

                <div class="modal-body">
                    <div class="row mb-3">
                        <label for="serviceType" class="col-sm-4 col-form-label"
                            style="color:#FFFFFF">{{ config('app.lang') != 'en' ? __('연장 가입서비스') : __('Extended Subscription Service') }}</label>
                        <div class="col-sm-8">
                            <select class="form-control" name="serviceType">
                                <option>{{ config('app.lang') != 'en' ? __('베이직') : __('Basic') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="usagePeriod" class="col-sm-4 col-form-label"
                            style="color:#FFFFFF">{{ config('app.lang') != 'en' ? __('연장 사용기간') : __('Extended Usage Period') }}</label>
                        <div class="col-sm-8">
                            <select class="form-control" name="usagePeriod">
                                <option>{{ config('app.lang') != 'en' ? __('12개월') : __('12 months') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="startDate" class="col-sm-4 col-form-label"
                            style="color:#FFFFFF">{{ config('app.lang') != 'en' ? __('연장 사용시작일') : __('Extended Start Date') }}</label>
                        <div class="col-sm-8">
                            <input type="date" class="form-control" name="startDate" placeholder="YYYY-MM-DD">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="endDate" class="col-sm-4 col-form-label"
                            style="color:#FFFFFF">{{ config('app.lang') != 'en' ? __('연장 사용만료일') : __('Extended Expiration Date') }}</label>
                        <div class="col-sm-8">
                            <input type="date" class="form-control" name="endDate" placeholder="YYYY-MM-DD">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="serviceFee" class="col-sm-4 col-form-label"
                            style="color:#FFFFFF">{{ config('app.lang') != 'en' ? __('서비스 이용료 (부가세별도)') : __('Service Fee (excluding VAT)') }}</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control form-control-custom numeric-input" name="serviceFee"
                                step="0.01"
                                placeholder="{{ config('app.lang') != 'en' ? __('5,000,000원') : __('5,000,000 won') }}">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="userCount" class="col-sm-4 col-form-label"
                            style="color:#FFFFFF">{{ config('app.lang') != 'en' ? __('서비스 사용자수(명)') : __('Number of Users') }}</label>
                        <div class="col-sm-8">
                            <input type="number" class="form-control form-control-custom numeric-input" name="userCount"
                                placeholder="{{ config('app.lang') != 'en' ? __('100~150') : __('100~150') }}">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-primary"
                        data-dismiss="modal">{{ config('app.lang') != 'en' ? __('저장') : __('Save') }}</button>
                    <button type="button" class="btn btn-info"
                        data-dismiss="modal">{{ config('app.lang') != 'en' ? __('취소') : __('Cancel') }}</button>
                </div>

            </div>
        </div>
    </div> -->
    <!--  -->
</div>
<script>
function handleChangeStartDate() {
    const startDate = document.getElementById('startDate').value;
    const expiryDate = calculateExpiryDate(startDate, usagePeriod);
    document.getElementById('endDate').value = expiryDate;
    document.getElementById('expirationDate').value = expiryDate;
    document.getElementById('serviceStartDate').value = startDate;
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

    document.getElementById('userCount').value =
        `${serviceUsageSetting[0].number_of_user_from}-${serviceUsageSetting[0].number_of_user_to}`;
    document.getElementById('inputServiceOfUse').value = serviceUsageSetting[0].service_classification_desc
    calculateServiceFee();
    resetFields();
}

function calculateServiceFee() {
    if (isMultipleOf12(usagePeriod)) {
        document.getElementById('serviceFee').value = serviceClassificationYearly * (usagePeriod / 12)
    } else {
        document.getElementById('serviceFee').value = serviceClassificationMonthly * usagePeriod
    }
}

function resetFields() {
    const startDate = document.getElementsByName('startDate');
    for (var i = 0; i < startDate.length; i++) {
        startDate[i].value = '';
    }
    const endDate = document.getElementsByName('endDate');
    for (var i = 0; i < endDate.length; i++) {
        endDate[i].value = '';
    }
    document.getElementById('expirationDate').value = '';
    document.getElementById('serviceStartDate').value = '';
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

function save() {
    if (!globalValidateInput()) {
        document.getElementById('error-message-pop-up').innerText =
            `{{ config('app.lang') != 'en' ? __('필수 입력란을 모두 작성해 주세요.') : __('Please fill out all required fields.') }}`
        $('#errorFailedWithMessagePopupModal').modal('show');
        return
    }

    const formData = new FormData();

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
        formData.append('subscription_information[membership_registration_date]',
            membershipDateElement.value);
    }
    const startDateElement = document.getElementById('serviceStartDate');
    if (startDateElement && startDateElement.value) {
        formData.append('subscription_information[service_start_date]', startDateElement.value);
    }
    const expirationDateElement = document.getElementById('expirationDate');
    if (expirationDateElement && expirationDateElement.value) {
        formData.append('subscription_information[service_expiration_date]', expirationDateElement
            .value);
    }

    const userIdElement = document.getElementById('userId');
    if (userIdElement && userIdElement.value) {
        formData.append('user_id', userIdElement.value);
    }
    const passwordElement = document.getElementById('password');
    if (passwordElement && passwordElement.value) {
        const checkvalidatePassword = validatePassword(passwordElement.value, '', false);
        if (checkvalidatePassword !== true) {
            return
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
        formData.append('subscription_information[name_of_representative]',
            representativeNameElement.value);
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

    // fieldOfHelpElement
    const label = document.getElementById('fieldOfHelp');
    if (label) {
        formData.append('field_of_help', label.value);
    }
    const selectedSubjects = document.getElementById('selectedSubjects');
    const fieldOfHelpElement = selectedSubjects.value ? selectedSubjects.value.split(' | ') : [];
    if (fieldOfHelpElement) {
        formData.append('subscription_information[fieldOfHelp]', fieldOfHelpElement);
        formData.append('selected_subjects', selectedSubjects.value);
    }
    //

    const nameElement = document.getElementById('name');
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
        formData.append('person_in_charge_information[cell_phone_number]', mobilePhoneElement
            .value);
    }
    const emailElement = document.getElementById('email');
    if (emailElement && emailElement.value) {
        formData.append('person_in_charge_information[email]', emailElement.value);
    }
    const emailConsentElement = document.getElementById('emailConsent');
    if (emailConsentElement) formData.append('person_in_charge_information[receive_email]',
        emailConsentElement.checked);
    const smsConsentElement = document.getElementById('smsConsent');
    if (smsConsentElement) formData.append('person_in_charge_information[receive_sms]',
        smsConsentElement.checked);

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
        formData.append('additional_information[email_receiving_tax_invoice]',
            emailReceivingInvoiceElement.value);
    }
    if (emailConsentElement) formData.append('additional_information[receive_email]',
        emailConsentElement.checked);
    const dataDocumentOutputElement = document.getElementById('dataDocumentOutput');
    if (dataDocumentOutputElement && dataDocumentOutputElement.value) {
        formData.append('additional_information[data_document_output]', dataDocumentOutputElement
            .value);
    }

    const subscriptionServiceElement = document.getElementById('subscriptionService');
    if (subscriptionServiceElement && subscriptionServiceElement.value) {
        formData.append('service_subscription_information[subscription_service]',
            subscriptionServiceElement.value);
    }
    const startDateElement2 = document.getElementById('startDate');
    if (startDateElement2 && startDateElement2.value) {
        formData.append('service_subscription_information[start_date_of_use]', startDateElement2
            .value);
    }
    const endDateElement = document.getElementById('endDate');
    if (endDateElement && endDateElement.value) {
        formData.append('service_subscription_information[expiration_date]', endDateElement.value);
    }
    const userCountElement = document.getElementById('userCount');
    if (userCountElement && userCountElement.value) {
        formData.append('service_subscription_information[number_of_service_users]',
            userCountElement.value);
    }
    const usagePeriodElement = document.getElementById('usagePeriod');
    if (usagePeriodElement && usagePeriodElement.value) {
        formData.append('service_subscription_information[usage_period_months]', usagePeriodElement
            .value);
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

    fetch(`{{route('user.store') }}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                $('#successUserPopupModal').modal('show');
            } else {
                if (data.message) {
                    document.getElementById('error-message-pop-up').innerText = data.message
                    $('#errorFailedWithMessagePopupModal').modal('show');
                } else {
                    $('#errorFailedPopupModal').modal('show');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            $('#errorFailedPopupModal').modal('show');
        });
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

function loadFile(event) {
    const output = document.getElementById('profileImage');
    output.src = URL.createObjectURL(event.target.files[0]);
    output.onload = function() {
        URL.revokeObjectURL(output.src) // free memory
    }
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

function redirectToUserList(url) {
    window.location.href = url;
}

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

    allFiles.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
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
</script>
@endsection