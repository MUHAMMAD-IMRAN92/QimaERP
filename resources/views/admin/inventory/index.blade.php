@extends('layouts.default')
@section('title', 'Transection Detail')
@section('content')

<style type="text/css">
    .nav.nav-tabs {
        float: left;
        display: block;
        margin-right: 20px;
        border-bottom: 0;
        border-right: 1px solid #ddd;
        padding-right: 15px;
    }

    .nav-tabs .nav-link {
        border: 1px solid transparent;
        border-top-left-radius: .25rem;
        border-top-right-radius: .25rem;
        background: #ccc;
    }

    .nav-tabs .nav-link.active {
        color: #495057;

        border-color: transparent !important;
    }

    .nav-tabs .nav-link {
        border: 1px solid transparent;
        border-top-left-radius: 0rem !important;
        border-top-right-radius: 0rem !important;
    }

    .tab-content>.active {
        display: block;
        /*background: #007bff;*/
        min-height: 165px;
    }

    .nav.nav-tabs {
        float: left;
        display: block;
        margin-right: 20px;
        border-bottom: 0;
        border-right: 1px solid transparent;
        padding-right: 15px;
    }

    #custom_tab li.nav-item a {
        color: #000;
        margin-bottom: 0px;
    }

    .batchnumber thead tr {
        border-bottom: 1px solid black;
    }

    .batchnumber tbody tr {
        border-bottom: 1px solid black;
    }

    .set-padding {
        padding: 10px;
    }

    .top-margin-set {
        margin-top: 10px;
    }
</style>
<script>
    $(document).ready(function() {
            $('#myTable').DataTable();
        });

</script>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><b>Mixing Coffee</b></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Inventory</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="card col-lg-12">
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="col-md-6">
                            @if ($errors->any())
                            <div class="alert alert-danger">

                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </div>
                            @endif
                            @if (session()->has('msg'))
                            <div class="alert alert-success" id="alert">
                                {{ session()->get('msg') }}
                            </div>
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <h2 class="text-center">Normal Products</h2>
                                @foreach ($products as $product)
                                <div class="row">
                                    <div class="col-md-10 offset-1">
                                        <div class="card">
                                            <div class="card-body">
                                                <h5 class="card-title">{{ $product['name'] }}</h5>
                                                <p class="card-text">Total Weight: <b>{{ $product['weight'] }}</b></p>
                                                {{-- <a href="#" class="btn btn-primary">Go somewhere</a> --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <div class="col-md-6">
                                <h2 class="text-center">Special Products</h2>
                                @foreach ($special_products as $product)
                                <div class="row">
                                    <div class="col-md-10 offset-1">
                                        <div class="card">
                                            <div class="card-body">
                                                <h5 class="card-title">{{ $product['name'] }}</h5>
                                                <p class="card-text">Total Weight: <b>{{ $product['weight'] }}</b></p>
                                                {{-- <a href="#" class="btn btn-primary">Go somewhere</a> --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
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

<script>
    setTimeout(function(){
        document.getElementById('alert').remove();
    }, 3000)
</script>

@endsection