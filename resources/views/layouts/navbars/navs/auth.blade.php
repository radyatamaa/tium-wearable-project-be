<nav class="navbar fixed-top navbar-expand-lg">
    <div class="container-fluid">
        <div class="navbar-wrapper">
            <div class="navbar-toggle d-inline">
                <button type="button" class="navbar-toggler">
                    <span class="navbar-toggler-bar bar1"></span>
                    <span class="navbar-toggler-bar bar2"></span>
                    <span class="navbar-toggler-bar bar3"></span>
                </button>
            </div>
            <a class="navbar-brand" href="/home">
                <img src="{{ asset('black') }}/icons/general/logo-navbar.svg">
            </a>
        </div>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation"
            aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-bar navbar-kebab"></span>
            <span class="navbar-toggler-bar navbar-kebab"></span>
            <span class="navbar-toggler-bar navbar-kebab"></span>
        </button>
        <div class="collapse navbar-collapse" id="navigation">
            <ul class="navbar-nav ml-auto">
                @if ($pageSlug == 'dashboard')
                <form id="filter-dashboard" method="GET" action="/home" class="d-flex justify-content-between w-100">
                    <div class="btn-group" style="margin-right:10px">
                        <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false" id="fullTermButton">
                            @if (config('app.lang') != 'en')
                            {{ __('선택') }}
                            @else
                            {{ __('Select') }}
                            @endif
                        </button>
                        <div class="dropdown-menu" id="dynamic-dropdown-services">
                            <!-- Dropdown items will be injected here by JavaScript -->
                        </div>
                    </div>
                    <div class="btn-group" style="margin-right:10px">
                        <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false" id="searchByButton">
                            @if (config('app.lang') != 'en')
                            {{ __('선택') }}
                            @else
                            {{ __('Select') }}
                            @endif
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="#" data-value="user_id">
                                @if (config('app.lang') != 'en')
                                {{ __('아이디') }}
                                @else
                                {{ __('ID') }}
                                @endif
                            </a>
                            <a class="dropdown-item" href="#" data-value="name">
                                @if (config('app.lang') != 'en')
                                {{ __('업체(기관)명') }}
                                @else
                                {{ __('Organization') }}
                                @endif
                            </a>
                        </div>
                    </div>
                    <input type="hidden" name="search_by" id="searchBy">
                    <input type="hidden" name="full_term" id="fullTerm">
                    <div class="search-container">
                        <input type="text" class="search-input"
                            placeholder="{{ config('app.lang') != 'en' ? '검색어를 입력해 주세요' : 'Please enter a search term' }}"
                            name="search" id="searchInput">
                        <button type="submit" class="search-button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
                @endif
                <li class="dropdown nav-item">
                    <ul class="dropdown-menu dropdown-menu-right dropdown-navbar">
                        <li class="nav-link">
                            <a href="#" class="nav-item dropdown-item">{{ __('Mike John responded to your email') }}</a>
                        </li>
                        <li class="nav-link">
                            <a href="#" class="nav-item dropdown-item">{{ __('You have 5 more tasks') }}</a>
                        </li>
                        <li class="nav-link">
                            <a href="#" class="nav-item dropdown-item">{{ __('Your friend Michael is in town') }}</a>
                        </li>
                        <li class="nav-link">
                            <a href="#" class="nav-item dropdown-item">{{ __('Another notification') }}</a>
                        </li>
                        <li class="nav-link">
                            <a href="#" class="nav-item dropdown-item">{{ __('Another one') }}</a>
                        </li>
                    </ul>
                </li>
                <li class="dropdown nav-item exclude-dropdown">
                    <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
                        <div class="photo">
                            <img src="{{ asset('black') }}/img/default-avatar.png" alt="{{ __('Profile Photo') }}">
                        </div>
                        <b class="caret d-none d-lg-block d-xl-block"></b>
                        <p class="d-lg-none">{{ config('app.lang') != 'en' ? __('로그아웃') : __('Log out'); }}</p>
                    </a>
                    <ul class="dropdown-menu dropdown-navbar">
                        <!-- <li class="nav-link">
                            <a href="{{ route('profile.edit') }}" class="nav-item dropdown-item">{{ __('Profile') }}</a>
                        </li>
                        <li class="nav-link">
                            <a href="#" class="nav-item dropdown-item">{{ __('Settings') }}</a>
                        </li> -->
                        <!-- <li class="dropdown-divider"></li> -->
                        <li class="nav-link">
                            <a href="{{ route('public-health-center.logout') }}" class="nav-item dropdown-item"
                                onclick="event.preventDefault();  document.getElementById('logout-form').submit();">{{ config('app.lang') != 'en' ? __('로그아웃') : __('Log out'); }}</a>
                        </li>
                    </ul>
                </li>
                <li class="separator d-lg-none"></li>
            </ul>
        </div>
    </div>
</nav>
<div class="modal modal-search fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="searchModal"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <input type="text" class="form-control" id="inlineFormInputGroup" placeholder="{{ __('SEARCH') }}">
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                    <i class="tim-icons icon-simple-remove"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function fetchDataServices() {
    fetch('/setting-job-title-list')
        .then(response => response.json())
        .then(data => {
            data = data.data
            loadSideBarUserManagement(data);
            loadDropdownFilterService(data);
            loadBreadcrum(data);
        })
        .catch(error => {
            console.error('Error fetching data:', error);
        });
}

function loadDropdownFilterService(serviceClassifications) {
    const dropdownItems = [{
        value: 'ALL',
        label: `{{ config('app.lang') != "en" ? __("모두") : __("All") }}`
    }];

    serviceClassifications.forEach(e => {
        dropdownItems.push({
            value: e.service_classification,
            label: e.service_classification_desc
        });
    });

    const dropdownMenu = document.getElementById('dynamic-dropdown-services');
    const fullTermButton = document.getElementById('fullTermButton');
    const fullTermInput = document.getElementById('fullTerm');

    if (dropdownMenu) {
        dropdownItems.forEach((item, index) => {
            const aElement = document.createElement('a');
            aElement.id = `dropdown-item-${index}`; // Assign a specific ID to each item
            aElement.className = 'dropdown-item';
            aElement.href = '#';
            aElement.setAttribute('data-value', item.value);
            aElement.innerHTML = item.label;

            // Add click event listener to update the button text and URL
            aElement.addEventListener('click', function(e) {
                e.preventDefault(); // Prevent the default anchor behavior

                // Update the button text and hidden input
                fullTermButton.innerHTML = item.label;
                fullTermInput.value = item.value;

                // Update URL with the selected value
                const url = new URL(window.location.href);
                url.searchParams.set('full_term', item.value);
                window.history.replaceState({}, '', url);
            });

            dropdownMenu.appendChild(aElement);
        });

        // Set the initial value based on the URL parameter (if exists)
        const urlParams = new URLSearchParams(window.location.search);
        const fullTermValue = urlParams.get('full_term');

        if (fullTermValue) {
            const selectedItem = dropdownItems.find(item => item.value === fullTermValue);
            if (selectedItem) {
                fullTermButton.innerHTML = selectedItem.label;
                fullTermInput.value = selectedItem.value;
            }
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    fetchDataServices();
});
</script>