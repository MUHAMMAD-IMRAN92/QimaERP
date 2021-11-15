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
<script>
    $(document).ready(function() {
        $('#myTable').DataTable();
    });
</script>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <div class="mx-lg-5">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6 pl-0">
                        <h1 class="m-0 text-dark text-uppercase font-weight-lighter text-heading">Inventory</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Inventory</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>


        <!-- Static data -->
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
            <span class="ml-md-2" style="font-weight: bold; text-decoration: underline;"> <a href="{{ url('/admin/allregion') }}">ALL
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
                    <option value="0" selected disabled>Select inventory</option>

                    <option value="1">1
                    </option>


                </select>
                <span class="ml-md-2">
                    Sub Region
                </span>
                <select class="ml-md-2" name="" id="governorate_dropdown">
                    <option value="0" selected disabled>Select Region</option>

                    <option value="1">1
                    </option>


                </select>
                <span class="ml-md-2">
                    Village
                </span>
                <select class="ml-md-2" name="" id="governorate_dropdown">
                    <option value="0" selected disabled>Select Village</option>

                    <option value="1">1
                    </option>


                </select>
            </div>

        </div>
        <hr>
        <div class="col-lg-12 ml-md-2 text-uppercase d-flex flex-wrap p-0 mb-3 data-tabs" id="transactions">
            <div class="col-sm-1 color bg-darkPurple p-2 content-box mr-1">
                <h4>0</h4>
                <p>Governorate</p>
            </div>
            <div class="col-sm-1 color bg-darkGreen p-2 content-box mr-1">
                <h4>0</h4>

                <p>Regions</p>
            </div>
            <div class="col-sm-1 color bg-darkGreen p-2 content-box mr-1">
                <h4>0</h4>

                <p>Villages </p>
            </div>
            <div class="col-sm-1 color bg-Green p-2 content-box mr-1">
                <h4>0</h4>

                <p>Farmers </p>
            </div>
            <div class="col-sm-1 color bg-darkRed p-2 content-box mr-1">
                <h4>0</h4>
                <p>Quantity </p>
            </div>
            <div class="col-sm-1 color bg-darkGreen p-2 content-box mr-1">
                <h4>0</h4>
                <p>yer coffee bought </p>
            </div>
            <div class="col-sm-1 color bg-lightBrown p-2 content-box mr-1">

            </div>
            <div class="col-sm-1 color bg-darkPurple p-2 content-box mr-1"></div>
            <div class="col-sm-1 color bg-darkPurple p-2 content-box mr-1"></div>
            <div class="col-sm-1 color bg-darkPurple p-2 content-box mr-1"></div>
            <div class="col-sm-1 color bg-lightGreen p-2 content-box mr-1"></div>
        </div>
        <hr>
        <!-- end static data -->

        <!-- Main content -->

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="card col-lg-12">
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="col-md-6">
                                @if ($errors->any())
                                <div class="alert alert-danger">

                                    @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </div>
                                @endif
                                @if (session()->has('msg'))
                                <div class="alert alert-success" id="alert">
                                    {{ session()->get('msg') }}
                                </div>
                                @endif
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <h2 class="text-center">Normal Products</h2>
                                    @foreach ($products as $product)
                                    <div class="row">
                                        <div class="col-md-10 offset-1">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h5 class="card-title">{{ $product['name'] }}</h5>
                                                    <p class="card-text">Raw Weight:
                                                        <b>{{ $product['rawWeight'] }}</b>
                                                    </p>
                                                    <p class="card-text">Bags Weight:
                                                        <b>{{ $product['bagWight'] }}</b>
                                                    </p>
                                                    <p class="card-text"> Weight: <b>{{ $product['weight'] }}</b></p>
                                                    {{-- <a href="#" class="btn btn-primary">Go somewhere</a> --}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                    <div class="row">
                                        <div class="col-md-10 offset-1">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h5 class="card-title">Grade 1 Green Coffee</h5>

                                                    <p class="card-text"> Weight: <b>{{ $nonspecialgradeonecfe }}</b></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h2 class="text-center">Special Products</h2>
                                    @foreach ($special_products as $product)
                                    <div class="row">
                                        <div class="col-md-10 offset-1">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h5 class="card-title">{{ $product['name'] }}</h5>
                                                    <p class="card-text">Raw Weight:
                                                        <b>{{ $product['rawWeight'] }}</b>
                                                    </p>
                                                    <p class="card-text">Bags Weight:
                                                        <b>{{ $product['bagWight'] }}</b>
                                                    </p>
                                                    <p class="card-text"> Weight: <b>{{ $product['weight'] }}</b></p>
                                                    {{-- <a href="#" class="btn btn-primary">Go somewhere</a> --}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                    <div class="row">
                                        <div class="col-md-10 offset-1">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h5 class="card-title">Special Grade 1 Green Coffee</h5>

                                                    <p class="card-text"> Weight: <b>{{ $speciallgradeonecfe }}</b></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
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
    setTimeout(function() {
        document.getElementById('alert').remove();
    }, 3000)
</script>

@endsection
