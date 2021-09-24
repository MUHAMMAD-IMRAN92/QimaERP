@extends('layouts.default')
@section('title', 'All Farmers')
@section('content')

    <style type="text/css">
        .dataTables_wrapper .dataTables_filter input {
            margin-left: 0.5em;
            width: 240px;
        }

        #search:hover {
            cursor: pointer;
        }

    </style>
    <script>
        $(document).ready(function() {
            $('#search').on('click', function() {
                let farmerName = $("#farmerName").val();
                let farmerCode = $("#farmerCode").val();

                $.ajax({
                    url: "{{ url('admin/search') }}",
                    data: {
                        'farmerName': farmerName,
                        'farmerCode': farmerCode
                    },
                    success: function(data) {
                        $('#table').empty();
                        $('#table').html(data.view);
                        console.log(data);
                    }
                });
            });
            $("#submitShippingBtn").on('click', function() {
                $("#submitShippingBtn").hide();
            });
        });
    </script>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        @if (Session::has('message'))
            <div class="alert alert-success" role="alert">
                <b>{{ Session::get('message') }}</b>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif
        @if (session()->has('msg'))
            <div class="alert alert-success alert-dismissible" id="alert">
                {{ session()->get('msg') }}
            </div>
        @endif
        @if (session()->has('alert'))
            <div class="alert alert-success alert-dismissible" id="alert">
                {{ session()->get('alert') }}
            </div>
        @endif
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">

                    <div class="col-sm-6">
                        <h1>Shipping
                            <a href="" class="btn btn-add rounded-circle">
                            </a>
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Shipping</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>
        <!--     Main      content -->
        <section class="content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-12">
                        <!-- /.card -->


                        <div class="card">
                            <div class="row">
                                <div class="col-md-6"></div>
                                <div class="col-md-6"> <label for="search">Search: </label>
                                    <input class="mt-3 ml-1" type="text" value="" id="farmerName"
                                        placeholder="Enter Farmer Name"> <strong>or</strong>
                                    <input class="mt-3 ml-1" type="text" value="" id="farmerCode"
                                        placeholder="Enter Farmer Code">
                                    &nbsp;<i class="fa fa-search" id="search" aria-hidden="true"></i>
                                </div>

                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <form action="{{ url('admin/shipping') }}" method="POST">
                                    @csrf
                                    <table id="get_batch_number" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Batch Number</th>
                                                <th>Weight</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="table">
                                            @foreach ($transactions as $transaction)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $transaction->batch_number }}</td>
                                                    <td>{{ $transaction->details->sum('container_weight') }}</td>
                                                    <td align="center"> <input type="checkbox" name="bags[]"
                                                            value="{{ $transaction->transaction_id }}"> </td>
                                                </tr>
                                            @endforeach

                                        </tbody>
                                    </table>
                                    <div class="row">
                                        <div class="col-md-10"></div>
                                        <div class="col-md-2"> <input class="btn btn-success " id="submitShippingBtn"
                                                type="submit" value="Submit">
                                        </div>
                                    </div>

                                </form>
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
