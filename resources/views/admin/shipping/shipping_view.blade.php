@foreach ($transactions as $transaction)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $transaction->batch_number }}</td>
        <td>{{ $transaction->details->sum('container_weight') }}</td>
        <td align="center"> <input type="checkbox" name="bags[]" value="{{ $transaction->transaction_id }}"> </td>
    </tr>
@endforeach
