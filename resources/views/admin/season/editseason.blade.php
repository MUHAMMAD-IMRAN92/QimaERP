@extends('layouts.default')
@section('title', 'All Season')

@section('content')
<style type="text/css">
  .select2-selection__rendered {
    line-height: 23px !important;

}

.select2-container .select2-selection--single {
    height: 35px !important;
}
.select2-selection__arrow {
    height: 34px !important;
}
</style>
     <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Edit Season</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Edit Season</li>
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
              <form role="form" method="POST" action="{{URL::to('')}}/admin/updateseason">
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
                <input type="hidden" name="season_id" value="{{$season->season_id}}">
                <div class="card-body">
                  <div class="form-group">
                    <label for="season_title">Title</label>
                    <input type="text" id="season_title" value="{{$season->season_title}}" class="form-control " id="exampleInputEmail1" name="season_title" placeholder="Enter Title" @error('season_title') is-invalid @enderror>
                    @error('season_title')
                        <div class="alert alert-danger">{{ $message }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button></div>
                    @enderror
                  </div>
                 <div class="form-group">
                  <label>Season Status</label>
                  <select class="form-control select2" name="status"  @error('status') is-invalid @enderror>
                    
                    <option @if($season->status) selected @endif value="0">Season Start</option>
                    <option @if($season->status) selected @endif value="1">Season End</option>
                     @error('season_title')
                        <div class="alert alert-danger">{{ $message }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button></div>
                    @enderror
                  </select>

                </div>
                   <!-- Date range -->

                <div class="form-group">

                  <label>Start Date:</label>
                    <div class="input-group date" id="reservationdate" data-target-input="nearest">
                        <input type="text" value="{{date('m-d-Y', strtotime($season->start_date))}}" name="start_date" id="startdate" class="form-control datetimepicker-input" data-target="#reservationdate" @error('start_date') is-invalid @enderror/>

                        <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>

                    </div>
                     @error('start_date')
                        <div class="alert alert-danger">{{ $message }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button></div>
                    @enderror
                </div>

                <div class="form-group">
                  <label>End Date:</label>
                    <div class="input-group date" id="enddate" data-target-input="nearest">
                       <input type="checkbox" name="end_date" value="1" onclick="var input = document.getElementById('end'); if(this.checked){ input.disabled = false; input.focus();}else{input.disabled=true;}" style="margin-top: 10px; margin-right: 18px;" checked />
                        <input type="text" value="{{date('m-d-Y', strtotime($season->end_date))}}" name="end_date" id="end" class="form-control datetimepicker-input" data-target="#enddate"/>
                        <div class="input-group-append" data-target="#enddate" data-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>
                    </div>
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
  <script type="text/javascript">
    $(function () {
      $('#reservationdate').datetimepicker({
        format: 'L'
    });
       //Date range picker
    $('#enddate').datetimepicker({
        format: 'L'
    });
     $('.select2').select2()


      })
  </script>
@endsection