@extends('admin.panel.layouts.master')

@section('styles')
  <link type="text/css" href="{{ asset('admin-assets/panel/css/users/users.css') }}" rel="stylesheet" />
@endsection

@section('site-header')
  {{ 'به نوبه | پنل مدیریت' }}
@endsection

@section('content')
@section('bread-crumb-title', 'ویرایش پزشک')
@livewire('admin.panel.doctors.doctor-edit', ['id' => $id])
@endsection
