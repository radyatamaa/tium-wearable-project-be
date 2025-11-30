@php
$pageTitle = config('app.lang') != 'en' ? __('병원 현황') : __('Overview');
@endphp

@extends('layouts.app', ['page' => $pageTitle, 'pageSlug' => 'overview'])

@section('content-fluid')
<h1>on develop Overview</h1>
@endsection