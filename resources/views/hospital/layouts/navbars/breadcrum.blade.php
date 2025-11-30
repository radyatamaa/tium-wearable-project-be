<nav aria-label="breadcrumb" role="navigation">
    <ol class="breadcrumb" style="background-color:#324D65">
        @if(request()->is('hospital/home'))
        <li class="breadcrumb-item" aria-current="page">
            @if (config('app.lang') != 'en')
            {{ __('통계') }}
            @else
            {{ __('stats') }}
            @endif
            | {{ $now_date }} | {{ $now_time }}

        </li>
        @elseif(request()->is('hospital/patient-management/inpatients'))
        <li class="breadcrumb-item" aria-current="page">
            @if (config('app.lang') != 'en')
            {{ __('입원환자 ') }}
            @else
            {{ __('Hospitalized patient ') }}
            @endif
            <!-- <span
                class="navbar-title-blue">{{ config('app.lang') != 'en' ? __('(전체 12,345)') : __('(Total 12.345) in charged patient') }}</span> -->
        </li>
        @elseif(request()->is('hospital/patient-management/discharged'))
        <li class="breadcrumb-item" aria-current="page">
            @if (config('app.lang') != 'en')
            {{ __('퇴원환자 ') }}
            @else
            {{ __('Discharged Patient ') }}
            @endif
            <!-- <span
                class="navbar-title-blue">{{ config('app.lang') != 'en' ? __(' (전체 12,345)') : __('(Total 12.345) discharged patient') }}</span> -->
        </li>
        @elseif(request()->is('hospital/history/emergency-alerts'))
        <li class="breadcrumb-item" aria-current="page">
            @if (config('app.lang') != 'en')
            {{ __('긴급알림이력 ') }}
            @else
            {{ __('Emergency notification history ') }}
            @endif
            <!-- <span
                class="navbar-title-blue">{{ config('app.lang') != 'en' ? __('(전체 12,345)') : __('(Total 12.345)') }}</span> -->
        </li>
        @elseif(request()->is('hospital/history/patient-calls'))
        <li class="breadcrumb-item" aria-current="page">
            @if (config('app.lang') != 'en')
            {{ __('환자호출이력 ') }}
            @else
            {{ __('Patient call history') }}
            @endif
            <!-- <span
                class="navbar-title-blue">{{ config('app.lang') != 'en' ? __('(전체 12,345)') : __('(Total 12.345)') }}</span> -->
        </li>
        @elseif(request()->is('hospital/device-management'))
        <li class="breadcrumb-item" aria-current="page">
            @if (config('app.lang') != 'en')
            {{ __('스마트워치 ') }}
            @else
            {{ __('Smartwatch') }}
            @endif
            <!-- <span
                class="navbar-title-blue">{{ config('app.lang') != 'en' ? __('(전체 12,345)') : __('(Total 12.345)') }}</span> -->
        </li>
        @elseif(request()->is('hospital/settings/location-management/top-location'))
        <li class="breadcrumb-item" aria-current="page">
            @if (config('app.lang') != 'en')
            {{ __('상위 로케이션') }}
            @else
            {{ __('Top Location Setting') }}
            @endif
            <!-- <span
                class="navbar-title-blue">{{ config('app.lang') != 'en' ? __('(전체 12,345)') : __('(Total 12.345)') }}</span> -->
        </li>
        @elseif(request()->is('hospital/settings/location-management/sub-location'))
        <li class="breadcrumb-item" aria-current="page">
            @if (config('app.lang') != 'en')
            {{ __('하위 로케이션 설정 ') }}
            @else
            {{ __('Sub Location Setting') }}
            @endif
            <!-- <span
                class="navbar-title-blue">{{ config('app.lang') != 'en' ? __('(전체 12,345)') : __('(Total 12.345)') }}</span> -->
        </li>
        @elseif(request()->is('hospital/settings/location-management/detailed-location'))
        <li class="breadcrumb-item" aria-current="page">
            @if (config('app.lang') != 'en')
            {{ __('상세 로케이션 설정') }}
            @else
            {{ __('Detailed Location Setting') }}
            @endif
            <!-- <span
                class="navbar-title-blue">{{ config('app.lang') != 'en' ? __('(전체 12,345)') : __('(Total 12.345)') }}</span> -->
        </li>
        @elseif(request()->is('hospital/settings/account-management'))
        <li class="breadcrumb-item" aria-current="page">
            @if (config('app.lang') != 'en')
            {{ __('계정관리') }}
            @else
            {{ __('Account Management') }}
            @endif
            <!-- <span
                class="navbar-title-blue">{{ config('app.lang') != 'en' ? __('(전체 ' . $accounts->count() . ')') : __('(Total ' . $accounts->count() . ')') }}</span> -->
        </li>
        @elseif(request()->is('hospital/settings/staff-management'))
        <li class="breadcrumb-item" aria-current="page">
            @if (config('app.lang') != 'en')
            {{ __('의료진관리') }}
            @else
            {{ __('Medical staff management') }}
            @endif
            <!-- <span
                class="navbar-title-blue">{{ config('app.lang') != 'en' ? __('(전체 12,345)') : __('(Total 12.345)') }}</span> -->
        </li>
        @endif
    </ol>
</nav>