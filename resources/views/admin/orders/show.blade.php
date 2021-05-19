@extends('layouts.default')
@section('title', 'Order')
@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><b>Order</b></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Order</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                  <div class="card">
                    <div class="card-body">
                      <h5 class="card-title">Order Details</h5>
                      <p class="card-text">Order Number: <b>{{ $order->order_number }}</b></p>
                      <p class="card-text">Status: <b>{{ $order->getStatus() }}</b></p>
                      <p class="card-text">Date: <b>{{ $order->created_at->toDateTimeString() }}</b></p>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="card">
                    <div class="card-body">
                      <h5 class="card-title">Customer Details</h5>
                      <p class="card-text">Name: <b>{{ $oder->customer->name }}</b></p>
                      <p class="card-text">Name: <b>{{ $oder->customer->phone }}</b></p>
                      <p class="card-text">Name: <b>{{ $oder->customer->name }}</b></p>
                    </div>
                  </div>
                </div>
              </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
@endsection