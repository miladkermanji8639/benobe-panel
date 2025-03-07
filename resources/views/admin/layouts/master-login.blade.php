<!DOCTYPE html>
<html lang="fa-IR" dir="rtl" class="scroll-smooth">

<head>
 @include('admin.layouts.partials.head-tag')
 @yield('head-tag')
 @yield('site-header')
</head>

<body>

 <main class="">

  @yield('content')

 </main>

 @include('admin.layouts.partials.scripts')
 @yield('scripts')
@networkStatus
</body>

</html>
