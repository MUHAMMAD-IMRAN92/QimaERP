<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
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
            <td>
                @foreach($arr[4] as $inv)
                @if ($inv == null)
                <td> <img class="famerimg" src="{{ Storage::disk('s3')->url('images/' . 'dumy.png') }}"
                        style="width: 150px  ; height:150px ; border-radius:30%; border: 1px solid gray;" alt=""></td>
            @else
                <td>
                    <a href="{{ Storage::disk('s3')->url('images/' . $inv) }}" target="_blank">
                    <img   class="famerimg"
                        style="width: 150px  ; height:150px ; border-radius:30%; border: 1px solid gray;"
                        src="{{ Storage::disk('s3')->url('images/' . $inv) }}" alt="no img"></td></a>
            @endif
                @endforeach
            </td>
        </tr>           
            @endforeach
        </tbody>
       
    </table>
    <br>
     Total Weight : <span>{{$total_weight}}</span>
</body>
</html>