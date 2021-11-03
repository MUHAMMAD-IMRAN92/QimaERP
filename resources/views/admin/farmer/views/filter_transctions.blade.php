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

    <p>Yer Total Coffee Purchased </p>
</div>

<div class="col-sm-1 color bg-Green mr-1">
    <h4>-</h4>
    <p>YER SETTELED</p>
</div>
<div class="col-sm-1 color bg-Green mr-1">
    <h4>-</h4>
    <p>YER REWARD</p>
</div>
<div class="col-sm-1 color bg-Green mr-1">
    <h4>-</h4>
    <p>YER ADDITIONAL
        PREMIUM</p>
</div>
<div class="col-sm-1 color bg-darkRed mr-1">
    <h4>-</h4>
    <p>YER
        ACCOUNT
        PAYABLE</p>
</div>
