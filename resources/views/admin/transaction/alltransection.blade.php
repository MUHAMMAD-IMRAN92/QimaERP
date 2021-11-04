@extends('layouts.default')
@section('title', 'All Transection')
@section('content')
<style type="text/css">
    .dataTables_wrapper .dataTables_filter input {
        margin-left: 0.5em;
        width: 240px;
    }
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <div class="mx-lg-5">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-8 pl-0">
                        <h1 class="m-0 text-dark text-uppercase font-weight-lighter text-heading">
                            Transactions
                            <a href="" class="btn btn-add rounded-circle">
                                {{-- <i class="fas fa-user-plus add-client-icon"></i> --}}
                            </a>
                        </h1>
                    </div>
                    <div class="col-sm-4">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">All Transactions</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <hr class="ml-md-2">
        <div class="row ml-2 text-uppercase mb-2">
            <strong>
                <b>Date Filter</b>
            </strong>
        </div>
        <div class="row ml-2">
            <form action="" method="POST" id="data-form">
                <label class="text-uppercase font-weight-normal mr-2 mb-0 letter-spacing-1" for="from">From</label>
                <input class="mr-3" type="date" name="" id="from">
                <label class="text-uppercase font-weight-normal mr-2 mb-0 letter-spacing-1" for="To">To</label>
                <input class="mr-3" type="date" name="" id="to">
            </form>
        </div>
        <br>
        <div class="row ml-2 blacklink letter-spacing-1">
            <span class="hover" id="today"> TODAY</span> &nbsp |
            <span class="ml-md-2 hover" id="yesterday"> YESTERDAY</span>
            &nbsp |
            <span class="ml-md-2 hover" id="weekToDate"> WEEK TO DATE
                </a></span>
            &nbsp |
            <span class="ml-md-2 hover" id="monthToDate"> MONTH
                TO
                DATE</a></span>
            &nbsp |
            <span class="ml-md-2 hover" id="lastmonth"> LAST
                MONTH</a></span>
            &nbsp |
            <span class="ml-2 hover" id="yearToDate"> YEAR TO
                DATE</a></span>
            &nbsp |
            <span class="ml-md-2 hover" id="currentyear"> 2021
                SEASON</a></span>
            &nbsp |
            <span class="ml-md-2 hover" id="lastyear"> 2020
                SEASON</a></span>
            &nbsp |
            <span class="ml-md-2" style="font-weight: bold; text-decoration: underline;"> <a href="{{ url('/admin/allregion') }}">ALL
                    TIME</a></span>
        </div>
        <hr class="ml-md-2">
        <div class="row ml-2 text-uppercase mb-2">
            <strong>
                <b>COFFEE PURCHASE TRANSACTIONS</b>
            </strong>
        </div>
        <p class="ml-2 letter-spacing-1 btn-color-darkRed">PENALTY  /  DATE  /  TIME /  BUYER  /  REGION  /  QUANTITY  /  VALUE - NOT-DEDUCTED</p>
        <hr class="ml-md-2">
        <div class="row ml-2 text-uppercase mb-2">
            <strong>
                <b>LOANS</b>
            </strong>
        </div>
        <p class="ml-2 letter-spacing-1 btn-color-darkRed">PENALTY  /  DATE  /  TIME /  BUYER  /  REGION  /  QUANTITY  /  VALUE - NOT-DEDUCTED</p>
        <hr class="ml-md-2">
        <div class="row ml-2 text-uppercase mb-2">
            <strong>
                <b>REWARDS</b>
            </strong>
        </div>
        <p class="ml-2 letter-spacing-1 btn-color-darkRed">PENALTY  /  DATE  /  TIME /  BUYER  /  REGION  /  QUANTITY  /  VALUE - NOT-DEDUCTED</p>
        <hr class="ml-md-2">
        <div class="row ml-2 text-uppercase mb-2">
            <strong>
                <b>BUYER PENALTIES & COMISSION</b>
            </strong>
        </div>
        <p class="ml-2 letter-spacing-1 btn-color-darkRed">PENALTY  /  DATE  /  TIME /  BUYER  /  REGION  /  QUANTITY  /  VALUE - NOT-DEDUCTED</p>
        <hr class="ml-md-2">
        <div class="row ml-2 text-uppercase mb-2">
            <strong>
                <b>LOCAL SALES</b>
            </strong>
        </div>
        <p class="ml-2 letter-spacing-1 btn-color-darkRed">PENALTY  /  DATE  /  TIME /  BUYER  /  REGION  /  QUANTITY  /  VALUE - NOT-DEDUCTED</p>
        <hr class="ml-md-2">
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">

                        <!-- /.card -->

                        <div class="card">
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="myTable" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>S#</th>
                                            <th>Code</th>
                                            <th>Status</th>
                                            <th>Action</th>

                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($transaction as $row)
                                        <tr>
                                            <td>{{ $row->transaction_id }}</td>
                                            <td>{{ $row->batch_number }}</td>
                                            <td>{{ ucfirst($row->transaction_status) }}</td>
                                            <td><a href="transactiondetail/{{ $row->transaction_id }}" class="btn btn-info btn-sm"><i class="fa fa-info-circle"></i></a>
                                                <a href="rawTransactions/{{ $row->transaction_id }}" class="btn btn-info btn-sm"><i class="fa fa-database" aria-hidden="true"></i></a>
                                            </td>
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
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#myTable').DataTable();
    });
</script>
@endsection
