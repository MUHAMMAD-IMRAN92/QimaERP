@extends('layouts.default')
@section('title', 'Edit Farmer')

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <div class="mx-lg-5">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark text-uppercase font-weight-lighter text-heading">Add Farmer</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Edit Farmer</li>
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
                        <div class="col-md-8">
                            <!-- general form elements -->
                            <div class="card card-primary">
                                <!-- /.card-header -->
                                <!-- form start -->
                                <form role="form" method="POST" action="{{ URL::to('/admin/create_farmer') }}"
                                    enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <div class="card-body col-md-6 text-uppercase letter-spacing-1">
                                        <div class="form-group">
                                            <label for="exampleInputPassword1">Farmer Information </label>
                                            <textarea type="text" id="info" class="form-control" value="" name="info"
                                                placeholder="Altitude" @error('') is-invalid @enderror></textarea>
                                            @error('')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="row">

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="customFile">Farmer Image</label>
                                                    <div class="">
                                                        <input type="file" class="" name="profile_picture"
                                                            id="customFile" @error('profile_picture') is-invalid @enderror>
                                                        @error('profile_picture')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                            </div>

                                        </div>
                                    </div>
                                    <!-- /.card-body -->
                                    <div class="card-footer">
                                        <button type="submit"
                                            class="btn btn-primary text-uppercase letter-spacing-1">Submit</button>
                                    </div>
                                </form>
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- /.card -->
                    </div>
                </div>
        </div>
        <!-- /.row -->
    </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection
