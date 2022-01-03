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
                $('.all_farmers').css({
                    'font-weight': 'normal',
                    'text-decoration': 'none'
                });
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
                $('.all_farmers').css({
                    'font-weight': 'normal',
                    'text-decoration': 'none'
                });
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
                $('.all_farmers').css({
                    'font-weight': 'normal',
                    'text-decoration': 'none'
                });
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
            $(".blacklink .hover").each(function(i, obj) {
                if ($("a", obj).attr("href") == window.location.href) {
                    $(this).css({
                        'font-weight': 'bold',
                        'text-decoration': 'underline'
                    });
                    console.log($("a", this).attr("href"), 'hello', window.location.href);
                } else {
                    console.log($("a", this).attr("href"), 'hello11', window.location.href);

                }

            });
        });
    </script>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <div class="mx-lg-5">

            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2 border-bottom-lightGray">

                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark text-uppercase font-weight-lighter text-heading">FARMERS

                            </h1>
                        </div>
                        <hr>
                        <div class="col-sm-6 d-flex justify-content-end align-items-end">
                            <ol class="breadcrumb float-sm-right">
                                <a href="{{ URL::to('') }}/admin/add_farmer" class="btn btn-add rounded-circle">
                                    <button class="btn btn-dark bg-transparent border-0 add-button text-uppercase">Add
                                        Farmer</button>
                                </a>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>
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
                <span class="hover"> <a
                        href="{{ url('admin/farmer_by_date/' . ($date = 'today')) }}">TODAY</a></span> &nbsp |
                <span class="ml-md-2 hover"> <a href="{{ url('admin/farmer_by_date/' . ($date = 'yesterday')) }}">
                        YESTERDAY</a></span>
                &nbsp |
                <span class="ml-md-2 hover"> <a href="{{ url('admin/farmer_by_date/' . ($date = 'weekToDate')) }}"> WEEK
                        TO
                        DATE
                    </a></span>
                &nbsp |
                <span class="ml-md-2 hover"> <a href="{{ url('admin/farmer_by_date/' . ($date = 'monthToDate')) }}">MONTH
                        TO
                        DATE</a></span>
                &nbsp |
                <span class="ml-md-2 hover"> <a href="{{ url('admin/farmer_by_date/' . ($date = 'lastmonth')) }}"> LAST
                        MONTH</a></span>
                &nbsp |
                <span class="ml-md-2 hover"> <a href="{{ url('admin/farmer_by_date/' . ($date = 'yearToDate')) }}">YEAR
                        TO
                        DATE</a></span>
                &nbsp |
                <span class="ml-md-2 hover"> <a href="{{ url('admin/farmer_by_date/' . ($date = 'currentyear')) }}">
                        2022
                        SEASON</a></span>
                &nbsp |
                <span class="ml-md-2 hover"> <a href="{{ url('admin/farmer_by_date/' . ($date = 'lastyear')) }}"> 2021
                        SEASON</a></span>
                &nbsp |
                <span class="ml-md-2 hover"> <a href="{{ url('admin/allfarmer') }}"> ALL TIME</a></span>
            </div>
            <hr class="ml-md-2">
            <div class="row ml-2 text-uppercase mb-2">
                <strong>
                    <b>REGION FILTER</b>
                </strong>
            </div>
            <div class="row ml-2">
                <div class="col-md-12 pl-0 text-uppercase">
                    <span class="all_farmers" style="font-weight: bold; text-decoration: underline;">
                        <a href=""> All Regions</a>
                    </span>
                    &nbsp |
                    <span class="ml-md-2">
                        Governrate
                    </span>
                    <select name="" id="governorate_dropdown">
                        <option value="0" selected disabled>Select Governrate</option>
                        @foreach ($governorates as $governorate)
                            <option value="{{ $governorate->governerate_id }}">{{ $governorate->governerate_title }}
                            </option>
                        @endforeach

                    </select>
                    <span class="ml-md-2">
                        Sub Region
                    </span>
                    <select name="" id="regions_dropdown">
                        <option value="0" selected disabled>Select Region</option>
                        @foreach ($regions as $region)
                            <option value="{{ $region->region_id }}">{{ $region->region_title }}</option>
                        @endforeach
                    </select>
                    <span class="ml-md-2">
                        Village
                    </span>
                    <select name="" id="village_dropdown">
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

                            <div class="card shadow-none">
                                <!-- /.card-header -->
                                <div class="table-responsive text-uppercase letter-spacing-2" id="famerstable">

                                    <table class="table table-borderless border-0 custom-table text-center" id="myTable"
                                        style="font-size:13px;">
                                        <thead>
                                            <tr align="center">
                                                <th class="border-0"></th>
                                                <!-- <th class="border border-dark">ID</th> -->
                                                <th class="border border-dark font-weight-lighter">Name</th>
                                                <th class="border border-dark border-left-0 font-weight-lighter">Code</th>
                                                <th class="border border-dark border-left-0 font-weight-lighter">First
                                                    Purchase</th>
                                                <th class="border border-dark border-left-0 font-weight-lighter">Last
                                                    Purchase</th>
                                                <th class="border border-dark border-left-0 font-weight-lighter">Governorate
                                                </th>
                                                <th class="border border-dark border-left-0 font-weight-lighter">Region</th>
                                                <th class="border border-dark border-left-0 font-weight-lighter">Village
                                                </th>
                                                <th class="border border-dark border-left-0 font-weight-lighter">Quantity
                                                </th>
                                                <th class="border border-dark border-left-0 font-weight-lighter">Coffee
                                                    Bought</th>
                                                <th class="border border-dark border-left-0 font-weight-lighter">Price Paid
                                                </th>
                                                <th class="border border-dark border-left-0 font-weight-lighter">Reward</th>
                                                <th class="border border-dark border-left-0 font-weight-lighter">Money Owed
                                                </th>
                                                <th class="border border-dark border-left-0 font-weight-lighter">Cupping
                                                    Score</th>
                                                <th class="border border-dark border-left-0 font-weight-lighter">Cup Profile
                                                </th>

                                                <th class="border border-dark border-left-0 font-weight-lighter">View
                                                    Details</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($farmers as $farmer)
                                                <tr>
                                                    @if ($farmer->picture_id == null)
                                                        <td class="border-0"> <img class="famerimg mr-2"
                                                                src="{{ Storage::disk('s3')->url('images/' . 'dumy.png') }}"
                                                                alt="">
                                                        </td>
                                                    @else
                                                        <td class="border-0"> <img class="famerimg mr-2"
                                                                src="{{ Storage::disk('s3')->url('images/' . $farmer->image) }}"
                                                                alt=""></td>
                                                    @endif

                                                    <!-- <td class="border border-dark border-top-0">{{ $farmer->farmer_id }}</td> -->
                                                    <td class="border border-dark border-top-0">
                                                        {{ $farmer->farmer_name }}</td>
                                                    <td class="border border-dark border-left-0 border-top-0">
                                                        {{ $farmer->farmer_code }}</td>
                                                    <td class="border border-dark border-left-0 border-top-0">
                                                        {{ $farmer->first_purchase }}</td>
                                                    <td class="border border-dark border-left-0 border-top-0">
                                                        {{ $farmer->last_purchase }}</td>
                                                    <td class="border border-dark border-left-0 border-top-0">
                                                        {{ $farmer->governerate_title }}</td>
                                                    <td class="border border-dark border-left-0 border-top-0">
                                                        {{ $farmer->region_title }}</td>
                                                    <td class="border border-dark border-left-0 border-top-0">
                                                        {{ $farmer->village_title }}</td>
                                                    <td class="border border-dark border-left-0 border-top-0">
                                                        {{ number_format($farmer->quantity) }}</td>
                                                    @if ($farmer->price_per_kg == null || $farmer->price_per_kg == 0)
                                                        <td class="border border-dark border-left-0 border-top-0">
                                                            {{ number_format($farmer->price * $farmer->quantity) }}</td>
                                                    @else
                                                        <td class="border border-dark border-left-0 border-top-0">
                                                            {{ number_format($farmer->price_per_kg * $farmer->quantity) }}
                                                        </td>
                                                    @endif
                                                    <td class="border border-dark border-left-0 border-top-0">
                                                        {{ $farmer->paidprice }}</td>
                                                    <td class="border border-dark border-left-0 border-top-0">
                                                        {{ $farmer->reward }}</td>
                                                    <td class="border border-dark border-left-0 border-top-0">
                                                        {{ $farmer->id }}</td>
                                                    <td class="border border-dark border-left-0 border-top-0">
                                                        {{ $farmer->cupping_score }}</td>
                                                    <td class="border border-dark border-left-0 border-top-0">
                                                        {{ $farmer->cup_profile }}</td>
                                                    <td class="border border-dark border-left-0 border-top-0"> <a
                                                            href="{{ route('farmer.profile', $farmer) }}"><i
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
    </div>
    <!-- /.content-wrapper -->

@endsection
