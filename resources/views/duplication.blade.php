<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    <center>QIMA DUPLICATIONS</center>
    <table>
        <tr>
            <th>Batch_number</th>
            <th>Stage</th>
            <th>Local COde</th>
            <th>Repetations</th>
        </tr>
        @foreach ($data as $d)
            <tr>
                <td>{{ $d->batch_number }}</td>
                <td>{{ $d->sent_to }}</td>
                <td>{{ $d->local_code }}</td>
                <td>{{ $d->duplicate }}</td>
            </tr>
        @endforeach
    </table>
</body>

</html>
