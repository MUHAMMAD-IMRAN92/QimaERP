<script>
    $(document).ready(function() {

        $('#myTable').DataTable({
            columnDefs: [{
                orderable: false,
                targets: [11, 13, 14]
            }],
            order: [
                [1, 'asc']
            ]
        });
        $('#to').on('change', function() {
            let from = $('#from').val();
            let to = $('#to').val();

            $.ajax({

                url: "{{ url('admin/lot_mixing/betweenDate') }}",
                type: "GET",
                data: {
                    'from': from,
                    'to': to
                },
                success: function(data) {
                    $('#table-body').html(data);
                    console.log(data);
                }
            });
        });
        $('#governorate_dropdown').on('change', function(e) {
            // let from = $('#governorate_dropdown').val();
            $('.all_regions').css({
                'font-weight': 'normal',
                'text-decoration': 'none'
            });
            let from = e.target.value;
            $.ajax({
                url: "{{ url('admin/lot_mixing/filterLotMixingByGovernrate') }}",
                type: "GET",
                data: {
                    'from': from,

                },
                success: function(data) {
                    console.log(data);
                    $('#regions_dropdown').empty();

                    let html =
                        ' <option value="0" selected disabled>Select Region</option>';
                    data.regions.forEach(region => {
                        html += '<option value="' + region.region_id + '">' + region
                            .region_title + '</option>';
                    });

                    $('#regions_dropdown').append(html);
                    $('#table-body').html(data.view);

                }
            });
        });
        $('#regions_dropdown').on('change', function(e) {
            // let from = $('#regions_dropdown').val();
            $('.all_regions').css({
                'font-weight': 'normal',
                'text-decoration': 'none'
            });

            let from = e.target.value;
            $.ajax({
                url: "{{ url('admin/lot_mixing/filterLotMixingByRegion') }}",
                type: "GET",
                data: {
                    'from': from,

                },
                success: function(data) {
                    $('#village_dropdown').empty();
                    let html =
                        ' <option value="0" selected disabled>Select Village</option>';
                    data.villages.forEach(village => {
                        html += '<option value="' + village.village_id + '">' +
                            village
                            .village_title + '</option>';
                    });


                    $('#village_dropdown').append(html);
                    $('#table-body').html(data.view);
                    console.log(data);


                }
            });
        });
        $('#village_dropdown').on('change', function(e) {
            // let from = $('#regions_dropdown').val();
            $('.all_regions').css({
                'font-weight': 'normal',
                'text-decoration': 'none'
            });

            let from = e.target.value;
            $.ajax({
                url: "{{ url('admin/lot_mixing/filterLotMixingByvillage') }}",
                type: "GET",
                data: {
                    'from': from,

                },
                success: function(data) {
                    $('#table-body').html(data.view);
                    console.log(data);
                }
            });
        });
        $('#today').on('click', function() {
            $('.blacklink .hover').css({
                'font-weight': 'normal',
                'text-decoration': 'none'
            });
            $(this).css({
                'font-weight': 'bold',
                'text-decoration': 'underline'
            });
            $('#loader').css('display', 'block');
            $.ajax({
                url: "{{ url('admin/lot_mixing/filterByDays') }}",
                type: "GET",
                data: {
                    'date': 'today'
                },
                success: function(data) {

                    $('#table-body').html(data);

                    $('#loader').css('display', 'none');

                    console.log(data);
                }
            });
        });
        $('#yesterday').on('click', function() {
            $('.blacklink .hover').css({
                'font-weight': 'normal',
                'text-decoration': 'none'
            });
            $(this).css({
                'font-weight': 'bold',
                'text-decoration': 'underline'
            });
            $('#loader').css('display', 'block');
            $.ajax({
                url: "{{ url('admin/lot_mixing/filterByDays') }}",

                type: "GET",
                data: {
                    'date': 'yesterday'
                },
                success: function(data) {

                    $('#table-body').html(data);
                    $('#loader').css('display', 'none');

                    console.log(data);
                }
            });
        });
        $('#weekToDate').on('click', function() {
            $('.blacklink .hover').css({
                'font-weight': 'normal',
                'text-decoration': 'none'
            });
            $(this).css({
                'font-weight': 'bold',
                'text-decoration': 'underline'
            });
            $('#loader').css('display', 'block');
            $.ajax({
                url: "{{ url('admin/lot_mixing/filterByDays') }}",

                type: "GET",
                data: {
                    'date': 'weekToDate'
                },
                success: function(data) {

                    $('#table-body').html(data);
                    $('#loader').css('display', 'none');

                    console.log(data);
                }
            });
        });
        $('#monthToDate').on('click', function() {
            $('.blacklink .hover').css({
                'font-weight': 'normal',
                'text-decoration': 'none'
            });
            $(this).css({
                'font-weight': 'bold',
                'text-decoration': 'underline'
            });
            $('#loader').css('display', 'block');
            $.ajax({
                url: "{{ url('admin/lot_mixing/filterByDays') }}",

                type: "GET",
                data: {
                    'date': 'monthToDate'
                },
                success: function(data) {

                    $('#table-body').html(data);
                    $('#loader').css('display', 'none');

                    console.log(data);
                }
            });
        });
        $('#lastmonth').on('click', function() {
            $('.blacklink .hover').css({
                'font-weight': 'normal',
                'text-decoration': 'none'
            });
            $(this).css({
                'font-weight': 'bold',
                'text-decoration': 'underline'
            });
            $('#loader').css('display', 'block');
            $.ajax({
                url: "{{ url('admin/lot_mixing/filterByDays') }}",

                type: "GET",
                data: {
                    'date': 'lastmonth'
                },
                success: function(data) {

                    $('#table-body').html(data);
                    $('#loader').css('display', 'none');

                    console.log(data);
                }
            });
        });
        $('#yearToDate').on('click', function() {
            $('.blacklink .hover').css({
                'font-weight': 'normal',
                'text-decoration': 'none'
            });
            $(this).css({
                'font-weight': 'bold',
                'text-decoration': 'underline'
            });
            $('#loader').css('display', 'block');
            $.ajax({
                url: "{{ url('admin/lot_mixing/filterByDays') }}",

                type: "GET",
                data: {
                    'date': 'yearToDate'
                },
                success: function(data) {

                    $('#table-body').html(data);
                    $('#loader').css('display', 'none');

                    console.log(data);
                }
            });
        });
        $('#currentyear').on('click', function() {
            $('.blacklink .hover').css({
                'font-weight': 'normal',
                'text-decoration': 'none'
            });
            $(this).css({
                'font-weight': 'bold',
                'text-decoration': 'underline'
            });
            $('#loader').css('display', 'block');
            $.ajax({
                url: "{{ url('admin/lot_mixing/filterByDays') }}",

                type: "GET",
                data: {
                    'date': 'currentyear'
                },
                success: function(data) {

                    $('#table-body').html(data);
                    $('#loader').css('display', 'none');

                    console.log(data);
                }
            });
        });
        $('#lastyear').on('click', function() {
            $('.blacklink .hover').css({
                'font-weight': 'normal',
                'text-decoration': 'none'
            });
            $(this).css({
                'font-weight': 'bold',
                'text-decoration': 'underline'
            });
            $('#loader').css('display', 'block');
            $.ajax({
                url: "{{ url('admin/lot_mixing/filterByDays') }}",

                type: "GET",
                data: {
                    'date': 'lastyear'
                },

                success: function(data) {

                    $('#table-body').html(data);
                    $('#loader').css('display', 'none');

                    console.log(data);
                }
            });
        });
        $('#pack-approval').on('click', function() {
            $('#milling-form').attr('action',
                '{{ URL::to('admin/packaging/approval') }}');
            });
        const myTimeout = setTimeout(myGreeting, 5000);

        function myGreeting() {
            $(".alert").css('display', 'none');
        }

        $('.checkSentTo24').on('click', function() {
            console.log('');
            $('.checkSentTo29').prop('checked', false);
            $('#pack-approval').prop('disabled', true);
            $('#cnf-mixing').prop('disabled', false);
            if ($(".checkSentTo24:checkbox:checked").length > 0) {
                $('#pack-approval').prop('disabled', true);
            } else {
                $('#pack-approval').prop('disabled', false);
            }

        });
        $('.checkSentTo29').on('click', function() {

            $('.checkSentTo24').prop('checked', false);
            $('#cnf-mixing').prop('disabled', true);
            $('#pack-approval').prop('disabled', false);
            if ($(".checkSentTo29:checkbox:checked").length > 0) {
                $('#cnf-mixing').prop('disabled', true);
            } else {
                $('#cnf-mixing').prop('disabled', false);
            }


        });

    });
</script>
@foreach ($transactions as $transaction)
<tr>

    <td>
        {{ $transaction->transaction_id }}
    </td>
    <td>

        @php
        $farmers = parentBatch($transaction->batch_number);
        @endphp

        @foreach ($farmers as $farmer)

        @if ($farmer)
        {{ $farmer->farmer_name }} <br>
        @endif
        @endforeach
    </td>
    <td>


        @php
        $farmers = parentBatch($transaction->batch_number);
        @endphp
        @foreach ($farmers as $farmer)
        @if ($farmer)
        {{ $farmer->farmer_code }} <br>
        @endif

        @endforeach
    </td>
    <td>
        -
    </td>

    <td>
        {{ getGov($transaction->batch_number) }}
    </td>
    <td>
        {{ getRegion($transaction->batch_number) }}
    </td>
    <td>
        @php
        $farmers = parentBatch($transaction->batch_number);
        @endphp
        @foreach ($farmers as $farmer)
        @if ($farmer)
        @php
        $village = \App\Village::where('village_code', $farmer->village_code)->first();
        @endphp
        @if ($village)
        {{ $village->village_title }}
        <br>
        @endif
        @endif

        @endforeach
    </td>
    <td>
        -
    </td>
    <td>
        -
    </td>
    <td>
        -
    </td>
    <td>
        -
    </td>
    <td>
        @foreach ($transaction->details as $detail)
        {{ $detail->container_number . ' ' . $detail->container_weight }}
        @endforeach

    </td>
    <td>
        -
    </td>
    <td>

        @if ($transaction->sent_to == 24)
        <input type="checkbox" name="mixings[]" value="{{ $transaction->transaction_id }}" class="checkSentTo24">
        @endif

    </td>
    <td>

        @if ($transaction->sent_to == 29)
        <input type="checkbox" name="approvals[]" value="{{ $transaction->transaction_id }}" class="checkSentTo29">
        @endif

    </td>
    {{-- @endif --}}

</tr>
@endforeach