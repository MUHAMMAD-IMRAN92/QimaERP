<div class="col-sm-1 color bg-darkPurple">
    <h4>{{ $village->quantity }}</h4>
    <p>KG CHERRY
        BOUGHT</p>
</div>
<div class="col-sm-1 color bg-Green">
    <h4>{{ $village->price }} </h4>

    <p>YER TOTAL
        SPCIAILTY COFFEE
        PURCHASED</p>
</div>
<div class="col-sm-1 color bg-darkPurple">
    <h4>-</h4>

    <p>KG DRY COFFEE
        BOUGHT </p>
</div>
<div class="col-sm-1 color bg-mildGreen">
    <h4>-</h4>

    <p>YER TOTAL
        COMMERCIAL
        COFFEE
        PURCHASED</p>
</div>
<div class="col-sm-1 color bg-darkRed">
    <h4>-</h4>

    <p>YER
        ACCOUNT
        PAYABLE</p>
</div>
<div class="col-sm-1 color bg-Green">
    <h4>-</h4>

    <p>YER SETTELED</p>
</div>
<div class="col-sm-1 color bg-lightBrown">
    <h4>{{ $village->farmers->count() }}</h4>

    <p>
        TOTAL NUMBER
        OF FARMERS
        COFFEE BOUGHT
        FROM

    </p>
</div>
<div class="col-sm-1 color bg-lightGreen">
    @if ($village->farmers->count() > 0)

        <h4>{{ $village->quantity / $village->farmers->count() }}</h4>
    @else
        <h4>0</h4>
    @endif

    <p>KG CHERRY
        AVERAGE PER
        FARMER</p>
</div>
