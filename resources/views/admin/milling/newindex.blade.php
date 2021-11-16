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

    </style>
    <style src="//cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css"></style>
    <script src="//cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"> </script>

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
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1><b>Milling Coffee</b></h1>
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
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="card col-lg-12">
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="col-md-12">
                                @if ($errors->any())
                                    <div class="alert alert-danger">

                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach

                                    </div>
                                @endif
                                <form role="form" method="POST" action="{{ URL::to('admin/milling_coffee') }}">
                                    {{ csrf_field() }}
                                    <table id="myTable">
                                        <tr>
                                            <th>Farmer Name</th>
                                            <th>Farmer </th>
                                            <th>Governerate</th>
                                            <th>Region</th>
                                            <th>VIllage</th>
                                            <th>Quantity</th>
                                            <th>Select</th>
                                        </tr>
                                        @foreach ($transactions as $transaction)
                                            <tr>
                                                @if (Str::contains($transaction['transaction']->batch_number, '000'))
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
                                                        @php
                                                            
                                                            $batchNumber = $transaction['transaction']->batch_number;
                                                            $batchExplode = explode('-', $batchNumber);
                                                            $gov = $batchExplode[0];
                                                        @endphp
                                                        <input type="checkbox" data-gov-rate="<?= $gov ?>"
                                                            name="transaction_id[]"
                                                            value="{{ $transaction['transaction']->transaction_id }}"
                                                            class="check_gov{{ $transaction['transaction']->transaction_id }}"
                                                            onClick="checkGov('<?= $gov ?>',{{ $transaction['transaction']->transaction_id }})">
                                                    </td>
                                                @else
                                                    <td>
                                                        {{ getFarmer($transaction['transaction']->batch_number) }}
                                                    </td>
                                                    <td>
                                                        {{ explode('-', $transaction['transaction']->batch_number)[0] . '-' . explode('-', $transaction['transaction']->batch_number)[1] . '-' . explode('-', $transaction['transaction']->batch_number)[2] . '-' . explode('-', $transaction['transaction']->batch_number)[3] }}
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
                                                        @php
                                                            
                                                            $batchNumber = $transaction['transaction']->batch_number;
                                                            $batchExplode = explode('-', $batchNumber);
                                                            $gov = $batchExplode[0];
                                                        @endphp
                                                        <input type="checkbox" data-gov-rate="<?= $gov ?>"
                                                            name="transaction_id[]"
                                                            value="{{ $transaction['transaction']->transaction_id }}"
                                                            class="check_gov{{ $transaction['transaction']->transaction_id }}"
                                                            onClick="checkGov('<?= $gov ?>',{{ $transaction['transaction']->transaction_id }})">
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </table>
                                    <div class="card-footer">
                                        <button type="submit" id="submitbtn" class="btn btn-primary">Submit</button>
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

        });
    </script>
@endsection
