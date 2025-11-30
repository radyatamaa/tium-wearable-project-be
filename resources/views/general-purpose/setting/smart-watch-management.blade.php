<div class="custom-container">
    <div class="custom-card custom-left-card" id="left-card">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header card-header-primary d-flex justify-content-between"
                        style="--header-background-color: #1E3142;">
                        <div class="smartwatch-search-container">
                            <div class="smartwatch-search-top">
                                <form id="filter-form" method="GET" action="/general-purpose/setting/smart-watch"
                                    class="d-flex justify-content-between w-100">
                                    <div class="smartwatch-search-input-container">
                                        <input type="text" class="smartwatch-search-input" id="searchInput"
                                            name="search"
                                            placeholder="{{ config('app.lang') != 'en' ? __('계정 정보를 관리할 수 있습니다.') : __('You can manage account information.') }}">
                                    </div>
                                    <button type="submit" class="smartwatch-search-button">
                                        {{ config('app.lang') != 'en' ? __('시리얼, 기기명 검색') : __('Search by Serial, Device Name') }}<i
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
                                    id="register-device-btn">{{ config('app.lang') != 'en' ? __('기기등록') : __('Register Device') }}</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="styled-table-general-purpose-container">
                            <table class="styled-table-general-purpose" style="border-spacing:-10px-15px;">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>{{ config('app.lang') != 'en' ? __('기기시리얼') : __('Device Serial') }}</th>
                                        <th>{{ config('app.lang') != 'en' ? __('기기명') : __('Device Name') }}</th>
                                        <th>{{ config('app.lang') != 'en' ? __('등록대상') : __('Registered To') }}</th>
                                        <th>{{ config('app.lang') != 'en' ? __('사용여부') : __('Usage Status') }}</th>
                                        <th>{{ config('app.lang') != 'en' ? __('등록일') : __('Registration Date') }}</th>
                                        <th class="text-right">{{ config('app.lang') != 'en' ? __('수정') : __('Edit') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($datas as $data)
                                    <tr data-id="{{ $data->id }}">
                                        <td><input type="checkbox" class="checkbox-click" value="{{ $data->id}}"></td>
                                        <td>{{ $data->serial }}</td>
                                        <td>{{ $data->name }}</td>
                                        <td>{{ $data->registered_to }}</td>
                                        <td>{{ $data->usage_status ? 'Y' : 'N' }}</td>
                                        <td>{{ $data->registration_date }}</td>
                                        <td class="td-actions text-right">
                                            <button class="edit-button-box" type="button"
                                                onclick="edit({{ $data->id }})"> <img
                                                    src="{{ asset('black') }}/icons/general/edit-box.svg"
                                                    alt="{{ config('app.lang') != 'en' ? __('수정') : __('Edit') }}"></button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center">
                            {{ $datas->links('layouts.pagination') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="custom-resizer" id="resizer"></div>
    <div class="custom-card custom-right-card" id="right-card" style="display:none;">
        <h2 class="title" style="text-align:center;">
            {{ config('app.lang') != 'en' ? __('기기정보수정') : __('Edit Device Information') }}
        </h2>
        <div class="smartwatch-form-container">
            <div class="smartwatch-form-group">
                <label for="device-serial">{{ config('app.lang') != 'en' ? __('기기시리얼') : __('Device Serial') }}</label>
                <input type="text" id="device-serial" name="device-serial">
            </div>
            <div class="smartwatch-form-group">
                <label for="device-name">{{ config('app.lang') != 'en' ? __('기기명') : __('Device Name') }}</label>
                <input type="text" id="device-name" name="device-name">
            </div>
            <div class="smartwatch-form-actions">
                <button type="submit" class="smartwatch-btn smartwatch-btn-primary"
                    id="save-btn">{{ config('app.lang') != 'en' ? __('저장') : __('Save') }}</button>
                <button type="reset" class="smartwatch-btn smartwatch-btn-secondary"
                    onclick="closeForm('right-card')">{{ config('app.lang') != 'en' ? __('취소') : __('Cancel') }}</button>
            </div>
        </div>
    </div>
    <div class="custom-card custom-right-card" id="register-card" style="display:none;">
        <h2 class="title" style="text-align:center;">
            {{ config('app.lang') != 'en' ? __('기기등록') : __('Register Device') }}
        </h2>
        <div class="smartwatch-form-container">
            <div class="smartwatch-form-group">
                <label
                    for="register-device-serial">{{ config('app.lang') != 'en' ? __('기기시리얼') : __('Device Serial') }}</label>
                <input type="text" id="register-device-serial" name="serial">
            </div>
            <div class="smartwatch-form-group">
                <label
                    for="register-device-name">{{ config('app.lang') != 'en' ? __('기기명') : __('Device Name') }}</label>
                <input type="text" id="register-device-name" name="name">
            </div>
            <div class="smartwatch-form-group">
                <label
                    for="register-registered-to">{{ config('app.lang') != 'en' ? __('등록대상') : __('Registered To') }}</label>
                <input type="text" id="register-registered-to" name="registered_to">
            </div>
            <div class="smartwatch-form-actions">
                <button type="submit" class="smartwatch-btn smartwatch-btn-primary"
                    id="register-btn">{{ config('app.lang') != 'en' ? __('등록') : __('Register') }}</button>
                <button type="reset" class="smartwatch-btn smartwatch-btn-secondary"
                    onclick="closeForm('register-card')">{{ config('app.lang') != 'en' ? __('취소') : __('Cancel') }}</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentDeviceId;

document.addEventListener('DOMContentLoaded', function() {
    const rows = document.querySelectorAll('.styled-table tbody tr');
    const rightCard = document.getElementById('right-card');
    const registerCard = document.getElementById('register-card');
    const serialInput = document.getElementById('device-serial');
    const nameInput = document.getElementById('device-name');
    // const registeredToInput = document.getElementById('registered-to');
    const saveBtn = document.getElementById('save-btn');
    const registerBtn = document.getElementById('register-btn');
    const registerDeviceBtn = document.getElementById('register-device-btn');


    rows.forEach(row => {
        row.addEventListener('click', function() {
            currentDeviceId = this.dataset.id;
            fetch(`/general-purpose/setting/smart-watch/${currentDeviceId}`)
                .then(response => response.json())
                .then(data => {
                    serialInput.value = data.serial;
                    nameInput.value = data.name;
                    rightCard.style.display = 'block';
                    registerCard.style.display = 'none';
                })
                .catch(error => console.error('Error:', error));
        });
    });

    saveBtn.addEventListener('click', function(e) {
        e.preventDefault();
        const formData = {
            serial: serialInput.value,
            name: nameInput.value,
            // registered_to: registeredToInput.value,
        };

        fetch(`/general-purpose/setting/smart-watch/${currentDeviceId}`, {
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

    registerDeviceBtn.addEventListener('click', function() {
        rightCard.style.display = 'none';
        registerCard.style.display = 'block';
    });

    registerBtn.addEventListener('click', function(e) {
        e.preventDefault();
        const formData = new FormData();
        formData.append('serial', document.getElementById('register-device-serial').value);
        formData.append('name', document.getElementById('register-device-name').value);
        formData.append('registered_to', document.getElementById('register-registered-to').value);

        fetch('/general-purpose/setting/smart-watch', {
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
    const rightCard = document.getElementById('right-card');
    const registerCard = document.getElementById('register-card');
    const serialInput = document.getElementById('device-serial');
    const nameInput = document.getElementById('device-name');
    const registeredToInput = document.getElementById('registered-to');

    currentDeviceId = id;
    fetch(`/general-purpose/setting/smart-watch/${id}`)
        .then(response => response.json())
        .then(data => {
            serialInput.value = data.serial;
            nameInput.value = data.name;
            rightCard.style.display = 'block';
            registerCard.style.display = 'none';
        })
        .catch(error => console.error('Error:', error));
}

function closeForm(formId) {
    document.getElementById(formId).style = 'display:none';
}

function refreshPage() {
    let newPath = '/general-purpose/setting/smart-watch';
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
            fetch('/general-purpose/setting/smart-watch', {
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