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

            width: 200px fit-content;
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

        });

    </script>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Dashboard</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        {{-- <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{URL::to('')}}/admin/dashboard">Home</a></li>
                           </ol> --}}
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
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
                <span class="ml-2"> <a href="{{ url('/admin/dashboard') }}">ALL
                        TIME</a></span>
            </div>
            <hr>
            <div class="row ml-2" id="transactions">
                <div class="col-sm-1 color bg-danger">
                    <h3>{{ $governorate->count() }}</h3>
                    <p>Governorate</p>
                </div>
                <div class="col-sm-1 color bg-primary">
                    <h3>{{ App\Region::count() }}</h3>

                    <p>Regions</p>
                </div>
                <div class="col-sm-1 color bg-warning">
                    <h3>{{ $villages->count() }}</h3>

                    <p>Villages </p>
                </div>
                <div class="col-sm-1 color bg-primary">
                    <h3>{{ App\Farmer::count() }}</h3>

                    <p>Farmers </p>
                </div>
                <div class="col-sm-1 color bg-dark">
                    <h3>{{ $totalWeight }}</h3>
                    <p>Total Coffee </p>
                </div>
                <div class="col-sm-1 color bg-danger">
                    <h3>{{ $totalPrice }}</h3>
                    <p>Yer Coffee Purchased</p>
                </div>
                <div class="col-sm-1 color bg-warning"></div>
                <div class="col-sm-1 color bg-info"></div>
                <div class="col-sm-1 color bg-dark"></div>
            </div>
            <div class="row ml-2">
                <h5>QUANTITY CHERRY BOUGHT</h5>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <canvas id="myChart" style="width:100%;max-height:300px"></canvas>

                    <script>
                        var xValues = [50, 60, 70, 80, 90, 100, 110, 120, 130, 140, 150];
                        var yValues = [7, 8, 8, 9, 9, 9, 10, 11, 14, 14, 15];

                        new Chart("myChart", {
                            type: "line",
                            data: {
                                labels: xValues,
                                datasets: [{
                                    fill: false,
                                    lineTension: 0,
                                    backgroundColor: "rgba(0,0,255,1.0)",
                                    borderColor: "rgba(0,0,255,0.1)",
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
                                            min: 6,
                                            max: 16
                                        }
                                    }],
                                }
                            }
                        });

                    </script>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><b>Farmer</b> </h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width: 10px">Sr#</th>

                                        <th>Farmer Name</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($farmers as $farmer)
                                    @if($farmer != null)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>

                                            <td>{{ $farmer['farmer_name'] }}</td>

                                        </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->

                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><b>Regions</b> </h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr style="white-space:nowrap">
                                        <th style="width: 10px">Sr#</th>
                                       
                                        <th>Region Name</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($regions->take(5) as $region)
                                        <tr style="white-space:nowrap">
                                            <td>{{  $loop->iteration }}</td>
                                            
                                            <td >{{ $region->region_title }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->

                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><b>
                                    <center> SPECIALTY COFFEE IN STOCK</center>
                                </b> </h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Today</h6>
                                    <div class="set-width bg-primary m-1">
                                        <p class="ml-1">2000</p>
                                        <p class="ml-1">....</p>
                                    </div>
                                    <div class="set-width bg-primary m-1 ">
                                        <p class="ml-1">1000</p>
                                        <p class="ml-1">China </p>
                                    </div>
                                    <div class="set-width bg-primary m-1 ">
                                        <p class="ml-1">1000</p>
                                        <p class="ml-1">China </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6>End Date</h6>
                                    <div class="set-width bg-primary m-1">
                                        <p class="ml-1">2000</p>
                                        <p class="ml-1">....</p>
                                    </div>
                                    <div class="set-width bg-primary m-1 ">
                                        <p class="ml-1">1000</p>
                                        <p class="ml-1">China </p>
                                    </div>
                                    <div class="set-width bg-primary m-1 ">
                                        <p class="ml-1">1000</p>
                                        <p class="ml-1">China </p>
                                    </div>
                                </div>
                                <div class="col-md-6"></div>
                            </div>


                        </div>
                        <!-- /.card-body -->

                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><b>
                                    <center>COMMERCIAL COFFEE IN STOCK</center>
                                </b> </h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Today</h6>
                                    <div class="set-width bg-primary m-1">
                                        <p class="ml-1">2000</p>
                                        <p class="ml-1">....</p>
                                    </div>
                                    <div class="set-width bg-primary m-1 ">
                                        <p class="ml-1">1000</p>
                                        <p class="ml-1">China </p>
                                    </div>
                                    <div class="set-width bg-primary m-1 ">
                                        <p class="ml-1">1000</p>
                                        <p class="ml-1">China </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6>End Date</h6>
                                    <div class="set-width bg-primary m-1">
                                        <p class="ml-1">2000</p>
                                        <p class="ml-1">....</p>
                                    </div>
                                    <div class="set-width bg-primary m-1 ">
                                        <p class="ml-1">1000</p>
                                        <p class="ml-1">China </p>
                                    </div>
                                    <div class="set-width bg-primary m-1 ">
                                        <p class="ml-1">1000</p>
                                        <p class="ml-1">China </p>
                                    </div>
                                </div>
                                <div class="col-md-6"></div>
                            </div>


                        </div>
                        <!-- /.card-body -->

                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <canvas id="newchart" style="width:100%;max-width:400px"></canvas>

                    <script>
                        var xValues = [50, 60, 70, 80, 90, 100, 110, 120, 130, 140, 150];
                        var yValues = [7, 8, 8, 9, 9, 9, 10, 11, 14, 14, 15];

                        new Chart("newchart", {
                            type: "line",
                            data: {
                                labels: xValues,
                                datasets: [{
                                    fill: false,
                                    lineTension: 0,
                                    backgroundColor: "rgba(0,0,255,1.0)",
                                    borderColor: "rgba(0,0,255,0.1)",
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
                                            min: 6,
                                            max: 16
                                        }
                                    }],
                                }
                            }
                        });

                    </script>
                </div>
                <div class="col-md-7 ">
                    <canvas id="3rd" class="ml-2" style="width:100%;max-width:500px; height:200px;"></canvas>

                    <script>
                        var xValues = [50, 60, 70, 80, 90, 100, 110, 120, 130, 140, 150];
                        var yValues = [7, 8, 8, 9, 9, 9, 10, 11, 14, 14, 15];

                        new Chart("3rd", {
                            type: "bar",
                            data: {
                                labels: xValues,
                                datasets: [{
                                    fill: false,
                                    lineTension: 0,
                                    backgroundColor: "rgba(0,0,255,1.0)",
                                    borderColor: "rgba(0,0,255,0.1)",
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
                                            min: 6,
                                            max: 16
                                        }
                                    }],
                                }
                            }
                        });

                    </script>
                </div>
            </div>
            <div class="row">
                <canvas id="4thchart" style="width:100%;max-width:400px"></canvas>

                <script>
                    var xValues = [50, 60, 70, 80, 90, 100, 110, 120, 130, 140, 150];
                    var yValues = [7, 8, 8, 9, 9, 9, 10, 11, 14, 14, 15];

                    new Chart("4thchart", {
                        type: "line",
                        data: {
                            labels: xValues,
                            datasets: [{
                                fill: false,
                                lineTension: 0,
                                backgroundColor: "rgba(0,0,255,1.0)",
                                borderColor: "rgba(0,0,255,0.1)",
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
                                        min: 6,
                                        max: 16
                                    }
                                }],
                            }
                        }
                    });

                </script>
            </div>
        </div>
        <!-- /.content-header -->


    </div>
@endsection
