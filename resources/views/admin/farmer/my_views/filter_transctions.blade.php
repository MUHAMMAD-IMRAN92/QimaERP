<div class="col-sm-1 color bg-danger">
    <h3 style="font-size: 16px !important">{{ $farmer->first_purchase }}</h3>
    <p>First Purchade</p>
</div>
<div class="col-sm-1 color bg-primary">
    <h3 style="font-size: 16px !important">{{ $farmer->last_purchase }}</h3>

    <p>Last Purchase</p>
</div>
<div class="col-sm-1 color bg-warning">
    @if (!$farmer->price_per_kg)
        <h3 style="font-size: 16px !important">{{ number_format($farmer->price * $farmer->quantity) }}
        </h3>
    @else
        <h3 style="font-size: 16px !important">
            {{ number_format($farmer->price_per_kg * $farmer->quantity) }}
        </h3>
    @endif

    <p>yer total coffee purchased </p>
</div>
<div class="col-sm-1 color bg-info">
    <h3 style="font-size: 16px !important">{{ $farmer->quantity }}</h3>

    <p>Quantity</p>
</div>
<div class="col-sm-1 color bg-dark"></div>
<div class="col-sm-1 color bg-danger"></div>
<div class="col-sm-1 color bg-success"></div>
<div class="col-sm-1 color bg-success"></div>
<div class="col-sm-1 color bg-success"></div>
<div class="col-sm-1 color bg-success"></div>
<div class="col-sm-1 color bg-success"></div>
