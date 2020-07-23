@extends('admin_layout.app')
@section('page_css')
@endsection
@section('content')
<div class="wrapper">

    @include('admin_layout.topbar')
    @include('admin_layout.sidebar')

    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        @include('admin_layout.header')

        <!-- Main content -->
        <section class="content">
            <!-- Info boxes -->
            @if(Session::has('message'))
            <div class="alert alert-info alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                {{ Session::get('message') }}
            </div> 
            @endif
            @if(Session::has('updatemessage'))
            <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                {{ Session::get('updatemessage') }}
            </div> 
            @endif
            @if(Session::has('delete'))
            <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                {{ Session::get('delete') }}
            </div> 
            @endif
            <!-- /.row -->

            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">{{$title}}</h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>

                                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <table id="appointments_table"class="display" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th class="tbl-style">Sr #</th>
                                                <th class="tbl-style">Username</th>
                                                <th class="tbl-style">Doctor</th>
                                                <th class="tbl-style">Appointment Time</th>
                                                <th class="tbl-style">Type</th>
                                                <th class="tbl-style">Status</th>
<!--                                                <th class="tbl-style">Action</th>-->
                                            </tr>
                                        </thead>
                                    </table>

                                </div>

                            </div>
                            <!-- /.row -->
                        </div>
                    </div>
                    <!-- /.box -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    @include('admin_layout.footer')
    <div class="control-sidebar-bg"></div>

</div>

@endsection

@section('page_js')
<script>
    let base_path
            = '<?= asset('/') ?>';
    $(document).ready(function () {


        var t = $('#appointments_table').DataTable({
            "processing": true,
            "serverSide": true,
            "deferRender": true,
            "language": {
                "searchPlaceholder": "Search by doctor and username"
            },
            "ajax": {
                url: '<?= asset('admin/getappointments') ?>',
            },
            "columns": [
                {"data": null},
                {"mRender": function (data, type, row) {
                        return '<a href=' + base_path + 'admin/userdetail/' + row.user_id + '>' + row.user.name + '</a>';
                    }
                },
                {"mRender": function (data, type, row) {
                        return '<a href=' + base_path + 'admin/doctordetail/' + row.doc_id + ' >' + row.doctor.first_name + '</a>';
                    }
                },
                {"mRender": function (data, type, row) {
                        var appointmentData = row.booking_time;
                        if (row.type == 'hospital') {
                            var current_date = row.booking_time;
                            var ts = Date.parse(current_date);
                            var myDate = new Date(ts);
                            appointmentData = myDate.getFullYear() + '-' + ('0' + (myDate.getMonth() + 1)).slice(-2) + '-' + ('0' + myDate.getDate()).slice(-2);
                        }
                        return '<td>' + appointmentData + '</td>';
                    }
                },

                {"data": 'type'},
                {"data": 'status'},
//                {"mRender": function (data, type, row) {
//                        return '<a class="editor_remove" data-id="' + row.id + '" href="javascript:void(0)">Delete</a>';
//                    }
//                }
            ],
            "columnDefs": [{
                    "searchable": false,
                    "orderable": false,
                    "targets": [0, 1, 2],
                }
            ],

            "order": [], //Initial no order.
            "aaSorting": [],
        });

        t.on('order.dt search.dt', function () {

            t.on('draw.dt', function () {
                var PageInfo = $('#appointments_table').DataTable().page.info();
                t.column(0, {page: 'current'}).nodes().each(function (cell, i) {
                    cell.innerHTML = i + 1 + PageInfo.start;
                });
            });
        }).draw();
    });

    $('#appointments_table').on('click', 'a.editor_remove', function (e) {
        e.preventDefault();
        if (confirm('Are you sure you want to delete this appointment?')) {
            $(this).closest('tr').remove();
            var id = $(this).data('id');
            $.ajax({
                type: "GET",
                url: base_path + 'admin/deleteappointments/' + id

            });
            location.reload();
        }
    });

//    $(document).ready(function () {             
//  $('.dataTables_filter input[type="search"]').css(
//     {'width':'250px','display':'inline-block'}
//  );
//});
</script>
@endsection