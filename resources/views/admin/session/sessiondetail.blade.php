@extends('layouts.default')
@section('title', 'Transection Detail')
@section('content')

<style type="text/css">
    .nav.nav-tabs {
        float: left;
        display: block;
        margin-right: 20px;
        border-bottom:0;
        border-right: 1px solid #ddd;
        padding-right: 15px;
    }
    .nav-tabs .nav-link {
        border: 1px solid transparent;
        border-top-left-radius: .25rem;
        border-top-right-radius: .25rem;
        background: #ccc;
    }

    .nav-tabs .nav-link.active {
        color: #495057;

        border-color: transparent !important;
    }
    .nav-tabs .nav-link {
        border: 1px solid transparent;
        border-top-left-radius: 0rem!important;
        border-top-right-radius: 0rem!important;
    }
    .tab-content>.active {
        display: block;
        /*background: #007bff;*/
        min-height: 165px;
    }
    .nav.nav-tabs {
        float: left;
        display: block;
        margin-right: 20px;
        border-bottom: 0;
        border-right: 1px solid transparent;
        padding-right: 15px;
    }
    #custom_tab li.nav-item a {
        color: #000;
        margin-bottom: 0px; 
    }
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><b>Session Number</b>:{{$session_no}}</h1>
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
                <div class="card col-lg-12">
                    <!-- /.card-header -->
                    <div class="card-body">
                     
                            <?php foreach ($transactions as $key => $transaction) {
 
                                ?>
                                <div class="col-md-6">
                                    <div class="card card-info">
                                        <div class="card-header">
                                            <h3 class="card-title">Batch Number {{$transaction[0]->batch_number}}</h3>
                                        </div>
                                        <!-- /.card-header -->
                                        <!-- form start -->
                                        <table class="table table-bordered">
                                            <thead>                  
                                                <tr>
                                                    <th style="width: 10px">#</th>
                                                    <th>Container Number</th>
                                                    <th>Weight</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $i=1;
                                                foreach ($transaction as $key => $transact) {


                                                    if (isset($transact->transactionDetail)) {
                                                        foreach ($transact->transactionDetail as $key => $transactionDetail) {
                                                            ?>
                                                            <tr>
                                                                <td>{{$i}}.</td>
                                                                <td>{{$transactionDetail->container_number}}</td>
                                                                <td>{{$transactionDetail->container_weight.' KG'}}</td>
                                                            </tr>
                                                            <?php
                                                            $i++;
                                                        }
                                                    }
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            <?php } ?>
                       
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