@extends('layouts.default')
@section('title', 'All Governor')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="mx-lg-5">

            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-8">
                            <h1 class="m-0 text-dark text-uppercase font-weight-lighter text-heading">Add Governor</h1>
                        </div>
                        <div class="col-sm-4">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Add Governor</li>
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
                                    <h3 class="card-title text-uppercase letter-spacing-2">Add</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <form role="form" method="POST" action="{{ URL::to('') }}/admin/addgovernor">
                                    {{-- @if ($errors->any())
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
                        @endif --}}
                                    {{ csrf_field() }}
                                    <div class="card-body col-md-6 text-uppercase letter-spacing-1">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Code</label>
                                            <input type="text" id="governerate_code" class="form-control "
                                                id="exampleInputEmail1" name="governerate_code" placeholder="Enter Code"
                                                @error('governerate_code') is-invalid @enderror>
                                            @error('governerate_code')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputPassword1">Title</label>
                                            <input type="text" id="governerate_title" class="form-control"
                                                id="exampleInputPassword1" name="governerate_title" placeholder="Title"
                                                @error('governerate_title') is-invalid @enderror>
                                            @error('governerate_title')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="price_per_kg">Description </label>
                                            <textarea type="text" id="description" class="form-control" value=""
                                                name="description" placeholder="Village Information" @error('description')
                                                is-invalid @enderror> </textarea>
                                            @error('description')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
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
                <!-- /.row -->
            </section>
        </div>
    </div><!-- /.container-fluid -->

    <!-- /.content -->

    <!-- /.content-wrapper -->

@endsection
