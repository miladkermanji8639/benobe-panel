@extends('admin.panel.layouts.master')

@section('styles')
    <link type="text/css" href="{{ asset('admin-assets/css/panel/manualappointment/manualappointment.css') }}" rel="stylesheet" />
@endsection

@section('site-header')
    {{ 'به نوبه | پنل مدیریت' }}
@endsection

@section('content')
    @section('bread-crumb-title', 'ویرایش نوبت دستی')
    @livewire('admin.panel.manual-appointments.manual-appointment-edit', ['id' => $id])
@endsection