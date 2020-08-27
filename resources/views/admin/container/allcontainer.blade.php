@extends('layouts.default')
@section('title', 'All Container')
@section('content')
    

    <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Conatiners Information 
              <a href="{{url('admin/addcontainer')}}" class="btn btn-add rounded-circle"> 
                <i class="fas fa-user-plus add-client-icon"></i>
              </a>
            </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">All Conatiners</li>
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
                <table id="myTable" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>S#</th>
                    <th>Code</th>
                    <th>Status</th>
                    <th>Action</th>
                    
                  </tr>
                  </thead>
                 <tbody>
                   
                   
                      <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><a href="transactiondetail/" class="btn btn-info btn-sm"><i class="fa fa-info-circle"></i></a> </td>
                      </tr>
                   
                  
                 </tbody>
                  <tfoot>
                 <tr>
                    <th>S#</th>
                    <th>Code</th>
                    <th>Status</th>
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
  <script type="text/javascript">
    $(document).ready( function () {
    $('#myTable').DataTable();
} );
  </script>
@endsection