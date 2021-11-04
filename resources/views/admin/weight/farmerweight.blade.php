@extends('layouts.default')
@section('title', 'All Farmers')
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
                <div class="row mb-2">

                    <div class="col-sm-6">
                        <h1>Farmer Weight
                            {{-- <a href="" class="btn btn-add rounded-circle"> 
                <i class="fas fa-user-plus add-client-icon"></i>
              </a> --}}
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">{{ $village->village_code }}</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">

                        <!-- /.card -->

                        <div class="card">
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        @php
                                            $row = $village->village_code;
                                            $totalweight = App\TransactionDetail::whereHas('transaction', function ($q) use ($row) {
                                                $q->where('sent_to', 2)
                                                    ->where('batch_number', 'NOT LIKE', '%000%')
                                                    ->Where('batch_number', 'LIKE', "$row-%");
                                            })->sum('container_weight');
                                        @endphp
                                        <tr>
                                            <th>Village Code</th>
                                            <th>Village Title</th>
                                            <th>Village Weight</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ $village->village_code }}</td>
                                            <td>{{ $village->village_title }}</td>
                                            <td>{{ $totalweight }}</td>
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
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">

                        <!-- /.card -->

                        <div class="card">
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="allfarmer" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>S#</th>
                                            <th>Farmer Code</th>
                                            <th>Farmer Name</th>
                                            <th>Village Code</th>
                                            <th>Farmer Nicn</th>
                                            <th>Farmer Weight</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($farmer as $row)
                                            @php
                                                $farmer = $row->farmer_code;
                                                $totalweight = App\TransactionDetail::whereHas('transaction', function ($q) use ($farmer) {
                                                    // $q->Where('batch_number', 'LIKE', "$farmer%");
                                                    $q->where('sent_to', 2)
                                                        ->where('batch_number', 'NOT LIKE', '%000%')
                                                        ->Where('batch_number', 'LIKE', "$farmer%");
                                                })->sum('container_weight');
                                            @endphp
                                            <tr>
                                                <td>{{ $row->farmer_id }}</td>
                                                <td>{{ $row->farmer_code }}</td>
                                                <td>{{ $row->farmer_name }}</td>
                                                <td>{{ $row->village_code }}</td>
                                                <td>{{ $row->farmer_nicn }}</td>
                                                <td>{{ $totalweight }} kg</td>
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
    <!-- /.content-wrapper -->
    <script type="text/javascript">
        $(document).ready(function() {
            $('#allfarmer').DataTable();
        });
    </script>
@endsection
