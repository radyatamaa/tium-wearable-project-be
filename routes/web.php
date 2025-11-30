<?php

use App\Http\Controllers\CSCenterController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SettingJobTitleController;
use App\Http\Controllers\SettingDetailedFieldController;
use App\Http\Controllers\SettingServiceUsageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SalesManagementController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\InquiryForUseController;
use App\Http\Controllers\AdminManagementController;
use App\Http\Controllers\Auth\PublicHealthCenterAuthController;
use App\Http\Controllers\Auth\HospitalAuthController;
use App\Http\Controllers\Auth\GeneralPurposeAuthController;
use App\Http\Controllers\Auth\PublicWelfareAuthController;
use App\Http\Middleware\ClearSessionOnLogin;

Route::get('/download-app', [App\Http\Controllers\HelperController::class, 'downloadApk']);


Route::get('/', function () {
    return view('welcome');
});

Route::get('/oauth-callback/kakao', function () {
    return view('welcome');
});
Route::get('/oauth-callback/naver', function () {
    return view('welcome');
});

Route::get('/login-sso-test', [App\Http\Controllers\CSCenterController::class, 'showLoginSSOForm'])->name('cscenter.showLoginSSOForm');
Route::get('/cscenter', [App\Http\Controllers\CSCenterController::class, 'showCSForm'])->name('cscenter.showCSForm');
Route::post('/cscenter/submit', [App\Http\Controllers\CSCenterController::class, 'submitCSForm'])->name('cscenter.submitCSForm');

// Public Health Center Authentication Routes
Route::middleware([ClearSessionOnLogin::class])->group(function () {
    Route::get('/public-health-center/login', [PublicHealthCenterAuthController::class, 'showLoginForm'])->name('public-health-center.login');
    Route::post('/public-health-center/login', [PublicHealthCenterAuthController::class, 'login']);

    Route::get('/hospital/login', [HospitalAuthController::class, 'showLoginForm'])->name('hospital.login');
    Route::post('/hospital/login', [HospitalAuthController::class, 'login']);

    Route::get('/general-purpose/login', [GeneralPurposeAuthController::class, 'showLoginForm'])->name('general-purpose.login');
    Route::post('/general-purpose/login', [GeneralPurposeAuthController::class, 'login']);

    Route::get('/public-welfare/login', [PublicWelfareAuthController::class, 'showLoginForm'])->name('public-welfare.login');
    Route::post('/public-welfare/login', [PublicWelfareAuthController::class, 'login']);
});

Route::post('/public-health-center/token-login', [PublicHealthCenterAuthController::class, 'loginWithToken']);
Route::post('/public-health-center/logout', [PublicHealthCenterAuthController::class, 'logout'])->name('public-health-center.logout');
Route::post('/public-health-center/token-logout', [PublicHealthCenterAuthController::class, 'logoutWithToken']);

// Hospital Authentication Routes

Route::post('/hospital/token-login', [HospitalAuthController::class, 'loginWithToken']);
Route::post('/hospital/logout', [HospitalAuthController::class, 'logout'])->name('hospital.logout');
Route::post('/hospital/token-logout', [HospitalAuthController::class, 'logoutWithToken']);

// General Purpose Authentication Routes

Route::post('/general-purpose/token-login', [GeneralPurposeAuthController::class, 'loginWithToken']);
Route::post('/general-purpose/logout', [GeneralPurposeAuthController::class, 'logout'])->name('general-purpose.logout');
Route::post('/general-purpose/token-logout', [GeneralPurposeAuthController::class, 'logoutWithToken']);

// General Purpose Authentication Routes
Route::post('/public-welfare/token-login', [PublicWelfareAuthController::class, 'loginWithToken']);
Route::post('/public-welfare/logout', [PublicWelfareAuthController::class, 'logout'])->name('public-welfare.logout');
Route::post('/public-welfare/token-logout', [PublicWelfareAuthController::class, 'logoutWithToken']);

// export data
Route::get('/hospital/patient-management/inpatients/export', [App\Http\Controllers\InpatientsController::class, 'downloadExcel']);
Route::get('/hospital/patient-management/patient/export-data-vital-per-day/{id}', [App\Http\Controllers\InpatientsController::class, 'downloadExcelDataVital']);

Route::get('/hospital/patient-management/discharged/export', [App\Http\Controllers\DischargedPatientsController::class, 'downloadExcel']);
Route::get('sales-management/export', [SalesManagementController::class, 'downloadExcel']);
Route::get('/hospital/history/emergency-alerts/export', [App\Http\Controllers\EmergencyAlertsController::class, 'downloadExcel']);
Route::get('/hospital/history/patient-calls/export', [App\Http\Controllers\PatientCallsController::class, 'downloadExcel']);
Route::get('/hospital/device-management/export', [App\Http\Controllers\DeviceManagementController::class, 'downloadExcel']);
Route::get('/hospital/settings/location-management/top-location/export', [App\Http\Controllers\LocationManagementController::class, 'topDownloadExcel']);
Route::get('/hospital/settings/location-management/sub-location/export', [App\Http\Controllers\LocationManagementController::class, 'subDownloadExcel']);
Route::get('/hospital/settings/location-management/detailed-location/export', [App\Http\Controllers\LocationManagementController::class, 'DetailedDownloadExcel']);
Route::get('/hospital/settings/account-management/export', [App\Http\Controllers\AccountManagementController::class, 'downloadExcel']);
Route::get('/hospital/settings/staff-management/export', [App\Http\Controllers\StaffManagementController::class, 'downloadExcel']);
Route::get('/public-welfare/patient-management/history/download-pdf/{id}', [App\Http\Controllers\PatientManagementPublicWelfareController::class, 'downloadMeasurementInformationPDF'])->name('patient_management.downloadMeasurementInformationPDF');
Route::get('/public-welfare/patient-management/history/download-excel/{id}', [App\Http\Controllers\PatientManagementPublicWelfareController::class, 'downloadMeasurementInformationExcel'])->name('patient_management.downloadMeasurementInformationExcel');
Route::get('/general-purpose/patient-management/history/download-pdf/{id}', [App\Http\Controllers\PatientManagementGeneralPurposeController::class, 'downloadMeasurementInformationPDF'])->name('patient_management.downloadMeasurementInformationPDF');
Route::get('/general-purpose/patient-management/history/download-excel/{id}', [App\Http\Controllers\PatientManagementGeneralPurposeController::class, 'downloadMeasurementInformationExcel'])->name('patient_management.downloadMeasurementInformationExcel');

// Protected Routes for Public Health Center
Route::group(['middleware' => ['auth:publicHealthCenter', 'public-health-center']], function () {
    Route::get('/public-health-center/check-session', function () {
        return response()->json([
            'authenticated' => true,
        ]);
    });
    Route::get('home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('icons', ['as' => 'pages.icons', 'uses' => 'App\Http\Controllers\PageController@icons']);
    Route::get('maps', ['as' => 'pages.maps', 'uses' => 'App\Http\Controllers\PageController@maps']);
    Route::get('notifications', ['as' => 'pages.notifications', 'uses' => 'App\Http\Controllers\PageController@notifications']);
    Route::get('rtl', ['as' => 'pages.rtl', 'uses' => 'App\Http\Controllers\PageController@rtl']);
    Route::get('tables', ['as' => 'pages.tables', 'uses' => 'App\Http\Controllers\PageController@tables']);
    Route::get('typography', ['as' => 'pages.typography', 'uses' => 'App\Http\Controllers\PageController@typography']);
    Route::get('upgrade', ['as' => 'pages.upgrade', 'uses' => 'App\Http\Controllers\PageController@upgrade']);

    Route::resource('user', 'App\Http\Controllers\UserController', ['except' => ['show']]);
    Route::get('user/{id}', ['as' => 'profile.edit', 'uses' => 'App\Http\Controllers\UserController@edit']);
    Route::put('user/{id}', [UserController::class, 'update'])->name('user.update');
    Route::post('user/delete', [UserController::class, 'deleteMultiple'])->name('user.deleteMultiple');
    Route::get('user/postal-code/{id}', [UserController::class, 'findPostalCode'])->name('user.findPostalCode');
    Route::get('user/auto-login-for-user/{id}', [UserController::class, 'getLoginAccessForUser'])->name('user.getLoginAccessForUser');
    Route::get('user/auto-login-for-management/{id}', [UserController::class, 'getLoginAccessForManagement'])->name('user.getLoginAccessForManagement');
    Route::get('user/district/{id}', [UserController::class, 'getDistrictByCityId'])->name('user.getDistrictByCityId');
    Route::get('user/list/filter', [UserController::class, 'getUserList'])->name('user.getUserList');

    Route::get('profile', ['as' => 'profile.edit', 'uses' => 'App\Http\Controllers\ProfileController@edit']);
    Route::put('profile', ['as' => 'profile.update', 'uses' => 'App\Http\Controllers\ProfileController@update']);
    Route::put('profile/password', ['as' => 'profile.password', 'uses' => 'App\Http\Controllers\ProfileController@password']);

    Route::get('setting-job-title', ['as' => 'setting-job-title.edit', 'uses' => 'App\Http\Controllers\SettingJobTitleController@edit']);
    Route::post('setting-job-title', [SettingJobTitleController::class, 'store'])->name('setting_job_title.store');
    Route::get('setting-job-title-list', [App\Http\Controllers\SettingJobTitleController::class, 'list']);

    Route::get('setting-detailed-field', ['as' => 'setting-detailed-field.edit', 'uses' => 'App\Http\Controllers\SettingDetailedFieldController@edit']);
    Route::post('setting-detailed-field', [SettingDetailedFieldController::class, 'store'])->name('setting_detailed_field.store');

    Route::get('setting-service-usage', ['as' => 'setting-service-usage.edit', 'uses' => 'App\Http\Controllers\SettingServiceUsageController@edit']);
    Route::post('setting-service-usage', [SettingServiceUsageController::class, 'store'])->name('setting_service_usage.store');
    Route::get('setting-service-usage-list', [App\Http\Controllers\SettingServiceUsageController::class, 'list']);

    Route::get('sales-management', ['as' => 'sales-management.index', 'uses' => 'App\Http\Controllers\SalesManagementController@index']);
    Route::get('/sales-management', [SalesManagementController::class, 'index'])->name('sales-management.index');
    Route::get('/sales-management/details/{date}', [SalesManagementController::class, 'details'])->name('sales-management.details');

    Route::get('notice', ['as' => 'notice.index', 'uses' => 'App\Http\Controllers\NoticeController@index']);
    Route::post('notice', [NoticeController::class, 'store'])->name('notice.store');
    Route::get('notice/{id}', [NoticeController::class, 'show'])->name('notice.show');
    Route::put('notice/{id}', [NoticeController::class, 'update'])->name('notice.update');
    Route::delete('notice/delete/{id}', [NoticeController::class, 'destroy'])->name('notice.destroy');
    Route::delete('notice/bulk-delete', [NoticeController::class, 'deleteSelected'])->name('notice.deleteSelected');

    Route::get('inquiry-for-use', ['as' => 'inquiry-for-use.index', 'uses' => 'App\Http\Controllers\InquiryForUseController@index']);
    Route::get('inquiry-for-use/{id}', [InquiryForUseController::class, 'edit'])->name('inquiry-for-use.edit');
    Route::post('inquiry-for-use/update/{id}', [InquiryForUseController::class, 'update'])->name('inquiry-for-use.update');
    Route::post('inquiry-for-use/delete/{id}', [InquiryForUseController::class, 'destroy'])->name('inquiry-for-use.destroy');
    Route::post('inquiry-for-use/bulk-delete', [InquiryForUseController::class, 'bulkDelete'])->name('inquiry-for-use.bulkDelete');

    Route::get('admin-management', ['as' => 'admin-management.index', 'uses' => 'App\Http\Controllers\AdminManagementController@index']);
    Route::post('admin-management', [AdminManagementController::class, 'store'])->name('admin-management.store');
    Route::get('admin-management/{id}', [AdminManagementController::class, 'show'])->name('admin-management.show');
    Route::put('admin-management/{id}', [AdminManagementController::class, 'update'])->name('admin-management.update');
    Route::delete('admin-management/delete/{id}', [AdminManagementController::class, 'destroy'])->name('admin-management.destroy');
    Route::post('admin-management/bulk-delete', [AdminManagementController::class, 'deleteSelected'])->name('admin-management.deleteSelected');


    Route::get('customer-center', ['as' => 'customer-center.index', 'uses' => 'App\Http\Controllers\CSCenterController@index']);
    Route::get('customer-center/{id}', [CSCenterController::class, 'edit'])->name('customer-center.edit');
    Route::post('customer-center/update/{id}', [CSCenterController::class, 'update'])->name('customer-center.update');
    Route::post('customer-center/delete/{id}', [CSCenterController::class, 'destroy'])->name('customer-center.destroy');
    Route::post('customer-center/bulk-delete', [CSCenterController::class, 'bulkDelete'])->name('customer-center.bulkDelete');

    Route::get('log-blood-pressure', ['as' => 'log-blood-pressure.index', 'uses' => 'App\Http\Controllers\MeasurementReportController@indexBloodPressure']);
    Route::get('log-heart-rate', ['as' => 'log-heart-rate.index', 'uses' => 'App\Http\Controllers\MeasurementReportController@indexHeartRate']);
    Route::get('log-oxygen', ['as' => 'log-oxygen.index', 'uses' => 'App\Http\Controllers\MeasurementReportController@indexOxygen']);
    Route::get('log-temperature', ['as' => 'log-temperature.index', 'uses' => 'App\Http\Controllers\MeasurementReportController@indexTemperature']);
    Route::get('log-patient-call-hospital', ['as' => 'log-patient-call-hospital.index', 'uses' => 'App\Http\Controllers\MeasurementReportController@indexPatientCallHospital']);
    Route::get('log-patient-call-public-welfare', ['as' => 'log-patient-call-public-welfare.index', 'uses' => 'App\Http\Controllers\MeasurementReportController@indexPatientCallPublicWelfare']);
    Route::get('patient-report', ['as' => 'patient-report.index', 'uses' => 'App\Http\Controllers\MeasurementReportController@indexPatientReport']);
    Route::get('patient-report/export-data/{id}', [App\Http\Controllers\MeasurementReportController::class, 'downloadExcelDataVital']);


});

// Protected Routes for Hospital
Route::group(['middleware' => ['auth:hospital,hospitalStaff', 'hospital', 'hospitalStaff']], function () {
    Route::get('/hospital/check-session', function () {
        return response()->json([
            'authenticated' => true,
        ]);
    });
    Route::get('/hospital/home', [App\Http\Controllers\HomeHospitalController::class, 'index'])->name('hospital.home');
    Route::get('/hospital/nav-data', [App\Http\Controllers\HomeHospitalController::class, 'navData']);
    Route::get('/hospital/home/emergency', [App\Http\Controllers\HomeHospitalController::class, 'emergencyList']);
    Route::get('/hospital/home/patient-call', [App\Http\Controllers\HomeHospitalController::class, 'patientCallList']);
    Route::get('/hospital/home/summary-count', [App\Http\Controllers\HomeHospitalController::class, 'summaryCount']);

    Route::get('/hospital/patient-management/inpatients', [App\Http\Controllers\InpatientsController::class, 'index'])->name('hospital.patient_management.inpatients');
    Route::get('/hospital/patient-management/inpatients/{id}', [App\Http\Controllers\InpatientsController::class, 'show']);
    Route::post('/hospital/patient-management/inpatients', [App\Http\Controllers\InpatientsController::class, 'store']);
    Route::put('/hospital/patient-management/inpatients/{id}', [App\Http\Controllers\InpatientsController::class, 'update']);
    Route::delete('/hospital/patient-management/inpatients', [App\Http\Controllers\InpatientsController::class, 'destroy']);
    Route::post('/hospital/patient-management/inpatients/set-measurement-patient', [App\Http\Controllers\InpatientsController::class, 'setSettingMeasurement']);
    Route::get('/hospital/patient-management/inpatients/body-measurment-info/{id}', [App\Http\Controllers\InpatientsController::class, 'getChartData']);
    Route::get('/hospital/patient-management/inpatients/body-measurment-info-table/{id}', [App\Http\Controllers\InpatientsController::class, 'getChartDataTable']);
    Route::post('/hospital/patient-management/inpatients/discharge', [App\Http\Controllers\InpatientsController::class, 'dischargePatient']);
    Route::post('/hospital/patient-management/inpatients/return-inpatient', [App\Http\Controllers\InpatientsController::class, 'returnInPatient']);

    Route::get('/hospital/patient-management/inpatients/emergency-history/{id}', [App\Http\Controllers\InpatientsController::class, 'emergencyHistoryPatient']);
    Route::get('/hospital/patient-management/inpatients/emergency-history/detail/{id}', [App\Http\Controllers\InpatientsController::class, 'detailEmergencyHistoryPatient']);
    Route::get('/hospital/patient-management/inpatients/patient-call-history/{id}', [App\Http\Controllers\InpatientsController::class, 'patientCallHistoryPatient']);
    Route::get('/hospital/patient-management/inpatients/patient-call-history/detail/{id}', [App\Http\Controllers\InpatientsController::class, 'detailPatientCallHistoryPatient']);
    Route::get('/hospital/patient-management/devices', [App\Http\Controllers\InpatientsController::class, 'getDevices']);

    Route::get('/hospital/patient-management/discharged', [App\Http\Controllers\DischargedPatientsController::class, 'index'])->name('hospital.patient_management.discharged');
    Route::get('/hospital/patient-management/discharged/{id}', [App\Http\Controllers\DischargedPatientsController::class, 'show']);
    Route::delete('/hospital/patient-management/discharged', [App\Http\Controllers\DischargedPatientsController::class, 'destroy']);
    Route::post('/hospital/patient-management/discharged/set-measurement-patient', [App\Http\Controllers\DischargedPatientsController::class, 'setSettingMeasurement']);



    Route::get('/hospital/history/emergency-alerts', [App\Http\Controllers\EmergencyAlertsController::class, 'index'])->name('hospital.history.emergency_alerts');
    Route::get('/hospital/history/emergency-alerts/{id}', [App\Http\Controllers\EmergencyAlertsController::class, 'show']);
    Route::delete('/hospital/history/emergency-alerts', [App\Http\Controllers\EmergencyAlertsController::class, 'destroy']);
    Route::post('/hospital/history/emergency-alerts/content/{id}', [App\Http\Controllers\EmergencyAlertsController::class, 'createContent']);

    Route::get('/hospital/history/patient-calls', [App\Http\Controllers\PatientCallsController::class, 'index'])->name('hospital.history.patient_calls');
    Route::get('/hospital/history/patient-calls/{id}', [App\Http\Controllers\PatientCallsController::class, 'show']);
    Route::delete('/hospital/history/patient-calls', [App\Http\Controllers\PatientCallsController::class, 'destroy']);
    Route::post('/hospital/history/patient-calls/content/{id}', [App\Http\Controllers\PatientCallsController::class, 'createContent']);

    Route::get('/hospital/device-management', [App\Http\Controllers\DeviceManagementController::class, 'index'])->name('hospital.device_management');
    Route::get('/hospital/device-management/{id}', [App\Http\Controllers\DeviceManagementController::class, 'show'])->name('hospital.device_management');
    Route::post('/hospital/device-management', [App\Http\Controllers\DeviceManagementController::class, 'store'])->name('hospital.device_management');
    Route::delete('/hospital/device-management', [App\Http\Controllers\DeviceManagementController::class, 'destroy'])->name('hospital.device_management');


    Route::get('/hospital/settings/location-management/top-location', [App\Http\Controllers\LocationManagementController::class, 'topIndex'])->name('hospital.settings.location-management.top-location');
    Route::get('/hospital/settings/location-management/top-location/{id}', [App\Http\Controllers\LocationManagementController::class, 'topShow'])->name('hospital.settings.location-management.top-location');
    Route::post('/hospital/settings/location-management/top-location', [App\Http\Controllers\LocationManagementController::class, 'topStore'])->name('hospital.settings.location-management.top-location');
    Route::delete('/hospital/settings/location-management/top-location', [App\Http\Controllers\LocationManagementController::class, 'topDestroy'])->name('hospital.settings.location-management.top-location');

    Route::get('/hospital/settings/location-management/sub-location', [App\Http\Controllers\LocationManagementController::class, 'subIndex'])->name('hospital.settings.location-management.sub-location');
    Route::get('/hospital/settings/location-management/sub-location/{id}', [App\Http\Controllers\LocationManagementController::class, 'subShow'])->name('hospital.settings.location-management.sub-location');
    Route::post('/hospital/settings/location-management/sub-location', [App\Http\Controllers\LocationManagementController::class, 'subStore'])->name('hospital.settings.location-management.sub-location');
    Route::delete('/hospital/settings/location-management/sub-location', [App\Http\Controllers\LocationManagementController::class, 'subDestroy'])->name('hospital.settings.location-management.sub-location');

    Route::get('/hospital/settings/location-management/detailed-location', [App\Http\Controllers\LocationManagementController::class, 'detailedIndex'])->name('hospital.settings.location-management.detailed-location');
    Route::get('/hospital/settings/location-management/detailed-location/{id}', [App\Http\Controllers\LocationManagementController::class, 'detailedShow'])->name('hospital.settings.location-management.detailed-location');
    Route::post('/hospital/settings/location-management/detailed-location', [App\Http\Controllers\LocationManagementController::class, 'detailedStore'])->name('hospital.settings.location-management.detailed-location');
    Route::delete('/hospital/settings/location-management/detailed-location', [App\Http\Controllers\LocationManagementController::class, 'detailedDestroy'])->name('hospital.settings.location-management.detailed-location');


    Route::get('/hospital/settings/account-management', [App\Http\Controllers\AccountManagementController::class, 'index'])->name('hospital.settings.account_management');
    Route::get('/hospital/settings/account-management/{id}', [App\Http\Controllers\AccountManagementController::class, 'show'])->name('hospital.settings.account_management');
    Route::post('/hospital/settings/account-management', [App\Http\Controllers\AccountManagementController::class, 'store'])->name('hospital.settings.account_management');
    Route::delete('/hospital/settings/account-management', [App\Http\Controllers\AccountManagementController::class, 'destroy'])->name('hospital.settings.account_management');

    Route::get('/hospital/settings/staff-management', [App\Http\Controllers\StaffManagementController::class, 'index'])->name('hospital.staff_management');
    Route::get('/hospital/settings/staff-management/{id}', [App\Http\Controllers\StaffManagementController::class, 'show'])->name('hospital.settings.staff_management');
    Route::post('/hospital/settings/staff-management', [App\Http\Controllers\StaffManagementController::class, 'store'])->name('hospital.settings.staff_management');
    Route::post('/hospital/settings/staff-management/update/{id}', [App\Http\Controllers\StaffManagementController::class, 'update'])->name('hospital.settings.staff_management');

    Route::delete('/hospital/settings/staff-management', [App\Http\Controllers\StaffManagementController::class, 'destroy'])->name('hospital.settings.staff_management');
});


// Protected Routes for General Purpose
Route::group(['middleware' => ['auth:generalPurpose', 'general-purpose']], function () {
    Route::get('/general-purpose/check-session', function () {
        return response()->json([
            'authenticated' => true,
        ]);
    });
    Route::get('/general-purpose/home', [App\Http\Controllers\HomeGeneralPurposeController::class, 'index'])->name('general-purpose.home');
    Route::get('/general-purpose/nav-data', [App\Http\Controllers\HomeGeneralPurposeController::class, 'navData']);
    Route::post('/general-purpose/change-account-password', [App\Http\Controllers\HomeGeneralPurposeController::class, 'changePassword'])->name('general-purpose.changePassword');
    Route::get('/general-purpose/home/user-management', [App\Http\Controllers\HomeGeneralPurposeController::class, 'userManagementList']);
    Route::get('/general-purpose/home/patient-call', [App\Http\Controllers\HomeGeneralPurposeController::class, 'patientCallList']);

    Route::get('/general-purpose/setting/alarm', [App\Http\Controllers\SettingGeneralPurposeController::class, 'alarmSetting'])->name('setting.index');
    Route::post('/general-purpose/setting/alarm', [App\Http\Controllers\SettingGeneralPurposeController::class, 'setAlarmSetting'])->name('setting.set_alarm');

    Route::get('/general-purpose/setting/smart-watch', [App\Http\Controllers\SettingGeneralPurposeController::class, 'smartWatch'])->name('setting.index');
    Route::get('/general-purpose/setting/smart-watch/{id}', [App\Http\Controllers\SettingGeneralPurposeController::class, 'smartWatchShow'])->name('setting.smartWatchShow');
    Route::put('/general-purpose/setting/smart-watch/{id}', [App\Http\Controllers\SettingGeneralPurposeController::class, 'smartWatchUpdate'])->name('setting.smartWatchUpdate');
    Route::post('/general-purpose/setting/smart-watch', [App\Http\Controllers\SettingGeneralPurposeController::class, 'smartWatchStore'])->name('setting.smartWatchStore');
    Route::delete('/general-purpose/setting/smart-watch', [App\Http\Controllers\SettingGeneralPurposeController::class, 'smartWatchDestroy'])->name('setting.smartWatchDestroy');

    Route::get('/general-purpose/setting/admin-management', [App\Http\Controllers\SettingGeneralPurposeController::class, 'adminManagement'])->name('setting.index');
    Route::get('/general-purpose/setting/admin-management/{id}', [App\Http\Controllers\SettingGeneralPurposeController::class, 'adminManagementShow'])->name('setting.adminManagementShow');
    Route::put('/general-purpose/setting/admin-management/{id}', [App\Http\Controllers\SettingGeneralPurposeController::class, 'adminManagementUpdate'])->name('setting.adminManagementUpdate');
    Route::post('/general-purpose/setting/admin-management', [App\Http\Controllers\SettingGeneralPurposeController::class, 'adminManagementStore'])->name('setting.adminManagementStore');
    Route::post('/general-purpose/setting/admin-management/change-password/{id}', [App\Http\Controllers\SettingGeneralPurposeController::class, 'adminManagementChangePassword'])->name('setting.adminManagementChangePassword');
    Route::delete('/general-purpose/setting/admin-management', [App\Http\Controllers\SettingGeneralPurposeController::class, 'adminManagementDestroy'])->name('setting.adminManagementDestroy');

    Route::get('/general-purpose/setting/user-management', [App\Http\Controllers\SettingGeneralPurposeController::class, 'userManagement'])->name('setting.index');
    Route::get('/general-purpose/setting/user-management/{id}', [App\Http\Controllers\SettingGeneralPurposeController::class, 'userManagementShow'])->name('setting.userManagementShow');
    Route::put('/general-purpose/setting/user-management/{id}', [App\Http\Controllers\SettingGeneralPurposeController::class, 'userManagementUpdate'])->name('setting.userManagementUpdate');
    Route::post('/general-purpose/setting/user-management', [App\Http\Controllers\SettingGeneralPurposeController::class, 'userManagementStore'])->name('setting.userManagementStore');
    Route::delete('/general-purpose/setting/user-management', [App\Http\Controllers\SettingGeneralPurposeController::class, 'userManagementDestroy'])->name('setting.userManagementDestroy');
    Route::get('/general-purpose/setting/devices', [App\Http\Controllers\SettingGeneralPurposeController::class, 'getDevices']);


    Route::get('/general-purpose/emergency-history', [App\Http\Controllers\EmergencyHistoryGeneralPurposeController::class, 'index'])->name('emergeny_history.index');
    Route::get('/general-purpose/emergency-history/{id}', [App\Http\Controllers\EmergencyHistoryGeneralPurposeController::class, 'show'])->name('emergeny_history.show');
    Route::get('/general-purpose/emergency-history/user_id/{id}', [App\Http\Controllers\EmergencyHistoryGeneralPurposeController::class, 'getWithUserCustomerId'])->name('emergeny_history.getWithUserCustomerId');
    Route::get('/general-purpose/emergency-history-list', [App\Http\Controllers\EmergencyHistoryGeneralPurposeController::class, 'getEmergencyReport'])->name('emergeny_history.getEmergencyReport');
    Route::post('/general-purpose/emergency-history/content/{id}', [App\Http\Controllers\EmergencyHistoryGeneralPurposeController::class, 'createContent']);

    Route::get('/general-purpose/patient-management/{id}', [App\Http\Controllers\PatientManagementGeneralPurposeController::class, 'index'])->name('patient_management.index');
    Route::get('/general-purpose/patient-management/history/{id}', [App\Http\Controllers\PatientManagementGeneralPurposeController::class, 'getBodyMeasurementInformation'])->name('patient_management.getBodyMeasurementInformation');

    Route::get('/general-purpose/patient-call-history', [App\Http\Controllers\PatientCallsGeneralPurposeController::class, 'index'])->name('patient_call_history.index');
    Route::get('/general-purpose/patient-call-history/{id}', [App\Http\Controllers\PatientCallsGeneralPurposeController::class, 'show'])->name('patient_call_history.show');
    Route::get('/general-purpose/patient-call-history/user_id/{id}', [App\Http\Controllers\PatientCallsGeneralPurposeController::class, 'getWithUserCustomerId'])->name('patient_call_history.getWithUserCustomerId');
    Route::get('/general-purpose/patient-call-history-list', [App\Http\Controllers\PatientCallsGeneralPurposeController::class, 'getPatientCallReport'])->name('patient_call_history.getWithUserCustomerId');
    Route::post('/general-purpose/patient-call-history/content/{id}', [App\Http\Controllers\PatientCallsGeneralPurposeController::class, 'createContent']);

});


// Protected Routes for Public Welfare
Route::group(['middleware' => ['auth:publicWelfare', 'public-welfare']], function () {
    Route::get('/public-welfare/check-session', function () {
        return response()->json([
            'authenticated' => true,
        ]);
    });
    Route::get('/public-welfare/home', [App\Http\Controllers\HomePublicWelfareController::class, 'index'])->name('public-welfare.home');
    Route::get('/public-welfare/nav-data', [App\Http\Controllers\HomePublicWelfareController::class, 'navData']);
    Route::post('/public-welfare/change-account-password', [App\Http\Controllers\HomePublicWelfareController::class, 'changePassword'])->name('general-purpose.changePassword');
    Route::get('/public-welfare/home/user-management', [App\Http\Controllers\HomePublicWelfareController::class, 'userManagementList']);
    Route::get('/public-welfare/home/patient-call', [App\Http\Controllers\HomePublicWelfareController::class, 'patientCallList']);

    Route::get('/public-welfare/setting/alarm', [App\Http\Controllers\SettingPublicWelfareController::class, 'alarmSetting']);
    Route::post('/public-welfare/setting/alarm', [App\Http\Controllers\SettingPublicWelfareController::class, 'setAlarmSetting']);

    Route::get('/public-welfare/setting/smart-watch', [App\Http\Controllers\SettingPublicWelfareController::class, 'smartWatch'])->name('setting.index');
    Route::get('/public-welfare/setting/smart-watch/{id}', [App\Http\Controllers\SettingPublicWelfareController::class, 'smartWatchShow'])->name('setting.smartWatchShow');
    Route::put('/public-welfare/setting/smart-watch/{id}', [App\Http\Controllers\SettingPublicWelfareController::class, 'smartWatchUpdate'])->name('setting.smartWatchUpdate');
    Route::post('/public-welfare/setting/smart-watch', [App\Http\Controllers\SettingPublicWelfareController::class, 'smartWatchStore'])->name('setting.smartWatchStore');
    Route::delete('/public-welfare/setting/smart-watch', [App\Http\Controllers\SettingPublicWelfareController::class, 'smartWatchDestroy'])->name('setting.smartWatchDestroy');

    Route::get('/public-welfare/setting/admin-management', [App\Http\Controllers\SettingPublicWelfareController::class, 'adminManagement'])->name('setting.index');
    Route::get('/public-welfare/setting/admin-management/{id}', [App\Http\Controllers\SettingPublicWelfareController::class, 'adminManagementShow'])->name('setting.adminManagementShow');
    Route::put('/public-welfare/setting/admin-management/{id}', [App\Http\Controllers\SettingPublicWelfareController::class, 'adminManagementUpdate'])->name('setting.adminManagementUpdate');
    Route::post('/public-welfare/setting/admin-management', [App\Http\Controllers\SettingPublicWelfareController::class, 'adminManagementStore'])->name('setting.adminManagementStore');
    Route::post('/public-welfare/setting/admin-management/change-password/{id}', [App\Http\Controllers\SettingPublicWelfareController::class, 'adminManagementChangePassword'])->name('setting.adminManagementChangePassword');
    Route::delete('/public-welfare/setting/admin-management', [App\Http\Controllers\SettingPublicWelfareController::class, 'adminManagementDestroy'])->name('setting.adminManagementDestroy');

    Route::get('/public-welfare/setting/ward', [App\Http\Controllers\SettingPublicWelfareController::class, 'ward'])->name('setting.index');
    Route::get('/public-welfare/setting/ward/{id}', [App\Http\Controllers\SettingPublicWelfareController::class, 'wardShow'])->name('setting.wardShow');
    Route::put('/public-welfare/setting/ward/{id}', [App\Http\Controllers\SettingPublicWelfareController::class, 'wardUpdate'])->name('setting.wardUpdate');
    Route::post('/public-welfare/setting/ward', [App\Http\Controllers\SettingPublicWelfareController::class, 'wardStore'])->name('setting.wardStore');
    Route::delete('/public-welfare/setting/ward', [App\Http\Controllers\SettingPublicWelfareController::class, 'wardDestroy'])->name('setting.wardDestroy');

    Route::get('/public-welfare/setting/staff-management', [App\Http\Controllers\SettingPublicWelfareController::class, 'staffManagement'])->name('setting.index');
    Route::get('/public-welfare/setting/staff-management/{id}', [App\Http\Controllers\SettingPublicWelfareController::class, 'staffManagementShow'])->name('setting.staffManagementShow');
    Route::post('/public-welfare/setting/staff-management/{id}', [App\Http\Controllers\SettingPublicWelfareController::class, 'staffManagementUpdate'])->name('setting.staffManagementUpdate');
    Route::post('/public-welfare/setting/staff-management', [App\Http\Controllers\SettingPublicWelfareController::class, 'staffManagementStore'])->name('setting.staffManagementStore');
    Route::post('/public-welfare/setting/staff-management/change-password/{id}', [App\Http\Controllers\SettingPublicWelfareController::class, 'staffManagementChangePassword'])->name('setting.staffManagementChangePassword');
    Route::delete('/public-welfare/setting/staff-management', [App\Http\Controllers\SettingPublicWelfareController::class, 'staffManagementDestroy'])->name('setting.staffManagementDestroy');


    Route::get('/public-welfare/setting/user-management', [App\Http\Controllers\SettingPublicWelfareController::class, 'userManagement'])->name('setting.index');
    Route::get('/public-welfare/setting/user-management/{id}', [App\Http\Controllers\SettingPublicWelfareController::class, 'userManagementShow'])->name('setting.userManagementShow');
    Route::put('/public-welfare/setting/user-management/{id}', [App\Http\Controllers\SettingPublicWelfareController::class, 'userManagementUpdate'])->name('setting.userManagementUpdate');
    Route::post('/public-welfare/setting/user-management', [App\Http\Controllers\SettingPublicWelfareController::class, 'userManagementStore'])->name('setting.userManagementStore');
    Route::delete('/public-welfare/setting/user-management', [App\Http\Controllers\SettingPublicWelfareController::class, 'userManagementDestroy'])->name('setting.userManagementDestroy');
    Route::get('/public-welfare/setting/devices', [App\Http\Controllers\SettingPublicWelfareController::class, 'getDevices'])->name('setting.getDevices');


    Route::get('/public-welfare/emergency-history', [App\Http\Controllers\EmergencyHistoryPublicWelfareController::class, 'index'])->name('emergeny_history.index');
    Route::get('/public-welfare/emergency-history/{id}', [App\Http\Controllers\EmergencyHistoryPublicWelfareController::class, 'show'])->name('emergeny_history.show');
    Route::get('/public-welfare/emergency-history/user_id/{id}', [App\Http\Controllers\EmergencyHistoryPublicWelfareController::class, 'getWithUserCustomerId'])->name('emergeny_history.getWithUserCustomerId');
    Route::get('/public-welfare/emergency-history-list', [App\Http\Controllers\EmergencyHistoryPublicWelfareController::class, 'getEmergencyReport'])->name('emergeny_history.getEmergencyReport');
    Route::post('/public-welfare/emergency-history/content/{id}', [App\Http\Controllers\EmergencyHistoryPublicWelfareController::class, 'createContent']);

    Route::get('/public-welfare/patient-management/{id}', [App\Http\Controllers\PatientManagementPublicWelfareController::class, 'index'])->name('patient_management.index');
    Route::get('/public-welfare/patient-management/history/{id}', [App\Http\Controllers\PatientManagementPublicWelfareController::class, 'getBodyMeasurementInformation'])->name('patient_management.getBodyMeasurementInformation');

    Route::get('/public-welfare/patient-call-history', [App\Http\Controllers\PatientCallsPublicWelfareController::class, 'index'])->name('patient_call_history.index');
    Route::get('/public-welfare/patient-call-history/{id}', [App\Http\Controllers\PatientCallsPublicWelfareController::class, 'show']);
    Route::get('/public-welfare/patient-call-history/user_id/{id}', [App\Http\Controllers\PatientCallsPublicWelfareController::class, 'getWithUserCustomerId'])->name('patient_call_history.getWithUserCustomerId');
    Route::get('/public-welfare/patient-call-history-list', [App\Http\Controllers\PatientCallsPublicWelfareController::class, 'getPatientCallReport'])->name('patient_call_history.getWithUserCustomerId');
    Route::post('/public-welfare/patient-call-history/content/{id}', [App\Http\Controllers\PatientCallsPublicWelfareController::class, 'createContent']);
});