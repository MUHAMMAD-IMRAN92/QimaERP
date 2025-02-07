<!DOCTYPE html>
<html lang="en">
<style>
    .duplication-table {

        width: 100%;
        text-align: center;
    }

    .border {
        border: 1px solid black;
    }

    tr td {
        border: 1px solid black;
    }

</style>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    <center>
        <h1>QIMA DUPLICATIONS</h1>

    </center>
    {{-- <h3> <b> Note! </b>The duplication is considered when baskets are repeated twice or more times in a Batch Number.</h3>
    <table class="duplication-table border">
        <tr class="border">
            <th>Batch_number</th>
            <th>Stage</th>
            <th>Local Code</th>
            <th>Repetations</th>
            <th>Bakets</th>
        </tr>
        @foreach ($data as $d)
            <tr class="border">
                <td>{{ $d->batch_number }}</td>
                <td>{{ $d->sent_to }}</td>
                <td>{{ $d->local_code }}</td>
                <td>{{ $d->duplicate }}</td>
                <td>
                    @foreach ($d->details as $detail)
                        {{ $detail->container_number . ' : ' . $detail->container_weight }} <br>
                    @endforeach
                </td>
            </tr>
        @endforeach
    </table> --}}


    <table id='myTable' class="duplication-table border">
        <tr class="border">
            <th>Farmer name</th>
            <th>Farmer Code</th>
            <th>Batch Number</th>
            <th>Local_code</th>
            <th>Date of Purchase</th>
            <th>Purchase Weight</th>
            <th>Coffee buyer</th>
        </tr>
        @foreach ($data as $d)
            <tr class="border">
                <td>{{ getFarmer($d->batch_number) }}</td>
                <td>{{ \Str::beforelast($d->batch_number, '-') }}</td>
                <td>{{ $d->batch_number }}</td>
                <td>{{ $d->local_code }}</td>
                <td>{{ $d->local_created_at }}</td>

                <td>
                    @foreach ($d->details as $detail)
                        {{ $detail->container_number . ' : ' . $detail->container_weight }} <br>
                    @endforeach
                </td> @php
                    $user = \App\User::find($d->created_by);
                @endphp
                <td>{{ $user->user_first_name . ' ' . $user->last_name }}</td>
            </tr>
        @endforeach
    </table>

</body>

</html>
