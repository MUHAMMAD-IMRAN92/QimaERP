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