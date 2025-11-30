<div class="setting-public-welfare-card">
    <div class="setting-public-welfare-card-content">
        <div class="setting-public-welfare-card-title">
            {{ config('app.lang') != 'en' ? __('측정기준설정') : __('Measurement Standard Settings') }}
            <span
                class="setting-public-welfare-card-subtitle">{{ config('app.lang') != 'en' ? __('기본설정값') : __('Default Settings') }}</span>
        </div>
        <div class="setting-public-welfare-card-actions">
            <span class="setting-public-welfare-refresh-icon" id="reset"><img
                    src="{{ asset('black') }}/icons/general/refresh.svg"></span>
        </div>
    </div>
</div>

<form id="settings-form">
    @csrf
    <table class="card-form-table">
        <thead>
            <tr>
                <th style="background-color:#3E6282">{{ config('app.lang') != 'en' ? __('체온') : __('Body Temp') }}</th>
                <th>{{ config('app.lang') != 'en' ? __('최저') : __('Min') }}</th>
                <th>{{ config('app.lang') != 'en' ? __('최대') : __('Max') }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="background-color:#324D65;color:#FFFFFF">
                    {{ config('app.lang') != 'en' ? __('주의') : __('Caution') }}
                </td>
                <td><input type="number" step="0.1" name="body_temp_caution_min" id="body_temp_caution_min"
                        value="{{ $settings->body_temp_caution_min }}"></td>
                <td><input type="number" step="0.1" name="body_temp_caution_max" id="body_temp_caution_max"
                        value="{{ $settings->body_temp_caution_max }}"></td>
            </tr>
            <tr>
                <td style="background-color:#324D65;color:#FFFFFF">
                    {{ config('app.lang') != 'en' ? __('위험') : __('Danger') }}
                </td>
                <td><input type="number" step="0.1" name="body_temp_danger_min" id="body_temp_danger_min"
                        value="{{ $settings->body_temp_danger_min }}"></td>
                <td><input type="number" step="0.1" name="body_temp_danger_max" id="body_temp_danger_max"
                        value="{{ $settings->body_temp_danger_max }}"></td>
            </tr>
        </tbody>
    </table>

    <table class="card-form-table">
        <thead>
            <tr>
                <th style="background-color:#3E6282">{{ config('app.lang') != 'en' ? __('심박수') : __('Heart Rate') }}
                </th>
                <th>{{ config('app.lang') != 'en' ? __('최저') : __('Min') }}</th>
                <th>{{ config('app.lang') != 'en' ? __('최대') : __('Max') }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="background-color:#324D65;color:#FFFFFF">
                    {{ config('app.lang') != 'en' ? __('주의') : __('Caution') }}
                </td>
                <td><input type="number" step="0.1" name="heart_rate_caution_min" id="heart_rate_caution_min"
                        value="{{ $settings->heart_rate_caution_min }}"></td>
                <td><input type="number" step="0.1" name="heart_rate_caution_max" id="heart_rate_caution_max"
                        value="{{ $settings->heart_rate_caution_max }}"></td>
            </tr>
            <tr>
                <td style="background-color:#324D65;color:#FFFFFF">
                    {{ config('app.lang') != 'en' ? __('위험') : __('Danger') }}
                </td>
                <td><input type="number" step="0.1" name="heart_rate_danger_min" id="heart_rate_danger_min"
                        value="{{ $settings->heart_rate_danger_min }}"></td>
                <td><input type="number" step="0.1" name="heart_rate_danger_max" id="heart_rate_danger_max"
                        value="{{ $settings->heart_rate_danger_max }}"></td>
            </tr>
        </tbody>
    </table>

    <table class="card-form-table">
        <thead>
            <tr>
                <th style="background-color:#3E6282">
                    {{ config('app.lang') != 'en' ? __('호흡수') : __('Respiratory Rate') }}
                </th>
                <th>{{ config('app.lang') != 'en' ? __('최저') : __('Min') }}</th>
                <th>{{ config('app.lang') != 'en' ? __('최대') : __('Max') }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="background-color:#324D65;color:#FFFFFF">
                    {{ config('app.lang') != 'en' ? __('주의') : __('Caution') }}
                </td>
                <td><input type="number" step="0.1" name="respiratory_rate_caution_min"
                        id="respiratory_rate_caution_min" value="{{ $settings->respiratory_rate_caution_min }}"></td>
                <td><input type="number" step="0.1" name="respiratory_rate_caution_max"
                        id="respiratory_rate_caution_max" value="{{ $settings->respiratory_rate_caution_max }}"></td>
            </tr>
            <tr>
                <td style="background-color:#324D65;color:#FFFFFF">
                    {{ config('app.lang') != 'en' ? __('위험') : __('Danger') }}
                </td>
                <td><input type="number" step="0.1" name="respiratory_rate_danger_min" id="respiratory_rate_danger_min"
                        value="{{ $settings->respiratory_rate_danger_min }}"></td>
                <td><input type="number" step="0.1" name="respiratory_rate_danger_max" id="respiratory_rate_danger_max"
                        value="{{ $settings->respiratory_rate_danger_max }}"></td>
            </tr>
        </tbody>
    </table>

    <table class="card-form-table">
        <thead>
            <tr>
                <th rowspan="2" style="background-color:#3E6282">
                    {{ config('app.lang') != 'en' ? __('혈압') : __('Blood Pressure') }}
                </th>
                <th colspan="2">{{ config('app.lang') != 'en' ? __('수축') : __('Systolic') }}</th>
                <th colspan="2">{{ config('app.lang') != 'en' ? __('이완') : __('Diastolic') }}</th>
            </tr>
            <tr>
                <th>{{ config('app.lang') != 'en' ? __('최저') : __('Min') }}</th>
                <th>{{ config('app.lang') != 'en' ? __('최대') : __('Max') }}</th>
                <th>{{ config('app.lang') != 'en' ? __('최저') : __('Min') }}</th>
                <th>{{ config('app.lang') != 'en' ? __('최대') : __('Max') }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="background-color:#324D65;color:#FFFFFF">
                    {{ config('app.lang') != 'en' ? __('주의') : __('Caution') }}
                </td>
                <td><input type="number" step="0.1" name="blood_pressure_systolic_caution_min"
                        id="blood_pressure_systolic_caution_min" id="blood_pressure_systolic_caution_min"
                        value="{{ $settings->blood_pressure_systolic_caution_min }}"></td>
                <td><input type="number" step="0.1" name="blood_pressure_systolic_caution_max"
                        id="blood_pressure_systolic_caution_max" id="blood_pressure_systolic_caution_max"
                        value="{{ $settings->blood_pressure_systolic_caution_max }}"></td>
                <td><input type="number" step="0.1" name="blood_pressure_diastolic_caution_min"
                        id="blood_pressure_diastolic_caution_min" id="blood_pressure_diastolic_caution_min"
                        value="{{ $settings->blood_pressure_diastolic_caution_min }}"></td>
                <td><input type="number" step="0.1" name="blood_pressure_diastolic_caution_max"
                        id="blood_pressure_diastolic_caution_max" id="blood_pressure_diastolic_caution_max"
                        value="{{ $settings->blood_pressure_diastolic_caution_max }}"></td>
            </tr>
            <tr>
                <td style="background-color:#324D65;color:#FFFFFF">
                    {{ config('app.lang') != 'en' ? __('위험') : __('Danger') }}
                </td>
                <td><input type="number" step="0.1" name="blood_pressure_systolic_danger_min"
                        id="blood_pressure_systolic_danger_min" id="blood_pressure_systolic_danger_min"
                        value="{{ $settings->blood_pressure_systolic_danger_min }}"></td>
                <td><input type="number" step="0.1" name="blood_pressure_systolic_danger_max"
                        id="blood_pressure_systolic_danger_max" id="blood_pressure_systolic_danger_max"
                        value="{{ $settings->blood_pressure_systolic_danger_max }}"></td>
                <td><input type="number" step="0.1" name="blood_pressure_diastolic_danger_min"
                        id="blood_pressure_diastolic_danger_min" id="blood_pressure_diastolic_danger_min"
                        value="{{ $settings->blood_pressure_diastolic_danger_min }}"></td>
                <td><input type="number" step="0.1" name="blood_pressure_diastolic_danger_max"
                        id="blood_pressure_diastolic_danger_max" id="blood_pressure_diastolic_danger_max"
                        value="{{ $settings->blood_pressure_diastolic_danger_max }}"></td>
            </tr>
        </tbody>
    </table>

    <table class="card-form-table">
        <thead>
            <tr>
                <th style="background-color:#3E6282">
                    {{ config('app.lang') != 'en' ? __('산소포화도') : __('Oxygen Saturation') }}
                </th>
                <th>{{ config('app.lang') != 'en' ? __('최저') : __('Min') }}</th>
                <th>{{ config('app.lang') != 'en' ? __('최대') : __('Max') }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="background-color:#324D65;color:#FFFFFF">
                    {{ config('app.lang') != 'en' ? __('주의') : __('Caution') }}
                </td>
                <td><input type="number" step="0.1" name="oxygen_saturation_caution_min"
                        id="oxygen_saturation_caution_min" id="oxygen_saturation_caution_min"
                        value="{{ $settings->oxygen_saturation_caution_min }}"></td>
                <td><input type="number" step="0.1" name="oxygen_saturation_caution_max"
                        id="oxygen_saturation_caution_max" id="oxygen_saturation_caution_max"
                        value="{{ $settings->oxygen_saturation_caution_max }}"></td>
            </tr>
            <tr>
                <td style="background-color:#324D65;color:#FFFFFF">
                    {{ config('app.lang') != 'en' ? __('위험') : __('Danger') }}
                </td>
                <td><input type="number" step="0.1" name="oxygen_saturation_danger_min"
                        id="oxygen_saturation_danger_min" id="oxygen_saturation_danger_min"
                        value="{{ $settings->oxygen_saturation_danger_min }}"></td>
                <td><input type="number" step="0.1" name="oxygen_saturation_danger_max"
                        id="oxygen_saturation_danger_max" id="oxygen_saturation_danger_max"
                        value="{{ $settings->oxygen_saturation_danger_max }}"></td>
            </tr>
        </tbody>
    </table>

    <table class="card-form-table">
        <thead>
            <tr>
                <th style="background-color:#3E6282">{{ config('app.lang') != 'en' ? __('심전도') : __('ECG') }}
                </th>
                <th>{{ config('app.lang') != 'en' ? __('최저') : __('Min') }}</th>
                <th>{{ config('app.lang') != 'en' ? __('최대') : __('Max') }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="background-color:#324D65;color:#FFFFFF">
                    {{ config('app.lang') != 'en' ? __('주의') : __('Caution') }}
                </td>
                <td><input type="number" step="0.1" name="ecg_caution_min" id="ecg_caution_min"
                        value="{{ $settings->ecg_caution_min }}"></td>
                <td><input type="number" step="0.1" name="ecg_caution_max" id="ecg_caution_max"
                        value="{{ $settings->ecg_caution_max }}"></td>
            </tr>
            <tr>
                <td style="background-color:#324D65;color:#FFFFFF">
                    {{ config('app.lang') != 'en' ? __('위험') : __('Danger') }}
                </td>
                <td><input type="number" step="0.1" name="ecg_danger_min" id="ecg_danger_min"
                        value="{{ $settings->ecg_danger_min }}"></td>
                <td><input type="number" step="0.1" name="ecg_danger_max" id="ecg_danger_max"
                        value="{{ $settings->ecg_danger_max }}"></td>
            </tr>
        </tbody>
    </table>

    <table class="card-form-table">
        <thead>
            <tr>
                <th rowspan="2" style="background-color:#3E6282;color:#FFFFFF">
                    {{ config('app.lang') != 'en' ? __('체온측정시') : __('External Temperature Criteria') }}
                </th>
                <th>{{ config('app.lang') != 'en' ? __('기본값') : __('Default Value') }}</th>
                <th rowspan="2" style="background-color:#192734;color:#FFFFFF;border:1px solid #192734">
                    {{ config('app.lang') != 'en' ? __('35 이상이면 알람이 울리지 않습니다') : __('The alarm does not sound if it is above 35') }}
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="background-color:#3E6282;color:#FFFFFF">
                    {{ config('app.lang') != 'en' ? __('외부온도기준') : __('for Body Temperature Measurement') }}
                </td>
                <td><input type="number" step="0.1" name="external_temp_default_value" id="external_temp_default_value"
                        value="{{ $settings->external_temp_default_value }}"></td>
                <td style="background-color:#192734;color:#FFFFFF;border:1px solid #192734"></td>
            </tr>
        </tbody>
    </table>

    <div style="margin-left:20px;margin-top:20px">
        <button type="submit" class="btn btn-primary" id="save-button"
            style="padding:10px 205px">{{ config('app.lang') != 'en' ? __('저장') : __('Save') }}</button>
        <button type="button" class="btn btn-primary" id="reset-button"
            style="padding:10px 205px">{{ config('app.lang') != 'en' ? __('초기화') : __('Reset') }}</button>
    </div>
</form>

<script>
    document.getElementById('settings-form').addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);
        const csrfToken = document.querySelector('input[name="_token"]').value;

        fetch('/public-welfare/setting/alarm', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    $('#successPopupModal').modal('show');
                } else {
                    $('#errorFailedPopupModal').modal('show');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                $('#errorFailedPopupModal').modal('show');
            });
    });

    document.getElementById('reset-button').addEventListener('click', function () {
        document.getElementById('body_temp_caution_min').value = 34.5;
        document.getElementById('body_temp_caution_max').value = 38.5;
        document.getElementById('body_temp_danger_min').value = 33.5;
        document.getElementById('body_temp_danger_max').value = 39.5;
        document.getElementById('heart_rate_caution_min').value = 60;
        document.getElementById('heart_rate_caution_max').value = 120;
        document.getElementById('heart_rate_danger_min').value = 50;
        document.getElementById('heart_rate_danger_max').value = 160;
        document.getElementById('respiratory_rate_caution_min').value = 15;
        document.getElementById('respiratory_rate_caution_max').value = 22;
        document.getElementById('respiratory_rate_danger_min').value = 10;
        document.getElementById('respiratory_rate_danger_max').value = 30;
        document.getElementById('blood_pressure_systolic_caution_min').value = 60;
        document.getElementById('blood_pressure_systolic_caution_max').value = 140;
        document.getElementById('blood_pressure_diastolic_caution_min').value = 50;
        document.getElementById('blood_pressure_diastolic_caution_max').value = 160;
        document.getElementById('blood_pressure_systolic_danger_min').value = 50;
        document.getElementById('blood_pressure_systolic_danger_max').value = 100;
        document.getElementById('blood_pressure_diastolic_danger_min').value = 40;
        document.getElementById('blood_pressure_diastolic_danger_max').value = 120;
        document.getElementById('oxygen_saturation_caution_min').value = 95;
        document.getElementById('oxygen_saturation_caution_max').value = 85;
        document.getElementById('oxygen_saturation_danger_min').value = 90;
        document.getElementById('oxygen_saturation_danger_max').value = 90;
        document.getElementById('external_temp_default_value').value = 35;
        document.getElementById('ecg_caution_min').value = 101;
        document.getElementById('ecg_caution_max').value = 110;
        document.getElementById('ecg_danger_min').value = 111;
        document.getElementById('ecg_danger_max').value = 130;
    });

    document.getElementById('reset').addEventListener('click', function () {
        window.location.reload()
    });
</script>