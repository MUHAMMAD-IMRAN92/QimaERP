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
                            <h3 class="box-title">Add New Hospital</h3>
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
                                    <form method="POST" action="{{asset('admin/store')}}" enctype="multipart/form-data" id="hospital_form">
                                        {{csrf_field()}}
                                        <div class="form-group">
                                            <label for="city_id">Select City <span style="color: red">*</span></label>
                                            <select id="city_id" class="form-control select2" name="city_id" style="width: 100%;">
                                                @foreach($cities as $city)
                                                <option value="{{$city->id}}">{{$city->title}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="hos_name">Add Hospital Name <span style="color: red">*</span></label>
                                            <input type="text" class="form-control" id="hos_name" aria-describedby="emailHelp" name="hospital_name" placeholder="Enter Hospital Name"  value="{{ old('hospital_name') }}">
                                            @if ($errors->has('hospital_name'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong class="clr-red">{{ $errors->first('hospital_name') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            <label for="hos_address">Add Hospital Address <span style="color: red">*</span></label>
                                            <input type="text" class="form-control" id="hos_address" aria-describedby="emailHelp" name="hospital_address" placeholder="Enter Hospital Address" value="{{ old('hospital_address') }}">
                                            @if ($errors->has('hospital_address'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong class="clr-red">{{ $errors->first('hospital_address') }}</strong>
                                            </span>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label for="hos_phone_num">Add Hospital Phone <span style="color: red">*</span></label>
                                            <input type="text" class="form-control" id="hos_phone_num" aria-describedby="emailHelp" name="phone" placeholder="+92xxxxxx" value="{{ old('phone') }}">
                                            @if ($errors->has('phone'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong class="clr-red">{{ $errors->first('phone') }}</strong>
                                            </span>
                                            @endif
                                        </div>


                                        <div class="form-group">
                                            <label for="hos_phone_num">Hospital Image</label>
                                            <input type="file" class="form-control" id="profile_image" aria-describedby="emailHelp" name="hos_image"  accept="image/*">
                                        </div>
                                        <div class="form-group">
                                            <label for="about">Hospital Latitude</label>
                                            <input type="text" class="form-control" id="hospital_latitute" aria-describedby="emailHelp" value="{{ old('hos_lat') }}" name="hos_lat" placeholder="Enter Hospital Location lat">
                                        </div>
                                        <div class="form-group">
                                            <label for="about">Hospital longitude</label>
                                            <input type="text" class="form-control" id="hospital_longitude" aria-describedby="emailHelp" value="{{ old('hos_lng') }}" name="hos_lng" placeholder="Enter Hospital Location lng">
                                        </div>

                                        <div class="form-group">
                                            <label for="about">Website Link <span style="color: red">*</span></label>
                                            <input type="text" class="form-control" id="exampleInputHospital" aria-describedby="emailHelp" value="{{ old('website_link') }}" name="website_link" placeholder="https://www.google.com/">
                                            @if ($errors->has('website_link'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong class="clr-red">{{ $errors->first('website_link') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            <label for="about">Hospital Type <span style="color: red">*</span></label>
                                            <select id="gender" class="form-control select2" name="type" style="width: 100%;" value="{{ old('type') }}">
                                                <option value="private">Private</option> 
                                                <option value="government">Government</option> 
                                                <option value="Semi Government">Other</option> 
                                            </select>
                                        </div>
                                        <div class="form-group"> 
                                            <label for="about">Closed Time <span style="color: red">*</span></label>
                                            <input  value="{{ old('closed_time') }}" type="time" class="form-control" id="closed_time" aria-describedby="emailHelp" name="closed_time">
                                            @if ($errors->has('closed_time'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong class="clr-red">{{ $errors->first('closed_time') }}</strong>
                                            </span>
                                            @endif
                                        </div> 
                                        <div class="form-group">
                                            <label for="about">About <span style="color: red">*</span></label>
                                            <textarea rows="4" class="form-control" id="about" aria-describedby="emailHelp" name="about" placeholder="Enter About">{{ old('about') }}</textarea>
                                            @if ($errors->has('about'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong class="clr-red">{{ $errors->first('about') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            <label for="facilitates">Facilitates <span style="color: red"></span></label>
                                            <input class="form-control"  name="facilitates[]" value="{{ old('facilitates[]') }}" id="facilitates_check">
                                            
                                            @if ($errors->has('facilitates'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong class="clr-red">{{ $errors->first('facilitates') }}</strong>
                                            </span>
                                            @endif
                                        </div>

                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </form>
                                </div>
                                <!-- <div class="col-md-3">
                                    <p class="text-center">
                                        <a  class="btn btn-primary pull-right"href="{{asset('admin/allhospitals')}}"><strong>All Hospitals:  </strong></a>  

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