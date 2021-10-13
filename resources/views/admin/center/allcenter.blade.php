@extends('layouts.default')
@section('title', 'All Center')
@section('content')
    <style type="text/css">
        .dataTables_wrapper .dataTables_filter input {
            margin-left: 0.5em;
            width: 240px;
        }

    </style>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                @if (session()->has('msg'))
                    <div class="alert alert-success" role="alert">
                        <b>{{ Session::get('msg') }}</b>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                @endif
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Centers Information
                            <a href="{{ url('/admin/addcenter') }}" class="btn btn-add rounded-circle">
                                <i class="fas fa-user-plus add-client-icon"></i>
                            </a>
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">All Centers</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">

                        <!-- /.card -->

                        <div class="card">
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="centers" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>S#</th>
                                            <th>Code</th>
                                            <th>Title</th>
                                            <th>Action</th>

                                        </tr>
                                    </thead>


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
        let base_path = '<?= asset('/') ?>';
        $(document).ready(function() {
            var t = $('#centers').DataTable({
                "processing": true,
                "serverSide": true,
                "deferRender": true,
                "language": {
                    "searchPlaceholder": "Search by Code And Title"
                },
                "ajax": {
                    url: '<?= asset('admin/getcenter') ?>',
                },
                "columns": [{
                        "data": null
                    },

                    {
                        "data": 'center_code'
                    },
                    {
                        "data": 'center_name'
                    },
                    {
                        "mRender": function(data, type, row) {
                            return '<a href=' + base_path + 'admin/editcenter/' + row.center_id +
                                '>Edit</a>| <a href=' + base_path + 'admin/centerdetail/' + row
                                .center_id + ' data-id="' + row.center_id +
                                '">Detail</a>| <a href=' + base_path + 'admin/del_center/' + row
                                .center_id + '>Delete</a>';
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
                var PageInfo = $('#centers').DataTable().page.info();
                t.column(0, {
                    page: 'current'
                }).nodes().each(function(cell, i) {
                    cell.innerHTML = i + 1 + PageInfo.start;
                });

            }).draw();
        });
    </script>
@endsection
