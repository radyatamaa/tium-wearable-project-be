<nav aria-label="breadcrumb" role="navigation">
    <ol class="breadcrumb" id="breadcrumb-side-bar">
        <!-- Breadcrumb items will be injected here by JavaScript -->
    </ol>
</nav>

<script>
    function loadBreadcrum(serviceClassifications) {
        const breadcrumbContainer = document.getElementById('breadcrumb-side-bar');
        const typeFilter = `{{ request()->session()->get('type_filter') }}`;

        const breadcrumbs = [{
            condition: window.location.pathname === '/home',
            label: `{{ config('app.lang') != 'en' ? __('대시보드') : __('Dashboard') }}`
        }];


        let userManagementBreadcrumbs = [];

        serviceClassifications.forEach(e => {
            breadcrumbs.push({
                condition: ((new URLSearchParams(window.location.search).get('type') === e
                    .service_classification) ||
                    typeFilter === e.service_classification) &&
                    window.location.pathname.includes('user'),
                label: e.service_classification_desc
            });
        })

        breadcrumbs.push(...[{
            condition: window.location.pathname === '/setting-job-title',
            label: `{{ config('app.lang') != 'en' ? __('서비스/직위설정') : __('Service/Job title setting') }}`
        },
        {
            condition: window.location.pathname === '/setting-detailed-field',
            label: `{{ config('app.lang') != 'en' ? __('세부분야 설정') : __('Detailed field setting') }}`
        },
        {
            condition: window.location.pathname === '/setting-service-usage',
            label: `{{ config('app.lang') != 'en' ? __('서비스 이용설정') : __('Service usage settings') }}`
        },
        {
            condition: window.location.pathname === '/sales-management',
            label: `{{ config('app.lang') != 'en' ? __('매출관리') : __('Sales Management') }}`
        },
        {
            condition: window.location.pathname === '/notice',
            label: `{{ config('app.lang') != 'en' ? __('공지사항') : __('Notice') }}`
        },
        {
            condition: window.location.pathname === '/inquiry-for-use',
            label: `{{ config('app.lang') != 'en' ? __('이용문의') : __('Inquiry for use') }}`
        },
        {
            condition: window.location.pathname === '/customer-center',
            label: `{{ config('app.lang') != 'en' ? __('고객센터') : __('customer center') }}`
        },
        {
            condition: window.location.pathname === '/log-blood-pressure',
            label: `{{ config('app.lang') != 'en' ? __('혈압 기록') : __('log blood pressure') }}`
        },
        {
            condition: window.location.pathname === '/log-temperature',
            label: `{{ config('app.lang') != 'en' ? __('체온 기록') : __('log temperature') }}`
        },
        {
            condition: window.location.pathname === '/log-heart-rate',
            label: `{{ config('app.lang') != 'en' ? __('심박수 기록') : __('log heart rate') }}`
        },
        {
            condition: window.location.pathname === '/log-oxygen',
            label: `{{ config('app.lang') != 'en' ? __('산소 기록') : __('log oxygen') }}`
        },
        {
            condition: window.location.pathname === '/log-patient-call-hospital',
            label: `{{ config('app.lang') != 'en' ? __('환자 병원 전화') : __('Patient Call Hospital') }}`
        },
        {
            condition: window.location.pathname === '/log-patient-call-public-welfare',
            label: `{{ config('app.lang') != 'en' ? __('환자 전화 공공 복지') : __('Patient Call public welfare') }}`
        },
        {
            condition: window.location.pathname === '/admin-management',
            label: `{{ config('app.lang') != 'en' ? __('관리자관리') : __('Admin Management') }}`
        },
        {
            condition: window.location.pathname === '/patient-report',
            label: `{{ config('app.lang') != 'en' ? __('환자 보고서') : __('Patient Report') }}`
        },
        ]);


        // Create the breadcrumb item
        breadcrumbs.forEach(item => {
            if (item.condition) {
                const liElement = document.createElement('li');
                liElement.className = 'breadcrumb-item active';
                liElement.setAttribute('aria-current', 'page');
                liElement.innerHTML = item.label;
                breadcrumbContainer.appendChild(liElement);
            }
        });
    }
</script>