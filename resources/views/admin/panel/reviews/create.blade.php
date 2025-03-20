@extends('Admin.panel.layouts.master')

@section('styles')
    <link type="text/css" href="{{ asset('Admin-assets/css/panel/review/review.css') }}" rel="stylesheet" />
@endsection

@section('site-header')
    {{ 'به نوبه | پنل مدیریت' }}
@endsection

@section('content')
    @section('bread-crumb-title', 'افزودن نظر جدید')
    @livewire('Admin.panel.reviews.review-create')
@endsection