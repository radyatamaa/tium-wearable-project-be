<div class="custom-container">
    <div class="custom-card custom-left-card" id="left-card">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header card-header-primary d-flex justify-content-between"
                        style="--header-background-color: #1E3142;">
                        <div class="smartwatch-search-container">
                            <div class="smartwatch-search-top">
                                <form id="filter-form" method="GET" action="/public-welfare/setting/admin-management"
                                    class="d-flex justify-content-between w-100">
                                    <div class="smartwatch-search-input-container">
                                        <input type="text" class="smartwatch-search-input" id="searchInput"
                                            name="search"
                                            placeholder="{{ config('app.lang') != 'en' ? __('계정 정보를 관리할 수 있습니다.') : __('You can manage account information.') }}">
                                    </div>
                                    <button type="submit"
                                        class="smartwatch-search-button">{{ config('app.lang') != 'en' ? __('이름, 번호로 검색') : __('Search by Name ,Number') }}<i
                                            class="fas fa-search"></i></button>

                                </form>
                            </div>
                            <div class="smartwatch-search-bottom">
                                <button class="smartwatch-refresh-button" onclick="refreshPage()"><img
                                        src="{{ asset('black') }}/icons/general/refresh.svg"></button>
                                <button class="btn btn-danger" style="border-radius: 4px;padding: 10px 20px;"
                                    id="delete-device-btn"
                                    onclick="deleteSelected()">{{ config('app.lang') != 'en' ? __('삭제') : __('Elimination') }}</button>
                                <button class="btn btn-primary" style="border-radius: 4px;padding: 10px 20px;"
                                    id="register-admin-btn">{{ config('app.lang') != 'en' ? __('계정등록') : __('Register Account') }}</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="styled-table-public-welfare-container">
                            <table class="styled-table-public-welfare" style="border-spacing:-10px-15px">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>{{ config('app.lang') != 'en' ? __('아이디') : __('ID') }}</th>
                                        <th>{{ config('app.lang') != 'en' ? __('이름') : __('Name') }}</th>
                                        <th>{{ config('app.lang') != 'en' ? __('분류') : __('Classification') }}</th>
                                        <th>{{ config('app.lang') != 'en' ? __('진료과') : __('Department') }}</th>
                                        <th>{{ config('app.lang') != 'en' ? __('연락처') : __('Contact') }}</th>
                                        <th>{{ config('app.lang') != 'en' ? __('등록일') : __('Registration Date') }}</th>
                                        <th>{{ config('app.lang') != 'en' ? __('비밀번호변경') : __('Change Password') }}</th>
                                        <th class="text-right">{{ config('app.lang') != 'en' ? __('수정') : __('Edit') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($datas as $admin)
                                    @if($admin->user_id == '001')
                                    @continue
                                    @endif
                                    <tr data-id="{{ $admin->id }}">
                                        <td><input type="checkbox" class="checkbox-click" value="{{ $admin->id}}"></td>
                                        <td>{{ $admin->user_id }}</td>
                                        <td>{{ $admin->name }}</td>
                                        <td>{{ $admin->classification }}</td>
                                        <td>{{ $admin->department }}</td>
                                        <td>{{ $admin->contact }}</td>
                                        <td>{{ $admin->registration_date }}</td>
                                        <td class="td-actions">
                                            <button class="btn btn-warning btn-sm change-password-btn" type="button">
                                                {{ config('app.lang') != 'en' ? __('비밀번호변경') : __('Change Password') }}
                                            </button>
                                        </td>
                                        <td class="td-actions text-right">
                                            <button class="edit-button-box" type="button"
                                                onclick="edit({{ $admin->id }})"> <img
                                                    src="{{ asset('black') }}/icons/general/edit-box.svg"
                                                    alt="{{ config('app.lang') != 'en' ? __('수정') : __('Edit') }}"></button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-center">
                                {{ $datas->links('layouts.pagination') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="custom-resizer" id="resizer"></div>
    <div class="custom-card custom-right-card" id="right-card" style="display:none;">
        <h2 class="title" style="text-align:center;">
            {{ config('app.lang') != 'en' ? __('계정정보수정') : __('Edit Account Information') }}
        </h2>
        <div class="smartwatch-form-container">
            <div class="smartwatch-form-group">
                <label for="admin-id">{{ config('app.lang') != 'en' ? __('아이디') : __('ID') }}</label>
                <input type="text" id="admin-id" name="admin-id" value="" class="required-input">
            </div>
            <div class="smartwatch-form-group">
                <label for="admin-name">{{ config('app.lang') != 'en' ? __('이름') : __('Name') }}</label>
                <input type="text" id="admin-name" name="admin-name" value="" class="required-input">
            </div>
            <div class="smartwatch-form-group">
                <label
                    for="admin-classification">{{ config('app.lang') != 'en' ? __('분류') : __('Classification') }}</label>
                <select id="admin-classification" name="admin-classification" class="form-control required-input">
                    <option>{{ config('app.lang') != 'en' ? __('선택') : __('Select') }}</option>
                    @foreach($jobTitles as $jobTitle)
                    <option value="{{ $jobTitle }}">{{ $jobTitle }}</option>
                    @endforeach
                </select>
            </div>
            <div class="smartwatch-form-group">
                <label for="admin-department">{{ config('app.lang') != 'en' ? __('진료과') : __('Department') }}</label>
                <select id="admin-department" name="admin-department" class="form-control required-input">
                    <option>{{ config('app.lang') != 'en' ? __('선택') : __('Select') }}</option>
                    @foreach($departments as $department)
                    <option value="{{ $department }}">{{ $department }}</option>
                    @endforeach
                </select>
            </div>
            <div class="smartwatch-form-group">
                <label for="admin-contact">{{ config('app.lang') != 'en' ? __('연락처') : __('Contact') }}</label>
                <input type="text" id="admin-contact" name="admin-contact" value="" class="required-input">
            </div>
            <div class="smartwatch-form-actions">
                <button type="submit" id="save-btn"
                    class="smartwatch-btn smartwatch-btn-primary">{{ config('app.lang') != 'en' ? __('저장') : __('Save') }}</button>
                <button type="reset" onclick="closeForm('right-card')"
                    class="smartwatch-btn smartwatch-btn-secondary">{{ config('app.lang') != 'en' ? __('취소') : __('Cancel') }}</button>
            </div>
        </div>
    </div>
    <div class="custom-card custom-right-card" id="register-card" style="display:none;">
        <h2 class="title" style="text-align:center;">
            {{ config('app.lang') != 'en' ? __('계정등록') : __('Register Account') }}
        </h2>
        <div class="smartwatch-form-container">
            <div class="smartwatch-form-group">
                <label for="register-admin-id">{{ config('app.lang') != 'en' ? __('아이디') : __('ID') }}</label>
                <input type="text" id="register-admin-id" name="id" class="required-input">
            </div>
            <div class="smartwatch-form-group">
                <label for="register-admin-name">{{ config('app.lang') != 'en' ? __('이름') : __('Name') }}</label>
                <input type="text" id="register-admin-name" name="name" class="required-input">
            </div>
            <div class="smartwatch-form-group">
                <label
                    for="register-admin-classification">{{ config('app.lang') != 'en' ? __('분류') : __('Classification') }}</label>
                <select id="register-admin-classification" name="register-admin-classification" class="required-input">
                    <option>{{ config('app.lang') != 'en' ? __('선택') : __('Select') }}</option>
                    @foreach($jobTitles as $jobTitle)
                    <option value="{{ $jobTitle }}">{{ $jobTitle }}</option>
                    @endforeach
                </select>
            </div>
            <div class="smartwatch-form-group">
                <label
                    for="register-admin-department">{{ config('app.lang') != 'en' ? __('진료과') : __('Department') }}</label>
                <select id="register-admin-department" name="register-admin-department" class="required-input">
                    <option>{{ config('app.lang') != 'en' ? __('선택') : __('Select') }}</option>
                    @foreach($departments as $department)
                    <option value="{{ $department }}">{{ $department }}</option>
                    @endforeach
                </select>
            </div>
            <div class="smartwatch-form-group">
                <label for="register-admin-contact">{{ config('app.lang') != 'en' ? __('연락처') : __('Contact') }}</label>
                <input type="text" id="register-admin-contact" name="contact" class="required-input">
            </div>
            <div class="smartwatch-form-group">
                <label
                    for="register-admin-password">{{ config('app.lang') != 'en' ? __('비밀번호') : __('Password') }}</label>
                <input type="password" id="register-admin-password" name="contact" class="required-input">
            </div>
            <div class="smartwatch-form-actions">
                <button type="submit" class="smartwatch-btn smartwatch-btn-primary"
                    id="register-btn">{{ config('app.lang') != 'en' ? __('등록') : __('Register') }}</button>
                <button type="reset" onclick="closeForm('register-card')"
                    class="smartwatch-btn smartwatch-btn-secondary">{{ config('app.lang') != 'en' ? __('취소') : __('Cancel') }}</button>
            </div>
        </div>
    </div>

    <!-- modal change password-->
    <div class="modal-change-password" id="changePasswordModalAdmin" tabindex="-1" role="dialog">
        <div class="modal-change-password-modal-dialog" role="document">
            <div class="modal-change-password-modal-content">
                <div class="modal-change-password-modal-header">
                    <h5 class="modal-change-password-modal-title">
                        {{ config('app.lang') != 'en' ? __('비밀번호변경') : __('Change Password') }}
                    </h5>
                </div>
                <div class="modal-change-password-modal-body">
                    <form id="changePasswordFormAdmin">
                        @csrf
                        <div class="smartwatch-form-group">
                            <label
                                for="new_password_admin">{{ config('app.lang') != 'en' ? __('비밀번호') : __('Password') }}</label>
                            <input type="new_password_admin" class="form-control" id="new_password_admin"
                                name="new_password_admin"
                                placeholder="{{ config('app.lang') != 'en' ? __('8-20자리의 숫자, 영문각각 1개 이상 포함') : __('8-20 characters with at least 1 number and 1 letter') }}"
                                required>
                        </div>
                        <div class="smartwatch-form-group">
                            <label
                                for="new_password_confirmation_admin">{{ config('app.lang') != 'en' ? __('비밀번호확인') : __('Confirm Password') }}</label>
                            <input type="password" class="form-control" id="new_password_confirmation_admin"
                                name="new_password_confirmation_admin"
                                placeholder="{{ config('app.lang') != 'en' ? __('8-20자리의 숫자, 영문각각 1개 이상 포함') : __('8-20 characters with at least 1 number and 1 letter') }}"
                                required>
                        </div>
                        <div style="display: flex;justify-content: space-between;">
                            <button type="submit" class="btn btn-primary"
                                style="width: 45%;">{{ config('app.lang') != 'en' ? __('확인') : __('Confirm') }}</button>
                            <button type="button" class="btn btn-info" style="width: 45%;background-color:#324D65"
                                data-dismiss="modal"
                                onclick="closeForm('changePasswordModalAdmin')">{{ config('app.lang') != 'en' ? __('취소') : __('Cancel') }}</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--  -->
</div>

<script>
let currentAdminId;

document.addEventListener('DOMContentLoaded', function() {
    const rightCard = document.getElementById('right-card');
    const registerCard = document.getElementById('register-card');
    const adminIdInput = document.getElementById('admin-id');
    const adminNameInput = document.getElementById('admin-name');
    const adminClassificationInput = document.getElementById('admin-classification');
    const adminDepartmentInput = document.getElementById('admin-department');
    const adminContactInput = document.getElementById('admin-contact');
    const saveBtn = document.getElementById('save-btn');
    const registerBtn = document.getElementById('register-btn');
    const registerAdminBtn = document.getElementById('register-admin-btn');


    saveBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (!globalValidateInputSinglePage()) {
            document.getElementById('error-message-pop-up').innerText =
                `{{ config('app.lang') != 'en' ? __('필수 입력란을 모두 작성해 주세요.') : __('Please fill out all required fields.') }}`
            $('#errorFailedWithMessagePopupModal').modal('show');
            return
        }
        const formData = {
            user_id: adminIdInput.value,
            name: adminNameInput.value,
            classification: adminClassificationInput.value,
            department: adminDepartmentInput.value,
            contact: adminContactInput.value,
        };

        fetch(`/public-welfare/setting/admin-management/${currentAdminId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                },
                body: JSON.stringify(formData)
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
    });

    registerAdminBtn.addEventListener('click', function() {
        rightCard.style.display = 'none';
        registerCard.style.display = 'block';
    });

    registerBtn.addEventListener('click', function(e) {
        if (!globalValidateInputSinglePage()) {
            document.getElementById('error-message-pop-up').innerText =
                `{{ config('app.lang') != 'en' ? __('필수 입력란을 모두 작성해 주세요.') : __('Please fill out all required fields.') }}`
            $('#errorFailedWithMessagePopupModal').modal('show');
            return
        }
        e.preventDefault();
        const formData = new FormData();
        formData.append('user_id', document.getElementById('register-admin-id').value);
        formData.append('name', document.getElementById('register-admin-name').value);
        formData.append('classification', document.getElementById('register-admin-classification')
            .value);
        formData.append('department', document.getElementById('register-admin-department').value);
        formData.append('contact', document.getElementById('register-admin-contact').value);
        formData.append('password', document.getElementById('register-admin-password').value);

        fetch('/public-welfare/setting/admin-management', {
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
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const changePasswordButtons = document.querySelectorAll('.change-password-btn');
    const changePasswordModalAdmin = document.getElementById('changePasswordModalAdmin');
    const closeModalButtons = changePasswordModalAdmin.querySelectorAll('.close, .btn-secondary');
    const changePasswordFormAdmin = document.getElementById('changePasswordFormAdmin');
    const passwordInput = document.getElementById('new_password_admin');
    const confirmPasswordInput = document.getElementById('new_password_confirmation_admin');
    const adminIdInput = document.getElementById('admin-id');

    changePasswordButtons.forEach(button => {
        button.addEventListener('click', function() {
            const adminId = this.closest('tr').dataset.id;
            adminIdInput.value = adminId;
            changePasswordModalAdmin.style.display = 'block';
        });
    });

    closeModalButtons.forEach(button => {
        button.addEventListener('click', function() {
            changePasswordModalAdmin.style.display = 'none';
        });
    });

    changePasswordFormAdmin.addEventListener('submit', function(event) {
        event.preventDefault();

        const check = validatePassword(passwordInput.value, confirmPasswordInput.value);
        if (check !== true) {
            return
        }
        const formData = new FormData(changePasswordFormAdmin);
        const adminId = adminIdInput.value;

        fetch('/public-welfare/setting/admin-management/change-password/' + adminId, {
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
                    changePasswordModalAdmin.style.display = 'none';
                } else {
                    $('#errorFailedPopupModal').modal('show');
                }
            })
            .catch(error => console.error('Error:', error));
    });
});



function edit(id) {
    const rightCard = document.getElementById('right-card');
    const registerCard = document.getElementById('register-card');
    const adminIdInput = document.getElementById('admin-id');
    const adminNameInput = document.getElementById('admin-name');
    const adminClassificationInput = document.getElementById('admin-classification');
    const adminDepartmentInput = document.getElementById('admin-department');
    const adminContactInput = document.getElementById('admin-contact');
    currentAdminId = id;
    fetch(`/public-welfare/setting/admin-management/${currentAdminId}`)
        .then(response => response.json())
        .then(data => {
            adminIdInput.value = data.user_id;
            adminNameInput.value = data.name;
            adminClassificationInput.value = data.classification;
            adminDepartmentInput.value = data.department;
            adminContactInput.value = data.contact;
            rightCard.style.display = 'block';
            registerCard.style.display = 'none';
        })
        .catch(error => console.error('Error:', error));
}

function closeForm(formId) {
    document.getElementById(formId).style = 'display:none';
}

function refreshPage() {
    let newPath = '/public-welfare/setting/admin-management';
    window.location.href = newPath;
}


function deleteSelected() {
    let selectedIds = [];
    document.querySelectorAll('.checkbox-click:checked').forEach(checkbox => {
        selectedIds.push(checkbox.value);
    });



    if (selectedIds.length > 0) {
        // Show the confirmation modal
        $('#deletedConfirmationPopUpModal').modal('show');

        // Add a one-time event listener for the confirm button
        document.getElementById('confirmed').addEventListener('click', function confirmHandler() {
            // Proceed with the delete request
            fetch('/public-welfare/setting/admin-management', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        ids: selectedIds
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    if (data.success) {
                        $('#successDeletedPopupModal').modal('show');
                    } else {
                        if (data.message) {
                            document.getElementById('error-message-pop-up').innerText = data.message
                            $('#errorFailedWithMessagePopupModal').modal('show');
                        } else {
                            $('#errorFailedPopupModal').modal('show');
                        }
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
}
</script>