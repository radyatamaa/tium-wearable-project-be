@extends('layouts.app', ['class' => 'login-page', 'page' => __(''), 'contentClass' => 'login-page'])

@section('content')
<div class="col-md-10 text-center ml-auto mr-auto">
</div>
<div class="col-lg-4 col-md-6 ml-auto mr-auto">
    <form class="form" id="loginForm" method="post" action="{{ route('public-welfare.login') }}">
        @csrf
        <div class="login-card">
            <div><img src="{{ asset('black') }}/icons/general/logo.svg"></div>
            <div class="form-group">
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="tim-icons icon-single-02"></i></span>
                    </div>
                    <input type="text" class="form-control default" id="user_id" name="user_id" placeholder="아이디"
                        value="{{ old('user_id') }}">
                </div>
            </div>
            <div class="form-group">
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="tim-icons icon-lock-circle"></i></span>
                    </div>
                    <input type="password" class="form-control default" id="password" name="password"
                        placeholder="비밀번호">
                </div>
            </div>
            <button type="submit" class="btn btn-custom btn-block mb-3">로그인</button>
            <div class="form-group text-left mb-3">
                <input type="checkbox" class="form-check-custom-input" id="saveId">
                <label class="form-check-label" for="saveId">아이디 저장</label>
            </div>
        </div>
    </form>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const userIdInput = document.getElementById('user_id');
    const passwordInput = document.getElementById('password');
    const saveIdCheckbox = document.getElementById('saveId');

    // Load saved login info from localStorage
    if (localStorage.getItem('user_id')) {
        userIdInput.value = localStorage.getItem('user_id');
        saveIdCheckbox.checked = true;
    }

    loginForm.addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent form submission

        // Save login info to localStorage if the checkbox is checked
        if (saveIdCheckbox.checked) {
            localStorage.setItem('user_id', userIdInput.value);
        } else {
            localStorage.removeItem('user_id');
        }

        // Submit the form
        loginForm.submit();
    });

    // Show the error popup modal if there are validation errors
    @if(count($errors) > 0)
    $('#errorLoginPopupModal').modal('show');
    @endif
});
</script>
@endsection