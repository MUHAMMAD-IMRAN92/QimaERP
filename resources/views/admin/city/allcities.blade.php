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
            <!-- /.row -->

            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">All Cities</h3>                         
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <p class="text-center">
                                        <a class="btn btn-primary font-style pull-right" href="{{asset('admin/addcity')}}"><strong>Add City </strong></a>
                                    </p>
                                    <table id="cities_table" class="display" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th  class="tbl-style">Sr #</th>
                                                <th  class="tbl-style">Title</th>
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
        var t = $('#cities_table').DataTable({
            "processing": true,
            "serverSide": true,
            "deferRender": true,
            "language": {
                "searchPlaceholder": "Search by city name"
            },
            "ajax": {
                url: '<?= asset('admin/getcities') ?>',
            },
            "columns": [
                {"data": null},
                {"data": 'title'},
                {"mRender": function (data, type, row) {
                        return '<a href=' + base_path + 'admin/editcitie/' + row.id + '>Edit</a>| <a href=' + base_path + 'admin/deletecities/' + row.id + ' class="editor_remove" data-id="' + row.id + '">Delete</a>';
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
            var PageInfo = $('#cities_table').DataTable().page.info();
            t.column(0, {page: 'current'}).nodes().each(function (cell, i) {
                cell.innerHTML = i + 1 + PageInfo.start;
            });

        }).draw();
    });

    $('#cities_table').on('click', 'a.editor_remove', function (e) {
        e.preventDefault();
        if (confirm('You want to delete city?')) {
            var id = $(this).data('id');
            window.location.href = base_path + 'admin/deletecities/' + id;
//            $(this).closest('tr').remove();
//            var id = $(this).data('id');
//            $.ajax({
//                type: "GET",
//                url: base_path + 'admin/deletecities/' + id
//
//            });
//            location.reload();
        }
    });
</script>
@endsection