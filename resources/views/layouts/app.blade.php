<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Black Dashboard') }}</title>
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('black') }}/icons/general/company-logo.png">
    <link rel="icon" type="image/png" href="{{ asset('black') }}/icons/general/company-logo.png">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:200,300,400,600,700,800" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-pUc6g7+kFUIl7N3A1XyI4mM0CqdziaVawQNR41oICw5XsnCoTg7RkMGrj9j3n2Vy+37ayh6GQjBBlFpl9K7oKg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Icons -->
    <link href="{{ asset('black') }}/css/nucleo-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">

    <!-- CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('black') }}/css/hospital.css?v=1.0.0" rel="stylesheet" />
    <link href="{{ asset('black') }}/css/general-purpose.css?v=1.0.0" rel="stylesheet" />
    <link href="{{ asset('black') }}/css/public-welfare.css?v=1.0.0" rel="stylesheet" />
    <link href="{{ asset('black') }}/css/sales-management.css?v=1.0.0" rel="stylesheet" />
    <link href="{{ asset('black') }}/css/user.css?v=1.0.0" rel="stylesheet" />
    <link href="{{ asset('black') }}/css/setting.css?v=1.0.0" rel="stylesheet" />
    <link href="{{ asset('black') }}/css/setting-public-welfare.css?v=1.0.0" rel="stylesheet" />
    <link href="{{ asset('black') }}/css/black-dashboard.css?v=1.0.0" rel="stylesheet" />
    <link href="{{ asset('black') }}/css/theme.css" rel="stylesheet" />
</head>

<body class="{{ $class ?? '' }}">
    @if (Auth::guard('publicHealthCenter')->check() && !request()->is('cscenter'))
        <div class="wrapper">
            @include('layouts.navbars.sidebar')
            <div class="main-panel">
                @include('layouts.navbars.navbar')
                <div class="content">
                    <div class="container-fluid">
                        @include('layouts.navbars.breadcrum')
                        @yield('content-fluid')
                    </div>
                </div>
                @include('layouts.footer')
            </div>
        </div>
        <form id="logout-form" action="{{ route('public-health-center.logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    @elseif ((Auth::guard('hospital')->check() || Auth::guard('hospitalStaff')->check()) && !request()->is('cscenter'))
        <div class="wrapper">
            <div class="main-panel">
                @include('hospital.layouts.navbars.navbar')
                <div class="content" style="padding:78px 30px 30px 25px">
                    <div class="container-fluid">
                        @include('hospital.layouts.navbars.breadcrum')
                        @yield('content-fluid')
                    </div>
                </div>
                @include('layouts.footer')
            </div>
        </div>
        <form id="logout-form" action="{{ route('hospital.logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    @elseif (Auth::guard('generalPurpose')->check() && !request()->is('cscenter'))
        <div class="wrapper">
            <div class="main-panel">
                @include('general-purpose.layouts.navbars.navbar')
                <div class="content" style="padding:78px 30px 30px 25px">
                    <div class="container-fluid">
                        @yield('content-fluid')
                    </div>
                </div>
                @include('layouts.footer')
            </div>
        </div>
        <form id="logout-form" action="{{ route('general-purpose.logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    @elseif (Auth::guard('publicWelfare')->check() && !request()->is('cscenter'))
        <div class="wrapper">
            <div class="main-panel">
                @include('public-welfare.layouts.navbars.navbar')
                <div class="content" style="padding:78px 30px 30px 25px">
                    <div class="container-fluid">
                        @yield('content-fluid')
                    </div>
                </div>
                @include('layouts.footer')
            </div>
        </div>
        <form id="logout-form" action="{{ route('public-welfare.logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    @else
        <div class="wrapper wrapper-full-page">
            <div class="full-page {{ $contentClass ?? '' }}">
                <div class="content">
                    <div class="container">
                        @yield('content')
                    </div>
                </div>
                @include('layouts.footer')
            </div>
        </div>
    @endif

    <!-- Error Login Popup Modal -->
    <div class="modal fade" id="errorLoginPopupModal">
        <div class="modal-dialog modal-sm custom-error-popup-dialog">
            <div class="modal-content custom-error-popup-content">
                <div class="modal-body text-center custom-error-popup-body" style="background-color:#1E3142">
                    <img src="{{ asset('black') }}/icons/general/error.svg" alt="warning">
                    <p class="custom-error-popup-text" style="color:#FFFFFF">
                        <span class="custom-error-popup-highlight">아이디 or</span> <span
                            class="custom-error-popup-highlight">비밀번호</span> 가 일치하지 않습니다.
                    </p>
                    <p class="custom-error-popup-text" style="color:#FFFFFF">
                        다시 확인해주세요.
                    </p>
                    <button type="button" class="btn btn-primary btn-sm" style="padding: 10px 16px;width:100%"
                        data-dismiss="modal">
                        확인
                    </button>

                </div>
            </div>
        </div>
    </div>
    <!--  -->

    <!-- Error Failed Popup Modal -->
    <div class="modal fade" id="errorFailedPopupModal">
        <div class="modal-dialog modal-sm custom-error-popup-dialog">
            <div class="modal-content custom-error-popup-content">
                <div class="modal-body text-center custom-error-popup-body" style="background-color:#1E3142">
                    <img src="{{ asset('black') }}/icons/general/error.svg" alt="warning">
                    <p class="custom-error-popup-text" style="color:#FFFFFF">
                        {{ config('app.lang') != 'en' ? __('실패한') : __('Failed') }}
                    </p>
                    <button type="button" class="btn btn-primary btn-sm" style="padding: 10px 16px;width:100%"
                        data-dismiss="modal">
                        {{ config('app.lang') != 'en' ? __('확인') : __('Ok') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!--  -->

    <!-- Error Failed With Message Popup Modal -->
    <div class="modal fade" id="errorFailedWithMessagePopupModal">
        <div class="modal-dialog modal-sm custom-error-popup-dialog">
            <div class="modal-content custom-error-popup-content">
                <div class="modal-body text-center custom-error-popup-body" style="background-color:#1E3142">
                    <img src="{{ asset('black') }}/icons/general/error.svg" alt="warning">
                    <p class="custom-error-popup-text" style="color:#FFFFFF" id="error-message-pop-up">
                        <!-- content from javascript -->
                    </p>
                    <button type="button" class="btn btn-primary btn-sm" style="padding: 10px 16px;width:100%"
                        data-dismiss="modal">
                        {{ config('app.lang') != 'en' ? __('확인') : __('Ok') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!--  -->

    <!-- Success Saved Popup Modal -->
    <div class="modal fade" id="successPopupModal">
        <div class="modal-dialog modal-sm custom-error-popup-dialog">
            <div class="modal-content custom-error-popup-content">
                <div class="modal-body text-center custom-error-popup-body" style="background-color:#1E3142">
                    <p class="custom-error-popup-text" style="color:#FFFFFF">
                        {{ config('app.lang') != 'en' ? __('성공') : __('Success') }}
                    </p>
                    <button type="button" class="btn btn-primary btn-sm" style="padding: 10px 16px;width:100%"
                        data-dismiss="modal">
                        {{ config('app.lang') != 'en' ? __('확인') : __('Ok') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!--  -->

    <!-- Success Deleted Popup Modal -->
    <div class="modal fade" id="successDeletedPopupModal">
        <div class="modal-dialog modal-sm custom-error-popup-dialog">
            <div class="modal-content custom-error-popup-content">
                <div class="modal-body text-center custom-error-popup-body" style="background-color:#1E3142">
                    <p class="custom-error-popup-text" style="color:#FFFFFF">
                        {{ config('app.lang') != 'en' ? __('삭제됨') : __('deleted') }}
                    </p>
                    <button type="button" class="btn btn-primary btn-sm" style="padding: 10px 16px;width:100%"
                        data-dismiss="modal">
                        {{ config('app.lang') != 'en' ? __('확인') : __('Ok') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!--  -->

    <!--  Confirmation Delete Popup Modal -->
    <div class="modal fade" id="deletedConfirmationPopUpModal">
        <div class="modal-dialog modal-sm custom-error-popup-dialog">
            <div class="modal-content custom-error-popup-content">
                <div class="modal-body text-center custom-error-popup-body" style="background-color:#1E3142">
                    <p class="custom-error-popup-text" style="color:#FFFFFF">
                        {{ config('app.lang') != 'en' ? __('선택 정보를 삭제 하시겠습니까?') : __('Are you sure you want to delete your selected information?') }}
                    </p>
                    <div style="display: flex; justify-content: space-between; gap: 0.5em;">
                        <button type="button" class="btn btn-primary btn-sm" style="padding: 10px 16px; width: 50%;"
                            id="confirmed">
                            {{ config('app.lang') != 'en' ? __('확인') : __('Yes') }}
                        </button>
                        <button type="button" class="btn btn-warning btn-sm" style="padding: 10px 16px; width: 50%;"
                            data-dismiss="modal">
                            {{ config('app.lang') != 'en' ? __('취소') : __('No') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--  -->

    <!--  Confirmation Save Popup Modal -->
    <div class="modal fade" id="savedConfirmationPopUpModal">
        <div class="modal-dialog modal-sm custom-error-popup-dialog">
            <div class="modal-content custom-error-popup-content">
                <div class="modal-body text-center custom-error-popup-body" style="background-color:#1E3142">
                    <p class="custom-error-popup-text" style="color:#FFFFFF">
                        {{ config('app.lang') != 'en' ? __('입력한 정보로 저장하시겠습니까?') : __('Do you want to save it as the information you entered?') }}
                    </p>
                    <div style="display: flex; justify-content: space-between; gap: 0.5em;">
                        <button type="button" class="btn btn-primary btn-sm" style="padding: 10px 16px; width: 50%;"
                            id="confirmed-saved">
                            {{ config('app.lang') != 'en' ? __('확인') : __('Yes') }}
                        </button>
                        <button type="button" class="btn btn-warning btn-sm" style="padding: 10px 16px; width: 50%;"
                            data-dismiss="modal">
                            {{ config('app.lang') != 'en' ? __('취소') : __('No') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--  -->

    <!--  Confirmation Save With Message Popup Modal -->
    <div class="modal fade" id="savedConfirmationWithMessagePopUpModal">
        <div class="modal-dialog modal-sm custom-error-popup-dialog">
            <div class="modal-content custom-error-popup-content">
                <div class="modal-body text-center custom-error-popup-body" style="background-color:#1E3142">
                    <p class="custom-error-popup-text" style="color:#FFFFFF" id="confirmation-save-message-pop-up">
                        <!-- content from javascript -->
                    </p>
                    <div style="display: flex; justify-content: space-between; gap: 0.5em;">
                        <button type="button" class="btn btn-primary btn-sm" style="padding: 10px 16px; width: 50%;"
                            id="confirmed-saved-with-message">
                            {{ config('app.lang') != 'en' ? __('확인') : __('Yes') }}
                        </button>
                        <button type="button" class="btn btn-warning btn-sm" style="padding: 10px 16px; width: 50%;"
                            data-dismiss="modal">
                            {{ config('app.lang') != 'en' ? __('취소') : __('No') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--  -->

    <!-- Error Session Logout Popup Modal -->
    <div class="modal fade" id="errorSessionLogoutPopupModal">
        <div class="modal-dialog modal-sm custom-error-popup-dialog">
            <div class="modal-content custom-error-popup-content">
                <div class="modal-body text-center custom-error-popup-body" style="background-color:#1E3142">
                    <img src="{{ asset('black') }}/icons/general/error.svg" alt="warning">
                    <p class="custom-error-popup-text" style="color:#FFFFFF">
                        {{ config('app.lang') != 'en' ? __('귀하의 세션이 로그아웃되었습니다. 다시 로그인해주세요') : __('Your session has been logged out. Please log in again') }}
                    </p>
                    <button type="button" class="btn btn-primary btn-sm" style="padding: 10px 16px; width: 100%;"
                        data-dismiss="modal" onclick="window.location.reload();">
                        {{ config('app.lang') != 'en' ? __('확인') : __('Ok') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!--  -->

    <!-- Success Saved User Popup Modal -->
    <div class="modal fade" id="successUserPopupModal">
        <div class="modal-dialog modal-sm custom-error-popup-dialog">
            <div class="modal-content custom-error-popup-content">
                <div class="modal-body text-center custom-error-popup-body" style="background-color:#1E3142">
                    <p class="custom-error-popup-text" style="color:#FFFFFF">
                        {{ config('app.lang') != 'en' ? __('성공') : __('Success') }}
                    </p>
                    <button type="button" class="btn btn-primary btn-sm" style="padding: 10px 16px;width:100%"
                        data-dismiss="modal">
                        {{ config('app.lang') != 'en' ? __('확인') : __('Ok') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!--  -->

    <!-- Error Failed With Message User Popup Modal -->
    <div class="modal fade" id="errorFailedWithMessageUserPopupModal">
        <div class="modal-dialog modal-sm custom-error-popup-dialog">
            <div class="modal-content custom-error-popup-content">
                <div class="modal-body text-center custom-error-popup-body" style="background-color:#1E3142">
                    <img src="{{ asset('black') }}/icons/general/error.svg" alt="warning">
                    <p class="custom-error-popup-text" style="color:#FFFFFF" id="error-message-pop-up">
                        <!-- content from javascript -->
                    </p>
                    <button type="button" class="btn btn-primary btn-sm" style="padding: 10px 16px;width:100%"
                        data-dismiss="modal">
                        {{ config('app.lang') != 'en' ? __('확인') : __('Ok') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!--  -->

    <!-- Success Saved With Message Popup Modal -->
    <div class="modal fade" id="successWithMessagePopupModal">
        <div class="modal-dialog modal-sm custom-error-popup-dialog">
            <div class="modal-content custom-error-popup-content">
                <div class="modal-body text-center custom-error-popup-body" style="background-color:#1E3142">
                    <p class="custom-error-popup-text" style="color:#FFFFFF" id="success-message-pop-up">
                        <!-- content from javascript -->
                    </p>
                    <button type="button" class="btn btn-primary btn-sm" style="padding: 10px 16px;width:100%"
                        data-dismiss="modal">
                        {{ config('app.lang') != 'en' ? __('확인') : __('Ok') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!--  -->

    <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>

    <script src="{{ asset('black') }}/js/core/jquery.min.js"></script>
    <script src="{{ asset('black') }}/js/core/popper.min.js"></script>
    <script src="{{ asset('black') }}/js/core/bootstrap.min.js"></script>
    <script src="{{ asset('black') }}/js/plugins/perfect-scrollbar.jquery.min.js"></script>
    <!--  Google Maps Plugin    -->
    <!-- Place this tag in your head or just before your close body tag. -->
    {{--
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE"></script> --}}
    <!-- Chart JS -->
    {{--
    <script src="{{ asset('black') }}/js/plugins/chartjs.min.js"></script> --}}
    <!--  Notifications Plugin    -->
    <script src="{{ asset('black') }}/js/plugins/bootstrap-notify.js"></script>

    <script src="{{ asset('black') }}/js/black-dashboard.min.js?v=1.0.0"></script>
    <script src="{{ asset('black') }}/js/theme.js"></script>
    <!-- <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    @stack('js')

    <script>
        function createPagination(data, limit, loadFunctionName, paginationId) {
            const paginationLinksPc = document.getElementById(paginationId);
            paginationLinksPc.innerHTML = '';

            const paginationContainer = document.createElement('div');
            paginationContainer.classList.add('pagination-container');

            const paginationInfo = document.createElement('div');
            paginationInfo.classList.add('pagination-info');
            paginationInfo.innerHTML = `
        @if (config('app.lang') != 'en')
            ${data.total}개 항목 중 ${data.from}~${data.to}개 표시 중
        @else
            Showing ${data.from} to ${data.to} of ${data.total} entries
        @endif
    `;
            paginationContainer.appendChild(paginationInfo);

            const nav = document.createElement('nav');
            nav.setAttribute('data-pagination', '');

            // First Page Link
            const firstPageLink = document.createElement('a');
            firstPageLink.href = '#';
            firstPageLink.innerHTML = '<i class="ion-chevron-left-double"></i>';
            if (data.current_page !== 1) {
                firstPageLink.onclick = (e) => {
                    e.preventDefault();
                    window[loadFunctionName](1, limit);
                };
            } else {
                firstPageLink.classList.add('disabled');
            }
            nav.appendChild(firstPageLink);

            // Previous 5 Pages Link
            const prev5PageLink = document.createElement('a');
            prev5PageLink.href = '#';
            prev5PageLink.innerHTML = '<i class="ion-chevron-left"></i><i class="ion-chevron-left"></i>';
            if (data.current_page > 5) {
                prev5PageLink.onclick = (e) => {
                    e.preventDefault();
                    window[loadFunctionName](data.current_page - 5, limit);
                };
            } else {
                prev5PageLink.classList.add('disabled');
            }
            nav.appendChild(prev5PageLink);

            // Previous Page Link
            const previousPageLink = document.createElement('a');
            previousPageLink.href = '#';
            previousPageLink.innerHTML = '<i class="ion-chevron-left"></i>';
            if (data.prev_page_url) {
                previousPageLink.onclick = (e) => {
                    e.preventDefault();
                    window[loadFunctionName](new URL(data.prev_page_url).searchParams.get('page'), limit);
                };
            } else {
                previousPageLink.classList.add('disabled');
            }
            nav.appendChild(previousPageLink);

            const ul = document.createElement('ul');

            // Calculate Start and End for Pagination Links
            const start = Math.floor((data.current_page - 1) / 5) * 5 + 1;
            const end = start + 4 > data.last_page ? data.last_page : start + 4;

            for (let i = start; i <= end; i++) {
                const li = document.createElement('li');
                const a = document.createElement('a');
                a.href = 'javascript:void(0)';
                a.innerText = i;
                if (i === data.current_page) {
                    li.classList.add('current');
                } else {
                    a.onclick = () => window[loadFunctionName](i, limit);
                }
                li.appendChild(a);
                ul.appendChild(li);
            }
            nav.appendChild(ul);

            // Next Page Link
            const nextPageLink = document.createElement('a');
            nextPageLink.href = '#';
            nextPageLink.innerHTML = '<i class="ion-chevron-right"></i>';
            if (data.next_page_url) {
                nextPageLink.onclick = (e) => {
                    e.preventDefault();
                    window[loadFunctionName](new URL(data.next_page_url).searchParams.get('page'), limit);
                };
            } else {
                nextPageLink.classList.add('disabled');
            }
            nav.appendChild(nextPageLink);

            // Next 5 Pages Link
            const next5PageLink = document.createElement('a');
            next5PageLink.href = '#';
            next5PageLink.innerHTML = '<i class="ion-chevron-right"></i><i class="ion-chevron-right"></i>';
            if (data.current_page + 5 <= data.last_page) {
                next5PageLink.onclick = (e) => {
                    e.preventDefault();
                    window[loadFunctionName](data.current_page + 5, limit);
                };
            } else {
                next5PageLink.classList.add('disabled');
            }
            nav.appendChild(next5PageLink);

            // Last Page Link
            const lastPageLink = document.createElement('a');
            lastPageLink.href = '#';
            lastPageLink.innerHTML = '<i class="ion-chevron-right-double"></i>';
            if (data.current_page !== data.last_page) {
                lastPageLink.onclick = (e) => {
                    e.preventDefault();
                    window[loadFunctionName](data.last_page, limit);
                };
            } else {
                lastPageLink.classList.add('disabled');
            }
            nav.appendChild(lastPageLink);

            paginationContainer.appendChild(nav);
            paginationLinksPc.appendChild(paginationContainer);

            const paginationLimit = document.createElement('div');
            paginationLimit.classList.add('pagination-limit');
            paginationLimit.innerHTML = `
        <div class="btn-group">
            <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                ${limit} ${limit == 10 ? '{{ __('개씩 보기') }}' : '{{ __(':limit 개씩 보기', [
    'limit' => ` +
            limit + `
]) }}'}
            </button>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="javascript:void(0)" onclick="${loadFunctionName}(${data.current_page}, 10)">10</a>
                <a class="dropdown-item" href="javascript:void(0)" onclick="${loadFunctionName}(${data.current_page}, 25)">25</a>
                <a class="dropdown-item" href="javascript:void(0)" onclick="${loadFunctionName}(${data.current_page}, 50)">50</a>
                <a class="dropdown-item" href="javascript:void(0)" onclick="${loadFunctionName}(${data.current_page}, 100)">100</a>
            </div>
        </div>`;
            paginationContainer.appendChild(paginationLimit);
        }

        function createPaginationWithArg(arg1, data, limit, loadFunctionName, paginationId) {
            const paginationLinksPc = document.getElementById(paginationId);
            paginationLinksPc.innerHTML = '';

            const paginationContainer = document.createElement('div');
            paginationContainer.classList.add('pagination-container');

            const paginationInfo = document.createElement('div');
            paginationInfo.classList.add('pagination-info');
            paginationInfo.innerHTML = `
        @if (config('app.lang') != 'en')
            ${data.total}개 항목 중 ${data.from}~${data.to}개 표시 중
        @else
            Showing ${data.from} to ${data.to} of ${data.total} entries
        @endif
    `;
            paginationContainer.appendChild(paginationInfo);

            const nav = document.createElement('nav');
            nav.setAttribute('data-pagination', '');

            // First Page Link
            const firstPageLink = document.createElement('a');
            firstPageLink.href = '#';
            firstPageLink.innerHTML = '<i class="ion-chevron-left-double"></i>';
            if (data.current_page !== 1) {
                firstPageLink.onclick = (e) => {
                    e.preventDefault();
                    window[loadFunctionName](arg1, 1, limit);
                };
            } else {
                firstPageLink.classList.add('disabled');
            }
            nav.appendChild(firstPageLink);

            // Previous 5 Pages Link
            const prev5PageLink = document.createElement('a');
            prev5PageLink.href = '#';
            prev5PageLink.innerHTML = '<i class="ion-chevron-left"></i><i class="ion-chevron-left"></i>';
            if (data.current_page > 5) {
                prev5PageLink.onclick = (e) => {
                    e.preventDefault();
                    window[loadFunctionName](arg1, data.current_page - 5, limit);
                };
            } else {
                prev5PageLink.classList.add('disabled');
            }
            nav.appendChild(prev5PageLink);

            // Previous Page Link
            const previousPageLink = document.createElement('a');
            previousPageLink.href = '#';
            previousPageLink.innerHTML = '<i class="ion-chevron-left"></i>';
            if (data.prev_page_url) {
                previousPageLink.onclick = (e) => {
                    e.preventDefault();
                    window[loadFunctionName](arg1, new URL(data.prev_page_url).searchParams.get('page'), limit);
                };
            } else {
                previousPageLink.classList.add('disabled');
            }
            nav.appendChild(previousPageLink);

            const ul = document.createElement('ul');

            // Calculate Start and End for Pagination Links
            const start = Math.floor((data.current_page - 1) / 5) * 5 + 1;
            const end = start + 4 > data.last_page ? data.last_page : start + 4;

            for (let i = start; i <= end; i++) {
                const li = document.createElement('li');
                const a = document.createElement('a');
                a.href = 'javascript:void(0)';
                a.innerText = i;
                if (i === data.current_page) {
                    li.classList.add('current');
                } else {
                    a.onclick = () => window[loadFunctionName](arg1, i, limit);
                }
                li.appendChild(a);
                ul.appendChild(li);
            }
            nav.appendChild(ul);

            // Next Page Link
            const nextPageLink = document.createElement('a');
            nextPageLink.href = '#';
            nextPageLink.innerHTML = '<i class="ion-chevron-right"></i>';
            if (data.next_page_url) {
                nextPageLink.onclick = (e) => {
                    e.preventDefault();
                    window[loadFunctionName](arg1, new URL(data.next_page_url).searchParams.get('page'), limit);
                };
            } else {
                nextPageLink.classList.add('disabled');
            }
            nav.appendChild(nextPageLink);

            // Next 5 Pages Link
            const next5PageLink = document.createElement('a');
            next5PageLink.href = '#';
            next5PageLink.innerHTML = '<i class="ion-chevron-right"></i><i class="ion-chevron-right"></i>';
            if (data.current_page + 5 <= data.last_page) {
                next5PageLink.onclick = (e) => {
                    e.preventDefault();
                    window[loadFunctionName](arg1, data.current_page + 5, limit);
                };
            } else {
                next5PageLink.classList.add('disabled');
            }
            nav.appendChild(next5PageLink);

            // Last Page Link
            const lastPageLink = document.createElement('a');
            lastPageLink.href = '#';
            lastPageLink.innerHTML = '<i class="ion-chevron-right-double"></i>';
            if (data.current_page !== data.last_page) {
                lastPageLink.onclick = (e) => {
                    e.preventDefault();
                    window[loadFunctionName](arg1, data.last_page, limit);
                };
            } else {
                lastPageLink.classList.add('disabled');
            }
            nav.appendChild(lastPageLink);

            paginationContainer.appendChild(nav);
            paginationLinksPc.appendChild(paginationContainer);

            const paginationLimit = document.createElement('div');
            paginationLimit.classList.add('pagination-limit');
            paginationLimit.innerHTML = `
        <div class="btn-group">
            <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                ${limit} ${limit == 10 ? '{{ __('개씩 보기') }}' : '{{ __(':limit 개씩 보기', [
    'limit' => ` +
            limit + `
]) }}'}
            </button>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="javascript:void(0)" onclick="${loadFunctionName}(${arg1},${data.current_page}, 10)">10</a>
                <a class="dropdown-item" href="javascript:void(0)" onclick="${loadFunctionName}(${arg1},${data.current_page}, 25)">25</a>
                <a class="dropdown-item" href="javascript:void(0)" onclick="${loadFunctionName}(${arg1},${data.current_page}, 50)">50</a>
                <a class="dropdown-item" href="javascript:void(0)" onclick="${loadFunctionName}(${arg1},${data.current_page}, 100)">100</a>
            </div>
        </div>`;
            paginationContainer.appendChild(paginationLimit);
        }
    </script>

    <script>
        function formatTime(isoString) {
            const date = new Date(isoString);
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            return `${hours}:${minutes}`;
        }

        function getFile(filePath, profile) {

            fetch(filePath, {
                method: 'HEAD'
            })
                .then(response => {
                    if (response.ok) {
                        // File exists
                        profile.src = filePath;
                    } else {
                        // File does not exist
                        console.log('File not found.');
                        // You can set a default image or handle the error here
                        profile.src = `{{ asset('black') }}/img/default-avatar.png`;
                    }
                })
                .catch(error => {
                    // Handle network errors or other issues
                    console.error('Error checking file existence:', error);
                    // You can set a default image or handle the error here
                    profile.src = `{{ asset('black') }}/img/default-avatar.png`;
                });
        }

        function validatePassword(password, confirmPassword, validateConfirmPass = true) {
            // Validasi panjang minimal dan maksimal password
            if (password.length < 8) {
                document.getElementById('error-message-pop-up').innerText = currentLang !== 'en' ?
                    '비밀번호는 최소 8자 이상이어야 합니다.' :
                    'The password must be at least 8 characters long.'
                $('#errorFailedWithMessagePopupModal').modal('show');
                return false;
            }

            if (password.length > 20) {
                document.getElementById('error-message-pop-up').innerText = currentLang !== 'en' ?
                    '비밀번호는 최대 20자 이하여야 합니다.' :
                    'The password must be no more than 20 characters long.'
                $('#errorFailedWithMessagePopupModal').modal('show');
                return false;
            }

            // Regex untuk memeriksa apakah password mengandung setidaknya satu huruf dan satu angka
            const letterNumberRegex = /^(?=.*[A-Za-z])(?=.*\d)/;

            // Validasi apakah password mengandung setidaknya satu huruf dan satu angka
            if (!letterNumberRegex.test(password)) {
                document.getElementById('error-message-pop-up').innerText = currentLang !== 'en' ?
                    '비밀번호는 최소 하나의 문자와 숫자를 포함해야 합니다.' :
                    'The password must contain at least one letter and one number.'
                $('#errorFailedWithMessagePopupModal').modal('show');
                return false;
            }

            // Regex untuk memeriksa apakah password hanya mengandung huruf dan angka
            const alphanumericRegex = /^[A-Za-z\d]+$/;

            // Validasi apakah password hanya mengandung huruf dan angka
            if (!alphanumericRegex.test(password)) {
                document.getElementById('error-message-pop-up').innerText = currentLang !== 'en' ?
                    '비밀번호는 문자와 숫자로만 이루어져야 합니다.' :
                    'The password must contain only letters and numbers.'
                $('#errorFailedWithMessagePopupModal').modal('show');
                return false;
            }

            if (validateConfirmPass) {
                if (password !== confirmPassword) {
                    document.getElementById('error-message-pop-up').innerText = currentLang !== 'en' ?
                        '비밀번호와 확인 비밀번호가 일치하지 않습니다.' :
                        'The password and confirmation password do not match.'
                    $('#errorFailedWithMessagePopupModal').modal('show');
                    return false;
                }
            }

            return true;
        }

        $('#successPopupModal').on('hidden.bs.modal', function () {
            location.reload();
        });

        $('#successDeletedPopupModal').on('hidden.bs.modal', function () {
            location.reload();
        });

        $('#successUserPopupModal').on('hidden.bs.modal', function () {
            window.location.href = `{{ route('user.index') }}`;
        });

        function calculateExpiryDate(startDate, periodMonths) {
            // Konversi startDate menjadi objek Date
            let date = new Date(startDate);

            // Tambahkan jumlah bulan ke tanggal
            date.setMonth(date.getMonth() + periodMonths);

            // Mengembalikan tanggal dalam format 'YYYY-MM-DD'
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0'); // Menambahkan 1 karena bulan dimulai dari 0
            const day = String(date.getDate()).padStart(2, '0');

            return `${year}-${month}-${day}`;
        }

        function isMultipleOf12(number) {
            return number % 12 === 0;
        }

        function clearSelectOptions(selectId) {
            let selectElement = document.getElementById(selectId);
            selectElement.innerHTML = ''; // Menghapus semua opsi
        }

        function addOrSelectOption(selectId, value) {
            let selectElement = document.getElementById(selectId);
            let optionExists = false;

            // Periksa apakah opsi dengan nilai yang sama sudah ada
            for (let i = 0; i < selectElement.options.length; i++) {
                if (selectElement.options[i].value === value) {
                    selectElement.selectedIndex = i;
                    optionExists = true;
                    break;
                }
            }

            // Jika opsi belum ada, tambahkan opsi baru dan pilih
            if (!optionExists) {
                const option = document.createElement('option');
                option.value = value;
                option.textContent = value;
                selectElement.appendChild(option);
                selectElement.value = value; // Pilih opsi yang baru ditambahkan
            }
        }

        function setSelectValue(selectId, value) {
            let selectElement = document.getElementById(selectId);
            for (let i = 0; i < selectElement.options.length; i++) {
                if (selectElement.options[i].value == value) {
                    selectElement.selectedIndex = i;
                    break;
                }
            }
        }

        function removeElementsByName(name) {
            const elements = document.querySelectorAll(`[name='${name}']`);
            if (elements.length > 0) {
                elements.forEach(element => element.remove());
            }
        }

        function updateDatePickers(months) {
            const startDatePickers = document.getElementsByName('start_date_range');
            const endDatePickers = document.getElementsByName('end_date_range');

            const today = new Date();
            const startDate = new Date();
            const endDate = formatDate(today);

            startDate.setMonth(today.getMonth() - months);
            const formattedStartDate = formatDate(startDate);

            startDatePickers.forEach(datePicker => {
                datePicker.value = formattedStartDate;
            });

            endDatePickers.forEach(datePicker => {
                datePicker.value = endDate;
            });

            const datepickerInpatient1 = document.getElementById('datepicker2-1');
            const datepickerInpatient2 = document.getElementById('datepicker2-2');
            if (datepickerInpatient1 && datepickerInpatient2) {
                fetchGetHealthMeasurementHistory('blood_pressure')
            }


            const datepickerGpEmg1 = document.getElementById('datepicker-gp-emergency-history-1');
            const datepickerGpEmg2 = document.getElementById('datepicker-gp-emergency-history-2');
            if (datepickerGpEmg1 && datepickerGpEmg2) {
                loadDetailsEmergencyHistoryUser()
            }

            const datepickerGpPc1 = document.getElementById('datepicker-gp-patient-call-history-1');
            const datepickerGpPc2 = document.getElementById('datepicker-gp-patient-call-history-2');
            if (datepickerGpPc1 && datepickerGpPc2) {
                loadDetailsPatientCallHistoryUser()
            }

            const datepickerGpEmgH1 = document.getElementById('datepicker-gp-page-emg-history-1');
            const datepickerGpEmgH2 = document.getElementById('datepicker-gp-page-emg-history-2');
            if (datepickerGpEmgH1 && datepickerGpEmgH2) {
                var form = document.getElementById('filter-form-gp-emg-h');
                form.submit();
            }


            const datepickerPwEmg1 = document.getElementById('datepicker-pw-emergency-history-1');
            const datepickerPwEmg2 = document.getElementById('datepicker-pw-emergency-history-2');
            if (datepickerPwEmg1 && datepickerPwEmg2) {
                loadDetailsEmergencyHistoryUser()
            }

            const datepickerPwPc1 = document.getElementById('datepicker-pw-patient-call-history-1');
            const datepickerPwPc2 = document.getElementById('datepicker-pw-patient-call-history-2');
            if (datepickerPwPc1 && datepickerPwPc2) {
                loadDetailsPatientCallHistoryUser()
            }

            const datepickerPwEmgH1 = document.getElementById('datepicker-pw-page-emg-history-1');
            const datepickerPwEmgH2 = document.getElementById('datepicker-pw-page-emg-history-2');
            if (datepickerPwEmgH1 && datepickerPwEmgH2) {
                var form = document.getElementById('filter-form-pw-emg-h');
                form.submit();
            }
        }

        function formatDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }


        function exportExcel(url) {
            window.open(url, '_blank');
        }

        function validateNumber(input) {
            input.value = input.value.replace(/[^0-9]/g, '');
        }

        function globalValidateInput() {
            const requiredInputs = document.querySelectorAll('.required-input');
            let allValid = true;

            requiredInputs.forEach(input => {
                const component = input.closest('.component'); // Mendapatkan elemen .component terdekat

                // Validasi untuk elemen input
                if (input.tagName.toLowerCase() === 'input') {
                    if (input.type === 'checkbox') {
                        if (!input.checked) {
                            if (component) {
                                component.style.border = '5px solid #FC565D'; // Tambahkan border pada .component
                            } else {
                                input.closest('label').style.border =
                                    '5px solid #FC565D'; // Jika tidak ada .component, tambahkan border pada label
                            }
                            allValid = false;
                        }
                    } else {
                        if (input.value.trim() === '') {
                            input.style.border = '5px solid #FC565D';
                            allValid = false;
                        }
                    }

                    // Menambahkan event listener hanya jika belum ada
                    if (!input.hasAttribute('data-listener-added')) {
                        input.addEventListener('input', () => {
                            if (input.type === 'checkbox') {
                                if (input.checked) {
                                    if (component) {
                                        component.style.border = ''; // Hapus border pada .component
                                    } else {
                                        input.closest('label').style.border =
                                            ''; // Hapus border pada label jika tidak ada .component
                                    }
                                }
                            } else {
                                if (input.value.trim() !== '') {
                                    input.style.border =
                                        ''; // Menghapus gaya border jika input tidak kosong
                                }
                            }
                        });
                        input.setAttribute('data-listener-added', 'true');
                    }
                }

                // Validasi untuk elemen select
                if (input.tagName.toLowerCase() === 'select') {
                    if (input.value === '' || input.value === null || input.value.toLowerCase() === 'Select'
                        .toLowerCase() || input.value === '선택') {
                        input.style.border = '5px solid #FC565D';
                        allValid = false;
                    }

                    // Menambahkan event listener hanya jika belum ada
                    if (!input.hasAttribute('data-listener-added')) {
                        input.addEventListener('change', () => {
                            if (input.value !== '' && input.value !== null) {
                                input.style.border = ''; // Menghapus gaya border jika select memiliki nilai
                            }
                        });
                        input.setAttribute('data-listener-added', 'true');
                    }
                }

                // Validasi untuk elemen textarea
                if (input.tagName.toLowerCase() === 'textarea') {
                    if (input.value.trim() === '') {
                        input.style.border = '5px solid #FC565D';
                        allValid = false;
                    }

                    // Menambahkan event listener hanya jika belum ada
                    if (!input.hasAttribute('data-listener-added')) {
                        input.addEventListener('input', () => {
                            if (input.value.trim() !== '') {
                                input.style.border = ''; // Menghapus gaya border jika textarea tidak kosong
                            }
                        });
                        input.setAttribute('data-listener-added', 'true');
                    }
                }
            });

            return allValid;
        }

        function globalValidateInputSinglePage() {
            const requiredInputs = document.querySelectorAll('.required-input');
            let allValid = true;

            requiredInputs.forEach(input => {
                // Periksa apakah parent card memiliki display yang tidak none
                const parentCard = input.closest('.custom-card');
                if (parentCard && window.getComputedStyle(parentCard).display !== 'none') {
                    // Validasi untuk elemen input
                    if (input.tagName.toLowerCase() === 'input') {
                        if (input.value.trim() === '') {
                            input.style.border = '5px solid #FC565D';
                            allValid = false;
                        }

                        // Menambahkan event listener hanya jika belum ada
                        if (!input.hasAttribute('data-listener-added')) {
                            input.addEventListener('input', () => {
                                if (input.value.trim() !== '') {
                                    input.style.border =
                                        ''; // Menghapus gaya perbatasan jika input tidak kosong
                                }
                            });
                            input.setAttribute('data-listener-added', 'true');
                        }
                    }

                    // Validasi untuk elemen select
                    if (input.tagName.toLowerCase() === 'select') {
                        if (input.value === '' || input.value === null || input.value.toLowerCase() === 'select' ||
                            input.value === '선택') {
                            input.style.border = '5px solid #FC565D';
                            allValid = false;
                        }

                        // Menambahkan event listener hanya jika belum ada
                        if (!input.hasAttribute('data-listener-added')) {
                            input.addEventListener('change', () => {
                                if (input.value !== '' && input.value !== null && input.value
                                    .toLowerCase() !== 'select' && input.value !== '선택') {
                                    input.style.border =
                                        ''; // Menghapus gaya perbatasan jika select memiliki nilai
                                }
                            });
                            input.setAttribute('data-listener-added', 'true');
                        }
                    }
                }
            });

            return allValid;
        }

        function globalValidateInputForModal(modalId) {
            const modal = document.getElementById(modalId);
            const requiredInputs = modal.querySelectorAll('.required-input');
            let allValid = true;

            requiredInputs.forEach(input => {
                // Validasi untuk elemen input
                if (input.tagName.toLowerCase() === 'input') {
                    if (input.value.trim() === '') {
                        input.style.border = '5px solid #FC565D';
                        allValid = false;
                    }

                    // Menambahkan event listener hanya jika belum ada
                    if (!input.hasAttribute('data-listener-added')) {
                        input.addEventListener('input', () => {
                            if (input.value.trim() !== '') {
                                input.style.border =
                                    ''; // Menghapus gaya perbatasan jika input tidak kosong
                            }
                        });
                        input.setAttribute('data-listener-added', 'true');
                    }
                }

                // Validasi untuk elemen select
                if (input.tagName.toLowerCase() === 'select') {
                    if (input.value === '' || input.value === null || input.value.toLowerCase() === 'select' ||
                        input.value === '선택') {
                        input.style.border = '5px solid #FC565D';
                        allValid = false;
                    }

                    // Menambahkan event listener hanya jika belum ada
                    if (!input.hasAttribute('data-listener-added')) {
                        input.addEventListener('change', () => {
                            if (input.value !== '' && input.value !== null && input.value.toLowerCase() !==
                                'select' && input.value !== '선택') {
                                input.style.border =
                                    ''; // Menghapus gaya perbatasan jika select memiliki nilai
                            }
                        });
                        input.setAttribute('data-listener-added', 'true');
                    }
                }

                // Validasi untuk elemen textarea
                if (input.tagName.toLowerCase() === 'textarea') {
                    if (input.value.trim() === '') {
                        input.style.border = '5px solid #FC565D';
                        allValid = false;
                    }

                    // Menambahkan event listener hanya jika belum ada
                    if (!input.hasAttribute('data-listener-added')) {
                        input.addEventListener('input', () => {
                            if (input.value.trim() !== '') {
                                input.style.border =
                                    ''; // Menghapus gaya perbatasan jika textarea tidak kosong
                            }
                        });
                        input.setAttribute('data-listener-added', 'true');
                    }
                }
            });

            return allValid;
        }

        function globalValidateInputWithUniqueName(name) {
            const requiredInputs = document.querySelectorAll('.' + name);
            let allValid = true;

            requiredInputs.forEach(input => {
                // Validasi untuk elemen input
                if (input.tagName.toLowerCase() === 'input') {
                    if (input.type === 'checkbox') {
                        if (!input.checked) {
                            input.closest('label').style.border = '5px solid #FC565D';
                            allValid = false;
                        }
                    } else {
                        if (input.value.trim() === '') {
                            input.style.border = '5px solid #FC565D';
                            allValid = false;
                        }
                    }


                    // Menambahkan event listener hanya jika belum ada
                    if (!input.hasAttribute('data-listener-added')) {
                        input.addEventListener('input', () => {
                            if (input.type === 'checkbox') {
                                if (input.checked) {
                                    input.closest('label').style.border =
                                        ''; // Menghapus gaya perbatasan jika input tidak kosong
                                }
                            } else {
                                if (input.value.trim() !== '') {
                                    input.style.border =
                                        ''; // Menghapus gaya perbatasan jika input tidak kosong
                                }
                            }

                        });
                        input.setAttribute('data-listener-added', 'true');
                    }
                }

                // Validasi untuk elemen select
                if (input.tagName.toLowerCase() === 'select') {
                    if (input.value === '' || input.value === null || input.value.toLowerCase() === 'Select'
                        .toLowerCase() || input.value === '선택') {
                        input.style.border = '5px solid #FC565D';
                        allValid = false;
                    }

                    // Menambahkan event listener hanya jika belum ada
                    if (!input.hasAttribute('data-listener-added')) {
                        input.addEventListener('change', () => {
                            if (input.value !== '' && input.value !== null) {
                                input.style.border =
                                    ''; // Menghapus gaya perbatasan jika select memiliki nilai
                            }
                        });
                        input.setAttribute('data-listener-added', 'true');
                    }
                }

                // Validasi untuk elemen textarea
                if (input.tagName.toLowerCase() === 'textarea') {
                    if (input.value.trim() === '') {
                        input.style.border = '5px solid #FC565D';
                        allValid = false;
                    }

                    // Menambahkan event listener hanya jika belum ada
                    if (!input.hasAttribute('data-listener-added')) {
                        input.addEventListener('input', () => {
                            if (input.value.trim() !== '') {
                                input.style.border =
                                    ''; // Menghapus gaya perbatasan jika textarea tidak kosong
                            }
                        });
                        input.setAttribute('data-listener-added', 'true');
                    }
                }
            });

            return allValid;
        }

        document.addEventListener('DOMContentLoaded', function () {
            var numericInputs = document.querySelectorAll('.numeric-input');
            numericInputs.forEach(function (input) {
                input.addEventListener('input', function () {
                    validateNumber(input);
                });
            });

            function setupDropdowns() {
                var dropdownButtons = document.getElementsByClassName('dropdown-toggle');

                for (var i = 0; i < dropdownButtons.length; i++) {
                    (function (index) {
                        var dropdownButton = dropdownButtons[index];

                        if (dropdownButton.closest('.exclude-dropdown')) {
                            return;
                        }

                        var dropdownMenu = dropdownButton.nextElementSibling;
                        var dropdownItems = dropdownMenu.getElementsByClassName('dropdown-item');

                        for (var j = 0; j < dropdownItems.length; j++) {
                            dropdownItems[j].addEventListener('click', function (e) {
                                e.preventDefault();
                                var selectedText = this.innerText;
                                var selectedValue = this.getAttribute('data-value');
                                dropdownButton.textContent = selectedText; // Use textContent here

                                if (dropdownButton.getAttribute('id')) {
                                    var hiddenInput = dropdownButton.getAttribute('id').replace(
                                        'Button',
                                        '');
                                    var inputElement = document.getElementById(hiddenInput);
                                    if (inputElement) {
                                        inputElement.value = selectedValue;
                                    }

                                }
                            });
                        }
                    })(i);
                }
            }

            function getQueryParam(param) {
                var urlParams = new URLSearchParams(window.location.search);
                return urlParams.get(param);
            }

            function setDropdownDefault() {
                var searchBy = getQueryParam('search_by');
                var fullTerm = getQueryParam('full_term');
                var startDate = getQueryParam('start_date_range');
                var endDate = getQueryParam('end_date_range');
                var search = getQueryParam('search');
                var usage_status = getQueryParam('usage_status');

                if (searchBy) {
                    var searchByButton = document.getElementById('searchByButton');
                    var searchByItems = searchByButton.nextElementSibling.getElementsByClassName('dropdown-item');
                    for (var i = 0; i < searchByItems.length; i++) {
                        if (searchByItems[i].getAttribute('data-value') === searchBy) {
                            var selectedText = searchByItems[i].innerText;
                            searchByButton.textContent = selectedText; // Use textContent here
                            var hiddenInput = document.getElementById('searchBy');
                            if (hiddenInput) {
                                hiddenInput.value = searchBy;
                            }
                            break;
                        }
                    }
                }

                if (fullTerm) {
                    var fullTermButton = document.getElementById('fullTermButton');
                    var fullTermItems = fullTermButton.nextElementSibling.getElementsByClassName('dropdown-item');
                    for (var i = 0; i < fullTermItems.length; i++) {
                        if (fullTermItems[i].getAttribute('data-value') === fullTerm) {
                            var selectedText = fullTermItems[i].innerText;
                            fullTermButton.textContent = selectedText; // Use textContent here
                            var hiddenInput = document.getElementById('fullTerm');
                            if (hiddenInput) {
                                hiddenInput.value = fullTerm;
                            }
                            break;
                        }
                    }
                }

                if (startDate) {
                    let dateFrom = document.getElementById('datepicker-1');
                    if (dateFrom) {
                        dateFrom.value = startDate;
                    }

                    // general purpose
                    dateFrom = document.getElementById('datepicker-gp-page-emg-history-1');
                    if (dateFrom) {
                        dateFrom.value = startDate;
                    }

                    dateFrom = document.getElementById('datepicker-gp-emergency-history-1');
                    if (dateFrom) {
                        dateFrom.value = startDate;
                    }

                    dateFrom = document.getElementById('datepicker-gp-patient-call-history-1');
                    if (dateFrom) {
                        dateFrom.value = startDate;
                    }


                    // public welfare
                    dateFrom = document.getElementById('datepicker-pw-page-emg-history-1');
                    if (dateFrom) {
                        dateFrom.value = startDate;
                    }

                    dateFrom = document.getElementById('datepicker-pw-emergency-history-1');
                    if (dateFrom) {
                        dateFrom.value = startDate;
                    }

                    dateFrom = document.getElementById('datepicker-pw-patient-call-history-1');
                    if (dateFrom) {
                        dateFrom.value = startDate;
                    }
                }

                if (endDate) {
                    let dateTo = document.getElementById('datepicker-2');
                    if (dateTo) {
                        dateTo.value = endDate;
                    }

                    // general purpose
                    dateTo = document.getElementById('datepicker-gp-page-emg-history-2');
                    if (dateTo) {
                        dateTo.value = endDate;
                    }

                    dateTo = document.getElementById('datepicker-gp-emergency-history-2');
                    if (dateTo) {
                        dateTo.value = endDate;
                    }

                    dateTo = document.getElementById('datepicker-gp-patient-call-history-2');
                    if (dateTo) {
                        dateTo.value = endDate;
                    }


                    // public welfare
                    dateTo = document.getElementById('datepicker-pw-page-emg-history-2');
                    if (dateTo) {
                        dateTo.value = endDate;
                    }

                    dateTo = document.getElementById('datepicker-pw-emergency-history-2');
                    if (dateTo) {
                        dateTo.value = endDate;
                    }

                    dateTo = document.getElementById('datepicker-pw-patient-call-history-2');
                    if (dateTo) {
                        dateTo.value = endDate;
                    }
                }

                if (search) {
                    document.getElementById('searchInput').value = search;
                }

                if (usage_status) {
                    var fullTermButton = document.getElementById('usage_status');
                    var fullTermItems = fullTermButton.nextElementSibling.getElementsByClassName('dropdown-item');
                    for (var i = 0; i < fullTermItems.length; i++) {
                        if (fullTermItems[i].getAttribute('data-status') === usage_status) {
                            var selectedText = fullTermItems[i].innerText;
                            fullTermButton.textContent = selectedText; // Use textContent here
                            var hiddenInput = document.getElementById('usage_status');
                            if (hiddenInput) {
                                hiddenInput.value = usage_status;
                            }
                            break;
                        }
                    }
                }
            }

            setupDropdowns();
            setDropdownDefault();
        });
    </script>
    <script>
        let currentMonth = new Date().getMonth();
        let currentYear = new Date().getFullYear();
        let currentLang = `{{ config('app.lang') != 'en' ? __('ko') : __('en') }}`; // Default language

        const translations = {
            en: {
                months: [
                    "January", "February", "March", "April", "May", "June",
                    "July", "August", "September", "October", "November", "December"
                ],
                daysOfWeek: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"]
            },
            ko: {
                months: [
                    "1월", "2월", "3월", "4월", "5월", "6월",
                    "7월", "8월", "9월", "10월", "11월", "12월"
                ],
                daysOfWeek: ["일", "월", "화", "수", "목", "금", "토"]
            }
        };

        function toggleLanguage(lang) {
            currentLang = lang;
            // Update the calendar with the new language
            document.querySelectorAll('.calendar').forEach(calendar => {
                const calendarId = calendar.id;
                createCalendar(calendarId, currentYear, currentMonth);
            });
        }

        function toggleCalendar(calendarId) {
            const calendar = document.getElementById(calendarId);
            const datePickerContainer = calendar.closest('.date-picker-container');

            if (calendar.style.display === 'block') {
                calendar.style.display = 'none';
            } else {
                // Close all open calendars
                document.querySelectorAll('.calendar').forEach(cal => cal.style.display = 'none');

                calendar.style.display = 'block';
                if (!calendar.innerHTML) {
                    createCalendar(calendarId, currentYear, currentMonth);
                }

                // Add an event listener for clicks outside the calendar and date picker container
                document.addEventListener('click', function handleClickOutside(event) {
                    if (!datePickerContainer.contains(event.target)) {
                        calendar.style.display = 'none';
                        document.removeEventListener('click', handleClickOutside);
                    }
                });
            }
        }

        function createCalendar(calendarId, year, month) {
            const calendar = document.getElementById(calendarId);

            calendar.innerHTML = `
    <table>
        <thead>
            <tr>
                <th><span class="nav-button" onclick="changeMonth('${calendarId}', -1)">&lt;</span></th>
                <th colspan="5">
                    <div class="select-dropdown">
                        <select id="${calendarId}-month" onchange="updateCalendar('${calendarId}')">
                            ${translations[currentLang].months.map((m, index) => `<option value="${index}" ${index === month ? 'selected' : ''}>${m}</option>`).join('')}
                        </select>
                    </div>
                    <div class="select-dropdown">
                        <select id="${calendarId}-year" onchange="updateCalendar('${calendarId}')">
                            ${generateYearOptions(year).map(y => `<option value="${y}" ${y === year ? 'selected' : ''}>${y}</option>`).join('')}
                        </select>
                    </div>
                </th>
                <th><span class="nav-button" onclick="changeMonth('${calendarId}', 1)">&gt;</span></th>
            </tr>
            <tr>${translations[currentLang].daysOfWeek.map(day => `<th>${day}</th>`).join('')}</tr>
        </thead>
        <tbody>
            ${generateCalendarDays(year, month, calendarId)}
        </tbody>
    </table>
    `;
        }

        function generateYearOptions(currentYear) {
            const years = [];
            for (let i = currentYear - 50; i <= currentYear + 50; i++) {
                years.push(i);
            }
            return years;
        }

        function generateCalendarDays(year, month, calendarId) {
            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            let days = "";
            let dayCount = 1;

            for (let i = 0; i < 6; i++) {
                days += "<tr>";
                for (let j = 0; j < 7; j++) {
                    if (i === 0 && j < firstDay) {
                        days += "<td></td>";
                    } else if (dayCount > daysInMonth) {
                        days += "<td></td>";
                    } else {
                        const inputId = calendarId.replace('calendar', 'datepicker');
                        days +=
                            `<td onclick="selectDate(${year}, ${month}, ${dayCount}, '${inputId}', '${calendarId}')">${dayCount}</td>`;
                        dayCount++;
                    }
                }
                days += "</tr>";
            }
            return days;
        }

        function selectDate(year, month, day, datepickerId, calendarId) {
            const datepicker = document.getElementById(datepickerId);
            const formattedDate = `${year}-${(month + 1).toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
            datepicker.value = formattedDate;
            document.getElementById(calendarId).style.display = 'none';

            if (datepickerId === 'datepicker2-1' || datepickerId === 'datepicker2-2') {
                fetchGetHealthMeasurementHistory('blood_pressure')
            }

            if (datepickerId === 'datepicker-gp-emergency-history-1' || datepickerId ===
                'datepicker-gp-emergency-history-2') {
                loadDetailsEmergencyHistoryUser()
            }

            if (datepickerId === 'datepicker-gp-patient-call-history-1' || datepickerId ===
                'datepicker-gp-patient-call-history-2') {
                loadDetailsPatientCallHistoryUser()
            }

            if (datepickerId === 'datepicker-gp-page-emg-history-1' || datepickerId ===
                'datepicker-gp-page-emg-history-2') {
                var form = document.getElementById('filter-form-gp-emg-h');
                form.submit();
            }


            if (datepickerId === 'datepicker-pw-emergency-history-1' || datepickerId ===
                'datepicker-pw-emergency-history-2') {
                loadDetailsEmergencyHistoryUser()
            }

            if (datepickerId === 'datepicker-pw-patient-call-history-1' || datepickerId ===
                'datepicker-pw-patient-call-history-2') {
                loadDetailsPatientCallHistoryUser()
            }

            if (datepickerId === 'datepicker-pw-page-emg-history-1' || datepickerId ===
                'datepicker-pw-page-emg-history-2') {
                var form = document.getElementById('filter-form-pw-emg-h');
                form.submit();
            }


        }

        function changeMonth(calendarId, change) {
            currentMonth += change;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            } else if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            createCalendar(calendarId, currentYear, currentMonth);
        }

        function updateCalendar(calendarId) {
            const selectedMonth = parseInt(document.getElementById(`${calendarId}-month`).value, 10);
            const selectedYear = parseInt(document.getElementById(`${calendarId}-year`).value, 10);
            currentMonth = selectedMonth;
            currentYear = selectedYear;
            createCalendar(calendarId, currentYear, currentMonth);
        }
    </script>

    <script>
        $(document).ready(function () {
            $().ready(function () {
                $sidebar = $('.sidebar');
                $navbar = $('.navbar');
                $main_panel = $('.main-panel');

                $full_page = $('.full-page');

                $sidebar_responsive = $('body > .navbar-collapse');
                sidebar_mini_active = true;
                white_color = false;

                window_width = $(window).width();

                fixed_plugin_open = $('.sidebar .sidebar-wrapper .nav li.active a p').html();

                $('.fixed-plugin a').click(function (event) {
                    if ($(this).hasClass('switch-trigger')) {
                        if (event.stopPropagation) {
                            event.stopPropagation();
                        } else if (window.event) {
                            window.event.cancelBubble = true;
                        }
                    }
                });

                $('.fixed-plugin .background-color span').click(function () {
                    $(this).siblings().removeClass('active');
                    $(this).addClass('active');

                    var new_color = $(this).data('color');

                    if ($sidebar.length != 0) {
                        $sidebar.attr('data', new_color);
                    }

                    if ($main_panel.length != 0) {
                        $main_panel.attr('data', new_color);
                    }

                    if ($full_page.length != 0) {
                        $full_page.attr('filter-color', new_color);
                    }

                    if ($sidebar_responsive.length != 0) {
                        $sidebar_responsive.attr('data', new_color);
                    }
                });

                $('.switch-sidebar-mini input').on("switchChange.bootstrapSwitch", function () {
                    var $btn = $(this);

                    if (sidebar_mini_active == true) {
                        $('body').removeClass('sidebar-mini');
                        sidebar_mini_active = false;
                        blackDashboard.showSidebarMessage('Sidebar mini deactivated...');
                    } else {
                        $('body').addClass('sidebar-mini');
                        sidebar_mini_active = true;
                        blackDashboard.showSidebarMessage('Sidebar mini activated...');
                    }

                    // we simulate the window Resize so the charts will get updated in realtime.
                    var simulateWindowResize = setInterval(function () {
                        window.dispatchEvent(new Event('resize'));
                    }, 180);

                    // we stop the simulation of Window Resize after the animations are completed
                    setTimeout(function () {
                        clearInterval(simulateWindowResize);
                    }, 1000);
                });

                $('.switch-change-color input').on("switchChange.bootstrapSwitch", function () {
                    var $btn = $(this);

                    if (white_color == true) {
                        $('body').addClass('change-background');
                        setTimeout(function () {
                            $('body').removeClass('change-background');
                            $('body').removeClass('white-content');
                        }, 900);
                        white_color = false;
                    } else {
                        $('body').addClass('change-background');
                        setTimeout(function () {
                            $('body').removeClass('change-background');
                            $('body').addClass('white-content');
                        }, 900);

                        white_color = true;
                    }
                });
            });
        });
    </script>
    @stack('js')
</body>

</html>