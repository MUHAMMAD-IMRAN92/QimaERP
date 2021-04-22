@extends('layouts.default')
@section('title', 'All Villages')
@section('content')
    <style type="text/css">
        .dataTables_wrapper .dataTables_filter input {
            margin-left: 0.5em;
            width: 240px;
        }

    </style>
<script>
$(document).ready( function () {
    $('#myTable').DataTable();
} );
</script>


    <div class="content-wrapper">
        @if (session()->has('update'))
            <div class="alert alert-success">
                {{ session()->get('update') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @if (Session::has('message'))
            <div class="alert alert-success" role="alert">
                <b>{{ Session::get('message') }}</b>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">

                    <div class="col-sm-6">
                        <h1>Add New Village <a href="{{ URL::to('') }}/admin/addnewvillage"
                                class="btn btn-add rounded-circle">
                                <button class="btn btn-dark">Add Farmer</button>
                            </a>

                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">

                        </ol>
                    </div>
                </div>
            </div>
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
                                <table id="myTable" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>S#</th>
                                            <th>Village Code</th>
                                            <th>Village Title (En)</th>
                                            <th>Village Title (Ar)</th>
                                            <th>Price Per Kg</th>

                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $village)
                                            <tr>
                                                <td>{{ $village->village_id }}</td>
                                                <td>{{ $village->village_code }}</td>

                                                <td>{{ $village->village_title }}</td>
                                                <td>{{ $village->village_title_ar }}</td>
                                                <td>{{ $village->price_per_kg }}</td>

                                                <td> <a  href="{{ url('admin/editvillage/' . $village->village_id) }}">Edit</a> &nbsp
                                                <a href="{{ route('village.profile', $village) }}">View<i
                                                            class="fas fa-eye"></i></a> </td>
                                            </tr>
                                        @endforeach
                                    </tbody>


                                </table>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </section>

    </div>


@endsection

