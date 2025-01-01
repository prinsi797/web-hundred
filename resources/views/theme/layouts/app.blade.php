<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <meta name="keywords" content="Hundred" />
  <meta name="description" content="Hundred" />

  <!-- Open Graph Meta-->
  <meta property="og:type" content="website" />
  <meta property="og:SITE_NAME" content="Hundred" />
  <meta property="og:title" content="Hundred" />
  <meta property="og:url" content="http://github.com/akramchauhan" />
  <meta property="og:description" content="Quality first code" />
  {{-- <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/favicon.png') }}"> --}}

  <!-- Twitter meta-->
  <meta property="twitter:card" content="summary_large_image" />
  <meta property="twitter:site" content="@digitalchauhan" />
  <meta property="twitter:creator" content="@digitalchauhan" />

  @if(isset($title))
  <title>{{ $title }}</title>
  @else
  <title>{{ config('app.name', 'Laravel') }}</title>
  @endif

  <link rel="stylesheet" type="text/css" href="{{ asset('theme/css/main.css') }}" />
  <link rel="stylesheet" type="text/css" href="{{ asset('plugins/font-awesome-4.7.0/font-awesome.min.css') }}" />
  <link rel="stylesheet" type="text/css" href="{{ asset('theme/css/custom.css') }}" />
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/global.css') }}" />
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/primary_colors.css') }}" />
  @stack('styles')
  <style>
    .auth_logo {
      width: 100px;
    }
  </style>
</head>

<body class="app sidebar-mini rtl">
  <!-- Navbar-->
  <header class="app-header">
    <a class="app-header__logo" href="{{ route('admin.dashboard') }}">
      <?php
      $logos = logos();
      $logo_white = $logos['white'];
      ?>
     <h3 class="text-white text-center" style="line-height: 2.2; margin-bottom: 0px">H U N D R E D</h3>
    </a>
    <!-- Sidebar toggle button-->
    @if(Auth::check())
    <a class="app-sidebar__toggle" href="#" data-toggle="sidebar" aria-label="Hide Sidebar"></a>
    @endif
    <!-- Navbar Right Menu-->
    <ul class="app-nav">
      @if(Auth::check())
      <!-- User Menu-->
      <li class="dropdown">
        <a class="app-nav__item" href="#" data-toggle="dropdown" aria-label="Open Profile Menu"><i class="fa fa-user fa-lg"></i></a>
        <ul class="dropdown-menu settings-menu dropdown-menu-right">
          <li>
            <a class="dropdown-item" href="{{ route('admin.settings.edit_profile') }}">
              <i class="fa fa-edit"></i> {{ __('Edit Profile') }}</a>
            <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
              <i class="fa fa-sign-out fa-lg"></i> {{ __('Logout') }}</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
              @csrf
            </form>
          </li>
        </ul>
      </li>
      @endif
    </ul>
  </header>
  <!-- Sidebar menu-->
  @if(Auth::check())
  <div class="app-sidebar__overlay" data-toggle="sidebar"></div>
  <?php

  if (auth()->user()->isAdmin) {
    $menu_array_blade = resource_path('views/configuration/menu_array.blade.php');
  } elseif(auth()->user()->isUser){
    $menu_array_blade = resource_path('views/configuration/menu_array.blade.php');
  } else {
    $menu_array_blade = resource_path('views/configuration/menu_array_users.blade.php');
  }
  include($menu_array_blade);
  ?>
  @include('theme.layouts.partial.menu_items')
  @endif
  <main class="app-content">
    @yield('content')
  </main>
  <!-- Essential javascripts for application to work-->
  <script src="{{ asset('theme/plugins/jquery-3.2.1.min.js') }}"></script>
  <script src="{{ asset('theme/plugins/popper.min.js') }}"></script>
  <script src="{{ asset('theme/plugins/bootstrap.min.js') }}"></script>
  <script src="{{ asset('theme/plugins/pace.min.js') }}"></script>
  <script src="{{ asset('theme/js/main.js') }}"></script>
  <script src="{{ asset('theme/js/custom_fns.js') }}"></script>
  <script type="text/javascript">

  </script>
  @stack('scripts')
</body>

</html>