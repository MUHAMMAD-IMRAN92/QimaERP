@extends('layouts.default')
@section('title', 'All Village')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Add Village</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Add Village</li>
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
                                <h3 class="card-title">Add</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form role="form" method="POST" action="{{ URL::to('') }}/admin/addvillage"
                                enctype="multipart/form-data">
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
                                <div class="card-body col-md-6">
                                    <div class="form-group">
                                        <label for="country_name">All Region</label>
                                        <select class="form-control input-add-inception" name="region_code"
                                            @error('region_code') is-invalid @enderror>
                                            @foreach ($region as $row)
                                                <option value="{{ $row->region_code }}">{{ $row->region_code }}</option>
                                            @endforeach
                                        </select>
                                        @error('region_code')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Village Title (English)</label>
                                        <input type="text" id="village_title" class="form-control"
                                            id="exampleInputPassword1" name="village_title"
                                            placeholder="Village Title (English)" @error('village_title') is-invalid
                                            @enderror>
                                        @error('village_title')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="village_title_ar">Village Title (Arabic)</label>
                                        <input type="text" id="village_title" class="form-control" id="village_title_ar"
                                            name="village_title_ar" placeholder="Village Title (Arabic)"
                                            @error('village_title_ar') is-invalid @enderror>
                                        @error('village_title_ar')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="price_per_kg">Price Per Kg </label>
                                        <input type="number" id="price_per_kg" class="form-control" value=""
                                            name="price_per_kg" placeholder="Price Per Kg" @error('price_per_kg') is-invalid
                                            @enderror>
                                        @error('price per kg')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="price_per_kg">Add Image </label>
                                        <br>
                                        <input type="file" id="price_per_kg" value=""
                                            name="village_image" placeholder="Price Per Kg" @error('village_image')
                                            is-invalid @enderror>
                                        @error('village_image')
                                            <span class="text-danger">{{ $message }}</span>
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

@endsection
