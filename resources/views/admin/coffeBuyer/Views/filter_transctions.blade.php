<div class="col-sm-1 color bg-danger">
    <h3 style="font-size: 16px !important">{{ $buyer->first_purchase }}</h3>
    <p>First Purchase</p>
</div>
<div class="col-sm-1 color bg-primary">
    <h3 style="font-size: 16px !important">{{ $buyer->last_purchase }}</h3>

    <p>Last Purchase</p>
</div>

<div class="col-sm-1 color bg-info">
    <h3 style="font-size: 16px !important">{{ number_format($buyer->sum) }}</h3>

    <p>Quantity</p>
</div>
<div class="col-sm-1 color bg-secondary ">
    <h3 style="font-size: 16px !important">{{ number_format($buyer->price) }}</h3>

    <p>yer total coffee purchased</p>
</div>
<div class="col-sm-1 color bg-dark"></div>
<div class="col-sm-1 color bg-danger"></div>
<div class="col-sm-1 color bg-success"></div>
