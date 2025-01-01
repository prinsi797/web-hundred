@extends('theme.layouts.app')
<link rel="stylesheet" type="text/css"
    href="https://cdn.jsdelivr.net/npm/intl-tel-input@21.0.8/build/css/intlTelInput.css">
@push('styles')
    <style>
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
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
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

            <div class="col-md-12 form_page">
                <form action="{{ $form_action }}" class="" method="post" enctype="multipart/form-data">
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
                                                <label for="name">Name</label>
                                                <input id="name" type="text" placeholder="Enter name"
                                                    class="form-control" name="name" required
                                                    @if ($edit) value="{{ $data->name }}"@else value="{{ old('name') }}" @endif
                                                    autocomplete="name" autofocus>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="short_name">Short Name</label>
                                                <input id="short_name" type="text" placeholder="Enter Short Name"
                                                    class="form-control" name="short_name" required
                                                    @if ($edit) value="{{ $data->short_name }}" @else value="{{ old('short_name') }}" @endif
                                                    autocomplete="short_name" autofocus>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="image_url">School Image</label>
                                                <input id="image_url" type="file" placeholder="Enter School Image"
                                                    class="form-control" name="image_url"
                                                    @if ($edit) value="{{ $data->image_url }}"@else value="{{ old('image_url') }}" @endif
                                                    autocomplete="image_url" autofocus>
                                                @if ($edit && $data->image_url)
                                                    <img src="{{ asset('/storage/SchoolImage/' . $data->image_url) }}"
                                                        id="image_url_preview" height="60px" width="60px" />
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="website">Website</label>
                                                <input id="website" type="text" placeholder="Enter Website"
                                                    class="form-control" name="website"
                                                    @if ($edit) value="{{ $data->website }}" @else value="{{ old('website') }}" @endif
                                                    autocomplete="website" autofocus>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="card">
                        <div class="card-body">
                            <div class="row form_sec">
                                @if ($edit)
                                    <div class="col-12">
                                        <h5>Address Information</h5>
                                    </div>
                                @else
                                    <div class="col-12">
                                        <h5>Address Information</h5>
                                    </div>
                                @endif
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="street" class="control-label">{{ __('Street') }}</label>
                                        <input id="street" type="text" placeholder="Enter Street" class="form-control"
                                            name="street" required
                                            @if ($edit) value="{{ $data->street }}" @else value="{{ old('street') }}" @endif
                                            autocomplete="street" autofocus>
                                    </div>
                                    <div class="form-group">
                                        <label for="street2" class="control-label">{{ __('Street 2') }}</label>
                                        <input id="street2" type="text" placeholder="Enter Street" class="form-control"
                                            name="street2" required
                                            @if ($edit) value="{{ $data->street2 }}" @else value="{{ old('street2') }}" @endif
                                            autocomplete="street2" autofocus>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                <label for="city" class="control-label">{{ __('City') }}</label>
                                                <input id="city" type="text" placeholder="Enter City"
                                                    class="form-control" name="city" required
                                                    @if ($edit) value="{{ $data->city }}" @else value="{{ old('city') }}" @endif
                                                    autocomplete="city" autofocus>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group">
                                                <label for="zipcode" class="control-label">{{ __('Zip Code') }}</label>
                                                <input id="zipcode" type="text" class="form-control" name="zipcode"
                                                    placeholder="Enter Zip Code" required
                                                    @if ($edit) value="{{ $data->zipcode }}" @else value="{{ old('zipcode') }}" @endif
                                                    autocomplete="zipcode" autofocus>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="city" class="control-label">{{ __('State') }}</label>
                                        <?php
                                        $states = getStates();
                                        ?>
                                        <select class="form-control change_state" name="state" required>
                                            <option value="">Select State</option>
                                            @foreach ($states as $state_code => $state_name)
                                                <?php
                                                $selected = '';
                                                if ($edit && $state_code == $data['state']) {
                                                    $selected = 'selected';
                                                }
                                                ?>
                                                <option value="{{ $state_code }}" {{ $selected }}>
                                                    {{ $state_name }}</option>
                                            @endforeach
                                        </select>

                                        @error('state')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
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
                    <br />
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary add_site">
                                @if ($edit)
                                    Update Changes
                                @else
                                    Add School
                                @endif
                            </button>
                            @if ($edit)
                                <a href="{{ $cancel }}" class="btn btn-secondary">Cancel</a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script type="text/javascript" src="{{ asset('plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}">
    </script>
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
            $('.datepicker').datepicker({
                autoclose: true,
                setDate: new Date(),
                todayHighlight: true,
                clearBtn: true,
                format: "yyyy-mm-dd",
            });
        });

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
