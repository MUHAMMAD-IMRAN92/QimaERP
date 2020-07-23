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
            @if(Session::has('message'))
            <div class="alert alert-info alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                {{ Session::get('message') }}
            </div> 
            @endif
            <!-- Info boxes -->
            <!-- /.row -->
            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Admin Profile</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <img style="width:200px" src="" id="doctor_image">
                                </div>
                                <div class="col-md-6">
                                    <form method="POST" action="{{asset('admin/update_profile')}}" enctype="multipart/form-data" id="hospital_form">
                                        {{csrf_field()}}      
                                        <div class="form-group">
                                            <label for="hos_name">Name <span style="color: red">*</span></label>
                                            <input type="text" class="form-control" id="hos_name" aria-describedby="emailHelp" name="name" placeholder="Enter Name"  value="{{ old('name' ,$admin->name) }}">
                                            @if ($errors->has('name'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong class="clr-red">{{ $errors->first('name') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            <label for="hos_address">Email<span style="color: red">*</span></label>
                                            <input type="text" class="form-control" id="hos_address" aria-describedby="emailHelp" name="email" placeholder="Enter Email" value="{{ old('email' ,$admin->email) }}">
                                            @if ($errors->has('email'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong class="clr-red">{{ $errors->first('email') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            <label for="Password">Password</label>
                                            <input type="text" autocomplete="off" class="form-control" id="Password" aria-describedby="emailHelp" name="password" placeholder="Enter Password"  value="{{ old('password') }}">
                                            @if ($errors->has('password'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong class="clr-red">{{ $errors->first('password') }}</strong>
                                            </span>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label for="Password">Confirm Password</label>
                                            <input type="text" autocomplete="off" class="form-control" id="Password" aria-describedby="emailHelp" name="password_confirmation" placeholder="Enter Password"  value="{{ old('password_confirmation') }}">
                                            @if ($errors->has('password_confirmation'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong class="clr-red">{{ $errors->first('password_confirmation') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </form>
                                </div>
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
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyALhVLV8VPUoHqK9OzUzRzvhuPsmtuLJIY&libraries=places"></script>
<script>
    function initialize() {
        var input = document.getElementById('hos_address');
        var autocomplete = new google.maps.places.Autocomplete(input);
        google.maps.event.addListener(autocomplete, 'place_changed', function () {
            var place = autocomplete.getPlace();
            document.getElementById('hospital_latitute').value = place.geometry.location.lat();
            document.getElementById('hospital_longitude').value = place.geometry.location.lng();
        });
    }
    google.maps.event.addDomListener(window, 'load', initialize);
</script>
@endsection