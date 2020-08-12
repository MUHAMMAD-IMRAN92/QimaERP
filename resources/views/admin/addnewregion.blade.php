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
            <h1>Add Region</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Add Region</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- left column -->
          <div class="col-md-12">
            <!-- general form elements -->
            <div class="card card-primary">

              <div class="card-header">
                <h3 class="card-title">Add</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form role="form" method="POST" action="{{URL::to('')}}/admin/addregion">
                
                 @if ($errors->any())
                  <div class="alert alert-danger">
                      <ul>
                          @foreach ($errors->all() as $error)
                              <li>{{ $error }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </li>

                          @endforeach
                      </ul>
                      
                  </div>
              @endif
                {{ csrf_field() }}
                <div class="card-body">
                  <div class="form-group">
                   <label for="country_name">All Governor</label>
                    <select class="form-control input-add-inception" name="governerate_code">
                       @foreach($governor as $row)
                      <option value="{{$row->governerate_code}}">{{$row->governerate_code}}</option>
                      @endforeach
                    </select>
                  </div>
                    
                 
                  <div class="form-group">
                    <label for="exampleInputEmail1">Region Code</label>
                    <input type="text" class="form-control" id="exampleInputEmail1" name="region_code" placeholder="Enter Code">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputPassword1">Region Title</label>
                    <input type="text" class="form-control" id="exampleInputPassword1" name="region_title" placeholder="Title">
                  </div>
                 
                 </div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>
              </form>
            </div>
            <!-- /.card -->

           
            </div>
            <!-- /.card -->

          </div>
         
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  
@endsection