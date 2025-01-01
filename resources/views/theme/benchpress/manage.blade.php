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
                                                <label for="app_user_id">User</label>
                                                <select name="app_user_id" class="form-control change_advertiser" required
                                                    id="app_user_id">
                                                    <option value="">Select Users</option>
                                                    @foreach ($users as $user)
                                                        <option value="{{ $user->id }}"
                                                            @if ($edit && $data->app_user_id == $user->id) selected @endif>
                                                            {{ $user->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="date">Date</label>
                                                <input id="date" type="text" placeholder="Choose date" class="form-control datepicker" name="date" @if ($edit) value="{{ $data->date }}" @else value="{{ old('date') }}" @endif autocomplete="date" autofocus>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="bench_press">Bench Press</label>
                                                <input id="bench_press" type="text" placeholder="Enter Bench Press"
                                                    class="form-control" name="bench_press"
                                                    @if ($edit) value="{{ $data->bench_press }}" @else value="{{ old('bench_press') }}" @endif
                                                    autocomplete="bench_press" autofocus>
                                            </div>
                                        </div>
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
                                    Add BenchPres
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
