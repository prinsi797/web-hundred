@extends('theme.layouts.app')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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

                      <input id="first_name" type="text" placeholder="Enter First Name" class="form-control" name="first_name" required @if ($edit) value="{{ $data->first_name }}" @else value="{{ old('first_name') }}" @endif autocomplete="first_name" autofocus>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="last_name">Last Name</label>
                      <input id="last_name" type="text" placeholder="Enter Last Name" class="form-control" name="last_name" required @if ($edit) value="{{ $data->last_name }}" @else value="{{ old('last_name') }}" @endif autocomplete="last_name" autofocus>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-group">
                      <label for="tagline">Email</label>
                      <input id="email" type="text" disabled placeholder="Enter Email" class="form-control" name="email" required @if ($edit) value="{{ $data->email }}" @else value="{{ old('email') }}" @endif autocomplete="email" autofocus>
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
                  <input type="password" name="old_password" autocomplete="new-password" class="form-control" id="old_password" aria-describedby="old_passwordHelp">
                  <small id="old_passwordHelp" class="form-text text-muted"></small>
                </div>
                @endif
                <div class="form-group">
                  <label for="new_password">Password</label>
                  <div class="float-right btn btn-sm btn-warning generate-password-btn"> <i class="fa fa-unlock-alt"></i> Generate Strong Password</div>
                  <div class="input-group" id="show_hide_password">
                    <input type="password" name="password" autocomplete="new-password" class="form-control new_password" id="new_password" placeholder="Enter Password" aria-describedby="new_passwordHelp">
                    <div class="input-group-append toggle-password">
                      <span class="input-group-text" id="basic-addon2"><i class="fa fa-eye-slash" aria-hidden="true"></i></span>
                    </div>
                  </div>
                  <small id="new_passwordHelp" class="form-text text-muted"></small>
                </div>

                <div class="form-group">
                  <label for="new_password_confirmation">Confirm Password</label>
                  <div class="input-group" id="show_hide_confirm_password">
                    <input type="password" name="password_confirmation" placeholder="Enter Confirm Password" class="form-control confirm_password" id="new_password_confirmation" aria-describedby="new_password_confirmationHelp">
                    <div class="input-group-append toggle-password">
                      <span class="input-group-text" id="basic-addon2"><i class="fa fa-eye-slash" aria-hidden="true"></i></span>
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
                  <input type="checkbox" name="is_report_shared" {{ $checked }} class="is_report_shared" id="is_report_shared">
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
  </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript">
  $(document).ready(function(e) {
    $('.change_role').select2({
      placeholder: "Select Role",
      allowClear: true,
      minimumResultsForSearch: Infinity
    });
    $('.change_state').select2({
      placeholder: "Select State",
      allowClear: true
    })
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