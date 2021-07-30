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
            margin-left: 2px;
        }

        .set-width {

            width: 90px;
            height: 70px fit-content;
            background-color: purple !important;
        }

        a {
            color: rgb(0, 0, 0);
        }

        .blacklink a {

            color: rgb(0, 0, 0);
            background-color: transparent;
            text-decoration: none;
            font-size: 14px;

        }

        .famerimg {

            width: 50px;
            border-radius: 50%;
        }

    </style>
    <script>
        $(document).ready(function() {
            $('#myTable').DataTable();
            $('#to').on('change', function() {
                let from = $('#from').val();
                let to = $('#to').val();

                $.ajax({
                    url: "{{ url('admin/filter_farmers') }}",
                    type: "GET",
                    data: {
                        'from': from,
                        'to': to
                    },
                    success: function(data) {

                        $('#famerstable').html(data);
                        console.log(data);
                    }
                });
            });
            $('#governorate_dropdown').on('change', function(e) {
                // let from = $('#governorate_dropdown').val();
                let from = e.target.value;
                $.ajax({
                    url: "{{ url('admin/filter_farmers_by_region') }}",
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
                        console.log(data);

                        $('#famerstable').html(data.view);


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
                        console.log(data);

                        $('#village_dropdown').append(html);
                        $('#famerstable').html(data.view);

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
        });
    </script>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">

        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">

                    <div class="col-sm-6">
                        <h1>FARMERS

                        </h1>
                    </div>
                    <hr>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <a href="{{ URL::to('') }}/admin/add_farmer" class="btn btn-add rounded-circle">
                                <button class="btn btn-dark">Add Farmer</button>
                            </a>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <div class="row ml-3">
            <strong>
                <b>Date Filter</b>
            </strong>
        </div>
        <div class="row ml-3">
            <form action="" method="POST" id="data-form">
                <label for="from">From</label>
                <input type="date" name="" id="from">
                <label for="To">To</label>
                <input type="date" name="" id="to">
            </form>
        </div>
        <br>
        <div class="row ml-3 blacklink ">
            <span class="ml-3"> <a href="{{ url('admin/farmer_by_date/' . ($date = 'today')) }}">TODAY</a></span> &nbsp |
            <span class="ml-3"> <a href="{{ url('admin/farmer_by_date/' . ($date = 'yesterday')) }}"> YESTERDAY</a></span>
            &nbsp |
            <span class="ml-3"> <a href="{{ url('admin/farmer_by_date/' . ($date = 'weekToDate')) }}"> WEEK TO DATE
                </a></span>
            &nbsp |
            <span class="ml-3"> <a href="{{ url('admin/farmer_by_date/' . ($date = 'monthToDate')) }}">MONTH TO
                    DATE</a></span>
            &nbsp |
            <span class="ml-3"> <a href="{{ url('admin/farmer_by_date/' . ($date = 'lastmonth')) }}"> LAST
                    MONTH</a></span>
            &nbsp |
            <span class="ml-3"> <a href="{{ url('admin/farmer_by_date/' . ($date = 'yearToDate')) }}">YEAR TO
                    DATE</a></span>
            &nbsp |
            <span class="ml-3"> <a href="{{ url('admin/farmer_by_date/' . ($date = 'currentyear')) }}"> 2021
                    SEASON</a></span>
            &nbsp |
            <span class="ml-3"> <a href="{{ url('admin/farmer_by_date/' . ($date = 'lastyear')) }}"> 2020
                    SEASON</a></span>
            &nbsp |
            <span class="ml-3"> <a href="{{ url('admin/allfarmer') }}"> ALL TIME</a></span>
        </div>
        <hr>
        <div class="row ml-3">
            <strong>
                <b>REGION FILTER</b>
            </strong>
        </div>
        <div class="row">
            <div class="col-md-12">
                <b class="ml-3"><a href=""> All Regions</a></b> |
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
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        @if (Session::has('message'))
                            <div class="alert alert-success" role="alert">
                                <b>{{ Session::get('message') }}</b>
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                            </div>
                        @endif
                        @if (Session::has('updatefarmer'))
                            <div class="alert alert-success" role="alert">
                                <b>{{ Session::get('updatefarmer') }}</b>
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                            </div>
                        @endif
                        <!-- /.card -->

                        <div class="card">
                            <!-- /.card-header -->
                            <div class="table-responsive" id="famerstable">
                                <table class="table" id="myTable" style="font-size:13px;">
                                    <thead>
                                        <tr align="center">
                                            <th></th>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Code</th>
                                            <th>First Purchase</th>
                                            <th>Last Purchase</th>
                                            <th>Governorate</th>
                                            <th>Region</th>
                                            <th>Village</th>
                                            <th>Quantity</th>
                                            <th>Coffe Bought</th>
                                            <th>Reward</th>
                                            <th>Money Owed</th>
                                            <th>Cupping Score</th>
                                            <th>Cup Profile</th>

                                            <th>View Details</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($farmers as $farmer)
                                            <tr>
                                                @if ($farmer->picture_id == null)
                                                    <td> <img class="famerimg"
                                                            src="{{ asset('public/images/farmericon.png') }}" alt="">
                                                    </td>
                                                @else
                                                    <td> <img class="famerimg"
                                                            src="{{ asset('storage/app/images/' . $farmer->image) }}"
                                                            alt=""></td>
                                                @endif

                                                <td>{{ $farmer->farmer_id }}</td>
                                                <td>{{ $farmer->farmer_name }}</td>
                                                <td>{{ $farmer->farmer_code }}</td>
                                                <td>{{ $farmer->first_purchase }}</td>
                                                <td>{{ $farmer->last_purchase }}</td>
                                                <td>{{ $farmer->governerate_title }}</td>
                                                <td>{{ $farmer->region_title }}</td>
                                                <td>{{ $farmer->village_title }}</td>
                                                <td>{{ number_format($farmer->quantity) }}</td>
                                                @if ($farmer->price_per_kg == null)
                                                    <td>{{ number_format($farmer->price * $farmer->quantity) }}</td>
                                                @else
                                                    <td>{{ number_format($farmer->price_per_kg * $farmer->quantity) }}
                                                    </td>
                                                @endif

                                                <td>{{ $farmer->id }}</td>
                                                <td>{{ $farmer->id }}</td>
                                                <td>{{ $farmer->id }}</td>
                                                <td>{{ $farmer->id }}</td>
                                                <td> <a href="{{ route('farmer.profile', $farmer) }}"><i
                                                            class="fas fa-eye"></i></a></td>


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
