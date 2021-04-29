@extends('layouts.default')
@section('title', 'All Regions')
@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <style type="text/css">
        .dataTables_wrapper .dataTables_filter input {
            margin-left: 0.5em;
            width: 240px;
        }

        .small-box>.inner {
            background-color: white;
            color: black;
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
                    url: "{{ url('admin/filter_farmers') }}",
                    type: "GET",
                    data: {
                        'from': from,
                        'to': to
                    },
                    success: function(data) {

                        $('#famerstable').html(data);
                        console.log(data);
                    }
                });
            });
            $('#governorate_dropdown').on('change', function(e) {
                // let from = $('#governorate_dropdown').val();
                let from = e.target.value;
                $.ajax({
                    url: "{{ url('admin/filter_farmers_by_region') }}",
                    type: "GET",
                    data: {
                        'from': from,

                    },
                    success: function(data) {
                        $('#regions_dropdown').empty();

                        let html =
                            ' <option value="0" selected disabled>Select Region</option>';
                        for (let [key, element] of Object.entries(data)) {
                            html += '<option value="' + element.region_id + '">' + element
                                .region_title + '</option>';
                        }

                        $('#regions_dropdown').append(html);
                        console.log(data);
                    }
                });
            });
            $('#regions_dropdown').on('change', function(e) {
                // let from = $('#regions_dropdown').val();
                let from = e.target.value;
                $.ajax({
                    url: "{{ url('admin/filter_villages') }}",
                    type: "GET",
                    data: {
                        'from': from,

                    },
                    success: function(data) {
                        $('#village_dropdown').empty();
                        let html =
                            ' <option value="0" selected disabled>Select Village</option>';
                        for (let [key, element] of Object.entries(data.villages)) {
                            html += '<option value="' + element.village_id + '">' + element
                                .village_title + '</option>';
                        }
                        console.log(data.region);

                        $('#village_dropdown').append(html);


                    }
                });
            });
            $('#village_dropdown').on('change', function(e) {
                // let from = $('#regions_dropdown').val();
                let from = e.target.value;
                $.ajax({
                    url: "{{ url('admin/farmer_by_villages') }}",
                    type: "GET",
                    data: {
                        'from': from,

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
        @if (Session::has('message'))
            <div class="alert alert-success" role="alert">
                <b>{{ Session::get('message') }}</b>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">

                    <div class="col-sm-6">
                        <h1>Region

                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item btn btn-success"><a href="#">Add Village</a></li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
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
            <span class="ml-2 hover" id="today"> TODAY</span> &nbsp |
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
            <span class="ml-2"> <a href="">ALL
                    TIME</a></span>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <b class="ml-2"><a href=""> All Regions</a></b> |
                Governrate <select name="" id="governorate_dropdown">
                    <option value="0" selected disabled>Select Governrate</option>
                    @foreach ($governorates as $governorate)
                        <option value="{{ $governorate->governerate_id }}">{{ $governorate->governerate_title }}
                        </option>
                    @endforeach

                </select>
                Sub Region <select name="" id="regions_dropdown">
                    <option value="0" selected disabled>Select Region</option>
                    @foreach ($regions as $region)
                        <option value="{{ $region->region_id }}">{{ $region->region_title }}</option>
                    @endforeach
                </select>
                Village <select name="" id="village_dropdown">
                    <option value="0" selected disabled>Select Village</option>
                    @foreach ($villages as $village)
                        <option value="{{ $village->village_id }}">{{ $village->village_title }}</option>
                    @endforeach
                </select>
            </div>

        </div>
        <hr>
        <div class="row ml-2">
            <div class="col-sm-1 color bg-danger">
                <h3>{{ count($governorates) }}</h3>
                <p>Governorate</p>
            </div>
            <div class="col-sm-1 color bg-primary">
                <h3>{{ count($regions) }}</h3>

                <p>Regions</p>
            </div>
            <div class="col-sm-1 color bg-warning">
                <h3>{{ count($villages) }}</h3>

                <p>Villages </p>
            </div>
            <div class="col-sm-1 color bg-info"></div>
            <div class="col-sm-1 color bg-dark"></div>
            <div class="col-sm-1 color bg-danger"></div>
            <div class="col-sm-1 color bg-warning"></div>
            <div class="col-sm-1 color bg-info"></div>
            <div class="col-sm-1 color bg-dark"></div>
        </div>
        <hr>
        <div class="row ml-2">
          <h5>QUANTITY CHERRY BOUGHT</h5>
      </div>
      <div class="row">
          <div class="col-md-11 ml-4">
              <canvas id="myChart" style="width:100%;max-height:300px"></canvas>

              <script>
                  var xValues = [50, 60, 70, 80, 90, 100, 110, 120, 130, 140, 150];
                  var yValues = [7, 8, 8, 9, 9, 9, 10, 11, 14, 14, 15];

                  new Chart("myChart", {
                      type: "line",
                      data: {
                          labels: xValues,
                          datasets: [{
                              fill: false,
                              lineTension: 0,
                              backgroundColor: "rgba(0,0,255,1.0)",
                              borderColor: "rgba(0,0,255,0.1)",
                              data: yValues
                          }]
                      },
                      options: {
                          legend: {
                              display: false
                          },
                          scales: {
                              yAxes: [{
                                  ticks: {
                                      min: 6,
                                      max: 16
                                  }
                              }],
                          }
                      }
                  });

              </script>
          </div>
      </div>
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">

                        <!-- /.card -->

                        <div class="card">
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="myTable" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                           
                                            <th>Region Code</th>
                                            <th>Region Title</th>
                                            <th>Action</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($regions as $row)
                                            <tr>
                                                    
                                                <td>{{ $row->region_code }}</td>
                                                <td>{{ $row->region_title }}</td>
                                                <td>
                                                    <a href="editregion/{{ $row->region_id }}"
                                                        class="btn btn-info btn-sm"><i class="fas fa-edit"></i></a>

                                                    <a href="deleteregion/{{ $row->region_id }}"
                                                        class="btn btn-danger btn-sm trigger-btn"><i
                                                            class="fas fa-trash-alt"></i></a>
                                                </td>

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
    <script>
        let base_path = '<?= asset(' / ') ?>';
        $(document).ready(function() {
            var t = $('#region').DataTable({
                "processing": true,
                "serverSide": true,
                "deferRender": true,
                "language": {
                    "searchPlaceholder": "Search by Code And Title"
                },
                "ajax": {
                    url: '<?= asset('
                    admin / getregion ') ?>',
                },
                "columns": [{
                        "data": null
                    },

                    {
                        "data": 'region_code'
                    },
                    {
                        "data": 'region_title'
                    },
                    {
                        "mRender": function(data, type, row) {
                            return '<a href=' + base_path + 'admin/deleteregion/' + row.region_id +
                                ' class="editor_remove" data-id="' + row.region_id + '">Delete</a>';
                        }
                    }
                ],
                "columnDefs": [{
                    "searchable": false,
                    "orderable": false,
                    "targets": [0, 2],
                }],
                "order": [], //Initial no order.
                "aaSorting": [],
            });

            t.on('draw.dt', function() {
                var PageInfo = $('#region').DataTable().page.info();
                t.column(0, {
                    page: 'current'
                }).nodes().each(function(cell, i) {
                    cell.innerHTML = i + 1 + PageInfo.start;
                });

            }).draw();
        });

    </script>
@endsection
