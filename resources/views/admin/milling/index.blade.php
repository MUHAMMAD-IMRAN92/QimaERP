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
    .batchnumber thead tr{
        border-bottom: 1px solid black;
    }
    .batchnumber tbody tr{
        border-bottom: 1px solid black;
    }
    .set-padding{
        padding: 10px;
    }
    .top-margin-set{
        margin-top: 10px;
    }
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><b>Milling Coffee</b></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Milling Coffee</li>
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

                        <?php foreach ($transactions as $key => $transaction) { ?>
                            <div class="col-md-6">
                                <div class="card card-info">
                                    <div class="card-header">
                                        <h3 class="card-title">Session Number: {{$transaction[0]['transaction']->session_no}} </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">

                                            <?php
                                            foreach ($transaction as $key => $trans) {
                                                $batchNumber = $trans['transaction']->batch_number;
                                                ?>
                                                <div class="col-md-12">
                                                    <div class="card set-padding">
                                                        <div class="row">
                                                            <div class="col-md-12 top-margin-set">
                                                                <h4 class="card-title">Batch Number: {{$batchNumber}} <input type="checkbox" value=""></h4>
                                                            </div> 
                                                            <div class="col-md-12">
                                                                <table class="batchnumber">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Farmer Code</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php
                                                                        $removeLocalId = explode("-", $batchNumber);
                                                                        if ($removeLocalId[3] != '000') {
                                                                            array_pop($removeLocalId);
                                                                            $farmerCode = implode("-", $removeLocalId);
                                                                            ?>
                                                                            <tr>
                                                                                <td>{{$farmerCode}}</td>
                                                                            </tr>
                                                                            <?php
                                                                        } else {
                                                                            $childTransactions = $trans['child_transactions'];
                                                                            foreach ($childTransactions as $key => $childTransaction) {
                                                                                $removeLastIndex = explode("-", $childTransaction->batch_number);
                                                                                array_pop($removeLastIndex);
                                                                                $farmerCode = implode("-", $removeLastIndex);
                                                                                ?>
                                                                                <tr>
                                                                                    <td>{{$farmerCode}}</td>
                                                                                </tr>
                                                                                <?php
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>


                                        </div>
                                    </div>
                                    <!-- /.card-header -->
                                    <!-- form start -->
    <!--                                        <table class="table table-bordered">
                                        <thead>                  
                                            <tr>
                                                <th style="width: 10px">#</th>
                                                <th>Farmer Code</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                           
                                        </tbody>
                                    </table>-->
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

@endsection