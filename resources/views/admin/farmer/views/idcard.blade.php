@if ($farmer['cnicImage'] == null)
    <td> <img class="famerimg" src="{{ asset('public/images/farmericon.png') }}"
            style="width: 150px  ; height:150px ; border-radius:30%; border: 1px solid gray;" alt="" id="idimage"></td>
@else
    <td> <img class="famerimg" style="width: 150px  ; height:150px ; border-radius:30%; border: 1px solid gray;"
            src="{{ asset('storage/app/images/' . $farmer['cnicImage']) }}" alt="no img" id="idimage"></td>
@endif
