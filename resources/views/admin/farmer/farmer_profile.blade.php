@extends('layouts.default')
@section('title', 'All Farmers')
@section('content')

    <style type="text/css">
        .dataTables_wrapper .dataTables_filter input {
            margin-left: 0.5em;
            width: 240px;
        }

        .color {

            width: 200px fit-content;
            height: 80px fit-content;
            margin-left: 4px;
        }

        .blacklink a {
            color: black;
        }

        .anchor a {

            color: rgb(204, 38, 38);
            background-color: transparent;
            text-decoration: none;
            font-size: 15px;

        }

        .margin-left {
            margin-left: 306PX;
        }

        #farmerTable {
            border-collapse: separate;
            border-spacing: 3px;
        }

        #farmerTable tr td {
            padding: unset !important;
        }

        .txt-size {
            font-size: 12px;
        }

        .blacklink .hover:hover {
            cursor: pointer;
        }

        #reciepts,
        #iddocuments {
            cursor: pointer;
        }

    </style>
    <script>
        $(document).ready(function() {
            $('#myTable').DataTable();
            $('#to').on('change', function() {
                let from = $('#from').val();
                let to = $('#to').val();


                $.ajax({
                    url: "{{ url('admin/filter_farmer_profile/' . $farmer->farmer_id) }}",
                    type: "GET",
                    data: {
                        'from': from,
                        'to': to
                    },
                    success: function(data) {

                        $('#transaction').html(data);
                        console.log(data);
                    }
                });
            });
            $('#today').on('click', function() {

                $.ajax({
                    url: "{{ url('admin/farmer_by_date_profile/' . $farmer->farmer_id) }}",
                    type: "GET",
                    data: {
                        'date': 'today'
                    },
                    success: function(data) {

                        $('#transaction').html(data);
                        console.log(data);
                    }
                });
            });
            $('#yesterday').on('click', function() {

                $.ajax({
                    url: "{{ url('admin/farmer_by_date_profile/' . $farmer->farmer_id) }}",
                    type: "GET",
                    data: {
                        'date': 'yesterday'
                    },
                    success: function(data) {

                        $('#transaction').html(data);
                        console.log(data);
                    }
                });
            });
            $('#weekToDate').on('click', function() {

                $.ajax({
                    url: "{{ url('admin/farmer_by_date_profile/' . $farmer->farmer_id) }}",
                    type: "GET",
                    data: {
                        'date': 'weekToDate'
                    },
                    success: function(data) {

                        $('#transaction').html(data);
                        console.log(data);
                    }
                });
            });
            $('#monthToDate').on('click', function() {

                $.ajax({
                    url: "{{ url('admin/farmer_by_date_profile/' . $farmer->farmer_id) }}",
                    type: "GET",
                    data: {
                        'date': 'monthToDate'
                    },
                    success: function(data) {

                        $('#transaction').html(data);
                        console.log(data);
                    }
                });
            });
            $('#lastmonth').on('click', function() {

                $.ajax({
                    url: "{{ url('admin/farmer_by_date_profile/' . $farmer->farmer_id) }}",
                    type: "GET",
                    data: {
                        'date': 'lastmonth'
                    },
                    success: function(data) {

                        $('#transaction').html(data);
                        console.log(data);
                    }
                });
            });
            $('#yearToDate').on('click', function() {

                $.ajax({
                    url: "{{ url('admin/farmer_by_date_profile/' . $farmer->farmer_id) }}",
                    type: "GET",
                    data: {
                        'date': 'yearToDate'
                    },
                    success: function(data) {

                        $('#transaction').html(data);
                        console.log(data);
                    }
                });
            });
            $('#currentyear').on('click', function() {

                $.ajax({
                    url: "{{ url('admin/farmer_by_date_profile/' . $farmer->farmer_id) }}",
                    type: "GET",
                    data: {
                        'date': 'currentyear'
                    },
                    success: function(data) {

                        $('#transaction').html(data);
                        console.log(data);
                    }
                });
            });
            $('#lastyear').on('click', function() {

                $.ajax({
                    url: "{{ url('admin/farmer_by_date_profile/' . $farmer->farmer_id) }}",
                    type: "GET",
                    data: {
                        'date': 'lastyear'
                    },
                    success: function(data) {

                        $('#transaction').html(data);
                        console.log(data);
                    }
                });
            });
            $('#reciepts').on('click', function() {
                $.ajax({
                    url: "{{ url('admin/farmer_invoice/invoices/' . $farmer->farmer_id) }}",
                    type: "GET",
                    success: function(data) {
                        $('#farmer-inovices').html(data);
                        console.log(data);
                    }
                });
            });
            $('#iddocuments').on('click', function() {
                $.ajax({
                    url: "{{ url('admin/farmer_id_documents/' . $farmer->farmer_id) }}",
                    type: "GET",
                    success: function(data) {
                        $('#farmer-inovices').html(data);
                        console.log(data);
                    }
                });
            });
        });
    </script>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">

        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">

                    <div class="col-sm-6">
                        <h1>Farmer Profile

                        </h1>
                    </div>

                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active"> Farmer / Profile</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->



            <div class="row  margin-left anchor">
                <span class="ml-2"> <a href="{{ url('admin/editfarmer/' . $farmer->farmer_id) }}">EDIT
                        INFORMATION</a></span> &nbsp |
                <span class="ml-2"> <a href="#"> ADD CROPSTER REPORT</a></span> &nbsp |
                <span class="ml-2"> <a href="{{ url('admin/editfarmer/' . $farmer->farmer_id) }}"> OVERRIDE
                        PRICE
                    </a></span> &nbsp |
                <span class="ml-2"> <a href="#">OVERRIDE REWARD</a></span> &nbsp |
                <span class="ml-2"> <a href="#">ADD PREMIUM</a></span>&nbsp |
                <span class="ml-2"> <a href="#">SETTLE LOAN</a></span>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-4">
                    @if ($farmer->picture_id == null)
                        <td> <img class="famerimg" src="{{ Storage::disk('s3')->url('images/' . 'dumy.png') }}"
                                style="width: 300px ; height:300px; border-radius:50%; border: 1px solid gray;" alt=""></td>
                    @else
                        <td> <img class="famerimg"
                                style="width: 300px ; height:300px; border-radius:50%; border: 1px solid gray;"
                                src="{{ Storage::disk('s3')->url('images/' . $farmer->image) }}" {{-- src="https://qima.s3.us-east-2.amazonaws.com/images/1631859778.JPG" --}}
                                alt=" no img"></td>
                    @endif

                </div>
                <div class="col-md-8">
                    <table class="table table-borderless" id="farmerTable">

                        <tbody>
                            <tr>
                                <td colspan=""> <strong>Name</strong> </td>
                                <td colspan="4">{{ $farmer->farmer_name }}</td>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td colspan=""><strong>IDENTIFICATION NUMBER</strong></td>
                                <td colspan="4">{{ $farmer->farmer_nicn }}</td>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td colspan=""><strong>CODE </strong></td>
                                <td colspan="4">{{ $farmer->farmer_code }}</td>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td colspan=""><strong>GOVERNORATE</strong></td>
                                <td colspan="4">{{ $farmer->governerate_title }}</td>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td colspan=""><strong>REGION</strong></td>
                                <td colspan="4">{{ $farmer->region_title }}</td>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td colspan=""><strong>VILLAGE</strong></td>
                                <td colspan="4">{{ $farmer->village_title }}</td>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td colspan=""><strong>ALTITUDE</strong></td>
                                <td colspan="4">pending</td>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td colspan=""><strong>HOUSE HOLD SIZE</strong></td>
                                <td colspan="4">pending</td>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td colspan=""><strong>FARM SIZE</strong></td>
                                <td colspan="4">pending</td>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td colspan=""><strong>NO OF TREES</strong></td>
                                <td colspan="4">pending</td>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td colspan=""><strong>STARTED WORKING WITH QIMA</strong></td>
                                <td colspan="4">{{ $farmer->created_at->toDateString() }}</td>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td colspan=""><strong>FAMER INFORMATION</strong></td>
                                <td colspan="4">pending</td>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td colspan=""><strong>CUPPING SCORE</strong></td>
                                <td colspan="4">pending</td>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td colspan=""><strong>CUP PROFILE</strong></td>
                                <td colspan="4">pending</td>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td colspan=""><strong>TEL</strong>
                                </td>
                                <td colspan="8">pending</td>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td colspan=""><strong>PRICE PER KG</strong>
                                </td>

                                @if ($farmer->price_per_kg != null)
                                    <td colspan="4">{{ number_format($farmer->price_per_kg) }}
                                    </td>
                                @else
                                    <td colspan="4">{{ number_format($farmer->price) }}</td>
                                @endif

                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td colspan=""><strong>REWARD PER KG</strong></td>
                                <td colspan="4">pending</td>
                                <td colspan="4"></td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
            <hr>

            <div class="row ml-2">
                <strong>
                    <b>Date Filter</b>
                </strong>
            </div>
            <div class="row ml-2">
                <form action="" method="POST" id="data-form">
                    <label for="from">From</label>
                    <input type="date" name="" id="from">
                    <label for="To">To</label>
                    <input type="date" name="" id="to">
                </form>
            </div>
            <div class="row ml-2 blacklink ">
                <span class="ml-2 hover" id="today"> TODAY</span> &nbsp |
                <span class="ml-2 hover" id="yesterday"> YESTERDAY</span>
                &nbsp |
                <span class="ml-2 hover" id="weekToDate"> WEEK TO DATE
                    </a></span>
                &nbsp |
                <span class="ml-2 hover" id="monthToDate"> MONTH
                    TO
                    DATE</a></span>
                &nbsp |
                <span class="ml-2 hover" id="lastmonth"> LAST
                    MONTH</a></span>
                &nbsp |
                <span class="ml-2 hover" id="yearToDate"> YEAR TO
                    DATE</a></span>
                &nbsp |
                <span class="ml-2 hover" id="currentyear"> 2021
                    SEASON</a></span>
                &nbsp |
                <span class="ml-2 hover" id="lastyear"> 2020
                    SEASON</a></span>
                &nbsp |
                <span class="ml-2"> <a href="{{ route('farmer.profile', $farmer) }}">ALL
                        TIME</a></span>
            </div>
            <hr>
            <div class="row ml-2" id="transaction">
                <div class="col-sm-1 color bg-danger">
                    <h3 style="font-size: 16px !important">{{ $farmer->first_purchase }}</h3>
                    <p>First Purchase</p>
                </div>
                <div class="col-sm-1 color bg-primary">
                    <h3 style="font-size: 16px !important">{{ $farmer->last_purchase }}</h3>

                    <p>Last Purchase</p>
                </div>
                <div class="col-sm-1 color bg-warning">
                    @if ($farmer->price_per_kg == null)
                        <h3 style="font-size: 16px !important">{{ number_format($farmer->price * $farmer->quantity) }}
                        </h3>
                    @else
                        <h3 style="font-size: 16px !important">
                            {{ number_format($farmer->price_per_kg * $farmer->quantity) }}
                        </h3>
                    @endif

                    <p>yer total coffee purchased </p>
                </div>
                <div class="col-sm-1 color bg-info">
                    <h3 style="font-size: 16px !important">{{ $farmer->quantity }}</h3>

                    <p>Quantity</p>
                </div>


            </div>
            <hr>
            <div class="row ml-2 blacklink">

                <span href="" class="ml-2" id="iddocuments">
                    <p>ID DOCUMENTS &nbsp;|</p>
                </span>
                <span class="ml-2" id="reciepts">
                    <p>RECIEPTS &nbsp;|</p>
                </span>
                <span href="#" class="ml-2">
                    <p>LOANS &nbsp;|</p>
                </span>
                <span href="#" class="ml-2">
                    <p>PREMIUMS &nbsp;</p>
                </span>

            </div>
            <div class="row ml-2" id="farmer-inovices">

                @if ($farmer->cnicImage == null)
                    <td> <img class="famerimg" src="{{ Storage::disk('s3')->url('images/' . 'dumy.png') }}"
                            style="width: 150px ; height:80px ; border-radius:50%; border: 1px solid gray;" alt=""
                            id="idimage"></td>
                @else
                    <td> <img class="famerimg"
                            style="width: 150px  ; height:80px ; border-radius:50%; border: 1px solid gray;"
                            src="{{ Storage::disk('s3')->url('images/' . $farmer->cnicImage) }}" alt="no img"
                            id="idimage"></td>
                @endif


            </div>
            <hr>
            <b>
                <p>TRANSACTIONS </p>
            </b>
            @foreach ($farmer->transactions as $transaction)
                <div class="row ml-2">
                    <div class="">
                    <ol class=" breadcrumb float-sm-right txt-size">
                        <li class="breadcrumb-item active">
                            {{ $transaction->created_at }} /{{ $farmer->farmer_name }} /
                            {{ $farmer->governerate_title }} /
                            {{ $farmer->region_title }} /
                            @php
                                echo floatval($transaction->details->sum('container_weight'));
                            @endphp

                        </li>
                        </ol>
                    </div>
                </div>
            @endforeach
        </section>
    </div>



@endsection
