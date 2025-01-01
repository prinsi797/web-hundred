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
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/primary_colors.css') }}" />
  @stack('styles')
  <style>
    .toggle-password {
      cursor: pointer;
    }

    .generate-password-btn {
      margin-top: -5px;
    }

    .single-page-link {
      color: blue;
      text-decoration: underline;
    }

    .terms_label {
      cursor: pointer;
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
  <section class="login-content my-5">
    {{-- <div class="logo">
      <?php
      $logos = logos();
      $logo_white = $logos['white'];
      ?>
      <h1>{{ __('H U N D R E D') }}</h1>
    </div> --}}
    <div class="text-center">
      <h1 class="text-white mb-5">H U N D R E D</h1>
  </div>
    <div class="container">
      <div class="row justify-content-center">
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
          @if(!empty($errors->get('password')) && count($errors->get('password')) > 0)
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
          </ul>
          @else
          <ul class="list-group list-group-flush">
            @foreach ($errors->all() as $error)
            <!-- {{ dd($error) }} -->
            <li class="list-group-item list-group-item-danger">{{ $error }}</li>
            @endforeach
          </ul>
          @endif
        </div>

        @endif
        <div class="col-md-10 mb-5">
          <div class="card">
            <div class="card-header">
              <i class="fa fa-lg fa-fw fa-user"></i>{{ __('Create an Account') }}
            </div>

            <div class="card-body">
              <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="row">
                  <div class="col-md-6">
                    <div class="row">
                      <div class="col">
                        <div class="form-group">
                          <label for="first_name" class="control-label">{{ __('First Name') }}</label>
                          <input id="first_name" type="text" class="form-control @error('first_name') is-invalid @enderror" name="first_name" value="{{ old('first_name') }}" required autocomplete="first_name" autofocus>

                          @error('first_name')
                          <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                          </span>
                          @enderror
                        </div>
                      </div>
                      <div class="col">
                        <div class="form-group">
                          <label for="last_name" class="control-label">{{ __('Last Name') }}</label>
                          <input id="last_name" type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name" value="{{ old('last_name') }}" required autocomplete="last_name" autofocus>

                          @error('last_name')
                          <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                          </span>
                          @enderror
                        </div>
                      </div>
                    </div>


                    <div class="form-group">
                      <label for="email" class="control-label">{{ __('E-Mail Address') }}</label>
                      <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                      @error('email')
                      <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                      </span>
                      @enderror
                    </div>

                    <div class="form-group">
                      <label for="password" class="control-label">{{ __('Password') }}</label>
                      <div class="float-right btn btn-sm btn-warning generate-password-btn"> <i class="fa fa-unlock-alt"></i> Generate Strong Password</div>
                      <div class="input-group" id="show_hide_password">
                        <input id="password" type="password" class="password form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                        <div class="input-group-append toggle-password">
                          <span class="input-group-text" id="basic-addon2"><i class="fa fa-eye-slash" aria-hidden="true"></i></span>
                        </div>
                      </div>

                      @error('password')
                      <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                      </span>
                      @enderror
                    </div>

                    <div class="form-group">
                      <label for="password-confirm" class="control-label">{{ __('Confirm Password') }}</label>
                      <input id="password-confirm" type="password" class="confirm_password form-control" name="password_confirmation" required autocomplete="new-password">
                    </div>
                    <div class="form-group">
                      <label for="company" class="control-label">{{ __('Company Name') }}</label>
                      <input id="company" type="text" class="form-control @error('company') is-invalid @enderror" name="company" value="{{ old('company') }}" required autocomplete="company" autofocus>

                      @error('company')
                      <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                      </span>
                      @enderror
                    </div>
                  </div>
                  <div class="col-md-6">

                    <div class="form-group">
                      <label for="street" class="control-label">{{ __('Street Address') }}</label>
                      <input id="street" type="text" class="form-control @error('street') is-invalid @enderror" name="street" value="{{ old('street') }}" required autocomplete="street" autofocus>

                      @error('street')
                      <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                      </span>
                      @enderror
                    </div>
                    <div class="form-group">
                      <label for="street2" class="control-label">{{ __('Street Address 2') }}</label>
                      <input id="street2" type="text" class="form-control @error('street2') is-invalid @enderror" name="street2" value="{{ old('street2') }}" autocomplete="street2" autofocus>

                      @error('street2')
                      <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                      </span>
                      @enderror
                    </div>
                    <div class="row">
                      <div class="col">
                        <div class="form-group">
                          <label for="city" class="control-label">{{ __('City') }}</label>
                          <input id="city" type="text" class="form-control @error('city') is-invalid @enderror" name="city" value="{{ old('city') }}" required autocomplete="city" autofocus>

                          @error('city')
                          <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                          </span>
                          @enderror
                        </div>
                      </div>
                      <div class="col">
                        <div class="form-group">
                          <label for="zipcode" class="control-label">{{ __('Postal code') }}</label>
                          <input id="zipcode" type="text" class="form-control @error('zipcode') is-invalid @enderror" name="zipcode" value="{{ old('zipcode') }}" required autocomplete="zipcode" autofocus>

                          @error('zipcode')
                          <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                          </span>
                          @enderror
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="state" class="control-label">{{ __('State') }}</label>
                      <?php
                      $states = getStates();
                      $old_state = "";
                      if ((old('state'))) {
                        $old_state = old('state');
                      }
                      ?>

                      <select class="form-control" name="state" id="state" required>
                        <option value="">Select State</option>
                        @foreach($states as $state_code => $state_name)
                        <?php
                        $selected = "";
                        if (old('state') && old('state') == $state_code) {
                          $selected = "selected";
                        }
                        ?>
                        <option value="{{ $state_code}}" {{ $selected }}> {{ $state_name }}</option>
                        @endforeach
                      </select>

                      @error('state')
                      <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                      </span>
                      @enderror
                    </div>
                    <div class="form-group">
                      <div class="utility">
                        <div class="row">
                          <div class="col-md-12">
                            <label>Please select company type:</label>
                          </div>
                          <div class="col-md-3">
                            <label for="is_non_profit_no">
                              <input type="radio" name="is_non_profit" required value="0" id="is_non_profit_no" {{ old('is_non_profit') == "0" ? 'checked' : '' }}><span class="label-text">{{ __(' For-Profit') }}</span>
                            </label>
                          </div>
                          <div class="col-md-3">
                            <label for="is_non_profit_yes" title="Not Allowed at this point">
                              <input type="radio" name="is_non_profit" value="1" id="is_non_profit_yes" {{ old('is_non_profit') == "1" ? 'checked' : '' }}><span class="label-text">{{ __(' Nonprofit') }}</span>
                            </label>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-12">
                    <input type="checkbox" name="terms" id="terms" required />
                    <label for="terms" class="terms_label">I agree to the
                      <a href="{{ route('show.page', ['slug' => 'terms-and-conditions' ]) }}" class="single-page-link" target="_blank" title="Click here to read Terms and Conditions">Terms and Conditions</a>
                    </label>
                  </div>

                  <div class="col-12">
                    <div class="g-recaptcha" id="feedback-recaptcha" data-sitekey="{{ env('GOOGLE_RECAPTCHA_KEY') }}"> </div>
                  </div>
                  
                </div>
                <hr />
                <div class="form-group mb-0">
                  <div class="row">
                    <div class="col"><a href="{{ route('login') }}">Already Registered? Login</a></div>
                    <div class="col"><button type="submit" class="btn btn-primary float-right">
                        {{ __('Create an Account') }}
                      </button></div>
                  </div>
                </div>
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
        $(".confirm_password").val(password);

      });
    });
  </script>
</body>

</html>