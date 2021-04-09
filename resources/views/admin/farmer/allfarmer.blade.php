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

        a {

            color: rgb(0, 0, 0);
            background-color: transparent;
            text-decoration: none;

        }

    </style>
    <script>
        $(document).ready(function() {
            $('#myTable').DataTable();
            $('#to').on('change', function() {
                let from = $('#from').val();
                let to = $('#to').val();
           
                $.ajax({
                    url: "{{ url('admin/filter_farmers') }}" ,
                    type: "GET" ,
                    data: {
                        'from': from ,
                        'to': to
                    },
                   success: function(data) {
                         
                          $('#famerstable').html(data);
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
                        <h1>FARMERS
                            <a href="{{ URL::to('') }}/admin/add_farmer" class="btn btn-add rounded-circle">
                                <i class="fas fa-user-plus add-client-icon"></i>
                            </a>
                        </h1>
                    </div>
                    <hr>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">All Farmer</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
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
        <br>
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
            <strong>
                <b>REGION FILTER</b>
            </strong>
        </div>
        <div class="row">
            <div class="col-md-12">
                <b class="ml-2"><a href=""> All Regions</a></b> |
                Governrate <select name="" id="">
                    @foreach ($governorates as $governorate)
                        <option value="{{ $governorate->governerate_id }}">{{ $governorate->governerate_title }}
                        </option>
                    @endforeach

                </select>
                Sub Region <select name="" id="">
                    @foreach ($regions as $region)
                        <option value="{{ $region->region_title }}">{{ $region->region_title }}</option>
                    @endforeach
                </select>
                Village <select name="" id="">
                    @foreach ($villages as $village)
                        <option value="{{ $village->village_title }}">{{ $village->village_title }}</option>
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
                        <!-- /.card -->

                        <div class="card" >
                            <!-- /.card-header -->
                            <div class="table-responsive" id="famerstable">
                                <table class="table" id="myTable">
                                    <thead>
                                        <tr style="font-size:13px;">
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Code</th>
                                            <th>First Purchase</th>
                                            <th>Last Purchase</th>
                                            <th>Governorate</th>
                                            <th>Region</th>
                                            <th>Village</th>
                                            <th>Quantity</th>
                                            <th>Coffe Bought</th>
                                            <th>Reward</th>
                                            <th>Money Owed</th>
                                            <th>Cupping Score</th>
                                            <th>Cup Profile</th>

                                            <th>View Details</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($farmers as $farmer)
                                            <tr>

                                                <td>{{ $farmer->farmer_id }}</td>
                                                <td>{{ $farmer->farmer_name }}</td>
                                                <td>{{ $farmer->farmer_code }}</td>
                                                <td>{{ $farmer->id }}</td>
                                                <td>{{ $farmer->id }}</td>
                                                <td>{{ $farmer->governerate_title }}</td>
                                                <td>{{ $farmer->region_title }}</td>
                                                <td>{{ $farmer->village_title }}</td>
                                                <td>{{ $farmer->id }}</td>
                                                <td>{{ $farmer->id }}</td>
                                                <td>{{ $farmer->id }}</td>
                                                <td>{{ $farmer->id }}</td>
                                                <td>{{ $farmer->id }}</td>
                                                <td>{{ $farmer->id }}</td>
                                                <td> <a href="{{ route('farmer.profile', $farmer) }}"><i
                                                            class="fas fa-eye"></i></a></td>


                                            </tr>

                                        @endforeach

                                    </tbody>

                                </table>
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
    <!-- /.content-wrapper -->


@endsection
