@extends('layouts.default')
@section('content')
    <style type="text/css">
        .dataTables_wrapper .dataTables_filter input {
            margin-left: 0.5em;
            width: 240px;
        }

        table td,
        th {
            border: 1px solid black;

        }

        table {
            width: 100%;
        }

    </style>


    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Transactions Information
                            <a href="" class="btn btn-add rounded-circle">
                                {{-- <i class="fas fa-user-plus add-client-icon"></i> --}}
                            </a>
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">All Transactions</li>
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
                                <table style="border: 1px solid black">
                                    <thead style="border: 1px solid black; text-align:center">
                                        <tr>
                                            <th>Batch Number </th>
                                            <th>Baskets </th>
                                            <th>Created By </th>
                                        </tr>
                                    </thead>
                                    <tbody style="border: 1px solid black">
                                        @if (isset($transactionChild))
                                            @foreach ($transactionChild as $transaction)
                                                <tr>
                                                    <td>{{ $transaction->batch_number }} </td>
                                                    <td>
                                                        @foreach ($transaction->details as $detail)
                                                            {{ $detail->container_number }} :
                                                            {{ $detail->container_weight }}
                                                            <br>
                                                        @endforeach
                                                    </td>
                                                    <td>
                                                        {{ App\User::find($transaction->created_by)->first_name . App\User::find($transaction->created_by)->last_name }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        @if (isset($allTransactions))
                                            @foreach ($allTransactions->reverse() as $transaction)
                                                <tr>
                                                    <td>{{ $transaction->batch_number }} </td>
                                                    <td>
                                                        @foreach ($transaction->details as $detail)
                                                            {{ $detail->container_number }} :
                                                            {{ $detail->container_weight }}
                                                            <br>
                                                        @endforeach
                                                    </td>
                                                    <td>
                                                        {{ App\User::find($transaction->created_by)->first_name . App\User::find($transaction->created_by)->last_name }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        @if (isset($transactionparentId))
                                            @foreach ($transactionparentId->reverse() as $transaction)
                                                <tr>
                                                    <td>{{ $transaction->batch_number }} </td>
                                                    <td>
                                                        @foreach ($transaction->details as $detail)
                                                            {{ $detail->container_number }} :
                                                            {{ $detail->container_weight }}
                                                            <br>
                                                        @endforeach
                                                    </td>
                                                    <td>
                                                        {{ App\User::find($transaction->created_by)->first_name . App\User::find($transaction->created_by)->last_name }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif


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
@endsection
