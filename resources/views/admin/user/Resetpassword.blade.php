@extends('layouts.default')
@section('title', 'All User')

@section('content')

     <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Reset Password</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Reset Password</li>
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
          <div class="col-md-6 ">
            @if (\Session::has('message'))
              <div class="alert alert-success">
                 <p>{{ \Session::get('message') }}<button type="button" class="close" data-dismiss="alert">&times;</button></p>
                 
              </div>
            @endif
            <!-- general form elements -->
            <div class="card card-primary ">

              
              <!-- /.card-header -->
              <!-- form start -->
              <form role="form" method="POST" action="{{URL::to('/admin/updatepassword')}}">
                {{ csrf_field() }}
                <input type="hidden" name="user_id" value="{{$reset->user_id}}">
                <div class="card-body ">
                  
                  
                <div class="form-group">
                    <label for="exampleInputPassword1">Old Password</label>
                    <input type="password" class="form-control" name="oldpassword" id="exampleInputPassword1" placeholder="Old Password">
                    @if (\Session::has('old'))
                      <span  class="text-danger">{{ \Session::get('old') }}</span>       
                    @endif
                </div>
                <div class="form-group">
                    <label for="exampleInputPassword1">New Password</label>
                    <input type="password" class="form-control" name="newpassword" id="exampleInputPassword1" placeholder=" New Password">
                     @if (\Session::has('new'))
                      <span  class="text-danger">{{ \Session::get('new') }}</span>       
                    @endif
                </div>        
                </div>
                <!-- /.card-body -->

                <div class="card-footer ">
                  <button type="submit" class="btn btn-primary ">Submit</button>
                </div>
              </form>
            </div>
            <!-- /.card -->

           
            </div>
            <!-- /.card -->

          </div>
         
        </div>
        <!-- /.row -->
         </section>
   
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
@endsection
