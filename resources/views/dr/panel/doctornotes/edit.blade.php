@extends('dr.panel.layouts.master')

@section('styles')
  <link type="text/css" href="{{ asset('dr-assets/css/panel/doctornote/doctornote.css') }}" rel="stylesheet" />
  <style>
    .myPanelOption {
      display: none
    }
  </style>
@endsection

@section('site-header')
  {{ 'به نوبه | پنل مدیریت' }}
@endsection

@section('content')
@section('bread-crumb-title', 'ویرایش توضیحات')
@livewire('dr.panel.doctornotes.doctornote-edit', ['id' => $id])
@section('scripts')


  <script src="{{ asset('dr-assets/panel/js/dr-panel.js') }}"></script>


@endsection
@endsection
