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
                            <h3 class="box-title">Add New City</h3>

                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>

                                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <p class="text-center">
                                        <a class="btn btn-primary pull-right"  href="{{asset('admin/allcities')}}"><strong>All Cities  </strong></a> 
                                    </p>

                                    <form method="POST" action="{{url('admin/storecity')}}">
                                        {{csrf_field()}}
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">City Name</label>
                                            <input type="text" class="form-control" id="exampleInputCity" aria-describedby="emailHelp" name="city_name" placeholder="Enter City" value="{{ old('city_name') }}">
                                            @if ($errors->has('city_name'))
                                            <span class="invalid-feedback clr-red" role="alert">
                                                <strong>{{ $errors->first('city_name') }}</strong>
                                            </span>
                                            @endif
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

@endsection