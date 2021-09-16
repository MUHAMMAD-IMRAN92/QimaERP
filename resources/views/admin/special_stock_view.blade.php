    <h6>Today</h6>
    <h6 style=" margin-left: 53%;  margin-top: -26px;">
        End Date
    </h6>
    @foreach ($stock as $key => $s)
        <div class="row">


            <div class="col-md-6">

                <div class="set-width bg-primary ">
                    <p class="ml-1">{{ $s['wareHouse'] }}</p>
                    <p class="ml-1">{{ $s['today'] }}</p>
                </div>
            </div>
            <div class="col-md-6">

                <div class="set-width bg-primary ">
                    <p class="ml-1">{{ $s['wareHouse'] }}</p>
                    <p class="ml-1">{{ $s['end'] }}</p>
                </div>
            </div>
        </div>
    @endforeach
