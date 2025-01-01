<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="description" content="" />
    <!-- Twitter meta-->
    <meta property="twitter:card" content="summary_large_image" />
    <meta property="twitter:site" content="@digitalchauhan" />
    <meta property="twitter:creator" content="@digitalchauhan" />
    <!-- Open Graph Meta-->
    <meta property="og:type" content="website" />
    <meta property="og:SITE_NAME" content="HUNDRED LOGIN" />
    <meta property="og:title" content="Custom bootstrap panel designed by Akram Chauhan" />
    <meta property="og:url" content="https://akramchauhan.com" />
    <meta property="og:description"
        content="This is a responsive admin panel built with Bootstrap 4 by Akram Chauhan" />
    <title>{{ config('app.name', 'Laravel') }}</title>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <!-- Main CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('theme/css/main.css') }}" />
    {{-- <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/favicon.png') }}"> --}}
    <!-- Font-icon css-->
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/font-awesome-4.7.0/font-awesome.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/primary_colors.css') }}" />
    @stack('styles')
    <style>
        .auth_logo {
            width: 300px;
        }
    </style>
</head>

<body class="app sidebar-mini rtl">
    <section class="material-half-bg">
        <div class="cover"></div>
    </section>
    <section class="login-content">

        <div class="text-center">
            <h1 class="text-white mb-5">H U N D R E D</h1>
            @if (session()->has('message'))
                <p class="alert alert-info">{{ session()->get('message') }}</p>
            @endif
        </div>

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    @if (session()->has('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('status'))
                        <div class="alert alert-warning" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-warning" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="my-3">
                            <ul class="list-group list-group-flush">
                                @foreach ($errors->all() as $error)
                                    <li class="list-group-item list-group-item-danger">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="card">
                        <div class="card-header">
                            {{ __('Login') }}
                        </div>
                        <div class="card-body">
                            <form class="login-form" method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="form-group">
                                    <label class="control-label">Email</label>
                                    <input id="email" class="form-control @error('email') is-invalid @enderror"
                                        type="email" name="email" placeholder="Email" value="{{ old('email') }}"
                                        required autocomplete="email" autofocus>
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Password</label>
                                    <input id="password" class="form-control @error('password') is-invalid @enderror"
                                        type="password" name="password" required autocomplete="current-password"
                                        placeholder="Password">
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="g-recaptcha" id="feedback-recaptcha"
                                    data-sitekey="{{ env('GOOGLE_RECAPTCHA_KEY') }}"></div>
                                <div class="form-group"><br /></div>
                                <div class="form-group">
                                    <div class="utility">
                                        <div class="animated-checkbox">
                                            <label for="remember">
                                                <input type="checkbox" name="remember" id="remember"
                                                    {{ old('remember') ? 'checked' : '' }}><span
                                                    class="label-text">{{ __('Remember Me') }}</span>
                                            </label>
                                        </div>
                                        @if (Route::has('password.request'))
                                            <p class="semibold-text mb-2"><a href="{{ route('password.request') }}"
                                                    data-toggle="flip">{{ __('Forgot Your Password?') }}</a></p>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group btn-container">
                                    <button class="btn btn-primary btn-block" type="submit"><i
                                            class="fa fa-sign-in fa-lg fa-fw"></i>{{ __('Login') }}</button>
                                </div>
                            </form>
                            <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Essential javascripts for application to work-->
    <script src="{{ asset('theme/plugins/jquery-3.2.1.min.js') }}"></script>
    <script src="{{ asset('theme/plugins/popper.min.js') }}"></script>
    <script src="{{ asset('theme/plugins/bootstrap.min.js') }}"></script>
    <script src="{{ asset('theme/js/main.js') }}"></script>
    <!-- The javascript plugin to display page loading on top-->
    <script src="{{ asset('theme/plugins/pace.min.js') }}"></script>
    @stack('scripts')
</body>

</html>
