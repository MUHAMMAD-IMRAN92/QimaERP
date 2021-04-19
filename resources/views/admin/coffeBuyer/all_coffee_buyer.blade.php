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
            background-color: transparent;
            text-decoration: none;

        }

        .famerimg {

            width: 20px;
            border-radius: 50%;
        }

        .adduser a {
            color: rgb(182, 18, 18);

        }

        .gap {
            width: 10% !important;
            border: none !important;
            text-align: center !important;
        }

    </style>

    <script>
        $(document).ready(function() {
            $('#myTable').DataTable();
            $('#to').on('change', function() {
                let from = $('#from').val();
                let to = $('#to').val();

                $.ajax({
                    url: "{{ url('admin/filtercoffeebuyer') }}",
                    type: "GET",
                    data: {
                        'from': from,
                        'to': to
                    },
                    success: function(data) {
                        console.log(data);
                        $('#tables').html(data);

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
                        for (let [key, element] of Object.entries(data)) {
                            html += '<option value="' + element.region_id + '">' + element
                                .region_title + '</option>';
                        }

                        $('#regions_dropdown').append(html);
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
                        for (let [key, element] of Object.entries(data.villages)) {
                            html += '<option value="' + element.village_id + '">' + element
                                .village_title + '</option>';
                        }
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
                        // $('#tables').html(data);
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
                        <h1>COFFE BUYERS
                            {{-- <a href="{{ URL::to('') }}/admin/add_farmer" class="btn btn-add rounded-circle">
                                <i class="fas fa-user-plus add-client-icon"></i>
                            </a> --}}
                        </h1>
                    </div>
                    <hr>
                    <div class="col-sm-4 adduser ml-3">
                        <a href="{{ url('admin/adduser') }}">Add Coffee Buyer</a> &nbsp;|
                        <a href="{{ url('admin/adduser') }}">Add Coffee Buyer Manger</a>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
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

        <br>
        <div class="row ml-2 blacklink ">
            <span class="ml-2"> <a href="{{ url('admin/coffeeBuyerByDate/' . ($date = 'today')) }}">TODAY</a></span> &nbsp
            |
            <span class="ml-2"> <a href="{{ url('admin/coffeeBuyerByDate/' . ($date = 'yesterday')) }}">
                    YESTERDAY</a></span>
            &nbsp |
            <span class="ml-2"> <a href="{{ url('admin/coffeeBuyerByDate/' . ($date = 'weekToDate')) }}"> WEEK TO DATE
                </a></span>
            &nbsp |
            <span class="ml-2"> <a href="{{ url('admin/coffeeBuyerByDate/' . ($date = 'monthToDate')) }}">MONTH TO
                    DATE</a></span>
            &nbsp |
            <span class="ml-2"> <a href="{{ url('admin/coffeeBuyerByDate/' . ($date = 'lastmonth')) }}"> LAST
                    MONTH</a></span>
            &nbsp |
            <span class="ml-2"> <a href="{{ url('admin/coffeeBuyerByDate/' . ($date = 'yearToDate')) }}">YEAR TO
                    DATE</a></span>
            &nbsp |
            <span class="ml-2"> <a href="{{ url('admin/coffeeBuyerByDate/' . ($date = 'currentyear')) }}"> 2021
                    SEASON</a></span>
            &nbsp |
            <span class="ml-2"> <a href="{{ url('admin/coffeeBuyerByDate/' . ($date = 'lastyear')) }}"> 2020
                    SEASON</a></span>
            &nbsp |
            <span class="ml-2"> <a href="{{ url('admin/allcoffeebuyer') }}"> ALL TIME</a></span>
        </div>
        <hr>
        <div class="row ml-2">
            <strong>
                <b>REGION FILTER</b>
            </strong>
        </div>
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
                        <div class="row ml-2" id="tables">
                            <div class="col-md-12">
                                <div class="row ">
                                    <div class="col-md-6">
                                        <caption> <b> Coffee Buying Manger</b></caption>
                                        <table class="table table-bordered" style="font-size:13px;">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th>Name</th>
                                                    <th>First Purchase</th>
                                                    <th>Last Purchase</th>
                                                    <th>City</th>
                                                    <th class="gap"></th>
                                                    <th>CHREEY BOUGHT</th>
                                                    <th>PRICE PAID</th>
                                                    <th class="gap"></th>
                                                    <th> <span>DRY COFFEE</span> BOUGHT</th>
                                                    <th>PRICE PAID</th>
                                                    <th class="gap"></th>
                                                    <th>Total Price Paid</th>
                                                    <th>View Details</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($coffeeBuyerMangers as $coffeeBuyerManger)
                                                    <tr>

                                                        @if ($coffeeBuyerManger->picture_id == null)
                                                            <td> <img class="famerimg"
                                                                    src="{{ asset('public/dist/img/farmericon.png') }}"
                                                                    alt="">
                                                            </td>
                                                        @else
                                                            <td> <img class="famerimg"
                                                                    src="{{ asset('public/storage/images/' . $coffeeBuyerManger->image) }}"
                                                                    alt=""></td>
                                                        @endif
                                                        <td>{{ $coffeeBuyerManger->first_name }}</td>
                                                        <td>{{ $coffeeBuyerManger->first_purchase }}</td>
                                                        <td>{{ $coffeeBuyerManger->last_purchase }}</td>

                                                        <td>lahore</td>
                                                        <td class="gap"></td>
                                                        <td>{{ $coffeeBuyerManger->special_weight }}</td>
                                                        <td>{{ $coffeeBuyerManger->special_price }}</td>
                                                        <td class="gap"></td>
                                                        <td>{{ $coffeeBuyerManger->non_special_weight }}</td>
                                                        <td>{{ $coffeeBuyerManger->non_special_price }}</td>
                                                        <td class="gap"></td>

                                                        <td>{{  $coffeeBuyerManger->special_price +  $coffeeBuyerManger->non_special_price  }}</td>

                                                        <td> <a
                                                                href="{{ route('coffeBuyer.profile', $coffeeBuyerManger) }}"><i
                                                                    class="fas fa-eye"></i></a></td>

                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    {{-- <div class="col-md-2 ml-2">
                                            <caption>Specialty</caption>
                                            <table class="table table-bordered">

                                                <thead>
                                                    <tr>
                                                        <td>CHREEY BOUGHT</td>
                                                        <td>PRICE PAID</td>

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>John</td>
                                                        <td>Doe</td>

                                                    </tr>

                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="col-md-2 mi-2">
                                            <caption>Non-Specialty</caption>
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <td> <span>DRY COFFEE</span> BOUGHT</td>
                                                        <td>PRICE PAID</td>

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>John</td>
                                                        <td>Doe</td>

                                                    </tr>

                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="col-md-1 ml-2">
                                            <caption>&nbsp;</caption>
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <td>Firstname</td>


                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>John</td>


                                                    </tr>

                                                </tbody>
                                            </table>
                                        </div> --}}
                                </div>
                                <hr>

                                <div class="row ">
                                    <div class="col-md-6">
                                        <caption> <b> Coffee Buyer</b></caption>
                                        <table class="table table-bordered" style="font-size:13px;">
                                            <thead>
                                                <tr align="center" >
                                                    <th></th>
                                                    <th>Name</th>
                                                    <th>First Purchase</th>
                                                    <th>Last Purchase</th>
                                                    <th>City</th>
                                                    <th class="gap"></th>
                                                    <th>CHREEY BOUGHT</th>
                                                    <th>PRICE PAID</th>
                                                    <th class="gap"></th>
                                                    <th> <span>DRY COFFEE</span> BOUGHT</th>
                                                    <th>PRICE PAID</th>
                                                    <th class="gap"></th>
                                                    <th>Total Price Paid</th>
                                                    <th>View Details</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($coffeeBuyers as $coffeeBuyer)
                                                    <tr>

                                                        @if ($coffeeBuyer->picture_id == null)
                                                            <td> <img class="famerimg"
                                                                    src="{{ asset('public/dist/img/farmericon.png') }}"
                                                                    alt="">
                                                            </td>
                                                        @else
                                                            <td> <img class="famerimg"
                                                                    src="{{ asset('public/storage/images/' . $coffeeBuyer->image) }}"
                                                                    alt=""></td>
                                                        @endif
                                                        <td>{{ $coffeeBuyer->first_name }}</td>
                                                        <td>{{ $coffeeBuyer->first_purchase }}</td>
                                                        <td>{{ $coffeeBuyer->last_purchase }}</td>

                                                        <td>lahore</td>
                                                        <td class="gap"></td>
                                                        <td>{{ $coffeeBuyer->special_weight }}</td>
                                                        <td>{{ $coffeeBuyer->special_price }}</td>
                                                        <td class="gap"></td>
                                                        <td>{{ $coffeeBuyer->non_special_weight }}</td>
                                                        <td>{{ $coffeeBuyer->non_special_price }}</td>
                                                        <td class="gap"></td>

                                                        <td>{{  $coffeeBuyer->special_price +  $coffeeBuyer->non_special_price  }}</td>

                                                        <td> <a href="{{ route('coffeBuyer.profile', $coffeeBuyer) }}"><i
                                                                    class="fas fa-eye"></i></a></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    {{-- <div class="col-md-2 ml-2">
                                            <caption>Specialty</caption>
                                            <table class="table table-bordered">

                                                <thead>
                                                    <tr>
                                                        <td>CHREEY BOUGHT</td>
                                                        <td>PRICE PAID</td>

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>John</td>
                                                        <td>Doe</td>

                                                    </tr>

                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="col-md-2 mi-2">
                                            <caption>Non-Specialty</caption>
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <td> <span>DRY COFFEE</span> BOUGHT</td>
                                                        <td>PRICE PAID</td>

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>John</td>
                                                        <td>Doe</td>

                                                    </tr>

                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="col-md-1 ml-2">
                                            <caption>&nbsp;</caption>
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <td>Firstname</td>


                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>John</td>


                                                    </tr>

                                                </tbody>
                                            </table>
                                        </div> --}}
                                </div>
                            </div>
                        </div>
                        <!-- /.card -->

                    </div>
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
