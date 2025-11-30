<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\HeartRateController;
use App\Http\Controllers\Api\SosHistoryCallController;
use App\Http\Controllers\Api\BloodPressureController;
use App\Http\Controllers\Api\OxygenSaturationController;
use App\Http\Controllers\Api\SleepController;
use App\Http\Controllers\Api\TemperatureController;
use App\Http\Controllers\Api\RespiratoryRateController;
use App\Http\Controllers\Api\MenstrualCycleController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\EcgController;
use App\Http\Controllers\Api\CaloriesController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::post('v1/gateway/login', [UserController::class, 'loginWithSecretKey']);
Route::post('v1/user/login', [UserController::class, 'login']);
Route::post('v1/user/register', [UserController::class, 'register']);
Route::get('v1/gateway/report/testcheck', [UserController::class, 'getReportActivity']);
Route::post('v1/fcm/save-token', [NotificationController::class, 'saveToken']);
Route::post('v1/user/check-email', [UserController::class, 'checkEmail']);
Route::post('v1/user/validate-sso-google', [UserController::class, 'validateSSOGoogle']);
Route::post('v1/user/validate-sso-naver', [UserController::class, 'validateSSONaver']);
Route::post('v1/user/validate-sso-kakao', [UserController::class, 'validateSSOKakao']);

Route::middleware('auth.api')->group(function () {
    Route::get('v1/user/profile', [UserController::class, 'getProfile']);
    Route::put('v1/user/profile', [UserController::class, 'update']);
    Route::put('v1/user/update-password', [UserController::class, 'updatePassword']);
    Route::get('v1/user/sos-contact', [UserController::class, 'getSOSContact']);
    Route::post('v1/user/sos-contact', [UserController::class, 'upSertSOSContact']);
    Route::delete('v1/user/sos-contact/{id}', [UserController::class, 'deleteSOSContact']);
    Route::get('v1/user/notification-range-setting', [UserController::class, 'getRangeSetting']);
    Route::post('v1/user/notification-range-setting', [UserController::class, 'setRangeSetting']);
    Route::get('v1/user/goal-setting', [UserController::class, 'getGoalsSetting']);
    Route::post('v1/user/goal-setting', [UserController::class, 'setGoalsSetting']);
    Route::get('v1/summary/activity-record', [UserController::class, 'getSummary']);
    Route::delete('v1/user/delete-account', [UserController::class, 'deleteAccount']);

    Route::get('v1/sos/call-history', [SosHistoryCallController::class, 'getSOSCallHistory']);
    Route::post('v1/sos/call-history', [SosHistoryCallController::class, 'createCallHistory']);

    Route::get('v1/heart-rate/summary', [HeartRateController::class, 'getSummaryHeartRate']);
    Route::post('v1/heart-rate/request', [HeartRateController::class, 'storeHearRateActivity']);
    Route::get('v1/heart-rate/history', [HeartRateController::class, 'getRecords']);

    Route::get('v1/blood-pressure/summary', [BloodPressureController::class, 'getSummaryBloodPressure']);
    Route::post('v1/blood-pressure/request', [BloodPressureController::class, 'storeBloodPressureActivity']);
    Route::get('v1/blood-pressure/history', [BloodPressureController::class, 'getRecords']);

    Route::get('v1/oxygen-saturation/summary', [OxygenSaturationController::class, 'getSummary']);
    Route::post('v1/oxygen-saturation/request', [OxygenSaturationController::class, 'storeActivity']);
    Route::get('v1/oxygen-saturation/history', [OxygenSaturationController::class, 'getRecords']);

    Route::get('v1/sleep/summary', [SleepController::class, 'getSummary']);
    Route::post('v1/sleep/request', [SleepController::class, 'storeActivity']);

    Route::get('v1/temperature/summary', [TemperatureController::class, 'getSummary']);
    Route::post('v1/temperature/request', [TemperatureController::class, 'storeActivity']);
    Route::get('v1/temperature/history', [TemperatureController::class, 'getRecords']);

    Route::get('v1/respiratory-rate/summary', [RespiratoryRateController::class, 'getSummary']);
    Route::post('v1/respiratory-rate/request', [RespiratoryRateController::class, 'store']);
    Route::get('v1/respiratory-rate/history', [RespiratoryRateController::class, 'getRecords']);

    Route::get('v1/menstrual-cycle/summary', [MenstrualCycleController::class, 'getSummary']);
    Route::post('v1/menstrual-cycle/request', [MenstrualCycleController::class, 'store']);

    Route::get('v1/ecg/summary', [EcgController::class, 'getSummaryEcg']);
    Route::post('v1/ecg/request', [EcgController::class, 'storeEcgActivity']);
    Route::get('v1/ecg/history', [EcgController::class, 'getRecords']);

    Route::post('v1/calories/request', [CaloriesController::class, 'store']);
    Route::get('v1/calories/summary', [CaloriesController::class, 'getSummary']);

    Route::post('v1/device/connect', [UserController::class, 'devicesConnect']);
    Route::post('v1/device/disconnect', [UserController::class, 'devicesDisconnect']);

    Route::post('v1/activities/request', [UserController::class, 'postActivities']);
});
Route::middleware('auth.api_gateway')->group(function () {
    Route::post('v1/gateway/report', [UserController::class, 'synchReportedActivity']);
    Route::post('v1/gateway/callsos', [UserController::class, 'sosCallGateway']);
});