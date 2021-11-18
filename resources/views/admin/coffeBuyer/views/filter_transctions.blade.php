<div class="col-sm-1 color bg-darkPurple mr-1">
    <h4>{{ $buyer->first_purchase }}</h3>
        <p>First Purchase</p>
</div>
<div class="col-sm-1 color bg-darkPurple mr-1">
    <h4>{{ $buyer->last_purchase }}</h3>

        <p>Last Puchase</p>
</div>
<div class="col-sm-1 color bg-Green mr-1">
    <h4>{{ number_format($buyer->sum) }}</h3>

        <p>Quantity</p>
</div>
<div class="col-sm-1 color bg-Green mr-1">
    <h4>{{ number_format($buyer->price) }}</h3>

        <p>yer total coffee purchased</p>
</div>
<div class="col-sm-1 color bg-Green mr-1"></div>
<div class="col-sm-1 color bg-Green mr-1"></div>
<div class="col-sm-1 color bg-darkRed mr-1"></div>
