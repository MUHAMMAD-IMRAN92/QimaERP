 @php $segment = Request::segment(2); @endphp
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>QIMA | Dashboard</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{asset('public/plugins/fontawesome-free/css/all.min.css')}}">
  <!-- Ionicons -->
  <script src="{{asset('public/plugins/jquery/jquery.min.js')}}"></script>
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bbootstrap 4 -->
  <link rel="stylesheet" href="{{asset('public/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')}}">
  <!-- iCheck -->
  <link rel="stylesheet" href="{{asset('public/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
  <!-- JQVMap -->
  {{-- <link rel="stylesheet" href="{{asset('public/plugins/jqvmap/jqvmap.min.css')}}"> --}}
  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('public/dist/css/adminlte.min.css')}}">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="{{asset('public/plugins/overlayScrollbars/css/OverlayScrollbars.min.css')}}">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="{{asset('public/plugins/daterangepicker/daterangepicker.css')}}">
  <!-- summernote -->
  <link rel="stylesheet" href="{{asset('public/plugins/summernote/summernote-bs4.css')}}">
  <link rel="stylesheet" href="{{asset('public/css/custom.css')}}">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="{{asset('public/plugins/select2/css/select2.min.css')}}" />
  
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      
    

      
    </ul>
    <ul class="navbar-nav ml-auto">
     <li class="nav-item dropdown ">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          {{ucfirst(Auth::User()->first_name)}}
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href="{{url('admin/resetpassword')}}/{{Auth::User()->user_id}}">Reset Password</a>
          <div class="dropdown-divider"></div>
           <a href="{{URL::to('')}}/admin/logout" class="nav-link">Logout</a>
        </div>
      </li>
    </ul>
   
  </nav>
  <!-- /.navbar -->
</div>
  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
<!--    <a href="index3.html" class="brand-link">
      <img src="{{URL::to('')}}/public/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
           style="opacity: .8">
    </a>-->

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="{{URL::to('')}}/public/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">QIMA</a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
       @hasrole('Super Admin')
 <li class="nav-item"  @if($segment == 'dashboard') class="active" @endif>
             <a href="{{URL::to('')}}/admin/dashboard" class="nav-link ">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
                
              </p>
             </a>
          	</li>  
            <li class="nav-item has-treeview {{ (request()->segment(1) == 'allusers' ) ? 'menu-open' : '' }}">
                                <a href="{{url('admin/allusers')}}" class="nav-link {{ (request()->segment(1) == 'allusers') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-user"></i>
                                    <p>
                                        Users Settings
                                        <i class="fas fa-angle-left right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview pl-2 nav-dropdown">
                                    <li class="nav-item">
                                        <a href="{{url('admin/allusers')}}" class="nav-link {{ (request()->segment(1) == 'allusers') ? 'active' : '' }}">
                                            <i class="fas fa-long-arrow-alt-right nav-icon"></i>
                                            <p>Users</p>
                                        </a>
                                    </li>
                                    
                                </ul>
                            </li>
          	 <li class="nav-item"  @if($segment == 'allgovernor' || $segment == 'editgovernor') class="active" @endif>
             <a href="{{URL::to('')}}/admin/allgovernor" class="nav-link ">
              <i class="nav-icon fas fa-user-alt"></i>
              <p>
                Governor
                
              </p>
             </a>
          	</li>  
            
@else

@endhasrole
         <li class="nav-item"  @if($segment == 'allregion') class="active" @endif>
             <a href="{{URL::to('')}}/admin/allregion" class="nav-link ">
              <i class="nav-icon fas fa-globe"></i>
              <p>
                Region
                
              </p>
             </a>
            </li>   
            <li class="nav-item"   @if($segment == 'allvillage')class="active" @endif>
             <a href="{{URL::to('')}}/admin/allvillage" class="nav-link ">
              <i class="nav-icon fas fa-tree"></i>
              <p>
                Village
                
              </p>
             </a>
            </li> 
             <li class="nav-item"   @if($segment == 'allfarmer')class="active" @endif>
             <a href="{{URL::to('')}}/admin/allfarmer" class="nav-link ">
              <i class="nav-icon fas fa-tractor"></i>
              <p>
                Farmer
                
              </p>
             </a>
            </li> 
            @hasrole('Super Admin')
<li class="nav-item"   @if($segment == 'allbatchnumber')class="active" @endif>
             <a href="{{URL::to('')}}/admin/allbatchnumber" class="nav-link ">
             <i class="nav-icon fas fa-sort-numeric-up-alt"></i>
              <p>
                Batch Number
                
              </p>
             </a>
            </li> 
            <li class="nav-item"   @if($segment == 'allcenter')class="active" @endif>
             <a href="{{URL::to('')}}/admin/allcenter" class="nav-link ">
             <i class="nav-icon fab fa-centercode"></i>
              <p>
               Center
                <i class="fas fa-angle-left right"></i>
              </p>
             </a>
             <ul class="nav nav-treeview pl-2 nav-dropdown">
                                    <li class="nav-item">
                                        <a href="{{url('admin/allcenter')}}" class="nav-link {{ (request()->segment(1) == 'allcenter') ? 'active' : '' }}">
                                            <i class="fas fa-long-arrow-alt-right nav-icon"></i>
                                            <p>All Center</p>
                                        </a>
                                    </li>
                                  {{--   <li class="nav-item">
                                        <a href="{{url('admin/centerdetail')}}" class="nav-link {{ (request()->segment(1) == 'centerdetail') ? 'active' : '' }}">
                                            <i class="fas fa-long-arrow-alt-right nav-icon"></i>
                                            <p>Center Detail</p>
                                        </a>
                                    </li> --}}
                                    
                                </ul>
            </li>
            <li class="nav-item"   @if($segment == 'alltransection')class="active" @endif>
             <a href="{{URL::to('')}}/admin/alltransection" class="nav-link ">
             <i class="nav-icon fas fa-exchange-alt"></i>
              <p>
               Transaction
                
              </p>
             </a>
            
            <li class="nav-item"   @if($segment == 'addcontainer'|| $segment == 'allcontainer')class="active" @endif>
             <a href="{{URL::to('')}}/admin/allcontainer" class="nav-link ">
             <i class="nav-icon fas fa-shopping-basket"></i>
              <p>
               Container
                
              </p>
             </a>
            </li>

            <li class="nav-item"   @if($segment == 'addseason')class="active" @endif>
             <a href="{{URL::to('')}}/admin/allseason" class="nav-link ">
              <i class="fal fa-trees"></i>
             <i class="nav-icon fas fa-tree"></i>
              <p>
               Season
                
              </p>
             </a>
            </li>
            <li class="nav-item"   @if($segment == 'governorweight')class="active" @endif>
             <a href="{{URL::to('')}}/admin/governorweight" class="nav-link ">
              <i class="fal fa-trees"></i>
             <i class="nav-icon fas fa-weight"></i>
              <p>
               Total Weight
                
              </p>
             </a>
            </li>
@else
    
@endhasrole
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>














































































































