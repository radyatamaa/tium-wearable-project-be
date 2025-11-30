<?php

namespace App\Services;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use App\Models\Scopes\UserGeneralPurposeScope;
use App\Models\DevicesSmartWatchGeneralPurpose;
use App\Models\DevicesSmartWatchPublicWelfare;
use Illuminate\Http\Request;
use App\Models\DevicesHospital;

class DeviceService
{
    public static function checkDeviceInOtherSide($deviceCode)
    {
        $deviceOtherHospitalSide = DevicesHospital::withoutGlobalScope(UserGeneralPurposeScope::class)
            ->where(DB::raw('LOWER(device_code)'), strtolower($deviceCode))->first();
        if ($deviceOtherHospitalSide) {
            return [
                'success' => false,
                'message' => config('app.lang') != 'en' ? '해당 기기 코드는 이미 다른 병원에서 사용 중입니다.' : 'The device code is already in use at another hospital.',
                'data' => null
            ];
        }

        $deviceOtherGeneralPurposeSide = DevicesSmartWatchGeneralPurpose::withoutGlobalScope(UserGeneralPurposeScope::class)
            ->where(DB::raw('LOWER(serial)'), strtolower($deviceCode))->first();
        if ($deviceOtherGeneralPurposeSide) {
            return [
                'success' => false,
                'message' => config('app.lang') != 'en' ? '해당 장치 코드는 이미 다른 산업 부분에서 사용되고 있습니다.' : 'The device code is already in use in other industrial sectors.',
                'data' => null
            ];
        }

        $deviceOtherPublicWelfareSide = DevicesSmartWatchPublicWelfare::withoutGlobalScope(UserGeneralPurposeScope::class)
            ->where(DB::raw('LOWER(serial)'), strtolower($deviceCode))->first();
        if ($deviceOtherPublicWelfareSide) {
            return [
                'success' => false,
                'message' => config('app.lang') != 'en' ? '해당 기기코드는 이미 다른 공공복지 부문에서 사용되고 있습니다.' : 'The device code is already being used in other public welfare sectors.',
                'data' => null
            ];
        }


        return [
            'success' => true,
            'message' => 'success',
            'data' => null,
        ];
    }
}