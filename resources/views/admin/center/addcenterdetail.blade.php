@extends('layouts.default')
@section('title', 'All Center')
@section('content')
<style type="text/css">
  .select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #007BFF !important;
    border: 1px solid #007BFF;
}
.select2-container .select2-selection--single {
    height: 39px;
}
</style>
     <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1> Center Detail</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active"> Center Detail</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- left column -->
          <div class="col-md-12">
            <!-- general form elements -->
            <div class="card card-primary">

              <div class="card-header">
                <h3 class="card-title">Add</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form role="form" method="POST" action="{{URL::to('/admin/addcenterroles')}}">
                {{--  @if ($errors->any())
                  <div class="alert alert-danger">
                      <ul>
                          @foreach ($errors->all() as $error)
                              <li>{{ $error }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </li>
                          @endforeach
                      </ul>
                  </div>
              @endif --}}
                {{ csrf_field() }}
                <div class="card-body">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Center</label>
                   <select   class="form-control managerselect2s4" name="center_id"  @error('center_id') is-invalid @enderror>
                          @foreach($Center as $row)
                           <option value="{{ $row->center_id }}">{{ $row->center_name  }}</option>
                          @endforeach
                    </select>
                  </div>
                 {{-- <div class="form-group">
                   <label for="country_name">User Roles</label>
                    <select   class="form-control managerselect2s4" name="center_manager_id[]"  multiple="multiple" @error('center_manager_id') is-invalid @enderror>
                      @foreach($role as $row)
                      <optgroup label="{{ $row->name }}">
                          @foreach($row->users as $sub)
                          
                              <option value="{{ $sub->user_id }}">{{ $sub->email  }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {{isset( $sub->center_user)? $sub->center_user->center->center_name :''}}</option>
                          @endforeach
                      </optgroup>
                      @endforeach
                      @foreach($user as $row)
                      <option value="{{$row->user_id}}">{{$row->email}}</option>
                      @endforeach
                      @error('center_manager_id')
                       <span  class="text-danger">{{ $message }}</span>
                      @enderror
                    </select>

                  </div> --}}
                
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>
              </form>
            </div>
            <!-- /.card -->

           
            </div>
            <!-- /.card -->

          </div>
         
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
 
@endsection
