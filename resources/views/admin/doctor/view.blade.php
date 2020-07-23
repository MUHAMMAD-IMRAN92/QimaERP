@extends('admin_layout.app')
@section('page_css')
@endsection
@section('content')
<?php
$status = null;
$type = null;
if (isset($_GET["status"])) {
    $status = $_GET["status"];
}
if (isset($_GET["type"])) {
    $type = $_GET["type"];
}
?>
<div class="wrapper">
    @include('admin_layout.topbar')
    @include('admin_layout.sidebar') 
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        @include('admin_layout.header')
        <!-- Main content -->
        <section class="content">
            @include('admin_layout.messages')           
            <div class="row">
                <div class="col-md-3">

                    <!-- Profile Image -->
                    <div class="box box-primary">
                        <div class="box-body box-profile">
                            <img class="profile-user-img img-responsive profile-pic  img-circle" src="<?= getDocImg($doctor) ?>" alt="Doctor profile picture">

                            <h3 class="profile-username text-center">{{$doctor->first_name.' '.$doctor->last_name}}</h3>

                            <p class="text-muted text-center">Doctor</p>

                            <ul class="list-group list-group-unbordered">
                                <li class="list-group-item">
                                    <b>Total Booking</b> <a class="pull-right">{{$doctor->bookings->count()}}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Total Earning</b> <a class="pull-right">{{$doctor->bookings->where('status','completed')->sum('amount')}}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Total Online Booking</b> <a class="pull-right">{{$doctor->onlineBooking->count()}}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Total Hospital Booking</b> <a class="pull-right">{{$doctor->hospitalBooking->count()}}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Total Completed Booking</b> <a class="pull-right">{{$doctor->bookings->where('status','completed')->count()}}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Total Pending Booking</b> <a class="pull-right">{{$doctor->bookings->where('status','pending')->count()}}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Total Approved Booking</b> <a class="pull-right">{{$doctor->bookings->where('status','approved')->count()}}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Total Rejected Booking</b> <a class="pull-right">{{$doctor->bookings->where('status','rejected')->count()}}</a>
                                </li>

                            </ul>

                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->

                    <!-- About Me Box -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">About Me</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <strong><i class="fa fa-book margin-r-5"></i> Education</strong>

                            <p class="text-muted">
                                <?php
                                if (isset($doctor->doctorEducation)) {
                                    foreach ($doctor->doctorEducation as $key => $doctorEducation) {
                                        echo $doctorEducation->institution . ' ' . $doctorEducation->education . '<br>';
                                    }
                                }
                                ?>

                            </p>

                            <hr>
                            <strong><i class="fa fa-book margin-r-5"></i> Bio</strong>

                            <p class="text-muted">
                                {{$doctor->bio}}
                            </p>

                            <hr>

                            <strong><i class="fa fa-map-marker margin-r-5"></i> Location</strong>

                            <p class="text-muted">{{$doctor->location}}</p>

                            <hr>

                            <strong><i class="fa fa-pencil margin-r-5"></i>Specializations</strong>

                            <p>
                                <?php foreach ($doctor->specility as $specility) { ?>  
                                    <span class="label label-primary">{{$specility->title}}</span>
                                <?php } ?>
                            </p> 
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
                <!-- /.col -->
                <div class="col-md-9">

                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs" id="myTab">

                            <li class="{{ (!Session::get('tab2_active') || Session::get('tab2_active')=='appointments' ) ?'active'  : ''}}"><a href="#appointments" data-toggle="tab">Appointments</a></li>
                            <li ><a href="#onlinetime" data-toggle="tab">Online Time</a></li>
                            <li><a href="#hospitaltime" data-toggle="tab">Hospital Time</a></li>
                            <li class="tab {{ (Session::get('tab2_active')=='setting') ?'active'  : ''}}"><a href="#settings" data-toggle="tab">Settings</a></li>
                            <li><a href="#dreducation" data-toggle="tab">Education</a></li>
                        </ul>
                        <div class="tab-content">
                            <!--Appointments-->
                            <!-- /.tab-pane -->
                            <div class="{{ (!Session::get('tab2_active') || Session::get('tab2_active')=='appointments' ) ?'active'  : ''}} tab-pane" id="appointments">
                                <!-- The timeline -->
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="box">
                                            <div class="frm-top">
                                                <!-- <div class="form-group"> -->
                                                <form method="GET" action="{{asset('admin/doctordetail/'.$doctor->id)}}" enctype="multipart/form-data">
                                                    <div class="col-md-4">
                                                        <select id="type" class="form-control" name="type" style="width: 100%;">

                                                            <option @if($type == 'online') selected @endif value="online">Online</option> 
                                                            <option @if($type == 'hospital') selected @endif value="hospital">Hospital</option> 
                                                        </select>
                                                    </div>

                                                    <div class="col-md-5">
                                                        <select id="status" class="form-control" name="status" style="width: 100%;">
                                                            <option @if($status == 'pending') selected @endif  value="pending">Pending</option> 
                                                            <option @if($status == 'rejected') selected @endif value="rejected">Rejected</option> 
                                                            <option @if($status == 'approved') selected @endif value="approved">Approved</option> 
                                                            <option @if($status == 'completed') selected @endif value="completed">Completed</option> 
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <button class="btn" type="submit">submit</button>
                                                    </div>

                                                </form>
                                            </div>
                                            <!-- </div> -->
                                        </div>
                                    </div>
                                </div>
                                <ul class="timeline timeline-inverse">


                                    @foreach ($appointments as $appointment)
                                    <!-- timeline time label -->
                                    <li class="time-label">
                                        <span class="bg-green">
                                            {{date('d-M-Y',strtotime($appointment->booking_time))}}
                                        </span>
                                    </li>
                                    <!-- /.timeline-label -->
                                    <!-- timeline item -->
                                    <li>
                                        <i class="fa fa-envelope bg-blue"></i>

                                        <div class="timeline-item">
                                            <span class="time"><i class="fa fa-clock-o"></i> {{date('h:i A',strtotime($appointment->booking_time)) .' - '.date('h:i A',strtotime($appointment->booking_end_time)) }}</span>

                                            <h3 class="timeline-header"><a href="javascript:void(0)">{{$appointment->user->name}}</a> has booked {{$appointment->type}} appointment</h3>

                                            <div class="timeline-body">
                                                @if($appointment->hospital_id)
                                                Hospital : {{$appointment->hospital->name}}
                                                @else
                                                Online Booking
                                                @endif
                                            </div>
                                            @php
                                            $btn='info';

                                            if($appointment->status=='rejected')
                                            $btn='danger';
                                            elseif ($appointment->status=='approved')
                                            $btn='primary';
                                            elseif ($appointment->status=='completed')
                                            $btn='success'; 
                                            @endphp
                                            <?php if ($appointment->type == 'online') { ?>
                                                <div class="timeline-footer">
                                                    <a class="btn btn-{{$btn}} btn-xs">{{$appointment->status}}</a>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </li>
                                    <!-- END timeline item -->
                                    <!-- timeline item -->
                                    @if($appointment->review)
                                    <li>
                                        <i class="fa fa-comments bg-yellow"></i>

                                        <div class="timeline-item">
                                            <span class="time"><i class="fa fa-clock-o"></i> {{timeago($appointment->review->created_at)}} ago</span>

                                            <h3 class="timeline-header"><a href="javascript:void(0)">{{$appointment->user->name}}</a> reviewed on your Appointment</h3>

                                            <div class="timeline-body">
                                                <b> Rating</b>: {{$appointment->review->rating}} / 5
                                                <p><b>Review</b> : {{$appointment->review->review}}</p>
                                            </div>
                                            <div class="timeline-footer">
                                            </div>
                                        </div>
                                    </li>
                                    @endif
                                    @endforeach
                                </ul>
                            </div>
                            <!-- /.tab-pane -->
                            <div class=" tab-pane" id="onlinetime">
                                <!-- Post -->
                                <form class="form-horizontal" method="POST" action="{{asset('admin/storedoctortime')}}" enctype="multipart/form-data">
                                    {{csrf_field()}} 
                                    <input type="hidden" name="id" value="{{$doctor->id}}">


                                    {{csrf_field()}} 
                                    <input type="hidden" name="id" value="{{$doctor->id}}">
                                    <label for="inputName" class="col-sm-6 text-center margin-bottom">Start Time</label>
                                    <label for="inputName" class="col-sm-4 text-center margin-bottom">End Time</label>
                                    <label for="inputName" class="col-sm-2 text-center margin-bottom">Off Day</label>
                                    @php
                                    $mydays = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday','Sunday'); 
                                    $i=0;
                                    @endphp
                                    @foreach($mydays as $day)
                                    @php
                                    $detail = $doctor->docTime->where('day',$day)->where('type','online')->first();
                                    $i++;
                                    @endphp
                                    <?php
                                    $startTime = '00:00';
                                    $endTime = '00:00';
                                    if (isset($detail->start_time) && $detail->start_time && $detail->start_time != '0:00') {
                                        $startTime = date("g:i a", strtotime($detail->start_time));
                                    }
                                    if (isset($detail->end_time)&& $detail->end_time && $detail->end_time != '0:00') {
                                        $endTime = date("g:i a", strtotime($detail->end_time));
                                    }
                                    ?>
                                    <div class="form-group">

                                        <label class="col-sm-2" for="start_time">{{$day}}:</label>
                                        <div class="col-sm-4"> 
                                            <input   @if($detail) value="{{$startTime}}" @endif type="text" class="timepicker1 form-control required_class_{{$i}}" id="start_time" aria-describedby="emailHelp" name="start_time[]" @if($detail && $detail->off_day == 0) required @endif>
                                        </div> 
                                        <div class="col-sm-4"> 
                                            <input @if($detail) value="{{$endTime}}" @endif  type="text" class="timepicker1 form-control required_class_{{$i}}" id="end_time" aria-describedby="emailHelp" name="end_time[]" @if($detail && $detail->off_day == 0) required @endif>
                                        </div>
                                        <div class="col-sm-2"> 
                                            <input @if($detail && $detail->off_day) checked @endif  value="{{$day}}" type="checkbox" class="check_box" id="{{$i}}" aria-describedby="emailHelp" name="off_day[]">
                                        </div>
                                    </div>
                                    @endforeach
                                    <div class="form-group">
                                        <div class="col-sm-10">
                                            <button type="submit" class="btn btn-primary pull-right">Submit</button>
                                        </div>
                                    </div>

                                </form>
                                <!-- /.post -->
                            </div>
                            <!--Hospital Time-->
                            <div class=" tab-pane" id="hospitaltime">
                                <!-- Post -->
                                <form class="form-horizontal" method="POST" action="{{asset('admin/storehospitaltime')}}" enctype="multipart/form-data">
                                    {{csrf_field()}} 
                                    <input type="hidden" name="id" value="{{$doctor->id}}">


                                    {{csrf_field()}} 
                                    <input type="hidden" name="id" value="{{$doctor->id}}">
                                    <label for="inputName" class="col-sm-3 text-center margin-bottom">Start Time</label>
                                    <label for="inputName" class="col-sm-3 text-center margin-bottom">End Time</label>
                                    <label for="inputName" class="col-sm-3 text-center margin-bottom">Day</label>
                                    <label for="inputName" class="col-sm-3 text-center margin-bottom">Hospital</label>
                                    <div id="all_hospital_time">
                                        @php
                                        $i=0;
                                        @endphp
                                        @foreach($doctor->doctorHospitals as $hospital_time)
                                        @php 
                                        $i++;
                                        $days=$hospital_time->times->pluck('day')->toArray();
                                        @endphp

                                        <?php
                                        $hospitalstartTime = '00:00';
                                        $hospitalendTime = '00:00';
                                        if (isset($hospital_time->times[0]) && isset($hospital_time->times[0]->start_time) && $hospital_time->times[0]->start_time && $hospital_time->times[0]->start_time != '0:00') {
                                            $hospitalstartTime = date("g:i a", strtotime($hospital_time->times[0]->start_time));
                                        }
                                        if (isset($hospital_time->times[0]) &&isset($hospital_time->times[0]->end_time) && $hospital_time->times[0]->end_time && $hospital_time->times[0]->end_time != '0:00') {
                                            $hospitalendTime = date("g:i a", strtotime($hospital_time->times[0]->end_time));
                                        }
                                        ?>
                                        <div class="form-group full_hospital_time_row">
                                            <div class="col-sm-3"> 
                                                <input value="{{$hospitalstartTime}}" type="text" class="timepicker1 form-control" id="start_time" aria-describedby="emailHelp" name="old[<?= $i ?>][start_time]">
                                                <input value="{{$hospital_time->id}}" type="hidden" class="form-control" id="old_ids" aria-describedby="emailHelp" name="old[<?= $i ?>][old_id]">
                                            </div> 
                                            <input type="hidden" name="old_id[]" value="{{$hospital_time->id}}">
                                            <div class="col-sm-3"> 
                                                <input value="{{$hospitalendTime}}" type="text" class="timepicker1 form-control" id="end_time" aria-describedby="emailHelp" name="old[<?= $i ?>][end_time]">
                                            </div>
                                            <div class="col-sm-3">

                                                <select multiple="" id="days" class="form-control select2" name="old[<?= $i ?>][days][]" style="width: 100%;">
                                                    @foreach($mydays as $day)
                                                    <option @if(in_array($day,$days)) selected @endif value="{{$day}}">{{$day}}</option> 
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-sm-2"> 
                                                <select  id="gender" class="form-control select2" name="old[<?= $i ?>][hospital]" style="width: 100%;">
                                                    @foreach($hospitals as $hospital)
                                                    <option @if($hospital->id == $hospital_time->hospital_id) selected @endif value="{{$hospital->id}}">{{$hospital->name}}</option> 
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-sm-1"> 
                                                <i class="btn fa fa-trash delete_hospital_row" onclick="deleterow(this)"></i>
                                            </div>
                                        </div> 
                                        @endforeach
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-6"> 
                                            <button type="button" onclick="addMoreElements()" class="btn btn-success pull-right">Add Hospital Timing</button>
                                        </div>
                                        <div class="col-sm-6"> 
                                            <button type="submit" class="btn btn-primary pull-right">Submit</button>
                                        </div>
                                    </div>

                                </form>
                                <!-- /.post -->
                            </div>

                            <!--Settings-->
                            <div class="tab-pane {{ (Session::get('tab2_active')=='setting') ?'active'  : ''}}" id="settings">

                                <form class="form-horizontal" method="POST" action="{{asset('admin/updatedoctor')}}" enctype="multipart/form-data">
                                    {{csrf_field()}} 
                                    <input type="hidden" name="id" value="{{$doctor->id}}">
                                    <div class="form-group">
                                        <label for="inputName" class="col-sm-2 control-label">First Name <span style="color: red">*</span></label>

                                        <div class="col-sm-10">
                                            <input value="{{$doctor->first_name}}" type="text" class="form-control" id="first_name" aria-describedby="emailHelp" name="first_name" placeholder="Enter First Name">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail" class="col-sm-2 control-label">Last Name <span style="color: red">*</span></label>

                                        <div class="col-sm-10">
                                            <input value="{{$doctor->last_name}}" type="text" class="form-control" id="last_name" aria-describedby="emailHelp" name="last_name" placeholder="Enter Last Name">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail" class="col-sm-2 control-label">Email <span style="color: red">*</span></label>

                                        <div class="col-sm-10">
                                            <input value="{{$doctor->email}}" type="text" class="form-control" id="email" aria-describedby="emailHelp" name="email" placeholder="Enter Email">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputName" class="col-sm-2 control-label">Profile Image</label>

                                        <div class="col-sm-10">
                                            <input style="width: 200px" accept="image/*" type="file" class="form-control" id="profile_image" aria-describedby="emailHelp" name="profile_image">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="inputSkills" class="col-sm-2 control-label">Gender <span style="color: red">*</span></label>

                                        <div class="col-sm-10">
                                            <select id="gender" class="form-control select2" name="gender" style="width: 100%;">

                                                <option @if($doctor->gender == 'male') selected @endif  value="male">Male</option> 
                                                <option @if($doctor->gender == 'female') selected @endif value="female">Female</option> 
                                                <option @if($doctor->gender == 'other') selected @endif value="other">Other</option> 
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputSkills" class="col-sm-2 control-label">Select City <span style="color: red">*</span></label>

                                        <div class="col-sm-10">
                                            <select id="city_id" class="form-control select2" name="city_id" style="width: 100%;">
                                                @foreach($cities as $city)
                                                <option @if($doctor->city_id == $city->id) selected @endif value="{{$city->id}}">{{$city->title}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    @php 
                                    $spc=$doctor->doctorspecility->pluck('speciality_id')->toArray();
                                    @endphp


                                    <div class="form-group">
                                        <label for="inputSkills" class="col-sm-2 control-label">Specializations <span style="color: red">*</span></label>
                                        <div class="col-sm-10">
                                            <select id="specialization" class="form-control select2" name="specialization[]" multiple="" style="width: 100%;">
                                                <?php foreach ($specializations as $specialization) {
                                                    ?>
                                                    <option @if(in_array($specialization->id,$spc)) selected @endif value="{{$specialization->id}}">{{$specialization->title}}</option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <!--                                    <div class="form-group">
                                                                            <label for="inputName" class="col-sm-2 control-label">Address <span style="color: red">*</span></label>
                                    
                                                                            <div class="col-sm-10">
                                                                                <input value="{{$doctor->location}}" type="text" class="form-control" id="location" aria-describedby="emailHelp" name="location" placeholder="Enter Address">
                                                                            </div>
                                                                        </div>-->
                                    <div class="form-group">
                                        <label for="inputName" class="col-sm-2 control-label">Phone <span style="color: red">*</span></label>

                                        <div class="col-sm-10">
                                            <input value="{{ old('phone',$doctor->phone) }}" type="text" class="form-control" id="phone" aria-describedby="emailHelp" name="phone" placeholder="+92xxxxxx">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputName" class="col-sm-2 control-label">Fee <span style="color: red">*</span></label>

                                        <div class="col-sm-10">
                                            <input step=".1" value="{{$doctor->fee}}" type="number" class="form-control" id="fee" aria-describedby="emailHelp" name="fee" placeholder="Enter Fee">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputName" class="col-sm-2 control-label">Password</label>

                                        <div class="col-sm-10">
                                            <input autocomplete="off" type="text" class="form-control" id="Password" aria-describedby="emailHelp" name="password" placeholder="Enter Password">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputExperience" class="col-sm-2 control-label">Bio <span style="color: red">*</span></label>

                                        <div class="col-sm-10">
                                            <textarea rows="4" class="form-control" id="bio" aria-describedby="emailHelp" name="bio" placeholder="Enter Bio">{{$doctor->bio}}</textarea>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-offset-2 col-sm-10">
                                            <button type="submit" class="btn btn-danger">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!--Education Time-->
                            <div class=" tab-pane" id="dreducation">
                                <!-- Post -->
                                <form class="form-horizontal" method="POST" action="{{asset('admin/storedoctoreducation')}}" enctype="multipart/form-data">
                                    {{csrf_field()}} 
                                    <input type="hidden" name="id" value="{{$doctor->id}}">


                                    {{csrf_field()}} 
                                    <input type="hidden" name="doctor_id" value="{{$doctor->id}}">
                                    <label for="inputName" class="col-sm-5 text-center margin-bottom">Institution</label>
                                    <label for="inputName" class="col-sm-5 text-center margin-bottom">Education</label>
                                    <div id="all_doctor_education_time"> 
                                        <?php
                                        $doctorEducationCount = 0;
                                        foreach ($doctor_education as $key => $doctor_edu) {
                                            ?>
                                            <div class="form-group full_doctor_time_row">
                                                <div class="col-sm-5"> 
                                                    <input value="{{$doctor_edu->institution}}" type="text" class="form-control" id="institution" aria-describedby="emailHelp" name="doctor_education[<?= $doctorEducationCount ?>][institution]">
                                                </div> 
                                                <div class="col-sm-5"> 
                                                    <input value="{{$doctor_edu->education}}" type="text" class="form-control" id="education" aria-describedby="emailHelp" name="doctor_education[<?= $doctorEducationCount ?>][education]">
                                                </div>
                                                <div class="col-sm-1"> 
                                                    <i class="btn fa fa-trash delete_doctor_row" onclick="deleterow(this)"></i>
                                                </div>
                                            </div> 
                                            <?php
                                            $doctorEducationCount++;
                                        }
                                        ?>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-6"> 
                                            <button type="button" onclick="addMoreEducation()" class="btn btn-success pull-right">Add More Education</button>
                                        </div>
                                        <div class="col-sm-6"> 
                                            <button type="submit" class="btn btn-primary pull-right">Submit</button>
                                        </div>
                                    </div>

                                </form>
                                <!-- /.post -->
                            </div>
                            <!-- /.tab-pane -->
                        </div>
                        <!-- /.tab-content -->
                    </div>
                    <!-- /.nav-tabs-custom -->
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
    var total_hospitals = 0;
    var total_doctor_education = "<?php echo $doctorEducationCount; ?>";
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
    function deleterow(ele) {
        $(ele).closest('.form-group').remove();
    }
    function addMoreElements() {
        var numItems = $('.full_hospital_time_row').length;
        console.log(numItems);
        var html = '<div class="form-group full_hospital_time_row"><div class="col-sm-3"><input type="text" class="timepicker1 form-control" id="start_time" aria-describedby="emailHelp" name="hospital[' + total_hospitals + '][start_time_new]">' +
                '</div>  <div class="col-sm-3"> <input type="text" class="timepicker1 form-control" id="end_time" aria-describedby="emailHelp" name="hospital[' + total_hospitals + '][end_time_new]">' +
                '</div> <div class="col-sm-3"><select multiple="" id="days" class="form-control select2" name="hospital[' + total_hospitals + '][days_new][]" style="width: 100%;">';
<?php foreach ($mydays as $day) { ?>
            html = html + '<option value="<?= $day ?>"><?= $day ?></option>';
<?php } ?>
        html = html + '</select> </div>  <div class="col-sm-2">   <select  id="gender" class="form-control select2" name="hospital[' + total_hospitals + '][hospital_new]" style="width: 100%;">';
<?php foreach ($hospitals as $hospital) { ?>
            html = html + '<option value="<?= $hospital->id ?>"><?= $hospital->name ?></option>';
<?php } ?>
        html = html + '</select>  </div> <div class="col-sm-1">  <i class="btn fa fa-trash delete_hospital_row" onclick="deleterow(this)"></i> </div> </div>';
        $('#all_hospital_time').append(html);
        $('.select2').select2();
        $('.timepicker1').timepicki();
        total_hospitals++;
    }


    function addMoreEducation() {
        var html = "";
        html += "<div class=\"form-group full_doctor_time_row\">";
        html += "<div class=\"col-sm-5\">";
        html += " <input value=\"\" type=\"text\" class=\"form-control\" id=\"institution\" aria-describedby=\"emailHelp\" name=\"doctor_education[" + total_doctor_education + "][institution]\">";
        html += "</div>";
        html += "<div class=\"col-sm-5\">";
        html += "<input value=\"\" type=\"text\" class=\"form-control\" id=\"education\" aria-describedby=\"emailHelp\" name=\"doctor_education[" + total_doctor_education + "][education]\">";
        html += " </div>";
        html += "<div class=\"col-sm-1\">";
        html += "<i class=\"btn fa fa-trash delete_doctor_row\" onclick=\"deleterow(this)\"></i>";
        html += "</div>";
        html += "</div>";
        $('#all_doctor_education_time').append(html);
        total_doctor_education++;
        html = "";
    }

    $('.check_box').click(function () {
        var id = $(this).attr('id');
        if ($(this).is(':checked')) {

            $('.required_class_' + id).prop('required', false);

        } else {
            $('.required_class_' + id).prop('required', true);
        }
    });

    $('#myTab a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
        // localStorage.setItem('doctor_page', $(e.target).attr("href").substr(1));

    });

// store the currently selected tab in the hash value
    $("ul.nav-tabs > li > a").on("shown.bs.tab", function (e) {
        var id = $(e.target).attr("href").substr(1);
        window.location.hash = id;
    });

// on load of the page: switch to the currently selected tab
    var hash = window.location.hash;
//    if (!hash) {
//        if (localStorage.getItem('doctor_page')) {
//            
//            hash = '#'+localStorage.getItem('doctor_page');
//        } else {
//            hash = '#appointments';
//        }
//
//    }
    $('#myTab a[href="' + hash + '"]').tab('show');

    $('.timepicker1').timepicki();
</script>
@endsection