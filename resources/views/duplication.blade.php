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
    <table class="duplication-table border">
        <tr class="border">
            <th>Batch_number</th>
            <th>Stage</th>
            <th>Local Code</th>
            <th>Repetations</th>
        </tr>
        @foreach ($data as $d)
            <tr class="border">
                <td>{{ $d->batch_number }}</td>
                <td>{{ $d->sent_to }}</td>
                <td>{{ $d->local_code }}</td>
                <td>{{ $d->duplicate }}</td>
            </tr>
        @endforeach
    </table>
</body>

</html>
