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
            @if (session()->has('msg'))
                <div class="alert alert-success">
                    <p>{{ session()->get('msg') }}<button type="button" class="close"
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
                                <li class="breadcrumb-item active">Roles</li>
                            </ol>
                        </div>
                        <div class="col-sm-8 pl-0">
                            <h1 class="m-0 text-dark text-uppercase font-weight-lighter text-heading">Roles

                            </h1>
                        </div>
                        <div class="col-sm-4 d-flex justify-content-end align-items-end">
                            <ol class="breadcrumb float-sm-right">
                                <a href="{{ url('/admin/roles/create') }}" class="px-0 btn btn-add rounded-circle">
                                    <button class="px-0 btn btn-dark bg-transparent border-0 add-button text-uppercase">
                                        Add Role</button>
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
                                    <table id="users" class="table border-bottom-0 table-bordered text-center custom-table ">
                                        <thead>
                                            <tr>
                                                <th>S#</th>
                                                <th> Name</th>
                                                <th>Guard Name</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($roles as $role)
                                                <tr>
                                                    <td>
                                                        {{ $loop->iteration }}
                                                    </td>
                                                    <td>{{ $role->name }}</td>
                                                    <td>{{ $role->guard_name }}</td>
                                                </tr>
                                            @endforeach

                                        </tbody>

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
@endsection
