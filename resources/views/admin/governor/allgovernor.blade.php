@extends('layouts.default')
@section('title', 'All Governor')
@section('content')
    <style type="text/css">
        .dataTables_wrapper .dataTables_filter input {
            margin-left: 0.5em;
            width: 240px;
        }

    </style>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <div class="mx-lg-5">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-8 pl-0">
                            <h1 class="m-0 text-dark text-uppercase font-weight-lighter text-heading">Governors Information
                                <a href="{{ url('/admin/addnewgovernor') }}" class="btn btn-add rounded-circle" style="color:red;">
                                    Add Governerate
                                </a>
                            </h1>
                        </div>
                        <div class="col-sm-4">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">All Governors</li>
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
                            <div class="card shadow-none">
                                <!-- /.card-header -->
                                <div class="table-responsive text-uppercase letter-spacing-2 governors_table ">
                                    <table class="table border-bottom-0 table-bordered-custom text-center custom-table "
                                        id="governors" style="font-size:13px;">
                                        <thead>
                                            <tr>
                                                <th class="font-weight-lighter">S#</th>
                                                <th class="font-weight-lighter">Code</th>
                                                <th class="font-weight-lighter">Title</th>
                                                <th class="font-weight-lighter">Action</th>
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
    </div>
    <!-- /.content-wrapper -->
    <script>
        let base_path = '<?= asset('/') ?>';
        $(document).ready(function() {
            var t = $('#governors').DataTable({
                "processing": true,
                "serverSide": true,
                "deferRender": true,
                "language": {
                    "searchPlaceholder": "Search by Code And Title"
                },
                "ajax": {
                    url: '<?= asset('admin/getgovernors') ?>'
                },
                "columns": [{
                        "data": null
                    },

                    {
                        "data": 'governerate_code'
                    },
                    {
                        "data": 'governerate_title'
                    },
                    {
                        "mRender": function(data, type, row) {
                            console.log(row);
                            return '<a href=' + base_path + 'admin/editgovernor/' + row
                                .governerate_id + '>Edit</a>| <a href=' + base_path +
                                'admin/deletegovernor/' + row.governerate_id +
                                ' class="editor_remove" data-id="' + row.governerate_id +
                                '">Delete</a>';
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
                var PageInfo = $('#governors').DataTable().page.info();
                t.column(0, {
                    page: 'current'
                }).nodes().each(function(cell, i) {
                    cell.innerHTML = i + 1 + PageInfo.start;
                });

            }).draw();
        });
    </script>
@endsection
