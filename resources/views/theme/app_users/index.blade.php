@extends('theme.layouts.app')
@section('content')
    <style>
        .modal-dialog.modal-dialog-centered {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
        }

        /* CSS to make the tables display horizontally */
        .horizontal-table {
            display: inline-table;
            margin-right: 20px;
            /* Adjust as needed */
        }
    </style>
    <?php
    $page_number = 1;
    ?>
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
            </div>
            <div class="col-md-12">
                <?php
                $table_status == 'all' ? ($all_status = 'active') : ($all_status = '');
                $table_status == 'trashed' ? ($trash_status = 'active') : ($trash_status = '');
                ?>
                <ul class="nav nav-tabs">
                    <input type="hidden" class="all_trashed_input" value="{{ $table_status }}">
                    <li class="nav-item ">
                        <a class="nav-link {{ $all_status }} all_trashed" data-val="all" href="#">All
                            ({{ $all_count }})</a>
                    </li>
                    {{-- <li class="nav-item">
                        <a class="nav-link {{ $trash_status }} all_trashed" data-val="trashed" href="#">Deleted
                            ({{ $trashed_count }})</a>
                    </li> --}}
                </ul>
                <div class="card">
                    <div class="card-header">
                        <input type="hidden" class="action_selected" value="trash">
                        <div class="float-left trash_selected_button bulk_select_btn">
                            <div class="input-group pr-2">
                                <button class="btn btn-primary trash_selected" name="trash_selected">Trash Selected</button>
                            </div>
                        </div>
                        <div class="float-left restore_selected_button bulk_select_btn">
                            <div class="input-group pr-2">
                                <button class="btn btn-primary delete_selected" name="delete_selected">Delete
                                    Selected</button>
                            </div>
                        </div>
                        <div class="float-left restore_selected_button bulk_select_btn">
                            <div class="input-group pr-2">
                                <button class="btn btn-primary restore_selected" name="restore_selected">Restore
                                    Selected</button>
                            </div>
                        </div>
                        <div class="float-left">
                            <input type="hidden" name="page_number" id="page_number" class="page_number"
                                value="{{ $page_number }}">
                            <div class="input-group pr-2">
                                <input type="text" class="form-control search" name="search" id="search"
                                    placeholder="Search by Name">
                            </div>
                        </div>
                        <button class="btn btn-primary pl-2 search_data">Search</button>
                        <button class="btn btn-primary pl-2 reset_data">Reset</button>
                        <div class="float-right">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="inputGroupSelect01">Row</label>
                                </div>
                                <select class="custom-select change_row_limit" id="inputGroupSelect01">
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                </select>
                                <a class="btn btn-primary ml-2" href="{{ $create_route }}">Add</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="ajax_loader p-3" align="center"><img
                                src="{{ asset('assets/images/ajax_loader_circular.gif') }}" alt=""></div>
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        <div class="load_data"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Status Popup --}}

    <div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-labelledby="statusModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="statusModalLabel">Stats</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Deadlift Table -->
                    <table class="table horizontal-table" id="deadliftTable" style="width: 30% !important;">
                       
                    </table>

                    <!-- Power Clean Table -->
                    <table class="table horizontal-table" id="powerCleanTable" style="width: 30% !important;">
                       
                    </table>

                    <!-- Bench Press Table -->
                    <table class="table horizontal-table" id="benchPressTable" style="width: 30% !important;">
                       
                    </table>

                    <!-- Squat Table -->
                    <table class="table horizontal-table" id="squatTable" style="width: 30% !important;">
                      
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Contact popup --}}

    <div class="modal fade" id="contactsModal" tabindex="-1" role="dialog" aria-labelledby="contactsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="contactsModalLabel">Contacts</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Phone Number</th>
                            </tr>
                        </thead>
                        <tbody id="contactsTableBody">
                            <!-- Contact table content will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- School popup --}}

    <div class="modal fade" id="schoolModal" tabindex="-1" role="dialog" aria-labelledby="schoolModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="schoolModalLabel">Schools</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="">
                                <tr>
                                    <th>School ID</th>
                                    <th>Name</th>
                                    <th>Image</th>
                                    <th>Short Name</th>
                                    <th>Website</th>
                                    <th>Street</th>
                                    <th>Street2</th>
                                    <th>Zipcode</th>
                                    <th>City</th>
                                    <th>State</th>
                                </tr>
                            </thead>
                            <tbody id="schoolTableBody">
                                <!-- School table content will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Friend Model friends_btn --}}
    <div class="modal fade" id="friendModal" tabindex="-1" role="dialog" aria-labelledby="friendModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="friendModalLabel">Friends</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>DOB</th>
                                    <th>Phone</th>
                                    <th>Profile photo</th>
                                    <th>User Name</th>
                                </tr>
                            </thead>
                            <tbody id="friendTableBody">
                                <!-- School table content will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>



    @push('scripts')
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

        <script type="text/javascript">
            function load_data() {
                $(".load_data").html('');
                $(".ajax_loader").show();

                var token = '{{ csrf_token() }}';
                var limit = $(".change_row_limit option:selected").val();
                var page_number = $(".page_number").val();
                var string = $(".search").val();
                var all_trashed = $(".all_trashed_input").val();

                $.ajax({
                    type: 'GET',
                    url: '{{ $ajax_route }}',
                    data: {
                        _token: token,
                        page_number: page_number,
                        string: string,
                        all_trashed: all_trashed,
                        limit: limit
                    },
                    success: function(html) {
                        $(".ajax_loader").hide();
                        $(".load_data").html(html);
                        // perform pagination.
                        $(".page-link").click(function(e) {
                            e.preventDefault();
                            page_number = $(this).attr('data-page');
                            $(".page_number").val(page_number);
                            load_data();
                        });

                        //Trash Item
                        $(".trash_btn").click(function(e) {
                            e.preventDefault();
                            var data_id = $(this).attr('data-id');
                            // var data_status = $(this).attr('data-status');
                            var status_msg = "It will be deleted from the system!";
                            swal({
                                    title: "Are you sure?",
                                    text: status_msg,
                                    icon: "warning",
                                    buttons: true,
                                    dangerMode: true,
                                })
                                .then((willDelete) => {
                                    if (willDelete) {

                                        var token = '{{ csrf_token() }}';
                                        $.ajax({
                                            type: 'POST',
                                            url: '{{ $delete_route }}',
                                            data: {
                                                _token: token,
                                                data_id: data_id,
                                                action: 'trash',
                                                is_bulk: 0,
                                            },
                                            dataType: 'JSON',
                                            success: function(resp) {
                                                var res_msg =
                                                    "It has been deleted successfully.";
                                                swal(res_msg, {
                                                    icon: "success",
                                                }).then(function() {
                                                    location.reload();
                                                });
                                            },

                                        });
                                    }
                                });
                        });

                        //Delete Item
                        $(".delete_btn").click(function(e) {
                            e.preventDefault();
                            var data_id = $(this).attr('data-id');
                            // var data_status = $(this).attr('data-status');
                            var status_msg = "It will be deleted from the system!";
                            swal({
                                    title: "Are you sure?",
                                    text: status_msg,
                                    icon: "warning",
                                    buttons: true,
                                    dangerMode: true,
                                })
                                .then((willDelete) => {
                                    if (willDelete) {

                                        var token = '{{ csrf_token() }}';
                                        $.ajax({
                                            type: 'POST',
                                            url: '{{ $delete_route }}',
                                            data: {
                                                _token: token,
                                                data_id: data_id,
                                                action: 'delete',
                                                is_bulk: 0,
                                            },
                                            dataType: 'JSON',
                                            success: function(resp) {
                                                var res_msg =
                                                    "Item has been deleted successfully.";
                                                swal(res_msg, {
                                                    icon: "success",
                                                }).then(function() {
                                                    location.reload();
                                                });
                                            },

                                        });
                                    }
                                });
                        });
                        //status button
                        $(".status_btn").click(function(e) {
                            e.preventDefault();
                            var data_id = $(this).data('id');
                            $.ajax({
                                type: 'GET',
                                url: '{{ route('admin.app_users.status') }}',
                                data: {
                                    app_user_id: data_id
                                },
                                success: function(response) {
                                    // Hide tables based on lift_type
                                    $('#deadliftTable').empty();
                                    $('#powerCleanTable').empty();


                                    // Hide all tables first
                                    $('#deadliftTable').hide();
                                    $('#powerCleanTable').hide();

                                    // Show appropriate table based on lift_type
                                    if (response.user.lift_type === 'deadlift') {
                                        $('#deadliftTable').show();
                                    } else if (response.user.lift_type === 'power_clean') {
                                        $('#powerCleanTable').show();
                                    }
                                    // Append Deadlift Records
                                    var deadliftRecords = '';
                                    if (response.latest_deadlift.length > 0) {
                                        var deadliftHeading =
                                            '<thead><tr><th>Deadlift Date</th><th>Deadlift Value</th></tr></thead>';
                                        $('#deadliftTable').html(deadliftHeading);
                                        $.each(response.latest_deadlift, function(index,
                                            record) {
                                            deadliftRecords += '<tr><td>' + record
                                                .date +
                                                '</td><td>' + record.deadlift +
                                                '</td></tr>';
                                        });
                                        $('#deadliftTable').append(deadliftRecords);
                                    } else {
                                        var deadliftHeading =
                                            '<thead><tr><th>Deadlift Date</th><th>Deadlift Value</th></tr></thead>';
                                        $('#deadliftTable').html(deadliftHeading);

                                        $('#deadliftTable').append(
                                            '<tr><td colspan="2">No Deadlift records found</td></tr>'
                                        );
                                    }

                                    // Append Power Clean Records
                                    var powerCleanRecords = '';
                                    if (response.latest_power_clean.length > 0) {
                                        var powerCleanHeading =
                                            '<thead><tr><th>Powerclean Date</th><th>Powerclean</th></tr></thead>';
                                        $('#powerCleanTable').html(powerCleanHeading);
                                        $.each(response.latest_power_clean, function(index,
                                            record) {
                                            powerCleanRecords += '<tr><td>' + record
                                                .date +
                                                '</td><td>' + record.power_clean +
                                                '</td></tr>';
                                        });

                                        $('#powerCleanTable').append(powerCleanRecords);
                                    }else {
                                        var powerCleanHeading =
                                            '<thead><tr><th>Powerclean Date</th><th>Powerclean Value</th></tr></thead>';
                                        $('#powerCleanTable').html(powerCleanHeading);

                                        $('#powerCleanTable').append(
                                            '<tr><td colspan="2">No Powerclean records found</td></tr>'
                                        );
                                    }

                                    // Append Bench Press Records
                                    var benchPressRecords = '';
                                    if (response.latest_bench_press.length > 0) {
                                        var benchPressHeading =
                                            '<thead><tr><th>Benchpress Date</th><th>Benchpress</th></tr></thead>';
                                        $('#benchPressTable').html(benchPressHeading);
                                        $.each(response.latest_bench_press, function(index,
                                            record) {
                                            benchPressRecords += '<tr><td>' + record
                                                .date +
                                                '</td><td>' + record.bench_press +
                                                '</td></tr>';
                                        });
                                        $('#benchPressTable').append(benchPressRecords);
                                    }else {
                                        var benchPressHeading =
                                            '<thead><tr><th>Benchpress Date</th><th>Benchpress Value</th></tr></thead>';
                                        $('#benchPressTable').html(benchPressHeading);

                                        $('#benchPressTable').append(
                                            '<tr><td colspan="2">No Benchpress records found</td></tr>'
                                        );
                                    }

                                    // Append Squat Records
                                    var squatRecords = '';
                                    if (response.latest_squat.length > 0) {
                                        var squatHeading =
                                            '<thead><tr><th>Squat Date</th><th>Squat</th></tr></thead>';
                                        $('#squatTable').html(squatHeading);
                                        $.each(response.latest_squat, function(index, record) {
                                            squatRecords += '<tr><td>' + record.date +
                                                '</td><td>' + record.squat +
                                                '</td></tr>';
                                        });
                                        $('#squatTable').append(squatRecords);
                                    }else {
                                        var squatHeading =
                                            '<thead><tr><th>Squat Date</th><th>Squat Value</th></tr></thead>';
                                        $('#squatTable').html(squatHeading);

                                        $('#squatTable').append(
                                            '<tr><td colspan="2">No Squat records found</td></tr>'
                                        );
                                    }

                                    $('#statusModal').modal('show');
                                },
                                error: function(xhr, status, error) {
                                    console.error(error);
                                }
                            });
                        });

                        //friend button 

                        $(".friends_btn").click(function(e) {
                            e.preventDefault();
                            var data_id = $(this).data('id');
                            $.ajax({
                                type: 'GET',
                                url: '{{ route('admin.app_users.friends') }}',
                                data: {
                                    app_user_id: data_id
                                },
                                success: function(response) {
                                    var friendsHtml = '';
                                    if (response.length >
                                        0) {
                                        response.forEach(function(friend) {
                                            friendsHtml += '<tr>';
                                            friendsHtml += '<td>' + friend.friend.id +
                                                '</td>';
                                            friendsHtml += '<td>' + friend.friend.name +
                                                '</td>';
                                            friendsHtml += '<td>' + friend.friend.dob +
                                                '</td>';
                                            friendsHtml += '<td>' + friend.friend
                                                .phone_number + '</td>';
                                            var imagePath =
                                                '{{ asset('storage/ProfilePic') }}' +
                                                '/' + friend.friend.profile_photo_url;
                                            friendsHtml += '<td><img src="' +
                                                imagePath + '" alt="' + friend.friend
                                                .profile_photo_url +
                                                '" class="img-thumbnail" style="width: 100px;"></td>';

                                            friendsHtml += '<td>' + friend.friend
                                                .username +
                                                '</td>';
                                            friendsHtml += '</tr>';
                                        });
                                    } else {
                                        friendsHtml +=
                                            '<tr><td colspan="10" class="text-center">No records available</td></tr>';
                                    }
                                    $('#friendTableBody').html(friendsHtml);
                                    $('#friendModal').modal('show');
                                },
                                error: function(xhr, status, error) {
                                    console.error(error);
                                }
                            });
                        });

                        // school button
                        $(".school_btn").click(function(e) {
                            e.preventDefault();
                            var data_id = $(this).data('id');
                            $.ajax({
                                type: 'GET',
                                url: '{{ route('admin.app_users.schools') }}',
                                data: {
                                    app_user_id: data_id
                                },
                                success: function(response) {
                                    var schoolsHtml = '';
                                    if (response.length > 0) {
                                        response.forEach(function(appUserSchool) {
                                            var school = appUserSchool.school;
                                            schoolsHtml += '<tr>';
                                            schoolsHtml += '<td>' + school.id + '</td>';
                                            schoolsHtml += '<td>' + school.name +
                                                '</td>';
                                            // Adjust the image_url to include the full path
                                            var imagePath =
                                                '{{ asset('storage/SchoolImage') }}' +
                                                '/' + school.image_url;
                                            schoolsHtml += '<td><img src="' +
                                                imagePath + '" alt="' + school.name +
                                                '" class="img-thumbnail" style="width: 100px;"></td>';
                                            schoolsHtml += '<td>' + school.short_name +
                                                '</td>';
                                            schoolsHtml += '<td>' + school.website +
                                                '</td>';
                                            schoolsHtml += '<td>' + school.street +
                                                '</td>';
                                            schoolsHtml += '<td>' + school.street2 +
                                                '</td>';
                                            schoolsHtml += '<td>' + school.zipcode +
                                                '</td>';
                                            schoolsHtml += '<td>' + school.city +
                                                '</td>';
                                            schoolsHtml += '<td>' + school.state +
                                                '</td>';
                                            schoolsHtml += '</tr>';
                                        });
                                    } else {
                                        schoolsHtml +=
                                            '<tr><td colspan="10" class="text-center">No records available</td></tr>';
                                    }
                                    $('#schoolTableBody').html(schoolsHtml);
                                    $('#schoolModal').modal('show');
                                },
                                error: function(xhr, status, error) {
                                    console.error(error);
                                }
                            });
                        });

                        //contact button 
                        $(".contacts_btn").click(function(e) {
                            e.preventDefault();
                            var data_id = $(this).attr('data-id');
                            $.ajax({
                                type: 'GET',
                                url: '{{ route('admin.app_users.contacts') }}',
                                data: {
                                    app_user_id: data_id
                                },
                                success: function(response) {
                                    var contactsHtml = '';
                                    if (response.length >
                                        0) {
                                        response.forEach(function(contact) {
                                            contactsHtml += '<tr>';
                                            // contactsHtml += '<td>' + contact.app_user
                                            //     .name +
                                            //     '</td>';
                                            contactsHtml += '<td>' + contact
                                                .contact_firstname +
                                                '</td>';
                                            contactsHtml += '<td>' + contact
                                                .contact_lastname +
                                                '</td>';
                                            contactsHtml += '<td>' + contact
                                                .contact_phone_number +
                                                '</td>';
                                            contactsHtml += '</tr>';
                                        });
                                    } else { // If response array is empty
                                        contactsHtml +=
                                            '<tr><td colspan="10" class="text-center">No records available</td></tr>';
                                    }
                                    $('#contactsTableBody').html(contactsHtml);
                                    $('#contactsModal').modal('show');
                                },
                                error: function(xhr, status, error) {
                                    console.error(error);
                                }
                            });
                        });

                        //Restore Item
                        $(".restore_btn").click(function(e) {
                            e.preventDefault();
                            var data_id = $(this).attr('data-id');
                            // var data_status = $(this).attr('data-status');
                            var status_msg = "Item will be restored!";
                            swal({
                                    title: "Are you sure?",
                                    text: status_msg,
                                    icon: "warning",
                                    buttons: true,
                                    dangerMode: true,
                                })
                                .then((willDelete) => {
                                    if (willDelete) {

                                        var token = '{{ csrf_token() }}';
                                        $.ajax({
                                            type: 'POST',
                                            url: '{{ $delete_route }}',
                                            data: {
                                                _token: token,
                                                data_id: data_id,
                                                action: 'restore',
                                                is_bulk: 0,
                                            },
                                            dataType: 'JSON',
                                            success: function(resp) {
                                                var res_msg =
                                                    "It has been restored successfully.";
                                                swal(res_msg, {
                                                    icon: "success",
                                                }).then(function() {
                                                    location.reload();
                                                });
                                            },

                                        });
                                    }
                                });
                        });

                        //Changing parent checkbox
                        $(".row_check_all").change(function(e) {
                            var action = $('.action_selected').val();
                            if (this.checked) {
                                $('.row_checkbox').prop('checked', true);
                                var checkbox_vals = [];
                                $('.row_checkbox').each(function() {
                                    (this.checked ? checkbox_vals.push($(this).val()) : "");
                                });
                                $("." + action + "_selected_button").show();
                                $("." + action + "_selected_button button").attr('data-id', checkbox_vals);
                            } else {
                                $("." + action + "_selected_button").hide();
                                $("." + action + "_selected_button button").attr('data-id', '');
                                $('.row_checkbox').prop('checked', false);
                            }
                        });

                        //Changing child checkbox
                        $(".row_checkbox").change(function(e) {
                            var checkbox_vals = [];
                            $('.row_checkbox').each(function() {
                                (this.checked ? checkbox_vals.push($(this).val()) : "");
                            });
                            var action = $('.action_selected').val();
                            if (checkbox_vals.length > 0) {
                                $("." + action + "_selected_button").show();
                                $("." + action + "_selected_button button").attr('data-id', checkbox_vals);
                            } else {
                                $("." + action + "_selected_button button").attr('data-id', '');
                                $("." + action + "_selected_button").hide();
                            }
                        });

                        // Trash selected.
                        $(".trash_selected").click(function(e) {
                            e.preventDefault();
                            var data_id = $(this).attr('data-id');
                            // var data_status = $(this).attr('data-status');
                            var status_msg = "One or more items will be trashed !";
                            swal({
                                    title: "Are you sure?",
                                    text: status_msg,
                                    icon: "warning",
                                    buttons: true,
                                    dangerMode: true,
                                })
                                .then((willDelete) => {
                                    if (willDelete) {

                                        var token = '{{ csrf_token() }}';
                                        $.ajax({
                                            type: 'POST',
                                            url: '{{ $delete_route }}',
                                            data: {
                                                _token: token,
                                                data_id: data_id,
                                                action: 'trash',
                                                is_bulk: 1,
                                            },
                                            dataType: 'JSON',
                                            success: function(resp) {
                                                var res_msg =
                                                    "Items are trashed successfully.";
                                                swal(res_msg, {
                                                    icon: "success",
                                                }).then(function() {
                                                    location.reload();
                                                });
                                            },

                                        });
                                    }
                                });
                        });

                        // Delete selected.
                        $(".delete_selected").click(function(e) {
                            e.preventDefault();
                            var data_id = $(this).attr('data-id');
                            // var data_status = $(this).attr('data-status');
                            var status_msg = "One or more items will be deleted !";
                            swal({
                                    title: "Are you sure?",
                                    text: status_msg,
                                    icon: "warning",
                                    buttons: true,
                                    dangerMode: true,
                                })
                                .then((willDelete) => {
                                    if (willDelete) {

                                        var token = '{{ csrf_token() }}';
                                        $.ajax({
                                            type: 'POST',
                                            url: '{{ $delete_route }}',
                                            data: {
                                                _token: token,
                                                data_id: data_id,
                                                action: 'delete',
                                                is_bulk: 1,
                                            },
                                            dataType: 'JSON',
                                            success: function(resp) {
                                                var res_msg =
                                                    "Items are deleted successfully.";
                                                swal(res_msg, {
                                                    icon: "success",
                                                }).then(function() {
                                                    location.reload();
                                                });
                                            },
                                        });
                                    }
                                });
                        });

                        // Restore selected.
                        $(".restore_selected").click(function(e) {
                            e.preventDefault();
                            var data_id = $(this).attr('data-id');
                            // var data_status = $(this).attr('data-status');
                            var status_msg = "One or more items will be deleted !";
                            swal({
                                    title: "Are you sure?",
                                    text: status_msg,
                                    icon: "warning",
                                    buttons: true,
                                    dangerMode: true,
                                })
                                .then((willDelete) => {
                                    if (willDelete) {

                                        var token = '{{ csrf_token() }}';
                                        $.ajax({
                                            type: 'POST',
                                            url: '{{ $delete_route }}',
                                            data: {
                                                _token: token,
                                                data_id: data_id,
                                                action: 'restore',
                                                is_bulk: 1,
                                            },
                                            dataType: 'JSON',
                                            success: function(resp) {
                                                var res_msg =
                                                    "Items has been restored successfully.";
                                                swal(res_msg, {
                                                    icon: "success",
                                                }).then(function() {
                                                    location.reload();
                                                });
                                            },

                                        });
                                    }
                                });
                        });
                    },
                });
            }
            $(document).ready(function() {
                $(".bulk_select_btn").hide();
                // $(".restore_selected_button").hide();
                load_data();
                $(".change_row_limit").change(function() {
                    load_data();
                });
                $(".search_data").click(function(e) {
                    e.preventDefault();
                    load_data();
                });
                $(".reset_data").click(function(e) {
                    e.preventDefault();
                    $(".search").val('');
                    load_data();
                });
                $(".all_trashed").click(function(e) {
                    $(".bulk_select_btn").hide();
                    e.preventDefault();
                    temp = $(this).attr('data-val');
                    $(".all_trashed").removeClass('active');
                    $(this).addClass('active');
                    $(".all_trashed_input").val(temp);
                    if (temp == "all") {
                        $(".action_selected").val('trash');
                    } else {
                        $(".action_selected").val('restore');
                    }
                    load_data();
                });
            });
        </script>
    @endpush
@endsection
