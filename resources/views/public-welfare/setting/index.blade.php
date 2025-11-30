@php
$pageTitle = config('app.lang') != 'en' ? __('대시보드') : __('Setting');
@endphp

@extends('layouts.app', ['page' => $pageTitle, 'pageSlug' => 'setting'])

@section('content-fluid')
<div class="setting-public-welfare-container">
    <div class="setting-public-welfare-navbar">
        <div class="{{ $menu == 'smartwatch' ? 'setting-public-welfare-navbar-item setting-public-welfare-navbar-item-active' : 'setting-public-welfare-navbar-item' }}"
            onclick="switchNav('smart-watch')">
            {{ config('app.lang') != 'en' ? __('스마트워치') : __('Smartwatch') }}
        </div>
        <div class="{{ $menu == 'alarm-setting' ? 'setting-public-welfare-navbar-item setting-public-welfare-navbar-item-active' : 'setting-public-welfare-navbar-item' }}"
            onclick="switchNav('alarm-settings')">
            {{ config('app.lang') != 'en' ? __('알람설정') : __('Alarm Settings') }}
        </div>
        <div class="{{ $menu == 'ward' ? 'setting-public-welfare-navbar-item setting-public-welfare-navbar-item-active' : 'setting-public-welfare-navbar-item' }}"
            onclick="switchNav('ward')">
            {{ config('app.lang') != 'en' ? __('병원') : __('Ward') }}
        </div>
        <div class="{{ $menu == 'staff-management' ? 'setting-public-welfare-navbar-item setting-public-welfare-navbar-item-active' : 'setting-public-welfare-navbar-item' }}"
            onclick="switchNav('staff-management')">
            {{ config('app.lang') != 'en' ? __('직원 관리') : __('Staff Management') }}
        </div>
        <div class="{{ $menu == 'admin-management' ? 'setting-public-welfare-navbar-item setting-public-welfare-navbar-item-active' : 'setting-public-welfare-navbar-item' }}"
            onclick="switchNav('admin-management')">
            {{ config('app.lang') != 'en' ? __('관리자관리') : __('Admin Management') }}
        </div>
        <div class="{{ $menu == 'user-management' ? 'setting-public-welfare-navbar-item setting-public-welfare-navbar-item-active' : 'setting-public-welfare-navbar-item' }}"
            onclick="switchNav('user-management')">
            {{ config('app.lang') != 'en' ? __('사용자관리') : __('User Management') }}
        </div>
    </div>

    @if($menu == 'smartwatch')
    <div class="setting-public-welfare-content" id="smart-watch" style="width:100%; display:block;">
        @include('public-welfare.setting.smart-watch-management')
    </div>
    @elseif ($menu == 'alarm-setting')
    <div class="setting-public-welfare-content" id="alarm-settings" style="display: block;">
        @include('public-welfare.setting.alarm-settings')
    </div>
    @elseif ($menu == 'ward')
    <div class="setting-public-welfare-content" id="ward" style="width:100%; display:block;">
        @include('public-welfare.setting.ward')
    </div>
    @elseif ($menu == 'staff-management')
    <div class="setting-public-welfare-content" id="staff-management" style="width:100%; display:block;">
        @include('public-welfare.setting.staff-management')
    </div>
    @elseif ($menu == 'admin-management')
    <div class="setting-public-welfare-content" id="admin-management" style="width:100%; display:block;">
        @include('public-welfare.setting.admin-management')
    </div>
    @elseif ($menu == 'user-management')
    <div class="setting-public-welfare-content" id="user-management" style="width:100%; display:block;">
        @include('public-welfare.setting.user-management')
    </div>
    @endif
</div>

<script>
const resizer = document.getElementById('resizer');
const leftCard = document.getElementById('left-card');
const rightCard = document.getElementById('right-card');

let isResizing = false;

resizer.addEventListener('mousedown', function(e) {
    isResizing = true;
    document.addEventListener('mousemove', onMouseMove);
    document.addEventListener('mouseup', onMouseUp);
});

function onMouseMove(e) {
    if (!isResizing) return;
    const offsetRight = document.body.offsetWidth - (e.clientX);
    leftCard.style.flexGrow = '0';
    leftCard.style.width = e.clientX + 'px';
    rightCard.style.flexGrow = '0';
    rightCard.style.width = offsetRight + 'px';
}

function onMouseUp() {
    isResizing = false;
    document.removeEventListener('mousemove', onMouseMove);
    document.removeEventListener('mouseup', onMouseUp);
}

function switchNav(contentId) {
    let newPath = '/public-welfare/setting/';
    switch (contentId) {
        case 'smart-watch':
            newPath += 'smart-watch';
            break;
        case 'alarm-settings':
            newPath += 'alarm';
            break;
        case 'ward':
            newPath += 'ward';
            break;
        case 'staff-management':
            newPath += 'staff-management';
            break;
        case 'admin-management':
            newPath += 'admin-management';
            break;
        case 'user-management':
            newPath += 'user-management';
            break;
    }

    // Change the URL and send a request to the server
    window.location.href = newPath;
}
</script>
@endsection