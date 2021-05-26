@extends('layouts.default')
@section('title', 'All Farmers')
@section('content')

    <style type="text/css">
        .dataTables_wrapper .dataTables_filter input {
            margin-left: 0.5em;
            width: 240px;
        }

    </style>
    <script>
        $(document).ready(function() {
          $("search").on('click' , function(){
            
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
                                <div class="col-md-8"></div>
                                <div class="col-md-4 "> <label for="search">Search: </label>
                                    <input class="mt-3 ml-1" type="" value="" id="search_text" placeholder="search by farmer">
                                    &nbsp;<i class="fa fa-search"  id="search" aria-hidden="true"></i>
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
                                        <tbody>
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
                                        <div class="col-md-2"> <input class="btn btn-success " type="submit" value="Submit">
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
