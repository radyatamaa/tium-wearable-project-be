<div class="custom-container">
    <div class="custom-card custom-left-card" id="left-card">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header card-header-primary d-flex justify-content-between"
                        style="--header-background-color: #1E3142;">
                        <div class="smartwatch-search-container">
                            <div class="smartwatch-search-top">
                                <form id="filter-form" method="GET" action="/public-welfare/setting/user-management"
                                    class="d-flex justify-content-between w-100">
                                    <div class="smartwatch-search-input-container">
                                        <input type="text" class="smartwatch-search-input" id="searchInput"
                                            name="search"
                                            placeholder="{{ config('app.lang') != 'en' ? __('계정 정보를 관리할 수 있습니다.') : __('You can manage account information.') }}">
                                    </div>
                                    <button type="submit"
                                        class="smartwatch-search-button">{{ config('app.lang') != 'en' ? __('이름, 이메일로 검색') : __('Search by Name, Email') }}<i
                                            class="fas fa-search"></i></button>

                                </form>
                            </div>
                            <div class="smartwatch-search-bottom">
                                <button class="smartwatch-refresh-button" onclick="refreshPage()"><img
                                        src="{{ asset('black') }}/icons/general/refresh.svg"></button>
                                <button class="btn btn-danger" style="border-radius: 4px;padding: 10px 20px;"
                                    id="delete-btn"
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
                                        <th>{{ config('app.lang') != 'en' ? __('이메일') : __('Email') }}</th>
                                        <th>{{ config('app.lang') != 'en' ? __('이름') : __('Name') }}</th>
                                        <th>{{ config('app.lang') != 'en' ? __('단위') : __('Unit') }}</th>
                                        <th>{{ config('app.lang') != 'en' ? __('위치') : __('Position') }}</th>
                                        <th>{{ config('app.lang') != 'en' ? __('연락처 정보') : __('Contact information') }}
                                        </th>
                                        <th>{{ config('app.lang') != 'en' ? __('등록일') : __('Registration Date') }}</th>
                                        <th>{{ config('app.lang') != 'en' ? __('사용자정보') : __('User Information') }}
                                        </th>
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
                                            <td>{{ $admin->email }}</td>
                                            <td>{{ $admin->name }}</td>
                                            <td>{{ $admin->unit }}</td>
                                            <td>{{ $admin->position }}</td>
                                            <td>{{ $admin->contact_information }}</td>
                                            <td>{{ $admin->created_at }}</td>
                                            <td class="td-actions">
                                                <button class="btn btn-warning btn-sm" type="button">
                                                    <a href="/public-welfare/patient-management/{{$admin->user_customer_id}}"
                                                        style="color:#ffffff">{{ config('app.lang') != 'en' ? __('사용자정보') : __('User Information') }}</a>

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
                <label for="admin-name">{{ config('app.lang') != 'en' ? __('이름') : __('Name') }}</label>
                <input type="text" id="admin-name" name="admin-name" value="">
            </div>
            <div class="smartwatch-form-group">
                <label for="admin-gender">{{ config('app.lang') != 'en' ? __('성별') : __('Gender') }}</label>
                <select id="admin-gender" name="admin-gender" class="form-control required-input">
                    <option value="M">{{ config('app.lang') != 'en' ? __('남성') : __('Male') }}</option>
                    <option value="F">{{ config('app.lang') != 'en' ? __('여성') : __('Female') }}</option>
                </select>
            </div>
            <div class="smartwatch-form-group">
                <label for="admin-age">{{ config('app.lang') != 'en' ? __('나이') : __('Age') }}</label>
                <input type="number" id="admin-age" name="admin-age" value="">
            </div>
            <div class="smartwatch-form-group">
                <label for="admin-email">{{ config('app.lang') != 'en' ? __('이메일') : __('Email') }}</label>
                <input type="email" id="admin-email" name="admin-email" value="">
            </div>
            <div class="smartwatch-form-group">
                <label for="admin-birth-date">{{ config('app.lang') != 'en' ? __('생년월일') : __('Birth Date') }}</label>
                <input type="date" id="admin-birth-date" name="admin-birth-date" value="">
            </div>
            <div class="smartwatch-form-group">
                <label for="admin-position">{{ config('app.lang') != 'en' ? __('직위') : __('Position') }}</label>
                <input type="text" id="admin-position" name="admin-position" value="">
            </div>
            <div class="smartwatch-form-group">
                <label for="admin-unit">{{ config('app.lang') != 'en' ? __('부서') : __('Unit') }}</label>
                <input type="text" id="admin-unit" name="admin-unit" value="">
            </div>
            <div class="smartwatch-form-group">
                <label
                    for="admin-contact-information">{{ config('app.lang') != 'en' ? __('연락처') : __('Contact Information') }}</label>
                <input type="text" id="admin-contact-information" name="admin-contact-information" value="">
            </div>
            <div class="smartwatch-form-group">
                <label
                    for="admin-emergency-contact-1">{{ config('app.lang') != 'en' ? __('비상 연락처 1') : __('Emergency Contact 1') }}</label>
                <input type="text" id="admin-emergency-contact-1" name="admin-emergency-contact-1" value="">
            </div>
            <div class="smartwatch-form-group">
                <label
                    for="admin-emergency-contact-2">{{ config('app.lang') != 'en' ? __('비상 연락처 2') : __('Emergency Contact 2') }}</label>
                <input type="text" id="admin-emergency-contact-2" name="admin-emergency-contact-2" value="">
            </div>
            <div class="smartwatch-form-group">
                <label
                    for="admin-device-code">{{ config('app.lang') != 'en' ? __('장치 코드') : __('Device Code') }}</label>
                <select id="admin-device-code" name="admin-device-code">
                    <option>{{ config('app.lang') != 'en' ? __('선택') : __('Select') }}</option>
                    @foreach($devices as $device)
                        <option value="{{$device->serial}}">{{ $device->serial }}
                        </option>
                    @endforeach
                </select>
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
                <label for="register-name">{{ config('app.lang') != 'en' ? __('이름') : __('Name') }}</label>
                <input type="text" id="register-name" name="name">
            </div>
            <div class="smartwatch-form-group">
                <label for="register-gender">{{ config('app.lang') != 'en' ? __('성별') : __('Gender') }}</label>
                <select id="register-gender" name="gender">
                    <option value="M">{{ config('app.lang') != 'en' ? __('남성') : __('Male') }}</option>
                    <option value="F">{{ config('app.lang') != 'en' ? __('여성') : __('Female') }}</option>
                </select>
            </div>
            <div class="smartwatch-form-group">
                <label for="register-age">{{ config('app.lang') != 'en' ? __('나이') : __('Age') }}</label>
                <input type="number" id="register-age" name="age">
            </div>
            <div class="smartwatch-form-group">
                <label for="register-email">{{ config('app.lang') != 'en' ? __('이메일') : __('Email') }}</label>
                <input type="email" id="register-email" name="email">
            </div>
            <div class="smartwatch-form-group">
                <label
                    for="register-birth-date">{{ config('app.lang') != 'en' ? __('생년월일') : __('Birth Date') }}</label>
                <input type="date" id="register-birth-date" name="birth_date">
            </div>
            <div class="smartwatch-form-group">
                <label for="register-position">{{ config('app.lang') != 'en' ? __('직위') : __('Position') }}</label>
                <input type="text" id="register-position" name="position">
            </div>
            <div class="smartwatch-form-group">
                <label for="register-unit">{{ config('app.lang') != 'en' ? __('부서') : __('Unit') }}</label>
                <input type="text" id="register-unit" name="unit">
            </div>
            <div class="smartwatch-form-group">
                <label
                    for="register-contact-information">{{ config('app.lang') != 'en' ? __('연락처') : __('Contact Information') }}</label>
                <input type="text" id="register-contact-information" name="contact_information">
            </div>
            <div class="smartwatch-form-group">
                <label
                    for="register-emergency-contact-1">{{ config('app.lang') != 'en' ? __('비상 연락처 1') : __('Emergency Contact 1') }}</label>
                <input type="text" id="register-emergency-contact-1" name="emergency_contact_1">
            </div>
            <div class="smartwatch-form-group">
                <label
                    for="register-emergency-contact-2">{{ config('app.lang') != 'en' ? __('비상 연락처 2') : __('Emergency Contact 2') }}</label>
                <input type="text" id="register-emergency-contact-2" name="emergency_contact_2">
            </div>
            <div class="smartwatch-form-group">
                <label
                    for="register-device-code">{{ config('app.lang') != 'en' ? __('장치 코드') : __('Device Code') }}</label>
                <select id="register-device-code" name="device_code">
                    <option>{{ config('app.lang') != 'en' ? __('선택') : __('Select') }}</option>
                    @foreach($devices as $device)
                        <option value="{{$device->serial}}">{{ $device->serial }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="smartwatch-form-group">
                <label for="register-password">{{ config('app.lang') != 'en' ? __('비밀번호') : __('Password') }}</label>
                <input type="password" id="register-password" name="password" class="required-input">
            </div>
            <div class="smartwatch-form-actions">
                <button type="submit" class="smartwatch-btn smartwatch-btn-primary"
                    id="register-btn">{{ config('app.lang') != 'en' ? __('등록') : __('Register') }}</button>
                <button type="reset" onclick="closeForm('register-card')"
                    class="smartwatch-btn smartwatch-btn-secondary">{{ config('app.lang') != 'en' ? __('취소') : __('Cancel') }}</button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentAdminId;

    document.addEventListener('DOMContentLoaded', function () {
        const rightCard = document.getElementById('right-card');
        const registerCard = document.getElementById('register-card');
        const saveBtn = document.getElementById('save-btn');
        const registerBtn = document.getElementById('register-btn');
        const registerAdminBtn = document.getElementById('register-admin-btn');

        saveBtn.addEventListener('click', function (e) {
            e.preventDefault();
            if (!globalValidateInputSinglePage()) {
                document.getElementById('error-message-pop-up').innerText =
                    `{{ config('app.lang') != 'en' ? __('필수 입력란을 모두 작성해 주세요.') : __('Please fill out all required fields.') }}`
                $('#errorFailedWithMessagePopupModal').modal('show');
                return;
            }
            const formData = {
                name: document.getElementById('admin-name').value,
                gender: document.getElementById('admin-gender').value,
                age: document.getElementById('admin-age').value,
                // affiliations: document.getElementById('register-affiliations').value,
                email: document.getElementById('admin-email').value,
                birth_date: document.getElementById('admin-birth-date').value,
                position: document.getElementById('admin-position').value,
                unit: document.getElementById('admin-unit').value,
                contact_information: document.getElementById('admin-contact-information').value,
                emergency_contact_1: document.getElementById('admin-emergency-contact-1').value,
                emergency_contact_2: document.getElementById('admin-emergency-contact-2').value,
                device_code: document.getElementById('admin-device-code').value,
            };

            fetch(`/public-welfare/setting/user-management/${currentAdminId}`, {
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

        registerAdminBtn.addEventListener('click', function () {
            rightCard.style.display = 'none';
            registerCard.style.display = 'block';
            fetchDevices()
        });

        registerBtn.addEventListener('click', function (e) {
            e.preventDefault();
            const formData = new FormData();
            formData.append('name', document.getElementById('register-name').value);
            formData.append('gender', document.getElementById('register-gender').value);
            formData.append('age', document.getElementById('register-age').value);
            // formData.append('affiliations', document.getElementById('register-affiliations').value);
            formData.append('email', document.getElementById('register-email').value);
            formData.append('birth_date', document.getElementById('register-birth-date').value);
            formData.append('position', document.getElementById('register-position').value);
            formData.append('unit', document.getElementById('register-unit').value);
            formData.append('contact_information', document.getElementById('register-contact-information')
                .value);
            formData.append('emergency_contact_1', document.getElementById('register-emergency-contact-1')
                .value);
            formData.append('emergency_contact_2', document.getElementById('register-emergency-contact-2')
                .value);
            formData.append('device_code', document.getElementById('register-device-code').value);
            formData.append('password', document.getElementById('register-password').value);

            fetch('/public-welfare/setting/user-management', {
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

    function edit(id) {
        fetchDevices()
        const registerCard = document.getElementById('register-card');
        const rightCard = document.getElementById('right-card');
        // const adminIdInput = document.getElementById('register-id');
        const adminNameInput = document.getElementById('admin-name');
        const adminGenderInput = document.getElementById('admin-gender');
        const adminAgeInput = document.getElementById('admin-age');
        // const adminAffiliationsInput = document.getElementById('register-affiliations');
        const adminEmailInput = document.getElementById('admin-email');
        const adminBirthDateInput = document.getElementById('admin-birth-date');
        const adminPositionInput = document.getElementById('admin-position');
        const adminUnitInput = document.getElementById('admin-unit');
        const adminContactInformationInput = document.getElementById('admin-contact-information');
        const adminEmergencyContact1Input = document.getElementById('admin-emergency-contact-1');
        const adminEmergencyContact2Input = document.getElementById('admin-emergency-contact-2');
        const adminDeviceCodeInput = document.getElementById('admin-device-code');

        currentAdminId = id;

        fetch(`/public-welfare/setting/user-management/${currentAdminId}`)
            .then(response => response.json())
            .then(data => {

                // adminIdInput.value = data.user_id;
                adminNameInput.value = data.name;
                adminGenderInput.value = data.gender;
                adminAgeInput.value = data.age;
                // adminAffiliationsInput.value = data.affiliations;
                adminEmailInput.value = data.email;
                adminBirthDateInput.value = data.birth_date;
                adminPositionInput.value = data.position;
                adminUnitInput.value = data.unit;
                adminContactInformationInput.value = data.contact_information;
                adminEmergencyContact1Input.value = data.emergency_contact_1;
                adminEmergencyContact2Input.value = data.emergency_contact_2;
                adminDeviceCodeInput.value = data.device_code;
                addOrSelectOption('admin-device-code', data.device_code);

                rightCard.style.display = 'block';
                registerCard.style.display = 'none';
            })
            .catch(error => console.error('Error:', error));
    }

    function refreshPage() {
        window.location.reload();
    }

    function closeForm(formId) {
        document.getElementById(formId).style = 'display:none';
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
                fetch('/public-welfare/setting/user-management', {
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

    function fetchDevices() {
        clearSelectOptions('register-device-code');
        clearSelectOptions('admin-device-code');
        const selectElementRegister = document.getElementById('register-device-code');
        const selectElement = document.getElementById('admin-device-code');

        fetch('/public-welfare/setting/devices')
            .then(response => response.json())
            .then(data => {
                if (Array.isArray(data.devices)) {
                    data.devices.forEach(device => {
                        if (!Array.from(selectElementRegister.options).some(option => option.value === device
                            .serial)) {
                            const option = document.createElement('option');
                            option.value = device.serial;
                            option.textContent = device.serial;
                            selectElementRegister.appendChild(option);
                        }
                    });
                }

                if (Array.isArray(data.devices)) {
                    data.devices.forEach(device => {
                        if (!Array.from(selectElement.options).some(option => option.value === device
                            .serial)) {
                            const option = document.createElement('option');
                            option.value = device.serial;
                            option.textContent = device.serial;
                            selectElement.appendChild(option);
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error fetching devices:', error);
            });
    }
</script>