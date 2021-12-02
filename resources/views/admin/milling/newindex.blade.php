@extends('layouts.default')
@section('title', 'Transection Detail')
@section('content')

    <style type="text/css">
        .nav.nav-tabs {
            float: left;
            display: block;
            margin-right: 20px;
            border-bottom: 0;
            border-right: 1px solid #ddd;
            padding-right: 15px;
        }

        .nav-tabs .nav-link {
            border: 1px solid transparent;
            border-top-left-radius: .25rem;
            border-top-right-radius: .25rem;
            background: #ccc;
        }

        .nav-tabs .nav-link.active {
            color: #495057;

            border-color: transparent !important;
        }

        .nav-tabs .nav-link {
            border: 1px solid transparent;
            border-top-left-radius: 0rem !important;
            border-top-right-radius: 0rem !important;
        }

        .tab-content>.active {

            display: block;
            /*background: #007bff;*/
            min-height: 165px;
        }

        .nav.nav-tabs {
            float: left;
            display: block;
            margin-right: 20px;
            border-bottom: 0;
            border-right: 1px solid transparent;
            padding-right: 15px;
        }

        #custom_tab li.nav-item a {
            color: #000;
            margin-bottom: 0px;
        }

        .batchnumber thead tr {
            border-bottom: 1px solid black;
        }

        .batchnumber tbody tr {
            border-bottom: 1px solid black;
        }

        .set-padding {
            padding: 10px;
        }

        .top-margin-set {
            margin-top: 10px;
        }

        #submitbtn {
            background-color: rgb(255, 255, 255);
            border-color: rgb(255, 255, 255);
            color: rgb(19, 17, 17);
            font-weight: bold;
        }

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
    <style href="//cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css"></style>
    <script src="//cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>

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
                        $('#transactionGraph').html(data);
                        console.log(data);
                    }
                });
            });
            $('#governorate_dropdown').on('change', function(e) {
                // let from = $('#governorate_dropdown').val();
                $('.all_regions').css({
                    'font-weight': 'normal',
                    'text-decoration': 'none'
                });
                let from = e.target.value;
                $.ajax({
                    url: "{{ url('admin/filterRegionByGovernrate') }}",
                    type: "GET",
                    data: {
                        'from': from,

                    },
                    success: function(data) {
                        console.log(data);
                        $('#regions_dropdown').empty();

                        let html =
                            ' <option value="0" selected disabled>Select Region</option>';
                        data.regions.forEach(region => {
                            html += '<option value="' + region.region_id + '">' + region
                                .region_title + '</option>';
                        });

                        $('#regions_dropdown').append(html);
                        $('#transactionGraph').html(data.view);

                    }
                });
            });
            $('#regions_dropdown').on('change', function(e) {
                // let from = $('#regions_dropdown').val();
                $('.all_regions').css({
                    'font-weight': 'normal',
                    'text-decoration': 'none'
                });

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
                        $('#transactionGraph').html(data.view);
                        console.log(data);


                    }
                });
            });
            $('#village_dropdown').on('change', function(e) {
                // let from = $('#regions_dropdown').val();
                $('.all_regions').css({
                    'font-weight': 'normal',
                    'text-decoration': 'none'
                });

                let from = e.target.value;
                $.ajax({
                    url: "{{ url('admin/filterRegionByVillages') }}",
                    type: "GET",
                    data: {
                        'from': from,

                    },
                    success: function(data) {
                        $('#transactionGraph').html(data.view);
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
                    url: "{{ url('admin/regionByDays') }}",
                    type: "GET",
                    data: {
                        'date': 'today'
                    },
                    success: function(data) {

                        $('#transactionGraph').html(data);
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
                    url: "{{ url('admin/regionByDays') }}",
                    type: "GET",
                    data: {
                        'date': 'yesterday'
                    },
                    success: function(data) {

                        $('#transactionGraph').html(data);
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
                    url: "{{ url('admin/regionByDays') }}",
                    type: "GET",
                    data: {
                        'date': 'weekToDate'
                    },
                    success: function(data) {

                        $('#transactionGraph').html(data);
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
                    url: "{{ url('admin/regionByDays') }}",
                    type: "GET",
                    data: {
                        'date': 'monthToDate'
                    },
                    success: function(data) {

                        $('#transactionGraph').html(data);
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
                    url: "{{ url('admin/regionByDays') }}",
                    type: "GET",
                    data: {
                        'date': 'lastmonth'
                    },
                    success: function(data) {

                        $('#transactionGraph').html(data);
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
                    url: "{{ url('admin/regionByDays') }}",
                    type: "GET",
                    data: {
                        'date': 'yearToDate'
                    },
                    success: function(data) {

                        $('#transactionGraph').html(data);
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
                    url: "{{ url('admin/regionByDays') }}",
                    type: "GET",
                    data: {
                        'date': 'currentyear'
                    },
                    success: function(data) {

                        $('#transactionGraph').html(data);
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
                    url: "{{ url('admin/regionByDays') }}",
                    type: "GET",
                    data: {
                        'date': 'lastyear'
                    },
                    success: function(data) {

                        $('#transactionGraph').html(data);
                        console.log(data);
                    }
                });
            });
        });
    </script>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <div class="mx-lg-5">

            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark text-uppercase font-weight-lighter text-heading">
                                Milling Coffee
                            </h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Milling Coffee</li>
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
                <span class="ml-md-2 hover" style="font-weight: bold; text-decoration: underline;"> <a
                        href="{{ url('/admin/allregion') }}">ALL
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
                        <a href=""> All Regions</a>
                    </span>
                    &nbsp |
                    <span class="ml-md-2">
                        Governrate
                    </span>
                    <select class="ml-md-2" name="" id="governorate_dropdown">
                        <option value="0" selected disabled>Select Governrate</option>
                        <option value="1">
                            1
                        </option>
                    </select>
                    <span class="ml-md-2">
                        Sub Region
                    </span>
                    <select class="ml-md-2" name="" id="regions_dropdown">
                        <option value="0" selected disabled>Select Region</option>
                        <option value="">1</option>
                    </select>
                    <span class="ml-md-2">
                        Village
                    </span>
                    <select class="ml-md-2" name="" id="village_dropdown">
                        <option value="0" selected disabled>Select Village</option>
                        <option value="">2</option>
                    </select>
                </div>

            </div>
            <hr>
            <div id="transactionGraph">


                <div class="col-lg-11 ml-md-2 text-uppercase d-flex flex-wrap p-0 mb-3 data-tabs" id="transactions">
                    <div class="col-sm-1 color bg-darkPurple p-2 content-box">
                        <h4>-</h4>
                        <p>KG CHERRY BOUGHT </p>
                    </div>
                    <div class="col-sm-1 color bg-darkGreen p-2 content-box">
                        <h4>-</h4>
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
                        <h4>-</h4>

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
                        <h4>-</h4>
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
                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="card col-lg-12">
                                <!-- /.card-header -->
                                <div class="card-body pl-0">
                                    <div class="col-md-12">
                                        @if ($errors->any())
                                            <div class="alert alert-danger">

                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach

                                            </div>
                                        @endif
                                        <form class="milling-form" role="form" method="POST" action="{{ URL::to('admin/milling_coffee') }}">
                                            {{ csrf_field() }}
                                                <table class="milling-table table table-borderless border-0 custom-table text-center"
                                                style="border-collapse: separate;" id="myTable">
                                                    <tr>
                                                        <th>Transaction id</th>
                                                        <th>Farmer Name</th>
                                                        <th>Farmer Code </th>
                                                        <th>Batch Number</th>
                                                        <th>product</th>
                                                        <th>Governerate</th>
                                                        <th>Region</th>
                                                        <th>VIllage</th>
                                                        <th>Quantity</th>
                                                        <th>Stage</th>
                                                        <th>Times</th>
                                                        <th> <button class="milling-link" type="submit" id="submitbtn"
                                                                class="btn btn-primary">Mix
                                                                Batches</button> </th>
                                                        <th id='milling-th'><button class="milling-link" type="submit" id="submitbtn"
                                                                class="btn btn-primary">Confirm Milling</button></th>
                                                    </tr>
                                                    @foreach ($transactions as $transaction)
                                                        <tr>
                                                            @if (Str::contains($transaction['transaction']->batch_number, '000'))
                                                                <td>
                                                                    @foreach ($transaction['child_transactions'] as $childtran)
                                                                        {{ $childtran->transaction_id }} <br>
                                                                    @endforeach
                                                                </td>
                                                                <td>
                                                                    @foreach ($transaction['child_transactions'] as $childtran)
                                                                        {{ getFarmer($childtran->batch_number) }} <br>
                                                                    @endforeach
                                                                </td>

                                                                <td>
                                                                    @foreach ($transaction['child_transactions'] as $childtran)
                                                                        {{ explode('-', $childtran->batch_number)[0] . '-' . explode('-', $childtran->batch_number)[1] . '-' . explode('-', $childtran->batch_number)[2] . '-' . explode('-', $childtran->batch_number)[3] }}
                                                                        <br>
                                                                    @endforeach
                                                                </td>
                                                                <td>
                                                                    @foreach ($transaction['child_transactions'] as $childtran)
                                                                        {{ $childtran->batch_number }} <br>
                                                                    @endforeach
                                                                </td>
                                                                <td>
                                                                    SPECIALTY
                                                                </td>
                                                                <td>
                                                                    @foreach ($transaction['child_transactions'] as $childtran)
                                                                        {{ getGov($childtran->batch_number) }} <br>
                                                                    @endforeach
                                                                </td>
                                                                <td>
                                                                    @foreach ($transaction['child_transactions'] as $childtran)
                                                                        {{ getRegion($childtran->batch_number) }} <br>
                                                                    @endforeach
                                                                </td>
                                                                <td>
                                                                    @foreach ($transaction['child_transactions'] as $childtran)
                                                                        {{ getVillage($childtran->batch_number) }} <br>
                                                                    @endforeach
                                                                </td>
                                                                <td>
                                                                    @foreach ($transaction['child_transactions'] as $childtran)
                                                                        {{ $childtran->transactionDetail->sum('container_weight') }}
                                                                        <br>
                                                                    @endforeach
                                                                </td>

                                                                <td>
                                                                    {{ stagesOfSentTo($transaction['transaction']->sent_to) }}
                                                                </td>
                                                                <td>
                                                                    @php
                                                                        $now = \Carbon\Carbon::now();
                                                                        $today = $now->today()->toDateString();
                                                                    @endphp
                                                                    {{ $transaction['transaction']->created_at->diffInDays($today) . ' ' . 'Days' }}
                                                                </td>
                                                                <td>
                                                                    @php

                                                                        $batchNumber = $transaction['transaction']->batch_number;
                                                                        $batchExplode = explode('-', $batchNumber);
                                                                        $gov = $batchExplode[0];
                                                                    @endphp
                                                                    @if ($transaction['transaction']->sent_to == 13)
                                                                        <input type="checkbox" data-gov-rate="<?= $gov ?>"
                                                                            name="transaction_id[]"
                                                                            value="{{ $transaction['transaction']->transaction_id }}"
                                                                            class="check_gov{{ $transaction['transaction']->transaction_id }}"
                                                                            onClick="checkGov('<?= $gov ?>',{{ $transaction['transaction']->transaction_id }})">
                                                                    @endif

                                                                </td>
                                                                <td>
                                                                    @php

                                                                        $batchNumber = $transaction['transaction']->batch_number;
                                                                        $batchExplode = explode('-', $batchNumber);
                                                                        $gov = $batchExplode[0];
                                                                    @endphp
                                                                    @if ($transaction['transaction']->sent_to == 140)
                                                                        <input type="checkbox" data-gov-rate="<?= $gov ?>"
                                                                            name="transaction_id[]"
                                                                            value="{{ $transaction['transaction']->transaction_id }}"
                                                                            class="check_gov{{ $transaction['transaction']->transaction_id }}"
                                                                            onClick="checkGov('<?= $gov ?>',{{ $transaction['transaction']->transaction_id }})">
                                                                    @endif
                                                                </td>
                                                            @else
                                                                <td>
                                                                    {{ $transaction['transaction']->transaction_id }}
                                                                </td>
                                                                <td>
                                                                    {{ getFarmer($transaction['transaction']->batch_number) }}
                                                                </td>
                                                                <td>
                                                                    {{ explode('-', $transaction['transaction']->batch_number)[0] . '-' . explode('-', $transaction['transaction']->batch_number)[1] . '-' . explode('-', $transaction['transaction']->batch_number)[2] . '-' . explode('-', $transaction['transaction']->batch_number)[3] }}
                                                                </td>
                                                                <td>
                                                                    {{ $transaction['transaction']->batch_number }}
                                                                </td>
                                                                <td>
                                                                    SPECIALTY
                                                                </td>
                                                                <td>
                                                                    {{ getGov($transaction['transaction']->batch_number) }}
                                                                </td>
                                                                <td>
                                                                    {{ getRegion($transaction['transaction']->batch_number) }}
                                                                </td>
                                                                <td>
                                                                    {{ getVillage($transaction['transaction']->batch_number) }}
                                                                </td>
                                                                <td>
                                                                    {{ $transaction['transaction']->transactionDetail->sum('container_weight') }}
                                                                </td>
                                                                <td>
                                                                    {{ stagesOfSentTo($transaction['transaction']->sent_to) }}
                                                                </td>
                                                                <td>
                                                                    @php
                                                                        $now = \Carbon\Carbon::now();
                                                                        $today = $now->today()->toDateString();
                                                                    @endphp
                                                                    {{ $transaction['transaction']->created_at->diffInDays($today) . ' ' . 'Days' }}
                                                                </td>
                                                                <td>
                                                                    @php

                                                                        $batchNumber = $transaction['transaction']->batch_number;
                                                                        $batchExplode = explode('-', $batchNumber);
                                                                        $gov = $batchExplode[0];
                                                                    @endphp
                                                                    @if ($transaction['transaction']->sent_to == 13)
                                                                        <input type="checkbox" data-gov-rate="<?= $gov ?>"
                                                                            name="transaction_id[]"
                                                                            value="{{ $transaction['transaction']->transaction_id }}"
                                                                            class="check_gov{{ $transaction['transaction']->transaction_id }}"
                                                                            onClick="checkGov('<?= $gov ?>',{{ $transaction['transaction']->transaction_id }})">
                                                                    @endif

                                                                </td>
                                                                <td>
                                                                    @php

                                                                        $batchNumber = $transaction['transaction']->batch_number;
                                                                        $batchExplode = explode('-', $batchNumber);
                                                                        $gov = $batchExplode[0];
                                                                    @endphp
                                                                    @if ($transaction['transaction']->sent_to == 140)
                                                                        <input type="checkbox" data-gov-rate="<?= $gov ?>"
                                                                            name="transaction_id[]"
                                                                            value="{{ $transaction['transaction']->transaction_id }}"
                                                                            class="check_gov{{ $transaction['transaction']->transaction_id }}"
                                                                            onClick="checkGov('<?= $gov ?>',{{ $transaction['transaction']->transaction_id }})">
                                                                    @endif
                                                                </td>
                                                            @endif
                                                        </tr>
                                                    @endforeach
                                                </table>
                                            <div class="card-footer">

                                            </div>
                                            <form>
                                    </div>
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
        <script>
            var gov = null;

            function checkGov(checkgov, id) {

                //alert(id);
                if (gov == null) {
                    gov = checkgov;
                } else {
                    if (gov != checkgov) {
                        if ($('.check_gov' + id).prop("checked") == true) {
                            alert("You can not mix two different governerates")
                            $('.check_gov' + id).prop('checked', false);
                        }

                    }
                }
                checkBoxCount = $('input[type="checkbox"]:checked').length;
                if (checkBoxCount == 0) {
                    console.log(checkBoxCount);
                    gov = null;
                }
            }
            $(document).ready(function() {
                $('#submitbtn').on('click', function() {
                    $('#submitbtn').hide();
                });
                $('#milling-th').on('click', function() {
                    $attr = $('form').attr('action', '{{ URL::to('admin/newMilliing') }}');

                });

            });
        </script>
    @endsection
