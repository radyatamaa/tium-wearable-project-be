<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use App\Models\ServiceJobSetting;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Models\UserGeneralPurpose;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Base query with conditions applied using whereHas
        $users = UserGeneralPurpose::whereHas('subscriptionInformation', function ($query) use ($request) {
            if ($request->filled('postalCodeAddress')) {
                $query->where('postal_code', $request->postalCodeAddress);
            }

            if ($request->filled('search')) {
                if ($request->search_by == 'name') {
                    $query = $query->where('organization_name', 'like', '%' . $request->search . '%');
                }
            }
        });

        $users = $users->whereHas('serviceSubscriptionInformation', function ($query) use ($request) {
            if ($request->filled('usage_status')) {
                $usageStatus = strtolower($request->usage_status);
                $query->whereRaw('LOWER(subscription_service) LIKE ?', ["%$usageStatus%"]);
            }

            if ($request->filled('expired_start_date') && $request->filled('expired_end_date')) {
                if ($request->expired_start_date != 'YYYY-MM-DD' && $request->expired_end_date != 'YYYY-MM-DD') {
                    $query->whereDate('expiration_date', '>=', $request->expired_start_date)
                        ->whereDate('expiration_date', '<=', $request->expired_end_date);
                }
            }
        });

        if ($request->filled('search')) {
            if ($request->search_by == 'user_id') {
                $users = $users->where('user_id', 'like', '%' . $request->search . '%');
            }
        }

        if ($request->filled('full_term')) {
            if ($request->full_term != 'ALL') {
                $users = $users->where('user_type', 'like', '%' . $request->full_term . '%');
            }
        }



        // Menyiapkan query pagination dengan filter
        $perPage = $request->input('limit', 10);

        $users = $users->orderBy('user_general_purposes.created_at', 'desc')
            ->paginate($perPage);

        $countExpired30days = UserGeneralPurpose::with(['serviceSubscriptionInformation'])
            ->join('subscription_information_general_purposes', 'subscription_information_general_purposes.user_general_purpose_id', '=', 'user_general_purposes.id')
            ->whereHas('serviceSubscriptionInformation', function ($q) {
                $q->whereDate('expiration_date', '>=', Carbon::now())
                    ->whereDate('expiration_date', '<=', Carbon::now()->addDays(30));
            })->count();

        $countDashboardServices = [];
        $services = ServiceJobSetting::get();
        foreach ($services as $service) {
            $countUserServices = UserGeneralPurpose::whereHas('subscriptionInformation')
                ->whereHas('serviceSubscriptionInformation')
                ->where('user_type', $service->service_classification)->count();

            $serviceDashboard = array(
                'id' => $service->service_classification,
                'name' => $service->service_classification_desc,
                'icon' => $service->icon,
                'color' => $service->color_dashboard,
                'count' => $countUserServices,
                'link' => '/user?type=' . $service->service_classification,
            );
            array_push($countDashboardServices, $serviceDashboard);
        }

        $cardExpiredDashboard = array(
            'id' => 'view-expiration-30-days',
            'name' => config('app.lang') != 'en' ? __('30일내 서비스종료') : __('Service Ending in 30 Days'),
            'icon' => '/icons/dashboard/service-ending-30-days.svg',
            'color' => 'custom-bg-light-purple',
            'count' => $countExpired30days,
            'link' => null,
        );
        array_push($countDashboardServices, $cardExpiredDashboard);
        $cardDashboardWidth = 100 / count($countDashboardServices);

        $lastNoticeNotification = Notice::where('status', '전시중')->first();

        return view('dashboard', [
            'users' => $users,
            'countDashboardServices' => $countDashboardServices,
            'lastNoticeNotification' => $lastNoticeNotification,
            'cardDashboardWidth' => $cardDashboardWidth,
        ]);
    }
}