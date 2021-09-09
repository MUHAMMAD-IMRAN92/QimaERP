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

        #region_farmer {
            border-radius: 50%;
            width: 15% !important;

        }


        .blacklink .hover:hover {
            cursor: pointer;
        }

    </style>

    <script>
        $(document).ready(function() {

            $('#to').on('change', function() {
                let from = $('#from').val();
                let to = $('#to').val();


                $.ajax({
                    url: "{{ url('admin/daysFilter/' . $buyer->user_id) }}",
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
                    url: "{{ url('admin/daysFilter/' . $buyer->user_id) }}",
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
                    url: "{{ url('admin/daysFilter/' . $buyer->user_id) }}",
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
                    url: "{{ url('admin/daysFilter/' . $buyer->user_id) }}",
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
                    url: "{{ url('admin/daysFilter/' . $buyer->user_id) }}",
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
                    url: "{{ url('admin/daysFilter/' . $buyer->user_id) }}",
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
                    url: "{{ url('admin/daysFilter/' . $buyer->user_id) }}",
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
                    url: "{{ url('admin/daysFilter/' . $buyer->user_id) }}",
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
                    url: "{{ url('admin/daysFilter/' . $buyer->user_id) }}",
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
        });
    </script>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">

        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">

                    <div class="col-sm-6">
                        <h1> Profile

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
                <span class="ml-5" style="margin-left: 60% !important"> <a
                        href="{{ url('admin/edituser/' . $buyer->id) }}">EDIT
                        INFORMATION</a></span> &nbsp |
                <span class="ml-2"> <a href=""> ADD PANELTY</a></span>

            </div>
            <hr>
            <div class="row">
                <div class="col-md-4">
                    @if ($buyer->image == null)
                        <td> <img class="famerimg ml-4" src="{{ asset('public/images/farmericon.png') }}"
                                style="width: 200px ; height:200px; border-radius:50%; border: 1px solid gray;" alt=""></td>
                    @else
                        <td> <img class="famerimg ml-4"
                                style="width:  200px ; height: 200px; border-radius:50%; border: 1px solid gray;"
                                src="{{ asset('public/storage/images/' . $buyer->image) }}" alt=" no img"></td>
                    @endif

                </div>
                <div class="col-md-8">
                    <table class="table table-borderless" id="farmerTable">

                        <tbody>
                            <tr>
                                <td colspan=""> <strong>Name</strong> </td>
                                <td colspan="4">{{ $buyer->first_name }}</td>
                                <td colspan="4"></td>
                            </tr>
                            {{-- <tr>
                                <td colspan=""><strong>IDENTIFICATION NUMBER</strong></td>
                                <td colspan="4">{{ $buyer->farmer_nicn }}</td>
                                <td colspan="4"></td>
                            </tr> --}}
                            <tr>
                                <td colspan=""><strong>City </strong></td>
                                <td colspan="4">lahore</td>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td colspan=""><strong>Tel</strong></td>
                                <td colspan="4">123456789</td>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td colspan=""><strong>Start Date</strong></td>
                                <td colspan="4">{{ $buyer->created_at }}</td>
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
                <span class="ml-2"> <a href="{{ route('coffeBuyer.profile', $buyer) }}">ALL
                        TIME</a></span>
            </div>
            <hr>
            <div class="row ml-2" id="transaction">
                <div class="col-sm-1 color bg-danger">
                    <h3 style="font-size: 16px !important">{{ $buyer->first_purchase }}</h3>
                    <p>First Purchase</p>
                </div>
                <div class="col-sm-1 color bg-primary">
                    <h3 style="font-size: 16px !important">{{ $buyer->last_purchase }}</h3>

                    <p>Last Puchase</p>
                </div>
                <div class="col-sm-1 color bg-info">
                    <h3 style="font-size: 16px !important">{{ number_format($buyer->sum) }}</h3>

                    <p>Quantity</p>
                </div>
                <div class="col-sm-1 color bg-secondary ">
                    <h3 style="font-size: 16px !important">{{ number_format($buyer->price) }}</h3>

                    <p>yer total coffee purchased</p>
                </div>
                <div class="col-sm-1 color bg-info"></div>
                <div class="col-sm-1 color bg-dark"></div>
                <div class="col-sm-1 color bg-danger"></div>
                <div class="col-sm-1 color bg-success"></div>

            </div>
            <hr>
            <div class="row ml-2 blacklink">

                <a href="" class="ml-2">
                    <p>ID DOCUMENTS &nbsp;|</p>
                </a>
                <a href="" class="ml-2">
                    <p> RECIEPTS &nbsp;|</p>
                </a>
                <a href="" class="ml-2">
                    <p>LOANS &nbsp;|</p>
                </a>
                <a href="" class="ml-2">
                    <p>PREMIUMS &nbsp;</p>
                </a>

            </div>
            <div class="row ml-2">
                <div class="col-sm-1 color bg-danger">
                    <h3>{{ App\Village::count() }}</h3>
                    <p>Villages</p>
                </div>
                <div class="col-sm-1 color bg-primary">
                    <h3>{{ App\Farmer::count() }}</h3>

                    <p>Farmers</p>
                </div>
                <div class="col-sm-1 color bg-warning">
                    <h3>{{ App\User::count() }}</h3>

                    <p>User </p>
                </div>


            </div>
            <hr>
            <b>
                <p> Villages Responsible For
                </p>
            </b>
            <div class="row">
                @foreach ($buyer->villages as $village)
                    <div class="col-sm-4 mb-1">
                        <ul class="list-group list-group-horizontal">
                            <li class="list-group-item data-content-list">
                                @if ($village['picture_id'] == null)
                                    <img src="{{ asset('public/images/farmericon.png') }}" id="region_farmer">
                                @else
                                    <img src="{{ asset('public/storage/images/' . $farmer['picture_id']) }}"
                                        id="region_farmer">
                                @endif

                                <span class="mr-3">{{ $village['village_title'] }}</span>
                            </li>

                        </ul>
                    </div>
                @endforeach
            </div>
            <hr>

            <b>
                <p>Farmers Bought From
                </p>
            </b>
            <div class="row">
                @foreach ($buyer->farmers as $farmer)
                    <div class="col-sm-4 mb-1">
                        <ul class="list-group list-group-horizontal">
                            <li class="list-group-item data-content-list">
                                @if ($farmer['farmer_image'] == null)
                                    <img src="{{ asset('public/images/farmericon.png') }}" id="region_farmer">
                                @else
                                    <img src="{{ asset('public/storage/images/' . $farmer['farmer_image']) }}"
                                        id="region_farmer">
                                @endif

                                <span class="mr-3">{{ $farmer['farmer_name'] }}</span>
                            </li>

                        </ul>
                    </div>
                @endforeach
            </div>
            <hr>
            <b>
                <p>TRANSACTIONS </p>
            </b>
            @foreach ($buyer->transactions as $transaction)
                <div class="row ml-2">
                    <div class="">
                        <ol class="breadcrumb float-sm-right txt-size">
                            <li class="breadcrumb-item active">
                                {{ $transaction->created_at }} /{{ $buyer->first_name }} /
                                {{ explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] }}
                                /
                                @php
                                 echo   floatval($transaction->details->sum('container_weight') );
                                @endphp

                            </li>
                        </ol>
                    </div>
                </div>
            @endforeach
        </section>

        <!-- Main content -->

        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->


@endsection
