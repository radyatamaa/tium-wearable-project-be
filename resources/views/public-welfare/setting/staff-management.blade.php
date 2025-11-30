<div class="custom-container">
    <div class="custom-card custom-left-card" id="left-card">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header card-header-primary d-flex justify-content-between"
                        style="--header-background-color: #1E3142;">
                        <div class="smartwatch-search-container">
                            <div class="smartwatch-search-top">
                                <form id="filter-form" method="GET" action="/public-welfare/setting/staff-management"
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
                                    id="register-user-staff-btn">{{ config('app.lang') != 'en' ? __('계정등록') : __('Register Account') }}</button>
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
                                        <th>{{ config('app.lang') != 'en' ? __('직업 분류') : __('Job Category') }}</th>
                                        <th>{{ config('app.lang') != 'en' ? __('직책') : __('Position') }}</th>
                                        <th>{{ config('app.lang') != 'en' ? __('직원 ID') : __('Employee ID') }}</th>
                                        <th>{{ config('app.lang') != 'en' ? __('등록일') : __('Registration Date') }}</th>
                                        <th>{{ config('app.lang') != 'en' ? __('비밀번호변경') : __('Change Password') }}</th>
                                        <th>{{ config('app.lang') != 'en' ? __('수정') : __('Edit') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($datas as $user)
                                    <tr data-id="{{ $user->id }}">
                                        <td><input type="checkbox" class="checkbox-click" value="{{ $user->id}}"></td>
                                        <td>{{ $user->user_id }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->job_category }}</td>
                                        <td>{{ $user->position }}</td>
                                        <td>{{ $user->employee_id }}</td>
                                        <td>{{ $user->hire_date }}</td>
                                        <td class="td-actions">
                                            <button class="btn btn-warning btn-sm change-password-btn" type="button">
                                                {{ config('app.lang') != 'en' ? __('비밀번호변경') : __('Change Password') }}
                                            </button>
                                        </td>
                                        <td class="td-actions text-right">
                                            <button class="edit-button-box" type="button"
                                                onclick="edit({{ $user->id }})"> <img
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
        <div class="social-worker-content">
            <div class="social-worker-left-section">
                <div class="profile-picture">
                    <img src="{{ asset('black') }}/img/default-avatar.png" alt="Profile" class="profile-img"
                        id="profileImageEdit">
                    <label for="fileUpload" class="edit-icon">
                        <input type="file" id="fileUpload" accept="image/*" onchange="loadFileEdit(event)">
                        <img src="{{ asset('black') }}/icons/general/pencil.svg">
                    </label>
                </div>
            </div>
            <div class="social-worker-right-section">
                <table class="social-worker-info-table">
                    <tr>
                        <th>{{ config('app.lang') != 'en' ? __('아이디') : __('ID') }}</th>
                        <td><input type="text" id="admin-id" name="admin-id" value="">
                        </td>
                    </tr>
                    <tr>
                        <th>{{ config('app.lang') != 'en' ? __('이름') : __('Name') }}</th>
                        <td><input type="text" id="name" name="name" value="">
                        </td>
                    </tr>
                    <tr>
                        <th>{{ config('app.lang') != 'en' ? __('직업 분류') : __('Job Category') }}</th>
                        <td><input type="text" id="job_category" name="job_category" value=""></td>
                    </tr>
                    <tr>
                        <th>{{ config('app.lang') != 'en' ? __('직책') : __('Position') }}</th>
                        <td><input type="text" id="position" name="position" value=""></td>
                    </tr>
                    <tr>
                        <th>{{ config('app.lang') != 'en' ? __('직원 ID') : __('Employee ID') }}</th>
                        <td><input type="text" id="employee_id" name="employee_id" value=""></td>
                    </tr>
                    <tr>
                        <th>{{ config('app.lang') != 'en' ? __('등록일') : __('Hire Date') }}</th>
                        <td><input type="date" id="hire_date" name="hire_date" value=""></td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="social-worker-additional-info">
            <table class="social-worker-info-table">
                <tr>
                    <th>{{ config('app.lang') != 'en' ? __('생년월일') : __('Date of Birth') }}</th>
                    <td><input type="date" id="date_of_birth" name="date_of_birth" value=""></td>
                </tr>
                <tr>
                    <th>{{ config('app.lang') != 'en' ? __('나이') : __('Age') }}</th>
                    <td><input type="text" id="age" name="age" value=""></td>
                </tr>
                <tr>
                    <th>{{ config('app.lang') != 'en' ? __('성별') : __('Gender') }}</th>
                    <td><select id="gender" name="gender">
                            <option value="Male">{{ config('app.lang') != 'en' ? __('남성') : __('Male') }}</option>
                            <option value="Female">{{ config('app.lang') != 'en' ? __('여성') : __('Female') }}</option>
                        </select></td>
                </tr>
                <tr>
                    <th>{{ config('app.lang') != 'en' ? __('주소') : __('Address') }}</th>
                    <td><input type="text" id="address" name="address" value=""></td>
                </tr>
                <tr>
                    <th>{{ config('app.lang') != 'en' ? __('연락처 1') : __('Contact 1') }}</th>
                    <td><input type="text" id="contact1" name="contact1" value=""></td>
                </tr>
                <tr>
                    <th>{{ config('app.lang') != 'en' ? __('연락처 2') : __('Contact 2') }}</th>
                    <td><input type="text" id="contact2" name="contact2" value=""></td>
                </tr>
                <tr>
                    <th>{{ config('app.lang') != 'en' ? __('이메일') : __('Email') }}</th>
                    <td><input type="text" id="email" name="email" value=""></td>
                </tr>
            </table>
        </div>
        <!-- <div class="social-worker-beneficiary-section">
            <h3>Welfare Beneficiary</h3>
            <div class="social-worker-actions">
                <button class="btn btn-primary">Load Participants</button>
                <div class="btn-group">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        Authorized Personnel Only
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="#">Action</a>
                        <a class="dropdown-item" href="#">Another action</a>
                        <a class="dropdown-item" href="#">Something else here</a>
                    </div>
                </div>
            </div>
            <div class="social-worker-participant-list">
                <p>Mr.Hohn(Seoul, Jungnang) <a href="#">Detailed</a></p>
                <p>Mr.Hohn(Seoul, Jungnang) <a href="#">Detailed</a></p>
                <p>Mr.Hohn(Seoul, Jungnang) <a href="#">Detailed</a></p>
                <p>Mr.Hohn(Seoul, Jungnang) <a href="#">Detailed</a></p>
            </div>
        </div> -->
        <div class="social-worker-actions smartwatch-form-actions">
            <button type="submit" id="save-btn"
                class="smartwatch-btn smartwatch-btn-primary">{{ config('app.lang') != 'en' ? __('저장') : __('Save') }}</button>
            <button type="reset" onclick="closeForm('right-card')"
                class="smartwatch-btn smartwatch-btn-secondary">{{ config('app.lang') != 'en' ? __('취소') : __('Cancel') }}</button>
        </div>
    </div>
    <div class="custom-card custom-right-card" id="register-card" style="display:none;">
        <h2 class="social-worker-header title" style="text-align:center;">
            {{ config('app.lang') != 'en' ? __('계정등록') : __('Register Account') }}
        </h2>
        <div class="social-worker-content">
            <div class="social-worker-left-section">
                <div class="profile-picture">
                    <img src="{{ asset('black') }}/img/default-avatar.png" alt="Profile" class="profile-img"
                        id="profileImage">
                    <label for="fileUploadRegister" class="edit-icon">
                        <input type="file" id="fileUploadRegister" accept="image/*" onchange="loadFile(event)">
                        <img src="{{ asset('black') }}/icons/general/pencil.svg">
                    </label>
                </div>
            </div>
            <div class="social-worker-right-section">
                <table class="social-worker-info-table">
                    <tr>
                        <th>{{ config('app.lang') != 'en' ? __('아이디') : __('ID') }}</th>
                        <td><input type="text" id="register-admin-id" name="admin-id" value="">
                        </td>
                    </tr>
                    <tr>
                        <th>{{ config('app.lang') != 'en' ? __('이름') : __('Name') }}</th>
                        <td><input type="text" id="register-name" name="name" value="">
                        </td>
                    </tr>
                    <tr>
                        <th>{{ config('app.lang') != 'en' ? __('직업 분류') : __('Job Category') }}</th>
                        <td><input type="text" id="register-job_category" name="job_category" value=""></td>
                    </tr>
                    <tr>
                        <th>{{ config('app.lang') != 'en' ? __('직책') : __('Position') }}</th>
                        <td><input type="text" id="register-position" name="position" value=""></td>
                    </tr>
                    <tr>
                        <th>{{ config('app.lang') != 'en' ? __('직원 ID') : __('Employee ID') }}</th>
                        <td><input type="text" id="register-employee_id" name="employee_id" value=""></td>
                    </tr>
                    <tr>
                        <th>{{ config('app.lang') != 'en' ? __('등록일') : __('Hire Date') }}</th>
                        <td><input type="date" id="register-hire_date" name="hire_date" value=""></td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="social-worker-additional-info">
            <table class="social-worker-info-table">
                <tr>
                    <th>{{ config('app.lang') != 'en' ? __('생년월일') : __('Date of Birth') }}</th>
                    <td><input type="date" id="register-date_of_birth" name="date_of_birth" value=""></td>
                </tr>
                <tr>
                    <th>{{ config('app.lang') != 'en' ? __('나이') : __('Age') }}</th>
                    <td><input type="text" id="register-age" name="age" value=""></td>
                </tr>
                <tr>
                    <th>{{ config('app.lang') != 'en' ? __('성별') : __('Gender') }}</th>
                    <td><select id="register-gender" name="gender">
                            <option value="Male">{{ config('app.lang') != 'en' ? __('남성') : __('Male') }}</option>
                            <option value="Female">{{ config('app.lang') != 'en' ? __('여성') : __('Female') }}</option>
                        </select></td>
                </tr>
                <tr>
                    <th>{{ config('app.lang') != 'en' ? __('주소') : __('Address') }}</th>
                    <td><input type="text" id="register-address" name="address" value=""></td>
                </tr>
                <tr>
                    <th>{{ config('app.lang') != 'en' ? __('연락처 1') : __('Contact 1') }}</th>
                    <td><input type="text" id="register-contact1" name="contact1" value=""></td>
                </tr>
                <tr>
                    <th>{{ config('app.lang') != 'en' ? __('연락처 2') : __('Contact 2') }}</th>
                    <td><input type="text" id="register-contact2" name="contact2" value=""></td>
                </tr>
                <tr>
                    <th>{{ config('app.lang') != 'en' ? __('이메일') : __('Email') }}</th>
                    <td><input type="text" id="register-email" name="email" value=""></td>
                </tr>
            </table>
        </div>
        <!-- <div class="social-worker-beneficiary-section">
            <h3>Welfare Beneficiary</h3>
            <div class="social-worker-actions">
                <button class="btn btn-primary">Load Participants</button>
                <div class="btn-group">
                    <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        Authorized Personnel Only
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="#">Action</a>
                        <a class="dropdown-item" href="#">Another action</a>
                        <a class="dropdown-item" href="#">Something else here</a>
                    </div>
                </div>
            </div>
            <div class="social-worker-participant-list">
                <p>Mr.Hohn(Seoul, Jungnang) <a href="#">Detailed</a></p>
                <p>Mr.Hohn(Seoul, Jungnang) <a href="#">Detailed</a></p>
                <p>Mr.Hohn(Seoul, Jungnang) <a href="#">Detailed</a></p>
                <p>Mr.Hohn(Seoul, Jungnang) <a href="#">Detailed</a></p>
            </div>
        </div> -->
        <div class="social-worker-actions smartwatch-form-actions">
            <button type="submit" id="register-btn"
                class="smartwatch-btn smartwatch-btn-primary">{{ config('app.lang') != 'en' ? __('등록') : __('Register') }}</button>
            <button type="reset" onclick="closeForm('register-card')"
                class="smartwatch-btn smartwatch-btn-secondary">{{ config('app.lang') != 'en' ? __('취소') : __('Cancel') }}</button>
        </div>
    </div>
</div>

<!-- modal change password-->
<div class="modal-change-password" id="changePasswordModalStaff" tabindex="-1" role="dialog">
    <div class="modal-change-password-modal-dialog" role="document">
        <div class="modal-change-password-modal-content">
            <div class="modal-change-password-modal-header">
                <h5 class="modal-change-password-modal-title">
                    {{ config('app.lang') != 'en' ? __('비밀번호변경') : __('Change Password') }}
                </h5>
            </div>
            <div class="modal-change-password-modal-body">
                <form id="changePasswordFormStaff">
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
                            onclick="closeForm('changePasswordModalStaff')">{{ config('app.lang') != 'en' ? __('취소') : __('Cancel') }}</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
<!--  -->

<script>
let currentUserId;

function loadFileEdit(event) {
    const output = document.getElementById('profileImageEdit');
    output.src = URL.createObjectURL(event.target.files[0]);
    output.onload = function() {
        URL.revokeObjectURL(output.src) // free memory
    }
}

function loadFile(event) {
    const output = document.getElementById('profileImage');
    output.src = URL.createObjectURL(event.target.files[0]);
    output.onload = function() {
        URL.revokeObjectURL(output.src) // free memory
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const rightCard = document.getElementById('right-card');
    const registerCard = document.getElementById('register-card');
    const nameInput = document.getElementById('name');
    const jobCategoryInput = document.getElementById('job_category');
    const positionInput = document.getElementById('position');
    const employeeIdInput = document.getElementById('employee_id');
    const hireDateInput = document.getElementById('hire_date');
    const dateOfBirthInput = document.getElementById('date_of_birth');
    const ageInput = document.getElementById('age');
    const genderInput = document.getElementById('gender');
    const addressInput = document.getElementById('address');
    const contact1Input = document.getElementById('contact1');
    const contact2Input = document.getElementById('contact2');
    const emailInput = document.getElementById('email');
    const saveBtn = document.getElementById('save-btn');
    const registerBtn = document.getElementById('register-btn');
    const registerUserStaffBtn = document.getElementById('register-user-staff-btn');
    const adminIdInput = document.getElementById('admin-id');

    // Photo profile preview
    // const photoProfileInput = document.getElementById('photo_profile');
    // const photoPreview = document.getElementById('photo_preview');
    // const registerPhotoProfileInput = document.getElementById('register-photo_profile');
    // const registerPhotoPreview = document.getElementById('register-photo_preview');

    // photoProfileInput.addEventListener('change', function(event) {
    //     const file = event.target.files[0];
    //     if (file) {
    //         const reader = new FileReader();
    //         reader.onload = function(e) {
    //             photoPreview.src = e.target.result;
    //             photoPreview.style.display = 'block';
    //         };
    //         reader.readAsDataURL(file);
    //     }
    // });

    // registerPhotoProfileInput.addEventListener('change', function(event) {
    //     const file = event.target.files[0];
    //     if (file) {
    //         const reader = new FileReader();
    //         reader.onload = function(e) {
    //             registerPhotoPreview.src = e.target.result;
    //             registerPhotoPreview.style.display = 'block';
    //         };
    //         reader.readAsDataURL(file);
    //     }
    // });

    saveBtn.addEventListener('click', function(e) {
        e.preventDefault();
        const formData = new FormData();
        // formData.append('photo_profile', photoProfileInput.files[0]);
        formData.append('name', nameInput.value);
        formData.append('job_category', jobCategoryInput.value);
        formData.append('position', positionInput.value);
        formData.append('employee_id', employeeIdInput.value);
        formData.append('hire_date', hireDateInput.value);
        formData.append('date_of_birth', dateOfBirthInput.value);
        formData.append('age', ageInput.value);
        formData.append('gender', genderInput.value);
        formData.append('address', addressInput.value);
        formData.append('contact1', contact1Input.value);
        formData.append('contact2', contact2Input.value);
        formData.append('email', emailInput.value);
        formData.append('user_id', adminIdInput.value);
        const fileUpload = document.getElementById('fileUpload');
        if (fileUpload && fileUpload.files.length > 0) {
            formData.append('photo_profile', fileUpload.files[0]);
        }
        fetch(`/public-welfare/setting/staff-management/${currentUserId}`, {
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

    registerUserStaffBtn.addEventListener('click', function() {
        rightCard.style = 'display:none';
        registerCard.style = 'display:block';
    });

    registerBtn.addEventListener('click', function(e) {
        e.preventDefault();
        const formData = new FormData();
        // formData.append('photo_profile', registerPhotoProfileInput.files[0]);
        formData.append('name', document.getElementById('register-name').value);
        formData.append('job_category', document.getElementById('register-job_category').value);
        formData.append('position', document.getElementById('register-position').value);
        formData.append('employee_id', document.getElementById('register-employee_id').value);
        formData.append('hire_date', document.getElementById('register-hire_date').value);
        formData.append('date_of_birth', document.getElementById('register-date_of_birth').value);
        formData.append('age', document.getElementById('register-age').value);
        formData.append('gender', document.getElementById('register-gender').value);
        formData.append('address', document.getElementById('register-address').value);
        formData.append('contact1', document.getElementById('register-contact1').value);
        formData.append('contact2', document.getElementById('register-contact2').value);
        formData.append('email', document.getElementById('register-email').value);
        formData.append('user_id', document.getElementById('register-admin-id').value);
        const fileUpload = document.getElementById('fileUploadRegister');
        if (fileUpload && fileUpload.files.length > 0) {
            formData.append('photo_profile', fileUpload.files[0]);
        }

        fetch('/public-welfare/setting/staff-management', {
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
    const changePasswordModalStaff = document.getElementById('changePasswordModalStaff');
    const closeModalButtons = changePasswordModalStaff.querySelectorAll('.close, .btn-secondary');
    const changePasswordFormStaff = document.getElementById('changePasswordFormStaff');
    const passwordInput = document.getElementById('new_password_admin');
    const confirmPasswordInput = document.getElementById('new_password_confirmation_admin');
    const adminIdInput = document.getElementById('admin-id');

    changePasswordButtons.forEach(button => {
        button.addEventListener('click', function() {
            const adminId = this.closest('tr').dataset.id;
            adminIdInput.value = adminId;
            changePasswordModalStaff.style.display = 'block';
        });
    });

    closeModalButtons.forEach(button => {
        button.addEventListener('click', function() {
            changePasswordModalStaff.style.display = 'none';
        });
    });

    changePasswordFormStaff.addEventListener('submit', function(event) {
        event.preventDefault();

        const check = validatePassword(passwordInput.value, confirmPasswordInput.value);
        if (check !== true) {
            return
        }

        const formData = new FormData(changePasswordFormStaff);
        const adminId = adminIdInput.value;

        fetch('/public-welfare/setting/staff-management/change-password/' + adminId, {
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
                    changePasswordModalStaff.style.display = 'none';
                } else {
                    $('#errorFailedPopupModal').modal('show');
                }
            })
            .catch(error => console.error('Error:', error));
    });
});

function edit(id) {
    const registerCard = document.getElementById('register-card');
    const nameInput = document.getElementById('name');
    const jobCategoryInput = document.getElementById('job_category');
    const positionInput = document.getElementById('position');
    const employeeIdInput = document.getElementById('employee_id');
    const hireDateInput = document.getElementById('hire_date');
    const dateOfBirthInput = document.getElementById('date_of_birth');
    const ageInput = document.getElementById('age');
    const genderInput = document.getElementById('gender');
    const addressInput = document.getElementById('address');
    const contact1Input = document.getElementById('contact1');
    const contact2Input = document.getElementById('contact2');
    const emailInput = document.getElementById('email');
    const photoPreview = document.getElementById('profileImageEdit');
    currentUserId = id;

    fetch(`/public-welfare/setting/staff-management/${currentUserId}`)
        .then(response => response.json())
        .then(data => {
            nameInput.value = data.name;
            nameInput.value = data.name;
            jobCategoryInput.value = data.job_category;
            positionInput.value = data.position;
            employeeIdInput.value = data.employee_id;
            hireDateInput.value = data.hire_date;
            dateOfBirthInput.value = data.date_of_birth;
            ageInput.value = data.age;
            genderInput.value = data.gender;
            addressInput.value = data.address;
            contact1Input.value = data.contact1;
            contact2Input.value = data.contact2;
            emailInput.value = data.email;
            document.getElementById('admin-id').value = data.user_id;
            getFile(`/storage/${data.photo_profile}`, photoPreview)
            rightCard.style.display = 'block';
            registerCard.style.display = 'none';
        })
        .catch(error => console.error('Error:', error));
}

function closeForm(formId) {
    document.getElementById(formId).style.display = 'none';
}

function refreshPage() {
    let newPath = '/public-welfare/setting/staff-management';
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
            fetch('/public-welfare/setting/staff-management', {
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