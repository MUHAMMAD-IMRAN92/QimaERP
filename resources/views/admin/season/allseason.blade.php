@extends('layouts.default')
@section('title', 'All Seasons')
@section('content')
    
 
    <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    @if(session()->has('destroy'))
    <div class="alert alert-success">
        {{ session()->get('destroy') }}
         <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
    </div>
    @endif
    @if(Session::has('message'))
                  <div class="alert alert-success" role="alert">
                  <b>{{Session::get('message')}}
                   <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button></b>
                  <button type="button" class="close" data-dismiss="alert">&times;</button>
                  </div>
                  @endif
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">

          <div class="col-sm-6">
            <h1>Season
              <a href="{{URL::to('')}}/admin/addseason" class="btn btn-add rounded-circle"> 
                <i class="fas fa-user-plus add-client-icon"></i>
              </a>
            </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">All Seasons</li>
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
                <table id="season_table"  class="table table-bordered table-striped ">
                  <thead>
                  <tr>
                    <th>S#</th>
                    <th>Title</th>
                    <th>Start Data</th>
                    <th>End Data</th>
                    <th>Action</th>
                    
                  </tr>
                  </thead>
                  
                   
                  <tfoot>
                  <tr>
                    <th>S#</th>
                    <th>Title</th>
                    <th>Start Data</th>
                    <th>End Data</th>
                    <th>Action</th>
                    
                  </tr>
                  </tfoot>
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
        var t = $('#season_table').DataTable({
            "processing": true,
            "serverSide": true,
            "deferRender": true,
            "language": {
                "searchPlaceholder": "Search by Title and Date"
            },
            "ajax": {
                url: '<?= asset('admin/getseason') ?>',
            },
            "columns": [
                {"data": null},
                
                {"data": 'season_title'},
                {"data": 'start_date'},
                {"data": 'end_date'},
                {"mRender": function (data, type, row) {
                        return '<a href=' + base_path + 'admin/editseason/' + row.season_id + '>Edit</a>| <a href=' + base_path + 'admin/deleteseason/' + row.season_id + ' class="editor_remove" data-id="' + row.season_id + '">Delete</a> | <a href=' + base_path + 'admin/seasonend/' + row.season_id + '>Season End</a>';
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
            var PageInfo = $('#season_table').DataTable().page.info();
            t.column(0, {page: 'current'}).nodes().each(function (cell, i) {
                cell.innerHTML = i + 1 + PageInfo.start;
            });

        }).draw();
    });

   
</script>
@endsection