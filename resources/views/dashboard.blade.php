@extends('layouts.default')
@section('title', 'Homepage')
@section('content')
<style type="text/css">
    .small-box>.inner{
        background-color: white;
        color: black;
    }
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Dashboard</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    {{--  <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{URL::to('')}}/admin/dashboard">Home</a></li>
                           </ol> --}}
                       </div><!-- /.col -->
                   </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
<section class="content">
                                <div class="container-fluid">
                                    <!-- Small boxes (Stat box) -->
                                    <div class="row">
                                        <div class="col-lg-3 col-6">
                                            <!-- small box -->
                                            <div class="small-box bg-info">
                                                <div class="inner">
                                                    <h3>{{App\Farmer::count()}}</h3>

                                                    <p>Farmers</p>
                                                </div>
                                                <div class="icon">
                                                    <i class="ion ion-bag"></i>
                                                </div>
                                                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                                            </div>
                                        </div>
                                        <!-- ./col -->
                                        <div class="col-lg-3 col-6">
                                            <!-- small box -->
                                            <div class="small-box bg-success">
                                                <div class="inner">
                                                    {{-- <h3>53<sup style="font-size: 20px">%</sup></h3> --}}
                                                    <h3>{{App\Village::count()}}</h3>
                                                    <p>Villages</p>
                                                </div>
                                                <div class="icon">
                                                    <i class="ion ion-stats-bars"></i>
                                                </div>
                                                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                                            </div>
                                        </div>
                                        <!-- ./col -->
                                        <div class="col-lg-3 col-6">
                                            <!-- small box -->
                                            <div class="small-box bg-warning">
                                                <div class="inner">
                                                    <h3>44</h3>

                                                    <p>User Registrations</p>
                                                </div>
                                                <div class="icon">
                                                    <i class="ion ion-person-add"></i>
                                                </div>
                                                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                                            </div>
                                        </div>
                                        <!-- ./col -->
                                        <div class="col-lg-3 col-6">
                                            <!-- small box -->
                                            <div class="small-box bg-danger">
                                                <div class="inner">
                                                    <h3>65</h3>

                                                    <p>Unique Visitors</p>
                                                </div>
                                                <div class="icon">
                                                    <i class="ion ion-pie-graph"></i>
                                                </div>
                                                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                                            </div>
                                        </div>
                                        <!-- ./col -->
                                    </div>
                                    <!-- /.row -->

                                </div><!-- /.container-fluid -->
                            </section>
                            <!-- /.content -->
                            <section class="content">
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h3 class="card-title"><b>Farmer</b> </h3>
                                                </div>
                                                <!-- /.card-header -->
                                                <div class="card-body">
                                                    <table class="table table-bordered">
                                                        <thead>                  
                                                            <tr>
                                                                <th style="width: 10px">Sr#</th>
                                                                <th>Farmer Code</th>
                                                                <th>Farmer Name</th>
                                                                <th>Village Code</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($farmer as $key=> $row)
                                                            <tr>
                                                                <td>{{$key+1}}</td>
                                                                <td>{{$row->farmer_code}}</td>
                                                                <td>{{$row->farmer_name}}</td>
                                                                <td>{{$row->village_code}}</td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <!-- /.card-body -->

                                            </div>


                                        </div>
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h3 class="card-title"><b>Village</b> </h3>
                                                </div>
                                                <!-- /.card-header -->
                                                <div class="card-body">
                                                    <table class="table table-bordered">
                                                        <thead>                  
                                                            <tr>
                                                                <th style="width: 10px">Sr#</th>
                                                                <th>Village Code</th>
                                                                <th>Village Name</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($village as $key=>  $row)
                                                            <tr>
                                                                <td>{{$key+1}}</td>
                                                                <td>{{$row->village_code}}</td>
                                                                <td>{{$row->village_title}}</td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <!-- /.card-body -->

                                            </div>


                                        </div>

                                    </div>
                                </div>
                            </section>
                            </div>
                            @endsection