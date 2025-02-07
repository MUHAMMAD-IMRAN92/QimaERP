@extends('layouts.default')
@section('title', 'Edit Farmer')

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <div class="mx-lg-5">
        <!-- Content Header (Page header) -->
        <div class="row">
            <div class="col-md-12">
                @if (Session::has('msg'))
                    <div class="alert alert-success" role="alert">
                        <b>{{ Session::get('msg') }}</b>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                @endif
            </div>
        </div>
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark text-uppercase font-weight-lighter text-heading">Edit Farmer</h1>
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
                    <div class="col-md-6 ">
                        <!-- general form elements -->
                        <div class="card card-primary">

                            <div class="card-header">
                                <h3 class="card-title text-uppercase letter-spacing-2">Edit</h3>
                                <h3 class="card-title float-sm-right">{{ $farmer->farmer_code }}</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form role="form" method="POST" action="{{ URL::to('/admin/updatefarmer') }}"
                                enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <input type="hidden" name="farmer_id" value="{{ $farmer->farmer_id }}">
                                <div class="card-body  text-uppercase letter-spacing-1">
                                    <div class="form-group">

                                        <input type="hidden" class="form-control"
                                            value="{{ Str::beforelast($farmer->farmer_code, '-') }}" name="code"
                                            {{ count($transaction) > 0 ? 'readonly' : '' }}>

                                    </div>
                                    <div class="form-group">
                                        <label for="farmer_code">Farmer Code</label>
                                        <input type="text" class="form-control" id=""
                                            value="{{ Str::afterlast($farmer->farmer_code, '-') }}" name="farmer_code"
                                            placeholder="Last Name" @error('farmer_name') is-invalid @enderror
                                            {{ count($transaction) > 0 ? 'readonly' : '' }}>

                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Farmer Name</label>
                                        <input type="text" id="farmer_name" class="form-control"
                                            id="exampleInputPassword1" value="{{ $farmer->farmer_name }}"
                                            name="farmer_name" placeholder="Last Name" @error('farmer_name') is-invalid
                                            @enderror>
                                        @error('farmer_name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Cnic</label>
                                        <input type="text" id="farmer_nicn" class="form-control"
                                            value="{{ $farmer->farmer_nicn }}" name="farmer_nicn"
                                            placeholder="farmer_nicn" @error('farmer_nicn') is-invalid @enderror>
                                        @error('farmer_nicn')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Price Per Kg </label>
                                        <input type="number" id="price_per_kg" class="form-control"
                                            value="{{ $farmer->price_per_kg }}" name="price_per_kg"
                                            placeholder="Price Per Kg" @error('') is-invalid @enderror>
                                        @error('')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Tel</label>
                                        <input type="text" id="ph" class="form-control" value="{{ $farmer->ph_no }}"
                                            name="ph_no" placeholder="Tel No" @error('ph') is-invalid @enderror>
                                        @error('ph')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Reward Per Kg </label>
                                        <input type="number" id="reward" class="form-control"
                                            value="{{ $farmer->reward }}" name="reward" placeholder="Price Per Kg"
                                            @error('price_per_kg') is-invalid @enderror>
                                        @error('price_per_kg')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Cupping Profile </label>
                                        <input type="text" id="cup_prof" class="form-control"
                                            value="{{ $farmer->cup_profile }}" name="cup_prof"
                                            placeholder="Cupping Score" @error('') is-invalid @enderror>
                                        @error('')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Cupping Score </label>
                                        <input type="number" id="cup_score" class="form-control"
                                            value="{{ $farmer->cupping_score }}" name="cup_score"
                                            placeholder="Cupping Score" @error('') is-invalid @enderror>
                                        @error('')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">House Hold Size </label>
                                        <input type="number" id="cup_score" class="form-control"
                                            value="{{ $farmer->house_hold_size }}" name="house_hold"
                                            placeholder="Cupping Score" @error('') is-invalid @enderror>
                                        @error('')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Farm Size</label>
                                        <input type="number" id="cup_score" class="form-control"
                                            value="{{ $farmer->farm_size }}" name="farm_size" placeholder="Farm Size"
                                            @error('') is-invalid @enderror>
                                        @error('')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">No Of Trees</label>
                                        <input type="number" id="trees" class="form-control"
                                            value=" {{ $farmer->no_of_trees }}" name="tree" placeholder="No Of Tree"
                                            @error('') is-invalid @enderror>
                                        @error('')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Altitude </label>
                                        <input type="number" id="alt" class="form-control"
                                            value="{{ $farmer->altitude }}" name="alt" placeholder="Altitude" @error('')
                                            is-invalid @enderror>
                                        @error('')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Farmer Information </label>
                                        <textarea type="text" id="info" class="form-control" value="" name="info"
                                            placeholder="Farmer Info" @error('') is-invalid
                                            @enderror>{{ $farmer->farmer_info }}</textarea>
                                        @error('')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="customFile">Farmer Image</label>
                                                <input type="hidden" name="picture_id" value="{{ $farmer->picture_id }}">
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
                                                <input type="hidden" name="idcard_picture_id"
                                                    value="{{ $farmer->idcard_picture_id }}">
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
                                    <button type="submit" class="btn btn-primary text-uppercase letter-spacing-1">Update</button>
                                </div>
                            </form>
                        </div>
                        <!-- /.card -->


                    </div>
                    <!-- /.card -->

                    <div class="col-md-6 ">
                        <!-- general form elements -->
                        <div class="card card-primary">
                            <div class="card-body">


                                @if (isset($farmer->profileImage))
                                    <div class="form-group" style="text-align: center;">
                                        <label for="customFile">Farmer Image</label><br>

                                        <img class="img-thumbnail" style="height: 195px;margin-bottom: 10px"
                                            {{-- src="{{ URL::to('') }}/storage/app/images/{{ isset($farmer->profileImage) ? $farmer->profileImage->user_file_name : '' }}" --}}
                                            src=" {{ Storage::disk('s3')->url('images/' . $farmer->profileImage->user_file_name) }}">
                                    </div>
                                @else
                                    <h5 class="d-flex justify-content-center">No Profile Image Found </h5>
                                @endif

                                @if (isset($farmer->idcardImage->user_file_name))

                                    <div class="form-group" style="text-align: center;">

                                        <label for="customFile">Id Card</label><br>

                                        <img class="img-thumbnail" style="height: 195px;margin-bottom: 10px"
                                            {{-- src="{{ URL::to('') }}/storage/app/images/{{ isset($farmer->idcardImage->user_file_name) ? $farmer->idcardImage->user_file_name : '' }}" --}}
                                            src=" {{ Storage::disk('s3')->url('images/' . $farmer->idcardImage->user_file_name) }} ">
                                    </div>
                                @else
                                    <h5 class="d-flex justify-content-center" style="margin-top: 100px">No Id Image Found
                                    </h5>
                                @endif

                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>

                </div>

            </div>
        </div>   <!-- /.row -->
    </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection
