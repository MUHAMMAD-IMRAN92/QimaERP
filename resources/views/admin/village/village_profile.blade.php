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

        .list-group .list-group-item {
            width: 100%;
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
            margin-left: 62%;
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

        .data-content-list {
            display: flex;
            align-items: center;
            padding-left: 4px;
            padding-right: 4px;
        }

        .data-content-list span {
            margin-left: 5px;
        }

    </style>
    <script>
        $(document).ready(function() {

            $('#to').on('change', function() {
                let from = $('#from').val();
                let to = $('#to').val();


                $.ajax({
                    url: "{{ url('admin/filter_village_profile/' . $village->village_id) }}",
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
                    url: "{{ url('admin/village_profile_days_filter/' . $village->village_id) }}",
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
                    url: "{{ url('admin/village_profile_days_filter/' . $village->village_id) }}",
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
                    url: "{{ url('admin/village_profile_days_filter/' . $village->village_id) }}",
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
                    url: "{{ url('admin/village_profile_days_filter/' . $village->village_id) }}",
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
                    url: "{{ url('admin/village_profile_days_filter/' . $village->village_id) }}",
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
                    url: "{{ url('admin/village_profile_days_filter/' . $village->village_id) }}",
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
                    url: "{{ url('admin/village_profile_days_filter/' . $village->village_id) }}",
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
                    url: "{{ url('admin/village_profile_days_filter/' . $village->village_id) }}",
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
                        <h1>Village Profile

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
                <span class="ml-2"> <a href="{{ url('admin/editvillage/' . $village->village_id) }}">EDIT
                        INFORMATION</a></span> &nbsp |

                <span class="ml-2"> <a href="{{ url('admin/editvillage/' . $village->village_id) }}"> OVERRIDE
                        PRICE
                    </a></span> &nbsp |
                <span class="ml-2"> <a href="">OVERRIDE REWARD</a></span>

            </div>
            <hr>
            <div class="row">
                <div class="col-md-4">
                    @if ($village->picture_id == null)
                        <td> <img class="famerimg" src="{{ asset('public/images/farmericon.png') }}"
                                style="width: 300px ; height:300px; border-radius:50%; border: 1px solid gray;" alt=""></td>
                    @else
                        <td> <img class="famerimg"
                                style="width: 300px ; height:300px; border-radius:50%; border: 1px solid gray;"
                                src="{{ asset('public/storage/images/' . $village->image) }}" alt=""></td>
                    @endif

                </div>
                <div class="col-md-8">
                    <table class="table table-borderless" id="farmerTable">

                        <tbody>
                            <tr>
                                <td colspan=""> <strong>Village</strong> </td>
                                <td colspan="4">{{ $village->village_title }}</td>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td colspan=""><strong>Region</strong></td>
                                <td colspan="4">{{ $village->region }}</td>
                                <td colspan="4"></td>
                            </tr>

                            <tr>
                                <td colspan=""><strong>GOVERNORATE</strong></td>
                                <td colspan="4">{{ $village->governrate }}</td>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td colspan=""><strong>ALTITUDE</strong></td>
                                <td colspan="4">pending</td>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td colspan=""><strong>VILLAGE INFORMATION</strong></td>
                                <td colspan="4">pending</td>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td colspan=""><strong>REGIONS INFORMATION</strong></td>
                                <td colspan="4">pending</td>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td colspan=""><strong>GOVERNORATE INFORMATION
                                    </strong></td>
                                <td colspan="4">pending</td>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td colspan=""><strong>NUMBER OF FARMERS
                                    </strong></td>
                                <td colspan="4">{{ count($village->farmers) }}</td>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td colspan=""><strong>PRICE PER KG</strong></td>
                                <td colspan="4">{{ $village->price_per_kg }}</td>
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
            <div class="row ml-2 blacklink">

                <a href="" class="ml-2">
                    <p>Photo Attachment &nbsp;</p>
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
                <span class="ml-2"> <a href="{{ route('village.profile', $village) }}">ALL
                        TIME</a></span>
            </div>
            <hr>

            <div class="row ml-2" id="transaction">
                <div class="col-sm-1 color bg-danger">
                    <h3 style="font-size: 16px !important">{{ $village->first_purchase }}</h3>
                    <p>First Purchade</p>
                </div>
                <div class="col-sm-1 color bg-primary">
                    <h3 style="font-size: 16px !important">{{ $village->last_purchase }} </h3>

                    <p>Last Purchase</p>
                </div>
                <div class="col-sm-1 color bg-warning">
                    <h3 style="font-size: 16px !important">{{ $village->price }}</h3>

                    <p>yer total coffee purchased </p>
                </div>
                <div class="col-sm-1 color bg-info">
                    <h3 style="font-size: 16px !important">{{ $village->quantity }}</h3>

                    <p>Quantity</p>
                </div>
                <div class="col-sm-1 color bg-dark"></div>
                <div class="col-sm-1 color bg-danger"></div>
                <div class="col-sm-1 color bg-success"></div>

            </div>
            <hr>



            <b>
                <p>Farmers Bought From
                </p>
            </b>

            <div class="row">
                @foreach ($village->farmers as $farmer)

                    <div class="col-md-4 mb-1">
                        <ul class="list-group list-group-horizontal">
                            <li class="list-group-item data-content-list">
                                @if ($farmer->file == null)
                                    <img src="{{ asset('public/images/farmericon.png') }}" id="region_farmer">
                                @else
                                    <img src="{{ asset('public/storage/images/' . $farmer->file->user_file_name) }}"
                                        id="region_farmer">
                                @endif

                                <span>{{ $farmer['farmer_name'] }}</span>
                            </li>

                        </ul>
                    </div>



                @endforeach
            </div>
            <hr>
            <b>
                <p>Coffee Buyers
                </p>
            </b>

            <div class="row">
                @foreach ($village->buyers as $buyer)

                    <div class="col-md-4 mb-1">
                        <ul class="list-group list-group-horizontal">
                            <li class="list-group-item data-content-list">
                                @if ($buyer->file == null)
                                    <img src="{{ asset('public/images/farmericon.png') }}" id="region_farmer">
                                @else
                                    <img src="{{ asset('public/storage/images/' . $buyer->file->user_file_name) }}"
                                        id="region_farmer">
                                @endif

                                <span>{{ $buyer['first_name'] }}</span>
                            </li>

                        </ul>
                    </div>



                @endforeach
            </div>

        </section>

        <!-- Main content -->

        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->


@endsection
