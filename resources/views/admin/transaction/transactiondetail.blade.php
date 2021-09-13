@extends('layouts.default')
@section('title', 'Transection Detail')
@section('content')

    <style>
        .circle {
            background-color: lightgreen;
            /* border: 1px solid rgb(145, 136, 136); */
            border-radius: 50%;
            width: 50%;
            height: 80px;
        }

        .message {
            background-color: lightblue;
            border: 1px solid rgb(145, 136, 136);
            border-radius: 6%;
            height: 80px;
        }



        .chat {
            margin-top: auto;
            margin-bottom: auto;
        }

        .card {
            /* height: 500px; */
            border-radius: 15px !important;
            /* background-color: rgba(0, 0, 0, 0.4) !important; */
        }

        .contacts_body {
            padding: 0.75rem 0 !important;
            overflow-y: auto;
            white-space: nowrap;
        }

        .msg_card_body {
            overflow-y: auto;
        }

        .card-header {
            border-radius: 15px 15px 0 0 !important;
            border-bottom: 0 !important;
        }

        .card-footer {
            border-radius: 0 0 15px 15px !important;
            border-top: 0 !important;
        }

        .container {
            align-content: center;
        }

        .search {
            border-radius: 15px 0 0 15px !important;
            background-color: rgba(0, 0, 0, 0.3) !important;
            border: 0 !important;
            color: white !important;
        }

        .search:focus {
            box-shadow: none !important;
            outline: 0px !important;
        }

        .type_msg {
            background-color: rgba(0, 0, 0, 0.3) !important;
            border: 0 !important;
            color: white !important;
            height: 60px !important;
            overflow-y: auto;
        }

        .type_msg:focus {
            box-shadow: none !important;
            outline: 0px !important;
        }

        .attach_btn {
            border-radius: 15px 0 0 15px !important;
            background-color: rgba(0, 0, 0, 0.3) !important;
            border: 0 !important;
            color: white !important;
            cursor: pointer;
        }

        .send_btn {
            border-radius: 0 15px 15px 0 !important;
            background-color: rgba(0, 0, 0, 0.3) !important;
            border: 0 !important;
            color: white !important;
            cursor: pointer;
        }

        .search_btn {
            border-radius: 0 15px 15px 0 !important;
            background-color: rgba(0, 0, 0, 0.3) !important;
            border: 0 !important;
            color: white !important;
            cursor: pointer;
        }

        .contacts {
            list-style: none;
            padding: 0;
        }

        .contacts li {
            width: 100% !important;
            padding: 5px 10px;
            margin-bottom: 15px !important;
        }


        .user_img {
            height: 70px;
            width: 70px;
            border: 1.5px solid #f5f6fa;

        }

        .user_img_msg {
            height: 40px;
            width: 40px;
            border: 1.5px solid #f5f6fa;

        }

        .img_cont {
            position: relative;
            height: 70px;
            width: 70px;
        }

        .img_cont_msg {
            height: 40px;
            width: 40px;
        }

        .online_icon {
            position: absolute;
            height: 15px;
            width: 15px;
            background-color: #4cd137;
            border-radius: 50%;
            bottom: 0.2em;
            right: 0.4em;
            border: 1.5px solid white;
        }

        .offline {
            background-color: #c23616 !important;
        }

        .user_info {
            margin-top: auto;
            margin-bottom: auto;
            margin-left: 15px;
        }

        .user_info span {
            font-size: 20px;
            color: white;
        }

        .user_info p {
            font-size: 10px;
            color: rgba(255, 255, 255, 0.6);
        }

        .video_cam {
            margin-left: 50px;
            margin-top: 5px;
        }

        .video_cam span {
            color: white;
            font-size: 20px;
            cursor: pointer;
            margin-right: 20px;
        }

        .msg_cotainer {
            margin-top: auto;
            margin-bottom: auto;
            margin-left: 10px;
            border-radius: 25px;
            background-color: #e8e9e5;
            padding: 10px;
            position: relative;
        }

        .msg_cotainer_send {
            margin-top: auto;
            margin-bottom: auto;
            margin-right: 10px;
            border-radius: 25px;
            background-color: #78e08f;
            padding: 10px;
            position: relative;
        }

        .msg_time {
            position: absolute;
            left: 0;
            bottom: -15px;
            color: rgba(0, 0, 0, 0.5);
            font-size: 10px;
        }

        .msg_time_send {
            position: absolute;
            right: 0;
            bottom: -15px;
            color: rgba(255, 255, 255, 0.5);
            font-size: 10px;
        }

        .msg_head {
            position: relative;
        }

        #action_menu_btn {
            position: absolute;
            right: 10px;
            top: 10px;
            color: white;
            cursor: pointer;
            font-size: 20px;
        }

        .action_menu {
            z-index: 1;
            position: absolute;
            padding: 15px 0;
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            border-radius: 15px;
            top: 30px;
            right: 15px;
            display: none;
        }

        .action_menu ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .action_menu ul li {
            width: 100%;
            padding: 10px 15px;
            margin-bottom: 5px;
        }

        .action_menu ul li i {
            padding-right: 10px;

        }

        .more-padding-on-right {
            padding-right: 100px;

        }

        .action_menu ul li:hover {
            cursor: pointer;
            background-color: rgba(0, 0, 0, 0.2);
        }

        .table-justify td {
            display: flex;
            justify-content: space-between;
        }

        .transaction-image {
            width: 150px;
            height: 150px;
            border-radius: 50px;
        }

        
        @media(max-width: 576px) {
            .contacts_card {
                margin-bottom: 15px !important;
            }
        }

    </style>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Transaction History
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li><a href="#">Home</a></li>/
                            <li>History</li>
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
                        @if (!$allTransactions->isEmpty())
                            <div class="card">
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-8">
                                            <h4>{{ $batchNumber }}</h4>
                                            <div class="card-body msg_card_body">
                                                @if (isset($transactionparentId))
                                                    @foreach ($transactionparentId as $key => $transaction)
                                                        <div class="d-flex justify-content-start mb-4">
                                                            <div class="img_cont_msg">
                                                                <img src="https://static.turbosquid.com/Preview/001292/481/WV/_D.jpg"
                                                                    class="rounded-circle user_img_msg">
                                                            </div>
                                                            <div class="msg_cotainer">

                                                                {{ App\User::find($transaction->created_by)->first_name . App\User::find($transaction->created_by)->last_name }}
                                                                ({{ $transaction->transaction_status }})
                                                                <br>
                                                                {{ $transaction->batch_number }}
                                                                <table style="width:100%;">



                                                                    @foreach ($transaction->details as $key => $detail)
                                                                        <tr class="">
                                                                <td>
                                                                    {{ $detail->container_number }}
                                                                </td>
                                                                @if (count($transaction->meta) == 0)
                                                                <td style="
                                                                            float:right;"
                                                                            class="">
                                                                    {{ $detail->container_weight }}

                                                                </td>
                                                                @else
                                                                <td >
                                                                    {{ $detail->container_weight }}

                                                                </td>
                                                                @endif
                                                            </tr>


                                                        @endforeach

                                                        @foreach ($transaction->meta as $key => $metas)
                                                            <tr>
                                                                <td>
                                                                    {{ ucwords(Str::of($metas->key)->replace('_', ' ')) }}

                                                                </td>
                                                                <td>
                                                                    {{ $metas->value }}
                                                                </td>
                                                            </tr>


                                                        @endforeach
                                                    </table>
                                                    <span class="
                                                                            msg_time">
                                                                            {{ $transaction->created_at }}</span>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endif
                                                @foreach ($allTransactions as $key => $allTransaction)
                                                    <div class="d-flex justify-content-start mb-4">
                                                        <div class="img_cont_msg">
                                                            <img src="https://static.turbosquid.com/Preview/001292/481/WV/_D.jpg"
                                                                class="rounded-circle user_img_msg">
                                                        </div>
                                                        <div class="msg_cotainer">

                                                            {{ App\User::find($allTransaction->created_by)->first_name . App\User::find($allTransaction->created_by)->last_name }}
                                                            ({{ $allTransaction->transaction_status }})
                                                            <br>
                                                            {{ $allTransaction->batch_number }}
                                                            <table style="width:100%;">



                                                                @foreach ($allTransaction->details as $key => $detail)
                                                                    <tr class="">
                                                                <td>
                                                                    {{ $detail->container_number }}
                                                                </td>
                                                                @if (count($allTransaction->meta) == 0)
                                                                <td style="
                                                                        float:right;"
                                                                        class="">
                                                                    {{ $detail->container_weight }}

                                                                </td>
                                                                @else
                                                                <td >
                                                                    {{ $detail->container_weight }}

                                                                </td>
                                                                @endif
                                                            </tr>


                                                        @endforeach

                                                        @foreach ($allTransaction->meta as $key => $metas)
                                                            <tr>
                                                                <td>
                                                                    {{ ucwords(Str::of($metas->key)->replace('_', ' ')) }}

                                                                </td>
                                                                <td>
                                                                    {{ $metas->value }}
                                                                </td>
                                                            </tr>


                                                        @endforeach
                                                    </table>
                                                    <span class="
                                                                        msg_time">
                                                                        {{ $allTransaction->created_at }}</span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                                @foreach ($transactionChild as $key => $child)
                                                    <div class="d-flex justify-content-start mb-4">
                                                        <div class="img_cont_msg">
                                                            <img src="https://static.turbosquid.com/Preview/001292/481/WV/_D.jpg"
                                                                class="rounded-circle user_img_msg">
                                                        </div>
                                                        <div class="msg_cotainer">

                                                            {{ App\User::find($child->created_by)->first_name . App\User::find($child->created_by)->last_name }}
                                                            ({{ $child->transaction_status }})
                                                            <br>
                                                            {{ $child->batch_number }}


                                                            <table style="width:100%;">

                                                                @foreach ($child->details as $key => $detail)
                                                                    <tr>
                                                                        <td>
                                                                            {{ $detail->container_number }}
                                                                        </td>
                                                                        <td>
                                                                            {{ $detail->container_weight }}

                                                                        </td>
                                                                    </tr>


                                                                @endforeach

                                                                @foreach ($child->meta as $key => $metas)
                                                                    <tr>
                                                                        <td>
                                                                            {{ ucwords(Str::of($metas->key)->replace('_', ' ')) }}

                                                                        </td>
                                                                        <td>
                                                                            {{ $metas->value }}
                                                                        </td>
                                                                    </tr>


                                                                @endforeach
                                                            </table>
                                                            <span class="msg_time">{{ $child->created_at }}</span>
                                                        </div>
                                                    </div>
                                                @endforeach

                                            </div>
                                        </div>
                                        <div class="col-4 mt-5">
                                            @if (!empty($invoiceName))
                                                @foreach ($invoiceName as $inv)
                                                    <img src="{{ asset('storage/app/images/' . $inv) }}" alt="No Image"
                                                        class="transaction-image">
                                                    <br>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>


                                </div>
                                <!-- /.card-body -->
                            </div>

                        @endif
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
            $('#transactionchild').DataTable();
        });
        $(document).ready(function() {
            $('#transactiionall').DataTable();
        });
    </script>
@endsection
