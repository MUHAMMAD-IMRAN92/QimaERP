<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>My E-Clinic | <?= $title ?></title>
        <link rel="shortcut icon" href="{{url('/admin_assets/favicon.png')}}" type="image/x-icon">
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

        <!-- Bootstrap 3.3.7 -->
        <link rel="stylesheet" href="{{asset('admin_assets/bower_components/bootstrap/dist/css/bootstrap.min.css')}}">
        <!-- Bootstrap 3.3.7 -->
        <link rel="stylesheet" href="{{asset('admin_assets/bower_components/bootstrap/dist/css/bootstrap.min.css')}}">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="{{asset('admin_assets/bower_components/font-awesome/css/font-awesome.min.css')}}">
        <!-- Ionicons -->
        <link rel="stylesheet" href="{{asset('admin_assets/bower_components/Ionicons/css/ionicons.min.css')}}">
        <!-- jvectormap -->
        <link rel="stylesheet" href="{{asset('/admin_assets/bower_components/jvectormap/jquery-jvectormap.css')}}">
        <link rel="stylesheet" href="{{asset('/admin_assets/bower_components/morris/morris.css')}}">
        <!-- Theme style -->
        <link rel="stylesheet" href="{{asset('admin_assets/dist/css/custom.css')}}">
        <!-- custom css -->
        <link rel="stylesheet" href="{{asset('admin_assets/dist/css/AdminLTE.min.css')}}">
        <!-- AdminLTE Skins. Choose a skin from the css/skins

             folder instead of downloading all of them to reduce the load. -->
        <link rel="stylesheet" href="{{asset('admin_assets/dist/css/skins/_all-skins.min.css')}}">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.1/css/responsive.dataTables.min.css">
        <!-- Select2 -->
        <link rel="stylesheet" href="{{asset('admin_assets/bower_components/select2/dist/css/select2.min.css')}}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/css/bootstrap.min.css">
        <link rel="stylesheet" href="{{asset('admin_assets/dist/css/bootstrap-tagsinput.css')}}">
        <link rel="stylesheet" href="{{asset('admin_assets/dist/css/timepicki.css')}}">
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

        <!-- Google Font -->
        <link rel="stylesheet"
              href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    </head>
</head>

@yield('page_css')
</head>
<body class="hold-transition skin-blue sidebar-mini">

    @yield('content')

    <!-- jQuery 3 -->
    <script src="{{ asset('admin_assets/bower_components/jquery/dist/jquery.min.js')}}"></script>
    <!-- Bootstrap 3.3.7 -->
    <script src="{{ asset('admin_assets/bower_components/bootstrap/dist/js/bootstrap.min.js')}}"></script>
    <!-- FastClick -->
    <script src="{{ asset('admin_assets/bower_components/fastclick/lib/fastclick.js')}}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('admin_assets/dist/js/adminlte.min.js')}}"></script>
    <!-- Sparkline -->
    <script src="{{ asset('admin_assets/bower_components/jquery-sparkline/dist/jquery.sparkline.min.js')}}"></script>
    <!-- jvectormap  -->
    <script src="{{ asset('admin_assets/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js')}}"></script>
    <script src="{{ asset('admin_assets/plugins/jvectormap/jquery-jvectormap-world-mill-en.js')}}"></script>
    <!-- SlimScroll -->
    <script src="{{ asset('admin_assets/bower_components/jquery-slimscroll/jquery.slimscroll.min.js')}}"></script>
    <!-- ChartJS -->
    <script src="{{ asset('admin_assets/bower_components/chart.js/Chart.js')}}"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.1/js/dataTables.responsive.min.js"></script>
    <script src="{{ asset('admin_assets/bower_components/select2/dist/js/select2.full.min.js')}}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script src="{{ asset('admin_assets/dist/js/bootstrap-tagsinput.min.js')}}"></script>
    <script src="{{ asset('admin_assets/dist/js/timepicki.js')}}"></script>

    <script>
$(function () {
//Initialize Select2 Elements
    $('.select2').select2()
});
    </script>
    <script type="text/javascript">
        $('#hospital_form').on('keyup keypress', function (e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                return false;
            }
        });

        $('#facilitates_check').on('beforeItemAdd', function (event) {
            // check item contents
            if (!/^[a-zA-Z,]+$/.test(event.item)) {
                // set to true to prevent the item getting added
                event.cancel = true;
            }
        });
        
         
    </script>
    @yield('page_js')
</body>
</html>
