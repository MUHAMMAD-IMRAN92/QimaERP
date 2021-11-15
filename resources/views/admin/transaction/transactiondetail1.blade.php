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
        #myImg {
        border-radius: 5px;
        cursor: pointer;
        transition: 0.3s;
    }

    #myImg:hover {
        opacity: 0.7;
    }

    /* The Modal (background) */
    .modal {
        display: none;
        /* Hidden by default */
        position: fixed;
        /* Stay in place */
        z-index: 1;
        /* Sit on top */
        padding-top: 100px;
        /* Location of the box */
        left: 0;
        top: 0;
        width: 100%;
        /* Full width */
        height: 100%;
        /* Full height */
        overflow: auto;
        /* Enable scroll if needed */
        background-color: rgb(0, 0, 0);
        /* Fallback color */
        background-color: rgba(0, 0, 0, 0.9);
        /* Black w/ opacity */
    }

    /* Modal Content (image) */
    .modal-content {
        margin: auto;
        display: block;
        width: 80%;
        max-width: 700px;
    }
    .pad-5{
        padding: 5px;
    }

    /* Caption of Modal Image */
    #caption {
        margin: auto;
        display: block;
        width: 80%;
        max-width: 700px;
        text-align: center;
        color: #ccc;
        padding: 10px 0;
        height: 150px;
    }

    /* Add Animation */
    .modal-content,
    #caption {
        -webkit-animation-name: zoom;
        -webkit-animation-duration: 0.6s;
        animation-name: zoom;
        animation-duration: 0.6s;
    }

    @-webkit-keyframes zoom {
        from {
            -webkit-transform: scale(0)
        }

        to {
            -webkit-transform: scale(1)
        }
    }

    @keyframes zoom {
        from {
            transform: scale(0)
        }

        to {
            transform: scale(1)
        }
    }

    /* The Close Button */
    .close {
        position: absolute;
        top: 15px;
        right: 35px;
        color: #f1f1f1;
        font-size: 40px;
        font-weight: bold;
        transition: 0.3s;
    }

    .close:hover,
    .close:focus {
        color: #bbb;
        text-decoration: none;
        cursor: pointer;
    }

    /* 100% Image Width on Smaller Screens */
    @media only screen and (max-width: 700px) {
        .modal-content {
            width: 100%;
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
                        <div class="card">
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-8">
                                        <h4>{{ $data['batchNumber'] }}</h4>
                                        <div class="card-body msg_card_body">

                                            @foreach ($data1 as $key => $transaction)
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
                                                                                    float:right;" class="">
                                                                            {{ $detail->container_weight }}

                                                                        </td>
                                                                    @else
                                                                        <td>
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
                                                        <span
                                                            class="
                                                                    msg_time">
                                                            {{ $transaction->created_at }}</span>
                                                    </div>
                                                   
                                                </div>
                                            @endforeach


                                        </div>
                                    </div>
                                    <div class="col-4 mt-5">
                                        @if (!empty($data['invoiceName']))
                                            @php
                                                $i = 0;
                                            @endphp
                                            @foreach ($data['invoiceName'] as $inv)
                                                <img src="{{ Storage::disk('s3')->url('images/' . $inv) }}"
                                                    alt="No Image" onclick="showModal('image_<?= $i ?>')"
                                                    id="image_<?= $i ?>" class="transaction-image">
                                                <br>
                                                <br>
                                            @endforeach
                                          
                                        @endif
                                       
                                    </div>
                                </div>
                                <div id="myModal" class="modal">
                                    <span class="close">&times;</span>
                                    <img class="modal-content" id="img01">
                                    <div id="caption"></div>
                                </div>

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
            $('#transactionchild').DataTable();
        });
        $(document).ready(function() {
            $('#transactiionall').DataTable();
        });
    </script>
    <script>
        function showModal(id) {
            var modal = document.getElementById("myModal");

            // Get the image and insert it inside the modal - use its "alt" text as a caption
            var img = document.getElementById(id);
            // console.log(img.src);
            var modalImg = document.getElementById("img01");
            var captionText = document.getElementById("caption");
            // img.onclick = function() {
            modal.style.display = "block";
            modalImg.src = img.src;
            // console.log(modalImg.src);
            captionText.innerHTML = this.alt;
            // }

            // Get the <span> element that closes the modal
        }
        // Get the modal


        // When the user clicks on <span> (x), close the modal
        $('.close').on('click', function() {
            var modal = document.getElementById("myModal");
            modal.style.display = "none";
        });
        // var span = document.getElementsByClassName("close")[0];
        // span
    </script>
@endsection
