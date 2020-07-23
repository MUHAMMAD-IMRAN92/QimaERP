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
                            <h3 class="box-title">Edit Hospital</h3>

                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>

                                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <img style="width:200px" src="" id="doctor_image">
                                </div>
                                <div class="col-md-4">
                                    <p class="text-center">
                                        <button type="button" class="btn btn-block btn-default"> <a href="{{asset('/admin/allhospitals')}}"><strong>All Hospitals:  </strong></a> </button>

                                    </p>

                                    <form method="POST" action="{{asset('admin/update')}}" enctype="multipart/form-data" id="hospital_form">
                                        {{csrf_field()}}
                                        <div class="form-group">
                                            <label>Select City <span style="color: red">*</span></label>
                                            <select class="form-control select2" name="city_id" style="width: 100%;">
                                                {{-- <option value="{{ $cities->City()->id}}" selected="">{{ $cities->City()->title}} </option> --}}
                                                @foreach($cities as $city)
                                                {{-- <option value="{{$city->id}}">{{$city->title}}</option> --}}
                                                <option value="{{ $city->id }}" {{$Hospitals->city_id == $city->id  ? 'selected' : ''}}>{{ $city->title}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInput">Hospital Name <span style="color: red">*</span></label>
                                            <input type="text" class="form-control" id="exampleInputHospital" aria-describedby="emailHelp" value="{{ old('hos_name',$Hospitals->name) }}" name="hos_name" placeholder="Enter Hospital Name">
                                            @if ($errors->has('hos_name'))
                                            <span class="invalid-feedback clr-red" role="alert">
                                                <strong>{{ $errors->first('hos_name') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            <label for="about">Hospital Address <span style="color: red">*</span></label>
                                            <input type="text" class="form-control" id="hos_address" aria-describedby="emailHelp" value="{{ old('hos_address',$Hospitals->location) }}" name="hos_address" placeholder="Enter Hospital Address">
                                            @if ($errors->has('hos_address'))
                                            <span class="invalid-feedback clr-red" role="alert">
                                                <strong>{{ $errors->first('hos_address') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            <label for="about">Hospital Image</label>
                                            <input type="file" class="form-control"  aria-describedby="emailHelp" name="hos_image" accept="image/*" id="profile_image">
                                        </div>
                                        <div class="form-group">
                                            <label for="about">Hospital Phone <span style="color: red">*</span></label>
                                            <input type="text" class="form-control" id="exampleInputHospital" aria-describedby="emailHelp" value="{{ old('phone',$Hospitals->phone) }}" name="phone" placeholder="+92xxxxxx">
                                            @if ($errors->has('phone'))
                                            <span class="invalid-feedback clr-red" role="alert">
                                                <strong>{{ $errors->first('phone') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            <label for="about">Hospital Latitude</label>
                                            <input type="text" class="form-control" id="hospital_latitute" aria-describedby="emailHelp" value="{{ old('hos_lat',$Hospitals->lat) }}" name="hos_lat" placeholder="Enter Hospital Location lat" readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="about">Hospital longitude</label>
                                            <input type="text" class="form-control" id="hospital_longitude" aria-describedby="emailHelp" value="{{ old('hos_lng',$Hospitals->lng) }}" name="hos_lng" placeholder="Enter Hospital Location lng" readonly>
                                        </div>

                                        <div class="form-group">
                                            <label for="about">Website Link <span style="color: red">*</span></label>
                                            <input type="text" class="form-control" id="exampleInputHospital" aria-describedby="emailHelp" value="{{ old('website_link',$Hospitals->website_link) }}" name="website_link" placeholder="https://www.google.com/">
                                            @if ($errors->has('website_link'))
                                            <span class="invalid-feedback clr-red" role="alert">
                                                <strong>{{ $errors->first('website_link') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            <label for="about">Hospital Type <span style="color: red">*</span></label>
                                            <select id="gender" class="form-control select2" name="type" style="width: 100%;">
                                                <option @if($Hospitals->type == 'male') selected @endif  value="private">Private</option> 
                                                <option @if($Hospitals->type == 'government') selected @endif value="government">Government</option> 
                                                <option @if($Hospitals->type == 'semi government') selected @endif value="Semi Government">Other</option> 
                                            </select>
                                        </div>
                                        <div class="form-group"> 
                                            <label for="about">Closed Time <span style="color: red">*</span></label>
                                            <input  value="{{$Hospitals->closed_time}}" type="time" class="form-control" id="closed_time" aria-describedby="emailHelp" name="closed_time">
                                            @if ($errors->has('closed_time'))
                                            <span class="invalid-feedback clr-red" role="alert">
                                                <strong>{{ $errors->first('closed_time') }}</strong>
                                            </span>
                                            @endif
                                        </div> 


                                        <div class="form-group">
                                            <label for="about">About <span style="color: red">*</span></label>
                                            <textarea rows="4" class="form-control" id="about" aria-describedby="emailHelp" name="about" placeholder="Enter About">{{$Hospitals->about}}</textarea>
                                            @if ($errors->has('about'))
                                            <span class="invalid-feedback clr-red" role="alert">
                                                <strong>{{ $errors->first('about') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                        <div class="form-group example_multivalue">
                                            <label for="facilitates">Facilitates </label>
                                            <select multiple data-role="tagsinput" name="facilitates[]" id="facilitates_check">
                                                <?php
                                                $facilitates = $Hospitals->facilitates;
                                                $facilitates = explode(",", $facilitates);
                                                foreach ($facilitates as $key => $facilitate) {
                                                    ?>
                                                    <option value="{{$facilitate}}">{{$facilitate}}</option>
                                                <?php }
                                                ?>
                                            </select>
                                            @if ($errors->has('facilitates'))
                                            <span class="invalid-feedback clr-red" role="alert">
                                                <strong>{{ $errors->first('facilitates') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                        <input type="hidden" name="hos_id" value="{{ $Hospitals->id}}">
                                        <button type="submit" class="btn btn-primary">Update</button>
                                    </form>
                                </div>

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