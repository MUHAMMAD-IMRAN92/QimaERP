@extends('layouts.default')
@section('title', 'All Villages')
@section('content')
<style type="text/css">
   .dataTables_wrapper .dataTables_filter input {
    margin-left: 0.5em;
    width: 240px;
}
 </style> 
    <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    @if(session()->has('update'))
    <div class="alert alert-success">
        {{ session()->get('update') }}
         <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
    </div>
    @endif
    @if(Session::has('message'))
                  <div class="alert alert-success" role="alert">
                  <b>{{Session::get('message')}}</b>
                  <button type="button" class="close" data-dismiss="alert">&times;</button>
                  </div>
                  @endif
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">

          <div class="col-sm-6">
            <h1>Add New Village  <a href="{{ URL::to('') }}/admin/addnewvillage" class="btn btn-add rounded-circle">
                <button class="btn btn-dark">Add Farmer</button>
            </a>
              
            </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
             
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
                <table id="village" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>S#</th>
                    <th>Village Code</th>
                    <th>Village Title (En)</th>
                    <th>Village Title (Ar)</th>
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
        var t = $('#village').DataTable({
            "processing": true,
            "serverSide": true,
            "deferRender": true,
            "language": {
                "searchPlaceholder": "Search by Code And Title"
            },
            "ajax": {
                url: '<?= asset('admin/getvillage') ?>',
            },
            "columns": [
                {"data": null},
                
                {"data": 'village_code'},
                {"data": 'village_title'},
                {"data": 'village_title_ar'},
                {"mRender": function (data, type, row) {
                        return '<a href=' + base_path + 'admin/editvillage/' + row.village_id + '>Edit</a>';
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
            var PageInfo = $('#village').DataTable().page.info();
            t.column(0, {page: 'current'}).nodes().each(function (cell, i) {
                cell.innerHTML = i + 1 + PageInfo.start;
            });

        }).draw();
    });

   
</script>
@endsection