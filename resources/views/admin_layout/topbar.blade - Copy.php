  <header class="main-header">

    <!-- Logo -->
 
    <a href="{{asset('admin/login')}}" class="logo">
    <div class="logo-section">
            <img class="clinic-logos" src="{{asset('admin_assets/dist/img/logo.jpeg')}}">
</div>
      <!-- mini logo for sidebar mini 50x50 pixels -->
    </a>

    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
      <!-- Navbar Right Menu -->
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="{{ asset('admin_assets/dist/img/user2-160x160.jpg')}}" class="user-image" alt="User Image">
              <span class="hidden-xs">Admin</span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="{{ asset('admin_assets/dist/img/user2-160x160.jpg')}}" class="img-circle" alt="User Image">

                <p>
                  Admin
                  <small>Onwer Since Nov. 2019</small>
                </p>
              </li> 
              <li class="user-footer">
                
                <div class="pull-right">
                  <a href="{{asset('admin/logout')}}" class="btn btn-default btn-flat">Sign out</a>
                </div>
              </li>
            </ul>
          </li>
          <!-- Control Sidebar Toggle Button -->
           
        </ul>
      </div>

    </nav>
   
  </header>