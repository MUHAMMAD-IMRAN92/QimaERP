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
            width: 3% !important;

        }
    </style>


    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">

        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">

                    <div class="col-sm-6">
                        <h1> Profile

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
                <span class="ml-5" style="margin-left: 60% !important"> <a
                        href="{{ url('admin/edituser/' . $buyer->id) }}">EDIT
                        INFORMATION</a></span> &nbsp |
                <span class="ml-2"> <a href=""> ADD PANELTY</a></span>

            </div>
            <hr>
            <div class="row">
                <div class="col-md-4">
                    @if ($buyer->image == null)
                        <td> <img class="famerimg ml-4" src="{{ asset('public/images/farmericon.png') }}"
                                style="width: 200px ; height:200px; border-radius:50%; border: 1px solid gray;" alt=""></td>
                    @else
                        <td> <img class="famerimg ml-4"
                                style="width:  200px ; height: 200px; border-radius:50%; border: 1px solid gray;"
                                src="{{ asset('public/storage/images/' . $buyer->image) }}" alt=" no img"></td>
                    @endif

                </div>
                <div class="col-md-8">
                    <table class="table table-borderless" id="farmerTable">

                        <tbody>
                            <tr>
                                <td colspan=""> <strong>Name</strong> </td>
                                <td colspan="4">{{ $buyer->first_name }}</td>
                                <td colspan="4"></td>
                            </tr>
                            {{-- <tr>
                                <td colspan=""><strong>IDENTIFICATION NUMBER</strong></td>
                                <td colspan="4">{{ $buyer->farmer_nicn }}</td>
                                <td colspan="4"></td>
                            </tr> --}}
                            <tr>
                                <td colspan=""><strong>City </strong></td>
                                <td colspan="4">lahore</td>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td colspan=""><strong>Tel</strong></td>
                                <td colspan="4">123456789</td>
                                <td colspan="4"></td>
                            </tr>
                            <tr>
                                <td colspan=""><strong>Start Date</strong></td>
                                <td colspan="4">{{ $buyer->created_at }}</td>
                                <td colspan="4"></td>
                            </tr>


                        </tbody>
                    </table>
                </div>
            </div>
            <hr>

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
                <span class="ml-2"> <a href="">TODAY</a></span> &nbsp |
                <span class="ml-2"> <a href=""> YESTERDAY</a></span>
                &nbsp |
                <span class="ml-2"> <a href=""> WEEK TO DATE </a></span> &nbsp |
                <span class="ml-2"> <a href="">MONTH TO DATE</a></span> &nbsp |
                <span class="ml-2"> <a href=""> LAST MONTH</a></span> &nbsp |
                <span class="ml-2"> <a href="">YEAR TO DATE</a></span> &nbsp |
                <span class="ml-2"> <a href=""> 2021 SEASON</a></span> &nbsp |
                <span class="ml-2"> <a href=""> 2021 SEASON</a></span> &nbsp |
                <span class="ml-2"> <a href=""> 2020 SEASON</a></span> &nbsp |
                <span class="ml-2"> <a href=""> ALL TIME</a></span>
            </div>
            <hr>
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
                <div class="col-sm-1 color bg-info"></div>
                <div class="col-sm-1 color bg-dark"></div>
                <div class="col-sm-1 color bg-danger"></div>
                <div class="col-sm-1 color bg-success"></div>

            </div>
            <hr>
            <div class="row ml-2 blacklink">

                <a href="" class="ml-2">
                    <p>ID DOCUMENTS &nbsp;|</p>
                </a>
                <a href="" class="ml-2">
                    <p> RECIEPTS &nbsp;|</p>
                </a>
                <a href="" class="ml-2">
                    <p>LOANS &nbsp;|</p>
                </a>
                <a href="" class="ml-2">
                    <p>PREMIUMS &nbsp;</p>
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
            <hr>
            <b>
                <p> Villages Responsible For
                </p>
            </b>
            @foreach ($buyer->villages as $village)
                @if ($village['picture_id'] == null)
                    <img src="{{ asset('public/images/farmericon.png') }}" alt="no img" id="region_farmer">
                @else
                    <img src="{{ asset('public/storage/images/' . $farmer['picture_id']) }}" alt="no img"
                        id="region_farmer">
                @endif

                <span class="mr-3">{{ $village['village_title'] }}</span>
            @endforeach

            <hr>
            <hr>
            <b>
                <p>Farmers Bought From
                </p>
            </b>
            @foreach ($buyer->farmers as $farmer)
                @if ($farmer['farmer_image'] == null)
                    <img src="{{ asset('public/images/farmericon.png') }}" alt="no img" id="region_farmer">
                @else
                    <img src="{{ asset('public/storage/images/' . $farmer['farmer_image'] ) }}" alt="no img"
                        id="region_farmer">
                @endif

                <span class="mr-3">{{ $farmer['farmer_name'] }}</span>
            @endforeach
            <hr>
            <b>
                <p>TRANSACTIONS </p>
            </b>
            @foreach ($buyer['transactions'] as $transaction)
                <div class="row ml-2">
                    <div class="">
                        <ol class="breadcrumb float-sm-right txt-size">
                            <li class="breadcrumb-item active">
                                {{ $transaction->created_at }} /{{$buyer->first_name}} / {{ $buyer->last_name }} /
                                
                                {{$transaction->details->sum('container_weight')}}.00

                            </li>
                        </ol>
                    </div>
                </div>
            @endforeach

        </section>

        <!-- Main content -->

        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->


@endsection
