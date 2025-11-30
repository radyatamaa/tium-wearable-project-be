<div class="measurement-info">
    <div class="measurement-header">
        <span class="measurement-title">체온</span>
        <div class="dropdown">
            <button class="dropdown-button" id="dropdownButton" onclick="handleDropdown(event)">
                체온 <span class="dot"></span>
            </button>
            <div class="dropdown-content" id="dropdownContent">
                <a href="#" data-measurement="temperature" onclick="handleDropdown(event)">체온</a>
                <a href="#" data-measurement="heart_rate" onclick="handleDropdown(event)">심박수</a>
                <a href="#" data-measurement="respiratory_rate" onclick="handleDropdown(event)">호흡수</a>
                <a href="#" data-measurement="blood_pressure" onclick="handleDropdown(event)">혈압</a>
                <a href="#" data-measurement="oxygen_saturation" onclick="handleDropdown(event)">산소</a>
                <a href="#" data-measurement="ecg" onclick="handleDropdown(event)">심전도</a>
            </div>
        </div>
    </div>
    <div class="chart-container" id="chartContainer" style="height: 400px; width: 100%;"></div>
    <div class="styled-table-general-purpose-container">
        <table class="styled-table-general-purpose" style="border-spacing:-10px-15px;">
            <thead>
                <tr>
                    <th>{{ config('app.lang') != 'en' ? __('측정시간') : __('Measurement time') }}</th>
                    <th>{{ config('app.lang') != 'en' ? __('체온') : __('Body temperature') }}</th>
                    <th>{{ config('app.lang') != 'en' ? __('심박수') : __('Heart rate') }}</th>
                    <th>{{ config('app.lang') != 'en' ? __('호흡수') : __('Respiratory rate') }}</th>
                    <th>{{ config('app.lang') != 'en' ? __('협압') : __('Blood pressure') }}</th>
                    <th>{{ config('app.lang') != 'en' ? __('산소') : __('Oxygen') }}</th>
                    <th>{{ config('app.lang') != 'en' ? __('심전도') : __('ECG') }}</th>
                    <th>{{ config('app.lang') != 'en' ? __('상태') : __('State') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['body_measurement_information'] as $data)
                <tr>
                    <td>{{$data['time'] }}</td>
                    <td>{{$data['body_temperature'] != null ? $data['body_temperature'] : 0}}</td>
                    <td>{{$data['heart_rate'] != null ? $data['heart_rate'] : 0}}</td>
                    <td>{{$data['respiratory_rate'] != null ? $data['respiratory_rate'] : 0}}</td>
                    <td>{{$data['blood_pressure'] != null ? $data['blood_pressure'] : 0}}</td>
                    <td>{{$data['oxygen_saturation'] != null ? $data['oxygen_saturation'] : 0}}</td>
                    <td>{{$data['ecg'] != null ? $data['ecg'] : 0}}</td>
                    <td>
                        @if($data['state'] == 'danger')
                        <button class="status-button danger" disabled>
                            위험</button>
                        @elseif(
                        $data['state'] == 'caution' || $data['state'] ==
                        'warning'
                        )
                        <button class="status-button caution" disabled>
                            주의</button>
                        @else
                        <button class="status-button normal" disabled>
                            정상</button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
function handleDropdown(event) {
    var dropdownContent = document.getElementById('dropdownContent');
    var dropdownButton = document.getElementById('dropdownButton');

    if (event.target.matches('.dropdown-button')) {
        // Toggle dropdown visibility
        dropdownContent.style.display = dropdownContent.style.display === 'block' ? 'none' : 'block';
    } else if (event.target.matches('.dropdown-content a')) {
        // Update button text and hide dropdown
        var selectedText = event.target.textContent || event.target.innerText;
        dropdownButton.innerHTML = selectedText + ' <span class="dot"></span>';
        dropdownContent.style.display = 'none';
    }

    // Close the dropdown if the user clicks outside of it
    window.onclick = function(e) {
        if (!e.target.matches('.dropdown-button') && !e.target.matches('.dropdown-content a')) {
            if (dropdownContent.style.display === 'block') {
                dropdownContent.style.display = 'none';
            }
        }
    };
}
</script>