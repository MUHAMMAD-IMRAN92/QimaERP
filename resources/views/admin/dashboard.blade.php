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
            <div class="row">
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box  box-styles">
                        <span class="info-box-icon hompepage-icons bg-clr-main  bg-aqua"><i class="fa fa-user-md" ></i></span>

                        <div class="info-box-content  main-page">
                            <span class="info-box-text">Total Doctors</span>
                            <span class="info-box-number">{{number_format($total_doc)}}</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box box-styles">
                        <span class="info-box-icon hompepage-icons bg-red"><i class="fa fa-user-o"></i></span>

                        <div class="info-box-content  main-page">
                            <span class="info-box-text">Total Users</span>
                            <span class="info-box-number">{{number_format($total_users)}}</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->

                <!-- fix for small devices only -->
                <div class="clearfix visible-sm-block"></div>

                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box  box-styles">
                        <span class="info-box-icon hompepage-icons bg-green"><i class="fa fa-dollar"></i></span>

                        <div class="info-box-content  main-page">
                            <span class="info-box-text">Total Income</span>
                            <span class="info-box-number">{{number_format($total_income)}} PKR</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box  box-styles">
                        <span class="info-box-icon hompepage-icons bg-yellow"><i class="fa fa-hospital-o"></i></span>

                        <div class="info-box-content main-page">
                            <span class="info-box-text">Total Hospitals</span>
                            <span class="info-box-number">{{number_format($total_hospitals)}}</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->

            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Last 7 Days Appointments</h3>

                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>

                                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <p class="text-center">
                                        <strong>Appointments: {{date('d M, Y', strtotime($graphstatDate))}} - {{date('d M, Y', strtotime($graphEndDate))}}</strong>
                                    </p>

                                    <div class="chart">
                                        <!-- Sales Chart Canvas -->
                                        <canvas id="salesChart" style="height: 180px;"></canvas>
                                    </div>
                                    <!-- /.chart-responsive -->
                                </div>
                                <!-- /.col -->
                                <div class="col-md-4">
                                    <p class="text-center">
                                        <strong>Appointments</strong>
                                    </p>
                                    @php
                                    $pending_width=0;
                                    $rejected_width=0;
                                    $completed_width=0;
                                    $approved_width=0;
                                    if($pending_appointments){
                                    $pending_width=round(($pending_appointments/$total_appointments )* 100);
                                    }
                                    if($rejected_appointments){
                                    $rejected_width=round(($rejected_appointments/$total_appointments )* 100);
                                    }
                                    if($completed_appointments){
                                    $completed_width=round(($completed_appointments/$total_appointments )* 100);
                                    }
                                    if($approved_appointments){
                                    $approved_width=round(($approved_appointments/$total_appointments )* 100);
                                    }
                                    @endphp
                                    <div class="progress-group">
                                        <span class="progress-text">Pending</span>
                                        <span class="progress-number"><b>{{$pending_appointments}}</b>/{{$total_appointments}}</span>

                                        <div class="progress sm">
                                            <div class="progress-bar progress-bar-aqua" style="width: {{$pending_width}}%"></div>
                                        </div>
                                    </div>
                                    <!-- /.progress-group -->
                                    <div class="progress-group">
                                        <span class="progress-text">Rejected</span>
                                        <span class="progress-number"><b>{{$rejected_appointments}}</b>/{{$total_appointments}}</span>

                                        <div class="progress sm">
                                            <div class="progress-bar progress-bar-red" style="width: {{$rejected_width}}%"></div>
                                        </div>
                                    </div>
                                    <!-- /.progress-group -->
                                    <div class="progress-group">
                                        <span class="progress-text">Completed</span>
                                        <span class="progress-number"><b>{{$completed_appointments}}</b>/{{$total_appointments}}</span>

                                        <div class="progress sm">
                                            <div class="progress-bar progress-bar-green" style="width: {{$completed_width}}%"></div>
                                        </div>
                                    </div>
                                    <!-- /.progress-group -->
                                    <div class="progress-group">
                                        <span class="progress-text">Approved</span>
                                        <span class="progress-number"><b>{{$approved_appointments}}</b>/{{$total_appointments}}</span>

                                        <div class="progress sm">
                                            <div class="progress-bar progress-bar-yellow" style="width: {{$approved_width}}%"></div>
                                        </div>
                                    </div>
                                    <!-- /.progress-group -->
                                </div>
                                <!-- /.col -->
                            </div>
                            <!-- /.row -->
                        </div>

                    </div>
                    <!-- /.box -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->

            <!-- Main row -->
            <div class="row">
                <!-- Left col -->
                <div class="col-md-12">
                    <!-- MAP & BOX PANE -->
                    <div class="box box-success">
                        <div class="box-header with-border">
                            <h3 class="box-title">Hospitals</h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body no-padding">
                            <div class="row">
                                <div class="col-md-9 col-sm-8">
                                    <div class="pad">
                                        <!-- Map will be created here -->

                                        <div id="map" style="height: 325px;"></div>
                                    </div>
                                </div>
                                <!-- /.col -->
                                <div class="col-md-3 col-sm-4">
                                    <div class="pad box-pane-right bg-green" >
                                        <div class="description-block margin-bottom">
                                            
                                         <span class=""><i class="fa fa-user-md doctor_logo_font"></i></span>
                                            <h5 class="description-header">{{number_format($total_doc)}}</h5>
                                            <span class="description-text">Doctors</span>
                                        </div>
                                        <!-- /.description-block -->
                                        <!--                    <div class="description-block margin-bottom">
                                                              <div class="sparkbar pad" data-color="#fff">90,50,90,70,61,83,63</div>
                                                              <h5 class="description-header">30%</h5>
                                                              <span class="description-text">Referrals</span>
                                                            </div>-->
                                        <!-- /.description-block -->
                                        <!--                    <div class="description-block">
                                                              <div class="sparkbar pad" data-color="#fff">90,50,90,70,61,83,63</div>
                                                              <h5 class="description-header">70%</h5>
                                                              <span class="description-text">Organic</span>
                                                            </div>-->
                                        <!-- /.description-block -->
                                    </div>
                                </div>
                                <!-- /.col -->
                            </div>
                            <!-- /.row -->
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->

                    <div class="row">
                        <div class="col-md-6">
                            <!-- USERS LIST -->
                            <div class="box box-danger">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Latest Doctor</h3>

                                    <div class="box-tools pull-right">
                                        {{-- <span class="label label-danger">8 New Members</span> --}}
                                        {{-- <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                    </button> --}}
                                    </div>
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body no-padding">
                                    <ul class="users-list clearfix">
                                        @foreach($doctors as $doctor)
                                        <a class="users-list-name" href="{{ url('/admin/doctordetail/'.$doctor->id) }}">
                                        <li>
                                            <img class="profile-pic2" src="{{getDocImg($doctor)}}" alt="User Image">
                                            <a class="users-list-name" href="{{ url('/admin/doctordetail/'.$doctor->id) }}">{{$doctor->first_name}} {{$doctor->last_name}}</a>
                                        </li>
                                        </a>
                                        @endforeach

                                    </ul>
                                    <!-- /.users-list -->
                                </div>
                                <!-- /.box-body -->
                                {{--  <div class="box-footer text-center">
                  <a href="javascript:void(0)" class="uppercase">View All Users</a>
                </div> --}}
                                <!-- /.box-footer -->
                            </div>
                            <!--/.box -->
                        </div>
                        <!-- /.col -->

                        <div class="col-md-6">
                            <!-- USERS LIST -->
                            <div class="box box-danger">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Latest User</h3>

                                    <div class="box-tools pull-right">
                                        {{-- <span class="label label-danger">8 New Members</span> --}}
                                        {{-- <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                    </button> --}}
                                    </div>
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body no-padding">
                                    <ul class="users-list clearfix">
                                        @foreach($users as $user)
                                        
                                        <a class="users-list-name" href="{{ url('admin/userdetail/'.$user->id) }}"> <li>
                                            <img class="profile-pic2"  src="{{asset('admin_assets/dist/img/user1-128x128.jpg')}}" alt="User Image">
                                            <a class="users-list-name" href="{{ url('admin/userdetail/'.$user->id) }}">{{$user->name}}</a>
                                        </li>
                                    </a>
                                        @endforeach
                                    </ul>
                                    <!-- /.users-list -->
                                </div>
                                <!-- /.box-body -->
                                {{-- <div class="box-footer text-center">
                  <a href="javascript:void(0)" class="uppercase">View All Users</a>
                </div> --}}
                                <!-- /.box-footer -->
                            </div>
                            <!--/.box -->
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->

                    <!-- TABLE: LATEST ORDERS -->
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <h3 class="box-title">Latest Appointments</h3>

                            {{-- <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div> --}}
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="table-responsive">
                                <table class="table no-margin">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Type</th>
                                            <th>Status</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 0;
                                        foreach ($appointments as $appointment) {
                                            ?>
                                            <tr>
                                                <td><a href="#">{{$appointment->id}}</a></td>
                                                <td>{{$appointment->type}}</td>
                                                @if($appointment->status == "completed")
                                                <td><span class="label label-info">{{$appointment->status}}</span></td>
                                                @elseif($appointment->status == "pending")
                                                <td><span class="label label-warning">{{$appointment->status}}</span></td>
                                                @elseif($appointment->status == "rejected")
                                                <td><span class="label label-danger">{{$appointment->status}}</span></td>
                                                @elseif($appointment->status == "approved")
                                                <td><span class="label label-success">{{$appointment->status}}</span></td>
                                                @endif

                                            </tr>
                                            <?php
                                            $i++;
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                <?php                             
                                if ($i == 10) {?>
                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-block btn-primary"><a class="users-list-name" href="{{ url('admin/appointments') }}">More</a></button>
                                    </div> 
                                <?php } ?>

                                {{-- <table class="table no-margin">
                  <thead>
                  <tr>
                    <th>Order ID</th>
                    <th>Item</th>
                    <th>Status</th>
                    <th>Popularity</th>
                  </tr>
                  </thead>
                  <tbody>
                  <tr>
                    <td><a href="pages/examples/invoice.html">OR9842</a></td>
                    <td>Call of Duty IV</td>
                    <td><span class="label label-success">Shipped</span></td>
                    <td>
                      <div class="sparkbar" data-color="#00a65a" data-height="20">90,80,90,-70,61,-83,63</div>
                    </td>
                  </tr>
                  <tr>
                    <td><a href="pages/examples/invoice.html">OR1848</a></td>
                    <td>Samsung Smart TV</td>
                    <td><span class="label label-warning">Pending</span></td>
                    <td>
                      <div class="sparkbar" data-color="#f39c12" data-height="20">90,80,-90,70,61,-83,68</div>
                    </td>
                  </tr>
                  <tr>
                    <td><a href="pages/examples/invoice.html">OR7429</a></td>
                    <td>iPhone 6 Plus</td>
                    <td><span class="label label-danger">Delivered</span></td>
                    <td>
                      <div class="sparkbar" data-color="#f56954" data-height="20">90,-80,90,70,-61,83,63</div>
                    </td>
                  </tr>
                  <tr>
                    <td><a href="pages/examples/invoice.html">OR7429</a></td>
                    <td>Samsung Smart TV</td>
                    <td><span class="label label-info">Processing</span></td>
                    <td>
                      <div class="sparkbar" data-color="#00c0ef" data-height="20">90,80,-90,70,-61,83,63</div>
                    </td>
                  </tr>
                  <tr>
                    <td><a href="pages/examples/invoice.html">OR1848</a></td>
                    <td>Samsung Smart TV</td>
                    <td><span class="label label-warning">Pending</span></td>
                    <td>
                      <div class="sparkbar" data-color="#f39c12" data-height="20">90,80,-90,70,61,-83,68</div>
                    </td>
                  </tr>
                  <tr>
                    <td><a href="pages/examples/invoice.html">OR7429</a></td>
                    <td>iPhone 6 Plus</td>
                    <td><span class="label label-danger">Delivered</span></td>
                    <td>
                      <div class="sparkbar" data-color="#f56954" data-height="20">90,-80,90,70,-61,83,63</div>
                    </td>
                  </tr>
                  <tr>
                    <td><a href="pages/examples/invoice.html">OR9842</a></td>
                    <td>Call of Duty IV</td>
                    <td><span class="label label-success">Shipped</span></td>
                    <td>
                      <div class="sparkbar" data-color="#00a65a" data-height="20">90,80,90,-70,61,-83,63</div>
                    </td>
                  </tr>
                  </tbody>
                </table> --}}
                            </div>
                            <!-- /.table-responsive -->
                        </div>
                        <!-- /.box-body -->
                        {{-- <div class="box-footer clearfix">
              <a href="javascript:void(0)" class="btn btn-sm btn-info btn-flat pull-left">Place New Order</a>
              <a href="javascript:void(0)" class="btn btn-sm btn-default btn-flat pull-right">View All Orders</a>
            </div> --}}
                        <!-- /.box-footer -->
                    </div>
                    <!-- /.box -->
                </div>
                <!-- /.col -->


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
    $(function () {

        'use strict';

        /* ChartJS
         * -------
         * Here we will create a few charts using ChartJS
         */

        // -----------------------
        // - MONTHLY SALES CHART -
        // -----------------------

        // Get context with jQuery - using jQuery's .get() method.
        var salesChartCanvas = $('#salesChart').get(0).getContext('2d');
        // This will get the first returned node in the jQuery collection.
        var salesChart = new Chart(salesChartCanvas);
        var bool = <?php echo($appointmentdateArray) ?>


        var salesChartData = {
            labels: bool,
            datasets: [
                {
                    label: 'Digital Goods',
                    fillColor: 'rgba(60,141,188,0.9)',
                    strokeColor: 'rgba(60,141,188,0.8)',
                    pointColor: '#3b8bba',
                    pointStrokeColor: 'rgba(60,141,188,1)',
                    pointHighlightFill: '#fff',
                    pointHighlightStroke: 'rgba(60,141,188,1)',
                    data: <?php echo($appointmentcountArray) ?>
                }
            ]
        };

        var salesChartOptions = {
            // Boolean - If we should show the scale at all
            showScale: true,
            // Boolean - Whether grid lines are shown across the chart
            scaleShowGridLines: false,
            // String - Colour of the grid lines
            scaleGridLineColor: 'rgba(0,0,0,.05)',
            // Number - Width of the grid lines
            scaleGridLineWidth: 1,
            // Boolean - Whether to show horizontal lines (except X axis)
            scaleShowHorizontalLines: true,
            // Boolean - Whether to show vertical lines (except Y axis)
            scaleShowVerticalLines: true,
            // Boolean - Whether the line is curved between points
            bezierCurve: true,
            // Number - Tension of the bezier curve between points
            bezierCurveTension: 0.3,
            // Boolean - Whether to show a dot for each point
            pointDot: false,
            // Number - Radius of each point dot in pixels
            pointDotRadius: 4,
            // Number - Pixel width of point dot stroke
            pointDotStrokeWidth: 1,
            // Number - amount extra to add to the radius to cater for hit detection outside the drawn point
            pointHitDetectionRadius: 20,
            // Boolean - Whether to show a stroke for datasets
            datasetStroke: true,
            // Number - Pixel width of dataset stroke
            datasetStrokeWidth: 2,
            // Boolean - Whether to fill the dataset with a color
            datasetFill: true,
            // String - A legend template
            legendTemplate: '<ul class=\'<%=name.toLowerCase()%>-legend\'><% for (var i=0; i<datasets.length; i++){%><li><span style=\'background-color:<%=datasets[i].lineColor%>\'></span><%=datasets[i].label%></li><%}%></ul>',
            // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
            maintainAspectRatio: true,
            // Boolean - whether to make the chart responsive to window resizing
            responsive: true
        };

        // Create the line chart
        salesChart.Line(salesChartData, salesChartOptions);

        // ---------------------------
        // - END MONTHLY SALES CHART -
        // ---------------------------
        // var lat = <?php echo($latLng) ?>;
//    var jsonObject= jQuery.parseJSON($latLng);
//    console.log(jsonObject);
        $('#world-map-markers').vectorMap({
            map: 'world_mill_en',
            normalizeFunction: 'polynomial',
            hoverOpacity: 0.7,
            hoverColor: true,
            backgroundColor: 'transparent',
            regionStyle: {
                initial: {
                    fill: 'rgba(210, 214, 222, 1)',
                    'fill-opacity': 1,
                    stroke: 'none',
                    'stroke-width': 0,
                    'stroke-opacity': 1
                },
                hover: {
                    'fill-opacity': 0.7,
                    cursor: 'pointer'
                },
                selected: {
                    fill: 'yellow'
                },
                selectedHover: {}
            },
            markerStyle: {
                initial: {
                    fill: '#00a65a',
                    stroke: '#111',
                }
            },
            markers: <?php echo($latLng) ?>


//       { latLng: [43.73, 7.41], name: 'Monaco' },
//       { latLng: [-0.52, 166.93], name: 'Nauru' },

        });


        /* SPARKLINE CHARTS
         * ----------------
         * Create a inline charts with spark line
         */

        // -----------------
        // - SPARKLINE BAR -
        // -----------------
        $('.sparkbar').each(function () {
            var $this = $(this);
            $this.sparkline('html', {
                type: 'bar',
                height: $this.data('height') ? $this.data('height') : '30',
                barColor: $this.data('color')
            });
        });

        // -----------------
        // - SPARKLINE PIE -
        // -----------------
        $('.sparkpie').each(function () {
            var $this = $(this);
            $this.sparkline('html', {
                type: 'pie',
                height: $this.data('height') ? $this.data('height') : '90',
                sliceColors: $this.data('color')
            });
        });

        // ------------------
        // - SPARKLINE LINE -
        // ------------------
        $('.sparkline').each(function () {
            var $this = $(this);
            $this.sparkline('html', {
                type: 'line',
                height: $this.data('height') ? $this.data('height') : '90',
                width: '100%',
                lineColor: $this.data('linecolor'),
                fillColor: $this.data('fillcolor'),
                spotColor: $this.data('spotcolor')
            });
        });
    });
</script>
<!--<script src="http://maps.google.com/maps/api/js?sensor=false" 
type="text/javascript"></script>-->


<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyALhVLV8VPUoHqK9OzUzRzvhuPsmtuLJIY&sensor=false&callback=initialize" 
type="text/javascript"></script>
<script type="text/javascript">
    var locations = <?php echo $latLng?>

    var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 5,
        center: new google.maps.LatLng(31.5204, 74.3587),
        mapTypeId: google.maps.MapTypeId.ROADMAP
    });

    var infowindow = new google.maps.InfoWindow();

    var marker, i;

    for (i = 0; i < locations.length; i++) {
        marker = new google.maps.Marker({
            position: new google.maps.LatLng(locations[i][1], locations[i][2]),
            map: map
        });

        google.maps.event.addListener(marker, 'click', (function (marker, i) {
            return function () {
                infowindow.setContent(locations[i][0]);
                infowindow.open(map, marker);
            }
        })(marker, i));
    }
</script>
@endsection
