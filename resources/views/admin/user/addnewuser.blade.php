@extends('layouts.default')
@section('title', 'All User')

@section('content')
<style type="text/css">
  a, a:hover{
  color:#333
}
</style>
     <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Add User</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Add User</li>
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
          <div class="col-md-12 ">
            <!-- general form elements -->
            <div class="card card-primary">

              <div class="card-header">
                <h3 class="card-title">Add</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form role="form" method="POST" action="{{URL::to('/admin/storeuser')}}">
                {{ csrf_field() }}
                <div class="card-body col-md-6 ">
                  <div class="form-group">
                    <label for="exampleInputEmail1">First Name</label>
                    <input type="text" id="first_name" class="form-control " id="exampleInputEmail1" name="first_name" placeholder="First Name" @error('first_name') is-invalid @enderror>
                    @error('first_name')
                       <span  class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                  <div class="form-group">
                    <label for="exampleInputPassword1">Last Name</label>
                    <input type="text" id="last_name" class="form-control" id="exampleInputPassword1" name="last_name" placeholder="Last Name"  @error('last_name') is-invalid @enderror>
                    @error('last_name')
                       <span  class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                  <div class="form-group">
                    <label for="exampleInputPassword1">Email</label>
                    <input type="email" id="email" class="form-control" name="email" placeholder="Email"  @error('email') is-invalid @enderror>
                    @error('email')
                       <span  class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>

                  <div class="form-group">
                    <label class="col-form-label" for="inputSuccess">Password</label></br>
                        <div style="display: flex;">
                          <input type="password"  placeholder="Enter new password" class="form-control " name="password" id="myInput" >
                          <a onclick="myFunction()" class="fa fa-eye ml-2 mt-2"></a>
                        </div>
                          
                      
                  </div>

                  <div class="form-group">
                    <label for="cars">Choose a Role:</label>

                      <select class="form-control" name="role_id">
                        @foreach($role as $row)
                        <option value="{{$row->id}}">{{$row->name}}</option>
                        @endforeach
                      </select>
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
 <script>
function myFunction() {
  var x = document.getElementById("myInput");
  if (x.type === "password") {
    x.type = "text";
  } else {
    x.type = "password";
  }
}
</script>
@endsection
