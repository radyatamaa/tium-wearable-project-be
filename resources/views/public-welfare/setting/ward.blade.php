<div class="custom-container">
    <div class="custom-card custom-left-card" id="left-card">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header card-header-primary d-flex justify-content-between"
                        style="--header-background-color: #1E3142;">
                        <div class="smartwatch-search-container">
                            <div class="smartwatch-search-top">
                                <form id="filter-form" method="GET" action="/public-welfare/setting/ward"
                                    class="d-flex justify-content-between w-100">
                                    <div class="smartwatch-search-input-container">
                                        <input type="text" class="smartwatch-search-input" id="searchInput"
                                            name="search"
                                            placeholder="{{ config('app.lang') != 'en' ? __('계정 정보를 관리할 수 있습니다.') : __('You can manage account information.') }}">
                                    </div>
                                    <button type="submit" class="smartwatch-search-button">
                                        {{ config('app.lang') != 'en' ? __('병동명, 병동번호 검색') : __('Search by Ward Name, Ward Number') }}<i
                                            class="fas fa-search"></i>
                                    </button>
                                </form>
                            </div>
                            <div class="smartwatch-search-bottom">
                                <button class="smartwatch-refresh-button" onclick="refreshPage()"><img
                                        src="{{ asset('black') }}/icons/general/refresh.svg"></button>
                                <button class="btn btn-danger" style="border-radius: 4px;padding: 10px 20px;"
                                    id="delete-device-btn"
                                    onclick="deleteSelected()">{{ config('app.lang') != 'en' ? __('삭제') : __('Elimination') }}</button>
                                <button class="btn btn-primary" style="border-radius: 4px;padding: 10px 20px;"
                                    id="register-ward-btn">{{ config('app.lang') != 'en' ? __('병동 등록') : __('Register Ward') }}</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="styled-table-public-welfare-container">
                            <table class="styled-table-public-welfare">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>{{ config('app.lang') != 'en' ? __('병동 번호') : __('Ward Number') }}</th>
                                        <th>{{ config('app.lang') != 'en' ? __('병동명') : __('Ward Name') }}</th>
                                        <th>{{ config('app.lang') != 'en' ? __('사용 여부') : __('Usage Status') }}</th>
                                        <th>{{ config('app.lang') != 'en' ? __('등록일') : __('Registration Date') }}</th>
                                        <th>{{ config('app.lang') != 'en' ? __('수정') : __('Edit') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($datas as $ward)
                                    <tr data-id="{{ $ward->id }}">
                                        <td><input type="checkbox" class="checkbox-click" value="{{ $ward->id}}"></td>
                                        <td>{{ $ward->ward_number }}</td>
                                        <td>{{ $ward->ward_name }}</td>
                                        <td>{{ $ward->usage_status ? 'Y' : 'N' }}</td>
                                        <td>{{ $ward->registration_date }}</td>
                                        <td class="td-actions text-right">
                                            <button class="edit-button-box" type="button"
                                                onclick="edit({{ $ward->id }})"> <img
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
            {{ config('app.lang') != 'en' ? __('병동 정보 수정') : __('Edit Ward Information') }}
        </h2>
        <div class="smartwatch-form-container">
            <div class="smartwatch-form-group">
                <label for="ward-number">{{ config('app.lang') != 'en' ? __('병동 번호') : __('Ward Number') }}</label>
                <input type="text" id="ward-number" name="ward-number" value="">
            </div>
            <div class="smartwatch-form-group">
                <label for="ward-name">{{ config('app.lang') != 'en' ? __('병동명') : __('Ward Name') }}</label>
                <input type="text" id="ward-name" name="ward-name" value="">
            </div>
            <div class="smartwatch-form-group">
                <label for="usage-status">{{ config('app.lang') != 'en' ? __('사용 여부') : __('Usage Status') }}</label>
                <select id="usage-status" name="usage_status">
                    <option value="1">{{ config('app.lang') != 'en' ? __('사용') : __('Use') }}</option>
                    <option value="0">{{ config('app.lang') != 'en' ? __('미사용') : __('Not Use') }}</option>
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
            {{ config('app.lang') != 'en' ? __('병동 등록') : __('Register Ward') }}
        </h2>
        <div class="smartwatch-form-container">
            <div class="smartwatch-form-group">
                <label
                    for="register-ward-number">{{ config('app.lang') != 'en' ? __('병동 번호') : __('Ward Number') }}</label>
                <input type="text" id="register-ward-number" name="ward_number">
            </div>
            <div class="smartwatch-form-group">
                <label for="register-ward-name">{{ config('app.lang') != 'en' ? __('병동명') : __('Ward Name') }}</label>
                <input type="text" id="register-ward-name" name="ward_name">
            </div>
            <div class="smartwatch-form-group">
                <label for="usage-status">{{ config('app.lang') != 'en' ? __('사용 여부') : __('Usage Status') }}</label>
                <select id="usage-status" name="usage_status">
                    <option value="1">{{ config('app.lang') != 'en' ? __('사용') : __('Use') }}</option>
                    <option value="0">{{ config('app.lang') != 'en' ? __('미사용') : __('Not Use') }}</option>
                </select>
            </div>
            <div class="smartwatch-form-actions">
                <button type="submit" id="register-btn"
                    class="smartwatch-btn smartwatch-btn-primary">{{ config('app.lang') != 'en' ? __('등록') : __('Register') }}</button>
                <button type="reset" onclick="closeForm('register-card')"
                    class="smartwatch-btn smartwatch-btn-secondary">{{ config('app.lang') != 'en' ? __('취소') : __('Cancel') }}</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentWardId;

document.addEventListener('DOMContentLoaded', function() {
    const rightCard = document.getElementById('right-card');
    const registerCard = document.getElementById('register-card');
    const wardNumberInput = document.getElementById('ward-number');
    const wardNameInput = document.getElementById('ward-name');
    const saveBtn = document.getElementById('save-btn');
    const registerBtn = document.getElementById('register-btn');
    const registerWardBtn = document.getElementById('register-ward-btn');
    const statusInput = document.getElementById('usage-status');

    saveBtn.addEventListener('click', function(e) {
        e.preventDefault();
        const formData = {
            ward_number: wardNumberInput.value,
            ward_name: wardNameInput.value,
            usage_status: statusInput.value === '1'
        };

        fetch(`/public-welfare/setting/ward/${currentWardId}`, {
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
                    $('#errorFailedPopupModal').modal('show');
                }
            })
            .catch(error => console.error('Error:', error));
    });

    registerWardBtn.addEventListener('click', function() {
        rightCard.style.display = 'none';
        registerCard.style.display = 'block';
    });

    registerBtn.addEventListener('click', function(e) {
        e.preventDefault();
        const formData = {
            ward_number: document.getElementById('register-ward-number').value,
            ward_name: document.getElementById('register-ward-name').value,
            usage_status: document.getElementById('usage-status').value === '1'
        };

        fetch('/public-welfare/setting/ward', {
                method: 'POST',
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
                    $('#errorFailedPopupModal').modal('show');
                }
            })
            .catch(error => console.error('Error:', error));
    });
});

function edit(id) {
    const wardNumberInput = document.getElementById('ward-number');
    const wardNameInput = document.getElementById('ward-name');
    const statusInput = document.getElementById('usage-status');
    currentWardId = id;

    fetch(`/public-welfare/setting/ward/${currentWardId}`)
        .then(response => response.json())
        .then(data => {
            wardNumberInput.value = data.ward_number;
            wardNameInput.value = data.ward_name;
            statusInput.value = data.usage_status ? '1' : '0';
            rightCard.style.display = 'block';
        })
        .catch(error => console.error('Error:', error));
}

function closeForm(formId) {
    document.getElementById(formId).style.display = 'none';
}

function refreshPage() {
    let newPath = '/public-welfare/setting/ward';
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
            fetch('/public-welfare/setting/ward', {
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