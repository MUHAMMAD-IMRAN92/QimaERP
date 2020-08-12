@extends('layouts.default')
@section('title', 'All Governor')
@section('content')
    

    <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Governors Information 
              <a href="{{url('/admin/addnewgovernor')}}" class="btn btn-add rounded-circle"> 
                <i class="fas fa-user-plus add-client-icon"></i>
              </a>
            </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">All Governors</li>
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
                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>S#</th>
                    <th>Code</th>
                    <th>Title</th>
                    <th>Action</th>
                    
                  </tr>
                  </thead>
                  <tbody>
                     @foreach($governors as $row)
                        <tr>
                          <td>{{$row->governerate_id}}</td>
                          <td>{{$row->governerate_code}}</td>
                          <td>{{$row->governerate_title}}</td>
                          <td>
                              <a href="editgovernor/{{$row->governerate_id}}" class="btn btn-info btn-sm"><i class="fas fa-edit"></i></a> 
                           
                              <a href="deletegovernor/{{$row->governerate_id}}" class="btn btn-danger btn-sm trigger-btn"  ><i class="fas fa-trash-alt"></i></a>
                          </td>
                         
                        </tr>
                     @endforeach
                  </tbody>
                  <tfoot>
                 <tr>
                    <th>S#</th>
                    <th>Code</th>
                    <th>Title</th>
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
  
@endsection