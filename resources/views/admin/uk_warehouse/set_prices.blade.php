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
            /* margin-left: 45%; */
        }
        .card-header{
            display: flex;
    justify-content: space-between;  
        }
        .card-header::after{
            display: none;
        }

    </style>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <div class="mx-lg-5">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            @if (session()->has('msg'))
                <div class="alert alert-success alert-dismissible" id="alert">
                    {{ session()->get('msg') }}
                </div>
            @endif
            <div class="container-fluid">
                <div class="row mb-2">

                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark text-uppercase font-weight-lighter text-heading"> Coffee Prices</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">UK WareHouse</li>
                            <li class="breadcrumb-item active">Set Prices</li>
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
                            <form action="{{ url('admin/uk_warehouse/assignToChaina') }}" method="POST">
                                @csrf
                                <div class="card card-info">
                                    <div class="row">
                                        @foreach ($transactionWOS as $transaction)
                                            <div class="col-md-6">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <span><strong> {{ $transaction->batch_number }}</strong></span>

                                                        @if (count($transaction->meta) > 0)


                                                            @foreach ($transaction->meta as $meta)
                                                                @if (isset($meta))
                                                                    <span class="searchfloat"><strong>
                                                                            Price : {{ $meta->value }}</strong>
                                                                            <span class="ml-2"> <input type="checkbox"
                                                                                value="{{ $transaction->transaction_id }}"
                                                                                name="transaction[]" id=""> </span>
                                                                    </span>
                                                                    {{-- @else --}}
                                                                @endif

                                                            @endforeach
                                                        @else
                                                            <span class="searchfloat"><strong>Price Not
                                                                    Assigned</strong>
                                                                    <span class="ml-2"> <input type="checkbox"
                                                                        value="{{ $transaction->transaction_id }}"
                                                                        name="transaction[]" id=""> </span>
                                                                    </span>

                                                        @endif
                                                       
                                                    </div>
                                                    <div class="card-body">
                                                        <table width="100%" style="border: 1px solid black">
                                                            @foreach ($transaction->details as $detail)

                                                                <tr style="border-bottom: 1px solid black;">
                                                                    <div calss="row">
                                                                        <div class="col-md-6">
                                                                            <td> {{ $detail->container_number }}</td>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <td align="center">
                                                                                {{ $detail->container_weight }}
                                                                            </td>
                                                                        </div>
                                                                    </div>
                                                                </tr>

                                                            @endforeach
                                                        </table>
                                                        <br>
                                                        <a href="{{ url('admin/uk_warehouse/set_price/' . $transaction->transaction_id) }}"
                                                            class="btn btn-primary">Set Price</a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        <div class="col-md-6">
                                            @foreach ($transactionsWS as $transaction)
                                                <div class="card">
                                                    <div class="card-header">
                                                        <span><strong> {{ $transaction->batch_number }}</strong></span>

                                                        @if (count($transaction->meta) > 0)


                                                            @foreach ($transaction->meta as $meta)
                                                                @if (isset($meta))
                                                                    <span class="searchfloat"><strong>
                                                                            Price : {{ $meta->value }}</strong>
                                                                            <span class="ml-2"> <input type="checkbox"
                                                                                value="{{ $transaction->transaction_id }}"
                                                                                name="transaction[]" id=""> </span>
                                                                    </span>
                                                                    {{-- @else --}}
                                                                @endif

                                                            @endforeach
                                                        @else
                                                            <span class="searchfloat"><strong>Price Not
                                                                    Assigned</strong>
                                                                    <span class="ml-2"> <input type="checkbox"
                                                                        value="{{ $transaction->transaction_id }}"
                                                                        name="transaction[]" id=""> </span>
                                                            </span>

                                                        @endif
                                                        
                                                    </div>
                                                    <div class="card-body">
                                                        <table width="100%" style="border: 1px solid black">
                                                            @foreach ($transaction->details as $detail)

                                                                <tr style="border-bottom: 1px solid black;" >
                                                                    <div calss="row">
                                                                        <div class="col-md-6">
                                                                            <td> {{ $detail->container_number }}</td>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <td align="center">
                                                                                {{ $detail->container_weight }}
                                                                            </td>
                                                                        </div>
                                                                    </div>
                                                                </tr>

                                                            @endforeach
                                                        </table>
                                                        <br>
                                                        <a href="{{ url('admin/uk_warehouse/set_price/' . $transaction->transaction_id) }}"
                                                            class="btn btn-primary">Set Price</a>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <input type="submit" class="btn bg-green-btn" value="Allocate To China ">
                            </form>
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
    <script>
        $('#myModal').modal('show');
    </script>
@endsection
