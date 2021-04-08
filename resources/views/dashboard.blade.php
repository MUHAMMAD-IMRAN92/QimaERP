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

    </style>

    <script>
        $(document).ready(function() {
            $("#to").on('change', function() {
                let from = $("#from").val();
                let to = $("#to").val();
               
                $.ajax({
                    url: "{{ url('admin/dashboard') }}",
                    data:  $('#data-form').serialize(),
                    success: function(data) {
                        alert(data);
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
                    <h5>Date Filter</h5>
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
            <div class="row ml-2 ">
                <span> <a href="">TODAY</a></span> | <span> <a href=""> YESTERDAY</a></span> | <span> <a href=""> WEEK TO
                        DATE </a></span> | <span> <a href="">MONTH TO DATE</a></span> | <span> <a href="">
                        LAST MONTH</a></span> | <span> <a href="">YEAR TO DATE</a></span> | <span> <a href=""> 2021
                        SEASON</a></span> | <span> <a href=""> 2021 SEASON</a></span> | <span> <a href=""> 2020
                        SEASON</a></span>| <span> <a href=""> ALL TIME</a></span>
            </div>
            <hr>
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
                <div class="col-sm-1 color bg-info"></div>
                <div class="col-sm-1 color bg-dark"></div>
                <div class="col-sm-1 color bg-danger"></div>
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
                                    @foreach ($farmer as $key => $row)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>

                                            <td>{{ $row->farmer_name }}</td>

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
                            <h3 class="card-title"><b>Regions</b> </h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width: 10px">Sr#</th>
                                        <th>Region Code</th>
                                        <th>Region Name</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($regions as $region)
                                        <tr>
                                            <td>{{ $region->region_id }}</td>
                                            <td>{{ $region->region_code }}</td>
                                            <td>{{ $region->region_title }}</td>
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

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- Small boxes (Stat box) -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ App\Farmer::count() }}</h3>

                                <p>Farmers</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>
                            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-success">
                            <div class="inner">
                                {{-- <h3>53<sup style="font-size: 20px">%</sup></h3> --}}
                                <h3>{{ App\Village::count() }}</h3>
                                <p>Villages</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-stats-bars"></i>
                            </div>
                            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ App\User::count() }}</h3>

                                <p>User </p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-person-add"></i>
                            </div>
                            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>65</h3>

                                <p>Unique Visitors</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-pie-graph"></i>
                            </div>
                            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                </div>
                <!-- /.row -->

            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6">
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
                                            <th>Farmer Code</th>
                                            <th>Farmer Name</th>
                                            <th>Village Code</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($farmer as $key => $row)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $row->farmer_code }}</td>
                                                <td>{{ $row->farmer_name }}</td>
                                                <td>{{ $row->village_code }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.card-body -->

                        </div>


                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title"><b>Village</b> </h3>
                            </div>
                            <!-- /card-header -->
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width: 10px">Sr#</th>
                                            <th>Village Code</th>
                                            <th>Village Name</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($village as $key => $row)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $row->village_code }}</td>
                                                <td>{{ $row->village_title }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.card-body -->

                        </div>


                    </div>

                </div>
            </div>
        </section>
    </div>
@endsection
