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
                                <li class="breadcrumb-item active">Add Role</li>
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
                                <form role="form" method="POST" action="{{ URL::to('/admin/roles') }}">
                                    {{ csrf_field() }}
                                    <div class="card-body col-md-6 text-uppercase letter-spacing-1">
                                        <div class="form-group">
                                            <label for="name"> Name</label>
                                            <input type="text" id="name" class="form-control " id="name" name="name"
                                                placeholder="Role Name" @error('name') is-invalid @enderror>
                                            @error('name')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="guard_name">Guard Name</label>
                                            <select name="guard_name" class="form-control" id="guard_name">
                                                <option value="web" selected>web</option>
                                            </select>
                                            @error('guard_name')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
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

@endsection
