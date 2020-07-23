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
                            <h3 class="box-title">All  Hospitals</h3>
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
                                    <p class="text-center">
                                        <a class="btn btn-primary pull-right font-style margin-bottom" href="{{asset('admin/addhospital')}}"><strong>Add Hospitals  </strong></a> 
                                    </p>
                                    <table id="hospitals_table"class="display" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th  class="tbl-style">Sr #</th>
                                                <th  class="tbl-style">Image</th>
                                                <th  class="tbl-style">Name</th>
                                                <th  class="tbl-style">Phone</th>
                                                <th  class="tbl-style">City</th>
                                                <th  class="tbl-style">Address</th>
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


        var t = $('#hospitals_table').DataTable({
            "processing": true,
            "serverSide": true,
            "deferRender": true,
            "language": {
                "searchPlaceholder": "Search by hospital name"
            },
            "ajax": {
                url: '<?= asset('admin/gethospitals') ?>',
            },
            "columns": [
                {"data": null},
                {"mRender": function (data, type, row) {
                        if (row.hos_image) {
                            var profile_image = row.hos_image;
                        } else {
                            var profile_image = 'demouser.jpg';
                        }
                        return '<img style="width:50px" src=' + base_path + 'storage/app/hos_image/' + profile_image + '>';
                    }
                },
                {"data": 'name'},
                {"data": 'phone'},
                {"data": 'city.title'},
                {"data": 'location'},
                {"mRender": function (data, type, row) {
                        return '<a href=' + base_path + 'admin/edithospital/' + row.id + '>Edit</a>|<a href=' + base_path + 'admin/deletehos/' + row.id + ' class="editor_remove" data-id="' + row.id + '">Delete</a>';
                    }
                }
            ],
            "columnDefs": [{
                    "searchable": false,
                    "orderable": false,
                    "targets": [0, 1, 4, 6],
                }
            ],
             "order": [], //Initial no order.
            "aaSorting": [],
        });

        t.on('order.dt search.dt', function () {

            t.on('draw.dt', function () {
                var PageInfo = $('#hospitals_table').DataTable().page.info();
                t.column(0, {page: 'current'}).nodes().each(function (cell, i) {
                    cell.innerHTML = i + 1 + PageInfo.start;
                });
            });
        }).draw();
    });

    $('#hospitals_table').on('click', 'a.editor_remove', function (e) {
        e.preventDefault();
        if (confirm('You want to delete hospital?')) {

            //$(this).closest('tr').remove();
            var id = $(this).data('id');
            window.location.href = base_path + 'admin/deletehos/' + id;
            // window.location(base_path + 'admin/deletehos/' + id);
//             window.location.replace(base_path + 'admin/deletehos/' + id);
//            $.ajax({
//                type: "GET",
//                url: base_path + 'admin/deletehos/' + id
//            });
//            location.reload();
        }
    });
</script>

@endsection