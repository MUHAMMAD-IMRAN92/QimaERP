@foreach ($invoices as $inv)
    @if ($inv == null)
        <td> <img class="famerimg" src="{{ asset('public/images/farmericon.png') }}"
                style="width: 150px ; height:80px ; border-radius:50%; border: 1px solid gray;" alt=""></td>
    @else
        <td> <img class="famerimg" style="width: 150px  ; height:80px ; border-radius:50%; border: 1px solid gray;"
                src="{{ asset('storage/app/images/' . $inv) }}" alt="no img"></td>
    @endif
@endforeach
