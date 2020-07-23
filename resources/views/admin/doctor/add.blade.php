@extends('admin_layout.app')
@section('page_css')
@endsection
@section('content')
<div class="wrapper">
    @include('admin_layout.topbar')
    @include('admin_layout.sidebar')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        @include('admin_layout.header')
        <!-- Main content -->
        <section class="content">
            <!-- Info boxes -->
            <!-- /.row -->
            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">{{$title}}</h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <img style="width:200px" src="" id="doctor_image">
                                </div>
                                <div class="col-md-6">

                                    <form method="POST" action="{{asset('admin/storedoctor')}}" enctype="multipart/form-data">
                                        {{csrf_field()}}
                                        <div class="form-group">
                                            <label for="first_name">First Name <span style="color: red">*</span></label>
                                            <input type="text" class="form-control" id="first_name" aria-describedby="emailHelp" name="first_name" placeholder="Enter First Name" value="{{ old('first_name') }}">
                                            @if ($errors->has('first_name'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong class="clr-red">{{ $errors->first('first_name') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            <label for="last_name">Last Name <span style="color: red">*</span></label>
                                            <input type="text" class="form-control" id="last_name" aria-describedby="emailHelp" name="last_name" placeholder="Enter Last Name" value="{{ old('last_name') }}">
                                            @if ($errors->has('last_name'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong  class="clr-red">{{ $errors->first('last_name') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            <label for="email">Email <span style="color: red">*</span></label>
                                            <input type="text" class="form-control" id="email" aria-describedby="emailHelp" name="email" placeholder="Enter Email" value="{{ old('email') }}">
                                            @if ($errors->has('email'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong  class="clr-red">{{ $errors->first('email') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            <label for="profile_image">Profile Image</label>
                                            <input style="width: 200px" accept="image/*" type="file" class="form-control" id="profile_image" aria-describedby="emailHelp" name="profile_image" >
                                        </div>
                                        <div class="form-group">
                                            <label for="gender">Gender <span style="color: red">*</span></label>
                                            <select id="gender" class="form-control select2" name="gender" style="width: 100%;">
                                                <option value="male"  @if (old('gender') == 'male') selected="selected" @endif>Male</option> 
                                                <option value="female" @if (old('gender') == 'female') selected="selected" @endif>Female</option> 
                                                <option value="other" @if (old('gender') == 'other') selected="selected" @endif>Other</option> 
                                            </select>
                                        </div>
                                        <div class="form-group">

                                            <label for="specialization">Specializations <span style="color: red">*</span></label>
                                            <select id="specialization" class="form-control select2" name="specialization[]" multiple="" style="width: 100%;">
                                                @foreach($specializations as $specialization)
                                                <option value="{{$specialization->id}}">{{$specialization->title}}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('specialization'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong  class="clr-red">{{ $errors->first('specialization') }}</strong>
                                            </span>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label for="city_id">Select City <span style="color: red">*</span></label>
                                            <select id="city_id" class="form-control select2" name="city_id" style="width: 100%;">
                                                @foreach($cities as $city)
                                                <option value="{{$city->id}}" @if (old('city_id') == $city->id) selected="selected" @endif>{{$city->title}}</option>
                                                @endforeach
                                            </select>
                                        </div>

<!--                                        <div class="form-group">
                                            <label for="location">Address <span style="color: red">*</span></label>
                                            <input type="text" class="form-control" id="location" aria-describedby="emailHelp" name="location" placeholder="Enter Address" value="{{ old('location') }}">
                                            @if ($errors->has('location'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('location') }}</strong>
                                            </span>
                                            @endif
                                        </div>-->
                                        <div class="form-group">
                                            <label for="fee">Fee <span style="color: red">*</span></label>
                                            <input step=".1" type="number" class="form-control" id="fee" aria-describedby="emailHelp" name="fee" placeholder="Enter Fee" value="{{ old('fee') }}">
                                            @if ($errors->has('fee'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong  class="clr-red">{{ $errors->first('fee') }}</strong>
                                            </span>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label for="phone">Phone <span style="color: red">*</span></label>
                                            <input type="text" class="form-control" id="phone" aria-describedby="emailHelp" name="phone"  placeholder="+92xxxxxx" value="{{ old('phone') }}">
                                            @if ($errors->has('phone'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong class="clr-red">{{ $errors->first('phone') }}</strong>
                                            </span>
                                            @endif
                                        </div> 
                                        <div class="form-group">
                                            <label for="Password">Password <span style="color: red">*</span></label>
                                            <input type="text" autocomplete="off" class="form-control" id="Password" aria-describedby="emailHelp" name="password" placeholder="Enter Password"  value="{{ old('password') }}">
                                            @if ($errors->has('password'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong class="clr-red">{{ $errors->first('password') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            <label for="bio">Bio <span style="color: red">*</span></label>
                                            <textarea rows="4" class="form-control" id="bio" aria-describedby="emailHelp" name="bio" placeholder="Enter Bio">{{ old('bio') }}</textarea>
                                            @if ($errors->has('bio'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong class="clr-red">{{ $errors->first('bio') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </form>
                                </div>
                                <!-- <div class="col-md-3">
                                    <p class="text-center">
                                        <a  class="btn btn-primary pull-right"href="{{asset('admin/doctors')}}"><strong>All Doctors  </strong></a>  

                                    </p>
                                </div> -->
                                <!-- /.row -->
                            </div>
                        </div>
                        <!-- /.box -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    @include('admin_layout.footer')
    <div class="control-sidebar-bg"></div>

</div>

@endsection

@section('page_js')
<script>
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#doctor_image').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#profile_image").change(function () {
        readURL(this);
    });
</script>
@endsection