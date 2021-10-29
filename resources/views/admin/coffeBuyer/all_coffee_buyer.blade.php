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

    .blacklink a {

        color: rgb(0, 0, 0);
        background-color: transparent;
        text-decoration: none;
        font-size: 14px;

    }

    a {
        color: rgb(0, 0, 0);
    }

    .famerimg {

        width: 50px;
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
            $('.all_coffee_buyers').css({
                'font-weight': 'normal',
                'text-decoration': 'none'
            });
            let from = e.target.value;
            $.ajax({
                url: "{{ url('admin/filterBygovernrate') }}",
                type: "GET",
                data: {
                    'from': from,

                },
                success: function(data) {
                    console.log(data.view);
                    $('#regions_dropdown').empty();

                    let html =
                        ' <option value="0" selected disabled>Select Region</option>';
                    data.regions.forEach(region => {
                        html += '<option value="' + region.region_id + '">' + region
                            .region_title + '</option>';
                    });


                    $('#regions_dropdown').append(html);
                    $('#tables').html(data.view);

                }
            });
        });
        $('#regions_dropdown').on('change', function(e) {
            $('.all_coffee_buyers').css({
                'font-weight': 'normal',
                'text-decoration': 'none'
            });
            let from = $('#regions_dropdown').val();
            // let from = e.target.value;

            $.ajax({
                url: "{{ url('admin/filterByregions') }}",
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
                    $('#tables').html(data.view);
                    console.log(data);


                }
            });
        });
        $('#village_dropdown').on('change', function(e) {
            $('.all_coffee_buyers').css({
                'font-weight': 'normal',
                'text-decoration': 'none'
            });
            // let from = $('#regions_dropdown').val();
            let from = e.target.value;
            $.ajax({
                url: "{{ url('admin/filterByvillage') }}",
                type: "GET",
                data: {
                    'from': from,

                },
                success: function(data) {
                    $('#tables').html(data.view);
                    console.log(data.view);
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
                <div class="row mb-2">

                    <div class="col-sm-6 pl-0">
                        <h1 class="m-0 text-dark text-uppercase font-weight-lighter text-heading">COFFEE BUYERS
                            {{-- <a href="{{ URL::to('') }}/admin/add_farmer" class="btn btn-add rounded-circle">
                            <i class="fas fa-user-plus add-client-icon"></i>
                            </a> --}}
                        </h1>
                    </div>
                    <hr>
                    <div class="col-sm-6 d-flex justify-content-end align-items-end">
                        <ol class="breadcrumb float-sm-right">
                        <a href="{{ url('admin/adduser') }}" class="btn btn-add rounded-circle p-0">
                        <button class="btn btn-color-darkRed btn-dark bg-transparent border-0 add-button text-uppercase">Add Coffee Buyer</button>
                        </a>
                        <button class="btn btn-color-darkRed btn-dark bg-transparent border-0 add-button text-uppercase p-0">&nbsp;|</button>
                        <a href="{{ url('admin/adduser') }}" class="btn btn-add rounded-circle p-0">
                            <button class="btn btn-color-darkRed btn-dark bg-transparent border-0 add-button text-uppercase">Add Coffee Buying Manager</button>
                        </a>
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
            <span class=""> <a href="{{ url('admin/coffeeBuyerByDate/' . ($date = 'today')) }}">TODAY</a></span> &nbsp
            |
            <span class="ml-md-2 hover"> <a href="{{ url('admin/coffeeBuyerByDate/' . ($date = 'yesterday')) }}">
                    YESTERDAY</a></span>
            &nbsp |
            <span class="ml-md-2 hover"> <a href="{{ url('admin/coffeeBuyerByDate/' . ($date = 'weekToDate')) }}"> WEEK
                    TO DATE
                </a></span>
            &nbsp |
            <span class="ml-md-2 hover"> <a href="{{ url('admin/coffeeBuyerByDate/' . ($date = 'monthToDate')) }}">MONTH
                    TO
                    DATE</a></span>
            &nbsp |
            <span class="ml-md-2 hover"> <a href="{{ url('admin/coffeeBuyerByDate/' . ($date = 'lastmonth')) }}"> LAST
                    MONTH</a></span>
            &nbsp |
            <span class="ml-md-2 hover"> <a href="{{ url('admin/coffeeBuyerByDate/' . ($date = 'yearToDate')) }}">YEAR
                    TO
                    DATE</a></span>
            &nbsp |
            <span class="ml-md-2 hover"> <a href="{{ url('admin/coffeeBuyerByDate/' . ($date = 'currentyear')) }}"> 2021
                    SEASON</a></span>
            &nbsp |
            <span class="ml-md-2 hover"> <a href="{{ url('admin/coffeeBuyerByDate/' . ($date = 'lastyear')) }}"> 2020
                    SEASON</a></span>
            &nbsp |
            <span class="ml-md-2 hover"> <a href="{{ url('admin/allcoffeebuyer') }}"> ALL TIME</a></span>
        </div>
        <hr class="ml-md-2">
        <div class="row ml-2 text-uppercase mb-2">
            <strong>
                <b>REGION FILTER</b>
            </strong>
        </div>
        <div class="row ml-2">
            <div class="col-md-12 pl-0 text-uppercase">
                <span class="all_coffee_buyers" style="font-weight: bold; text-decoration: underline;">
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
                        <div class="row ml-2" id="tables">
                            <div class="col-md-12 p-0">

                                <div class="card shadow-none">
                                    <div class="table-responsive text-uppercase letter-spacing-2 coffee_buyeers_table">
                                        <table class="table table-borderless border-0 custom-table text-center" style="border-collapse: separate; font-size:13px;">
                                            <thead>
                                                <tr>
                                                    <th colspan="12" class="border-0 px-0 text-left">
                                                        <div class="text-uppercase pl-0">
                                                            Coffee Buying Managers
                                                        </div>
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th class="border-0"></th>
                                                    <th class="border border-dark font-weight-lighter">Name</th>
                                                    <th class="border border-dark border-left-0 font-weight-lighter">First Purchase</th>
                                                    <th class="border border-dark border-left-0 font-weight-lighter">Last Purchase</th>
                                                    <th class="border border-dark border-left-0 font-weight-lighter">City</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($coffeeBuyerMangers as $coffeeBuyerManger)
                                                <tr>

                                                    @if ($coffeeBuyerManger->picture_id == null)
                                                    <td class="border-0"> <img class="famerimg mr-2" src="{{ Storage::disk('s3')->url('images/' . 'dumy.png') }}" alt="">
                                                    </td>
                                                    @else
                                                    <td class="border-0"> <img class="famerimg mr-2" src="{{ Storage::disk('s3')->url('images/' . $coffeeBuyerManger->image) }}" alt=""></td>
                                                    @endif
                                                    <td class="border border-dark border-top-0">{{ $coffeeBuyerManger->first_name }}</td>
                                                    <td class="border border-dark border-left-0 border-top-0">{{ $coffeeBuyerManger->first_purchase }}</td>
                                                    <td class="border border-dark border-left-0 border-top-0">{{ $coffeeBuyerManger->last_purchase }}</td>
                                                    <td class="border border-dark border-left-0 border-top-0">lahore</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <table class="table table-borderless border-0 custom-table text-center" style="border-collapse: separate; font-size:13px;">
                                            <thead>
                                                <tr>
                                                    <th colspan="12" class="border-0 px-0">
                                                        <div class="text-uppercase pl-0 font-weight-lighter table_headers">
                                                            Specialty
                                                        </div>
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th class="border border-dark font-weight-lighter">CHREEY BOUGHT</th>
                                                    <th class="border border-dark border-left-0 font-weight-lighter">PRICE PAID</th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($coffeeBuyerMangers as $coffeeBuyerManger)
                                                <tr>
                                                    <td class="border border-dark border-top-0">{{ $coffeeBuyerManger->special_weight }}</td>
                                                    <td class="border border-dark border-left-0 border-top-0">{{ $coffeeBuyerManger->special_price }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <table class="table table-borderless border-0 custom-table text-center" style="border-collapse: separate; font-size:13px;">
                                            <thead>
                                                <tr>
                                                    <th colspan="12" class="border-0 px-0">
                                                        <div class="text-uppercase pl-0 font-weight-lighter table_headers">
                                                            Non Specialty
                                                        </div>
                                                    </th>
                                                </tr>
                                                <th class="border border-dark font-weight-lighter"> <span>DRY COFFEE</span> BOUGHT</th>
                                                <th class="border border-dark border-left-0 font-weight-lighter">PRICE PAID</th>
                                                <th class="border border-dark border-left-0 font-weight-lighter">Total Price Paid</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($coffeeBuyerMangers as $coffeeBuyerManger)
                                                <tr>

                                                    <td class="border border-dark border-top-0">{{ $coffeeBuyerManger->non_special_weight }}</td>
                                                    <td class="border border-dark border-left-0 border-top-0">{{ $coffeeBuyerManger->non_special_price }}</td>
                                                    <td class="border border-dark border-left-0 border-top-0">{{ $coffeeBuyerManger->special_price + $coffeeBuyerManger->non_special_price }}</td>

                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <table class="table table-borderless border-0 custom-table text-center" style="border-collapse: separate; font-size:13px;">
                                            <thead>

                                                <tr>
                                                    <th colspan="12" class="border-0 px-0 invisible">
                                                        <div class="text-uppercase pl-0 font-weight-lighter table_headers">
                                                        View Details
                                                        </div>
                                                    </th>
                                                </tr>
                                                <th class="border border-dark font-weight-lighter">View Details</th>
                                                </tr>

                                            </thead>
                                            <tbody>
                                                @foreach ($coffeeBuyerMangers as $coffeeBuyerManger)
                                                <tr>
                                                    <td class="border border-dark border-top-0"> <a href="{{ route('coffeBuyer.profile', $coffeeBuyerManger) }}"><i class="fas fa-eye"></i></a></td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <hr>


                                <div class="card shadow-none">
                                    <div class="table-responsive text-uppercase letter-spacing-2 coffee_buyeers_table">

                                    <table class="table table-borderless border-0 custom-table text-center" style="border-collapse: separate; font-size:13px;">
                                            <thead>
                                                <tr>
                                                    <th colspan="12" class="border-0 px-0 text-left">
                                                        <div class="text-uppercase pl-0">
                                                            Coffee Buyers
                                                        </div>
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th class="border-0"></th>
                                                    <th class="border border-dark font-weight-lighter">Name</th>
                                                    <th class="border border-dark border-left-0 font-weight-lighter">First Purchase</th>
                                                    <th class="border border-dark border-left-0 font-weight-lighter">Last Purchase</th>
                                                    <th class="border border-dark border-left-0 font-weight-lighter">City</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($coffeeBuyers as $coffeeBuyer)
                                                <tr>

                                                    @if ($coffeeBuyer->picture_id == null)
                                                    <td class="border-0"> <img class="famerimg" src="{{ Storage::disk('s3')->url('images/' . 'dumy.png') }}" alt="">
                                                    </td>
                                                    @else
                                                    <td class="border-0"> <img class="famerimg" src="{{ Storage::disk('s3')->url('images/' . $coffeeBuyer->image) }}" alt=""></td>
                                                    @endif
                                                    <td class="border border-dark border-top-0">{{ $coffeeBuyer->first_name }}</td>
                                                    <td class="border border-dark border-left-0 border-top-0">{{ $coffeeBuyer->first_purchase }}</td>
                                                    <td class="border border-dark border-left-0 border-top-0">{{ $coffeeBuyer->last_purchase }}</td>
                                                    <td class="border border-dark border-left-0 border-top-0">lahore</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <table class="table table-borderless border-0 custom-table text-center" style="border-collapse: separate; font-size:13px;">
                                            <thead>
                                                <tr>
                                                    <th colspan="12" class="border-0 px-0">
                                                        <div class="text-uppercase pl-0 font-weight-lighter table_headers">
                                                            Specialty
                                                        </div>
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th class="border border-dark font-weight-lighter">CHREEY BOUGHT</th>
                                                    <th class="border border-dark border-left-0 font-weight-lighter">PRICE PAID</th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                               @foreach ($coffeeBuyers as $coffeeBuyer)
                                                <tr>
                                                    <td class="border border-dark border-top-0">{{ $coffeeBuyer->special_weight }}</td>
                                                    <td class="border border-dark border-left-0 border-top-0">{{ $coffeeBuyer->special_price }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <table class="table table-borderless border-0 custom-table text-center" style="border-collapse: separate; font-size:13px;">
                                            <thead>
                                                <tr>
                                                    <th colspan="12" class="border-0 px-0">
                                                        <div class="text-uppercase pl-0 font-weight-lighter table_headers">
                                                            Non Specialty
                                                        </div>
                                                    </th>
                                                </tr>
                                                <th class="border border-dark font-weight-lighter"> <span>DRY COFFEE</span> BOUGHT</th>
                                                <th class="border border-dark border-left-0 font-weight-lighter">PRICE PAID</th>
                                                <th class="border border-dark border-left-0 font-weight-lighter">Total Price Paid</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($coffeeBuyers as $coffeeBuyer)
                                                <tr>

                                                    <td class="border border-dark border-top-0">{{ $coffeeBuyer->non_special_weight }}</td>
                                                    <td class="border border-dark border-left-0 border-top-0">{{ $coffeeBuyer->non_special_price }}</td>
                                                    <td class="border border-dark border-left-0 border-top-0">{{ $coffeeBuyer->special_price + $coffeeBuyer->non_special_price }}</td>

                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <table class="table table-borderless border-0 custom-table text-center" style="border-collapse: separate; font-size:13px;">
                                            <thead>

                                                <tr>
                                                    <th colspan="12" class="border-0 px-0 invisible">
                                                        <div class="text-uppercase pl-0 font-weight-lighter table_headers">
                                                        View Details
                                                        </div>
                                                    </th>
                                                </tr>
                                                <th class="border border-dark font-weight-lighter">View Details</th>
                                                </tr>

                                            </thead>
                                            <tbody>
                                                @foreach ($coffeeBuyers as $coffeeBuyer)
                                                <tr>
                                                    <td class="border border-dark border-top-0"> <a href="{{ route('coffeBuyer.profile', $coffeeBuyer) }}"><i class="fas fa-eye"></i></a></td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
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

</div>
<!-- /.content-wrapper -->


@endsection
