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

    #submitbtn {
        background-color: rgb(255, 255, 255);
        border-color: rgb(255, 255, 255);
        color: rgb(19, 17, 17);
        font-weight: bold;
    }

    .dataTables_wrapper .dataTables_filter input {
        margin-left: 0.5em;
        width: 240px;
    }

    .small-box>.inner {
        background-color: white;
        color: black;
    }

    .color {

        width: 200px;
        height: 80px fit-content;
        margin-left: 2px;
    }

    .set-width {

        width: 90px;
        height: 70px fit-content;
        background-color: purple !important;
    }

    a {

        color: rgb(0, 0, 0);
        background-color: transparent;
        text-decoration: none;

    }

    .blacklink .hover:hover {
        cursor: pointer;
    }

</style>
<style href="//cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css"></style>
<script src="//cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {

        $('#myTable').DataTable();

        });
</script>
<div class="col-md-12" >
    @if ($errors->any())
        <div class="alert alert-danger">

            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach

        </div>
    @endif
    <form class="milling-form" role="form" method="POST"
        action="{{ URL::to('admin/milling_coffee') }}">
        {{ csrf_field() }}
        <table
            class="milling-table table table-borderless border-0 custom-table text-center"
            style="border-collapse: separate;" id="myTable">
            <thead>
                <tr>
                    <th>Transaction id</th>
                    <th>Farmer Name</th>
                    <th>Farmer Code </th>
                    <th>Batch Number</th>
                    <th>product</th>
                    <th>Governerate</th>
                    <th>Region</th>
                    <th>VIllage</th>
                    <th>Quantity</th>
                    <th>Stage</th>
                    <th>Times</th>
                    <th> <button class="milling-link" type="submit" id="submitbtn"
                            class="btn btn-primary">Mix
                            Batches</button> </th>
                    <th id='milling-th'><button class="milling-link" type="submit"
                            id="submitbtn" class="btn btn-primary">Confirm
                            Milling</button></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transactions as $transaction)
                    <tr>
                        @if (Str::contains($transaction['transaction']->batch_number, '000'))
                            <td>

                                {{ $transaction['transaction']->transaction_id }}
                                <br>
                            </td>
                            <td>
                                @foreach ($transaction['child_transactions'] as $childtran)
                                    {{ getFarmer($childtran->batch_number) }} <br>
                                @endforeach
                            </td>

                            <td>
                                @foreach ($transaction['child_transactions'] as $childtran)
                                    {{ explode('-', $childtran->batch_number)[0] . '-' . explode('-', $childtran->batch_number)[1] . '-' . explode('-', $childtran->batch_number)[2] . '-' . explode('-', $childtran->batch_number)[3] }}
                                    <br>
                                @endforeach
                            </td>
                            <td>
                                @foreach ($transaction['child_transactions'] as $childtran)
                                    {{ $childtran->batch_number }} <br>
                                @endforeach
                            </td>
                            <td>
                                SPECIALTY
                            </td>
                            <td>
                                @foreach ($transaction['child_transactions'] as $childtran)
                                    {{ getGov($childtran->batch_number) }} <br>
                                @endforeach
                            </td>
                            <td>
                                @foreach ($transaction['child_transactions'] as $childtran)
                                    {{ getRegion($childtran->batch_number) }} <br>
                                @endforeach
                            </td>
                            <td>
                                @foreach ($transaction['child_transactions'] as $childtran)
                                    {{ getVillage($childtran->batch_number) }} <br>
                                @endforeach
                            </td>
                            <td>
                                @foreach ($transaction['child_transactions'] as $childtran)
                                    {{ $childtran->transactionDetail->sum('container_weight') }}
                                    <br>
                                @endforeach
                            </td>

                            <td>
                                {{ stagesOfSentTo($transaction['transaction']->sent_to) }}
                            </td>
                            <td>
                                @php
                                    $now = \Carbon\Carbon::now();
                                    $today = $now->today()->toDateString();
                                @endphp
                                {{ $transaction['transaction']->created_at->diffInDays($today) . ' ' . 'Days' }}
                            </td>
                            <td>
                                @php

                                    $batchNumber = $transaction['transaction']->batch_number;
                                    $batchExplode = explode('-', $batchNumber);
                                    $gov = $batchExplode[0];
                                @endphp
                                @if ($transaction['transaction']->sent_to == 13)
                                    <input type="checkbox" data-gov-rate="<?= $gov ?>"
                                        name="transaction_id[]"
                                        value="{{ $transaction['transaction']->transaction_id }}"
                                        class="check_gov{{ $transaction['transaction']->transaction_id }}"
                                        onClick="checkGov('<?= $gov ?>',{{ $transaction['transaction']->transaction_id }})">
                                @endif

                            </td>
                            <td>
                                @php

                                    $batchNumber = $transaction['transaction']->batch_number;
                                    $batchExplode = explode('-', $batchNumber);
                                    $gov = $batchExplode[0];
                                @endphp
                                @if ($transaction['transaction']->sent_to == 140)
                                    <input type="checkbox" data-gov-rate="<?= $gov ?>"
                                        name="transaction_id[]"
                                        value="{{ $transaction['transaction']->transaction_id }}"
                                        class="check_gov{{ $transaction['transaction']->transaction_id }}"
                                        onClick="checkGov('<?= $gov ?>',{{ $transaction['transaction']->transaction_id }})">
                                @endif
                            </td>
                        @else
                            <td>
                                {{ $transaction['transaction']->transaction_id }}
                            </td>
                            <td>
                                {{ getFarmer($transaction['transaction']->batch_number) }}
                            </td>
                            <td>
                                {{ explode('-', $transaction['transaction']->batch_number)[0] . '-' . explode('-', $transaction['transaction']->batch_number)[1] . '-' . explode('-', $transaction['transaction']->batch_number)[2] . '-' . explode('-', $transaction['transaction']->batch_number)[3] }}
                            </td>
                            <td>
                                {{ $transaction['transaction']->batch_number }}
                            </td>
                            <td>
                                SPECIALTY
                            </td>
                            <td>
                                {{ getGov($transaction['transaction']->batch_number) }}
                            </td>
                            <td>
                                {{ getRegion($transaction['transaction']->batch_number) }}
                            </td>
                            <td>
                                {{ getVillage($transaction['transaction']->batch_number) }}
                            </td>
                            <td>
                                {{ $transaction['transaction']->transactionDetail->sum('container_weight') }}
                            </td>
                            <td>
                                {{ stagesOfSentTo($transaction['transaction']->sent_to) }}
                            </td>
                            <td>
                                @php
                                    $now = \Carbon\Carbon::now();
                                    $today = $now->today()->toDateString();
                                @endphp
                                {{ $transaction['transaction']->created_at->diffInDays($today) . ' ' . 'Days' }}
                            </td>
                            <td>
                                @php

                                    $batchNumber = $transaction['transaction']->batch_number;
                                    $batchExplode = explode('-', $batchNumber);
                                    $gov = $batchExplode[0];
                                @endphp
                                @if ($transaction['transaction']->sent_to == 13)
                                    <input type="checkbox" data-gov-rate="<?= $gov ?>"
                                        name="transaction_id[]"
                                        value="{{ $transaction['transaction']->transaction_id }}"
                                        class="check_gov{{ $transaction['transaction']->transaction_id }}"
                                        onClick="checkGov('<?= $gov ?>',{{ $transaction['transaction']->transaction_id }})">
                                @endif

                            </td>
                            <td>
                                @php

                                    $batchNumber = $transaction['transaction']->batch_number;
                                    $batchExplode = explode('-', $batchNumber);
                                    $gov = $batchExplode[0];
                                @endphp
                                @if ($transaction['transaction']->sent_to == 140)
                                    <input type="checkbox" data-gov-rate="<?= $gov ?>"
                                        name="transaction_id[]"
                                        value="{{ $transaction['transaction']->transaction_id }}"
                                        class="check_gov{{ $transaction['transaction']->transaction_id }}"
                                        onClick="checkGov('<?= $gov ?>',{{ $transaction['transaction']->transaction_id }})">
                                @endif
                            </td>
                        @endif

                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="card-footer">

        </div>
        <form>
</div>
