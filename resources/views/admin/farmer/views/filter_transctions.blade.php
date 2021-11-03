<div class="col-sm-1 color bg-darkPurple mr-1">
    <h4>{{ $farmer->first_purchase }}</h4>
    <p>First Purchade</p>
</div>
<div class="col-sm-1 color bg-darkPurple mr-1">

    <h4>{{ $farmer->last_purchase }}</h4>

    <p>Last Purchase</p>
</div>
<div class="col-sm-1 color bg-Green mr-1">
    @if (!$farmer->price_per_kg)
    <h4>{{ number_format($farmer->price * $farmer->quantity) }}
    </h4>
    @else
    <h4>
        {{ number_format($farmer->price_per_kg * $farmer->quantity) }}
    </h4>
    @endif

    <p>yer total coffee purchased </p>
</div>
<div class="col-sm-1 color bg-Green mr-1">
    <h4>{{ $farmer->quantity }}</h4>

    <p>Quantity</p>
</div>
<div class="col-sm-1 color bg-Green mr-1"></div>
<div class="col-sm-1 color bg-Green mr-1"></div>
<div class="col-sm-1 color bg-darkRed mr-1"></div>
