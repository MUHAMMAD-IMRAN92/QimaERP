@extends('layouts.default')
@section('title', 'All Village')
@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <div class="mx-lg-5">
        <div class="row">
            <div class="col-md-12">
                @if (Session::has('msg'))
                <div class="alert alert-success" role="alert">
                    <b>{{ Session::get('msg') }}</b>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
                @endif
            </div>
        </div>
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark text-uppercase font-weight-lighter text-heading">Edit Village</h1>
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

                            {{-- <div class="card-header">
                                    <h3 class="card-title">Edit</h3>
                                    </div> --}}
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form role="form" method="POST" action="{{ URL::to('') }}/admin/updatevillage">
                                {{-- @if ($errors->any())
                                    <div class="alert alert-danger">
                                                                 <ul>
                                            @foreach ($errors->all() as $error)
                                            <li>{{ $error }}
                                <button type="button" class="close                                                                                                                   " data-dismiss="alert" aria-label="                                    Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                </li>
                                @endforeach
                                </ul>
                        </div>
                        @endif --}}
                        {{ csrf_field() }}
                        <input type="hidden" name="village_id" value="{{ $village->village_id }}">
                        <div class="row">
                            <div class="card-body col-md-6 text-uppercase letter-spacing-1">
                                <div class="form-group">

                                    <input type="hidden" id="village_title" class="form-control" id="hiddenfield" name="code" placeholder="Title" value="{{ Str::beforeLast($village->village_code, '-') }}">

                                </div>
                                <div class="form-group">
                                    <label for="exampleInputPassword1"> Code</label>
                                    <input type="text" id="village_title" class="form-control" id="exampleInputPassword1" name="village_code" placeholder="Title" value="{{ Str::afterLast($village->village_code, '-') }}" {{ count($transaction) > 0 ? 'readonly' : '' }}>

                                </div>
                                <div class="form-group">
                                    <label for="exampleInputPassword1">Village Title (English)</label>
                                    <input type="text" id="village_title" value="{{ $village->village_title }}" class="form-control" id="exampleInputPassword1" name="village_title" placeholder="Village Title (English)" @error('village_title') is-invalid @enderror>
                                    @error('village_title')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputPassword1">Village Title (Arabic)</label>
                                    <input type="text" id="village_title" value="{{ $village->village_title_ar }}" class="form-control" id="exampleInputPassword1" name="village_title_ar" placeholder="Village Title (Arabic)" @error('village_title_ar') is-invalid @enderror>
                                    @error('village_title_ar')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputPassword1">Price Per Kg </label>
                                    <input type="number" id="price_per_kg" class="form-control" value="{{ $village->price_per_kg }}" name="price_per_kg" placeholder="Price Per Kg" @error('farmer_nicn') is-invalid @enderror>
                                    @error('price_per_kg')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="card-body col-md-6" style=" margin-top:1em !important;">
                                <caption>
                                    <h4 class="text-uppercase letter-spacing-1">Farmers</h4>
                                </caption>
                                <div class="table-responsive text-uppercase letter-spacing-1 governors_table">
                                    <table class="table table-bordered table-stripeds">
                                        <thead>
                                            <th>Sr#</th>
                                            <th>Farmer Name</th>
                                            <th>Farmer Code</th>
                                        </thead>
                                        @foreach ($farmers as $farmer)
                                        <tr>
                                            <td>
                                                {{ $loop->iteration }}
                                            </td>
                                            <td>
                                                {{ $farmer->farmer_name }}
                                            </td>
                                            <td>
                                                {{ $farmer->farmer_code }}
                                            </td>

                                        </tr>
                                        @endforeach
                                    </table>
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
    <!-- /.row -->
</div><!-- /.container-fluid -->
</div>
</section>
<!-- /.content -->
</div>
<!-- /.content-wrapper -->

@endsection
