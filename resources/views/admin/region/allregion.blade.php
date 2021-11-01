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

            width: 200px;
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
                $('.all_regions').css({'font-weight':'normal', 'text-decoration':'none'});
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

                    }
                });
            });
            $('#regions_dropdown').on('change', function(e) {
                // let from = $('#regions_dropdown').val();
                $('.all_regions').css({'font-weight':'normal', 'text-decoration':'none'});

                let from = e.target.value;
                $.ajax({
                    url: "{{ url('admin/filterRegionByRegions') }}",
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


                        $('#village_dropdown').append(html);
                        $('#transactions').html(data.view);
                        console.log(data);


                    }
                });
            });
            $('#village_dropdown').on('change', function(e) {
                // let from = $('#regions_dropdown').val();
                $('.all_regions').css({'font-weight':'normal', 'text-decoration':'none'});

                let from = e.target.value;
                $.ajax({
                    url: "{{ url('admin/filterRegionByVillages') }}",
                    type: "GET",
                    data: {
                        'from': from,

                    },
                    success: function(data) {
                        $('#transactions').html(data.view);
                        console.log(data);
                    }
                });
            });
            $('#today').on('click', function() {
                $('.blacklink .hover').css({'font-weight':'normal', 'text-decoration':'none'});
                $(this).css({'font-weight':'bold', 'text-decoration':'underline'});
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
                $('.blacklink .hover').css({'font-weight':'normal', 'text-decoration':'none'});
                $(this).css({'font-weight':'bold', 'text-decoration':'underline'});
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
                $('.blacklink .hover').css({'font-weight':'normal', 'text-decoration':'none'});
                $(this).css({'font-weight':'bold', 'text-decoration':'underline'});
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
                $('.blacklink .hover').css({'font-weight':'normal', 'text-decoration':'none'});
                $(this).css({'font-weight':'bold', 'text-decoration':'underline'});
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
                $('.blacklink .hover').css({'font-weight':'normal', 'text-decoration':'none'});
                $(this).css({'font-weight':'bold', 'text-decoration':'underline'});
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
                $('.blacklink .hover').css({'font-weight':'normal', 'text-decoration':'none'});
                $(this).css({'font-weight':'bold', 'text-decoration':'underline'});
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
                $('.blacklink .hover').css({'font-weight':'normal', 'text-decoration':'none'});
                $(this).css({'font-weight':'bold', 'text-decoration':'underline'});
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
                $('.blacklink .hover').css({'font-weight':'normal', 'text-decoration':'none'});
                $(this).css({'font-weight':'bold', 'text-decoration':'underline'});
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
        <div class="mx-lg-5">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">

                    <div class="col-sm-6 pl-0">
                        <h1 class="m-0 text-dark text-uppercase font-weight-lighter text-heading">Region

                        </h1>
                    </div>
                    <div class="col-sm-6 d-flex justify-content-end align-items-end">
                        <ol class="breadcrumb float-sm-right">
                                <a href="{{ url('admin/addnewregion') }}" class="btn btn-add rounded-circle">
                                    <button class="btn btn-dark bg-transparent border-0 add-button text-uppercase">Add Region</button>
                                </a>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
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
            <span class="ml-2 hover" id="yearToDate"> YEAR TO
                DATE</a></span>
            &nbsp |
            <span class="ml-md-2 hover" id="currentyear"> 2021
                SEASON</a></span>
            &nbsp |
            <span class="ml-md-2 hover" id="lastyear"> 2020
                SEASON</a></span>
            &nbsp |
            <span class="ml-md-2 hover" style="font-weight: bold; text-decoration: underline;"> <a href="{{ url('/admin/allregion') }}">ALL
                    TIME</a></span>
        </div>
        <hr class="ml-md-2">
        <div class="row ml-2 text-uppercase mb-2">
                <strong>
                    <b>Region Filter</b>
                </strong>
        </div>
        <div class="row row ml-2 blacklink letter-spacing-1">
            <div class="col-md-12 pl-0 text-uppercase">
            <span class="all_regions" style="font-weight: bold; text-decoration: underline;">
                <a href="" > All Regions</a>
            </span>
                 &nbsp |
                 <span class="ml-md-2">
                Governrate
                 </span>
                 <select class="ml-md-2" name="" id="governorate_dropdown">
                    <option value="0" selected disabled>Select Governrate</option>
                    @foreach ($governorates as $governorate)
                        <option value="{{ $governorate->governerate_id }}">{{ $governorate->governerate_title }}
                        </option>
                    @endforeach

                </select>
                <span class="ml-md-2">
                Sub Region
                </span>
                <select class="ml-md-2" name="" id="regions_dropdown">
                    <option value="0" selected disabled>Select Region</option>
                    @foreach ($regions as $region)
                        <option value="{{ $region->region_id }}">{{ $region->region_title }}</option>
                    @endforeach
                </select>
                <span class="ml-md-2">
                Village
                </span>
                <select class="ml-md-2" name="" id="village_dropdown">
                    <option value="0" selected disabled>Select Village</option>
                    @foreach ($villages as $village)
                        <option value="{{ $village->village_id }}">{{ $village->village_title }}</option>
                    @endforeach
                </select>
            </div>

        </div>
        <hr>
        <div class="col-lg-12 ml-md-2 text-uppercase d-flex flex-wrap p-0 mb-3 data-tabs" id="transactions">
            <div class="col-sm-1 color bg-darkPurple p-2 content-box">
                <h4>{{ count($governorates) }}</h4>
                <p>Governorate</p>
            </div>
            <div class="col-sm-1 color bg-darkGreen p-2 content-box">
                <h4>{{ count($regions) }}</h4>

                <p>Regions</p>
            </div>
            <div class="col-sm-1 color bg-darkGreen p-2 content-box">
                <h4>{{ count($villages) }}</h4>

                <p>Villages </p>
            </div>
            <div class="col-sm-1 color bg-Green p-2 content-box">
                <h4>{{ App\Farmer::count() }}</h4>

                <p>Farmers </p>
            </div>
            <div class="col-sm-1 color bg-darkRed p-2 content-box">
                <h4>{{ $total_coffee }}</h4>
                <p>Quantity </p>
            </div>
            <div class="col-sm-1 color bg-darkGreen p-2 content-box">
                <h4>{{ $totalPrice }}</h4>
                <p>yer coffee bought </p>
            </div>
            <div class="col-sm-1 color bg-lightBrown p-2 content-box">

            </div>
            <div class="col-sm-1 color bg-darkPurple p-2 content-box"></div>
            <div class="col-sm-1 color bg-darkPurple p-2 content-box"></div>
            <div class="col-sm-1 color bg-darkPurple p-2 content-box"></div>
            <div class="col-sm-1 color bg-lightGreen p-2 content-box"></div>
        </div>
        <hr>
        <div class="row ml-2 text-uppercase mb-2">
                <strong>
                    <b>QUANTITY CHERRY BOUGHT</b>
                </strong>
        </div>
        <div class="row">
            <div class="col-md-11 ml-4">
                <canvas id="myChart" style="width:100%;max-height:500px"></canvas>

                <script>
                    var xValues = @json($regionName);
                    var yValues = @json($regionQuantity);

                    new Chart("myChart", {
                        type: "bar",
                        data: {
                            labels: xValues,
                            datasets: [{
                                fill: true,
                                lineTension: 0,
                                // backgroundColor: "rgba(0,0,255,0.2)",
                                backgroundColor: "purple",
                                borderColor: "purple",
                                // borderColor: "rgba(0,0,255,0.1)",
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
                                        max: 6000
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
                    <div>

                        <!-- /.card -->

                        <div class="card">
                            <!-- /.card-header -->
                            <div class="">

                                <table class="table table-bordered region-table-custom">
                                    <thead>
                                        <tr class="blacklink letter-spacing-1 text-uppercase">

                                            <th>Governorate / </th>
                                            <th>Region / </th>
                                            <th>Villages / </th>
                                            <th>Quantity /</th>
                                            <th>Value /</th>
                                            <th>Farmers /</th>
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
    </div>
    <!-- /.content-wrapper -->

@endsection
