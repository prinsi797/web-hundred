<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="description" content="" />
    <!-- Twitter meta-->
    <meta property="twitter:card" content="summary_large_image" />
    <meta property="twitter:site" content="@pratikborsadiya" />
    <meta property="twitter:creator" content="@pratikborsadiya" />
    <!-- Open Graph Meta-->
    <meta property="og:type" content="website" />
    <meta property="og:SITE_NAME" content="Foresite" />
    <meta property="og:title" content="Foresite panel" />
    <meta property="og:url" content="http:/localhost:8000" />
    <meta property="og:description" content="" />
    <title>{{ config('app.name', 'Laravel') }}</title>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <!-- Main CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('theme/css/main.css') }}" />
    {{-- <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/favicon.png') }}"> --}}
    <!-- Font-icon css-->
    <link rel="stylesheet" type="text/css" href="{{ asset('plugins/font-awesome-4.7.0/font-awesome.min.css') }}" />
    @stack('styles')
    <style>
        .toggle-password {
            cursor: pointer;
        }

        .generate-password-btn {
            margin-top: -5px;
        }

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
        <div class="logo">
            <?php
            $logos = logos();
            $logo_white = $logos['white'];
            ?>
            <h1>{{ __('H U N D R E D') }}</h1>
        </div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">{{ __('Reset Password') }}</div>

                        <div class="card-body">
                            @error('password')
                            <ul class="list-group">
                                <li class="list-group-item list-group-item-danger">
                                    Password must include:
                                    <ul>
                                        <li>8 characters minimum</li>
                                        <li>One upper case letter</li>
                                        <li>One lower case letter</li>
                                        <li>One number</li>
                                        <li>One special character</li>
                                    </ul>
                                </li>
                                <li class="list-group-item list-group-item-danger">The password confirmation does not match.</li>
                            </ul>
                            <br />
                            @enderror
                            <form method="POST" action="{{ route('password.update') }}">
                                @csrf

                                <input type="hidden" name="token" value="{{ $token }}">

                                <div class="form-group row">
                                    <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                                    <div class="col-md-6">
                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

                                        @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col">
                                        <div class="float-right btn btn-sm btn-warning generate-password-btn"> <i class="fa fa-unlock-alt"></i> Generate Strong Password</div>
                                    </div>
                                    <div class="col-2"></div>
                                </div>
                                <div class="form-group row">
                                    <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                                    <div class="col-md-6">
                                        <div class="input-group" id="show_hide_password">
                                            <input id="password" type="password" class="password form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                                            <div class="input-group-append toggle-password">
                                                <span class="input-group-text" id="basic-addon2"><i class="fa fa-eye-slash" aria-hidden="true"></i></span>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                                    <div class="col-md-6">
                                        <input id="password-confirm" type="password" class="password_confirm form-control" name="password_confirmation" required autocomplete="new-password">
                                    </div>
                                </div>

                                <div class="form-group row mb-0">
                                    <div class="col-md-6 offset-md-4">
                                        <button type="submit" class="btn btn-primary">
                                            {{ __('Reset Password') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
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
    <script src="{{ asset('theme/plugins/plugins/pace.min.js') }}"></script>
    <script src="{{ asset('theme/js/custom_fns.js') }}"></script>

    @stack('scripts')
    <script type="text/javascript">
        $(document).ready(function(e) {
            $("#show_hide_password .toggle-password").on('click', function(event) {
                event.preventDefault();
                if ($('#show_hide_password input').attr("type") == "text") {
                    $('#show_hide_password input').attr('type', 'password');
                    $('#show_hide_password i').addClass("fa-eye-slash");
                    $('#show_hide_password i').removeClass("fa-eye");
                } else if ($('#show_hide_password input').attr("type") == "password") {
                    $('#show_hide_password input').attr('type', 'text');
                    $('#show_hide_password i').removeClass("fa-eye-slash");
                    $('#show_hide_password i').addClass("fa-eye");
                }
            });

            $(".generate-password-btn").on('click', function(e) {
                $('.password').attr('type', 'text');
                // $('.confirm_password').attr('type', 'text');
                $('#show_hide_password i').removeClass("fa-eye-slash");
                $('#show_hide_password i').addClass("fa-eye");

                //generating password;
                var password = generateStrongPassword();
                $(".password").val(password);
                $(".password_confirm").val(password);

            });
        });
    </script>
</body>

</html>