<div class="col-sm-1 color p-0 ml-0">

@if ($farmer['cnicImage'] == null)
     <img class="famerimg" src="{{ asset('public/images/farmericon.png') }}"
    style="max-width: 100%; height: 100%;" alt="" id="idimage">
@else
     <img class="famerimg" style="max-width: 100%; height: 100%;"
            src="{{ asset('storage/app/images/' . $farmer['cnicImage']) }}" alt="no img" id="idimage">
@endif
</div>
