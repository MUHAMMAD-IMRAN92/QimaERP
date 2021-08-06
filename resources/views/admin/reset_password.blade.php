@extends('layouts.default')
@section('title', 'All Center')
@section('content')
    <style type="text/css">
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #007BFF !important;
            border: 1px solid #007BFF;
        }

    </style>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        @if (session()->has('msg'))
            <div class="alert alert-success" id="alert">
                {{ session()->get('msg') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Reset Password</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            {{-- <li class="breadcrumb-item"><a href="#">Home</a></li> --}}
                            {{-- <li class="breadcrumb-item active">Reset Password</li> --}}
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
                                <h3 class="card-title">Reset Pasword</h3>
                            </div>

                            <form role="form" method="POST" action="{{ url('admin/reset_password/' . $user->user_id) }}">

                                {{ csrf_field() }}
                                <div class="card-body">
                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    <div class="form-group">
                                        <input type="hidden" name="id" value="{{ $user->user_id }}">
                                        <label for="email">Email</label>
                                        <input type="email" name="" class="form-control " id="email"
                                            value="{{ $user->email }}">
                                        <div class="form-group">
                                            <label for="exampleInputPassword1">Password</label>
                                            <input type="password" id="password" class="form-control " id="password"
                                                name="password" placeholder="Enter New Password">
                                            @error('password')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputPassword1">Password</label>
                                            <input type="password" id="cnfpassword" class="form-control " id="cnfpassword"
                                                name="cnfpassword" placeholder="Confirm Password">
                                            @error('cnfpassword')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>



                                    </div>


                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                            </form>
                        </div>



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
