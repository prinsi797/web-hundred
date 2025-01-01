@extends('theme.layouts.app')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css"
    href="https://cdn.jsdelivr.net/npm/intl-tel-input@21.0.8/build/css/intlTelInput.css">
@push('styles')
    <style>
        .toggle-password {
            cursor: pointer;
        }

        .generate-password-btn {
            margin-top: -10px;
        }

        .select2-container .select2-selection--single {
            height: 40px;
        }

        .select2-container--default .select2-selection--single {
            border: 2px solid #ced4da;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 40px;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__display {
            color: #333;
        }

        /* phone number css */
        .iti {
            display: block !important;
        }
    </style>
@endpush
@section('content')
    <div class="container-fluid px-5">
        <div class="row justify-content-center">
            <div class="col-md-12">

                @if ($errors->any())
                    {{-- @if (!empty($errors->get('password')) && count($errors->get('password')) > 0)
                        <div class="alert alert-danger">
                            <ul>
                                <li class="">
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
                        </div>
                    @else --}}
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    {{-- @endif --}}
                @endif
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
            </div>

            <div class="col-md-6 form_page">
                <form action="{{ $form_action }}" class="" method="post">
                    @csrf
                    @if ($edit)
                        <input type="hidden" value="{{ $data->id }}" name="id">
                    @endif

                    <div class="card">
                        <div class="card-body">
                            <div class="row form_sec">
                                <div class="col-12">
                                    <h5>Basic Details</h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="first_name">First Name</label>

                                                <input id="first_name" type="text" placeholder="Enter First Name"
                                                    class="form-control" name="first_name" required
                                                    @if ($edit) value="{{ $data->first_name }}" @else value="{{ old('first_name') }}" @endif
                                                    autocomplete="first_name" autofocus>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="last_name">Last Name</label>
                                                <input id="last_name" type="text" placeholder="Enter Last Name"
                                                    class="form-control" name="last_name" required
                                                    @if ($edit) value="{{ $data->last_name }}" @else value="{{ old('last_name') }}" @endif
                                                    autocomplete="last_name" autofocus>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="tagline">Email</label>
                                                <input id="email" type="text" placeholder="Enter Email"
                                                    class="form-control" name="email" required
                                                    @if ($edit) value="{{ $data->email }}" @else value="{{ old('email') }}" @endif
                                                    autocomplete="email" autofocus>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="company">Company Name</label>
                                                <input id="company" type="text" placeholder="Enter Company Name"
                                                    class="form-control" name="company" required
                                                    @if ($edit) value="{{ $data->company }}" @else value="{{ old('company') }}" @endif
                                                    autocomplete="company" autofocus>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="phone_number">Phone</label>
                                                <input id="phone_number" type="text" placeholder="Enter Phone Number"
                                                    class="form-control" name="phone_number" required
                                                    @if ($edit) value="{{ $data->phone_number }}" @else value="{{ old('phone_number') }}" @endif
                                                    autocomplete="phone_number" autofocus>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br />
                    <div class="card">
                        <div class="card-body">
                            <div class="row form_sec">
                                @if ($edit)
                                    <div class="col-12">
                                        <h5>Change Password</h5>
                                    </div>
                                @else
                                    <div class="col-12">
                                        <h5>Set Password</h5>
                                    </div>
                                @endif
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    @if ($edit && !auth()->user()->isAdmin)
                                        <div class="form-group">
                                            <label for="old_password">Old Password</label>
                                            <input type="password" name="old_password" autocomplete="new-password"
                                                class="form-control" id="old_password"
                                                aria-describedby="old_passwordHelp">
                                            <small id="old_passwordHelp" class="form-text text-muted"></small>
                                        </div>
                                    @endif
                                    <div class="form-group">
                                        <label for="new_password">Password</label>
                                        <div class="float-right btn btn-sm btn-warning generate-password-btn"> <i
                                                class="fa fa-unlock-alt"></i> Generate Strong Password</div>
                                        <div class="input-group" id="show_hide_password">
                                            <input type="password" name="password" autocomplete="new-password"
                                                class="form-control new_password" id="new_password"
                                                placeholder="Enter Password" aria-describedby="new_passwordHelp">
                                            <div class="input-group-append toggle-password">
                                                <span class="input-group-text" id="basic-addon2"><i
                                                        class="fa fa-eye-slash" aria-hidden="true"></i></span>
                                            </div>
                                        </div>
                                        <small id="new_passwordHelp" class="form-text text-muted"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="new_password_confirmation">Confirm Password</label>
                                        <div class="input-group" id="show_hide_confirm_password">
                                            <input type="password" name="password_confirmation"
                                                placeholder="Enter Confirm Password" class="form-control confirm_password"
                                                id="new_password_confirmation"
                                                aria-describedby="new_password_confirmationHelp">
                                            <div class="input-group-append toggle-password">
                                                <span class="input-group-text" id="basic-addon2"><i
                                                        class="fa fa-eye-slash" aria-hidden="true"></i></span>
                                            </div>
                                        </div>
                                        <small id="new_password_confirmationHelp" class="form-text text-muted"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br />
                    @if (!auth()->user()->isAdmin)
                        <div class="card">
                            <div class="card-body">
                                <div class="row form_sec">
                                    <div class="col-12">
                                        <h5>Allow SavePas Administrators to view my balance sheets</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="switch">
                                            <?php
                                            $checked = '';
                                            if ($edit && $data->is_report_shared == 1) {
                                                $checked = 'checked';
                                            }
                                            ?>
                                            <input type="checkbox" name="is_report_shared" {{ $checked }}
                                                class="is_report_shared" id="is_report_shared">
                                            <span class="slider"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <!-- <div class="card">
                          <div class="card-body">
                            <div class="row form_sec">
                              <div class="col-12">
                                <h5>Two Factor Authentication (Email)</h5>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-md-6">
                                <label class="switch">
                                  <input type="checkbox" name="two_factor_enable" <?php if ($edit) {
                                      if ($data->two_factor_enable == 1) {
                                          echo 'checked';
                                      }
                                  } ?> class="two_factor_enable" id="two_factor_enable">
                                  <span class="slider"></span>
                                </label>
                              </div>
                            </div>
                          </div>
                        </div> -->
                    <br />
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary add_site">
                                @if ($edit)
                                    Update Changes
                                @else
                                    Add User
                                @endif
                            </button>
                            @if ($edit)
                                <a href="{{ $cancel }}" class="btn btn-secondary">Cancel</a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-6">
                @if ($subscription_info)
                    <div class="row">
                        <div class="col">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Subscriptions</h4>
                                </div>
                                <div class="card-body">

                                    <div class="alert alert-success">
                                        <div class="row">
                                            <div class="col">
                                                <h4>{{ $subscription_info['name'] }}</h4>
                                                <table class="table-sm">
                                                    <tr>
                                                        <td>Start Date </td>
                                                        <td>{{ prettyDate($subscription_info['current_period_start']) }}
                                                        </td>
                                                    </tr>
                                                    @if ($subscription_info['is_cancel_period'])
                                                        <tr>
                                                            <td>End Date </td>
                                                            <td>{{ prettyDate($subscription_info['cancel_at']) }}</td>
                                                        </tr>
                                                    @else
                                                        <tr>
                                                            <td>Auto Renew On </td>
                                                            <td>{{ prettyDate($subscription_info['current_period_end']) }}
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </table>
                                            </div>
                                            <div class="col text-right">
                                                <h2>${{ $subscription_info['price'] }}</h2>
                                                <br />
                                                @if (!$subscription_info['is_cancel_period'])
                                                    <a href="#" data-type="cancel"
                                                        class="btn cancel-subscription-btn btn-outline-danger">Cancel
                                                        subscription</a>
                                                    <form class="cancel-subscription-form" method="post"
                                                        action="{{ route('user.subscription.cancel') }}">
                                                        @csrf
                                                        <input type="hidden" name="user_id"
                                                            value="{{ $data['id'] }}">
                                                        <input type="hidden" name="request_type" value="cancel">
                                                        <input type="hidden" name="stripe_id"
                                                            value="{{ $subscription_info['stripe_id'] }}">
                                                    </form>
                                                @else
                                                    <a href="#" data-type="resume"
                                                        class="btn cancel-subscription-btn btn-warning">Auto renew</a>
                                                    <form class="cancel-subscription-form" method="post"
                                                        action="{{ route('user.subscription.cancel') }}">
                                                        @csrf
                                                        <input type="hidden" name="user_id"
                                                            value="{{ $data['id'] }}">
                                                        <input type="hidden" name="request_type" value="resume">
                                                        <input type="hidden" name="stripe_id"
                                                            value="{{ $subscription_info['stripe_id'] }}">
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br />
                @endif
                <div class="row">
                    <div class="col-6"></div>
                    @if ($payment_methods)
                        <div class="col-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Payment Methods</h4>
                                </div>
                                <div class="card-body">
                                    @if (count($payment_methods) > 0)
                                        @foreach ($payment_methods as $payment_method)
                                            <div class="alert alert-info">
                                                <div class="row">
                                                    <div class="col-2 text-left">
                                                        <h1 style="font-style:italic">
                                                            <i class="fa fa-cc-{{ $payment_method->card->brand }}"></i>
                                                        </h1>
                                                    </div>
                                                    <div class="col text-right">
                                                        <h4>**** **** **** {{ $payment_method->card->last4 }}</h4>
                                                        <p>
                                                            Expiry:
                                                            {{ $payment_method->card->exp_month }}/{{ $payment_method->card->exp_year }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="alert alert-warning">
                                            No Payment methods added yet.
                                        </div>
                                        <br />
                                        <div class="row">
                                            <div class="col-12 text-center">
                                                <a href="{{ route('user.upgrade') }}" class="btn btn-primary">Upgrade
                                                    your subscription.</a>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script type="text/javascript" src="{{ asset('plugins/phone-intlTel/intlTelInput.min.js') }}"></script>
    <script>
        const input = document.querySelector("#phone_number");
        window.intlTelInput(input, {
            utilsScript: "{{ asset('plugins/phone-intlTel/utils.js') }}",
            initialCountry: "us"
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.change_role').select2({
                placeholder: "Select Role",
                allowClear: true,
                minimumResultsForSearch: Infinity
            });
        });

        $(document).ready(function() {
            $('.change_state').select2({
                placeholder: "Select State",
                allowClear: true
            })
        })
        $(document).ready(function(e) {
            $('.cancel-subscription-btn').click(function(e) {
                var req_type = $(this).attr('data-type');
                if (req_type == "resume") {
                    $('.cancel-subscription-form').submit();
                } else if (req_type == "cancel") {
                    swal({
                        title: "Cancel Subscription",
                        text: "Are you sure that you want to cancel your subscription?",
                        icon: "warning",
                        buttons: true,
                        buttons: ["No", "Cancel"],
                        cancelButtonText: 'No',
                        confirmButtonText: 'Yes',
                        dangerMode: true,
                    }).then((cancelPurchase) => {
                        if (cancelPurchase) {
                            $('.cancel-subscription-form').submit();
                        }
                    });
                }
            });

            $(".change_role").change(function(e) {
                var role = this.value;
                if (role == 'Admin') {
                    $(".address-card").hide();
                } else if (role == 'User') {
                    $(".address-card").show();
                }
            });

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

            $("#show_hide_confirm_password").on('click', function(event) {
                event.preventDefault();
                if ($('#show_hide_confirm_password input').attr("type") == "text") {
                    $('#show_hide_confirm_password input').attr('type', 'password');
                    $('#show_hide_confirm_password i').addClass("fa-eye-slash");
                    $('#show_hide_confirm_password i').removeClass("fa-eye");
                } else if ($('#show_hide_confirm_password input').attr("type") == "password") {
                    $('#show_hide_confirm_password input').attr('type', 'text');
                    $('#show_hide_confirm_password i').removeClass("fa-eye-slash");
                    $('#show_hide_confirm_password i').addClass("fa-eye");
                }
            });

            $(".generate-password-btn").on('click', function(e) {
                $('.new_password').attr('type', 'text');
                // $('.confirm_password').attr('type', 'text');
                $('#show_hide_password i').removeClass("fa-eye-slash");
                $('#show_hide_password i').addClass("fa-eye");

                //generating password;
                var password = generateStrongPassword();
                $(".new_password").val(password);
                $(".confirm_password").val(password);

            });

            var is_edit = '{{ $edit }}';
            var is_admin = $(".change_role option:selected").val();
            if (is_edit == 1 && is_admin == 'Admin') {
                $(".address-card").hide();
            } else {
                $(".address-card").show();
            }
        });
    </script>
@endpush
