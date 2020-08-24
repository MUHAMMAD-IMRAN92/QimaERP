@extends('layouts.default')
@section('title', 'Transection Detail')
@section('content')
    

    <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Transactions Detail 
            </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Detail</li>
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
             @if(!$TransactionChild->isEmpty())
            <div class="card">
              <!-- /.card-header -->
              <div class="card-body">
                <table id="transactionchild" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th >Basket</th>
                  </tr>
                  </thead>
                 <tbody>
                    <p><b>Batch Number</b>: {{$transaction->batch_number}}</p>
                    @foreach($TransactionChild as $row)
                      <tr>
                        <td >{{$row->batch_number}}
                          <table>
                        @foreach($row->transactionDetail as $child)
                            <tr>
                              <td>
                                Id: {{$child->transaction_detail_id}}
                              </td>
                              <td>
                                Container: {{$child->container_number}}
                              </td>
                              <td>
                                Weight: {{$child->weight}}
                              </td>
                              
                            </tr>
                          @endforeach</td> 
                        </table>
                          
                      </tr>
                    @endforeach
                  
                 </tbody>
                  <tfoot>
                 <tr>
                    <th>Basket</th>
                  </tr>
                  </tfoot>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            @else
             <p><b>Batch Number</b>: {{$transaction->batch_number}}</p>
             <div class="card">
              <!-- /.card-header -->
              <div class="card-body">
                <table id="transactiionall" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>S#</th>
                    <th>Basket</th>
                    <th>Weight</th>
                  </tr>
                  </thead>
                 <tbody>
                   
                    @foreach($TransactionDetail as $row)
                      <tr>
                        <td>{{$row->transaction_detail_id}}</td>
                        <td>{{$row->container_number}}</td>
                        <td>{{$row->weight}}</td>
                      </tr>
                    @endforeach
                  
                 </tbody>
                  <tfoot>
                 <tr>
                    <th>S#</th>
                    <th>Basket</th>
                    <th>Weight</th>
                  </tr>
                  </tfoot>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            @endif
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
      $('#transactionchild').DataTable();
} );
     $(document).ready( function () {
    $('#transactiionall').DataTable();
} );
  </script>
@endsection