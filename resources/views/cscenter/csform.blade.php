@extends('layouts.app', ['class' => 'login-page', 'page' => __(''), 'contentClass' => 'login-page'])

@section('content')
<div class="col-md-10 text-center ml-auto mr-auto">
</div>
<div class="col-lg-9 col-md-6 ml-auto mr-auto">
    <form class="form" id="loginForm" method="post" action="{{ route('general-purpose.login') }}">
        @csrf
        <div class="login-card" style="width: 780px;">
            <div><img src="{{ asset('black') }}/icons/general/logo.svg"></div>
            <div class="row mb-3 w-100 justify-content-start">
                <label for="userId" class="col-sm-2 col-form-label"
                    style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('이름') : __('Name') }}
                </label>
                <div class="col-sm-10">
                    <input type="text" class="form-control required-input" name="name"
                        style="background-color: #FFFFFF; color: #1E3142;" id="name" placeholder="이름을 입력하세요...">
                </div>
            </div>

            <div class="row mb-3 w-100 justify-content-start">
                <label for="userId" class="col-sm-2 col-form-label"
                    style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('이메일') : __('email') }}
                </label>
                <div class="col-sm-10">
                    <input type="text" class="form-control required-input" name="email"
                        style="background-color: #FFFFFF; color: #1E3142;" id="email" placeholder="이메일을 입력하세요...">
                </div>
            </div>


            <div class="row mb-3 w-100 justify-content-start">
                <label for="userId" class="col-sm-2 col-form-label"
                    style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('제목') : __('title') }}
                </label>
                <div class="col-sm-10">
                    <input type="text" class="form-control required-input" name="title"
                        style="background-color: #FFFFFF; color: #1E3142;" id="title" placeholder="제목을 입력하세요...">
                </div>
            </div>

            <div class="row mb-3 w-100 justify-content-start">
                <label for="userId" class="col-sm-2 col-form-label"
                    style="color: #FFFFFF;">{{ config('app.lang') != 'en' ? __('내용') : __('content') }}
                </label>
                <div class="col-sm-10">
                    <textarea class="styled-textarea required-input" name="content" id="content"
                        style="background-color: #FFFFFF; color: #1E3142;border-radius:0.25rem"
                        placeholder="내용을 입력하세요..."></textarea>
                </div>
            </div>

            <button type="button" class="btn btn-custom btn-block mb-3" onclick="submitCSForm()">제출하다</button>
        </div>
    </form>
</div>
<script>
    function submitCSForm() {
        const formData = new FormData();
        formData.append('name', document.getElementById('name').value);
        formData.append('email', document.getElementById('email').value);
        formData.append('title', document.getElementById('title').value);
        formData.append('content', document.getElementById('content').value);

        fetch(`{{route('cscenter.submitCSForm')}}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            },
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    $('#successPopupModal').modal('show');
                } else {
                    if (data.message) {
                        document.getElementById('error-message-pop-up').innerText = data.message
                        $('#errorFailedWithMessagePopupModal').modal('show');
                    } else {
                        $('#errorFailedPopupModal').modal('show');
                    }
                }
            })
            .catch(error => console.error('Error:', error));
    }
</script>
@endsection