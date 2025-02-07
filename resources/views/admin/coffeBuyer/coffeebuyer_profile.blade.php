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
            margin-left: 306PX;
        }

        #farmerTable {
            border-collapse: separate;
            border-spacing: 0px;
        }

        #farmerTable tr td {
            padding: unset !important;
            padding: 0 20px !important;
            vertical-align: middle;
        }

        #farmerTable tr td:first-child {
            border-right: 1px solid rgba(0, 0, 0, .1);
        }

        #farmerTable tr:first-child td {
            padding-top: 1rem !important;
        }

        #farmerTable tr:last-child td {
            padding-bottom: 1rem !important;
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

        .list-group .list-group-item {
            width: 100%;
        }

        #reciepts:hover {
            cursor: pointer;
        }

        #buyeridcardbtn:hover {
            cursor: pointer;
        }

        #buyeridcard {
            display: none;
        }

        .hoverSize:hover {
            width: 500px !important;

        }

        /* #reciepts-hover:hover b{
                                                                                                                                                                                                                                                                cursor: pointer;
                                                                                                                                                                                                                                                            } */

    </style>

    <script>
        $(document).ready(function() {

            $('#to').on('change', function() {
                let from = $('#from').val();
                let to = $('#to').val();


                $.ajax({
                    url: "{{ url('admin/filterByDateprofile/' . $buyer->user_id) }}",
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
                $('.blacklink .hover').css({
                    'font-weight': 'normal',
                    'text-decoration': 'none'
                });
                $(this).css({
                    'font-weight': 'bold',
                    'text-decoration': 'underline'
                });
                $.ajax({
                    url: "{{ url('admin/daysFilter/' . $buyer->user_id) }}",
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
                $('.blacklink .hover').css({
                    'font-weight': 'normal',
                    'text-decoration': 'none'
                });
                $(this).css({
                    'font-weight': 'bold',
                    'text-decoration': 'underline'
                });
                $.ajax({
                    url: "{{ url('admin/daysFilter/' . $buyer->user_id) }}",
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
                $('.blacklink .hover').css({
                    'font-weight': 'normal',
                    'text-decoration': 'none'
                });
                $(this).css({
                    'font-weight': 'bold',
                    'text-decoration': 'underline'
                });
                $.ajax({
                    url: "{{ url('admin/daysFilter/' . $buyer->user_id) }}",
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
                $('.blacklink .hover').css({
                    'font-weight': 'normal',
                    'text-decoration': 'none'
                });
                $(this).css({
                    'font-weight': 'bold',
                    'text-decoration': 'underline'
                });
                $.ajax({
                    url: "{{ url('admin/daysFilter/' . $buyer->user_id) }}",
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
                $('.blacklink .hover').css({
                    'font-weight': 'normal',
                    'text-decoration': 'none'
                });
                $(this).css({
                    'font-weight': 'bold',
                    'text-decoration': 'underline'
                });
                $.ajax({
                    url: "{{ url('admin/daysFilter/' . $buyer->user_id) }}",
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
                $('.blacklink .hover').css({
                    'font-weight': 'normal',
                    'text-decoration': 'none'
                });
                $(this).css({
                    'font-weight': 'bold',
                    'text-decoration': 'underline'
                });
                $.ajax({
                    url: "{{ url('admin/daysFilter/' . $buyer->user_id) }}",
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
                $('.blacklink .hover').css({
                    'font-weight': 'normal',
                    'text-decoration': 'none'
                });
                $(this).css({
                    'font-weight': 'bold',
                    'text-decoration': 'underline'
                });
                $.ajax({
                    url: "{{ url('admin/daysFilter/' . $buyer->user_id) }}",
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
                $('.blacklink .hover').css({
                    'font-weight': 'normal',
                    'text-decoration': 'none'
                });
                $(this).css({
                    'font-weight': 'bold',
                    'text-decoration': 'underline'
                });
                $.ajax({
                    url: "{{ url('admin/daysFilter/' . $buyer->user_id) }}",
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
            $('#reciepts').on('click', function() {
                $.ajax({
                    url: "{{ url('admin/coffeeBuyer/reciepts/' . $buyer->user_id) }}",
                    type: "GET",
                    success: function(data) {
                        $('#transactionInv').html(data);
                        console.log(data);
                    }
                });
            });
            $('#buyeridcard').on('click', function() {
                $.ajax({
                    url: "{{ url('admin/coffeeBuyer/idcard/' . $buyer->user_id) }}",
                    type: "GET",
                    success: function(data) {
                        $('#transactionInv').html(data);
                        console.log(data);
                    }
                });
            });
            $('#buyeridcardbtn').on('click', function() {
                var html = $('#buyeridcard').html();
                $('#transactionInv').html(html);
            });
        });
    </script>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">

        <!-- Content Header (Page header) -->
        <section class="content-header mx-lg-5">
            <div class="container-fluid">
                <div class="row mb-2">

                    <div class="col-sm-6 pl-0">
                        <h1 class="m-0 text-dark text-uppercase font-weight-lighter text-heading"> Profile

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

            @if (Session::has('msg'))
                <div class="alert alert-success" role="alert">
                    <b>{{ Session::get('msg') }}</b>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            <div class="row anchor  btn-color-darkRed add-button justify-content-end">
                <span class="ml-2"> <a href="{{ url('admin/edituser/' . $buyer->id) }}">EDIT
                        INFORMATION</a></span> &nbsp |
                <span class="ml-2"> <a href=""> ADD PANELTY</a></span>

            </div>
            <hr class="ml-2 mb-0">
            <div class="row">
                <div class="col-md-4 my-3">
                    @if ($buyer->profile_image == null)
                        <img class="famerimg ml-4" src="{{ Storage::disk('s3')->url('images/' . 'dumy.png') }}"
                            style="width: 200px ; height:200px; border-radius:50%;" alt="">
                    @else
                        <img class="famerimg ml-4" style="width:  200px ; height: 200px; border-radius:50%;"
                            src="{{ Storage::disk('s3')->url('images/' . $buyer->profile_image) }}" alt=" no img">
                    @endif

                </div>
                <div class="col-md-8">
                    <table class="table table-borderless w-auto village_profile_table mb-0 h-100" id="farmerTable">

                        <tbody>
                            <tr>
                                <td colspan=""> <strong>Name</strong> </td>
                                <td colspan="4">{{ $buyer->first_name . ' ' . $buyer->last_name }}</td>

                            </tr>
                            {{-- <tr>
                                <td colspan=""><strong>IDENTIFICATION NUMBER</strong></td>
                                <td colspan="4">{{ $buyer->farmer_nicn }}</td>

                        </tr> --}}
                            <tr>
                                <td colspan=""><strong>City </strong></td>
                                <td colspan="4">-</td>

                            </tr>
                            <tr>
                                <td colspan=""><strong>Tel</strong></td>
                                <td colspan="4">123456789</td>

                            </tr>
                            <tr>
                                <td colspan=""><strong>Start Date</strong></td>
                                <td colspan="4">{{ $buyer->created_at }}</td>

                            </tr>


                        </tbody>
                    </table>
                </div>
            </div>
            <hr class="ml-2 mt-0">
            <div class="row ml-2 text-uppercase mb-2">
                <strong>
                    <b>Date Filter</b>
                </strong>
            </div>
            <div class="row ml-2">
                <form action="" method="POST" id="data-form">
                    <label class="text-uppercase font-weight-normal mr-2 mb-0 letter-spacing-1" for="from">From</label>
                    <input class="mr-3" type="date" name="" id="from">
                    <label class="text-uppercase font-weight-normal mr-2 mb-0 letter-spacing-1" for="To">To</label>
                    <input class="mr-3" type="date" name="" id="to">
                </form>
            </div>
            <br>
            <div class="row ml-2 blacklink letter-spacing-1">
                <span class="hover" id="today"> TODAY</span> &nbsp |
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
                <span class="ml-2"> <a href="{{ route('coffeBuyer.profile', $buyer) }}">ALL
                        TIME</a></span>
            </div>
            <hr class="ml-2">
            <div class="ml-2 text-uppercase d-flex flex-wrap p-0 mb-3 data-tabs" id="transaction">
                <div class="col-sm-1 color bg-darkPurple mr-1">
                    <h4>{{ $buyer->first_purchase }}</h3>
                        <p>First Purchase</p>
                </div>
                <div class="col-sm-1 color bg-darkPurple mr-1">
                    <h4>{{ $buyer->last_purchase }}</h3>

                        <p>Last Puchase</p>
                </div>
                <div class="col-sm-1 color bg-Green mr-1">
                    <h4>{{ number_format($buyer->price) }}</h3>

                        <p>yer total coffee purchased</p>
                </div>
                <div class="col-sm-1 color bg-Green mr-1">
                    <h4>-</h3>

                        <p>YER SETTELED</p>
                </div>
                <div class="col-sm-1 color bg-Green mr-1">
                    <h4>-</h3>

                        <p>YER
                            ACCOUNT
                            PAYABLE</p>
                </div>
                <div class="col-sm-1 color bg-Green mr-1">
                    <h4>-</h3>

                        <p>YER
                            PENALTY</p>
                </div>
                <div class="col-sm-1 color bg-darkRed mr-1">
                    <h4>-</h3>

                        <p>YER COMISSION</p>
                </div>
            </div>
            <hr class="ml-2">
            <div class="row ml-2 blacklink mb-2">
                <span class="row ml-2 text-uppercase mb-2 hover-2" id="buyeridcardbtn">
                    <strong>
                        <b>ID DOCUMENTS</b>
                    </strong>
                </span>
                <span class="ml-3 font-weight-bold">|</span>
                <span class="row ml-2 text-uppercase mb-2 hover-2">
                    <strong id="reciepts-hover">
                        <b id="reciepts">RECIEPTS</b>
                    </strong>
                </span>
                <span class="ml-3 font-weight-bold">|</span>
                <span class="row ml-2 text-uppercase mb-2 hover-2" id="">
                    <strong>
                        <b>LOANS</b>
                    </strong>
                </span>
                <span class="ml-3 font-weight-bold">|</span>
                <span class="row ml-2 text-uppercase mb-2 hover-2" id="">
                    <strong>
                        <b>PREMIUMS &nbsp;</b>
                    </strong>
                </span>

            </div>
            {{-- <div class="ml-2 text-uppercase d-flex flex-wrap p-0 mb-3 data-tabs" id="transactionInv">
                <div class="col-sm-1 color bg-darkPurple mr-1">
                    <h4>{{ App\Village::count() }}</h4>
                    <p>Villages</p>
                </div>
                <div class="col-sm-1 color bg-Green mr-1">
                    <h4>{{ App\Farmer::count() }}</h4>

                    <p>Farmers</p>
                </div>
                <div class="col-sm-1 color bg-darkRed mr-1">
                    <h4>{{ App\User::count() }}</h4>

                    <p>User </p>
                </div>


            </div> --}}
            <div class="row ml-2" id="transactionInv">
                <div class="col-sm-1 color p-0 ml-0">
                    <div id="idcard">


                        @if ($buyer->idcard_image == null)
                            <img class="famerimg" src="{{ Storage::disk('s3')->url('images/' . 'dumy.png') }}"
                                style="width: 100%; height: 100%;" alt="" id="idimage">


                        @else
                            <img class="famerimg hoverSize"
                                src="{{ Storage::disk('s3')->url('images/' . $buyer->idcard_image) }}"
                                style="width: 100%; height: 100%;" alt="" id="idimage">
                        @endif
                    </div>
                    <div id="myModal" class="modal">
                        <span class="close">&times;</span>
                        <img class="modal-content" id="img01">
                        <div id="caption"></div>
                    </div>
                </div>
            </div>
            <hr class="ml-2">
            <div class="row ml-2 text-uppercase mb-2">
                <strong>
                    <b>Villages Bought From</b>
                </strong>
            </div>

            <div class="row">
                @foreach ($buyer->villages as $village)
                    @if ($village)
                        <div class="col-sm-4 mb-1">
                            <ul class="list-group list-group-horizontal">
                                <li class="list-group-item data-content-list">
                                    @if ($village['picture_id'] == null)
                                        <img src="{{ Storage::disk('s3')->url('images/' . 'dumy.png') }}"
                                            id="region_farmer">
                                    @else
                                        <img src=" {{ Storage::disk('s3')->url('images/' . $farmer['image']) }}"
                                            id="region_farmer">
                                    @endif

                                    <span class="mr-3">{{ $village['village_title'] }}</span>
                                </li>

                            </ul>
                        </div>
                    @endif
                @endforeach
            </div>
            <hr class="ml-2">
            <div class="row ml-2 text-uppercase mb-2 d-flex align-items-center">
                <strong>
                    <b>Villages Responsible For</b>
                </strong>
                <a href="{{ url('admin/assignVillages/' . $buyer->user_id) }}"
                    class="btn btn-success bg-Green ml-5 mb-0">Assign
                    Villages
                </a>
            </div>
            <div class="row">
                @foreach ($buyer->resposibleVillage as $village)
                    @if ($village)
                        <div class="col-sm-4 mb-1">
                            <ul class="list-group list-group-horizontal">
                                <li class="list-group-item data-content-list">
                                    @if ($village['picture_id'] == null)
                                        <img src="{{ Storage::disk('s3')->url('images/' . 'dumy.png') }}"
                                            id="region_farmer">
                                    @else
                                        <img src=" {{ Storage::disk('s3')->url('images/' . $village['image']) }}"
                                            id="region_farmer">
                                    @endif

                                    <span class="mr-3">{{ $village['village_title'] }}</span>
                                </li>

                            </ul>
                        </div>
                    @endif
                @endforeach
            </div>
            <hr class="ml-2">
            <div class="row ml-2 text-uppercase mb-2">
                <strong>
                    <b>Farmers Bought From</b>
                </strong>
            </div>
            <div class="row">
                <table style="width:100%">
                    <thead>
                        <tr>
                            <th>Farmer Name</th>
                            <th>First Purchase </th>
                            <th>Last Purchase</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($buyer->farmers as $farmer)
                            {{-- <div class="col-sm-4 mb-1">
                        <ul class="list-group list-group-horizontal">
                            <li class="list-group-item data-content-list">
                                @if ($farmer['farmer_image'] == null)
                                    <img src="{{ Storage::disk('s3')->url('images/' . 'dumy.png') }}" id="region_farmer">
                                @else
                                    <img src="{{ Storage::disk('s3')->url('images/' . $farmer['farmer_image']) }}"
                                        id="region_farmer">
                                @endif

                                <span class="mr-3">{{ $farmer['farmer_name'] }}</span>
                            </li>

                        </ul>
                    </div> --}}


                            <tr>
                                <td>{{ $farmer['farmer_name'] }}</td>
                                <td>{{ $farmer->getfirstTransaction() }}</td>
                                <td> {{ $farmer->getlastTransaction() }}</td>
                            </tr>

                        @endforeach
                    </tbody>

                </table>
            </div>
            <hr class="ml-2">
            <div class="row ml-2 text-uppercase mb-2">
                <strong>
                    <b>TRANSACTIONS</b>
                </strong>
            </div>
            @foreach ($buyer->transactions as $transaction)
                <div class="row ml-2">
                    <div class="">
                        <ol class=" breadcrumb float-sm-right txt-size">
                            <li class="breadcrumb-item active">
                                {{ $transaction->created_at }} /{{ $buyer->first_name }} /
                                {{ explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] }}
                                /
                                {{                                 explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2] . '-' . explode('-', $transaction->batch_number)[3] }}
                                /
                                @php
                                    echo floatval($transaction->details->sum('container_weight'));
                                    echo '/';
                                    echo number_format(round(farmerPricePerKg($transaction->batch_number) * $transaction->details->sum('container_weight'), 2));
                                @endphp

                            </li>
                        </ol>
                    </div>
                </div>
            @endforeach
            <div id="buyeridcard">
                <div class="col-sm-1 color p-0 ml-0">
                    @if ($buyer->idcard_image == null)
                        <img class="famerimg" src="{{ Storage::disk('s3')->url('images/' . 'dumy.png') }}"
                            style="max-width: 100%; height: 100%;" alt="">


                    @else
                        <img class="famerimg hoverSize"
                            src="{{ Storage::disk('s3')->url('images/' . $buyer->idcard_image) }}"
                            style="width: 100%; height: 100%;" alt="" id="idimage">
                    @endif
                    <div id="myModal" class="modal">
                        <span class="close">&times;</span>
                        <img class="modal-content" id="img01">
                        <div id="caption"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main content -->

        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->


@endsection
