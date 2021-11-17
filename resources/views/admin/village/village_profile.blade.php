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
            padding-right: 40px !important;
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

        .village_profile_table td {
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 18px;

        }

        #myImg {
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }

        #myImg:hover {
            opacity: 0.7;
        }

        /* The Modal (background) */
        .modal {
            display: none;
            /* Hidden by default */
            position: fixed;
            /* Stay in place */
            z-index: 1;
            /* Sit on top */
            padding-top: 100px;
            /* Location of the box */
            left: 0;
            top: 0;
            width: 100%;
            /* Full width */
            height: 100%;
            /* Full height */
            overflow: auto;
            /* Enable scroll if needed */
            background-color: rgb(0, 0, 0);
            /* Fallback color */
            background-color: rgba(0, 0, 0, 0.9);
            /* Black w/ opacity */
        }

        /* Modal Content (image) */
        .modal-content {
            margin: auto;
            display: block;
            width: 80%;
            max-width: 700px;
        }

        /* Caption of Modal Image */
        #caption {
            margin: auto;
            display: block;
            width: 80%;
            max-width: 700px;
            text-align: center;
            color: #ccc;
            padding: 10px 0;
            height: 150px;
        }

        /* Add Animation */
        .modal-content,
        #caption {
            -webkit-animation-name: zoom;
            -webkit-animation-duration: 0.6s;
            animation-name: zoom;
            animation-duration: 0.6s;
        }

        @-webkit-keyframes zoom {
            from {
                -webkit-transform: scale(0)
            }

            to {
                -webkit-transform: scale(1)
            }
        }

        @keyframes zoom {
            from {
                transform: scale(0)
            }

            to {
                transform: scale(1)
            }
        }

        /* The Close Button */
        .close {
            position: absolute;
            top: 15px;
            right: 35px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            transition: 0.3s;
        }

        .close:hover,
        .close:focus {
            color: #bbb;
            text-decoration: none;
            cursor: pointer;
        }

        /* 100% Image Width on Smaller Screens */
        @media only screen and (max-width: 700px) {
            .modal-content {
                width: 100%;
            }
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
                $('.blacklink .hover').css({
                    'font-weight': 'normal',
                    'text-decoration': 'none'
                });
                $(this).css({
                    'font-weight': 'bold',
                    'text-decoration': 'underline'
                });
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
                $('.blacklink .hover').css({
                    'font-weight': 'normal',
                    'text-decoration': 'none'
                });
                $(this).css({
                    'font-weight': 'bold',
                    'text-decoration': 'underline'
                });
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
                $('.blacklink .hover').css({
                    'font-weight': 'normal',
                    'text-decoration': 'none'
                });
                $(this).css({
                    'font-weight': 'bold',
                    'text-decoration': 'underline'
                });
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
                $('.blacklink .hover').css({
                    'font-weight': 'normal',
                    'text-decoration': 'none'
                });
                $(this).css({
                    'font-weight': 'bold',
                    'text-decoration': 'underline'
                });
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
                $('.blacklink .hover').css({
                    'font-weight': 'normal',
                    'text-decoration': 'none'
                });
                $(this).css({
                    'font-weight': 'bold',
                    'text-decoration': 'underline'
                });
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
                $('.blacklink .hover').css({
                    'font-weight': 'normal',
                    'text-decoration': 'none'
                });
                $(this).css({
                    'font-weight': 'bold',
                    'text-decoration': 'underline'
                });
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
                $('.blacklink .hover').css({
                    'font-weight': 'normal',
                    'text-decoration': 'none'
                });
                $(this).css({
                    'font-weight': 'bold',
                    'text-decoration': 'underline'
                });
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
                $('.blacklink .hover').css({
                    'font-weight': 'normal',
                    'text-decoration': 'none'
                });
                $(this).css({
                    'font-weight': 'bold',
                    'text-decoration': 'underline'
                });
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
        <section class="content-header mx-lg-5">
            <div class="container-fluid">
                <div class="row mb-2">

                    <div class="col-sm-6 pl-0">
                        <h1 class="m-0 text-dark text-uppercase font-weight-lighter text-heading">Village Profile

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


            <div class="row anchor  btn-color-darkRed add-button justify-content-end">
                <span class="ml-2"> <a href="{{ url('admin/editvillage/' . $village->village_id) }}">EDIT
                        INFORMATION</a></span> &nbsp |

                <span class="ml-2"> <a href="{{ url('admin/editvillage/' . $village->village_id) }}"> SET
                        PRICE
                    </a></span> &nbsp |
                <span class="ml-2"> <a href="">SET REWARD</a></span>

            </div>
            <hr>
            <div class="row">
                <div class="col-md-4">
                    @if ($village->image == null)
                        <td> <img class="famerimg" src="{{ Storage::disk('s3')->url('images/dumy.png') }}"
                                style="width: 300px ; height:300px; border-radius:50%; border: 1px solid gray;" alt=""></td>
                    @else
                        <td> <img class="famerimg"
                                style="width: 300px ; height:300px; border-radius:50%; border: 1px solid gray;"
                                src="{{ Storage::disk('s3')->url('images/' . $village->image) }}" alt=""></td>
                    @endif

                </div>
                <div class="col-md-8">
                    <table class="table table-borderless w-auto village_profile_table" id="farmerTable">

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
                                <td colspan="4">{{ $village->altitude }}</td>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td colspan=""><strong>VILLAGE INFORMATION</strong></td>
                                <td colspan="4">{{ $village->description }}</td>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td colspan=""><strong>REGIONS INFORMATION</strong></td>
                                <td colspan="4">{{ regDescrption($village->region) }}</td>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td colspan=""><strong>GOVERNORATE INFORMATION
                                    </strong></td>
                                <td colspan="4">{{ govDescrption($village->governrate) }}</td>
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
                                <td colspan="4">{{ $village->reward_per_kg }}</td>
                                <td colspan="4"></td>
                            </tr>



                        </tbody>
                    </table>
                </div>
            </div>

            <hr class="ml-md-2">
            <div class="row ml-2 text-uppercase mb-2">
                <strong>
                    <b>Photo Attached</b>
                </strong>
            </div>
            <div class="row ml-2">
                <div class="col-sm-1 color bg-danger p-0 ml-0" style="display: flex; grid-gap: 8px;">
                    <!-- <h3>{{ App\Village::count() }}</h3>
                                                                                                                                                                                                                                                                                                                                                                                                                                        <p>Villages</p> -->
                    @if ($village->attach_images != null)
                        @php
                            $attachImages = explode('|', $village->attach_images);
                            $i = 1;
                        @endphp
                        @foreach ($attachImages as $img)

                            <img style="max-width: 100%; height: 100%;" onclick=showModal({{ $i }})
                                id="village_{{ $i }}" src="{{ Storage::disk('s3')->url('images/' . $img) }}"
                                alt="">
                            @php
                                $i++;
                            @endphp
                        @endforeach
                    @else
                        <img style="max-width: 100%; height: 100%;"
                            src="{{ Storage::disk('s3')->url('images/dumy.png') }}" alt="">
                    @endif
                </div>



            </div>
            <hr class="ml-md-2">
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
                <span class="ml-2 hover" style="font-weight: bold; text-decoration: underline;"> <a
                        href="{{ route('village.profile', $village) }}">ALL
                        TIME</a></span>
            </div>
            <hr>
            <div id="transaction">
                <div class="ml-2 text-uppercase d-flex flex-wrap p-0 mb-3 data-tabs">
                    <div class="col-sm-1 color bg-darkPurple">
                        <h4>{{ $village->quantity }}</h4>
                        <p>KG CHERRY
                            BOUGHT</p>
                    </div>
                    <div class="col-sm-1 color bg-Green">
                        <h4>{{ $village->price }} </h4>

                        <p>YER TOTAL
                            SPCIAILTY COFFEE
                            PURCHASED</p>
                    </div>
                    <div class="col-sm-1 color bg-darkPurple">
                        <h4>-</h4>

                        <p>KG DRY COFFEE
                            BOUGHT </p>
                    </div>
                    <div class="col-sm-1 color bg-mildGreen">
                        <h4>-</h4>

                        <p>YER TOTAL
                            COMMERCIAL
                            COFFEE
                            PURCHASED</p>
                    </div>
                    <div class="col-sm-1 color bg-darkRed">
                        <h4>-</h4>

                        <p>YER
                            ACCOUNT
                            PAYABLE</p>
                    </div>
                    <div class="col-sm-1 color bg-Green">
                        <h4>-</h4>

                        <p>YER SETTELED</p>
                    </div>
                    <div class="col-sm-1 color bg-lightBrown">
                        <h4>{{ $village->farmerCount }}</h4>

                        <p>
                            TOTAL NUMBER
                            OF FARMERS
                            COFFEE BOUGHT
                            FROM

                        </p>
                    </div>
                    <div class="col-sm-1 color bg-lightGreen">
                        @if ($village->farmers->count() > 0)
                            <h4>{{ $village->quantity / $village->farmers->count() }}</h4>


                        @else
                            <h4>0</h4>
                        @endif
                        <p>KG CHERRY
                            AVERAGE PER
                            FARMER</p>
                    </div>


                </div>
                <hr class="ml-2">
                <div class="row ml-2 text-uppercase mb-2">
                    <strong>
                        <b>Coffee Buyer</b>
                    </strong>
                </div>
                <div class="row ml-2 mb-2">
                    <ul class="list-group list-group-horizontal text-uppercase font-weight-bold w-100 flex-wrap">
                        @foreach ($village->buyers as $buyer)
                            <li class="col-3 list-group-item data-content-list border-0">
                                @if ($buyer->file == null)
                                    <img style="max-width: 30%; width:auto !important;"
                                        src="{{ Storage::disk('s3')->url('images/' . 'dumy.png') }}" id="region_farmer">
                                @else
                                    <img style="max-width: 40%; width:auto !important;"
                                        src="{{ Storage::disk('s3')->url('images/' . $buyer->file->user_file_name) }}"
                                        id="region_farmer">
                                @endif

                                <span class="ml-3 mr-4">{{ $buyer['first_name'] }}</span>
                            </li>
                        @endforeach
                    </ul>

                </div>
                <hr class="ml-2">
                <div class="row ml-2 text-uppercase mb-2">
                    <strong>
                        <b>Farmers</b>
                    </strong>
                </div>
                <div class="row ml-2 mb-2">
                    <ul class="list-group list-group-horizontal text-uppercase font-weight-bold w-100 flex-wrap">
                        @foreach ($village->farmers as $farmer)
                            <li class="col-3 list-group-item data-content-list border-0">
                                @if ($farmer->file == null)
                                    <img style="max-width: 30%; width: auto !important;"
                                        src="{{ Storage::disk('s3')->url('images/' . 'dumy.png') }}" id="region_farmer">
                                @else
                                    <img style="max-width: 30%; width: auto !important;"
                                        src="{{ Storage::disk('s3')->url('images/' . $farmer->file->user_file_name) }}"
                                        id="region_farmer">
                                @endif

                                <span class="ml-3 mr-4">{{ $farmer['farmer_name'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

            </div>

        </section>

        <!-- Main content -->

        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <div id="myModal" class="modal">
        <span class="close">&times;</span>
        <img class="modal-content" id="img01">
        <div id="caption"></div>
    </div>

    <script>
        function showModal(id) {
            var modal = document.getElementById("myModal");

            // Get the image and insert it inside the modal - use its "alt" text as a caption
            var img = document.getElementById('village_' + id);
            // console.log(img.src);
            var modalImg = document.getElementById("img01");
            var captionText = document.getElementById("caption");
            // img.onclick = function() {
            modal.style.display = "block";
            modalImg.src = img.src;
            // console.log(modalImg.src);
            captionText.innerHTML = this.alt;
            // }

            // Get the <span> element that closes the modal
        }
        // Get the modal


        // When the user clicks on <span> (x), close the modal
        $('.close').on('click', function() {
            var modal = document.getElementById("myModal");
            modal.style.display = "none";
        });
        // var span = document.getElementsByClassName("close")[0];
        // span
    </script>
@endsection
