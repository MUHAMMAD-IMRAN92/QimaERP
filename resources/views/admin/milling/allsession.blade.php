@extends('layouts.default')
@section('title', 'All Farmers')
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
            <div class="row mb-2">

                <div class="col-sm-6">
                    <h1>Session Numbers
                        <a href="" class="btn btn-add rounded-circle"> 
                        </a>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Session Numbers</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <!--     Main      content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <!-- /.card -->
                    <div class="card">
                        @if(Session::has('message'))
                        <div class="alert alert-success" role="alert">
                            <b>{{Session::get('message')}}</b>
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                        @endif
                        @if(Session::has('error'))
                        <div class="alert alert-danger" role="alert">
                            <b>{{Session::get('error')}}</b>
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                        @endif
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="get_batch_number" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Session Number</th>
                                        <th>Num Of Batches</th>
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
    let base_path
            = '<?= asset('/') ?>';
    $(document).ready(function () {
        var t = $('#get_batch_number').DataTable({
            "processing": true,
            "serverSide": true,
            "deferRender": true,
            "language": {
                "searchPlaceholder": "Session Number"
            },
            "ajax": {
                url: '<?= asset('admin/get_milling_sessions') ?>',
            },
            "columns": [
                {"data": null},
                {"data": 'session_no'},
                {"data": 'batch_count'},
                {"mRender": function (data, type, row) {
                        return '<a class="btn btn-info btn-sm" href=' + base_path + 'admin/milling_coffee/' + row.session_no + '><i class="fa fa-info-circle"></i></a>';
                    }
                }
            ],
            "columnDefs": [{
                    "searchable": false,
                    "orderable": false,
                    "targets": [0, 2],
                }
            ],
            "order": [], //Initial no order.
            "aaSorting": [],
        });

        t.on('draw.dt', function () {
            var PageInfo = $('#get_batch_number').DataTable().page.info();
            t.column(0, {page: 'current'}).nodes().each(function (cell, i) {
                cell.innerHTML = i + 1 + PageInfo.start;
            });

        }).draw();
    });


</script>
@endsection