@extends('admin_layout.app')
@section('page_css')
@endsection
@section('content')
<div class="wrapper">
    @include('admin_layout.topbar')
    @include('admin_layout.sidebar')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        @include('admin_layout.header')
        <!-- Main content -->
        <section class="content">
            <!-- Info boxes -->
            <!-- /.row -->
            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">{{ $title }}</h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <img style="width:200px" src="" id="doctor_image">
                                </div>
                                <div class="col-md-6">

                                    <form method="POST" action="{{url('admin/storespecialization')}}" enctype="multipart/form-data">
                                        {{csrf_field()}}
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Specialization Title <span style="color: red">*</span></label>
                                            <input type="text" class="form-control" id="exampleInputCity" aria-describedby="emailHelp" name="title" placeholder="Specialization" value="{{ old('title') }}">
                                            @if ($errors->has('title'))
                                            <span class="invalid-feedback clr-red" role="alert">
                                                <strong>{{ $errors->first('title') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            <label for="specialization_image">Specialization Image</label>
                                            <input type="file" class="form-control" id="profile_image" aria-describedby="emailHelp" name="specialization_image" accept="image/*">
                                        </div>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </form>
                                </div>

                            </div>
                            <!-- /.row -->
                        </div>
                    </div>
                    <!-- /.box -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    @include('admin_layout.footer')
    <div class="control-sidebar-bg"></div>

</div>

@endsection

@section('page_js')
<script>
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#doctor_image').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#profile_image").change(function () {
   
        readURL(this);
    });
</script>
@endsection