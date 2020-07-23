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
            @include('admin_layout.messages')
            <div class="row">
                <div class="col-md-3">

                    <!-- Profile Image -->
                    <div class="box box-primary">
                        <div class="box-body box-profile">
                            <img class="profile-user-img img-responsive profile-pic   img-circle" src="<?= getUserImg($user) ?>" alt="User profile picture">

                            <h3 class="profile-username text-center">{{$user->name}}</h3>

                            <p class="text-muted text-center">User</p>

                            <ul class="list-group list-group-unbordered">
                                <li class="list-group-item">
                                    <b>Total Booking</b> <a class="pull-right">{{$user->bookings->count()}}</a>
                                </li>

                                <li class="list-group-item">
                                    <b>Total Online Booking</b> <a class="pull-right">{{$user->onlineBooking->count()}}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Total Hospital Booking</b> <a class="pull-right">{{$user->hospitalBooking->count()}}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Total Completed Booking</b> <a class="pull-right">{{$user->bookings->where('status','completed')->count()}}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Total Pending Booking</b> <a class="pull-right">{{$user->bookings->where('status','pending')->count()}}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Total Approved Booking</b> <a class="pull-right">{{$user->bookings->where('status','approved')->count()}}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Total Rejected Booking</b> <a class="pull-right">{{$user->bookings->where('status','rejected')->count()}}</a>
                                </li>

                            </ul>

                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->


                </div>
                <!-- /.col -->
                <div class="col-md-9">
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#appointments" data-toggle="tab">Appointments</a></li>
                            <li><a href="#settings" data-toggle="tab">Settings</a></li>
                        </ul>
                        <div class="tab-content">
                            <!--Appointments-->
                            <!-- /.tab-pane -->
                            <div class="active tab-pane" id="appointments">
                                <!-- The timeline -->
                                <ul class="timeline timeline-inverse">
                                    @foreach ($appointments as $appointment)
                                    <!-- timeline time label -->
                                    <li class="time-label">
                                        <span class="bg-green">
                                            {{date('d-m-Y',strtotime($appointment->booking_time))}}
                                        </span>
                                    </li>
                                    <!-- /.timeline-label -->
                                    <!-- timeline item -->
                                    <li>
                                        <i class="fa fa-envelope bg-blue"></i>

                                        <div class="timeline-item">
                                            <span class="time"><i class="fa fa-clock-o"></i> {{date('h:i A',strtotime($appointment->booking_time))}}</span>

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
                                            <div class="timeline-footer">
                                                <a class="btn btn-{{$btn}} btn-xs">{{$appointment->status}}</a>
                                            </div>
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
                            <!--Settings-->
                            <div class="tab-pane" id="settings">

                                <form class="form-horizontal" method="POST" action="{{asset('admin/updateuser/'.$user->id)}}" enctype="multipart/form-data">
                                    {{csrf_field()}}                   
                                    <div class="form-group">
                                        <label for="inputName" class="col-sm-2 control-label">Name</label>
                                        <div class="col-sm-10">
                                            <input value="{{ old('name',$user->name) }}" type="text" class="form-control" id="name" aria-describedby="emailHelp" name="name" placeholder="Enter Name" readonly>
                                            @if ($errors->has('name'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('name') }}</strong>
                                            </span>
                                            @endif
                                        </div>

                                    </div>
                                    <div class="form-group">
                                        <label for="inputName" class="col-sm-2 control-label">Phone</label>
                                        <div class="col-sm-10">
                                            <input value="{{ old('phone',$user->phone_number) }}" type="text" class="form-control" id="phone" aria-describedby="emailHelp" name="phone" placeholder="+92xxxx" readonly>
                                            @if ($errors->has('phone'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('phone') }}</strong>
                                            </span>
                                            @endif
                                        </div>

                                    </div>
                                    <div class="form-group">
                                        <label for="inputName" class="col-sm-2 control-label">Profile Image</label>

                                        <div class="col-sm-10">
                                            <input style="width: 200px" accept="image/*" type="file" class="form-control" id="profile_image" aria-describedby="emailHelp" name="profile_image" readonly>
                                        </div>
                                    </div>



                                    <div class="form-group">
                                        <div class="col-sm-offset-2 col-sm-10">
                                            <button type="submit" class="btn btn-danger">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
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

</script>
@endsection