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

            $('#cfebuyer').on('click', function() {
                if($('#from-date').val() == '' || $('#to').val() == ''){
                    $('#form').submit(function (evt) {
                    evt.preventDefault();
                    });
                    console.log('empty');
                    alert('Please Select Interval dates');
                }
            });
            $('#cfeDrying').on('click', function() {
                if($('#from-date').val() == '' || $('#to').val() == ''){
                    $('#form').submit(function (evt) {
                    evt.preventDefault();
                    });
                    console.log('empty');
                    alert('Please Select Interval dates');
                }else{

                $('form').attr('action', '{{ url('admin/report/generateCfeDrying') }}');
                }
            });
            $('#cfeDryingWarehouse').on('click', function() {
                if($('#from-date').val() == '' || $('#to').val() == ''){
                    $('#form').submit(function (evt) {
                    evt.preventDefault();
                    });
                    console.log('empty');
                    alert('Please Select Interval dates');
                }else{
                $('form').attr('action', '{{ url('admin/report/generateWarehouse') }}');}
            });
        });
    </script>


    <div class="content-wrapper">
        <div class="mx-lg-5">

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

                            <div class="card pl-0 border-0 shadow-none">
                                <!-- /.card-header -->

                                <div class="card-body pl-0 border-0 shadow-none">
                                    <hr class="ml-md-2">
                                    <div class="row ml-2 text-uppercase mb-2">
                                        <strong>
                                            <b>Date Filter</b>
                                        </strong>
                                    </div>
                                    <div class="row ml-2 mb-2">

                                        <form class="col-12 pl-0" id="form" action="{{ url('admin/report/generate') }}"
                                            method="POST">
                                            @csrf


                                            <label class="text-uppercase font-weight-normal mr-2 mb-0 letter-spacing-1"
                                                for="exampleInputEmail1">From</label>
                                            <input class="mr-3" type="date" name="from" id="from-date"
                                                aria-describedby="emailHelp">

                                            <label class="text-uppercase font-weight-normal mr-2 mb-0 letter-spacing-1"
                                                for="exampleInputEmail1">To</label>
                                            <input class="mr-3" type="date" name="to" id="to"
                                                aria-describedby="emailHelp">

                                            <br>
                                            <!-- Main content -->
                                            <section class="content mt-4">
                                                <div class="container-fluid">
                                                    <div class="row">
                                                        <div class="col-lg-5">

                                                            <!-- /.card -->

                                                            <div class="card shadow-none">
                                                                <!-- /.card-header -->
                                                                <div class="">

                                                                    <table
                                                                        class="table table-bordered region-table-custom  mb-0">

                                                                        <tbody>
                                                                            <tr>
                                                                                <th>
                                                                                    Purchase
                                                                                </th>
                                                                                <td class="text-center">
                                                                                    <button style="min-width: 100px;"
                                                                                        type="submit"
                                                                                        id="cfebuyer"
                                                                                        class="btn bg-green-btn newbtn">
                                                                                        Report
                                                                                    </button>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <th>
                                                                                    Coffee on Drying
                                                                                </th>
                                                                                <td class="text-center">
                                                                                    <button style="min-width: 100px;"
                                                                                        type="submit" id="cfeDrying"
                                                                                        class="btn bg-green-btn newbtn">
                                                                                        Report
                                                                                    </button>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <th>
                                                                                    Dry coffee warehouse
                                                                                </th>
                                                                                <td class="text-center">
                                                                                    <button style="min-width: 100px;"
                                                                                        type="submit"
                                                                                        id="cfeDryingWarehouse"
                                                                                        class="btn bg-green-btn newbtn">
                                                                                        Report
                                                                                    </button>
                                                                                </td>
                                                                            </tr>
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
                                        </form>
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </section>

        </div>
    </div>

@endsection
