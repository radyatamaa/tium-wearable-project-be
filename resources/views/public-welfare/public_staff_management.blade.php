@php
$pageTitle = config('app.lang') != 'en' ? __('직원관리') : __('PublicStaffManagement');
@endphp

@extends('layouts.app', ['page' => $pageTitle, 'pageSlug' => 'public-staff-measurement'])

@section('content-fluid')
<h1>on develop PublicStaffManagement</h1>
@endsection