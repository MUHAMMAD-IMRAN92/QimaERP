@extends('layouts.default')
@section('title', 'Transection Detail')
@section('content')

<style type="text/css">
    .nav.nav-tabs {
    float: left;
    display: block;
    margin-right: 20px;
    border-bottom:0;
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
    border-top-left-radius: 0rem!important;
    border-top-right-radius: 0rem!important;
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
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><b>Batch Number</b>: {{$batch->batch_number}}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->

    <?php
    foreach ($transations_data as $key => $transations) {
        $centerManagerReceviedCoffee = null;
        $activeTab = 1;
        $coffeeSentDate = null;
        $coffeeRecDate = null;
        if (isset($transations->sent_transaction) && $transations->sent_transaction) {
            $activeTab = 2;
            $coffeeSentDate = $transations->sent_transaction->created_at;
            if (isset($transations->sent_transaction->center_manager_received_transaction) && $transations->sent_transaction->center_manager_received_transaction) {
                $activeTab = 3;
                $coffeeRecDate = $transations->sent_transaction->center_manager_received_transaction->created_at;
            }
        }
        ?>
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="card col-lg-12">
                        <!-- /.card-header -->


                        <div class="card-body">
                            <div class="col-12">
                                <div class="bs-example">
                                    <ul class="nav nav-tabs" id="custom_tab">
                                        <li class="nav-item">
                                            <a href="#coffee_buyer{{$transations->transaction_id}}" class="nav-link <?= $activeTab == 1 ? 'active' : ''; ?>" data-toggle="tab">Coffee Buyer<br><span style="text-align:center">{{date("d M Y", strtotime($transations->created_at))}}</span></a>
                                        </li>
                                        @if($activeTab >= 2)
                                        <li class="nav-item">
                                            <a href="#coffee_buyer_manager{{$transations->transaction_id}}" class="nav-link <?= $activeTab == 2 ? 'active' : ''; ?>" data-toggle="tab">Coffee Buyer Manager<br><span style="text-align:center">{{date("d M Y", strtotime($coffeeSentDate))}}</a>
                                        </li>
                                        @endif
                                        @if($activeTab >= 3)
                                        <li class="nav-item">
                                            <a href="#center_manager{{$transations->transaction_id}}" class="nav-link <?= $activeTab == 3 ? 'active' : ''; ?>" data-toggle="tab">Center Manager<br><span style="text-align:center">{{date("d M Y", strtotime($coffeeRecDate))}}</a>
                                        </li>
                                        @endif
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane fade <?= $activeTab == 1 ? 'show active' : ''; ?>" id="coffee_buyer{{$transations->transaction_id}}">
                                            <?php if ($transations->is_mixed == 1) { ?>
                                                <div class="card">
                                                    <!-- /.card-header -->
                                                    <div class="card-body">
                                                        <?php foreach ($transations->childTransation as $key => $child_transation) {
                                                            ?>

                                                            <table class="table table-bordered">
                                                                <tbody>
                                                                <p><b>Batch Number</b>: {{$child_transation->batch_number}}</p>

                                                                <tr>
                                                                    <th>Container</th>
                                                                    <th>Weight</th>
                                                                </tr>

                                                                </tbody>


                                                                @foreach($child_transation->transactionDetail as $child)

                                                                <tr>

                                                                    <td>
                                                                        {{$child->container_number}}
                                                                    </td>
                                                                    <td>
                                                                        {{$child->container_weight}} kg
                                                                    </td>


                                                                </tr>
                                                                @endforeach
                                                            </table>


                                                        <?php }
                                                        ?>
                                                    </div>
                                                </div>
                                            <?php } else { ?>
                                                <div class="card">
                                                    <!-- /.card-header -->
                                                    <div class="card-body">

                                                        <table class="table table-bordered">
                                                            <tbody>
                                                            <p><b>Batch Number</b>: {{$transations->batch_number}}</p>

                                                            <tr>
                                                                <th>Container</th>
                                                                <th>Weight</th>
                                                            </tr>

                                                            </tbody>


                                                            @foreach($transations->transactionDetail as $child)

                                                            <tr>

                                                                <td>
                                                                    {{$child->container_number}}
                                                                </td>
                                                                <td>
                                                                    {{$child->container_weight}} kg
                                                                </td>


                                                            </tr>
                                                            @endforeach
                                                        </table>
                                                    </div> 
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <div class="tab-pane fade <?= $activeTab == 2 ? 'show active' : ''; ?>" id="coffee_buyer_manager{{$transations->transaction_id}}">
                                            <?php
                                            if (isset($transations->sent_transaction) && $transations->sent_transaction) {
                                                if (isset($transations->sent_transaction->center_manager_received_transaction) && $transations->sent_transaction->center_manager_received_transaction) {
                                                    $centerManagerReceviedCoffee = $transations->sent_transaction->center_manager_received_transaction;
                                                }
                                                ?>
                                                <div class="card">
                                                    <!-- /.card-header -->
                                                    <div class="card-body">

                                                        <table class="table table-bordered">
                                                            <tbody>
                                                            <p><b>Batch Number</b>: {{$transations->sent_transaction->batch_number}}</p>

                                                            <tr>
                                                                <th>Container</th>
                                                                <th>Weight</th>
                                                            </tr>

                                                            </tbody>


                                                            @foreach($transations->sent_transaction->transactionDetail as $child)

                                                            <tr>

                                                                <td>
                                                                    {{$child->container_number}}
                                                                </td>
                                                                <td>
                                                                    {{$child->container_weight}} kg
                                                                </td>


                                                            </tr>
                                                            @endforeach
                                                        </table>
                                                    </div> 
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <div class="tab-pane fade <?= $activeTab == 3 ? 'show active' : ''; ?>" id="center_manager{{$transations->transaction_id}}">
                                            <?php if ($centerManagerReceviedCoffee) { ?>
                                                <div class="card">
                                                    <!-- /.card-header -->
                                                    <div class="card-body">

                                                        <table class="table table-bordered">
                                                            <tbody>
                                                            <p><b>Batch Number</b>: {{$centerManagerReceviedCoffee->batch_number}}</p>

                                                            <tr>
                                                                <th>Container</th>
                                                                <th>Weight</th>
                                                            </tr>

                                                            </tbody>


                                                            @foreach($centerManagerReceviedCoffee->transactionDetail as $child)

                                                            <tr>

                                                                <td>
                                                                    {{$child->container_number}}
                                                                </td>
                                                                <td>
                                                                    {{$child->container_weight}} kg
                                                                </td>


                                                            </tr>
                                                            @endforeach
                                                        </table>
                                                    </div> 
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
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
    <?php } ?> 
    <!-- /.content -->

</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('#transactionchild').DataTable();
    });
    $(document).ready(function () {
        $('#transactiionall').DataTable();
    });
</script>
@endsection