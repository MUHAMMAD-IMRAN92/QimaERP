@extends('layouts.default')
@section('title', 'Transection Detail')
@section('content')

    <style type="text/css">
        .nav.nav-tabs {
            float: left;
            display: block;
            margin-right: 20px;
            border-bottom: 0;
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
            border-top-left-radius: 0rem !important;
            border-top-right-radius: 0rem !important;
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

        .batchnumber thead tr {
            border-bottom: 1px solid black;
        }

        .batchnumber tbody tr {
            border-bottom: 1px solid black;
        }

        .set-padding {
            padding: 10px;
        }

        .top-margin-set {
            margin-top: 10px;
        }

    </style>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1><b>Milling Coffee</b></h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Milling Coffee</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="card col-lg-12">
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="col-md-6">
                                @if ($errors->any())
                                    <div class="alert alert-danger">

                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach

                                    </div>
                                @endif
                                <?php foreach ($transactions as $key => $transaction) { ?>

                                <div class="card card-info">
                                    <div class="card-header">
                                        <h3 class="card-title">Session Number:
                                            {{ $transaction[0]['transaction']->session_no }} </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <form role="form" method="POST"
                                                action="{{ URL::to('admin/milling_coffee') }}">
                                                {{ csrf_field() }}
                                                <?php foreach ($transaction as $key => $trans) {

                                                $batchNumber = $trans['transaction']->batch_number;
                                                $batchExplode = explode('-', $batchNumber);
                                                $gov = $batchExplode[0];
                                                ?>
                                                <div class="col-md-12">
                                                    <div class="card set-padding">
                                                        <div class="row">
                                                            <div class="col-md-12 top-margin-set">
                                                                <h4 class="card-title">Batch Number: {{ $batchNumber }}
<<<<<<< HEAD
                                                                    <input type="checkbox" data-gov-rate="<?= $gov ?>"
                                                                        name="transaction_id[]"
                                                                        value="{{ $trans['transaction']->transaction_id }}"
                                                                        class="check_gov{{ $trans['transaction']->transaction_id }}"
                                                                        onClick="checkGov('<?= $gov ?>',{{ $trans['transaction']->transaction_id }})">
                                                                </h4>
=======
                                                                    <input type="checkbox" data-gov-rate="<?= $gov ?>" name="transaction_id[]"value="{{ $trans['transaction']->transaction_id }}" class="check_gov{{ $trans['transaction']->transaction_id }}" onClick="checkGov('<?= $gov ?>',{{ $trans['transaction']->transaction_id }})"></h4>
<<<<<<< HEAD
                                                 </div>   
                                                                                              @foreach ($trans['transactionDetails'] as $detail)  
                                                                                                       
                                                                                                                                                                  
                                                                                                        <div class="col-md-6"> {{ $detail['container_number'] }}</div>
                                                                                                        <div class="col-md-6">{{ $detail['container_weight'] }}</div>
                                                                                                    
                                                                                                        
                                                                                                   
                                                                                              @endforeach
                                                                                               
                                                                                                <div class="col-md-12">
                                                                                                    <table class="batchnumber">
                                                                                                        <thead>
                                                                                                            <tr>
                                                                                                                <th>Farmer Code</th>
                                                                                                            </tr>
                                                                                                        </thead>
                                                                                                        <tbody>
                                                                                                            <?php
                                                                                                            $removeLocalId = explode('-', $batchNumber);
                                                                                                            if ($removeLocalId[3] != '000') {

                                                                                                                array_pop($removeLocalId);
                                                                                                                $farmerCode = implode('-', $removeLocalId);
                                                                                                                ?>
                                                                                                                <tr>
                                                                                                                    <td>{{ $farmerCode }}</td>
                                                                                                                </tr>
                                                                                                                <?php
                                                                                                            } else {
                                                                                                                $childTransactions = $trans['child_transactions'];
                                                                                                                foreach ($childTransactions as $key => $childTransaction) {

                                                                                                                    $removeLastIndex = explode('-', $childTransaction->batch_number);
                                                                                                                    array_pop($removeLastIndex);
                                                                                                                    $farmerCode = implode('-', $removeLastIndex);
                                                                                                                    ?>
                                                                                                                    <tr><td>{{ $farmerCode }}</td></tr>
                                                                                                                    <?php
                                                                                                                }
                                                                                                            }
                                                                                                            ?>
                                                                                                        </tbody>
                                                                                                    </table>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                <?php
                                                    } ?>

                                                                                <div class="card-footer">
                                                                                    <button type="submit" class="btn btn-primary">Submit</button>
                                                                                </div>
                                                                            </form>

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php } ?>
=======
             </div>   
                                                          <div class="row ml-2"> 
                                                                    @foreach ($details as $detail)  
                                                                    @foreach ($detail as $d)
                                                                                                                              
                                                                    <div class="col-md-6"> {{ $d['container_number'] }}</div>
                                                                    <div class="col-md-6">{{ $d['container_weight'] }}</div>
                                                                
                                                                    
                                                                 @endforeach
                                                                    
                                                                @endforeach
>>>>>>> 9675e961ffef118aab07d153503dec1fdd3a1dbf
                                                            </div>
                                                            @foreach ($trans['transactionDetails'] as $detail)

                                                                <div class="col-md-6"> {{ $detail['container_number'] }}
                                                                </div>
                                                                <div class="col-md-6">{{ $detail['container_weight'] }}
                                                                </div>

                                                            @endforeach

                                                            <div class="col-md-12">
                                                                <table class="batchnumber">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Farmer Code</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php
                                                                                                            $removeLocalId = explode('-', $batchNumber);
                                                                                                            if ($removeLocalId[3] != '000') {

                                                                                                                array_pop($removeLocalId);
                                                                                                                $farmerCode = implode('-', $removeLocalId);
                                                                                                                ?>
                                                                        <tr>
                                                                            <td>{{ $farmerCode }}</td>
                                                                        </tr>
                                                                        <?php
                                                                                                            } else {
                                                                                                                $childTransactions = $trans['child_transactions'];
                                                                                                                foreach ($childTransactions as $key => $childTransaction) {

                                                                                                                    $removeLastIndex = explode('-', $childTransaction->batch_number);
                                                                                                                    array_pop($removeLastIndex);
                                                                                                                    $farmerCode = implode('-', $removeLastIndex);
                                                                                                                    ?>
                                                                        <tr>
                                                                            <td>{{ $farmerCode }}</td>
                                                                        </tr>
                                                                        <?php
                                                                                                                }
                                                                                                            }
                                                                                                            ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
>>>>>>> b07d7e512ea72425ac66d932f3a39b12e6e3133d
                                                        </div>
                                                    </div>
                                                </div>
<<<<<<< HEAD
                                                <?php
                                                    } ?>

                                                <div class="card-footer">
                                                    <button type="submit" class="btn btn-primary">Submit</button>
                                                </div>
                                            </form>

                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <script>
        var gov = null;

        function checkGov(checkgov, id) {

            //alert(id);
            if (gov == null) {
                gov = checkgov;
            } else {
                if (gov != checkgov) {
                    if ($('.check_gov' + id).prop("checked") == true) {
                        alert("You can not mix two different governerates")
                        $('.check_gov' + id).prop('checked', false);
                    }

                }
            }
            checkBoxCount = $('input[type="checkbox"]:checked').length;
            if (checkBoxCount == 0) {
                console.log(checkBoxCount);
                gov = null;
            }
        }
    </script>
=======
                                                <!-- /.col -->
                                            </div>
                                            <!-- /.row -->
                                        </div>
                                        <!-- /.container-fluid -->
                                        </section>
                                        <!-- /.content -->
                                        </div>
                                        <script>
                                            var gov = null;

                                            function checkGov(checkgov, id) {

                                                //alert(id);
                                                if (gov == null) {
                                                    gov = checkgov;
                                                } else {
                                                    if (gov != checkgov) {
                                                        if ($('.check_gov' + id).prop("checked") == true) {
                                                            alert("You can not mix two different governerates")
                                                            $('.check_gov' + id).prop('checked', false);
                                                        }

                                                    }
                                                }
                                                checkBoxCount = $('input[type="checkbox"]:checked').length;
                                                if (checkBoxCount == 0) {
                                                    console.log(checkBoxCount);
                                                    gov = null;
                                                }
                                            }
                                        </script>
>>>>>>> 9675e961ffef118aab07d153503dec1fdd3a1dbf
@endsection
