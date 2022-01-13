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
            border-spacing: 0px;
        }

        #farmerTable tr td {
            padding: unset !important;
            padding: 0 20px !important;
        }

        #farmerTable tr td:first-child {
            border-right: 1px solid rgba(0, 0, 0, .1);
        }

        #farmerTable tr:first-child td {
            padding-top: 1rem !important;
        }

        #farmerTable tr:last-child td {
            padding-bottom: 1rem !important;
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

        #idimage:hover {
            width: 700px !important;
            height: 500px !important;
        }

        #batch_number {
            width: 100%;
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
                $('.blacklink .hover').css({
                    'font-weight': 'normal',
                    'text-decoration': 'none'
                });
                $(this).css({
                    'font-weight': 'bold',
                    'text-decoration': 'underline'
                });
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
                $('.blacklink .hover').css({
                    'font-weight': 'normal',
                    'text-decoration': 'none'
                });
                $(this).css({
                    'font-weight': 'bold',
                    'text-decoration': 'underline'
                });
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
                $('.blacklink .hover').css({
                    'font-weight': 'normal',
                    'text-decoration': 'none'
                });
                $(this).css({
                    'font-weight': 'bold',
                    'text-decoration': 'underline'
                });
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
                $('.blacklink .hover').css({
                    'font-weight': 'normal',
                    'text-decoration': 'none'
                });
                $(this).css({
                    'font-weight': 'bold',
                    'text-decoration': 'underline'
                });
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
                $('.blacklink .hover').css({
                    'font-weight': 'normal',
                    'text-decoration': 'none'
                });
                $(this).css({
                    'font-weight': 'bold',
                    'text-decoration': 'underline'
                });
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
                $('.blacklink .hover').css({
                    'font-weight': 'normal',
                    'text-decoration': 'none'
                });
                $(this).css({
                    'font-weight': 'bold',
                    'text-decoration': 'underline'
                });
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
                $('.blacklink .hover').css({
                    'font-weight': 'normal',
                    'text-decoration': 'none'
                });
                $(this).css({
                    'font-weight': 'bold',
                    'text-decoration': 'underline'
                });
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
                $('.blacklink .hover').css({
                    'font-weight': 'normal',
                    'text-decoration': 'none'
                });
                $(this).css({
                    'font-weight': 'bold',
                    'text-decoration': 'underline'
                });
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
                $('.blacklink .hover-2').css({
                    'border-bottom': '0'
                });
                $(this).css({
                    'border-bottom': '1px solid'
                });
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
                $('.blacklink .hover-2').css({
                    'border-bottom': '0'

                });
                $(this).css({
                    'border-bottom': '1px solid'
                });
                $.ajax({
                    url: "{{ url('admin/farmer_id_documents/' . $farmer->farmer_id) }}",
                    type: "GET",
                    success: function(data) {
                        $('#farmer-inovices').html(data);
                        console.log(data);
                    }
                });
            });
            // $('#end_season_btn').on('click', function() {
            //     $.ajax({
            //         url: "{{ url('admin/endSeason/' . $farmer->farmer_id) }}",
            //         type: 'GET',
            //         success: function(data) {
            //             console.log(data);
            //         }
            //     });
            // });
        });
    </script>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">

        <!-- Content Header (Page header) -->
        <section class="content-header mx-lg-5">
            <div class="container-fluid">
                <div class="row mb-2">

                    <div class="col-sm-6 pl-0">
                        <h1 class="m-0 text-dark text-uppercase font-weight-lighter text-heading">Farmer Profile
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

            @if (Session()->has('msg'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session()->get('msg') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            @if (Session()->has('dmsg'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session()->get('msg') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="row anchor  btn-color-darkRed add-button justify-content-end">
                <span class="ml-2"> <a href="{{ url('admin/editfarmer/' . $farmer->farmer_id) }}">EDIT
                        INFORMATION</a></span> &nbsp |
                <span class="ml-2"> <a href="#" data-toggle="modal" data-target="#exampleModal"> ADD CROPSTER
                        REPORT</a></span> &nbsp
                |
                <span class="ml-2"> <a href="{{ url('admin/editfarmer/' . $farmer->farmer_id) }}"> OVERRIDE
                        PRICE
                    </a></span> &nbsp |
                <span class="ml-2"> <a href="{{ url('admin/editfarmer/' . $farmer->farmer_id) }}">OVERRIDE
                        REWARD</a></span> &nbsp |
                <span class="ml-2"> <a href="#">ADD PREMIUM</a></span>&nbsp |
                <span class="ml-2"> <a href="#">SETTLE LOAN</a></span>
            </div>
            <hr class="mb-0">
            <div class="row">
                <form action="{{ url('admin/add_cropster_report') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Add Cropster Report</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="entity_id" value="{{ $farmer->farmer_id }}">
                                    <input type="hidden" name="entity_type" value="1">
                                    <input type="file" name="cropster_report" placeholder="Please Enter Batch Number"
                                        id="batch_number">
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">
                                        Upload Report
                                    </button>
                                </div>
                            </div>
                        </div>
                </form>
            </div>
            <div class="col-md-4 my-3">
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
                <table class="table table-borderless w-auto village_profile_table mb-0" id="farmerTable">

                    <tbody>
                        <tr>
                            <td colspan=""> <strong>Name</strong> </td>
                            <td colspan="4">{{ $farmer->farmer_name }}</td>
                        </tr>
                        <tr>
                            <td colspan=""><strong>IDENTIFICATION NUMBER</strong></td>
                            <td colspan="4">{{ $farmer->farmer_nicn }}</td>
                        </tr>
                        <tr>
                            <td colspan=""><strong>CODE </strong></td>
                            <td colspan="4">{{ $farmer->farmer_code }}</td>
                        </tr>
                        <tr>
                            <td colspan=""><strong>GOVERNORATE</strong></td>
                            <td colspan="4">{{ $farmer->governerate_title }}</td>
                        </tr>
                        <tr>
                            <td colspan=""><strong>REGION</strong></td>
                            <td colspan="4">{{ $farmer->region_title }}</td>
                        </tr>
                        <tr>
                            <td colspan=""><strong>VILLAGE</strong></td>
                            <td colspan="4">{{ $farmer->village_title }}</td>
                        </tr>
                        <tr>
                            <td colspan=""><strong>ALTITUDE</strong></td>
                            <td colspan="4">{{ $farmer->altitude }}</td>
                        </tr>
                        <tr>
                            <td colspan=""><strong>HOUSE HOLD SIZE</strong></td>
                            <td colspan="4">{{ $farmer->house_hold_size }}</td>
                        </tr>
                        <tr>
                            <td colspan=""><strong>FARM SIZE</strong></td>
                            <td colspan="4">{{ $farmer->farm_size }}</td>
                        </tr>
                        <tr>
                            <td colspan=""><strong>NO OF TREES</strong></td>
                            <td colspan="4">{{ $farmer->no_of_trees }}</td>
                        </tr>
                        <tr>
                            <td colspan=""><strong>STARTED WORKING WITH QIMA</strong></td>
                            <td colspan="4">{{ $farmer->created_at->toDateString() }}</td>
                        </tr>
                        <tr>
                            <td colspan=""><strong>FAMER INFORMATION</strong></td>
                            <td colspan="4">{{ $farmer->farmer_info }}</td>
                        </tr>
                        <tr>
                            <td colspan=""><strong>CUPPING SCORE</strong></td>
                            <td colspan="4">{{ $farmer->cupping_score }}</td>
                        </tr>
                        <tr>
                            <td colspan=""><strong>CUP PROFILE</strong></td>
                            <td colspan="4">{{ $farmer->cup_profile }}</td>
                        </tr>
                        <tr>
                            <td colspan=""><strong>TEL</strong>
                            </td>
                            <td colspan="8">{{ $farmer->ph_no }}</td>
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

                        </tr>
                        <tr>
                            <td colspan=""><strong>REWARD PER KG</strong></td>
                            <td colspan="4">{{ $farmer->reward }}</td>
                        </tr>

                    </tbody>
                </table>
            </div>
    </div>
    <hr class="ml-md-2 mt-0">
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
        <span class="ml-2 hover" style="font-weight: bold; text-decoration: underline;"> <a
                href="{{ route('farmer.profile', $farmer) }}">ALL
                TIME</a></span>
    </div>
    <hr class="ml-2">
    <div class="ml-2 text-uppercase d-flex flex-wrap p-0 mb-3 data-tabs" id="transaction">
        <div class="col-sm-1 color bg-darkPurple mr-1">
            <h4>{{ $farmer->first_purchase }}</h4>
            <p>First Purchase</p>
        </div>
        <div class="col-sm-1 color bg-darkPurple mr-1">
            <h4>{{ $farmer->last_purchase }}</h4>

            <p>Last Purchase</p>
        </div>
        <div class="col-sm-1 color bg-Green mr-1">
            @if ($farmer->price_per_kg == null)
                <h4>{{ number_format($farmer->price * $farmer->quantity) }}
                </h4>
            @else
                <h4>
                    {{ number_format($farmer->price_per_kg * $farmer->quantity) }}
                </h4>
            @endif

            <p>Yer Total Coffee Purchased </p>
        </div>

        <div class="col-sm-1 color bg-Green mr-1">
            <h4>-</h4>
            <p>YER SETTELED</p>
        </div>
        <div class="col-sm-1 color bg-Green mr-1">
            <h4>-</h4>
            <p>YER REWARD</p>
        </div>
        <div class="col-sm-1 color bg-Green mr-1">
            <h4>-</h4>
            <p>YER ADDITIONAL
                PREMIUM</p>
        </div>
        <div class="col-sm-1 color bg-darkRed mr-1">
            <h4>-</h4>
            <p>YER
                ACCOUNT
                PAYABLE</p>
        </div>
    </div>
    <hr>
    <div class="row ml-2 blacklink mb-2">
        <span class="row ml-2 text-uppercase mb-2 hover-2" id="iddocuments">
            <strong>
                <b>ID DOCUMENTS</b>
            </strong>
        </span>
        <span class="ml-3 font-weight-bold">|</span>
        <span class="row ml-2 text-uppercase mb-2 hover-2" id="reciepts">
            <strong>
                <b>RECIEPTS</b>
            </strong>
        </span>
        <span class="ml-3 font-weight-bold">|</span>
        <span class="row ml-2 text-uppercase mb-2 hover-2" id="">
            <strong>
                <b>LOANS</b>
            </strong>
        </span>
        <span class="ml-3 font-weight-bold">|</span>
        <span class="row ml-2 text-uppercase mb-2 hover-2" id="">
            <strong>
                <b>PREMIUMS &nbsp;</b>
            </strong>
        </span>
    </div>
    <div class="row ml-2" id="farmer-inovices">
        <div class="col-sm-1 color p-0 ml-0">

            @if ($farmer->cnicImage == null)

                <img class="famerimg" src="{{ Storage::disk('s3')->url('images/' . 'dumy.png') }}"
                    style="max-width: 100%; height: 100%;" alt="">

            @else
                <img class="famerimg" style="width: 100%; height: 100%;"
                    src="{{ Storage::disk('s3')->url('images/' . $farmer->cnicImage) }}" alt="no img" id="idimage">
            @endif
            <div id="myModal" class="modal">
                <span class="close">&times;</span>
                <img class="modal-content" id="img01">
                <div id="caption"></div>
            </div>
        </div>
    </div>
    <hr class="ml-2">
    <div class="ml-2">
        <strong>
            <b>Cropster Reports</b>
        </strong>
    </div>

    <div class="row">


        <div class="">
            <ul>
                @foreach ($farmer->cropsterReports as $url)
                    <li> <a href="{{ $url->file_url }}" target="_blank">Report
                            {{ Str::afterLast($url->file_url, '/') }}</a>
                    </li>
                @endforeach
            </ul>
        </div>

    </div>
    <hr class="ml-2">

    <div class="row ml-2">
        <div class="col-8 text-uppercase">
            <strong>
                <b>TRANSACTIONS</b>
            </strong>
        </div>

        <div class="col-2 mt-1">
            <b>Current Season</b> : {{ $farmer->season_no }}
        </div>
        <div class="col-2 " id="end_season_btn">
            <a class="btn btn-success" href="{{ url('admin/endSeason/' . $farmer->farmer_id) }}">End Season</a>
        </div>
    </div>

    @foreach ($farmer->transactions->reverse() as $transaction)
        <div class="row">
            <div class="col-4">
                <ol class=" breadcrumb  txt-size">
                    <li class="breadcrumb-item active">
                        {{ $transaction->created_at }} /{{ $farmer->farmer_name }}
                        /{{ App\User::find($transaction->created_by)->first_name . '' . App\User::find($transaction->created_by)->last_name }}/
                        {{ $farmer->governerate_title }} /
                        {{ $farmer->region_title }} / <a
                            href="{{ url('admin/farmer_invoice/' . $transaction->transaction_id) }}"><span> Upload
                                Invoice</span></a>
                        @php
                            echo floatval($transaction->details->sum('container_weight'));
                        @endphp
                        /{{ number_format(round($transaction->details->sum('container_weight') * farmerPricePerKg($transaction->batch_number), 2)) }}
                    </li>
                </ol>
            </div>
            <span class="col-2 ">{{ $transaction->batch_number }}</span>
        </div>
    @endforeach
    </section>
    </div>
@endsection
