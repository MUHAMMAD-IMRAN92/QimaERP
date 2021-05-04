@extends('layouts.default')
@section('title', 'All Regions')
@section('content')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <style type="text/css">
        .dataTables_wrapper .dataTables_filter input {
            margin-left: 0.5em;
            width: 240px;
        }

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
            $('#myTable').DataTable();
            $('#to').on('change', function() {
                let from = $('#from').val();
                let to = $('#to').val();

                $.ajax({

                    url: "{{ url('admin/regionByDate') }}",
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
            $('#governorate_dropdown').on('change', function(e) {
                // let from = $('#governorate_dropdown').val();
                let from = e.target.value;
                $.ajax({
                    url: "{{ url('admin/filterRegionByGovernrate') }}",
                    type: "GET",
                    data: {
                        'from': from,

                    },
                    success: function(data) {
                        $('#regions_dropdown').empty();

                        let html =
                            ' <option value="0" selected disabled>Select Region</option>';
                        data.regions.forEach(region => {
                            html += '<option value="' + region.region_id + '">' + region
                                .region_title + '</option>';
                        });

                        $('#regions_dropdown').append(html);
                        $('#transactions').html(data.view);
                        console.log(data);
                    }
                });
            });
            $('#regions_dropdown').on('change', function(e) {
                // let from = $('#regions_dropdown').val();
                let from = e.target.value;
                $.ajax({
                    url: "{{ url('admin/filter_villages') }}",
                    type: "GET",
                    data: {
                        'from': from,

                    },
                    success: function(data) {
                        $('#village_dropdown').empty();
                        let html =
                            ' <option value="0" selected disabled>Select Village</option>';
                        data.villages.forEach(village => {
                            html += '<option value="' + village.village_id + '">' +
                                village
                                .village_title + '</option>';
                        });
                        console.log(data.region);

                        $('#village_dropdown').append(html);


                    }
                });
            });
            $('#village_dropdown').on('change', function(e) {
                // let from = $('#regions_dropdown').val();
                let from = e.target.value;
                $.ajax({
                    url: "{{ url('admin/farmer_by_villages') }}",
                    type: "GET",
                    data: {
                        'from': from,

                    },
                    success: function(data) {
                        $('#famerstable').html(data);
                        console.log(data);
                    }
                });
            });
            $('#today').on('click', function() {
                $.ajax({
                    url: "{{ url('admin/regionByDays') }}",
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
                    url: "{{ url('admin/regionByDays') }}",
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
                    url: "{{ url('admin/regionByDays') }}",
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
                    url: "{{ url('admin/regionByDays') }}",
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
                    url: "{{ url('admin/regionByDays') }}",
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
                    url: "{{ url('admin/regionByDays') }}",
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
                    url: "{{ url('admin/regionByDays') }}",
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
                    url: "{{ url('admin/regionByDays') }}",
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
        @if (Session::has('message'))
            <div class="alert alert-success" role="alert">
                <b>{{ Session::get('message') }}</b>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">

                    <div class="col-sm-6">
                        <h1>Region

                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item btn btn-success"><a href="{{ url('admin/addnewvillage') }}">Add
                                    Village</a></li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
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
            <span class="ml-2"> <a href="{{ url('/admin/allregion') }}">ALL
                    TIME</a></span>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <b class="ml-2"><a href=""> All Regions</a></b> |
                Governrate <select name="" id="governorate_dropdown">
                    <option value="0" selected disabled>Select Governrate</option>
                    @foreach ($governorates as $governorate)
                        <option value="{{ $governorate->governerate_id }}">{{ $governorate->governerate_title }}
                        </option>
                    @endforeach

                </select>
                Sub Region <select name="" id="regions_dropdown">
                    <option value="0" selected disabled>Select Region</option>
                    @foreach ($regions as $region)
                        <option value="{{ $region->region_id }}">{{ $region->region_title }}</option>
                    @endforeach
                </select>
                Village <select name="" id="village_dropdown">
                    <option value="0" selected disabled>Select Village</option>
                    @foreach ($villages as $village)
                        <option value="{{ $village->village_id }}">{{ $village->village_title }}</option>
                    @endforeach
                </select>
            </div>

        </div>
        <hr>
        <div class="row ml-2" id="transactions">
            <div class="col-sm-1 color bg-danger">
                <h4>{{ count($governorates) }}</h4>
                <p>Governorate</p>
            </div>
            <div class="col-sm-1 color bg-primary">
                <h4>{{ count($regions) }}</h4>

                <p>Regions</p>
            </div>
            <div class="col-sm-1 color bg-warning">
                <h4>{{ count($villages) }}</h4>

                <p>Villages </p>
            </div>
            <div class="col-sm-1 color bg-info">
                <h4>{{ $total_coffee }}</h4>
                <p>Quantity </p>
            </div>
            <div class="col-sm-1 color bg-dark">
                <h4>{{ $totalPrice }}</h4>
                <p>yer coffee bought </p>
            </div>
            <div class="col-sm-1 color bg-danger">

            </div>
            <div class="col-sm-1 color bg-warning"></div>
            <div class="col-sm-1 color bg-info"></div>
            <div class="col-sm-1 color bg-dark"></div>
        </div>
        <hr>
        <div class="row ml-2">
            <h5>QUANTITY CHERRY BOUGHT</h5>
        </div>
        <div class="row">
            <div class="col-md-11 ml-4">
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
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">

                        <!-- /.card -->

                        <div class="card">
                            <!-- /.card-header -->
                            <div class="card-body">

                                <table class="table table-striped">
                                    <thead>
                                        <tr>

                                            <th>Governorates </th>
                                            <th>Regions </th>
                                            <th>Villages </th>
                                            <th>Quantity</th>
                                            <th>Values</th>
                                            <th>Farmers</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($governorates as $governorate)
                                            <tr>

                                                <td>{{ $governorate->governerate_title }}</td>
                                                <td>
                                                    @foreach ($governorate->regions as $region)
                                                        {{ $region->region_title }} <br>
                                                    @endforeach
                                                </td>
                                                <td>
                                                    @if ($governorate->villages)

                                                        @foreach ($governorate->villages as $village)
                                                            {{ $village->village_title }} <br>
                                                        @endforeach

                                                    @endif
                                                </td>

                                                <td>
                                                    @if ($governorate->villages)

                                                        @foreach ($governorate->villages as $village)
                                                            {{ $village->weight }} <br>
                                                        @endforeach
                                                    @endif
                                                </td>

                                                <td>
                                                    @if ($governorate->villages)

                                                        @foreach ($governorate->villages as $village)
                                                            {{ $village->weight * $village->price }} <br>
                                                        @endforeach
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($governorate->villages)

                                                        @foreach ($governorate->villages as $village)
                                                            {{ $village->farmers }} <br>
                                                        @endforeach
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
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
    <!-- /.content-wrapper -->

@endsection
