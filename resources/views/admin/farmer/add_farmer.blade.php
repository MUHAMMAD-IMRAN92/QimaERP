@extends('layouts.default')
@section('title', 'Edit Farmer')

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Farmer</h1>
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
                        <form role="form" method="POST" action="{{URL::to('/admin/create_farmer')}}" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="country_name">Villages</label>
                                    <select class="form-control input-add-inception" name="village_code" @error('village_code') is-invalid @enderror>
                                            @foreach($villages as $row)
                                            <option value="{{$row->village_code}}">{{$row->village_title .'  ('.$row->village_code .')'}}</option>
                                        @endforeach
                                    </select>
                                    @error('village_code')
                                    <span  class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputPassword1">Farmer Name</label>
                                    <input type="text" id="farmer_name" class="form-control" id="exampleInputPassword1" value="" name="farmer_name" placeholder="Last Name"  @error('farmer_name') is-invalid @enderror>
                                           @error('farmer_name')
                                           <span  class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputPassword1">CNIC</label>
                                    <input type="text" id="farmer_nicn" class="form-control" value="" name="farmer_nicn" placeholder="Farmer CNIC"  @error('farmer_nicn') is-invalid @enderror>
                                           @error('farmer_nicn')
                                           <span  class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>   

                                <div class="form-group">
                                    <label for="customFile">Farmer Image</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input"  name="profile_picture" id="customFile"  @error('profile_picture') is-invalid @enderror>
                                               <label class="custom-file-label" for="customFile">Choose file</label>
                                        @error('profile_picture')
                                        <span  class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div> 

                                <div class="form-group">

                                    <label for="customFile">Farmer Id Card Image</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input"   name="idcard_picture" id="customFile" @error('idcard_picture') is-invalid @enderror>
                                               <label class="custom-file-label" for="customFile">Choose file</label>
                                        @error('idcard_picture')
                                        <span  class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>     
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Update</button>
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
