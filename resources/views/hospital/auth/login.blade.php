@extends('layouts.app', ['class' => 'login-page', 'page' => __(''), 'contentClass' => 'login-page'])

@section('content')
<div class="col-md-10 text-center ml-auto mr-auto">
</div>
<div class="col-lg-4 col-md-6 ml-auto mr-auto">
    <form class="form" id="loginForm" method="post" action="{{ route('hospital.login') }}">
        @csrf
        <div class="login-card">
            <div class="logo">
                <img src="{{ asset('black') }}/icons/general/logo.svg">
            </div>
            <div class="login-hospital-navbar">
                <a href="#" class="login-hospital-medical-staff active" onclick="showTab('medical')">
                    {{ config('app.lang') != 'en' ? __('의료진 로그인') : __('Medical Staff Login') }}
                </a>
                <a href="#" class="login-hospital-admin-login" onclick="showTab('admin')">
                    {{ config('app.lang') != 'en' ? __('관리자 로그인') : __('Admin Login') }}
                </a>
            </div>
            <div id="medical" class="tab-content-login-hospital">
                <div class="form-group">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="tim-icons icon-single-02"></i></span>
                        </div>
                        <input type="text" class="form-control default" id="user_id" name="user_id"
                            placeholder="{{ config('app.lang') != 'en' ? __('사번') : __('User ID') }}"
                            value="{{ old('user_id') }}">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="tim-icons icon-lock-circle"></i></span>
                        </div>
                        <input type="password" class="form-control default" id="password" name="password"
                            placeholder="{{ config('app.lang') != 'en' ? __('비밀번호') : __('Password') }}">
                    </div>
                </div>
                <button type="submit" class="btn btn-custom btn-block mb-3">
                    {{ config('app.lang') != 'en' ? __('로그인') : __('Login') }}
                </button>
                <div class="form-group text-left mb-3">
                    <input type="checkbox" class="form-check-custom-input" id="saveId">
                    <label class="form-check-label" for="saveId">
                        {{ config('app.lang') != 'en' ? __('아이디 저장') : __('Remember ID') }}
                    </label>
                </div>
            </div>
            <div id="admin" class="tab-content-login-hospital" style="display:none;">
                <div class="form-group">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="tim-icons icon-single-02"></i></span>
                        </div>
                        <input type="text" class="form-control default" id="admin_user_id" name="admin_user_id"
                            placeholder="{{ config('app.lang') != 'en' ? __('관리자 아이디') : __('Admin ID') }}"
                            value="{{ old('admin_user_id') }}">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="tim-icons icon-lock-circle"></i></span>
                        </div>
                        <input type="password" class="form-control default" id="admin_password" name="admin_password"
                            placeholder="{{ config('app.lang') != 'en' ? __('관리자 비밀번호') : __('Admin Password') }}">
                    </div>
                </div>
                <button type="submit" class="btn btn-custom btn-block mb-3">
                    {{ config('app.lang') != 'en' ? __('로그인') : __('Login') }}
                </button>
                <div class="form-group text-left mb-3">
                    <input type="checkbox" class="form-check-custom-input" id="admin_saveId">
                    <label class="form-check-label" for="admin_saveId">
                        {{ config('app.lang') != 'en' ? __('아이디 저장') : __('Remember ID') }}
                    </label>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const loginForm = document.getElementById('loginForm');
        const userIdInput = document.getElementById('user_id');
        const passwordInput = document.getElementById('password');
        const saveIdCheckbox = document.getElementById('saveId');
        const adminUserIdInput = document.getElementById('admin_user_id');
        const adminPasswordInput = document.getElementById('admin_password');
        const adminSaveIdCheckbox = document.getElementById('admin_saveId');

        // Load saved login info from localStorage
        if (localStorage.getItem('user_id')) {
            userIdInput.value = localStorage.getItem('user_id');
            saveIdCheckbox.checked = true;
        }
        if (localStorage.getItem('admin_user_id')) {
            adminUserIdInput.value = localStorage.getItem('admin_user_id');
            adminSaveIdCheckbox.checked = true;
        }

        loginForm.addEventListener('submit', function (event) {
            event.preventDefault(); // Prevent form submission

            // Save login info to localStorage if the checkbox is checked
            if (saveIdCheckbox.checked) {
                localStorage.setItem('user_id', userIdInput.value);
            } else {
                localStorage.removeItem('user_id');
            }

            if (adminSaveIdCheckbox.checked) {
                localStorage.setItem('admin_user_id', adminUserIdInput.value);
            } else {
                localStorage.removeItem('admin_user_id');
            }

            // Submit the form
            loginForm.submit();
        });

        // Show the error popup modal if there are validation errors
        @if(count($errors) > 0)
            $('#errorLoginPopupModal').modal('show');
        @endif
    });

    function showTab(tab) {
        document.querySelectorAll('.tab-content-login-hospital').forEach(function (content) {
            content.style.display = 'none';
        });
        document.getElementById(tab).style.display = 'block';

        document.querySelectorAll('.login-hospital-navbar a').forEach(function (navItem) {
            navItem.classList.remove('active');
        });
        document.querySelector('.login-hospital-navbar a[onclick="showTab(\'' + tab + '\')"]').classList.add('active');
    }
</script>
@endsection