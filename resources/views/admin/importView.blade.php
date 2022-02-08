<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <form method="POST" action="{{url('admin/importPost')}}" enctype="multipart/form-data">
        @csrf
        <input type="file" name="gen">
        <button type="submit">Import</button>
    </form>
</body>

</html>