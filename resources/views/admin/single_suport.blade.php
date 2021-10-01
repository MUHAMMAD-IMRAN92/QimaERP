@extends('layouts.default')
@section('title', 'All Transection')
@section('content')
    <style type="text/css">
        .dataTables_wrapper .dataTables_filter input {
            margin-left: 0.5em;
            width: 240px;
        }

    </style>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">

                        <h1>{{ $support->title }}</h1>

                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Query</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">

                        <!-- /.card -->
                        <div class="card">

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-8">
                                        {{ $support->description }}</div>
                                    <div class="col-4">
                                        <img width="200px"
                                            src="{{ Storage::disk('s3')->url('images/' . $support->image) }}" alt="">
                                    </div>
                                </div>


                                <p><b style=" margin-left:30% ">Query By</b>
                                    :{{ \App\user::find($support->user_id)->first_name }}.{{ \App\user::find($support->user_id)->last_name }}
                                </p>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>

@endsection
