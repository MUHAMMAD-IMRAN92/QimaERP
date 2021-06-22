@extends('layouts.default')
@section('title', 'All Center')
@section('content')
<style type="text/css">
    .dataTables_wrapper .dataTables_filter input {
        margin-left: 0.5em;
        width: 240px;
    }
</style>    

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    @if (session()->has('msg'))
    <div class="alert alert-success">
        <p>{{ session()->get('msg') }}<button type="button" class="close" data-dismiss="alert">&times;</button></p>
    </div>
    @endif
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Roles
                        <a href="{{url('/admin/roles/create')}}" class="btn btn-add rounded-circle"> 
                            <i class="fas fa-user-plus add-client-icon"></i>
                        </a>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Roles</li>
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
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="users" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>S#</th>
                                        <th> Name</th>
                                        <th>Guard Name</th>
                                       
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($roles as $role)
                                   <tr>
                                    <td>
                                       {{$loop->iteration}}
                                    </td>
                                    <td>{{$role->name}}</td>
                                    <td>{{$role->guard_name}}</td>
                                   </tr>
                                    @endforeach
                                    
                                </tbody>

                            </table>
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
<!-- /.content-wrapper -->
@endsection