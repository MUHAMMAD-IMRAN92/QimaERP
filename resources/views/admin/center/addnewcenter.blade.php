@extends('layouts.default')
@section('title', 'All Center')
@section('content')
<style type="text/css">
  .select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #007BFF !important;
    border: 1px solid #007BFF;
}</style>
     <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Add Center</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Add Center</li>
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
              <form role="form" method="POST" action="{{URL::to('/admin/storecenter')}}">
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
                    <label for="exampleInputEmail1">Code</label>
                    <input type="text" id="center_code" class="form-control " id="exampleInputEmail1" name="center_code" placeholder="Enter Code" @error('center_code') is-invalid @enderror>
                    @error('center_code')
                       <span  class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                  <div class="form-group">
                    <label for="exampleInputPassword1">Title</label>
                    <input type="text" id="center_name" class="form-control" id="exampleInputPassword1" name="center_name" placeholder="Title"  @error('center_name') is-invalid @enderror>
                    @error('center_name')
                       <span  class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                 <div class="form-group">
                   <label for="country_name">All Center</label>
                    <select   class="form-control managerselect2s4" name="center_manager_id[]"  multiple="multiple" @error('center_manager_id') is-invalid @enderror>
                      @foreach($user as $row)
                      <option value="{{$row->user_id}}">{{$row->email}}</option>
                      @endforeach
                      @error('center_manager_id')
                       <span  class="text-danger">{{ $message }}</span>
                      @enderror
                    </select>

                  </div>
                
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
