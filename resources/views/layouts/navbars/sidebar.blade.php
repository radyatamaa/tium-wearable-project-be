<div class="sidebar">
    <div class="sidebar-wrapper">
        <ul class="nav">
            <li @if ($pageSlug == 'dashboard') class="active " @endif>
                <a href="{{ route('home') }}">
                    <i class="tim-icons icon-chart-bar-32"></i>
                    <p>
                        @if (config('app.lang') != 'en')
                            {{ __('대시보드') }}
                        @else
                            {{ __('Dashboard') }}
                        @endif
                    </p>
                </a>
            </li>
            <li>
                <a data-toggle="collapse" href="#user-management" @if ($pageSlug == 'user_management')
                aria-expanded="true" @endif>
                    <i class="tim-icons icon-single-02"></i>
                    <span class="nav-link-text">
                        @if (config('app.lang') != 'en')
                            {{ __('사용자관리') }}
                        @else
                            {{ __('User Management') }}
                        @endif
                    </span>

                    <b class="caret mt-1"></b>
                </a>

                <div id="user-management"
                    class="{{ $pageSlug == 'user_management' ? 'collapse show' : 'collapse hide' }}">
                    <ul class="nav pl-4" id="dynamic-ul">
                        <!-- Li items will be injected here by JavaScript -->
                    </ul>
                </div>
            </li>
            <li>
                <a data-toggle="collapse" href="#setting" @if (
                    $pageSlug == 'setting_job_title' ||
                    $pageSlug == 'detailed_field_setting' || $pageSlug == 'service_usage_settings'
                ) aria-expanded="true"
                @endif>
                    <i class="tim-icons icon-settings-gear-63"></i>
                    <span class="nav-link-text">
                        @if (config('app.lang') != 'en')
                            {{ __('설정') }}
                        @else
                            {{ __('Setting') }}
                        @endif
                    </span>
                    <b class="caret mt-1"></b>
                </a>

                <div @if (
                    $pageSlug == 'setting_job_title' || $pageSlug == 'detailed_field_setting' ||
                    $pageSlug == 'service_usage_settings'
                ) class="collapse show" @else class="collapse hide" @endif
                    id="setting">
                    <ul class="nav pl-4">
                        <li @if ($pageSlug == 'setting_job_title') class="active " @endif>
                            <a href="{{ route('setting-job-title.edit')  }}">
                                <p>
                                    @if (config('app.lang') != 'en')
                                        {{ __('서비스/직위설정') }}
                                    @else
                                        {{ __('Service/Job title setting') }}
                                    @endif
                                </p>
                            </a>
                        </li>
                        <li @if ($pageSlug == 'detailed_field_setting') class="active " @endif>
                            <a href="{{ route('setting-detailed-field.edit')  }}">
                                <p>
                                    @if (config('app.lang') != 'en')
                                        {{ __('세부분야 설정') }}
                                    @else
                                        {{ __('Detailed field setting') }}
                                    @endif
                                </p>
                            </a>
                        </li>
                        <li @if ($pageSlug == 'service_usage_settings') class="active " @endif>
                            <a href="{{ route('setting-service-usage.edit')  }}">
                                <p>
                                    @if (config('app.lang') != 'en')
                                        {{ __('서비스 이용설정') }}
                                    @else
                                        {{ __('Service usage setting') }}
                                    @endif
                                </p>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li>
                <a data-toggle="collapse" href="#site-management" @if (
                    $pageSlug == 'sales_management' ||
                    $pageSlug == 'notice' || $pageSlug == 'inquiry_for_use' || $pageSlug == 'admin_management'
                )
                aria-expanded="true" @endif>
                    <i class="tim-icons icon-wallet-43"></i>
                    <span class="nav-link-text">
                        @if (config('app.lang') != 'en')
                            {{ __('사이트관리') }}
                        @else
                            {{ __('Site Management') }}
                        @endif
                    </span>
                    <b class="caret mt-1"></b>
                </a>

                <div @if (
                    $pageSlug == 'sales_management' || $pageSlug == 'notice' || $pageSlug == 'inquiry_for_use' ||
                    $pageSlug == 'admin_management' || $pageSlug == 'cscenter' || $pageSlug == 'log_blood_pressure' ||
                    $pageSlug == 'log_temperature' || $pageSlug == 'log_heart_rate' || $pageSlug == 'log_oxygen' ||
                    $pageSlug == 'log-patient-call-hospital' || $pageSlug == 'log-patient-call-public-welfare' ||
                    $pageSlug == 'patient-report'
                ) class="collapse show" @else class="collapse hide" @endif
                    id="site-management">
                    <ul class="nav pl-4">
                        <li @if ($pageSlug == 'sales_management') class="active " @endif>
                            <a href="{{ route('sales-management.index')  }}">
                                <p>
                                    @if (config('app.lang') != 'en')
                                        {{ __('매출관리') }}
                                    @else
                                        {{ __('Sales Management') }}
                                    @endif
                                </p>
                            </a>
                        </li>
                        <li @if ($pageSlug == 'notice') class="active " @endif>
                            <a href="{{ route('notice.index')  }}">
                                <p>
                                    @if (config('app.lang') != 'en')
                                        {{ __('공지사항') }}
                                    @else
                                        {{ __('Notice') }}
                                    @endif
                                </p>
                            </a>
                        </li>
                        <li @if ($pageSlug == 'inquiry_for_use') class="active " @endif>
                            <a href="{{ route('inquiry-for-use.index')  }}">
                                <p>
                                    @if (config('app.lang') != 'en')
                                        {{ __('이용문의') }}
                                    @else
                                        {{ __('Inquiry for use') }}
                                    @endif
                                </p>
                            </a>
                        </li>
                        <li @if ($pageSlug == 'cscenter') class="active " @endif>
                            <a href="{{ route('customer-center.index')  }}">
                                <p>
                                    @if (config('app.lang') != 'en')
                                        {{ __('고객센터') }}
                                    @else
                                        {{ __('CS Center') }}
                                    @endif
                                </p>
                            </a>
                        </li>
                        <li @if ($pageSlug == 'log_blood_pressure') class="active " @endif>
                            <a href="{{ route('log-blood-pressure.index')  }}">
                                <p>
                                    @if (config('app.lang') != 'en')
                                        {{ __('혈압 기록') }}
                                    @else
                                        {{ __('Blood Pressure Log') }}
                                    @endif
                                </p>
                            </a>
                        </li>
                        <li @if ($pageSlug == 'log_temperature') class="active " @endif>
                            <a href="{{ route('log-temperature.index')  }}">
                                <p>
                                    @if (config('app.lang') != 'en')
                                        {{ __('체온 기록') }}
                                    @else
                                        {{ __('Temperature Log') }}
                                    @endif
                                </p>
                            </a>
                        </li>
                        <li @if ($pageSlug == 'log_heart_rate') class="active " @endif>
                            <a href="{{ route('log-heart-rate.index')  }}">
                                <p>
                                    @if (config('app.lang') != 'en')
                                        {{ __('심박수 기록') }}
                                    @else
                                        {{ __('Heart Rate Log') }}
                                    @endif
                                </p>
                            </a>
                        </li>
                        <li @if ($pageSlug == 'log_oxygen') class="active " @endif>
                            <a href="{{ route('log-oxygen.index')  }}">
                                <p>
                                    @if (config('app.lang') != 'en')
                                        {{ __('산소 기록') }}
                                    @else
                                        {{ __('Oxygen Log') }}
                                    @endif
                                </p>
                            </a>
                        </li>
                        <li @if ($pageSlug == 'log-patient-call-hospital') class="active " @endif>
                            <a href="{{ route('log-patient-call-hospital.index')  }}">
                                <p>
                                    @if (config('app.lang') != 'en')
                                        {{ __('환자 병원 전화') }}
                                    @else
                                        {{ __('Patient Call Hospital') }}
                                    @endif
                                </p>
                            </a>
                        </li>
                        <li @if ($pageSlug == 'log-patient-call-public-welfare') class="active " @endif>
                            <a href="{{ route('log-patient-call-public-welfare.index')  }}">
                                <p>
                                    @if (config('app.lang') != 'en')
                                        {{ __('환자 전화 공공 복지') }}
                                    @else
                                        {{ __('Patient Call public welfare') }}
                                    @endif
                                </p>
                            </a>
                        </li>
                        <li @if ($pageSlug == 'patient-report') class="active " @endif>
                            <a href="{{ route('patient-report.index')  }}">
                                <p>
                                    @if (config('app.lang') != 'en')
                                        {{ __('환자 보고서') }}
                                    @else
                                        {{ __('Patient') }}
                                    @endif
                                </p>
                            </a>
                        </li>
                        <li @if ($pageSlug == 'admin_management') class="active " @endif>
                            <a href="{{ route('admin-management.index')  }}">
                                <p>
                                    @if (config('app.lang') != 'en')
                                        {{ __('관리자관리') }}
                                    @else
                                        {{ __('Admin Management') }}
                                    @endif
                                </p>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <!-- <li @if ($pageSlug == 'icons') class="active " @endif>
                <a href="{{ route('pages.icons') }}">
                    <i class="tim-icons icon-atom"></i>
                    <p>{{ __('Icons') }}</p>
                </a>
            </li>
            <li @if ($pageSlug == 'maps') class="active " @endif>
                <a href="{{ route('pages.maps') }}">
                    <i class="tim-icons icon-pin"></i>
                    <p>{{ __('Maps') }}</p>
                </a>
            </li>
            <li @if ($pageSlug == 'notifications') class="active " @endif>
                <a href="{{ route('pages.notifications') }}">
                    <i class="tim-icons icon-bell-55"></i>
                    <p>{{ __('Notifications') }}</p>
                </a>
            </li>
            <li @if ($pageSlug == 'tables') class="active " @endif>
                <a href="{{ route('pages.tables') }}">
                    <i class="tim-icons icon-puzzle-10"></i>
                    <p>{{ __('Table List') }}</p>
                </a>
            </li>
            <li @if ($pageSlug == 'typography') class="active " @endif>
                <a href="{{ route('pages.typography') }}">
                    <i class="tim-icons icon-align-center"></i>
                    <p>{{ __('Typography') }}</p>
                </a>
            </li>
            <li @if ($pageSlug == 'rtl') class="active " @endif>
                <a href="{{ route('pages.rtl') }}">
                    <i class="tim-icons icon-world"></i>
                    <p>{{ __('RTL Support') }}</p>
                </a>
            </li>
            <li class=" {{ $pageSlug == 'upgrade' ? 'active' : '' }} bg-info">
                <a href="{{ route('pages.upgrade') }}">
                    <i class="tim-icons icon-spaceship"></i>
                    <p>{{ __('Upgrade to PRO') }}</p>
                </a>
            </li> -->
        </ul>
    </div>
</div>
<script>
    function loadSideBarUserManagement(serviceClassifications) {
        const pageSlug = `{{ $pageSlug }}`;
        const currentPath = `{{ request()->path() }}`;
        const currentTypeFilter = `{{ request()->session()->get('type_filter') }}`;

        const items = [];

        serviceClassifications.forEach(e => {
            items.push({
                type: e.service_classification,
                label: e.service_classification_desc,
                url: `{{ route('user.index') }}?type=${e.service_classification}`
            })
        })

        const ulElement = document.getElementById('dynamic-ul');

        items.forEach(item => {
            const liElement = document.createElement('li');
            // Check if this item should be active
            if (pageSlug === 'user_management' && currentTypeFilter.toLowerCase() === item.type.toLowerCase() &&
                currentPath.includes(
                    'user')) {
                liElement.classList.add('active');
            }

            const aElement = document.createElement('a');
            aElement.href = item.url;

            const pElement = document.createElement('p');
            pElement.innerHTML = item.label;

            aElement.appendChild(pElement);
            liElement.appendChild(aElement);
            ulElement.appendChild(liElement);
        });
    }
</script>