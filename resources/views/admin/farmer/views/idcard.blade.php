@if ($farmer['cnicImage'] == null)
    <td> <img class="famerimg" src="{{ Storage::disk('s3')->url('images/' . 'dumy') }}"
            style="width: 150px ; height:80px ; border-radius:50%; border: 1px solid gray;" alt="" id="idimage"></td>
@else
    <td> <img class="famerimg" style="width: 150px  ; height:80px ; border-radius:50%; border: 1px solid gray;"
            src="{{ Storage::disk('s3')->url('images/' . $farmer['cnicImage']) }}" alt="no img" id="idimage"></td>
@endif
