<!DOCTYPE html>
<html>

<head>
    <title>Body Measurement Information</title>
    <style>
    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        border: 1px solid #000;
        padding: 8px;
        text-align: left;
    }
    </style>
</head>

<body>
    <h1>Body Measurement Information</h1>
    <table>
        <thead>
            <tr>
                <th>Time</th>
                <th>Heart Rate</th>
                <th>Body Temperature</th>
                <th>Blood Pressure</th>
                <th>Oxygen Saturation</th>
                <th>Respiratory Rate</th>
                <th>State</th>
            </tr>
        </thead>
        <tbody>
            @foreach($activities as $activity)
            <tr>
                <td>{{ $activity['time'] }}</td>
                <td>{{ $activity['heart_rate'] }}</td>
                <td>{{ $activity['body_temperature'] }}</td>
                <td>{{ $activity['blood_pressure'] }}</td>
                <td>{{ $activity['oxygen_saturation'] }}</td>
                <td>{{ $activity['respiratory_rate'] }}</td>
                <td>{{ $activity['state'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>