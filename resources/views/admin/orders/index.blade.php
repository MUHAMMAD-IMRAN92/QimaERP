@extends('layouts.default')
@section('title', 'All Orders')
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
        @if (session()->has('msg'))
            {{-- <div class="alert alert-success alert-dismissible">
                {{ session()->get('msg') }}
            </div> --}}
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session()->get('msg') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @if (session()->has('dmsg'))
            {{-- <div class="alert alert-danger alert-dismissible">
                {{ session()->get('dmsg') }}
            </div> --}}
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session()->get('dmsg') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>All Orders
                        <a href="/admin/orders/create" class="btn btn-add rounded-circle">
                            <i class="fas fa-plus"></i>
                        </a>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">All Orders</li>
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
                        <form action="{{ url('admin/paidOrder') }}" method="POST">
                            @csrf
                            <input type="submit" value="Mark Order Paid" class="btn btn-success"
                                style="margin-left: 85% ; margin-top: 5px">
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="myTable" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Sr#</th>
                                            <th>Order Number</th>
                                            <th>Status</th>
                                            <th>Total Price</th>
                                            <th>Mark Paid</th>
                                            <th>Created At</th>
                                            <th>View</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($orders as $order)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $order->order_number }}</td>
                                                <td>{{ $order->getStatus() }}</td>
                                                <td>{{ $order->total }}</td>
                                                @if ($order->status == 4)
                                                    <td align="center "><input type="checkbox"
                                                            value="{{ $order->id }}" name="order[]" id=""></td>
                                                @else
                                                    <td></td>
                                                @endif
                                                <td>{{ $order->created_at->toDateTimeString() }}</td>
                                                <td><a href="{{ route('orders.show', $order) }}">View</a></td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                        </form>
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
<script type="text/javascript">
    $(document).ready(function() {
        $('#myTable').DataTable();
    });
</script>
@endsection
