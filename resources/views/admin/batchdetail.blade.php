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
                    <h1><b>Batch Number</b>: {{$batch->batch_number}}</h1>
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
                <div class="card">
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="col-12">
                            <div class="bs-example">
                                <ul class="nav nav-tabs" id="custom_tab">
                                    <li class="nav-item">
                                        <a href="#coffee_buyer" class="nav-link active" data-toggle="tab">Coffee Buyer</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#coffee_buyer_manager" class="nav-link" data-toggle="tab">Coffee Buyer Manager</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#center_manager" class="nav-link" data-toggle="tab">Center Manager</a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="coffee_buyer">

                                        <?php
                                        foreach ($batch->transaction as $key => $trans) {
                                            if ($trans->is_mixed == 1) {
                                                ?>
                                                <div class="card">
                                                    <!-- /.card-header -->
                                                    <div class="card-body">
                                                        <?php foreach ($trans->childTransation as $key => $child_transation) {
                                                            ?>

                                                            <table class="table table-bordered table-striped">
                                                                <tbody>
                                                                <p><b>Batch Number</b>: {{$child_transation->batch_number}}</p>

                                                                <tr>
                                                                    <th>Container</th>
                                                                    <th>Weight</th>
                                                                </tr>

                                                                </tbody>


                                                                @foreach($child_transation->transactionDetail as $child)

                                                                <tr>

                                                                    <td>
                                                                        {{$child->container_number}}
                                                                    </td>
                                                                    <td>
                                                                        {{$child->weight}} kg
                                                                    </td>


                                                                </tr>
                                                                @endforeach
                                                            </table>


                                                        <?php }
                                                        ?>
                                                    </div>
                                                </div>
                                            <?php } else {
                                                ?> 
                                                <div class="card">
                                                    <!-- /.card-header -->
                                                    <div class="card-body">

                                                        <table class="table table-bordered table-striped">
                                                            <tbody>
                                                            <p><b>Batch Number</b>: {{$trans->batch_number}}</p>

                                                            <tr>
                                                                <th>Container</th>
                                                                <th>Weight</th>
                                                            </tr>

                                                            </tbody>


                                                            @foreach($trans->transactionDetail as $child)

                                                            <tr>

                                                                <td>
                                                                    {{$child->container_number}}
                                                                </td>
                                                                <td>
                                                                    {{$child->weight}} kg
                                                                </td>


                                                            </tr>
                                                            @endforeach
                                                        </table>
                                                    </div> 
                                                </div>
                                                <?php
                                            }
                                        }
                                        ?>

                                    </div>
                                    <div class="tab-pane fade" id="coffee_buyer_manager">
                                        <?php foreach ($batch->sent_transaction as $key => $trans) { ?>
                                            <div class="card">
                                                <!-- /.card-header -->
                                                <div class="card-body">

                                                    <table class="table table-bordered table-striped">
                                                        <tbody>
                                                        <p><b>Batch Number</b>: {{$trans->batch_number}}</p>

                                                        <tr>
                                                            <th>Container</th>
                                                            <th>Weight</th>
                                                        </tr>

                                                        </tbody>


                                                        @foreach($trans->transactionDetail as $child)

                                                        <tr>

                                                            <td>
                                                                {{$child->container_number}}
                                                            </td>
                                                            <td>
                                                                {{$child->weight}} kg
                                                            </td>


                                                        </tr>
                                                        @endforeach
                                                    </table>
                                                </div> 
                                            </div>
                                        <?php }
                                        ?>

                                    </div>
                                    <div class="tab-pane fade" id="center_manager">
                                        <?php foreach ($batch->center_manager_received_transaction as $key => $trans) { ?>
                                            <div class="card">
                                                <!-- /.card-header -->
                                                <div class="card-body">

                                                    <table class="table table-bordered table-striped">
                                                        <tbody>
                                                        <p><b>Batch Number</b>: {{$trans->batch_number}}</p>

                                                        <tr>
                                                            <th>Container</th>
                                                            <th>Weight</th>
                                                        </tr>

                                                        </tbody>


                                                        @foreach($trans->transactionDetail as $child)

                                                        <tr>

                                                            <td>
                                                                {{$child->container_number}}
                                                            </td>
                                                            <td>
                                                                {{$child->weight}} kg
                                                            </td>


                                                        </tr>
                                                        @endforeach
                                                    </table>
                                                </div> 
                                            </div>
                                        <?php }
                                        ?>
                                    </div>
                                </div>
                            </div>





                        </div>

                    </div>
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
    $(document).ready(function () {
        $('#transactionchild').DataTable();
    });
    $(document).ready(function () {
        $('#transactiionall').DataTable();
    });
</script>
@endsection