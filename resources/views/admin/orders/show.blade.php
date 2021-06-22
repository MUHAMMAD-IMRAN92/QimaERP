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
              <p class="card-text">
                <table class="table">
                  <tbody>
                    <tr>
                      <th>Order Number</th>
                      <td>{{ $order->order_number }}</td>
                    </tr>
                    <tr>
                      <th>Status</th>
                      <td>{{ $order->getStatus() }}</td>
                    </tr>
                    <tr>
                      <th>Date</th>
                      <td>{{ $order->created_at->toDateTimeString() }}</td>
                    </tr>
                    <tr>
                      <th>Total Amount</th>
                      <td>{{ $order->total }}</td>
                    </tr>
                  </tbody>
                </table>
              </p>
            </div>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Customer Details</h5>
              <p class="card-text">
                <table class="table">
                  <tbody>
                    <tr>
                      <th>Name</th>
                      <td>{{ $order->customer->name }}</td>
                    </tr>
                    <tr>
                      <th>Phone</th>
                      <td>{{ $order->customer->phone }}</td>
                    </tr>
                    <tr>
                      <th>Email</th>
                      <td>{{ $order->customer->email ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                      <th>Address</th>
                      <td>{{ $order->customer->address }}</td>
                    </tr>
                  </tbody>
                </table>
              </p>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <h5>Products</h5>
        </div>
        <div class="col-md-12">
          <table class="table">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Product Name</th>
                <th scope="col">Type</th>
                <th scope="col">Weight</th>
                <th scope="col">Price</th>
                <th scope="col">Total</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($order->details as $detail)
              <tr>
                <th scope="row">{{ $loop->iteration }}</th>
                <td>{{ $detail->product->name }}</td>
                <td>{{ $detail->getType() }}</td>
                <td>{{ $detail->weight }}</td>
                <td>{{ $detail->price }}</td>
                <td>{{ $detail->total }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <!-- /.container-fluid -->
  </section>
  <!-- /.content -->
</div>
@endsection