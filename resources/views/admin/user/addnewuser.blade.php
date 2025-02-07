@extends('layouts.default')
@section('title', 'All User')

@section('content')
<style type="text/css">
    a,
    a:hover {
        color: #333
    }
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <div class="mx-lg-5">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark text-uppercase font-weight-lighter text-heading">Add User</h1>
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
                                <h3 class="card-title text-uppercase letter-spacing-2">Add</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form role="form" method="POST" action="{{URL::to('/admin/storeuser')}}" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="card-body col-md-6 text-uppercase letter-spacing-1">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">First Name</label>
                                        <input type="text" id="first_name" class="form-control " id="exampleInputEmail1" name="first_name" placeholder="First Name" @error('first_name') is-invalid @enderror>
                                        @error('first_name')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Last Name</label>
                                        <input type="text" id="last_name" class="form-control" id="exampleInputPassword1" name="last_name" placeholder="Last Name" @error('last_name') is-invalid @enderror>
                                        @error('last_name')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Email</label>
                                        <input type="email" id="email" class="form-control" name="email" placeholder="Email" @error('email') is-invalid @enderror>
                                        @error('email')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label class="col-form-label" for="inputSuccess">Password</label></br>
                                        <div style="display: flex;">
                                            <input type="password" placeholder="Enter new password" class="form-control " name="password" id="myInput">
                                            <a onclick="myFunction()" class="fa fa-eye ml-2 mt-2"></a>
                                        </div>


                                    </div>

                                    <div class="form-group">
                                        <label for="cars">Choose a Role:</label>

                                        <select class="form-control" name="role_id" id="select1">
                                            @foreach($role as $row)
                                            <option value="{{$row->id}}">{{$row->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group" id="select2" style="display:none;">
                                        <label for="cars">Center:</label>
                                        <select class="form-control" name="center_id">
                                            @foreach($center as $row)
                                            <option value="{{$row->center_id}}">{{$row->center_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="row">

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="customFile">User Image</label>
                                                <div class="">
                                                    <input type="file" class="" name="profile_picture" id="customFile"
                                                        @error('profile_picture') is-invalid @enderror>
                                                    @error('profile_picture')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="customFile">User Id Card Image</label>
                                                <div class="">
                                                    <input type="file" class="" name="idcard_picture" id="customFile"
                                                        @error('idcard_picture') is-invalid @enderror>
                                                    @error('idcard_picture')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <!-- /.card-body -->

                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary text-uppercase letter-spacing-1">Submit</button>
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
</div>
</section>
<!-- /.content -->
</div>
<!-- /.content-wrapper -->
<script>
    $("#select1").change(function() {
        if ($(this).val() == 3) {
            $("#select2").show();
        } else if ($(this).val() == 5) {
            $("#select2").show();
        } else if ($(this).val() == 6) {
            $("#select2").show();
        } else if ($(this).val() == 7) {
            $("#select2").show();
        } else {
            $("#select2").hide();
        }
    });

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
