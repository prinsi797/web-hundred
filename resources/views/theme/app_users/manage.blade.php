@extends('theme.layouts.app')
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
                                                <input id="name" type="text" placeholder="Enter Name"
                                                    class="form-control" name="name" required
                                                    @if ($edit) value="{{ $data->name }}" @else value="{{ old('name') }}" @endif
                                                    autocomplete="name" autofocus>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                          <div class="form-group">
                                              <label for="lift_type">Lift Type</label>
                                              <select name="lift_type" class="form-control" required id="lift_type">
                                                  <option value="">Select Lift Type</option>
                                                  <option value="deadlift" @if($edit && $data->lift_type == 'deadlift') selected @endif>Deadlift</option>
                                                  <option value="power_clean" @if($edit && $data->lift_type == 'power_clean') selected @endif>Power Clean</option>
                                              </select>
                                          </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="dob">Date Of Birth</label>
                                                <input id="dob" type="text" placeholder="Choose dob" required
                                                    class="form-control datepicker" name="dob"
                                                    @if ($edit) value="{{ $data->dob }}" @else value="{{ old('dob') }}" @endif
                                                    autocomplete="dob" autofocus>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="phone_number">Phone Number</label>
                                                <input id="phone_number" type="text" placeholder="Enter Phone Number"
                                                    class="form-control phone_number" name="phone_number" required
                                                    @if ($edit)
                                                    @if (strlen($data->phone_number) === 10) {{-- Assuming UK format has 10 digits --}}
                                                        value="{{ '(' . substr($data->phone_number, 0, 3) . ') ' . substr($data->phone_number, 3, 3) . '-' . substr($data->phone_number, 6) }}"
                                                    @else
                                                        value="{{ $data->phone_number }}"
                                                    @endif
                                                @else
                                                    value="{{ old('phone_number') }}"
                                                @endif
                                                    autocomplete="phone_number" autofocus>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="profile_photo_url">Profile Photo</label>
                                                <input id="profile_photo_url" type="file"
                                                    placeholder="Enter Profile Photo" class="form-control"
                                                    name="profile_photo_url"
                                                    @if ($edit) value="{{ $data->profile_photo_url }}" @else value="{{ old('profile_photo_url') }}" @endif
                                                    autocomplete="profile_photo_url" autofocus>
                                                @if ($edit && $data->profile_photo_url)
                                                    <img src="{{ asset('/storage/ProfilePic/' . $data->profile_photo_url) }}"
                                                        id="profile_photo_url_preview" height="60px" width="60px" />
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br />
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary add_site">
                                @if ($edit)
                                    Update Changes
                                @else
                                    Add App User
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
    <script type="text/javascript">
        $(document).ready(function(e) {
            $('.datepicker').datepicker({
                autoclose: true,
                setDate: new Date(),
                todayHighlight: true,
                clearBtn: true,
                format: "yyyy-mm-dd",
            });
            $('.phone_number').on('keyup', function() {
                var input = $(this).val().replace(/\D/g, '');
                var areaCode = input.substr(0, 3);
                var firstPart = input.substr(3, 3);
                var secondPart = input.substr(6, 4);
                var formattedNumber = "";

                if (input.length > 0) {
                    formattedNumber += "(" + areaCode;
                }
                if (input.length >= 4) {
                    formattedNumber += ") " + firstPart;
                }
                if (input.length >= 7) {
                    formattedNumber += "-" + secondPart;
                }
                $(this).val(formattedNumber);
            });
        });
    </script>
@endpush
