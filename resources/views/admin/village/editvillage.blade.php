@extends('layouts.default')
@section('title', 'All Village')
@section('content')
     <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Edit Village</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Edit Village</li>
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
                <h3 class="card-title">Edit</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form role="form" method="POST" action="{{URL::to('')}}/admin/updatevillage">
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
                <input type="hidden" name="village_id" value="{{$village->village_id}}">
                <div class="card-body col-md-6">
                  <div class="form-group">
                    <label for="exampleInputPassword1"> Code</label>
                    <input type="text" id="village_title" class="form-control" id="exampleInputPassword1" name="village_title" placeholder="Title" value="{{$village->village_code}}" readonly="readonly">
                   
                  </div>
                  <div class="form-group">
                    <label for="exampleInputPassword1">Title</label>
                    <input type="text" id="village_title" value="{{$village->village_title}}" class="form-control" id="exampleInputPassword1" name="village_title" placeholder="Title"  @error('village_title') is-invalid @enderror>
                     @error('village_title')
                        <span  class="text-danger">{{ $message }}</span>
                    @enderror
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