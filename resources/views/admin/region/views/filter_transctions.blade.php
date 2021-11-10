<div class="col-lg-11 ml-md-2 text-uppercase d-flex flex-wrap p-0 mb-3 data-tabs" id="transactions">
    <div class="col-sm-1 color bg-darkPurple p-2 content-box">
        <h4>{{ $total_coffee }}</h4>
        <p>KG CHERRY BOUGHT </p>
    </div>
    <div class="col-sm-1 color bg-darkGreen p-2 content-box">
        <h4>{{ $totalPrice }}</h4>
        <p>YER COFFEE Purchased</p>
    </div>
    <div class="col-sm-1 color bg-darkRed p-2 content-box">
        <h4>-</h4>

        <p>YER
            ACCOUNT
            PAYABLE </p>
    </div>
    <div class="col-sm-1 color bg-darkGreen p-2 content-box">
        <h4>-</h4>
        <p>YER SETTELED</p>
    </div>
    {{-- <div class="col-sm-1 color bg-darkGreen p-2 content-box">
        <h4>{{ App\Region::count() }}</h4>

        <p>Regions</p>
    </div> --}}
    <div class="col-sm-1 color bg-lightBrown p-2 content-box">
        <h4>{{ $farmerCount }}</h4>

        <p>Farmers </p>
    </div>
    {{-- <div class="col-sm-1 color bg-darkPurple p-2 content-box">
        <h4>{{ $governorate->count() }}</h4>
        <p>Governorate</p>
    </div> --}}
    {{-- <div class="col-sm-1 color bg-lightBrown p-2 content-box">
        <h4>{{ $totalWeight }}</h4>
        <p>Total Coffee </p>
    </div> --}}
    <div class="col-sm-1 color bg-lightGreen p-2 content-box">
        <h4>{{ $readyForExport }}</h4>
        <p>KG SPECIALTY
            COFFEE EXPORT
            READY IN YEMEN</p>
    </div>
    <div class="col-sm-1 color bg-lightGreen p-2 content-box">
        <h4>-</h4>
        <p>KG COMMERCIAL
            GREEN COFFEE
            EXPORT READY</p>
    </div>
    <div class="col-sm-1 color bg-darkGreen p-2 content-box">
        <h4>-</h4>
        <p>YER YEMEN
            SALES</p>
    </div>
    <div class="col-sm-1 color bg-lightGreen p-2 content-box">
        <h4>-</h4>
        <p>USD SALES</p>
    </div>

</div>
<hr>
<div class="row ml-2 text-uppercase mb-2">
    <strong>
        <b>QUANTITY CHERRY BOUGHT</b>
    </strong>
</div>
<div class="row">
    <div class="col-md-11 ml-4">
        <canvas id="myChart" style="width:100%;max-height:500px"></canvas>

        <script>
            var xValues = @json($regionName);
            var yValues = @json($regionQuantity);

            new Chart("myChart", {
                type: "line",
                data: {
                    labels: xValues,
                    datasets: [{
                        pointRadius: 4,
                        fill: false,
                        tension: 0.5,

                        backgroundColor: "black",
                        borderColor: "gray",
                        data: yValues
                    }]
                },
                options: {
                    legend: {
                        display: false
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                min: 0,
                                // max: 8000
                            }
                        }],
                    }
                }
            });
        </script>
    </div>
</div>

<br>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div>

                <!-- /.card -->

                <div class="card">
                    <!-- /.card-header -->
                    <div class="">

                        <table class="table table-bordered region-table-custom">
                            <thead>
                                <tr class="blacklink letter-spacing-1 text-uppercase">

                                    <th>Governorate / </th>
                                    <th>Region / </th>
                                    <th>Villages / </th>
                                    <th>Quantity /</th>
                                    <th>Value /</th>
                                    <th>Farmers /</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($governorates as $governorate)
                                    <tr>

                                        <td>{{ $governorate->governerate_title }}</td>
                                        <td>
                                            @foreach ($governorate->regions as $region)
                                                {{ $region->region_title }} <br>
                                            @endforeach
                                        </td>
                                        <td>
                                            @if ($governorate->villages)

                                                @foreach ($governorate->villages as $village)
                                                    {{ $village->village_title }} <br>
                                                @endforeach

                                            @endif
                                        </td>

                                        <td>
                                            @if ($governorate->villages)

                                                @foreach ($governorate->villages as $village)
                                                    {{ $village->weight }} <br>
                                                @endforeach
                                            @endif
                                        </td>

                                        <td>
                                            @if ($governorate->villages)

                                                @foreach ($governorate->villages as $village)
                                                    {{ $village->weight * $village->price }} <br>
                                                @endforeach
                                            @endif
                                        </td>
                                        <td>
                                            @if ($governorate->villages)

                                                @foreach ($governorate->villages as $village)
                                                    {{ $village->farmers }} <br>
                                                @endforeach
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
</section>
