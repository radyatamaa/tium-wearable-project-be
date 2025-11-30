@php
$pageTitle = config('app.lang') != 'en' ? __('대시보드') : __('Setting');
@endphp

@extends('layouts.app', ['page' => $pageTitle, 'pageSlug' => 'setting'])

@section('content-fluid')
<div class="setting-general-purpose-container">
    <div class="setting-general-purpose-navbar">
        <div class="{{ $menu == 'smartwatch' ? 'setting-general-purpose-navbar-item setting-general-purpose-navbar-item-active' : 'setting-general-purpose-navbar-item' }}"
            onclick="switchNav('smart-watch')">
            {{ config('app.lang') != 'en' ? __('스마트워치') : __('Smartwatch') }}
        </div>
        <div class="{{ $menu == 'alarm-setting' ? 'setting-general-purpose-navbar-item setting-general-purpose-navbar-item-active' : 'setting-general-purpose-navbar-item' }}"
            onclick="switchNav('alarm-settings')">
            {{ config('app.lang') != 'en' ? __('알람설정') : __('Alarm Settings') }}
        </div>
        <div class="{{ $menu == 'admin-management' ? 'setting-general-purpose-navbar-item setting-general-purpose-navbar-item-active' : 'setting-general-purpose-navbar-item' }}"
            onclick="switchNav('admin-management')">
            {{ config('app.lang') != 'en' ? __('관리자관리') : __('Admin Management') }}
        </div>
        <div class="{{ $menu == 'user-management' ? 'setting-general-purpose-navbar-item setting-general-purpose-navbar-item-active' : 'setting-general-purpose-navbar-item' }}"
            onclick="switchNav('user-management')">
            {{ config('app.lang') != 'en' ? __('사용자관리') : __('User Management') }}
        </div>
    </div>

    @if($menu == 'smartwatch')
    <div class="setting-general-purpose-content" id="smart-watch" style="width:100%; display:block;">
        @include('general-purpose.setting.smart-watch-management')
    </div>
    @elseif ($menu == 'alarm-setting')
    <div class="setting-general-purpose-content" id="alarm-settings" style="display: block;">
        @include('general-purpose.setting.alarm-settings')
    </div>
    @elseif ($menu == 'admin-management')
    <div class="setting-general-purpose-content" id="admin-management" style="width:100%; display:block;">
        @include('general-purpose.setting.admin-management')
    </div>
    @elseif ($menu == 'user-management')
    <div class="setting-general-purpose-content" id="user-management" style="width:100%; display:block;">
        @include('general-purpose.setting.user-management')
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
    let newPath = '/general-purpose/setting/';
    switch (contentId) {
        case 'smart-watch':
            newPath += 'smart-watch';
            break;
        case 'alarm-settings':
            newPath += 'alarm';
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