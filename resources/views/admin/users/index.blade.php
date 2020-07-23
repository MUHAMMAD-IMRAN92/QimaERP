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
                                    <table id="users_table"class="display" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th  class="tbl-style">Sr #</th>
                                                <th  class="tbl-style">Name</th>
                                                <th  class="tbl-style">Email</th>
                                                <th  class="tbl-style">Phone Number</th>
                                                <th  class="tbl-style">City</th>
                                                <th  class="tbl-style">Actions</th>
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


        var t = $('#users_table').DataTable({
            "processing": true,
            "serverSide": true,
            "deferRender": true,
            "language": {
                "searchPlaceholder": "Search by user name"
            },
            "ajax": {
                url: '<?= asset('admin/getusers') ?>',
            },
            "columns": [
                {"data": null},
                {"data": 'name'},
                {"data": 'email'},
                {"data": 'phone_number'},
                {"mRender": function (data, type, row) {
                        var city = '---';
                        if (row.city) {
                            var city = row.city.title;
                        }

                        return city;
                    }
                },
                {"mRender": function (data, type, row) {
                        // return '<a href=' + base_path + 'admin/userdetail/' + row.id + '>View</a>|<a class="editor_remove" data-id="' + row.id + '" href="javascript:void(0)">Delete</a>';
                        return '<a href=' + base_path + 'admin/userdetail/' + row.id + '>View</a>|<a href=' + base_path + 'admin/deletehos/' + row.id + ' class="editor_remove" data-id="' + row.id + '">Delete</a>';
                    }
                }
            ],
            "columnDefs": [{
                    "searchable": false,
                    "orderable": false,
                    "targets": [0, 4, 5],
                }
            ],
            "order": [], //Initial no order.
            "aaSorting": [],
        });

        t.on('order.dt search.dt', function () {

            t.on('draw.dt', function () {
                var PageInfo = $('#users_table').DataTable().page.info();
                t.column(0, {page: 'current'}).nodes().each(function (cell, i) {
                    cell.innerHTML = i + 1 + PageInfo.start;
                });
            });
        }).draw();
    });

    $('#users_table').on('click', 'a.editor_remove', function (e) {
        e.preventDefault();
        if (confirm('Are you sure you want to delete this user?')) {
            var id = $(this).data('id');
            window.location.href = base_path + 'admin/deleteuser/' + id;
//            $(this).closest('tr').remove();
//            var id = $(this).data('id');
//            $.ajax({
//                type: "GET",
//                url: base_path + 'admin/deleteuser/' + id
//            });
//            location.reload();
        }
    });
</script>
@endsection