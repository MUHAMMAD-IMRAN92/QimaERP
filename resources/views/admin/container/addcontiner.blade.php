@extends('layouts.default')
@section('title', 'Add Container')
@section('content')
     <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      @if(session()->has('message'))
          <div class="alert alert-danger">
              {{ session()->get('message') }}
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
          </div>

      @endif
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Add Container</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Add Container</li>
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
              <form role="form" method="POST" action="{{URL::to('')}}/admin/storecontainer">
                {{ csrf_field() }}
                <div class="card-body">
                  <div class="form-group">
                   <label for="country_name">Type</label>
                   
                    <select class="form-control " id="container_type" name="container_type">
                      @foreach($array as $row)
                      <option value="{{$row['code']}}">{{$row['type']}}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="codetype">Code</label>
                    <input type="text" id="codetype" class="form-control" name="codetype" readonly="readonly">
                  </div> 
                  <div class="form-group">
                    <label for="exampleInputPassword1">Number</label>
                    <input type="text" id="number" class="form-control" id="number" name="number" placeholder="number" @error('number') is-invalid @enderror onClick="checkPrice()">
                    @error('number')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                  </div> 
                  <div class="form-group">
                    <label for="exampleInputPassword1">Capacity</label>
                    <input type="text" id="capacity" class="form-control" id="exampleInputPassword1" name="capacity" placeholder="Capacity" @error('capacity') is-invalid @enderror>
                    @error('capacity')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
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
  <script type="text/javascript">
    var select = document.getElementById('container_type');
      var input = document.getElementById('codetype');
      select.onchange = function() {
          input.value = select.value;
      }
  </script>
@endsection