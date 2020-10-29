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
    @if(session()->has('update'))
    <div class="alert alert-success">
        {{ session()->get('update') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif
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
                    <h1>Add Environment</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Add Environment</li>
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
                            <h3 class="card-title">Add Environment</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form role="form" method="POST" action="{{URL::to('/admin/environments')}}">
                            {{ csrf_field() }}
                            <div class="card-body col-md-6 ">
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Environment Name</label>
                                    <input type="text" id="first_name" class="form-control " id="exampleInputEmail1" name="environment_name" placeholder="Environment Name" @error('environment_name') is-invalid @enderror>
                                           @error('environment_name')
                                           <span  class="text-danger">{{ $message }}</span>
                                    @enderror
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
    $("#select1").change(function () {
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
