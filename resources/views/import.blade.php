<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    <div class="container">
        <div class="card bg-light mt-3">
            <div class="card-header">
                Laravel 5.8 Import Export Excel to database Example - ItSolutionStuff.com
            </div>
            <div class="card-body">
                <form action="{{ route('import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="file" class="form-control">
                    <br>
                    <button type="submit">submit</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
