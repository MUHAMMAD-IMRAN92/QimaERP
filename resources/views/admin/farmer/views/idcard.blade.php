<div class="col-sm-1 color p-0 ml-0">

    @if ($farmer['cnicImage'] == null)

        <img style="max-width: 100%; height: 100%;" class="famerimg"
            src="{{ Storage::disk('s3')->url('images/' . 'dumy.png') }}" alt="">
    @else
        <img class="famerimg" style="width: 100%; height: 100%;"
            src="{{ Storage::disk('s3')->url('images/' . $farmer['cnicImage']) }}" alt="no img" id="idimage">
    @endif
</div>
