@extends('layouts.default')
@section('title', 'All Regions')
@section('content')
    
 
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
            <h1>Region Weight 
              {{-- <a href="" class="btn btn-add rounded-circle"> 
                <i class="fas fa-user-plus add-client-icon"></i>
              </a> --}}
            </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{URL::to('')}}/admin/governorweight">Governor Weight</a></li>
              <li class="breadcrumb-item active">{{$governore->governerate_code}}</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            
            <!-- /.card -->

            <div class="card">
              <!-- /.card-header -->
              <div class="card-body">
                <table  class="table table-bordered ">
                  <thead>
                  <tr>
                    <th>Governor Code</th>
                    <th>Governor Title</th>
                    <th>Governor Weight</th>
                  </tr>
                  </thead>
                  <tbody>
                     @php
                    $gov=$governore->governerate_code;
                    $totalweight = App\TransactionDetail::whereHas('transection', function($q) use($gov){
                            $q->where('is_parent', 0)
                            ->Where('batch_number','LIKE', "$gov%");
                        })->sum('container_weight');
                    @endphp
                   <tr>
                     <td>{{$governore->governerate_code}}</td>
                     <td>{{$governore->governerate_title}}</td>
                     <td>{{$totalweight}}</td>
                   </tr>
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
                    <th>Total Weight</th>
                    
                  </tr>
                  </thead>
                  <tbody>
                    @foreach($region as $row)
                    @php
                    $region=$row->region_code;
                    $totalweight = App\TransactionDetail::whereHas('transection', function($q) use($region){
                            $q->where('is_parent', 0)
                            ->Where('batch_number','LIKE', "$region%");
                        })->sum('container_weight');
                    @endphp
                        <tr>
                          <td>{{$row->region_id}}</td>
                          <td><a href="{{URL::to('')}}/admin/regionweightcode/{{$row->region_id}}">{{$row->region_code}}</a></td>
                          <td>{{$row->region_title}}</td>

                          <td>
                             {{$totalweight}} kg
                          </td> 
                         
                        </tr>
                    @endforeach
                  </tbody>
                  <tfoot>
                  <tr>
                   <th>S#</th>
                    <th>Region Code</th>
                    <th>Region Title</th>
                    <th>Total Weight</th>
                    
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
  <script type="text/javascript">
    $(document).ready( function () {
    $('#region').DataTable();
} );
  </script>
@endsection