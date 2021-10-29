@extends('layouts.default')
@section('title', 'All Transection')
@section('content')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.6.0/chart.min.js"></script>
    <style type="text/css">
        .dataTables_wrapper .dataTables_filter input {
            margin-left: 0.5em;
            width: 240px;
        }


        <style type="text/css">.dataTables_wrapper .dataTables_filter input {
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
        }

        .blacklink a {

            color: rgb(0, 0, 0);
            background-color: transparent;
            text-decoration: none;
            font-size: 14px;

        }

        .famerimg {

            width: 50px;
            border-radius: 50%;
        }

    </style>
    <script>
        $(document).ready(function() {
            $("#to").on('change', function() {
                let from = $("#from").val();
                let to = $("#to").val();

                $.ajax({

                    url: "{{ url('admin/supplyChainDate') }}",
                    type: "GET",
                    data: {
                        'from': from,
                        'to': to
                    },
                    success: function(data) {
                        $('#canvas-div').replaceWith(data);
                        console.log(data);
                    }
                });
            });
        });
    </script>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="mx-lg-5">

            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2 border-bottom-lightGray">

                        <div class="col-sm-8">
                            <h1 class="m-0 text-dark text-uppercase font-weight-lighter text-heading">Supply Chain

                            </h1>
                        </div>
                        <hr>
                        <div class="col-sm-4 d-flex justify-content-end align-items-end">


                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Supply Chain</li>
                            </ol>


                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

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
                <span class="hover"> <a
                        href="{{ url('admin/supplyChain/' . ($date = 'today')) }}">TODAY</a></span> &nbsp |
                <span class="ml-md-2 hover"> <a href="{{ url('admin/supplyChain/' . ($date = 'yesterday')) }}">
                        YESTERDAY</a></span>
                &nbsp |
                <span class="ml-md-2 hover"> <a href="{{ url('admin/supplyChain/' . ($date = 'weekToDate')) }}"> WEEK
                        TO
                        DATE
                    </a></span>
                &nbsp |
                <span class="ml-md-2 hover"> <a href="{{ url('admin/supplyChain/' . ($date = 'monthToDate')) }}">MONTH
                        TO
                        DATE</a></span>
                &nbsp |
                <span class="ml-md-2 hover"> <a href="{{ url('admin/supplyChain/' . ($date = 'lastmonth')) }}"> LAST
                        MONTH</a></span>
                &nbsp |
                <span class="ml-md-2 hover"> <a href="{{ url('admin/supplyChain/' . ($date = 'yearToDate')) }}">YEAR
                        TO
                        DATE</a></span>
                &nbsp |
                <span class="ml-md-2 hover"> <a href="{{ url('admin/supplyChain/' . ($date = 'currentyear')) }}">
                        2021
                        SEASON</a></span>
                &nbsp |
                <span class="ml-md-2 hover"> <a href="{{ url('admin/supplyChain/' . ($date = 'lastyear')) }}"> 2020
                        SEASON</a></span>
                &nbsp |
                <span class="ml-md-2 hover"> <a href="{{ url('admin/supplyChain') }}"> ALL TIME</a></span>
            </div>
            <hr class="ml-md-2">
            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-md-12" id="canvas-div">
                        <canvas id="myChart" width="100%"></canvas>
                        <script>
                            new Chart(document.getElementById('myChart').getContext('2d'), {
                                type: 'bar',
                                data: {
                                    labels: @json($managerName),
                                    datasets: [{
                                            label: "Year To Date",


                                            backgroundColor: [
                                                'rgb(172, 0, 0 )',
                                                'rgb(122, 33, 29)',
                                                'rgb(102, 0, 0)',
                                                'rgb(135, 93, 65)',
                                                'rgb(175, 151, 112)',
                                                'rgb(176, 216, 173)',
                                                'rgb(70, 125, 52)',
                                                'rgb(139, 101, 20)',
                                                'rgb(179, 203, 69)',
                                                'rgb(119, 50, 94)',
                                                'rgb(120, 66, 128)',
                                                'rgb(119, 97, 130)'
                                            ],
                                            data: @json($weightLabel)
                                        },
                                        {
                                            label: @json($barLabel),

                                            backgroundColor: [
                                                'rgb(172, 0, 0)',
                                                'rgb(122, 33, 29)',
                                                'rgb(102, 0, 0)',
                                                'rgb(135, 93, 65)',
                                                'rgb(175, 151, 112)',
                                                'rgb(176, 216, 173)',
                                                'rgb(70, 125, 52)',
                                                'rgb(139, 101, 20)',
                                                'rgb(179, 203, 69)',
                                                'rgb(119, 50, 94)',
                                                'rgb(120, 66, 128)',
                                                'rgb(119, 97, 130)'

                                            ],
                                            data: @json($weightToday)
                                        },
                                    ]
                                },
                                options: {
                                    legend: {
                                        display: false
                                    },
                                    scales: {
                                        yAxes: {
                                            beginAtZero: true
                                        },
                                    },

                                }
                            });
                        </script>
                    </div>
                </div>



            </section>
            <!-- /.content -->
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            $('#myTable').DataTable();
        });
        // console.log(@json($managerName));
    </script>

@endsection
