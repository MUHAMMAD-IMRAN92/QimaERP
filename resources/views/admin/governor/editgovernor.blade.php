@extends('layouts.default')
@section('title', 'All Governor')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <div class="mx-lg-5">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark text-uppercase font-weight-lighter text-heading">Edit Governor</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Edit Governor</li>
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

                            {{-- <div class="card-header">
                <h3 class="card-title">Update</h3>
              </div> --}}
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form role="form" method="POST" action="{{ URL::to('') }}/admin/updategovernor">
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                {{ csrf_field() }}
                                <div class="card-body col-md-6 text-uppercase letter-spacing-1">
                                    <input type="hidden" name="governor_id" value="{{ $governor->governerate_id }}">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Code</label>
                                        <input type="text" class="form-control" id="exampleInputEmail1"
                                            name="governerate_code" value="{{ $governor->governerate_code }}"
                                            placeholder="Enter Code" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Title</label>
                                        <input type="text" class="form-control" id="exampleInputPassword1"
                                            value="{{ $governor->governerate_title }}" name="governerate_title"
                                            placeholder="Title">
                                    </div>

                                    <div class="form-group">
                                        <label for="price_per_kg">Governerate Information</label>
                                        <textarea type="text" id="description" class="form-control" value=""
                                            name="description" placeholder="Village Information" @error('description')
                                            is-invalid @enderror> {{ $governor->description }}</textarea>
                                        @error('description')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <!-- /.card-body -->

                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary text-uppercase letter-spacing-1">Update</button>
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
