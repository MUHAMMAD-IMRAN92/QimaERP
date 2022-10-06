<div class="col-md-12">
    @if ($errors->any())
        <div class="alert alert-danger">

            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach

        </div>
    @endif
    <form class="milling-form" role="form" method="POST" action="{{ URL::to('admin/milling_coffee') }}">
        {{ csrf_field() }}
        <table class="milling-table table table-borderless border-0 custom-table text-center"
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
                    <th> <button class="milling-link" type="submit" id="mix">Mix
                            Batches</button> </th>
                    <th id='milling-th'><button class="milling-link" type="submit" id="millingbtn">Confirm
                            Milling</button></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transactions as $key => $transaction)
                    <tr>

                        <td>
                            {{ $transaction['transaction']->transaction_id }}
                        </td>
                        <td>
                            @php
                                $farmers = parentBatch($transaction['transaction']->batch_number);
                            @endphp
                            @foreach ($farmers as $keyf => $farmer)
                            @if ($keyf == 0)
                                @if ($farmer)
                                    {{ $farmer->farmer_name }} <br>
                                @endif @endif
                            @endforeach<i class="fa fa-info-circle" aria-hidden="true"
                            class="btn btn-primary" data-toggle="modal"
                            data-target="#exampleModalCenter{{ $key }}"></i>
                        </td>
                        <td>
                            @php
                                $farmers = parentBatch($transaction['transaction']->batch_number);
                            @endphp
                            @foreach ($farmers as $keyf => $farmer)
                                @if ($keyf == 0)
                                    @if ($farmer)
                                        {{ $farmer->farmer_code }} <br>
                                    @endif
                                @endif
                            @endforeach <i class="fa fa-info-circle" aria-hidden="true"
                                class="btn btn-primary" data-toggle="modal"
                                data-target="#exampleModalCenter{{ $key }}"></i>
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
                            {{-- {{ getVillage($transaction['transaction']->batch_number) }} --}}
                            @php
                                $farmers = parentBatch($transaction['transaction']->batch_number);
                            @endphp
                            @foreach ($farmers as $keyf => $farmer)
                                @php
                                    if ($farmer) {
                                        $village = App\Village::where('village_code', $farmer->village_code)->first();
                                    }

                                @endphp
                                @if ($keyf == 0)
                                    @if ($village->village_title)
                                        {{ $village->village_title }}
                                    @endif
                                @endif
                            @endforeach
                            <i class="fa fa-info-circle" aria-hidden="true" class="btn btn-primary" data-toggle="modal"
                                data-target="#exampleModalCenter{{ $key }}"></i>
                        </td>
                        <td>
                            @foreach ($transaction['transaction']->transactionDetail as  $keyf => $detail)
                            @if ($keyf == 0)
                                {{ $detail->container_number . ':' . $detail->container_weight }}
                                @endif
                            @endforeach
                            <i class="fa fa-info-circle" aria-hidden="true" class="btn btn-primary" data-toggle="modal"
                                data-target="#exampleModalCenter{{ $key }}"></i>
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
                                <input type="checkbox" data-gov-rate="<?= $gov ?>" name="transaction_id[]"
                                    value="{{ $transaction['transaction']->transaction_id }}"
                                    class="check_gov{{ $transaction['transaction']->transaction_id }} checkBox13"
                                    onClick="checkGov('<?= $gov ?>',{{ $transaction['transaction']->transaction_id }})">
                            @endif

                        </td>
                        <td>

                            @if ($transaction['transaction']->sent_to == 140)
                                <input type="checkbox" data-gov-rate="<?= $gov ?>" name="transaction_id[]"
                                    value="{{ $transaction['transaction']->transaction_id }}" class="checkSentTo140"
                                    onclick="disableFun()">
                            @endif
                        </td>
                        <div class="modal fade" id="exampleModalCenter{{ $key }}" tabindex="-1"
                            role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered  modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLongTitle">
                                            {{ $transaction['transaction']->batch_number }}
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">

                                            <div class="col-md-3 borderClass">
                                                <h3>Farmers</h3>
                                                <hr>
                                                <ul class="list-group list-group-flush">
                                                    @foreach ($farmers as $keyf => $farmer)
                                                        @if ($farmer)
                                                            <li class="list-group-item">
                                                                {{ $farmer->farmer_name }}
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            </div>
                                            <div class="col-md-3 borderClass">
                                                <h3>Farmer Code</h3>
                                                <hr>
                                                <ul class="list-group list-group-flush">
                                                    @foreach ($farmers as $keyf => $farmer)
                                                        @if ($farmer)
                                                            <li class="list-group-item">
                                                                {{ $farmer->farmer_code }}
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            </div>
                                            <div class="col-md-3 borderClass">
                                                <h3>Villages</h3>
                                                <hr>
                                                <span>
                                                    <ul class="list-group list-group-flush">
                                                        @foreach ($farmers as $farmer)
                                                            @php
                                                                if ($farmer) {
                                                                    $village = App\Village::where('village_code', $farmer->village_code)->first();
                                                                }

                                                            @endphp
                                                            @if ($village->village_title)
                                                                <li class="list-group-item">
                                                                    {{ $village->village_title }}
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                </span>
                                            </div>
                                            <div class="col-md-3 borderClass">
                                                <h3>Containers</h3>
                                                <hr>
                                                <ul class="list-group list-group-flush">
                                                    @foreach ($transaction['transaction']->transactionDetail as $keyf => $detail)
                                                        <li class="list-group-item">
                                                            {{ $detail->container_number . ':' }}
                                                            <b>{{ $detail->container_weight }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>

                                        </div>
                                    </div>

                                    <br>


                                </div>
                                {{-- <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    data-dismiss="modal">Close</button>
                                <button type="button"
                                    class="btn btn-primary">Save
                                    changes</button>
                            </div> --}}
                            </div>
                        </div>

                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="card-footer">

        </div>
        <form>
</div>
<script>
    $(document).ready(function() {

        $('#myTable').DataTable({
            columnDefs: [{
                orderable: false,
                targets: [11, 12]
            }],
            order: [
                [1, 'asc']
            ]
        });

        function disableFun() {
            $('.checkBox13').prop('checked', false);
            if ($(".checkSentTo140:checkbox:checked").length > 0) {
                console.log($(".checkSentTo140:checkbox:checked").length);
                $('#mix').prop('disabled', true);
                $('#millingbtn').attr('disabled', false);
            } else {
                console.log($(".checkSentTo140:checkbox:checked").length);
                $('#mix').prop('disabled', false);
            }
        }
        var gov = null;

        function checkGov(checkgov, id) {
            $('.checkSentTo140').prop('checked', false);

            if ($(".checkBox13:checkbox:checked").length > 0) {
                $('#millingbtn').attr('disabled', true);
                $('#mix').prop('disabled', false);
            } else {
                $('#millingbtn').attr('disabled', false);
            }
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

        $(document).ready(function() {
            $('#submitbtn').on('click', function() {
                $('#submitbtn').hide();
            });
            $('#milling-th').on('click', function() {
                console.log('imran');
                $attr = $('form').attr('action', '{{ URL::to('admin/newMilliing') }}');
            });
        });

        const slider = document.querySelector(".milling-form");
        const preventClick = (e) => {
            e.preventDefault();
            e.stopImmediatePropagation();
        }
        let isDown = false;
        let isDragged = false;
        let startX;
        let scrollLeft;

        slider.addEventListener("mousedown", e => {
            isDown = true;
            slider.classList.add("active");
            startX = e.pageX - slider.offsetLeft;
            scrollLeft = slider.scrollLeft;
        });
        slider.addEventListener("mouseleave", () => {
            isDown = false;
            slider.classList.remove("active");
        });
        slider.addEventListener("mouseup", (e) => {
            isDown = false;
            const elements = document.querySelectorAll(".milling-table a");
            if (isDragged) {
                for (let i = 0; i < elements.length; i++) {
                    elements[i].addEventListener("click", preventClick);
                }
            } else {
                for (let i = 0; i < elements.length; i++) {
                    elements[i].removeEventListener("click", preventClick);
                }
            }
            slider.classList.remove("active");
            isDragged = false;
        });
        slider.addEventListener("mousemove", e => {
            if (!isDown) return;
            isDragged = true;
            e.preventDefault();
            const x = e.pageX - slider.offsetLeft;
            const walk = (x - startX) * 2;
            slider.scrollLeft = scrollLeft - walk;
            console.log(walk);
        });
    });
</script>
