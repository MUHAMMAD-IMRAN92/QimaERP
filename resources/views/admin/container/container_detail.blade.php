@extends('layouts.default')
@section('title', 'Transection Detail')
@section('content')


    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Transactions Detail
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Detail</li>
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
                                <table id="transactiionall" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                       

                                            <th>Container Number</th>

                                            <th>Container Capacity</th>
                                        </tr>
                                    </thead>
                                    <tbody>


                                        <tr>

                                            <td>{{ $container->container_number }}</td>

                                            <td>{{ $container->capacity }} </td>
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
        <!-- /.content -->
    </div>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#transactionchild').DataTable();
        });
        $(document).ready(function() {
            $('#transactiionall').DataTable();
        });

    </script>
@endsection
