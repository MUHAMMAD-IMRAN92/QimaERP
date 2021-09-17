@foreach ($invoices as $inv)
    @if ($inv == null)
        <td> <img class="famerimg" src="{{ Storage::disk('s3')->url('images/' . 'dumy.png')  }}"
                style="width: 150px ; height:80px ; border-radius:50%; border: 1px solid gray;" alt=""></td>
    @else
        <td> <img class="famerimg" style="width: 150px  ; height:80px ; border-radius:50%; border: 1px solid gray;"
                src="{{Storage::disk('s3')->url('images/' . $inv) ) }}" alt="no img"></td>
    @endif
@endforeach
