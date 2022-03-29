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
                @foreach ($transactions as $transaction)
                    <tr>

                        <td>
                            {{ $transaction['transaction']->transaction_id }}
                        </td>
                        <td>
                            @php
                                $farmers = parentBatch($transaction['transaction']->batch_number);
                            @endphp
                            @foreach ($farmers as $farmer)
                                @if ($farmer)
                                    {{ $farmer->farmer_name }} <br>
                                @endif
                            @endforeach
                        </td>
                        <td>
                            @php
                                $farmers = parentBatch($transaction['transaction']->batch_number);
                            @endphp
                            @foreach ($farmers as $farmer)
                                @if ($farmer)
                                    {{ $farmer->farmer_code }} <br>
                                @endif
                            @endforeach
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
                            @foreach ($farmers as $farmer)
                                @php
                                    if ($farmer) {
                                        $village = App\Village::where('village_code', $farmer->village_code)->first();
                                    }

                                @endphp
                                @if ($village->village_title)
                                    {{ $village->village_title }}
                                @endif
                                <br>
                            @endforeach
                        </td>
                        <td>
                            @foreach ($transaction['transaction']->transactionDetail as $detail)
                                {{ $detail->container_number . ':' . $detail->container_weight }}
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
