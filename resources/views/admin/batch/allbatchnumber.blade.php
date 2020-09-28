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
            <h1>Batch Number 
              <a href="" class="btn btn-add rounded-circle"> 
                <i class="fas fa-user-plus add-client-icon"></i>
              </a>
            </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">All Batch Numbers</li>
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
                <table id="get_batch_number" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>S#</th>
                    <th>Batch Number</th>
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
                "searchPlaceholder": "Search"
            },
            "ajax": {
                url: '<?= asset('admin/getbatch') ?>',
            },
            "columns": [
                {"data": null},
                {"data": 'batch_number'},
                {"mRender": function (data, type, row) {
                        return '<a class="btn btn-info btn-sm" href=' + base_path + 'admin/batchdetail/' +  row.batch_number + '><i class="fa fa-info-circle"></i></a>';
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