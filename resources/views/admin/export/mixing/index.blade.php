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

    </style>
    <script>
        $(document).ready(function() {
            $('#myTable').DataTable();
        });

    </script>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1><b>Mixing Coffee</b></h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Packaging And Export | Mixing</li>
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
                            <div class="col-md-6">
                                @if ($errors->any())
                                    <div class="alert alert-danger">

                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach


                                @endif

                            </div>

                        </div>
                        <form action="{{ route('mixing.store') }}" method="POST">
                            @csrf
                            <label for="batch_number">Batch Number:</label>
                            <input type="text" id="batch_number" name="batch_number" placeholder="Enter Btach Number" required>
                            <table id="myTable" style="border: 1px solid black">
                                <thead>
                                    <tr style="border: 1px solid black">
                                        <th>Sr#</th>


                                        <th>Batch Number</th>
                                        <th>Container_Number</th>
                                        <th>Container_Weight</th>
                                        <th>Select Batch_Number</th>


                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($transactions as $transaction)
                                        <tr style="border: 1px solid black">
                                            <td style="border: 1px solid black">{{ $loop->iteration }}</td>

                                            <td style="border: 1px solid black">{{ $transaction->batch_number }}</td>


                                            <td style="border: 1px solid black">
                                                @foreach ($transaction->details as $detail)

                                                    {{ $detail->container_number }} <br>



                                                @endforeach
                                            </td>
                                            <td style="border: 1px solid black">
                                                @foreach ($transaction->details as $detail)

                                                    {{ $detail->container_weight }} <br>



                                                @endforeach
                                            </td>
                                            <td style="border: 1px solid black">

                                                <input type="checkbox" name="transactions[]" value="{{$transaction->transaction_id}}">
                                            </td>
                                        </tr>
                                    @endforeach


                                </tbody>
                            </table>
                            <div class="row mt-2 mb-2">
                                <div class="col-md-5"></div>
                                <div class="col-md-2">
                                    <input type="submit" value="Mix" class="btn btn-primary px-4">
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

@endsection
