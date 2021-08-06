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
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                @if (session()->has('msg'))
                    <div class="alert alert-success" id="alert">
                        {{ session()->get('msg') }}
                    </div>
                @endif
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>System Definitions

                            <a href="{{ route('systemdefinition.create') }}" class="btn btn-add rounded-circle">
                                <i class="fas fa-plus add-client-icon "></i>
                            </a>
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">System Definations</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">

                        <!-- /.card -->

                        <div class="card">
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <caption>Genetics</caption>
                                        <table id="centers" class="table table-bordered table-striped">

                                            <thead>
                                                <tr>
                                                    <th>Sr#</th>
                                                    <th>Genetic Name</th>
                                                    <th>Action</th>


                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($genetics as $genetic)
                                                    <tr>
                                                        <td>
                                                            {{ $loop->iteration }}
                                                        </td>
                                                        <td>
                                                            {{ $genetic->value }}
                                                        </td>
                                                        <td align="center">
                                                            <a href="{{ route('systemdefinition.edit', $genetic) }}"
                                                                class="btn btn-success">Edit</a>
                                                            <a href="{{ route('systemdefinition.del', $genetic) }}"
                                                                class="btn btn-danger">Delete</a>
                                                        </td>
                                                    </tr>
                                                @endforeach

                                            </tbody>

                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <caption>Flavours</caption>
                                        <table id="centers" class="table table-bordered table-striped">

                                            <thead>
                                                <tr>
                                                    <th>Sr#</th>
                                                    <th>Flavour Name</th>
                                                    <th>Action</th>



                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($flavours as $flavour)
                                                    <tr>
                                                        <td>
                                                            {{ $loop->iteration }}
                                                        </td>
                                                        <td>
                                                            {{ $flavour->value }}
                                                        </td>
                                                        <td align="center">
                                                            <a href="{{ route('systemdefinition.edit', $flavour) }}"
                                                                class="btn btn-success">Edit</a>
                                                            <a href="{{ route('systemdefinition.del', $flavour) }}"
                                                                class="btn btn-danger">Delete</a>
                                                        </td>
                                                    </tr>
                                                @endforeach

                                            </tbody>

                                        </table>
                                    </div>
                                </div>
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
    <!-- /.content-wrapper -->

@endsection
