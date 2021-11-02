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
        border-spacing: 0px;
    }

    #farmerTable tr td {
        padding: unset !important;
        padding: 0 20px !important;
    }
    #farmerTable tr td:first-child{
    border-right: 1px solid rgba(0,0,0,.1);
    }
    #farmerTable tr:first-child td{
    padding-top: 1rem !important;
    }
    #farmerTable tr:last-child td{
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

            <span class="ml-2"> <a href="{{ url('admin/editvillage/' . $village->village_id) }}"> OVERRIDE
                    PRICE
                </a></span> &nbsp |
            <span class="ml-2"> <a href="">OVERRIDE REWARD</a></span>

        </div>
        <hr class="ml-2 mb-0">
        <div class="row">
            <div class="col-md-4 my-3">
                @if ($village->picture_id == null)
                <td> <img class="famerimg" src="{{ asset('public/images/farmericon.png') }}" style="width: 300px ; height:300px; border-radius:50%; " alt=""></td>
                @else
                <td> <img class="famerimg" style="width: 300px ; height:300px; border-radius:50%; " src="{{ asset('public/storage/images/' . $village->image) }}" alt=""></td>
                @endif

            </div>
            <div class="col-md-8">
                <table class="table table-borderless w-auto village_profile_table mb-0" id="farmerTable">

                    <tbody>
                        <tr>
                            <td colspan=""> <strong>Village</strong> </td>
                            <td colspan="4">{{ $village->village_title }}</td>
                         </tr>
                        <tr>
                            <td colspan=""><strong>Region</strong></td>
                            <td colspan="4">{{ $village->region }}</td>
                         </tr>

                        <tr>
                            <td colspan=""><strong>GOVERNORATE</strong></td>
                            <td colspan="4">{{ $village->governrate }}</td>
                         </tr>
                        <tr>
                            <td colspan=""><strong>ALTITUDE</strong></td>
                            <td colspan="4">pending</td>
                         </tr>
                        <tr>
                            <td colspan=""><strong>VILLAGE INFORMATION</strong></td>
                            <td colspan="4">pending</td>
                         </tr>
                        <tr>
                            <td colspan=""><strong>REGIONS INFORMATION</strong></td>
                            <td colspan="4">pending</td>
                         </tr>
                        <tr>
                            <td colspan=""><strong>GOVERNORATE INFORMATION
                                </strong></td>
                            <td colspan="4">pending</td>
                         </tr>
                        <tr>
                            <td colspan=""><strong>NUMBER OF FARMERS
                                </strong></td>
                            <td colspan="4">{{ count($village->farmers) }}</td>
                         </tr>
                        <tr>
                            <td colspan=""><strong>PRICE PER KG</strong></td>
                            <td colspan="4">{{ $village->price_per_kg }}</td>
                         </tr>
                        <tr>
                            <td colspan=""><strong>REWARD PER KG</strong></td>
                            <td colspan="4">pending</td>
                         </tr>



                    </tbody>
                </table>
            </div>
        </div>

        <hr class="ml-md-2 mt-0">
        <div class="row ml-2 text-uppercase mb-2">
            <strong>
                <b>Photo Attached</b>
            </strong>
        </div>
        <div class="row ml-2">
            <div class="col-sm-1 color bg-danger p-0 ml-0">
                <!-- <h3>{{ App\Village::count() }}</h3>
                    <p>Villages</p> -->
                <img style="max-width: 100%; height: 100%;" src="https://images.pexels.com/photos/2662116/pexels-photo-2662116.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=650&w=940" alt="">
            </div>
            <div class="col-sm-1 color bg-primary p-0 ml-2">
                <!-- <h3>{{ App\Farmer::count() }}</h3>
                    <p>Farmers</p> -->
                <img style="max-width: 100%; height: 100%;" src="https://images.pexels.com/photos/2662116/pexels-photo-2662116.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=650&w=940" alt="">
            </div>
            <div class="col-sm-1 color bg-warning p-0 ml-2">
                <!-- <h3>{{ App\User::count() }}</h3>
                    <p>User </p> -->
                <img style="max-width: 100%; height: 100%;" src="https://images.pexels.com/photos/2662116/pexels-photo-2662116.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=650&w=940" alt="">
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
            <span class="ml-2 hover" style="font-weight: bold; text-decoration: underline;"> <a href="{{ route('village.profile', $village) }}">ALL
                    TIME</a></span>
        </div>
        <hr class="ml-2">

        <div class="ml-2 text-uppercase d-flex flex-wrap p-0 mb-3 data-tabs" id="transaction">
            <div class="col-sm-1 color bg-darkPurple mr-1">
                <h4>{{ $village->first_purchase }}</h4>
                <p>First Purchade</p>
            </div>
            <div class="col-sm-1 color bg-Green mr-1">
                <h4>{{ $village->last_purchase }} </h4>

                <p>Last Purchase</p>
            </div>
            <div class="col-sm-1 color bg-darkPurple mr-1">
                <h4>{{ $village->price }}</h4>

                <p>yer total coffee purchased </p>
            </div>
            <div class="col-sm-1 color bg-mildGreen mr-1">
                <h4>{{ $village->quantity }}</h4>

                <p>Quantity</p>
            </div>
            <div class="col-sm-1 color bg-darkRed mr-1"></div>
            <div class="col-sm-1 color bg-Green mr-1"></div>
            <div class="col-sm-1 color bg-lightBrown mr-1"></div>
            <div class="col-sm-1 color bg-lightGreen mr-1"></div>


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
                    <img style="max-width: 30%; width:auto !important;" src="{{ asset('public/images/farmericon.png') }}" id="region_farmer">
                    @else
                    <img style="max-width: 40%; width:auto !important;" src="{{ asset('public/storage/images/' . $buyer->file->user_file_name) }}" id="region_farmer">
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
                    <img style="max-width: 30%; width: auto !important;" src="{{ asset('public/images/farmericon.png') }}" id="region_farmer">
                    @else
                    <img style="max-width: 30%; width: auto !important;" src="{{ asset('public/storage/images/' . $farmer->file->user_file_name) }}" id="region_farmer">
                    @endif

                    <span class="ml-3 mr-4">{{ $farmer['farmer_name'] }}</span>
                </li>
                @endforeach
            </ul>
        </div>



    </section>

    <!-- Main content -->

    <!-- /.content -->
</div>
<!-- /.content-wrapper -->


@endsection
