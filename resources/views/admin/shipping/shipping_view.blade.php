
    <form action="{{ url('admin/shipping') }}" method="POST">
        @csrf
        <table id="get_batch_number" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Batch Number</th>
                    <th>Weight</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transactions as $transaction)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $transaction->batch_number }}</td>
                        <td>{{ $transaction->details->sum('container_weight') }}</td>
                        <td align="center"> <input type="checkbox" name="bags[]"
                                value="{{ $transaction->transaction_id }}"> </td>
                    </tr>
                @endforeach

            </tbody>
        </table>
        <div class="row">
            <div class="col-md-10"></div>
            <div class="col-md-2"> <input class="btn btn-success " type="submit" value="Submit">
            </div>
        </div>

    </form>
