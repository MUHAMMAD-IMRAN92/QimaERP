@extends('layouts.default')
@section('title', 'All Center')
@section('content')
    

    <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Users Information 
              <a href="{{url('/admin/adduser')}}" class="btn btn-add rounded-circle"> 
                <i class="fas fa-user-plus add-client-icon"></i>
              </a>
            </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">All Users</li>
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
                <table id="users" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>S#</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Action</th>
                    
                  </tr>
                  </thead>
                 
                  <tfoot>
                 <tr>
                    <th>S#</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
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
        var t = $('#users').DataTable({
            "processing": true,
            "serverSide": true,
            "deferRender": true,
            "language": {
                "searchPlaceholder": "Search by Name And Email"
            },
            "ajax": {
                url: '<?= asset('admin/getuser') ?>',
            },
            "columns": [
                {"data": null},
                
                {"data": 'first_name'},
                {"data": 'last_name'},
                {"data": 'email'},
                {"mRender": function (data, type, row) {
                        return '<a href=' + base_path + '' +  row.user_id + '>Edit</a>| <a href=' + base_path + '' + row.user_id + ' class="editor_remove" data-id="' + row.user_id + '">Delete</a>';
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
            var PageInfo = $('#users').DataTable().page.info();
            t.column(0, {page: 'current'}).nodes().each(function (cell, i) {
                cell.innerHTML = i + 1 + PageInfo.start;
            });

        }).draw();
    });

   
</script>
@endsection