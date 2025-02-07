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
        <div class="mx-lg-5">

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
                        <div class="col-sm-6"></div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">All Orders</li>
                            </ol>
                        </div>
                        <div class="col-sm-8">
                            <h1 class="m-0 text-dark text-uppercase font-weight-lighter text-heading">
                                All Orders
                            </h1>
                        </div>
                        <div class="col-sm-4 d-flex justify-content-end align-items-end">
                            <ol class="breadcrumb float-sm-right">
                                <a href="{{url('admin/orders/create')}}" class="px-0 btn btn-add rounded-circle">
                                    <button class="px-0 btn btn-dark bg-transparent border-0 add-button text-uppercase">
                                        Add Order
                                    </button>
                                </a>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
        <hr class="ml-md-2">
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <!-- /.card -->

                            <div class="card shadow-none">
                                <form action="{{ url('admin/paidOrder') }}" method="POST">
                                    @csrf
                                    <div class="text-right">
                                        <input type="submit" value="Mark Order Paid" class="btn bg-green-btn my-2">
                                     </div>
                                    <!-- /.card-header -->
                                    <div class="table-responsive text-uppercase letter-spacing-2 governors_table ">
                                        <table class="table border-bottom-0 table-bordered-custom text-center custom-table" id="myTable">
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
