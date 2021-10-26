<div class="row">
    <div class="text-center text-uppercase col-6 px-1">
        <h6><b>Today</b></h6>
    </div>
    <div class="text-center text-uppercase col-6 px-1">
        <h6><b>End Date</b></h6>
    </div>
</div>
@foreach ($nonspecialstock as $key => $s)

<div class="row mb-md-2 flex-1">
    <div class="col-md-6 data-tabs px-1">
        <div class="h-100 bg-dark-blue text-uppercase mb-2">
            <h4 class="ml-1">{{ $s['today'] }}</h4>
            <p class="ml-1 mb-0">{{ $s['wareHouse'] }}</p>
        </div>
    </div>
    <div class="col-md-6 data-tabs px-1">
        <div class="h-100 bg-dark-blue text-uppercase mb-2">
            <h4 class="ml-1">{{ $s['end'] }}</h4>
            <p class="ml-1 mb-0">{{ $s['wareHouse'] }}</p>
        </div>
    </div>
</div>
@endforeach
