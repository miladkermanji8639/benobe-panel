@extends('admin.panel.layouts.master')

@section('styles')
  <link type="text/css" href="{{ asset('Admin-assets/css/panel/usergroup/usergroup.css') }}" rel="stylesheet" />
@endsection

@section('site-header')
  {{ 'به نوبه | پنل مدیریت' }}
@endsection

@section('content')
@section('bread-crumb-title', 'مدیریت گروه  کاربری')
@livewire('admin.panel.usergroups.usergroup-list')
@endsection
