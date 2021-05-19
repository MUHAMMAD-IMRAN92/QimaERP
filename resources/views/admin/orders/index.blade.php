@extends('layouts.default')
@section('title', 'All Orders')
@section('content') 
<style type="text/css">
   .dataTables_wrapper .dataTables_filter input {
    margin-left: 0.5em;
    width: 240px;
}
 </style>
    <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>All Orders
              <a href="/admin/orders/create" class="btn btn-add rounded-circle"> 
                <i class="fas fa-plus"></i>
              </a>
            </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">All Orders</li>
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
                    <th>Sr#</th>
                    <th>Order Number</th>
                    <th>Status</th>
                    <th>Total Price</th>
                    <th>Created At</th>
                    <th>View</th>
                  </tr>
                  </thead>
                 <tbody>
                   
                    @foreach($orders as $order)
                      <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $order->order_number }}</td>
                        <td>{{ $order->getStatus() }}</td>
                        <td>{{ $order->total }}</td>
                        <td>{{ $order->created_at->toDateTimeString() }}</td>
                        <td><a href="{{ route('orders.show', $order) }}">View</a></td>
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
  <script type="text/javascript">
    $(document).ready( function () {
    $('#myTable').DataTable();
} );
  </script>
@endsection