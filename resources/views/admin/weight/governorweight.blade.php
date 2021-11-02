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
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Governor Weight Information
                            {{-- <a href="{{url('')}}" class="btn btn-add rounded-circle"> 
                <i class="fas fa-user-plus add-client-icon"></i>
              </a> --}}
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Governor Weight </li>
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
                                <table id="centers" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>S#</th>
                                            <th>Code</th>
                                            <th>Title</th>
                                            <th>Total Weight</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($governor as $row)
                                            @php
                                                $gov = $row->governerate_code;
                                                $totalweight = App\TransactionDetail::whereHas('transaction', function ($q) use ($gov) {
                                                    $q->where('is_parent', 0)
                                                        ->where('sent_to', 2)
                                                        ->where('batch_number', 'NOT LIKE', '%000%')
                                                        ->Where('batch_number', 'LIKE', "$gov%");
                                                })->sum('container_weight');
                                            @endphp
                                            <tr>
                                                <td>{{ $row->governerate_id }} </td>
                                                <td><a
                                                        href="{{ URL::to('') }}/admin/governorweightcode/{{ $row->governerate_id }}">{{ $row->governerate_code }}</a>
                                                </td>
                                                <td>{{ $row->governerate_title }} </td>
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
            $('#centers').DataTable();
        });
    </script>
@endsection
