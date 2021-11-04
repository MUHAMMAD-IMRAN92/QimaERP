@extends('layouts.default')
@section('title', 'All User')

@section('content')
<style type="text/css">
    a,
    a:hover {
        color: #333
    }
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <div class="mx-lg-5">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark text-uppercase font-weight-lighter text-heading">Assign Village</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Assign Village</li>
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
                    <div class="col-md-12 ">
                        <!-- general form elements -->
                        <div class="card card-primary">

                            <div class="card-header">
                                <h3 class="card-title text-uppercase letter-spacing-2">{{ $buyer->first_name . ' ' . $buyer->last_name }}</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form role="form" method="POST" action={{ url('admin/assignVillages') }}>
                                {{ csrf_field() }}
                                <div class="card-body col-md-12 text-uppercase letter-spacing-1">
                                    <div class="form-group">
                                        <input type="hidden" name="user_id" id="" value="{{ $buyer->user_id }}">
                                        <label for="cars">Choose Villages:</label>
                                        <div class="row">
                                            @foreach ($villages as $village)
                                            <div class="col-3">
                                                <input type="checkbox" name="villages[]" id="cars" value="{{ $village->village_id }}" {{ in_array($village->village_id, $villageId) ? 'checked' : '' }}>
                                                {{ $village->village_title }}
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                        </div>
                        <!-- /.card-body -->

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary text-uppercase letter-spacing-1">Submit</button>
                        </div>
                        </form>
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.card -->
            </div>
    </div>
</div>
<!-- /.row -->
</div><!-- /.container-fluid -->
</section>
<!-- /.content -->
</div>
<!-- /.content-wrapper -->
@endsection
