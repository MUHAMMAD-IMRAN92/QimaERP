<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    imran
    <table>
        <thead>
            <tr>
                <th>
                    Buyer Name
                </th>
                <th>
                    Farmer Name
                </th>
                <th>
                   Weight
                </th>
                <th>
                   Created_at
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach($array as $arr)
        <tr> <td>{{$arr[0]}}</td>
            <td>{{$arr[1]}}</td>
            <td>{{$arr[2]}}</td>
            <td>{{$arr[3]}}</td>
        </tr>
           
            @endforeach
        </tbody>
    </table>
</body>
</html>