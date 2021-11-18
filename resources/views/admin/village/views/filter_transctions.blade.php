<div class="ml-2 text-uppercase d-flex flex-wrap p-0 mb-3 data-tabs">
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
        <h4>{{ $village->farmerCount }}</h4>

        <p>
            TOTAL NUMBER
            OF FARMERS
            COFFEE BOUGHT
            FROM

        </p>
    </div>
    <div class="col-sm-1 color bg-lightGreen">
        @if ($village->newfarmers->count() > 0)
            <h4>{{ $village->quantity / $village->newfarmers->count() }}</h4>
        @else
            <h4>0</h4>
        @endif
        <p>KG CHERRY
            AVERAGE PER
            FARMER</p>
    </div>


</div>
<hr class="ml-2">
<div class="row ml-2 text-uppercase mb-2">
    <strong>
        <b>Coffee Buyer</b>
    </strong>
</div>
<div class="row ml-2 mb-2">
    <ul class="list-group list-group-horizontal text-uppercase font-weight-bold w-100 flex-wrap">
        @foreach ($village->newusers as $buyer)
            <li class="col-3 list-group-item data-content-list border-0">
                @if ($buyer->file == null)
                    <img style="max-width: 30%; width:auto !important;"
                        src="{{ Storage::disk('s3')->url('images/' . 'dumy.png') }}" id="region_farmer">
                @else
                    <img style="max-width: 40%; width:auto !important;"
                        src="{{ Storage::disk('s3')->url('images/' . $buyer->file->user_file_name) }}"
                        id="region_farmer">
                @endif

                <span class="ml-3 mr-4">{{ $buyer['first_name'] }}</span>
            </li>
        @endforeach
    </ul>

</div>
<hr class="ml-2">
<div class="row ml-2 text-uppercase mb-2">
    <strong>
        <b>Farmers</b>
    </strong>
</div>
<div class="row ml-2 mb-2">
    <ul class="list-group list-group-horizontal text-uppercase font-weight-bold w-100 flex-wrap">
        @foreach ($village->newfarmers as $farmer)
            <li class="col-3 list-group-item data-content-list border-0">
                @if ($farmer->file == null)
                    <img style="max-width: 30%; width: auto !important;"
                        src="{{ Storage::disk('s3')->url('images/' . 'dumy.png') }}" id="region_farmer">
                @else
                    <img style="max-width: 30%; width: auto !important;"
                        src="{{ Storage::disk('s3')->url('images/' . $farmer->file->user_file_name) }}"
                        id="region_farmer">
                @endif

                <span class="ml-3 mr-4">{{ $farmer['farmer_name'] }}</span>
            </li>
        @endforeach
    </ul>
</div>
