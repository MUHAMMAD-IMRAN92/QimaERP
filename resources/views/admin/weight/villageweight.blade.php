@extends('layouts.default')
@section('title', 'All Villages')
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
                        <h1> Village Weight
                            {{-- <a href="{{URL::to('')}}/admin/addnewvillage" class="btn btn-add rounded-circle"> 
                <i class="fas fa-user-plus add-client-icon"></i>
              </a> --}}
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">{{ $region->region_code }}</li>
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
                                            $row = $region->region_code;
                                            $totalweight = App\TransactionDetail::whereHas('transaction', function ($q) use ($row) {
                                                $q->where('sent_to', 2)
                                                    ->where('batch_number', 'NOT LIKE', '%000%')
                                                    ->Where('batch_number', 'LIKE', "$row%");
                                            })->sum('container_weight');
                                        @endphp
                                        <tr>
                                            <th>Region Code</th>
                                            <th>Region Title</th>
                                            <th>Region Weight</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ $region->region_code }} </td>
                                            <td>{{ $region->region_title }} </td>
                                            <td> {{ $totalweight }} </td>
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
                                <table id="village" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>S#</th>
                                            <th>Village Code</th>
                                            <th>Village Title</th>
                                            <th>Total Weight</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($village as $row)
                                            @php
                                                $village = $row->village_code;
                                                $totalweight = App\TransactionDetail::whereHas('transaction', function ($q) use ($village) {
                                                    $q->where('sent_to', 2)
                                                        ->where('batch_number', 'NOT LIKE', '%000%')
                                                        ->Where('batch_number', 'LIKE', "$village%");
                                                })->sum('container_weight');
                                            @endphp
                                            <tr>
                                                <td>{{ $row->village_id }}</td>
                                                <td><a
                                                        href="{{ URL::to('') }}/admin/villageweightcode/{{ $row->village_id }}">{{ $row->village_code }}</a>
                                                </td>
                                                <td>{{ $row->village_title }}</td>
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
            $('#village').DataTable();
        });
    </script>
@endsection
