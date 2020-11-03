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
                <div class="col-md-6 ">
                    <!-- general form elements -->
                    <div class="card card-primary">

                        <div class="card-header">
                            <h3 class="card-title">Edit</h3><h3 class="card-title float-sm-right">{{$farmer->farmer_code}}</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form role="form" method="POST" action="{{URL::to('/admin/updatefarmer')}}" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <input type="hidden" name="farmer_id" value="{{$farmer->farmer_id}}">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="exampleInputPassword1">Farmer Name</label>
                                    <input type="text" id="farmer_name" class="form-control" id="exampleInputPassword1" value="{{$farmer->farmer_name}}" name="farmer_name" placeholder="Last Name"  @error('farmer_name') is-invalid @enderror>
                                           @error('farmer_name')
                                           <span  class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputPassword1">Cnic</label>
                                    <input type="text" id="farmer_nicn" class="form-control" value="{{$farmer->farmer_nicn}}" name="farmer_nicn" placeholder="farmer_nicn"  @error('farmer_nicn') is-invalid @enderror>
                                           @error('farmer_nicn')
                                           <span  class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>   
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="customFile">Farmer Image</label>
                                            <input type="hidden" name="picture_id" value="{{$farmer->picture_id}}">
                                            <div class="">
                                                <input type="file" class="  name="profile_picture" id="customFile"  @error('profile_picture') is-invalid @enderror>
                                                       @error('profile_picture')
                                                       <span  class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div> 
                                    </div>
                                   <div class="col-md-6">
                                        <div class="form-group">

                                            <label for="customFile">Farmer Id Card Image</label>
                                            <input type="hidden" name="idcard_picture_id" value="{{$farmer->idcard_picture_id}}">
                                            <div class="">
                                                <input type="file" class=""   name="idcard_picture" id="customFile" @error('idcard_picture') is-invalid @enderror>

                                                       @error('idcard_picture')
                                                       <span  class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>    
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

                <div class="col-md-6 ">
                    <!-- general form elements -->
                    <div class="card card-primary">
                        <div class="card-body">


                            @if( isset($farmer->profileImage)?$farmer->profileImage->user_file_name : '')
                            <div class="form-group" style="text-align: center;">
                                <label for="customFile">Farmer Image</label><br>

                                <img class="img-thumbnail" style="height: 195px;margin-bottom: 10px" src="{{URL::to('')}}/storage/app/images/{{isset($farmer->profileImage)?$farmer->profileImage->user_file_name : ''}}">
                            </div> 
                            @else
                            <h5 class="d-flex justify-content-center">No Profile Image Found </h5>
                            @endif

                            @if(isset($farmer->idcardImage->user_file_name)?$farmer->idcardImage->user_file_name : '')

                            <div class="form-group" style="text-align: center;">

                                <label for="customFile">Id Card</label><br>

                                <img class="img-thumbnail" style="height: 195px;margin-bottom: 10px" src="{{URL::to('')}}/storage/app/images/{{isset($farmer->idcardImage->user_file_name)?$farmer->idcardImage->user_file_name : ''}}">

                            </div> 
                            @else
                            <h5 class="d-flex justify-content-center" style="margin-top: 100px">No Id Image Found</h5>
                            @endif

                        </div>
                        <!-- /.card-body -->
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
