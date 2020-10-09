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
                  @if(Session::has('updatefarmer'))
                  <div class="alert alert-success" role="alert">
                  <b>{{Session::get('updatefarmer')}}</b>
                  <button type="button" class="close" data-dismiss="alert">&times;</button>
                  </div>
                  @endif
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">

          <div class="col-sm-6">
            <h1>All Farmer
            </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">All Farmer</li>
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
                <table id="allfarmer" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>S#</th> 
                    <th>Farmer Code</th>
                    <th>Farmer Name</th>
                    <th>Village Code</th>
                    <th>Farmer Cnic</th>
                    <th>Status</th>
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
        var t = $('#allfarmer').DataTable({
            "processing": true,
            "serverSide": true,
            "deferRender": true,
            "language": {
                "searchPlaceholder": "Search"
            },
            "ajax": {
                url: '<?= asset('admin/getfarmer') ?>',
            },
            "columns": [
                {"data": null},
                
                {"data": 'farmer_code'},
                {"data": 'farmer_name'},
                {"data": 'village_code'},
                {"data": 'farmer_nicn'},
                {"mRender": function (data, type, row) {
                        if (row.is_status == 1) {
                            var status = 'Approved';
                        }else {
                           var status = 'Pending';
                        }
                        return '<td>'+status+'</td>';
                    }
                },
                {"mRender": function (data, type, row) {
                  if(row.is_status == 0){
                        return '<a href=' + base_path + 'admin/editfarmer/' + row.farmer_id + '>Edit</a>| <a href=' + base_path + 'admin/editfarmer/' + row.farmer_id + ' class="editor_remove" data-id="' + row.farmer_id + '">Delete</a>|  <a href=' + base_path + 'admin/statusupdate/' + row.farmer_id + ' data-id="' + row.farmer_id + '">Approve</a>'; }else{
                           return '<a href=' + base_path + 'admin/editfarmer/' + row.farmer_id + '>Edit</a>| <a href=' + base_path + 'admin/editfarmer/' + row.farmer_id + ' class="editor_remove" data-id="' + row.farmer_id + '">Delete</a>';
                        }
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
            var PageInfo = $('#allfarmer').DataTable().page.info();
            t.column(0, {page: 'current'}).nodes().each(function (cell, i) {
                cell.innerHTML = i + 1 + PageInfo.start;
            });

        }).draw();
    });


   
</script>
@endsection