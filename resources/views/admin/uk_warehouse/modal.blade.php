@extends('layouts.default')
@section('title', 'Transection Detail')
@section('content')

    <style type="text/css">
        .nav.nav-tabs {
            float: left;
            display: block;
            margin-right: 20px;
            border-bottom: 0;
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
            border-top-left-radius: 0rem !important;
            border-top-right-radius: 0rem !important;
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

        .batchnumber thead tr {
            border-bottom: 1px solid black;
        }

        .batchnumber tbody tr {
            border-bottom: 1px solid black;
        }

        .set-padding {
            padding: 10px;
        }

        .top-margin-set {
            margin-top: 10px;
        }

        .searchfloat {
            float: right;
        }

    </style>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1><b>Set Prices</b></h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Uk WareHose</li>
                            <li class="breadcrumb-item active">Set Prieces</li>
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

                            <form action="{{ url('admin/uk_warehouse/post_price/' . $transactionsWS->transaction_id) }}"
                                method="POST">
                                @csrf
                                <div class="card">
                                    <div class="card-header">
                                        <span><strong> {{ $transactionsWS->batch_number }}</strong></span>
                                        <input type="text" name="price" id="" class="searchfloat"
                                            placeholder="Enter Price Per KG">
                                    </div>
                                    <div class="card-body">
                                        <table width="100%" style="border: 1px solid black">
                                            @foreach ($transactionsWS->details as $detail)

                                                <tr style="border-bottom: 1px solid black;">
                                                    <td> {{ $detail->container_number }}</td>
                                                    <td align="center">
                                                        {{ $detail->container_weight }}
                                                    </td>
                                                </tr>

                                            @endforeach
                                        </table>
                                        <br>
                                        <input type="submit" class="btn btn-success" value="Set Price">
                                    </div>
                                </div>
                            </form>
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
    <script>
        $('#myModal').modal('show');
    </script>
@endsection
