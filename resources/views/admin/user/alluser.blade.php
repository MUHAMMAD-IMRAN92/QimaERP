@extends('layouts.default')
@section('title', 'All Center')
@section('content')
    <style type="text/css">
        .dataTables_wrapper .dataTables_filter input {
            margin-left: 0.5em;
            width: 240px;
        }

    </style>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <div class="mx-lg-5">
            @if (\Session::has('message'))
                <div class="alert alert-success">
                    <p>{{ \Session::get('message') }}<button type="button" class="close"
                            data-dismiss="alert">&times;</button></p>

                </div>
            @endif
            @if (session()->has('msg'))
                <div class="alert alert-success">
                    <p>{{ session()->get('msg') }}<button type="button" class="close"
                            data-dismiss="alert">&times;</button></p>

                </div>
            @endif
            @if (\Session::has('update'))
                <div class="alert alert-success">
                    <p>{{ \Session::get('update') }}<button type="button" class="close"
                            data-dismiss="alert">&times;</button></p>

                </div>
            @endif
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6"></div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">All Users</li>
                            </ol>
                        </div>
                        <div class="col-sm-8 pl-0">
                            <h1 class="m-0 text-dark text-uppercase font-weight-lighter text-heading">Users Information

                            </h1>
                        </div>
                        <div class="col-sm-4 d-flex justify-content-end align-items-end">
                            <ol class="breadcrumb float-sm-right">
                                <a href="{{ url('/admin/adduser') }}" class="px-0 btn btn-add rounded-circle">
                                    <button class="px-0 btn btn-dark bg-transparent border-0 add-button text-uppercase">
                                        Add User</button>
                                </a>
                            </ol>
                        </div>

                    </div>
                </div><!-- /.container-fluid -->
                <hr class="ml-md-2">
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">

                            <!-- /.card -->

                            <div class="card shadow-none">
                                <!-- /.card-header -->
                                <div class="table-responsive text-uppercase letter-spacing-2 governors_table ">
                                    <table id="users" class="table border-bottom-0 table-bordered-custom text-center custom-table ">
                                        <thead>
                                            <tr>
                                                <th>S#</th>
                                                <th>First Name</th>
                                                <th>Last Name</th>
                                                <th>Email</th>
                                                <th>Role</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>


                                    </table>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
    </div>
    <!-- /.content-wrapper -->
    <script>
        let base_path = '<?= asset('/') ?>';
        $(document).ready(function() {
            var t = $('#users').DataTable({
                "processing": true,
                "serverSide": true,
                "deferRender": true,
                "ajax": {
                    url: '<?= asset('admin/getuser') ?>',
                },
                "columns": [{
                        "data": null
                    },
                    {
                        "data": 'first_name'
                    },
                    {
                        "data": 'last_name'
                    },
                    {
                        "data": 'email'
                    },
                    {
                        "mRender": function(data, type, row) {
                            var role = '-';
                            if (typeof(row.roles) != "undefined" && row.roles !== null && typeof(row
                                    .roles[0]) != "undefined" && row.roles[0] !== null) {
                                role = row.roles[0].name;
                            }
                            return '<td>' + role + '</td>';
                        }
                    },
                    {
                        "mRender": function(data, type, row) {
                            let userId = row.user_id;
                            return '<a href=' + base_path + 'admin/edituser/' + row.user_id +
                                '>Edit</a> | <a href="#" class="editor_remove" data-id="' + row
                                .user_id +
                                '">Delete</a>| <a href=' + base_path + 'admin/resetPasswordView/' +
                                row.user_id + '>Reset Password</a>';
                        }
                    }
                ],
                "columnDefs": [{
                    "searchable": false,
                    "orderable": false,
                    "targets": [0, 2],
                }],
                "order": [], //Initial no order.
                "aaSorting": [],
            });
            t.on('draw.dt', function() {
                var PageInfo = $('#users').DataTable().page.info();
                t.column(0, {
                    page: 'current'
                }).nodes().each(function(cell, i) {
                    cell.innerHTML = i + 1 + PageInfo.start;
                });
            }).draw();
        });
        $('#users').on('click', 'a.editor_remove', function(e) {
            e.preventDefault();
            if (confirm('You want to delete user?')) {
                var id = $(this).data('id');
                $(this).closest('tr').remove();
                var id = $(this).data('id');
                $.ajax({
                    type: "GET",
                    url: base_path + 'admin/deleteuser/' + id

                });
                ////            location.reload();
            }
        });
    </script>
@endsection
