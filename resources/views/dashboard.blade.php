@extends('layouts.default')
@section('title', 'Homepage')
@section('content')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <style type="text/css">
        .small-box>.inner {
            background-color: white;
            color: black;
        }

        .color {

            height: 80px fit-content;
            margin-left: 2px;
        }

        .set-width {

            width: 90px;
            height: 70px fit-content;
            background-color: purple !important;
        }

        a {

            color: rgb(0, 0, 0);
            background-color: transparent;
            text-decoration: none;

        }

        .blacklink .hover:hover {
            cursor: pointer;
        }

        .flex-properties {
            display: inherit;
        }

        .background {
            background-color: green;
            margin: 0 0.5px;
            width: max-content;
        }

        .hover-text {
            visibility: hidden;
            background-color: white;
        }

        .background:hover .hover-text {
            visibility: visible;
        }

    </style>

    <script>
        $(document).ready(function() {
            $("#to").on('change', function() {
                let from = $("#from").val();
                let to = $("#to").val();

                $.ajax({

                    url: "{{ url('admin/dashboardByDate') }}",
                    type: "GET",
                    data: {
                        'from': from,
                        'to': to
                    },
                    success: function(data) {
                        $('#transactions').html(data);
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
                    url: "{{ url('admin/dashboardByDays') }}",
                    type: "GET",
                    data: {
                        'date': 'today'
                    },
                    success: function(data) {

                        $('#transactions').html(data);
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
                    url: "{{ url('admin/dashboardByDays') }}",
                    type: "GET",
                    data: {
                        'date': 'yesterday'
                    },
                    success: function(data) {

                        $('#transactions').html(data);
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
                    url: "{{ url('admin/dashboardByDays') }}",
                    type: "GET",
                    data: {
                        'date': 'weekToDate'
                    },
                    success: function(data) {

                        $('#transactions').html(data);
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
                    url: "{{ url('admin/dashboardByDays') }}",
                    type: "GET",
                    data: {
                        'date': 'monthToDate'
                    },
                    success: function(data) {

                        $('#transactions').html(data);
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
                    url: "{{ url('admin/dashboardByDays') }}",
                    type: "GET",
                    data: {
                        'date': 'lastmonth'
                    },
                    success: function(data) {

                        $('#transactions').html(data);
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
                    url: "{{ url('admin/dashboardByDays') }}",
                    type: "GET",
                    data: {
                        'date': 'yearToDate'
                    },
                    success: function(data) {

                        $('#transactions').html(data);
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
                    url: "{{ url('admin/dashboardByDays') }}",
                    type: "GET",
                    data: {
                        'date': 'currentyear'
                    },
                    success: function(data) {

                        $('#transactions').html(data);
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
                    url: "{{ url('admin/dashboardByDays') }}",
                    type: "GET",
                    data: {
                        'date': 'lastyear'
                    },
                    success: function(data) {

                        $('#transactions').html(data);
                        console.log(data);
                    }
                });
            });
            $('#specialCoffee').on('change', function() {
                let endDate = $("#specialCoffee").val();
                // alert(endDate);
                $.ajax({
                    url: "{{ url('admin/dashboard/specialCoffee') }}",
                    type: "GET",
                    data: {
                        'endDate': endDate
                    },
                    success: function(data) {
                        // alert('pres');
                        $('#ajaxspecialCoffee').html(data);
                        console.log(data);
                    }
                });
            });
            $('#nonspecialCoffee').on('change', function() {
                let endDate = $("#nonspecialCoffee").val();
                // alert(endDate);

                $.ajax({
                    url: "{{ url('admin/dashboard/nonspecialCoffee') }}",
                    type: "GET",
                    data: {
                        'endDate': endDate
                    },
                    success: function(data) {
                        $('#ajaxnonspecialCoffee').html(data);
                        console.log(data);
                    }
                });
            });
        });
    </script>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->

        <div class="content-header mx-lg-5">
            <div class="container-fluid">
                @if (session()->has('msg'))
                    <div class="alert alert-success" id="alert">
                        {{ session()->get('msg') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                <div class="row mb-2">
                    <div class="col-sm-6 pl-0">
                        <h1 class="m-0 text-dark text-uppercase font-weight-lighter text-heading">Dashboard</h1>

                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        {{-- <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{URL::to('')}}/admin/dashboard">Home</a></li>
                           </ol> --}}
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
            <hr class="ml-md-2">
            <div class="row ml-2 text-uppercase mb-2">
                <strong>
                    <b>Date Filter</b>
                </strong>
            </div>
            <div class="row ml-2 mb-2">
                <form action="" method="POST" id="data-form">
                    <label class="text-uppercase font-weight-normal mr-2 mb-0 letter-spacing-1" for="from">From</label>
                    <input class="mr-3" type="date" name="" id="from">
                    <label class="text-uppercase font-weight-normal mr-2 mb-0 letter-spacing-1" for="To">To</label>
                    <input class="mr-3" type="date" name="" id="to">
                </form>
            </div>
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
                <span class="ml-md-2 hover" id="yearToDate"> YEAR TO
                    DATE</a></span>
                &nbsp |
                <span class="ml-md-2 hover" id="currentyear"> 2021
                    SEASON</a></span>
                &nbsp |
                <span class="ml-md-2 hover" id="lastyear"> 2020
                    SEASON</a></span>
                &nbsp |
                <span class="ml-md-2 hover " style="font-weight: bold; text-decoration: underline;"> <a
                        href="{{ url('/admin/dashboard') }}">ALL
                        TIME</a></span>
            </div>
            <hr class="ml-md-2">
            <div class="col-lg-11 ml-md-2 text-uppercase d-flex flex-wrap p-0 mb-3 data-tabs" id="transactions">
                <div class="col-sm-1 color bg-darkPurple p-2 content-box">
                    <h4>{{ $totalWeight }}</h4>
                    <p>KG CHERRY BOUGHT </p>
                </div>
                <div class="col-sm-1 color bg-darkGreen p-2 content-box">
                    <h4>{{ $totalPrice }}</h4>
                    <p>YER COFFEE Purchased</p>
                </div>
                <div class="col-sm-1 color bg-darkRed p-2 content-box">
                    <h4>-</h4>

                    <p>YER
                        ACCOUNT
                        PAYABLE </p>
                </div>
                <div class="col-sm-1 color bg-darkGreen p-2 content-box">
                    <h4>-</h4>
                    <p>YER SETTELED</p>
                </div>
                {{-- <div class="col-sm-1 color bg-darkGreen p-2 content-box">
                    <h4>{{ App\Region::count() }}</h4>

                    <p>Regions</p>
                </div> --}}
                <div class="col-sm-1 color bg-lightBrown p-2 content-box">
                    <h4>{{ App\Farmer::count() }}</h4>

                    <p>Farmers </p>
                </div>
                {{-- <div class="col-sm-1 color bg-darkPurple p-2 content-box">
                    <h4>{{ $governorate->count() }}</h4>
                    <p>Governorate</p>
                </div> --}}
                {{-- <div class="col-sm-1 color bg-lightBrown p-2 content-box">
                    <h4>{{ $totalWeight }}</h4>
                    <p>Total Coffee </p>
                </div> --}}
                <div class="col-sm-1 color bg-lightGreen p-2 content-box">
                    <h4>{{ $readyForExport }}</h4>
                    <p>KG SPECIALTY
                        COFFEE EXPORT
                        READY IN YEMEN</p>
                </div>
                <div class="col-sm-1 color bg-lightGreen p-2 content-box">
                    <h4>-</h4>
                    <p>KG COMMERCIAL
                        GREEN COFFEE
                        EXPORT READY</p>
                </div>
                <div class="col-sm-1 color bg-darkGreen p-2 content-box">
                    <h4>-</h4>
                    <p>YER YEMEN
                        SALES</p>
                </div>
                <div class="col-sm-1 color bg-lightGreen p-2 content-box">
                    <h4>-</h4>
                    <p>USD SALES</p>
                </div>

            </div>
            <div class="row ml-2 text-uppercase mb-2">
                <strong>
                    <b>QUANTITY CHERRY BOUGHT</b>
                </strong>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <canvas id="myChart" style="width:100%;max-height:500px"></canvas>

                    <script>
                        console.log(@json($quantity));
                        var xValues = @json($createdAt);
                        var yValues = @json($quantity);
                        new Chart("myChart", {
                            type: "line",
                            data: {
                                labels: xValues,
                                datasets: [{
                                    pointRadius: 4,
                                    fill: false,
                                    tension: 0.5,

                                    backgroundColor: "black",
                                    borderColor: "gray",
                                    data: yValues
                                }]
                            },
                            options: {
                                legend: {
                                    display: false
                                },
                                scales: {
                                    yAxes: [{

                                        ticks: {
                                            min: 0,
                                            max: 10000
                                        }
                                    }],
                                    xAxes: [{
                                        barPercentage: 0.4
                                    }]
                                }

                            }
                        });
                    </script>
                </div>
            </div>
            <hr class="ml-md-2">
            <div class="row">
                <div class="col-md-3 vl">
                    <div class="card shadow-none">
                        <div class="text-uppercase px-3 h5">
                            <strong>
                                <b>Farmer</b>
                            </strong>
                            <p class="mb-0 card-custom-description">KG CHERRY<br>BOUGHT</p>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body pt-0">
                            <table class="table table-borderless">
                                <!-- <thead>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <tr>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <th style="width: 10px">Sr#</th>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <th>Farmer Name</th>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </tr>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </thead> -->
                                <tbody>
                                    @if (count($farmers) == 0)
                                        @php
                                        $loop = 5 - count($farmers); @endphp
                                        @foreach (App\Farmer::all()->take(5) as $farmer)
                                            <tr style="white-space:nowrap">
                                                <!-- <td>{{ $loop->iteration }}</td> -->

                                                <td class="d-flex align-items-center px-0">
                                                    <img class="rounded-circle" src="https://i.imgur.com/C4egmYM.jpg"
                                                        width="50">
                                                    <span class="ml-3">
                                                        {{ $farmer['farmer_name'] }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    @foreach ($farmers as $farmer)

                                        <tr>

                                            <!-- <td>{{ $loop->iteration }}</td> -->

                                            <td class="d-flex align-items-center px-0">
                                                <img class="rounded-circle" src="https://i.imgur.com/C4egmYM.jpg"
                                                    width="50">
                                                <span class="ml-3">
                                                    {{ $farmer['farmer_name'] . '-' . $farmer['weight'] }}
                                                </span>
                                            </td>

                                        </tr>

                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->

                    </div>
                </div>
                <div class="col-md-3 vl">
                    <div class="card shadow-none">
                        <div class="text-uppercase px-3 h5">
                            <strong>
                                <b>Regions</b>
                            </strong>
                            <p class="mb-0 card-custom-description">KG CHERRY<br>BOUGHT</p>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body pt-0">
                            <table class="table table-borderless">
                                <!-- <thead>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <tr style="white-space:nowrap">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <th style="width: 10px">Sr#</th>

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <th>Region Name</th>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </tr>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </thead> -->
                                <tbody>
                                    {{-- @if (count($regions) == 0)
                                    @php
                                    $loop = 5 - count($regions); @endphp
                                    @foreach (App\Region::all()->take(5) as $region)
                                        <tr style="white-space:nowrap">
                                            <!-- <td>{{ $loop->iteration }}</td> -->
                                            <td class="d-flex align-items-center px-0">
                                                    <img class="rounded-circle" src="https://i.imgur.com/C4egmYM.jpg" width="50">
                                                    <span class="ml-3">
                                                    {{ $region->region_title }}
                                                    </span>
                                                </td>
                                        </tr>
                                    @endforeach
                                @endif --}}
                                    @foreach ($regions as $region)
                                        <tr style="white-space:nowrap">
                                            <!-- <td>{{ $loop->iteration }}</td> -->

                                            <td class="d-flex align-items-center px-0">
                                                <img class="rounded-circle" src="https://i.imgur.com/C4egmYM.jpg"
                                                    width="50">
                                                <span class="ml-3">
                                                    {{ $region['region_title'] . '-' . $region['weight'] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->

                    </div>
                </div>
                <div class="col-md-3 vl">
                    <div class="card shadow-none h-100">

                        <div class="text-center text-uppercase">
                            <strong>
                                <b>SPECIALTY COFFEE IN STOCK</b>
                            </strong>
                        </div>
                        <!-- /.card-header -->
                        <input type="date" id="specialCoffee" name="endDate" class="form-control border-0">
                        <div class="card-body d-flex flex-column" id="ajaxspecialCoffee">
                            <div class="row">
                                <div class="text-center text-uppercase col-6 px-1">
                                    <h6><b>Today</b></h6>
                                </div>
                                <div class="text-center text-uppercase col-6 px-1">
                                    <h6><b>End Date</b></h6>
                                </div>
                            </div>
                            @foreach ($stock as $key => $s)
                                <div class="row mb-md-2 flex-1">
                                    <div class="col-md-6 data-tabs px-1">
                                        <div class="h-100 bg-dark-blue text-uppercase mb-2">
                                            <h4 class="ml-1">{{ $s['today'] }}</h4>
                                            <p class="ml-1 mb-0">{{ $s['wareHouse'] }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6 data-tabs px-1">
                                        <div class="h-100 bg-dark-blue text-uppercase mb-2">
                                            <h4 class="ml-1">{{ $s['end'] }}</h4>
                                            <p class="ml-1 mb-0">{{ $s['wareHouse'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <!-- /.card-body -->

                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-none h-100">

                        <div class="text-center text-uppercase">
                            <strong>
                                <b>COMMERCIAL COFFEE IN STOCK</b>
                            </strong>
                        </div>
                        <!-- /.card-header -->
                        <input type="date" id="nonspecialCoffee" name="endDate" class="form-control border-0">
                        <div class="card-body d-flex flex-column" id="ajaxnonspecialCoffee">

                            <div class="row">
                                <div class="text-center text-uppercase col-6 px-1">
                                    <h6><b>Today</b></h6>
                                </div>
                                <div class="text-center text-uppercase col-6 px-1">
                                    <h6><b>End Date</b></h6>
                                </div>
                            </div>
                            @foreach ($nonspecialstock as $key => $s)

                                <div class="row mb-md-2 flex-1">
                                    <div class="col-md-6 data-tabs px-1">
                                        <div class="h-100 bg-dark-blue text-uppercase mb-2">
                                            <h4 class="ml-1">{{ $s['today'] }}</h4>
                                            <p class="ml-1 mb-0">{{ $s['wareHouse'] }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6 data-tabs px-1">
                                        <div class="h-100 bg-dark-blue text-uppercase mb-2">
                                            <h4 class="ml-1">{{ $s['end'] }}</h4>
                                            <p class="ml-1 mb-0">{{ $s['wareHouse'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach



                        </div>
                        <!-- /.card-body -->

                    </div>
                </div>
            </div>
            <hr class="col-">
            <div class="row">

                <div class="col-md-5 ">
                    <center>Governorate Wise</center>
                    <canvas id="4rd" class="ml-md-2" style="width:100%; height:200px;"></canvas>

                    <script>
                        var xValues = @json($govName);
                        var yValues = @json($govQuantity);
                        var barColors = ["red", "green", "blue", "orange", "brown", "yellow", "purple", "black", "DeepPink",
                            "DarkOrange",
                        ];
                        new Chart("4rd", {
                            type: "line",
                            data: {
                                labels: xValues,
                                datasets: [{
                                    pointRadius: 3,
                                    // backgroundColor: "#e755ba",
                                    pointBackgroundColor: "white",
                                    pointBorderColor: "black",
                                    fill: false,
                                    lineTension: 0.3,
                                    borderWidth: 1,
                                    // lineColor: "black",
                                    borderColor: "black",
                                    data: yValues
                                }]
                            },
                            options: {
                                legend: {
                                    display: false
                                },
                                scales: {
                                    yAxes: [{
                                        ticks: {}
                                    }],
                                }
                            }
                        });
                    </script>
                </div>
                <div class="col-md-7">
                    <h3 class='ml-2' style="font-size: 10px">OVERVIEW OF COFFEE BOUGHT BY GOVERNORATE</h3>
                    <div class="row">

                        <div class="col-sm-12">

                            <table style=" height:200px; width: 100%; font-size: 12px ;">
                                <tr align="center" style="border-bottom: 1px solid black; border-right: 1px solid black">
                                    <td align="center" style="border-right: 1px solid black;">GOVERNORATE</td>
                                    <td align="center" style=" border-right: 1px solid black">QUANTITY</td>
                                    <td align="center" style=" border-right: 1px solid black">FARMERS</td>
                                    <td></td>
                                </tr>
                                <tbody>
                                    @foreach ($govQuantityRegion as $gov)
                                        <tr style="border-right: 1px solid black">
                                            <td align="center" style="border-right: 1px solid black">{{ $gov['title'] }}
                                            </td>
                                            <td align="center" style="border-right: 1px solid black">
                                                {{ $gov['weight'] . 'KGs' }}
                                            </td>
                                            <td align="center" style="border-right: 1px solid black">
                                                {{ $gov['farmerCount'] }}</td>
                                            <td>
                                                @foreach ($gov['region'] as $r)
                                                    @php
                                                        $widht = $r['weight'] / 500;
                                                    @endphp
                                                    {{-- <p>{{ $r['regionTitle']}}</p> --}}
                                                    <div class="flex-properties">
                                                        <div style="position: relative;" class="background"
                                                            width={{ $widht }}>

                                                            <span class="hover-text "> {{ $r['regionTitle'] }}</span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>

                </div>
            </div>
            <div class="row">
                <div class="col-md-5">
                    <center>Region Wise</center>
                    <canvas id="3rd" class="ml-md-2" style="width:100%; height:200px;"></canvas>

                    <script>
                        var xValues = @json($regionName);
                        var yValues = @json($regionQuantity);

                        new Chart("3rd", {
                            type: "line",
                            data: {
                                labels: xValues,
                                datasets: [{
                                    pointRadius: 3,
                                    // backgroundColor: "#e755ba",
                                    pointBackgroundColor: "white",
                                    pointBorderColor: "black",
                                    fill: false,
                                    lineTension: 0.3,
                                    borderWidth: 1,
                                    // lineColor: "black",
                                    borderColor: "black",
                                    //  backgroundColor: 'black',
                                    data: yValues
                                }]
                            },
                            options: {
                                legend: {
                                    display: false
                                },
                                scales: {
                                    yAxes: [{

                                    }],
                                }
                            }
                        });
                    </script>
                </div>
            </div>



        </div>
        <!-- /.content-header -->


    </div>
@endsection
