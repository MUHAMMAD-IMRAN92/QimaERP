@extends('layouts.default')
@section('title', 'Transection Detail')
@section('content')
    <style>
        /* Center the loader */
        #loader {
            position: absolute;
            left: 50%;
            top: 50%;
            z-index: 1;
            width: 120px;
            height: 120px;
            margin: -76px 0 0 -56px;
            border: 16px solid #cac6c6;
            border-radius: 50%;
            border-top: 16px solid #a81515;
            -webkit-animation: spin 2s linear infinite;
            animation: spin 2s linear infinite;
            display: none
        }

        @-webkit-keyframes spin {
            0% {
                -webkit-transform: rotate(0deg);
            }

            100% {
                -webkit-transform: rotate(360deg);
            }
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Add animation to "page content" */
        .animate-bottom {
            position: relative;
            -webkit-animation-name: animatebottom;
            -webkit-animation-duration: 1s;
            animation-name: animatebottom;
            animation-duration: 1s
        }

        @-webkit-keyframes animatebottom {
            from {
                bottom: -100px;
                opacity: 0
            }

            to {
                bottom: 0px;
                opacity: 1
            }
        }

        @keyframes animatebottom {
            from {
                bottom: -100px;
                opacity: 0
            }

            to {
                bottom: 0;
                opacity: 1
            }
        }

        #myDiv {
            display: none;
            text-align: center;
        }

    </style>
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

        #batch_number {
            width: 100%;
        }

    </style>
    <style href="//cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css"></style>
    <script src="//cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {

            $('#myTable').DataTable({
                columnDefs: [{
                    orderable: false,
                    targets: [11, 13, 14]
                }],
                order: [
                    [1, 'asc']
                ]
            });
            $('#to').on('change', function() {
                let from = $('#from').val();
                let to = $('#to').val();

                $.ajax({

                    url: "{{ url('admin/lot_mixing/betweenDate') }}",
                    type: "GET",
                    data: {
                        'from': from,
                        'to': to
                    },
                    success: function(data) {
                        $('#table-body').html(data);
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
                    url: "{{ url('admin/lot_mixing/filterLotMixingByGovernrate') }}",
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
                        $('#table-body').html(data.view);

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
                    url: "{{ url('admin/lot_mixing/filterLotMixingByRegion') }}",
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
                        $('#table-body').html(data.view);
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
                    url: "{{ url('admin/lot_mixing/filterLotMixingByvillage') }}",
                    type: "GET",
                    data: {
                        'from': from,

                    },
                    success: function(data) {
                        $('#table-body').html(data.view);
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
                $('#loader').css('display', 'block');
                $.ajax({
                    url: "{{ url('admin/lot_mixing/filterByDays') }}",
                    type: "GET",
                    data: {
                        'date': 'today'
                    },
                    success: function(data) {

                        $('#table-body').html(data);

                        $('#loader').css('display', 'none');

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
                $('#loader').css('display', 'block');
                $.ajax({
                    url: "{{ url('admin/lot_mixing/filterByDays') }}",

                    type: "GET",
                    data: {
                        'date': 'yesterday'
                    },
                    success: function(data) {

                        $('#table-body').html(data);
                        $('#loader').css('display', 'none');

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
                $('#loader').css('display', 'block');
                $.ajax({
                    url: "{{ url('admin/lot_mixing/filterByDays') }}",

                    type: "GET",
                    data: {
                        'date': 'weekToDate'
                    },
                    success: function(data) {

                        $('#table-body').html(data);
                        $('#loader').css('display', 'none');

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
                $('#loader').css('display', 'block');
                $.ajax({
                    url: "{{ url('admin/lot_mixing/filterByDays') }}",

                    type: "GET",
                    data: {
                        'date': 'monthToDate'
                    },
                    success: function(data) {

                        $('#table-body').html(data);
                        $('#loader').css('display', 'none');

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
                $('#loader').css('display', 'block');
                $.ajax({
                    url: "{{ url('admin/lot_mixing/filterByDays') }}",

                    type: "GET",
                    data: {
                        'date': 'lastmonth'
                    },
                    success: function(data) {

                        $('#table-body').html(data);
                        $('#loader').css('display', 'none');

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
                $('#loader').css('display', 'block');
                $.ajax({
                    url: "{{ url('admin/lot_mixing/filterByDays') }}",

                    type: "GET",
                    data: {
                        'date': 'yearToDate'
                    },
                    success: function(data) {

                        $('#table-body').html(data);
                        $('#loader').css('display', 'none');

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
                $('#loader').css('display', 'block');
                $.ajax({
                    url: "{{ url('admin/lot_mixing/filterByDays') }}",

                    type: "GET",
                    data: {
                        'date': 'currentyear'
                    },
                    success: function(data) {

                        $('#table-body').html(data);
                        $('#loader').css('display', 'none');

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
                $('#loader').css('display', 'block');
                $.ajax({
                    url: "{{ url('admin/lot_mixing/filterByDays') }}",

                    type: "GET",
                    data: {
                        'date': 'lastyear'
                    },

                    success: function(data) {

                        $('#table-body').html(data);
                        $('#loader').css('display', 'none');

                        console.log(data);
                    }
                });
            });
            $('#pack-approval').on('click', function() {
                $('#milling-form').attr('action',
                    '{{ URL::to('admin/packaging/approval') }}');
                    $('#pack-approval').css('display' , 'none');
            });
            $('#cnf-mixing').on('click', function() {
               
                    $('#cnf-mixing').css('display' , 'none');
            });
            const myTimeout = setTimeout(myGreeting, 5000);

            function myGreeting() {
                $(".alert").css('display', 'none');
            }

          
                $("#myTable").on("click",".checkSentTo24", function(){
                console.log('');
                $('.checkSentTo29').prop('checked', false);
                $('#pack-approval').prop('disabled', true);
                $('#cnf-mixing').prop('disabled', false);
                if ($(".checkSentTo24:checkbox:checked").length > 0) {
                    $('#pack-approval').prop('disabled', true);
                } else {
                    $('#pack-approval').prop('disabled', false);
                }

            });
          
            $("#myTable").on("click",".checkSentTo29", function(){

                $('.checkSentTo24').prop('checked', false);
                $('#cnf-mixing').prop('disabled', true);
                $('#pack-approval').prop('disabled', false);
                if ($(".checkSentTo29:checkbox:checked").length > 0) {
                    $('#cnf-mixing').prop('disabled', true);
                } else {
                    $('#cnf-mixing').prop('disabled', false);
                }

            });
    
            $('#confirm-mixing-modal').on('click', function() {
                    $('#confirm-mixing-modal').css('display' , 'none');
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
                                LOT MIXING
                            </h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <button class="btn btn-dark bg-transparent border-0 add-button text-uppercase mt-3">ADD
                                    CROPSTER REPORT</button>
                                {{-- /<li class="btn btn-dark bg-transparent border-0 add-button text-uppercase"><a href="#"></a></li> --}}
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
                <span class="ml-md-2 hover" id="currentyear"> 2022
                    SEASON</a></span>
                &nbsp |
                <span class="ml-md-2 hover" id="lastyear"> 2021
                    SEASON</a></span>
                &nbsp |
                <span class="ml-md-2 hover" style="font-weight: bold; text-decoration: underline;"> <a
                        href="{{ url('/admin/lot_mixing') }}">ALL
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
                        <option value="0" selected disabled>Select Governorate</option>
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

                <div id="loader"></div>
                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="card col-lg-12">
                                <!-- /.card-header -->
                                <div class="card-body pl-0" id='ajaxdiv'>
                                    <div class="col-md-12">
                                        @if ($errors->any())
                                            <div class="alert alert-danger">

                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach

                                            </div>
                                        @endif
                                        <form class="milling-form" action="{{ route('mixing.store') }}" method="POST"
                                            id="milling-form">
                                            {{ csrf_field() }}
                                            <table
                                                class="milling-table table table-borderless border-0 custom-table text-center"
                                                style="border-collapse: separate;" id="myTable">
                                                <!-- Button trigger modal -->


                                                <!-- Modal -->
                                                <div class="modal fade" id="exampleModal" tabindex="-1"
                                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLabel">Batch
                                                                    Number</h5>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <input type="text" name="batch_number"
                                                                    placeholder="Please Enter Batch Number"
                                                                    id="batch_number">
                                                            </div>

                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-primary" id='confirm-mixing-modal'> 
                                                                    Confirm Mixing
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <thead>
                                                    <tr>
                                                        <th>Transaction id</th>
                                                        <th>Name</th>
                                                        <th>Code</th>
                                                        <th>Harvest Date</th>
                                                        <th>Governorate</th>
                                                        <th>Region</th>
                                                        <th>VIllage</th>
                                                        <th>Altitude</th>
                                                        <th>Cupping Score</th>
                                                        <th>Process</th>
                                                        <th>Type Of Lot</th>
                                                        <th>Quantity</th>
                                                        <th>Location</th>
                                                        {{-- <th> <button class="milling-link" type="submit" id="submitbtn"
                                                                class="btn btn-primary">Mix
                                                                Batches</button> </th> --}}
                                                        {{-- <<button class="milling-link" type="submit"
                                                                id="submitbtn" class="btn btn-primary">Confirm
                                                                Mixing</button> --}}
                                                        <th id='milling-th'><button type="button" class="btn btn-primary"
                                                                data-toggle="modal" data-target="#exampleModal"
                                                                id="cnf-mixing">
                                                                Confirm Mixing
                                                            </button></th>
                                                        <th><button type="submit" class="btn btn-primary"
                                                                id='pack-approval'>
                                                                Packaging Approval
                                                            </button></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="table-body">
                                                    @foreach ($transactions as $transaction)
                                                        <tr>

                                                            <td>
                                                                {{ $transaction->transaction_id }}
                                                            </td>
                                                            <td>

                                                                @php
                                                                    $farmers = parentBatch($transaction->batch_number);
                                                                @endphp

                                                                @foreach ($farmers as $farmer)

                                                                    @if ($farmer)
                                                                        {{ $farmer->farmer_name }} <br>
                                                                    @endif
                                                                @endforeach
                                                            </td>
                                                            <td>


                                                                @php
                                                                    $farmers = parentBatch($transaction->batch_number);
                                                                @endphp
                                                                @foreach ($farmers as $farmer)
                                                                    @if ($farmer)
                                                                        {{ $farmer->farmer_code }} <br>
                                                                    @endif

                                                                @endforeach
                                                            </td>
                                                            <td>
                                                                -
                                                            </td>

                                                            <td>
                                                                {{ getGov($transaction->batch_number) }}
                                                            </td>
                                                            <td>
                                                                {{ getRegion($transaction->batch_number) }}
                                                            </td>
                                                            <td>
                                                                @php
                                                                    $farmers = parentBatch($transaction->batch_number);
                                                                @endphp
                                                                @foreach ($farmers as $farmer)
                                                                    @if ($farmer)
                                                                        @php
                                                                            $village = \App\Village::where('village_code', $farmer->village_code)->first();
                                                                        @endphp
                                                                        @if ($village)
                                                                            {{ $village->village_title }}
                                                                            <br>
                                                                        @endif
                                                                    @endif

                                                                @endforeach
                                                            </td>
                                                            <td>
                                                                -
                                                            </td>
                                                            <td>
                                                                -
                                                            </td>
                                                            <td>
                                                                -
                                                            </td>
                                                            <td>
                                                                @if (str_contains($transaction->batch_number, '000'))

                                                                    MicroLot
                                                                @else
                                                                    Single Farmer
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @foreach ($transaction->details as $detail)
                                                                    {{ $detail->container_number . ' ' . $detail->container_weight }}
                                                                @endforeach

                                                            </td>
                                                            <td>
                                                                -
                                                            </td>
                                                            <td>

                                                                @if ($transaction->sent_to == 24)
                                                                    <input type="checkbox" name="mixings[]"
                                                                        value="{{ $transaction->transaction_id }}"
                                                                        class="checkSentTo24">
                                                                @endif

                                                            </td>
                                                            <td>

                                                                @if ($transaction->sent_to == 29)
                                                                    <input type="checkbox" name="approvals[]"
                                                                        value="{{ $transaction->transaction_id }}"
                                                                        class="checkSentTo29" >
                                                                @endif

                                                            </td>
                                                            {{-- @endif --}}

                                                        </tr>
                                                    @endforeach
                                                </tbody>
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
        {{-- <script>
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
                    $('#cnf-mixing').css('display' , 'none');
                    $attr = $('form').attr('action', '{{ URL::to('admin/newMilliing') }}');
                });

            });

               
            const slider = document.querySelector(".milling-form");
            const preventClick = (e) => {
                e.preventDefault();
                e.stopImmediatePropagation();
            }
            let isDown = false;
            let isDragged = false;
            let startX;
            let scrollLeft;

            slider.addEventListener("mousedown", e => {
                isDown = true;
                slider.classList.add("active");
                startX = e.pageX - slider.offsetLeft;
                scrollLeft = slider.scrollLeft;
            });
            slider.addEventListener("mouseleave", () => {
                isDown = false;
                slider.classList.remove("active");
            });
            slider.addEventListener("mouseup", (e) => {
                isDown = false;
                const elements = document.querySelectorAll(".milling-table a");
                if (isDragged) {
                    for (let i = 0; i < elements.length; i++) {
                        elements[i].addEventListener("click", preventClick);
                    }
                } else {
                    for (let i = 0; i < elements.length; i++) {
                        elements[i].removeEventListener("click", preventClick);
                    }
                }
                slider.classList.remove("active");
                isDragged = false;
            });
            slider.addEventListener("mousemove", e => {
                if (!isDown) return;
                isDragged = true;
                e.preventDefault();
                const x = e.pageX - slider.offsetLeft;
                const walk = (x - startX) * 2;
                slider.scrollLeft = scrollLeft - walk;
                console.log(walk);
            });
        </script> --}}
  
    @endsection
