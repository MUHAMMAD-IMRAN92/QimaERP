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
                    @if (session()->has('msg'))
                        <div class="alert alert-success">
                            <p>{{ session()->get('msg') }}<button type="button" class="close"
                                    data-dismiss="alert">&times;</button></p>

                        </div>
                    @endif
                    <!-- left column -->
                    <div class="col-md-6 ">

                        <!-- general form elements -->
                        <div class="card card-primary ">

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form role="form" method="POST" action="{{ url('admin/updateUserPassword') }}">
                                {{ csrf_field() }}
                                <input type="hidden" name="id" value="{{ $user->id }}">
                                <div class="card-body ">



                                    <div class="form-group">
                                        <label for="exampleInputPassword1">New Password</label>
                                        <input type="password" class="form-control" name="password"
                                            id="exampleInputPassword1" placeholder=" New Password">
                                        {{-- @error('password')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror --}}
                                    </div>

                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Confirm Password</label>
                                        <input type="password" class="form-control" name="password_confirmation"
                                            id="exampleInputPassword1" placeholder=" New Password">
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
