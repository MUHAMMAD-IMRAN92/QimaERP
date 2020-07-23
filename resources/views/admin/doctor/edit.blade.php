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
     @include('admin_layout.messages')
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
                    <img  style="width:200px" src="<?= getDocImg($doctor)?>" id="doctor_image">
                </div>
                <div class="col-md-6">
          
                 <form method="POST" action="{{asset('admin/updatedoctor')}}" enctype="multipart/form-data">
                  {{csrf_field()}}
                  <input type="hidden" name="id" value="{{$doctor->id}}">
                    <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input value="{{$doctor->first_name}}" type="text" class="form-control" id="first_name" aria-describedby="emailHelp" name="first_name" placeholder="Enter First Name">
                  </div>
                    <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input value="{{$doctor->last_name}}" type="text" class="form-control" id="last_name" aria-describedby="emailHelp" name="last_name" placeholder="Enter Lat Name">
                  </div>
                <div class="form-group">
                <label for="profile_image">Profile Image</label>
                <input style="width: 200px" accept="image/*" type="file" class="form-control" id="profile_image" aria-describedby="emailHelp" name="profile_image" >
                  </div>
                    <div class="form-group">

                        <label for="gender">Gender</label>
                      <select id="gender" class="form-control select2" name="gender" style="width: 100%;">
                       
                      <option @if($doctor->gender == 'male') selected @endif  value="male">Male</option> 
                      <option @if($doctor->gender == 'female') selected @endif value="female">Female</option> 
                      <option @if($doctor->gender == 'other') selected @endif value="other">Other</option> 
                      </select>
                      </div>
                  <div class="form-group">
                      @php 
                      $spc=$doctor->specility->pluck('speciality_id')->toArray();
                      @endphp
                        <label for="specialization">Specializations</label>
                        <select id="specialization" class="form-control select2" name="specialization[]" multiple="" style="width: 100%;">
                        @foreach($specializations as $specialization)
                      <option @if(in_array($specialization->id,$spc)) selected @endif value="{{$specialization->id}}">{{$specialization->title}}</option>
                      @endforeach
                      </select>
                      </div>
                    <div class="form-group">

                        <label for="city_id">Select City</label>
                      <select id="city_id" class="form-control select2" name="city_id" style="width: 100%;">
                      @foreach($cities as $city)
                      <option @if($doctor->city_id == $city->id) selected @endif value="{{$city->id}}">{{$city->title}}</option>
                      @endforeach
                      </select>
                      </div>
                  
                  <div class="form-group">
                      <label for="location">Address</label>
                    <input value="{{$doctor->location}}" type="text" class="form-control" id="location" aria-describedby="emailHelp" name="location" placeholder="Enter Address">
                  </div>
                  
                  <div class="form-group">
                      <label for="phone">Phone</label>
                    <input value="{{$doctor->phone}}" type="number" class="form-control" id="phone" aria-describedby="emailHelp" name="phone" placeholder="Enter Phone Number">
                  </div> 
                  <div class="form-group">
                      <label for="fee">Fee</label>
                      <input step=".1" value="{{$doctor->fee}}" type="number" class="form-control" id="fee" aria-describedby="emailHelp" name="fee" placeholder="Enter Fee">
                  </div> 
                   <div class="form-group">
                       <label for="Password">Password</label>
                       <input autocomplete="off" type="text" class="form-control" id="Password" aria-describedby="emailHelp" name="password" placeholder="Enter Password">
                  </div>
                   <div class="form-group">
                       <label for="bio">Bio</label>
                       <textarea rows="4" class="form-control" id="bio" aria-describedby="emailHelp" name="bio" placeholder="Enter Bio">{{$doctor->bio}}</textarea>
                  </div>
                  <button type="submit" class="btn btn-primary">Submit</button>
                </form>
                </div>
                  <div class="col-md-3">
                      <p class="text-center">
                      <a  class="btn btn-primary pull-right"href="{{asset('admin/doctors')}}"><strong>All Doctors  </strong></a>  
                     
                  </p>
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

$("#profile_image").change(function(){
    readURL(this);
});
    </script>
@endsection