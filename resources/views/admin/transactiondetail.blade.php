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
                <table class="table table-bordered table-striped">
                 {{--  <thead>
                  <tr>
                    <th >Basket</th>
                  </tr>
                  </thea> --}}
                 <tbody>
                    <p><b>Batch Number</b>: {{$transaction->batch_number}}</p>
                            
                    @foreach($TransactionChild as $row)
                    <?php  $removeLocalId = explode("-", $row['batch_number']);
                             //::remove last index of array
                                  array_pop($removeLocalId);
                            $farmerCode = implode("-", $removeLocalId);
                           ?>
                      <tr>
                        
                        <td >
                          <table  class="table table-bordered table-striped">
                              
                                <tr>
                                  <th>Basket</th>
                                  <th>Container</th>
                                  <th>Weight</th>
                                  <th>Farmer </th>
                                </tr>
                              
                        @foreach($row->transactionDetail as $child)

                            <tr>
                              <td>
                                <b>{{$row->batch_number}}</b>
                              </td>
                              <td>
                               {{$child->container_number}}
                              </td>
                              <td>
                               {{$child->weight}} kg
                              </td>
                               <td>
                               {{--  @foreach($farmer as $far)
                                @foreach($far as $wq)
                                      @if( $farmerCode==$wq->farmer_code)
                                        {{$wq->farmer_name}}
                                      @endif
                                @endforeach
                            @endforeach --}}
                            @foreach($Farmer as $far)
                                    @if( $farmerCode==$far->farmer_code)
                                        {{$far->farmer_name}}<br> {{$far->farmer_id}}
                                    @endif
                            @endforeach
                              </td>
                              
                            </tr>
                          @endforeach
                           
                               
                              
                        
                        </table>
                        </td> 
                      </tr>
                    @endforeach
                  
                 </tbody>
                  {{-- <tfoot>
                 <tr>
                    <th>Basket</th>
                  </tr>
                  </tfoot> --}}
                </table>
              </div>
              <div class="card-body">
                <table class="table table-bordered table-striped">

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