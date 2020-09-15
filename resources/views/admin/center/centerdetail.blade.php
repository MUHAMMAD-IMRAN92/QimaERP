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
            <h1>{{$detail->center_name}}
            </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Center Detail</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-6">
                <table id="centers" class="table table-bordered ">
                  <thead class="thead-dark">
                  <tr>
                    <th>Name</th>
                    <th>Role</th>
                  </tr>
                  </thead>

                  <tbody>
                     <?php
                      $userId=array();
                     
                      ?>
                    @foreach($userrole as $row)
                       <?php 
                        array_push($userId,$row->user_id);

                       ?>
                    <tr>
                      <td>{{$row->first_name}}</td>
                      <td>{{isset($row->roles)?$row->roles['0']->name :''}}</td>
                    </tr>
                    @endforeach
                  </tbody>
                
                </table>
          </div>
           <div class="col-6">
            <div class="card card-dark">
              <div class="card-header">
                <h3 class="card-title">Select Roles</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form class="form-horizontal" role="form" method="POST" action="{{URL::to('/admin/updatecenterrole')}}">
                {{ csrf_field() }}
                <input type="hidden" name="center_id" value="{{$detail->center_id}}">
                <div class="form-group row" style="margin-top: 28px;margin-left: 11px;">
                  <div class="col-md-2"> <label for="exampleInputEmail1">Add Roles</label></div>
                  <div class="col-md-8">  <select  class="form-control managerselect2s4" name="center_manager_id[]"  multiple="multiple" @error('center_manager_id') is-invalid @enderror>
                       @foreach($role as $row)
                      <optgroup label="{{ $row->name }}">
                          @foreach($row->users as $sub)
                          
                              <option  @if (in_array($sub->user_id, $userId)) selected @endif value="{{ $sub->user_id }}">{{ $sub->email  }}&nbsp;&nbsp;&nbsp; {{isset( $sub->center_user)? $sub->center_user->center->center_name :''}}</option>
                          @endforeach
                      </optgroup>
                      @endforeach
                     {{--  @foreach($user as $row)
                      <option @if (in_array($row->user_id, $userId)) selected @endif value="{{$row->user_id}}">{{$row->email}}</option>
                      @endforeach --}}
                       
                    </select>
                     @error('center_manager_id')
                          <span class="text-danger">{{ $message }}</span>
                        @enderror
                  </div>
                
                  

                  </div>
                <!-- /.card-body -->
                <div class="card-footer">
                  <button type="submit" class="btn btn-dark text-center" style="margin-left: auto;margin-right: auto;display: block;">Update</button>
                  
                </div>
                <!-- /.card-footer -->
              </form>
            </div>
           </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->

      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  
@endsection