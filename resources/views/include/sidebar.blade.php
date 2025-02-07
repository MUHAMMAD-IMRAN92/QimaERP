<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>QIMA | Dashboard</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Font Awesome -->
    <script src="{{ asset('public/plugins/jquery/jquery.min.js') }}"></script>
    {{-- <script src="{{ asset('public/dist/js/jquery-jvectormap-2.0.5.min.js') }}"></script> --}}
    {{-- <link rel="stylesheet" href="{{asset('public/plugins/jqvmap/jqvmap.min.css')}}"> --}}


    <link rel="stylesheet" href="{{ asset('public/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Ionicons -->

    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bbootstrap 4 -->
    <link rel="stylesheet"
        href="{{ asset('public/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ asset('public/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- JQVMap -->
    <link rel="stylesheet" href="{{ asset('public/plugins/jqvmap/jqvmap.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('public/dist/css/adminlte.min.css') }}">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ asset('public/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="{{ asset('public/plugins/daterangepicker/daterangepicker.css') }}">
    <!-- summernote -->
    <link rel="stylesheet" href="{{ asset('public/plugins/summernote/summernote-bs4.css') }}">
    <link rel="stylesheet" href="{{ url('public/css/custom.css') }}">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="{{ asset('public/plugins/select2/css/select2.min.css') }}" />

    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <link href="{{ asset('public/css/theme-sugar.css') }}" rel="stylesheet">
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item menu-icon">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i
                            class="fas fa-bars"></i></a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown ">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        {{ ucfirst(Auth::User()->first_name) }}
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item"
                            href="{{ url('admin/resetpassword') }}/{{ Auth::User()->user_id }}">Reset Password</a>
                        <div class="dropdown-divider"></div>
                        <a href="{{ URL::to('') }}/admin/logout" class="nav-link">Logout</a>
                    </div>
                </li>
            </ul>

        </nav>
        <!-- /.navbar -->
    </div>
    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-lightGray elevation-4 custom-sidebar">

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar user panel (optional) -->
            <!-- <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img src="{{ URL::to('') }}/public/dist/img/qima_logo.png" class="img-circle elevation-2"
                        alt="User Image">
                </div>
                <div class="info">
                    <a href="/" class="d-block">QIMA</a>
                </div>
            </div> -->
            <div class="user-panel d-flex flex-column">
                <div class=" text-center mt-5">
                    <img style="width: auto; max-width: 100%;" src="{{ URL::to('') }}/public/dist/img/qima_logo.png"
                        alt="User Image">
                </div>
                <div class="notification-icons d-flex  mt-5 mb-5 justify-content-center">
                    <img style="width: auto;" src="{{ URL::to('') }}/public/dist/img/message.png" alt="User Image">
                    <div class="vl-sidebar mx-4"></div>
                    <img style="width: auto;" src="{{ URL::to('') }}/public/dist/img/flag_1.png" alt="User Image">
                </div>
            </div>
            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                    data-accordion="false">
                    <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                    @hasrole('Super Admin')
                        <li class="nav-item">
                            <a href="{{ URL::to('') }}/admin/dashboard"
                                class="nav-link {{ Request::is('admin/dashboard') ? 'active' : '' }}">
                                <!-- <i class="nav-icon fas fa-tachometer-alt"></i> -->
                                <p>
                                    Dashboard
                                </p>
                            </a>
                        </li>
                        <li class="nav-item active">
                            <a href="{{ URL::to('') }}/admin/allgovernor"
                                class="nav-link {{ Request::is('admin/allgovernor') ? 'active' : '' }}">
                                <!-- <i class="nav-icon fas fa-user-alt"></i> -->
                                <p>
                                    Governorates
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ URL::to('') }}/admin/allregion"
                                class="nav-link {{ Request::is('admin/allregion') ? 'active' : '' }}">
                                <!-- <i class="nav-icon fas fa-globe"></i> -->
                                <p>
                                    Regions
                                </p>
                            </a>

                        </li>
                        <li class="nav-item">
                            <a href="{{ URL::to('') }}/admin/allfarmer"
                                class="nav-link {{ Request::is('admin/allfarmer') ? 'active' : '' }}">
                                <!-- <i class="nav-icon fas fa-tractor"></i> -->
                                <p>
                                    Farmers
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ URL::to('') }}/admin/allcoffeebuyer"
                                class="nav-link  {{ Request::is('admin/allcoffeebuyer') ? 'active' : '' }}">
                                <!-- <i class="nav-icon fas fa-user"></i> -->
                                <p>
                                    Coffee Buyers
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('admin/inventory') }}"
                                class="nav-link {{ Request::is('admin/inventory') ? 'active' : '' }}">
                                <!-- <i class="nav-icon fas fa-pallet"></i> -->
                                <p>
                                    Inventory
                                </p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ URL::to('') }}/admin/supplyChain"
                                class="nav-link  {{ Request::is('admin/supplyChain') ? 'active' : '' }} ">
                                <!-- <i class="nav-icon fa fa-life-ring"></i> -->
                                <p>
                                    Supply Chain
                                </p>
                            </a>
                        </li>

                    @else

                    @endhasrole


                    <li class="nav-item">
                        <a href="{{ URL::to('') }}/admin/allvillage"
                            class="nav-link {{ Request::is('admin/allvillage') ? 'active' : '' }} ">
                            <!-- <i class="nav-icon fas fa-tree"></i> -->
                            <p>
                                Villages
                            </p>
                        </a>
                    </li>


                    @hasrole('Super Admin')
                        <li class="nav-item">
                            <a href="{{ URL::to('') }}/admin/allbatchnumber"
                                class="nav-link  {{ Request::is('admin/allbatchnumber') ? 'active' : '' }}">
                                <!-- <i class="nav-icon fas fa-sort-numeric-up-alt"></i> -->
                                <p>
                                    Batch Numbers
                                </p>
                            </a>
                        </li>
                        <li class="nav-item {{ Request::is('admin/allcenter') ? 'menu-open' : '' }}">
                            <a href="{{ URL::to('') }}/admin/allcenter" class="nav-link ">
                                <!-- <i class="nav-icon fab fa-centercode"></i> -->
                                <p>
                                    Center
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview pl-2 nav-dropdown">
                                <li class="nav-item">
                                    <a href="{{ url('admin/allcenter') }}"
                                        class="nav-link  {{ Request::is('admin/allcenter') ? 'active' : '' }}">
                                        <!-- <i class="fas fa-long-arrow-alt-right nav-icon"></i> -->
                                        <p>All Center</p>
                                    </a>
                                </li>

                            </ul>
                        </li>
                        <li class="nav-item {{ Request::is('admin/orders/*') ? 'menu-open' : '' }}">
                            <a href="" class="nav-link ">
                                <!-- <i class="nav-icon fas fa-box"></i> -->
                                <p>
                                    Orders
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview pl-2 nav-dropdown">
                                <li class="nav-item">
                                    <a href="{{ url('admin/orders/create') }}"
                                        class="nav-link {{ Request::is('admin/orders/create') ? 'active' : '' }}">
                                        <!-- <i class="nav-icon fas fa-address-book"></i> -->
                                        <p>
                                            Create Order
                                        </p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ url('admin/orders') }}"
                                        class="nav-link {{ Request::is('admin/orders') ? 'active' : '' }}">
                                        <!-- <i class="nav-icon fas fa-address-book"></i> -->
                                        <p>
                                            All Orders
                                        </p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="{{ URL::to('') }}/admin/shipping"
                                class="nav-link  {{ Request::is('admin/shipping') ? 'active' : '' }} ">
                                <!-- <i class="fas fa-shipping-fast"> </i> &nbsp; -->
                                <p>
                                    Shipping
                                </p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ URL::to('') }}/admin/system_definition"
                                class="nav-link {{ Request::is('admin/system_definition') ? 'active' : '' }}">
                                <!-- <i class=" nav-icon fab fa-linode"></i> -->
                                <p>
                                    System Definition
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ URL::to('') }}/admin/environments"
                                class="nav-link  {{ Request::is('admin/environments') ? 'active' : '' }}">
                                <!-- <i class="nav-icon fas fa-globe"></i> -->
                                <p>
                                    Environments
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ URL::to('') }}/admin/allcontainer"
                                class="nav-link  {{ Request::is('admin/allcontainer') ? 'active' : '' }}">
                                <!-- <i class="nav-icon fas fa-shopping-basket"></i> -->
                                <p>
                                    Containers
                                </p>
                            </a>
                        </li>


                        <li class="nav-item">
                            <a href="{{ url('admin/new_milling_coffee') }}"
                                class="nav-link {{ Request::is('admin/new_milling_coffee') ? 'active' : '' }}">
                                <!-- <i class="nav-icon fas fa-tree"> </i> -->
                                <p>
                                    Inventory In Process
                                </p>
                            </a>
                            {{-- <a href="{{ URL::to('') }}/admin/milling_coffee"
                                class="nav-link {{ Request::is('admin/milling_coffee') ? 'active' : '' }}">
                                <!-- <i class="nav-icon fas fa-tree"> </i> -->
                                <p>
                                    Milling
                                </p>
                            </a> --}}
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('admin/lot_mixing') }}"
                                class="nav-link {{ Request::is('admin/lot_mixing') ? 'active' : '' }}">
                                <!-- <i class="nav-icon fas fa-tree"> </i> -->
                                <p>
                                    LOT MIXING
                                </p>
                            </a>
                        </li>
                        <li class="nav-item {{ Request::is('admin/packaging/*') ? 'menu-open' : '' }}">
                            <a href="" class="nav-link ">
                                <!-- <i class="nav-icon fas fa-box"></i> -->
                                <p>
                                    Packaging Coffee
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview pl-2 nav-dropdown">
                                <li class="nav-item">
                                    <a href="{{ url('admin/packaging/mixing') }}"
                                        class="nav-link {{ Request::is('admin/packaging/mixing') ? 'active' : '' }}">
                                        <!-- <i class="fas fa-mortar-pestle nav-icon"></i> -->
                                        <p>
                                            Mixing
                                        </p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ url('admin/packaging/approval') }}"
                                        class="nav-link {{ Request::is('admin/packaging/approval') ? 'active' : '' }}">
                                        <!-- <i class="nav-icon fas fa-stamp"></i> -->
                                        <p>
                                            Packaging Approval
                                        </p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item {{ Request::is('admin/uk_warehouse/*') ? 'menu-open' : '' }}">
                            <a href="" class="nav-link ">
                                <!-- <i class="nav-icon fas fa-box"></i> -->
                                <p>
                                    UK WareHouse
                                    <i class="fas fa-angle-left right"></i>
                                </p>

                            </a>
                            <ul class="nav nav-treeview pl-2 nav-dropdown">
                                <li class="nav-item">
                                    <a href="{{ url('admin/uk_warehouse/index') }}"
                                        class="nav-link {{ Request::is('admin/uk_warehouse/index') ? 'active' : '' }}">
                                        <!-- <i class="nav-icon fas fa-dollar-sign"></i> -->
                                        <p>
                                            Set Prices
                                        </p>
                                    </a>
                                </li>


                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="{{ URL::to('') }}/admin/governorweight"
                                class="nav-link {{ Request::is('admin/governorweight') ? 'active' : '' }}">
                                <!-- <i class="nav-icon fas fa-weight"></i> -->
                                <p>
                                    Total Weights
                                </p>
                            </a>
                        </li>
                        <li
                            class="nav-item has-treeview {{ Request::is('admin/allusers') || Request::is('admin/roles') ? 'menu-open' : '' }}">
                            <a href="{{ url('admin/allusers') }}" class="nav-link">
                                <!-- <i class="nav-icon fas fa-user"></i> -->
                                <p>
                                    Users Settings
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview pl-2 nav-dropdown">
                                <li class="nav-item">
                                    <a href="{{ url('admin/allusers') }}"
                                        class="nav-link {{ Request::is('admin/allusers') ? 'active' : '' }}">
                                        <!-- <i class="fas fa-long-arrow-alt-right nav-icon"></i> -->
                                        <p>
                                            Users
                                        </p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ url('admin/roles') }}"
                                        class="nav-link {{ Request::is('admin/roles') ? 'active' : '' }}">
                                        <!-- <i class="fas fa-long-arrow-alt-right nav-icon"></i> -->
                                        <p>
                                            Roles
                                        </p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="{{ URL::to('') }}/admin/alltransection"
                                class="nav-link  {{ Request::is('admin/alltransection') ? 'active' : '' }} ">
                                <!-- <i class="nav-icon fas fa-exchange-alt"></i> -->
                                <p>
                                    Transactions
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ URL::to('') }}/admin/support"
                                class="nav-link  {{ Request::is('admin/support') ? 'active' : '' }} ">
                                <!-- <i class="nav-icon fa fa-life-ring"></i> -->
                                <p>
                                    Support Queries
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ URL::to('') }}/admin/report"
                                class="nav-link  {{ Request::is('admin/report') ? 'active' : '' }} ">
                                <!-- <i class="nav-icon fa fa-life-ring"></i> -->
                                <p>
                                    Generate Report
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ URL::to('') }}/admin/duplication"
                                class="nav-link  {{ Request::is('admin/duplication') ? 'active' : '' }} ">
                                <!-- <i class="nav-icon fa fa-life-ring"></i> -->
                                <p>
                                    Duplication
                                </p>
                            </a>
                        </li>
                         <!-- <li class="nav-item">
                            <a href="{{ URL::to('') }}/admin/importView"
                                class="nav-link  {{ Request::is('admin/duplication') ? 'active' : '' }} ">
                                <i class="nav-icon fa fa-life-ring"></i>
                                <p>
                                    import
                                </p>
                            </a>
                        </li> -->
                    @else

                    @endhasrole



                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>
    <script>
        $(document).ready(function() {
            $('.menu-icon').on('click', function() {
                console.log('hello');
                $(".notification-icons").toggleClass("invisible");
                $(".nav-sidebar .nav-item").toggleClass("border-0");
            });
        });
    </script>
