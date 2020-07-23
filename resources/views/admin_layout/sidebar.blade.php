 @php $segment = Request::segment(2); @endphp
<!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar side-clr">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
       
      <ul class="sidebar-menu" data-widget="tree">
        
        <li @if($segment == 'dashboard') class="active" @endif>
          <a href="{{asset('admin/login')}}">
            <i class="fa fa-dashboard"></i> <span>Dashboard</span>
          
          </a> 
        </li>
       
         <li @if($segment == 'allcities' || $segment == 'editcitie') class="active" @endif>
          <a href="{{asset('admin/allcities')}}">
            <i class="fa fa-location-arrow"></i> <span>Cities</span>
          
          </a> 
        </li>
        <li @if($segment == 'users' || $segment == 'userdetail') class="active" @endif>
          <a href="{{asset('admin/users')}}">
            <i class="fa fa-users"></i> <span>Users</span>
          
          </a> 
        </li>
         <li @if($segment == 'appointments' || $segment == 'appointments') class="active" @endif>
          <a href="{{asset('admin/appointments')}}">
            <i class="fa fa-calendar-o"></i> <span>Appointments</span>
          
          </a> 
        </li>
        <li @if($segment == 'specializations' || $segment == 'editspecialization') class="active" @endif>
          <a href="{{asset('admin/specializations')}}">
            <i class="fa fa-stethoscope"></i> <span>Specializations</span>
          
          </a> 
        </li>
        <li @if($segment == 'allhospitals' || $segment == 'addhospital' || $segment == 'edithospital') class="active" @endif>
          <a href="{{asset('admin/allhospitals')}}">
            <i class="fa fa-hospital-o"></i> <span>Hospital</span>
          
          </a> 
        </li>
        
         
         <li @if($segment == 'doctors' || $segment == 'adddoctor' || $segment == 'editdoctor' || $segment == 'doctordetail') class="active" @endif>
          <a href="{{asset('admin/doctors')}}">
            <i class="fa fa-user-md"></i> <span>Doctors</span>
          
          </a> 
        </li>
         <li @if($segment == 'profile') class="active" @endif>
          <a href="{{asset('admin/profile')}}">
            <i class="fa fa-user"></i> <span>Profile</span>
          
          </a> 
        </li>
        <li class="">
          <a href="{{asset('admin/logout')}}">
            <i class="fa fa-sign-out"></i> <span>Sign Out</span>
          
          </a> 
        </li>
        </ul>
    </section>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->