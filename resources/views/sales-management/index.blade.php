@php
$pageTitle = config('app.lang') != 'en' ? __('매출관리') : __('Sales Management');
@endphp

@extends('layouts.app', ['page' => $pageTitle, 'pageSlug' => 'sales_management'])

@section('content-fluid')
<!-- filters -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-primary d-flex justify-content-between"
                style="--header-background-color: #1E3142; background-color: var(--header-background-color);padding: 5px;">
                <form id="filter-form" method="GET" action="/sales-management"
                    class="d-flex justify-content-between w-100">
                    <div class="header-hospital-filter-left">
                        <div class="btn-group">
                            <div class="date-picker-container">
                                <i class="fas fa-calendar-alt" onclick="toggleCalendar('calendar-1')"></i>
                                <input type="text" id="datepicker-1" value="YYYY-MM-DD" name="start_date_range">
                                <div class="calendar" id="calendar-1"></div>
                            </div>
                        </div>
                        <div class="btn-group">
                            <div class="date-picker-container">
                                <i class="fas fa-calendar-alt" onclick="toggleCalendar('calendar-2')"></i>
                                <input type="text" id="datepicker-2" value="YYYY-MM-DD" name="end_date_range">
                                <div class="calendar" id="calendar-2"></div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-info btn-sm" type="button" style="padding: 10px 16px">
                            @if (config('app.lang') != 'en')
                            {{ __('검색') }}
                            @else
                            {{ __('Search') }}
                            @endif
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--  -->

<!-- summary report  -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="summary-row">
                    <div>{{ config('app.lang') != 'en' ? __('총 결제금액') : __('Total Payment Amount') }}</div>
                    <div>{{ number_format($summary->sum('total_payment_amount')) }}원</div>
                    <div>
                        {{ config('app.lang') != 'en' ? __('고객사가 결제한 금액입니다.') : __('This is the amount paid by the customer.') }}
                    </div>
                </div>
                <div class="summary-row">
                    <div>{{ config('app.lang') != 'en' ? __('총 결제건수') : __('Total Number of Payments') }}</div>
                    <div>{{ number_format($summary->sum('total_number_of_payments')) }}건</div>
                    <div>
                        {{ config('app.lang') != 'en' ? __('고객사가 결제한 총 건수입니다.') : __('This is the total number of payments made by the customer.') }}
                    </div>
                </div>
                <div class="summary-row">
                    <div>{{ config('app.lang') != 'en' ? __('결제대행수수료') : __('Payment Agency Fee') }}</div>
                    <div>-{{ number_format($summary->sum('payment_agency_fee')) }}원</div>
                    <div>
                        {{ config('app.lang') != 'en' ? __('결제 대행사에서 차감한 결제대행수수료 금액입니다.') : __('This is the payment agency fee deducted by the payment agency.') }}
                    </div>
                </div>
                <div class="summary-row">
                    <div>{{ config('app.lang') != 'en' ? __('매출금액') : __('Sales Amount') }}</div>
                    <div>{{ number_format($summary->sum('sales_amount')) }}원</div>
                    <div>
                        {{ config('app.lang') != 'en' ? __('수수료를 제외한 최종 매출금액입니다.') : __('This is the final sales amount excluding fees.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--  -->

<!-- payments reports -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-primary d-flex justify-content-between"
                style="--header-background-color: #1E3142;">
                <div>
                </div>
                <div>
                    <button class="btn btn-primary btn-sm" type="button"
                        onclick="exportExcel(`{{ url('sales-management/export') }}`)">
                        {{ config('app.lang') != 'en' ? __('엑셀 다운로드') : __('Download Excel') }}
                    </button>
                </div>
            </div>

            <div class="card-body">
                <div class="styled-table-container">
                    <table class="styled-table">
                        <thead>
                            <tr>
                                <th>{{ config('app.lang') != 'en' ? __('번호') : __('Number') }}</th>
                                <th>{{ config('app.lang') != 'en' ? __('일자') : __('Date') }}</th>
                                <th>{{ config('app.lang') != 'en' ? __('총 결제금액') : __('Total Payment Amount') }}</th>
                                <th>{{ config('app.lang') != 'en' ? __('결제건수') : __('Number of Payments') }}</th>
                                <th>{{ config('app.lang') != 'en' ? __('신용카드') : __('Credit Card') }}</th>
                                <th>{{ config('app.lang') != 'en' ? __('무통장입금') : __('Bank Transfer') }}</th>
                                <th>{{ config('app.lang') != 'en' ? __('가상계좌') : __('Virtual Account') }}</th>
                                <th>{{ config('app.lang') != 'en' ? __('실시간 계좌이체') : __('Real-Time Account Transfer') }}
                                </th>
                                <th>{{ config('app.lang') != 'en' ? __('결제대행 수수료') : __('Payment Agency Fee') }}</th>
                                <th>{{ config('app.lang') != 'en' ? __('상세결제내역') : __('Detailed Payment History') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($summary as $report)
                            <tr>
                                <td>{{ $loop->iteration + ($summary->currentPage() - 1) * $summary->perPage() }}</td>
                                <td>{{ $report->date }}</td>
                                <td>{{ number_format($report->total_payment_amount) }}원</td>
                                <td>{{ $report->total_number_of_payments }}</td>
                                <td>{{ number_format($report->credit_card) }}원</td>
                                <td>{{ number_format($report->bank_transfer) }}원</td>
                                <td>{{ number_format($report->virtual_account) }}원</td>
                                <td>{{ number_format($report->real_time_account_transfer) }}원</td>
                                <td>{{ number_format($report->payment_agency_fee) }}원</td>
                                <td><button class="btn btn-primary btn-sm"
                                        onclick="loadDetails('{{ $report->date }}')">{{ config('app.lang') != 'en' ? __('매출내역상세') : __('Details') }}</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center">
                    {{ $summary->links('layouts.pagination') }}
                </div>
            </div>
        </div>
    </div>
</div>
<!--  -->

<!-- detailed sales reports -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-primary d-flex justify-content-between"
                style="--header-background-color: #1E3142;">
                <h4 class="title" id="detailsTitle">{{ config('app.lang') != 'en' ? __('매출내역') : __('Details') }}</h4>
            </div>

            <div class="card-body">
                <div class="styled-table-container">
                    <table class="styled-table" id="detailsTable">
                        <thead>
                            <tr>
                                <th>{{ config('app.lang') != 'en' ? __('번호') : __('Number') }}</th>
                                <th>{{ config('app.lang') != 'en' ? __('아이디') : __('User ID') }}</th>
                                <th>{{ config('app.lang') != 'en' ? __('업체(기관)명') : __('Organization Name') }}</th>
                                <th>{{ config('app.lang') != 'en' ? __('주소') : __('Address') }}</th>
                                <th>{{ config('app.lang') != 'en' ? __('담당자명') : __('Name Of Person In Charge') }}</th>
                                <th>{{ config('app.lang') != 'en' ? __('결제번호') : __('Transaction ID') }}</th>
                                <th>{{ config('app.lang') != 'en' ? __('이용서비스') : __('Service Used') }}</th>
                                <th>{{ config('app.lang') != 'en' ? __('결제금액') : __('Total Amount') }}</th>
                                <th>{{ config('app.lang') != 'en' ? __('결제수단') : __('Payment Method') }}</th>
                                <th>{{ config('app.lang') != 'en' ? __('결제일자') : __('Payment Date') }}</th>
                                <th>{{ config('app.lang') != 'en' ? __('관리') : __('Manage') }}</th>
                            </tr>
                        </thead>
                        <tbody id="detailsBody">
                            <!-- AJAX-loaded content will go here -->
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center" id="paginationLinks">
                    <!-- AJAX-loaded pagination links will go here -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function loadDetails(date, page = 1, limit = 10) {
    document.getElementById('detailsTitle').innerText =
        `${date} {{ config('app.lang') != 'en' ? __('매출내역') : __('Details') }}`;
    fetch(`/sales-management/details/${date}?page=${page}&limit=${limit}`)
        .then(response => response.json())
        .then(data => {
            const detailsBody = document.getElementById('detailsBody');
            detailsBody.innerHTML = '';

            data.data.forEach((report, index) => {
                const row = document.createElement('tr');

                row.innerHTML = `
                    <td>${(data.current_page - 1) * data.per_page + index + 1}</td>
                    <td>${report.user_id}</td>
                    <td>${report.organization_name}</td>
                    <td>${report.address}</td>
                    <td>${report.person_in_charge}</td>
                    <td>${report.transaction_id}</td>
                    <td>${report.service_used}</td>
                    <td>${report.total_amount}</td>
                    <td>${report.payment_method}</td>
                    <td>${report.payment_date}</td>
                    <td><button class="btn btn-primary btn-sm">{{ config('app.lang') != 'en' ? __('매출전표') : __('Sales slip') }}</button></td>
                `;

                detailsBody.appendChild(row);
            });

            createPaginationWithArg(date, data, limit, 'loadDetails',
                'paginationLinks');

            // Scroll to the bottom of the page
            window.scrollTo({
                top: document.body.scrollHeight,
                behavior: 'smooth'
            });
        });


}

document.addEventListener("click", function(event) {
    if (event.target.matches(".pagination a")) {
        event.preventDefault();
        const url = new URL(event.target.href);
        const page = url.searchParams.get("page");
        const date = document.getElementById('detailsTitle').innerText.split(' ')[0];
        const limit = document.querySelector('.pagination-limit .btn').innerText.match(/\d+/)[0];
        loadDetails(date, page, limit);
    }
});
</script>
@endsection