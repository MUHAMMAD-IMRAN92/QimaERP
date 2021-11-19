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
                                        <label for="country_name">Villages</label>
                                        <select class="form-control input-add-inception" name="village_code"
                                            @error('village_code') is-invalid @enderror>
                                            @foreach ($villages as $row)
                                                <option value="{{ $row->village_code }}">
                                                    {{ $row->village_title . '  (' . $row->village_code . ')' }}</option>
                                            @endforeach
                                        </select>
                                        @error('village_code')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Farmer Name</label>
                                        <input type="text" id="farmer_name" class="form-control"
                                            id="exampleInputPassword1" value="" name="farmer_name" placeholder="Last Name"
                                            @error('farmer_name') is-invalid @enderror>
                                        @error('farmer_name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">CNIC</label>
                                        <input type="text" id="farmer_nicn" class="form-control" value=""
                                            name="farmer_nicn" placeholder="Farmer CNIC" @error('farmer_nicn') is-invalid
                                            @enderror>
                                        @error('farmer_nicn')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Price Per Kg </label>
                                        <input type="number" id="price_per_kg" class="form-control" value=""
                                            name="price_per_kg" placeholder="Price Per Kg" @error('price_per_kg') is-invalid
                                            @enderror>
                                        @error('price_per_kg')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Tel</label>
                                        <input type="text" id="ph" class="form-control" value="" name="ph_no"
                                            placeholder="Tel No" @error('ph') is-invalid @enderror>
                                        @error('ph')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Reward Per Kg </label>
                                        <input type="number" id="reward" class="form-control" value="" name="reward"
                                            placeholder="Price Per Kg" @error('price_per_kg') is-invalid @enderror>
                                        @error('price_per_kg')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Cupping Profile </label>
                                        <input type="text" id="cup_prof" class="form-control" value="" name="cup_prof"
                                            placeholder="Cupping Score" @error('') is-invalid @enderror>
                                        @error('')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Cupping Score </label>
                                        <input type="number" id="cup_score" class="form-control" value="" name="cup_score"
                                            placeholder="Cupping Score" @error('') is-invalid @enderror>
                                        @error('')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">House Hold Size </label>
                                        <input type="number" id="cup_score" class="form-control" value=""
                                            name="house_hold" placeholder="Cupping Score" @error('') is-invalid @enderror>
                                        @error('')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Farm Size</label>
                                        <input type="number" id="cup_score" class="form-control" value="" name="farm_size"
                                            placeholder="Farm Size" @error('') is-invalid @enderror>
                                        @error('')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">No Of Trees</label>
                                        <input type="number" id="trees" class="form-control" value="" name="tree"
                                            placeholder="No Of Tree" @error('') is-invalid @enderror>
                                        @error('')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Altitude </label>
                                        <input type="number" id="alt" class="form-control" value="" name="alt"
                                            placeholder="Altitude" @error('') is-invalid @enderror>
                                        @error('')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
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
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="customFile">Farmer Id Card Image</label>
                                                <div class="">
                                                    <input type="file" class="" name="idcard_picture"
                                                        id="customFile" @error('idcard_picture') is-invalid @enderror>
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
                                    <button type="submit" class="btn btn-primary text-uppercase letter-spacing-1"
                                        id="submit-btn-ofform">Submit</button>
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
