@extends('layouts.default')
@section('title', 'All Regions')
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
            <h1>Add New Region 
              <a href="{{url('/admin/addnewregion')}}" class="btn btn-add rounded-circle"> 
                <i class="fas fa-user-plus add-client-icon"></i>
              </a>
            </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">All Region</li>
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
                <table id="region" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>S#</th>
                    <th>Region Code</th>
                    <th>Region Title</th>
                    <th>Action</th>
                    
                  </tr>
                  </thead>
                  <tbody>
                    @foreach($region as $row)
                        <tr>
                          <td>{{$row->region_id}}</td>
                          <td>{{$row->region_code}}</td>
                          <td>{{$row->region_title}}</td>
                          <td>
                              <a href="editregion/{{$row->region_id}}" class="btn btn-info btn-sm"><i class="fas fa-edit"></i></a> 
                           
                              <a href="deleteregion/{{$row->region_id}}" class="btn btn-danger btn-sm trigger-btn"  ><i class="fas fa-trash-alt"></i></a>
                          </td>
                         
                        </tr>
                    @endforeach
                  </tbody>
                  
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
        var t = $('#region').DataTable({
            "processing": true,
            "serverSide": true,
            "deferRender": true,
            "language": {
                "searchPlaceholder": "Search by Code And Title"
            },
            "ajax": {
                url: '<?= asset('admin/getregion') ?>',
            },
            "columns": [
                {"data": null},
                
                {"data": 'region_code'},
                {"data": 'region_title'},
                {"mRender": function (data, type, row) {
                        return '<a href=' + base_path + 'admin/editregion/' + row.region_id + '>Edit</a>| <a href=' + base_path + 'admin/deleteregion' + row.region_id + ' class="editor_remove" data-id="' + row.region_id + '">Delete</a>';
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
            var PageInfo = $('#region').DataTable().page.info();
            t.column(0, {page: 'current'}).nodes().each(function (cell, i) {
                cell.innerHTML = i + 1 + PageInfo.start;
            });

        }).draw();
    });

   
</script>
@endsection