@extends('layouts.default')
@section('title', 'All Villages')
@section('content')
    <style type="text/css">
        .dataTables_wrapper .dataTables_filter input {
            margin-left: 0.5em;
            width: 240px;
        }

        .input-height {
            height: 70%;
        }

        .newbtn {
            width: 30%;

        }

    </style>
    <script>
        $(document).ready(function() {
            $('#myTable').DataTable();
            $('#cfeDrying').on('click', function() {
                $('form').attr('action', '{{ url('admin/report/generateCfeDrying') }}')
            });
            $('#cfeDryingWarehouse').on('click', function() {
                $('form').attr('action', '{{ url('admin/report/generateWarehouse') }}')
            });
        });
    </script>


    <div class="content-wrapper">

        @if (Session::has('msg'))
            <div class="alert alert-success" role="alert">
                <b>{{ Session::get('msg') }}</b>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6 pl-0">
                        <h1 class="m-0 text-dark text-uppercase font-weight-lighter text-heading">Generate Report

                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">

                        </ol>
                    </div>
                </div>
            </div>
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
                                <form action="{{ url('admin/report/generate') }}" method="POST">
                                    @csrf

                                    <div class="row">

                                        <div class="col-6"> <label for="exampleInputEmail1">From</label>
                                            <input type="date" name="from" class="form-control input-height"
                                                id="exampleInputEmail1" aria-describedby="emailHelp">
                                        </div>
                                        <div class="col-6"> <label for="exampleInputEmail1">To</label>
                                            <input type="date" name="to" class="form-control input-height"
                                                id="exampleInputEmail1" aria-describedby="emailHelp">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4"> <button type="submit"
                                                class="btn btn-primary newbtn  mt-4"
                                                style="margin-left:47%">Purchase</button></div>
                                        <div class="col-md-4">
                                            <button type="submit" id="cfeDrying" class="btn btn-primary  newbtn mt-4"
                                                style="margin-left:47%">Coffee on Drying</button>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="submit" id="cfeDryingWarehouse"
                                                class="btn btn-primary newbtn  mt-4" style="margin-left:47%">Dry
                                                coffee warehouse</button>
                                        </div>

                                    </div>

                                </form>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </section>

    </div>


@endsection
