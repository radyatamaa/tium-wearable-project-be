<?php

namespace App\Http\Controllers;

use App\Models\AdminPublicWelfare;
use App\Models\MasterDataCity;
use App\Models\MasterDataDistrict;
use App\Models\ServiceDetailedFieldSetting;
use App\Models\ServiceUsageSetting;
use App\Models\User;
use App\Http\Requests\UserRequest;
use App\Models\UserStaffHospital;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\UserGeneralPurpose;
use App\Models\SubscriptionInformationGeneralPurpose;
use App\Models\PersonInChargeInformationGeneralPurpose;
use App\Models\AdditionalInformationGeneralPurpose;
use App\Models\ServiceSubscriptionInformationGeneralPurpose;
use App\Models\UserAdminHospital;
use App\Models\AdminGeneralPurpose;
use App\Models\MemberStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the users
     *
     * @param  \App\Models\UserGeneralPurpose  $model
     * @return \Illuminate\View\View
     */
    public function index(Request $request, UserGeneralPurpose $model)
    {
        $serviceUsageSettings = ServiceUsageSetting::get();

        $typeFilter = $request->session()->get('type_filter');

        if ($request->query('type')) {
            $typeFilter = $request->query('type');
            $request->session()->put('type_filter', $typeFilter);
        }

        // Base query with conditions applied using whereHas
        $model = $model->whereHas('subscriptionInformation', function ($query) use ($request) {
            if ($request->filled('postalCodeAddress')) {
                $query->where('postal_code', $request->postalCodeAddress);
            }
            if ($request->filled('search')) {
                if ($request->search_by == 'name') {
                    $model = $query->where('organization_name', 'like', '%' . $request->search . '%');
                }
            }
        });

        $model = $model->whereHas('serviceSubscriptionInformation', function ($query) use ($request) {
            if ($request->filled('usage_status')) {
                if ($request->usage_status != 'all') {
                    $usageStatus = strtolower($request->usage_status);
                    $query->whereRaw('LOWER(subscription_service) LIKE ?', ["%$usageStatus%"]);
                }
            }

            if ($request->filled('start_date_range') && $request->filled('end_date_range')) {
                if ($request->start_date_range != 'YYYY-MM-DD' && $request->end_date_range != 'YYYY-MM-DD') {
                    $query->whereDate('start_date_of_use', '>=', $request->start_date_range)
                        ->whereDate('start_date_of_use', '<=', $request->end_date_range);
                }
            }

            if ($request->filled('expired_start_date') && $request->filled('expired_end_date')) {
                if ($request->expired_start_date != 'YYYY-MM-DD' && $request->expired_end_date != 'YYYY-MM-DD') {
                    $query->whereDate('expiration_date', '>=', $request->expired_start_date)
                        ->whereDate('expiration_date', '<=', $request->expired_end_date);
                }
            }
        });

        // Apply type filter to the base model query
        $model = $model->where('user_type', $typeFilter);

        // Apply search filters if provided
        if ($request->filled('search')) {
            if ($request->search_by == '') {
                $model = $model->where('user_id', 'like', '%' . $request->search . '%');
            }
        }

        // Paginate results
        $perPage = $request->input('limit', 10);
        $users = $model->orderBy('user_general_purposes.created_at', 'desc')->paginate($perPage);

        // Send data to view
        $cities = $this->getCity();
        return view('users.index', ['users' => $users, 'cities' => $cities, 'serviceUsageSettings' => $serviceUsageSettings]);
    }

    public function edit(Request $request, $id)
    {
        $serviceUsageSettings = ServiceUsageSetting::get();

        $user = UserGeneralPurpose::with(['subscriptionInformation', 'personInChargeInformation', 'additionalInformation', 'serviceSubscriptionInformation', 'MemberStatus'])->where('id', $id)->first();
        $file_attachments = [];
        if ($user->personInChargeInformation) {
            if ($user->personInChargeInformation->file_attachments) {
                $file_attachments = json_decode($user->personInChargeInformation->file_attachments, true);
            }
        }
        $selected_subjects = $user->subscriptionInformation ? explode(' | ', $user->subscriptionInformation->selected_subjects) : [];
        if ($user->password) {
            $user->password = '**********';
        }

        $subjects = [];
        $detailedSetting = ServiceDetailedFieldSetting::where('service_classification', $user->user_type)->first();
        if ($detailedSetting) {
            $subjects = explode(',', $detailedSetting->medical_subject);
        }

        $typeFilter = $request->session()->get('type_filter');

        if ($user->user_type) {
            $typeFilter = strtolower($user->user_type);
            $request->session()->put('type_filter', $typeFilter);
        }

        return view('users.edit', [
            'user' => $user,
            'file_attachments' => $file_attachments,
            'subjects' => $subjects,
            'serviceUsageSettings' => $serviceUsageSettings,
            'selected_subjects' => $selected_subjects,
        ]);
    }

    public function create(Request $request)
    {
        $typeFilter = $this->getUserType($request);
        $serviceUsageSettings = ServiceUsageSetting::get();

        $subjects = [];
        $detailedSetting = ServiceDetailedFieldSetting::where('service_classification', $typeFilter)->first();
        if ($detailedSetting) {
            $subjects = explode(',', $detailedSetting->medical_subject);
        }
        return view('users.create', ['subjects' => $subjects, 'serviceUsageSettings' => $serviceUsageSettings]);
    }
    public function getUserType(Request $request)
    {
        // Mendapatkan filter dari session jika ada
        $typeFilter = $request->session()->get('type_filter');

        // Memperbarui filter berdasarkan request baru
        if ($request->query('type')) {
            $typeFilter = $request->query('type');
            $request->session()->put('type_filter', $typeFilter);
        }

        switch ($typeFilter) {
            case "medical_institution":
                $typeFilter = 'MEDICAL_INSTITUTION';
                break;
            case "public_welfare":
                $typeFilter = 'PUBLIC_WELFARE';
                break;
            case "industrial_scene":
                $typeFilter = 'INDUSTRIAL_SCENE';
                break;
        }

        return $typeFilter;
    }

    public function store(Request $request)
    {
        // Start the transaction
        DB::beginTransaction();

        try {
            $typeFilter = $this->getUserType($request);
            $data = $request->all();

            $checkUserId = UserGeneralPurpose::where('user_type', $typeFilter)->where('user_id', $data['user_id'])->first();
            if ($checkUserId) {
                return response()->json(['success' => false, 'message' => config('app.lang') != 'en' ? '아이디가 이미 존재합니다.' : 'The ID already exists.', 'data' => null]);
            }
            $data['photo_profile'] = null;
            if ($request->hasFile('fileUpload')) {
                $path = $request->file('fileUpload')->store('photos', 'public');
                $data['photo_profile'] = $path;
            }

            // Create the user
            $password = '';
            if (isset($data['password'])) {
                $password = Hash::make($data['password']);
            }
            $user = UserGeneralPurpose::create([
                'nickname' => $data['nickname'],
                'address' => $data['fullAddress'],
                // change
                'receive_email' => $data['receive_email'] ?? false,
                'receive_sms' => $data['receive_sms'] ?? false,
                'data_output' => $data['data_output'] ?? false,
                //
                //   comment
                // 'member_status' => $data['member_status'] ?? '베이직',

                'user_type' => $typeFilter,
                'user_id' => $data['user_id'],
                'password' => $password,
                'photo_profile' => $data['photo_profile'],
            ]);

            $userId = $user->id;
            if (!$userId) {
                throw new \Exception("User ID is not set");
            }

            //Handle Member Status
            if ($request->has('member_status')) {
                $memberstatusData = $request->input('member_status');
                $user->MemberStatus()->create([
                    'field_of_help' => $memberstatusData['field_of_help'] ?? null,
                    'worker' => $memberstatusData['worker'] ?? null,
                    'service_of_use' => $memberstatusData['service_of_use'] ?? null,
                    'last_payment_date' => $memberstatusData['last_payment_date'] ?? null,
                    'another_payment_date' => $memberstatusData['another_payment_date'] ?? null,
                ]);
            }


            // Handle subscription information
            if ($request->has('subscription_information')) {
                $subscriptionData = $request->input('subscription_information');

                // fieldOfHelpElement Multiple Insert
                // $fieldOfHelpElementData = explode(',', $subscriptionData['fieldOfHelp']);
                // $fieldOfHelpElement = json_encode($fieldOfHelpElementData);
                //

                $cityId = null;
                if (isset($subscriptionData['postal_code'])) {
                    $city = MasterDataDistrict::where('postal_code', $subscriptionData['postal_code'])->first();
                    if ($city) {
                        $cityId = $city->city_id;
                    }
                }
                $user->subscriptionInformation()->create([
                    'membership_registration_date' => $subscriptionData['membership_registration_date'] ?? null,
                    'service_start_date' => $subscriptionData['service_start_date'] ?? null,
                    'service_expiration_date' => $subscriptionData['service_expiration_date'] ?? null,
                    'organization_name' => $subscriptionData['organization_name'] ?? null,
                    'address' => $subscriptionData['address'] ?? null,
                    'name_of_representative' => $subscriptionData['name_of_representative'] ?? null,
                    'number_of_workers' => $subscriptionData['number_of_workers'] ?? null,
                    'homepage' => $subscriptionData['homepage'] ?? null,
                    'hr_management_usage' => $subscriptionData['hr_management_usage'] ?? null,
                    'fax_number' => $subscriptionData['fax_number'] ?? null, //cek //*solve
                    'full_address' => $data['fullAddress'] ?? null,
                    'postal_code' => $subscriptionData['postal_code'] ?? null,
                    'city_id' => $cityId,
                    'field_of_help' => $data['field_of_help'] ?? null,
                    'selected_subjects' => $data['selected_subjects'] ?? null,
                ]);
            }

            // Handle person in charge information
            if ($request->has('person_in_charge_information')) {
                $personInChargeData = $request->input('person_in_charge_information');
                $personInChargeData['file_attachments'] = [];
                if ($request->hasFile('fileAttachments')) {
                    foreach ($request->file('fileAttachments') as $file) {
                        $originalName = $file->getClientOriginalName();
                        $path = $file->store('photos', 'public');
                        array_push($personInChargeData['file_attachments'], [
                            'path' => $path,
                            'original_name' => $originalName,
                        ]);
                    }
                }
                $user->personInChargeInformation()->create([
                    'name' => $personInChargeData['name'] ?? null,
                    'job_title' => $personInChargeData['job_title'] ?? null,
                    'position' => $personInChargeData['position'] ?? null,
                    'department' => $personInChargeData['department'] ?? null,
                    'direct_number' => $personInChargeData['direct_number'] ?? null,
                    'cell_phone_number' => $personInChargeData['cell_phone_number'] ?? null,
                    'email' => $personInChargeData['email'] ?? null,
                    'receive_email' => $personInChargeData['receive_email'] == 'true' ?? false, //cek
                    'receive_sms' => $personInChargeData['receive_sms'] == 'true' ?? false, //cek
                    'file_attachments' => json_encode($personInChargeData['file_attachments']),
                ]);
            }

            // Handle additional information
            if ($request->has('additional_information')) {
                $additionalData = $request->input('additional_information');
                $user->additionalInformation()->create([
                    'business_number' => $additionalData['business_number'] ?? null,
                    'corporate_number' => $additionalData['corporate_number'] ?? null,
                    'type_of_business' => $additionalData['type_of_business'] ?? null,
                    'business_type' => $additionalData['business_type'] ?? null,
                    'email_receiving_tax_invoice' => $additionalData['email_receiving_tax_invoice'] ?? null,
                    'data_document_output' => $additionalData['data_document_output'] ?? null,
                    'receive_email' => $additionalData['receive_email'] == 'true' ? true : false,
                ]);
            }

            // Handle service subscription information
            if ($request->has('service_subscription_information')) {
                $serviceSubscriptionData = $request->input('service_subscription_information');
                // $usage_period_months = preg_replace('/[^0-9]/', '', $serviceSubscriptionData['usage_period_months']);
                $usage_period_months = $serviceSubscriptionData['usage_period_months'];
                $user->serviceSubscriptionInformation()->create([
                    'subscription_service' => $serviceSubscriptionData['subscription_service'] ?? null,
                    'start_date_of_use' => $serviceSubscriptionData['start_date_of_use'] ?? null,
                    'expiration_date' => $serviceSubscriptionData['expiration_date'] ?? null,
                    'number_of_service_users' => $serviceSubscriptionData['number_of_service_users'] ?? null,
                    'usage_period_months' => (int) $usage_period_months ?? null,
                    'service_fee' => $serviceSubscriptionData['service_fee'] ?? null, //uncoment
                ]);
            }

            // Handle init administrator company
            if ($typeFilter == 'MEDICAL_INSTITUTION') {
                $checkUserId = UserAdminHospital::where('user_id', $data['user_id'])->first();
                if ($checkUserId) {
                    return response()->json(['success' => false, 'message' => config('app.lang') != 'en' ? '아이디가 이미 존재합니다.' : 'The ID already exists.', 'data' => null]);
                }
                $administratorCompany = UserAdminHospital::create([
                    'user_id' => $data['user_id'],
                    'password' => $password,
                    'name_person_in_charge' => $data['nickname'],
                    'permission' => 'Admin',
                    'registration_date' => now(),
                    'contact_person_position' => $personInChargeData['cell_phone_number'] ?? null,
                    'contact_number' => $personInChargeData['cell_phone_number'] ?? null,
                    'email' => $personInChargeData['email'] ?? null,
                    'user_general_purpose_id' => $userId,
                    'is_administrator_company' => true,
                ]);
            } else if ($typeFilter == 'INDUSTRIAL_SCENE') {
                $checkUserId = AdminGeneralPurpose::where('user_id', $data['user_id'])->first();
                if ($checkUserId) {
                    return response()->json(['success' => false, 'message' => config('app.lang') != 'en' ? '아이디가 이미 존재합니다.' : 'The ID already exists.', 'data' => null]);
                }
                $administratorCompany = AdminGeneralPurpose::create([
                    'user_id' => $data['user_id'],
                    'name' => $data['nickname'],
                    'classification' => 'Admin',
                    // add password + nambah field di model
                    'password' => $password,
                    //
                    'department' => '',
                    'contact' => $personInChargeData['cell_phone_number'] ?? '',
                    'registration_date' => now(),
                    'user_general_purpose_id' => $userId,
                    'is_administrator_company' => true,
                ]);
            } else if ($typeFilter == 'PUBLIC_WELFARE') {
                $checkUserId = AdminPublicWelfare::where('user_id', $data['user_id'])->first();
                if ($checkUserId) {
                    return response()->json(['success' => false, 'message' => config('app.lang') != 'en' ? '아이디가 이미 존재합니다.' : 'The ID already exists.', 'data' => null]);
                }
                $administratorCompany = AdminPublicWelfare::create([
                    'user_id' => $data['user_id'],
                    'name' => $data['nickname'],
                    'classification' => 'Admin',
                    // add password + nambah field di model
                    'password' => $password,
                    //
                    'department' => '',
                    'contact' => $personInChargeData['cell_phone_number'] ?? null,
                    'registration_date' => now(),
                    'user_general_purpose_id' => $userId,
                    'is_administrator_company' => true,
                ]);
            } else {
                $checkUserId = AdminGeneralPurpose::where('user_id', $data['user_id'])->first();
                if ($checkUserId) {
                    return response()->json(['success' => false, 'message' => config('app.lang') != 'en' ? '아이디가 이미 존재합니다.' : 'The ID already exists.', 'data' => null]);
                }
                $administratorCompany = AdminGeneralPurpose::create([
                    'user_id' => $data['user_id'],
                    'name' => $data['nickname'],
                    'classification' => 'Admin',
                    // add password + nambah field di model
                    'password' => $password,
                    //
                    'department' => '',
                    'contact' => $personInChargeData['cell_phone_number'] ?? '',
                    'registration_date' => now(),
                    'user_general_purpose_id' => $userId,
                    'is_administrator_company' => true,
                ]);
            }

            // Commit the transaction
            DB::commit();

            return response()->json([
                'success' => true,
            ]);
        } catch (\Exception $e) {
            // Rollback the transaction
            DB::rollBack();

            return response()->json(['success' => false, 'message' => config('app.lang') != 'en' ? '실패한' : 'Failed', 'data' => null]);
        }
    }

    // Update user information
    public function update(Request $request, $id)
    {
        $userGeneralPurpose = UserGeneralPurpose::find($id);

        if (!$userGeneralPurpose) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        $getUserType = $this->getUserType($request);

        if ($request->hasFile('fileUpload')) {
            $path = $request->file('fileUpload')->store('photos', 'public');
            $request->merge(['photo_profile' => $path]);
        }
        try {
            DB::transaction(function () use ($request, $userGeneralPurpose, $getUserType, $id) {
                $userGeneralPurpose->fill([
                    'address' => $request->input('address'),
                    'receive_email' => $request->input('receive_email') ? 1 : 0,
                    'receive_sms' => $request->input('receive_sms') ? 1 : 0,
                    'data_output' => $request->input('data_output') ? 1 : 0,
                    'member_status' => $request->input('member_status') ?? '베이직',
                    'user_type' => $getUserType,
                    'photo_profile' => $request->filled('photo_profile') ? $request->input('photo_profile') : $userGeneralPurpose->photo_profile,
                ]);

                if ($request->filled('password')) {
                    if ($request->password != '**********') {
                        $userGeneralPurpose->password = Hash::make($request->input('password'));
                    }
                }

                $userGeneralPurpose->save();

                //Handle Member Status
                $memberStatusData = $userGeneralPurpose->MemberStatus;
                if ($memberStatusData) {
                    $memberStatusData->fill([
                        'field_of_help' => $request->input('member_status.field_of_help'),
                        'worker' => $request->input('member_status.worker'),
                        'service_of_use' => $request->input('member_status.service_of_use'),
                        'last_payment_date' => $request->input('member_status.last_payment_date'),
                        'another_payment_date' => $request->input('member_status.another_payment_date'),
                    ]);
                    $memberStatusData->save();
                } else if ($request->member_status) {
                    $userGeneralPurpose->MemberStatus()->create([
                        'field_of_help' => $request->input('member_status.field_of_help') ?? null,
                        'worker' => $request->input('member_status.worker') ?? null,
                        'service_of_use' => $request->input('member_status.service_of_use') ?? null,
                        'last_payment_date' => $request->input('member_status.last_payment_date') ?? null,
                        'another_payment_date' => $request->input('member_status.another_payment_date') ?? null,
                    ]);
                }

                // Handle subscription information
                $subscriptionInfo = $userGeneralPurpose->subscriptionInformation;
                if ($subscriptionInfo) {
                    $cityId = null;
                    if ($request->input('subscription_information.postal_code')) {
                        $city = MasterDataDistrict::where('postal_code', $request->input('subscription_information.postal_code'))->first();
                        if ($city) {
                            $cityId = $city->city_id;
                        }
                    }
                    $subscriptionInfo->fill($request->only([
                        'membership_registration_date',
                        'service_start_date',
                        'service_expiration_date',
                        'representative_contact_info',
                        'fax_number',
                        'organization_name',
                        'name_of_representative',
                        'number_of_workers',
                        'homepage',
                        'hr_management_usage',
                        'fax_number',
                    ]));

                    $subscriptionInfo->address = $request->input('address');
                    $subscriptionInfo->field_of_help = $request->input('field_of_help');
                    $subscriptionInfo->selected_subjects = $request->input('selected_subjects');
                    $subscriptionInfo->full_address = $request->input('fullAddress');
                    $subscriptionInfo->postal_code = $request->input('subscription_information.postal_code');
                    $subscriptionInfo->city_id = $cityId;
                    $subscriptionInfo->save();
                }

                // Handle person in charge information
                $personInCharge = $userGeneralPurpose->personInChargeInformation;
                if ($personInCharge) {
                    // $personInCharge->fill($request->only([
                    //     'name',
                    //     'job_title',
                    //     'position',
                    //     'department',
                    //     'direct_number',
                    //     'cell_phone_number',
                    //     'email',
                    //     'receive_sms'
                    // ]));

                    $personInChargeData['file_attachments'] = [];
                    if ($request->hasFile('fileAttachments')) {
                        foreach ($request->file('fileAttachments') as $file) {
                            $originalName = $file->getClientOriginalName();
                            $path = $file->store('photos', 'public');
                            array_push($personInChargeData['file_attachments'], [
                                'path' => $path,
                                'original_name' => $originalName,
                            ]);
                        }
                    }

                    $personInCharge->fill([
                        'name' => $request->input('person_in_charge_information.name'),
                        'job_title' => $request->input('person_in_charge_information.job_title'),
                        'position' => $request->input('person_in_charge_information.position'),
                        'department' => $request->input('person_in_charge_information.department'),
                        'direct_number' => $request->input('person_in_charge_information.direct_number'),
                        'cell_phone_number' => $request->input('person_in_charge_information.cell_phone_number'),
                        'email' => $request->input('person_in_charge_information.email'),
                        'receive_sms' => $request->input('person_in_charge_information.receive_sms') ? 1 : 0,
                        'receive_email' => $request->input('person_in_charge_information.receive_email') ? 1 : 0
                    ]);
                    // $personInCharge->receive_email = $request->input('receive_email') ? 1 : 0;

                    if (count($personInChargeData['file_attachments']) > 0) {
                        $personInCharge->file_attachments = json_encode($personInChargeData['file_attachments']);
                    }
                    $personInCharge->save();
                }

                // Handle additional information
                $additionalInfo = $userGeneralPurpose->additionalInformation;
                if ($additionalInfo) {
                    // $additionalInfo->fill($request->only([
                    //     'business_number',
                    //     'corporate_number',
                    //     'type_of_business',
                    //     'business_type',
                    //     'email_receiving_tax_invoice',
                    //     'data_document_output'
                    // ]));
                    $additionalInfo->fill([
                        'business_number' => $request->input('additional_information.business_number'),
                        'corporate_number' => $request->input('additional_information.corporate_number'),
                        'type_of_business' => $request->input('additional_information.type_of_business'),
                        'business_type' => $request->input('additional_information.business_type'),
                        'email_receiving_tax_invoice' => $request->input('additional_information.email_receiving_tax_invoice'),
                        'data_document_output' => $request->input('additional_information.data_document_output'),
                        'receive_email' => $request->input('additional_information.receive_email'),

                    ]);
                    $additionalInfo->save();
                }

                // Handle service subscription information
                $serviceSubscriptionInfo = $userGeneralPurpose->serviceSubscriptionInformation;
                if ($serviceSubscriptionInfo) {
                    // $serviceSubscriptionInfo->fill($request->only([
                    //     'subscription_service',
                    //     'start_date_of_use',
                    //     'expiration_date',
                    //     'number_of_service_users',
                    //     'usage_period_months',
                    //     'service_fee'
                    // ]));

                    $serviceSubscriptionInfo->fill([
                        'subscription_service' => $request->input('service_subscription_information.subscription_service'),
                        'start_date_of_use' => $request->input('service_subscription_information.start_date_of_use'),
                        'expiration_date' => $request->input('service_subscription_information.expiration_date'),
                        'number_of_service_users' => $request->input('service_subscription_information.number_of_service_users'),
                        'email_receiving_tax_invoice' => $request->input('service_subscription_information.email_receiving_tax_invoice'),
                        'usage_period_months' => $request->input('service_subscription_information.usage_period_months'),
                        'service_fee' => $request->input('service_subscription_information.service_fee'),

                    ]);

                    $serviceSubscriptionInfo->save();
                }

                // Handle init administrator company
                if ($getUserType == 'MEDICAL_INSTITUTION') {
                    $administratorCompany = UserAdminHospital::where('user_general_purpose_id', $id)
                        ->where('is_administrator_company', true)
                        ->first();

                    if ($administratorCompany) {
                        $administratorCompany->fill([
                            'contact_person_position' => $request->input('person_in_charge_information.cell_phone_number') ? $request->input('person_in_charge_information.cell_phone_number') : $administratorCompany->contact_person_position,
                            'contact_number' => $request->input('person_in_charge_information.cell_phone_number') ? $request->input('person_in_charge_information.cell_phone_number') : $administratorCompany->contact_person_position,
                            'email' => $request->input('person_in_charge_information.email') ? $request->input('person_in_charge_information.email') : $administratorCompany->email,
                        ]);

                        if ($request->filled('password')) {
                            if ($request->password != '**********') {
                                $administratorCompany->password = Hash::make($request->input('password'));
                            }
                        }
                        $administratorCompany->save();
                    }

                } else if ($getUserType == 'INDUSTRIAL_SCENE') {
                    $administratorCompany = AdminGeneralPurpose::where('user_general_purpose_id', $id)
                        ->where('is_administrator_company', true)
                        ->first();

                    if ($administratorCompany) {
                        $administratorCompany->fill([
                            'contact' => $request->input('person_in_charge_information.cell_phone_number') ? $request->input('person_in_charge_information.cell_phone_number') : $administratorCompany->contact,
                        ]);

                        if ($request->filled('password')) {
                            if ($request->password != '**********') {
                                $administratorCompany->password = Hash::make($request->input('password'));
                            }
                        }
                        $administratorCompany->save();
                    }
                } else if ($getUserType == 'PUBLIC_WELFARE') {
                    $administratorCompany = AdminPublicWelfare::where('user_general_purpose_id', $id)
                        ->where('is_administrator_company', true)
                        ->first();

                    if ($administratorCompany) {
                        $administratorCompany->fill([
                            'contact' => $request->input('person_in_charge_information.cell_phone_number') ? $request->input('person_in_charge_information.cell_phone_number') : $administratorCompany->contact,
                        ]);

                        if ($request->filled('password')) {
                            if ($request->password != '**********') {
                                $administratorCompany->password = Hash::make($request->input('password'));
                            }
                        }
                        $administratorCompany->save();
                    }
                } else {
                    $administratorCompany = AdminGeneralPurpose::where('user_general_purpose_id', $id)
                        ->where('is_administrator_company', true)
                        ->first();

                    if ($administratorCompany) {
                        $administratorCompany->fill([
                            'contact' => $request->input('person_in_charge_information.cell_phone_number') ? $request->input('person_in_charge_information.cell_phone_number') : $administratorCompany->contact,
                        ]);

                        if ($request->filled('password')) {
                            if ($request->password != '**********') {
                                $administratorCompany->password = Hash::make($request->input('password'));
                            }
                        }
                        $administratorCompany->save();
                    }
                }
            });

            return response()->json(['success' => true, 'message' => 'User information updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update user information', 'error' => $e->getMessage()], 500);
        }
    }

    // Extend subscription
    public function extendSubscription(Request $request, $id)
    {
        $userGeneralPurpose = UserGeneralPurpose::find($id);

        if (!$userGeneralPurpose) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        $serviceSubscriptionInfo = $userGeneralPurpose->serviceSubscriptionInformation;
        if (!$serviceSubscriptionInfo) {
            return response()->json(['success' => false, 'message' => 'Service subscription information not found'], 404);
        }

        $serviceSubscriptionInfo->fill($request->only([
            'subscription_service',
            'start_date_of_use',
            'expiration_date',
            'number_of_service_users',
            'usage_period_months',
            'service_fee'
        ]));
        $serviceSubscriptionInfo->save();

        return response()->json(['success' => true, 'message' => 'Service subscription extended successfully']);
    }

    public function deleteMultiple(Request $request)
    {
        $getUserType = $this->getUserType($request);

        $userIds = $request->input('user_ids');

        if (!$userIds || count($userIds) == 0) {
            return response()->json(['success' => false, 'message' => 'No users selected'], 400);
        }

        DB::beginTransaction();

        try {
            UserGeneralPurpose::whereIn('id', $userIds)->delete();

            foreach ($userIds as $userId) {
                if ($getUserType == 'MEDICAL_INSTITUTION') {
                    UserAdminHospital::where('user_general_purpose_id', $userId)->delete();
                } else if ($getUserType == 'INDUSTRIAL_SCENE') {
                    AdminGeneralPurpose::where('user_general_purpose_id', $userId)->delete();
                } else if ($getUserType == 'PUBLIC_WELFARE') {
                    AdminPublicWelfare::where('user_general_purpose_id', $userId)->delete();
                } else {
                    AdminGeneralPurpose::where('user_general_purpose_id', $userId)->delete();
                }
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Selected users have been deleted']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'An error occurred while deleting users'], 500);
        }
    }

    public function findPostalCode(Request $request, $id)
    {
        $address = MasterDataDistrict::with(['masterDataCity'])->where('postal_code', $id)->first();
        return response()->json(['success' => true, 'data' => $address]);
    }

    public function getLoginAccessForUser(Request $request, $id)
    {
        $user = UserGeneralPurpose::where('id', $id)->first();
        if ($user) {
            if ($user->user_type == 'MEDICAL_INSTITUTION') {
                $companyUser = UserStaffHospital::where('user_general_purpose_id', $id)->first();
                if (!$companyUser) {
                    $companyUser = UserStaffHospital::create([
                        'registration_date' => date('Y-m-d'),
                        'birthdate' => null,
                        'age' => 0,
                        'gender' => 'M',
                        'job_title' => '',
                        'department' => '',
                        'position' => '',
                        'contact1' => '',
                        'contact2' => '',
                        'address' => '',
                        'email' => '',
                        'user_id' => Str::random(30),
                        'password' => Hash::make('secret'),
                        'photo_profile' => '',
                        'employee_number' => '',
                        'user_general_purpose_id' => $id
                    ]);
                }

                $userLogin = [
                    'user_id' => $companyUser->user_id,
                    'password' => $companyUser->password,
                ];
                $result = base64_encode(json_encode($userLogin));

                return response()->json([
                    'success' => true,
                    'data' => [
                        'user_type' => $user->user_type,
                        'key' => $result,
                    ]
                ]);
            } else if ($user->user_type == 'PUBLIC_WELFARE') {
                $companyUser = AdminPublicWelfare::where('user_general_purpose_id', $id)->first();
                $userLogin = [
                    'user_id' => $companyUser->user_id,
                    'password' => $companyUser->password,
                ];
                $result = base64_encode(json_encode($userLogin));

                return response()->json([
                    'success' => true,
                    'data' => [
                        'user_type' => $user->user_type,
                        'key' => $result,
                    ]
                ]);
            } else if ($user->user_type == 'INDUSTRIAL_SCENE') {
                $companyUser = AdminGeneralPurpose::where('user_general_purpose_id', $id)->first();
                $userLogin = [
                    'user_id' => $companyUser->user_id,
                    'password' => $companyUser->password,
                ];
                $result = base64_encode(json_encode($userLogin));

                return response()->json([
                    'success' => true,
                    'data' => [
                        'user_type' => $user->user_type,
                        'key' => $result,
                    ]
                ]);
            } else {
                $companyUser = AdminGeneralPurpose::where('user_general_purpose_id', $id)->first();
                $userLogin = [
                    'user_id' => $companyUser->user_id,
                    'password' => $companyUser->password,
                ];
                $result = base64_encode(json_encode($userLogin));

                return response()->json([
                    'success' => true,
                    'data' => [
                        'user_type' => $user->user_type,
                        'key' => $result,
                    ]
                ]);
            }
        }
    }

    public function getLoginAccessForManagement(Request $request, $id)
    {
        $user = UserGeneralPurpose::where('id', $id)->first();
        if ($user) {
            if ($user->user_type == 'MEDICAL_INSTITUTION') {
                $companyUser = UserAdminHospital::where('user_general_purpose_id', $id)->first();
                $userLogin = [
                    'admin_user_id' => $companyUser->user_id,
                    'admin_password' => $companyUser->password,
                ];
                $result = base64_encode(json_encode($userLogin));

                return response()->json([
                    'success' => true,
                    'data' => [
                        'user_type' => $user->user_type,
                        'key' => $result,
                    ]
                ]);
            } else if ($user->user_type == 'PUBLIC_WELFARE') {
                $companyUser = AdminPublicWelfare::where('user_general_purpose_id', $id)->first();
                $userLogin = [
                    'user_id' => $companyUser->user_id,
                    'password' => $companyUser->password,
                ];
                $result = base64_encode(json_encode($userLogin));

                return response()->json([
                    'success' => true,
                    'data' => [
                        'user_type' => $user->user_type,
                        'key' => $result,
                    ]
                ]);
            } else if ($user->user_type == 'INDUSTRIAL_SCENE') {
                $companyUser = AdminGeneralPurpose::where('user_general_purpose_id', $id)->first();
                $userLogin = [
                    'user_id' => $companyUser->user_id,
                    'password' => $companyUser->password,
                ];
                $result = base64_encode(json_encode($userLogin));

                return response()->json([
                    'success' => true,
                    'data' => [
                        'user_type' => $user->user_type,
                        'key' => $result,
                    ]
                ]);
            } else {
                $companyUser = AdminGeneralPurpose::where('user_general_purpose_id', $id)->first();
                $userLogin = [
                    'user_id' => $companyUser->user_id,
                    'password' => $companyUser->password,
                ];
                $result = base64_encode(json_encode($userLogin));

                return response()->json([
                    'success' => true,
                    'data' => [
                        'user_type' => $user->user_type,
                        'key' => $result,
                    ]
                ]);
            }
        }
    }

    private function getCity()
    {
        $orderBy = 'city_name';

        if (config('app.lang') == 'en') {
            $orderBy = 'city_name_en';
        }
        $data = MasterDataCity::orderBy($orderBy, 'asc')->get();

        return $data;
    }


    public function getDistrictByCityId(Request $request, $id)
    {
        $orderBy = 'district_name';

        if (config('app.lang') == 'en') {
            $orderBy = 'district_name_en';
        }
        $data = MasterDataDistrict::where('city_id', $id)->orderBy($orderBy, 'asc')->get();

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function getUserList(Request $request)
    {
        $getUserType = $this->getUserType($request);

        $perPage = $request->input('limit', 10);
        $query = UserGeneralPurpose::select('user_general_purposes.*', 'subscription_information_general_purposes.organization_name')
            ->leftJoin('subscription_information_general_purposes', 'subscription_information_general_purposes.user_general_purpose_id', '=', 'user_general_purposes.id')
            ->where('user_type', $getUserType);

        if ($request->filled('postal_code')) {
            $query = $query->where('subscription_information_general_purposes.postal_code', '=', $request->postal_code);
        }

        $query = $query->orderBy('subscription_information_general_purposes.organization_name', 'asc');
        $user = $query->paginate($perPage);
        return response()->json(['success' => true, 'data' => $user]);
    }

}