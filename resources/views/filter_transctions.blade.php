{{-- transactiongraph --}}

<div class="col-lg-11 ml-md-2 text-uppercase d-flex flex-wrap p-0 mb-3 data-tabs" id="transactions">
    <div class="col-sm-1 color bg-darkPurple p-2 content-box">
        <h4>{{ $total_coffee }}</h4>
        <p>KG CHERRY BOUGHT </p>
    </div>
    <div class="col-sm-1 color bg-darkGreen p-2 content-box">
        <h4>{{ $totalPrice }}</h4>
        <p>YER COFFEE PURCHASED</p>
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

    <div class="col-sm-1 color bg-lightBrown p-2 content-box">
        <h4>{{ $farmerCount }}</h4>

        <p>Farmers </p>
    </div>

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
<div class="row ml-2 text-uppercase mb-2">
    <strong>
        <b>QUANTITY CHERRY BOUGHT</b>
    </strong>
</div>
<div class="row">
    <div class="col-md-12">
        <canvas id="myChart" style="width:100%;max-height:500px"></canvas>

        <script>
            console.log(@json($quantity));
            var xValues = @json($createdAt);
            var yValues = @json($quantity);
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
                                max: 10000
                            }
                        }],
                        xAxes: [{
                            barPercentage: 0.4
                        }]
                    }

                }
            });
        </script>
    </div>
</div>
<hr class="ml-md-2">
<div class="row">
    {{-- <div class="col-md-3 vl">
    <div class="card shadow-none">
        <div class="text-uppercase px-3 h5">
            <strong>
                <b>Farmer</b>
            </strong>
            <p class="mb-0 card-custom-description">KG CHERRY<br>BOUGHT</p>
        </div>
        <!-- /.card-header -->
        <div class="card-body pt-0">
            <table class="table table-borderless">
                <!-- <thead>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <tr>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <th style="width: 10px">Sr#</th>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <th>Farmer Name</th>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </tr>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </thead> -->
                <tbody>
                    @if (count($farmers) == 0)
                        @php
                        $loop = 5 - count($farmers); @endphp
                        @foreach (App\Farmer::all()->take(5) as $farmer)
                            <tr style="white-space:nowrap">
                                <!-- <td>{{ $loop->iteration }}</td> -->

                                <td class="d-flex align-items-center px-0">
                                    <img class="rounded-circle" src="https://i.imgur.com/C4egmYM.jpg"
                                        width="50">
                                    <span class="ml-3">
                                        {{ $farmer['farmer_name'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    @foreach ($farmers as $farmer)

                        <tr>

                            <!-- <td>{{ $loop->iteration }}</td> -->

                            <td class="d-flex align-items-center px-0">
                                <img class="rounded-circle" src="https://i.imgur.com/C4egmYM.jpg"
                                    width="50">
                                <span class="ml-3">
                                    <b>{{ $farmer['farmer_name'] . '-' . $farmer['weight'] }}</b>
                                </span>
                            </td>

                        </tr>

                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- /.card-body -->

    </div>
</div> --}}
    <div class="col-md-3 vl">
        <div class="card shadow-none">
            <div class="text-uppercase px-3 h5">
                <strong>
                    <b>Coffee Buyers</b>
                </strong>
                <p class="mb-0 card-custom-description">KG CHERRY<br>BOUGHT</p>
            </div>
            <!-- /.card-header -->
            <div class="card-body pt-0">
                <table class="table table-borderless">
                    <!-- <thead>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <tr>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <th style="width: 10px">Sr#</th>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <th>Farmer Name</th>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </tr>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </thead> -->
                    <tbody>
                        {{-- @if (count($topBuyer) == 0)
                        @php
                        $loop = 5 - count($farmers); @endphp
                        @foreach (App\User::all()->take(5) as $farmer)
                            <tr style="white-space:nowrap">
                                <!-- <td>{{ $loop->iteration }}</td> -->

                                <td class="d-flex align-items-center px-0">
                                    <img class="rounded-circle" src="https://i.imgur.com/C4egmYM.jpg"
                                        width="50">
                                    <span class="ml-3">
                                        {{ $farmer['farmer_name'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    @endif --}}
                        @if ($topBuyer->count() == 0)

                            <td>
                                <center> <b class="mt-5">No Coffee Bought</b> </center>
                            </td>
                        @endif
                        @foreach ($topBuyer as $buyer)

                            <tr>

                                <!-- <td>{{ $loop->iteration }}</td> -->

                                <td class="d-flex align-items-center px-0">
                                    <img class="rounded-circle" src="https://i.imgur.com/C4egmYM.jpg" width="50">
                                    <span class="ml-3">
                                        <b>{{ $buyer['name'] . '-' . $buyer['weight'] }}</b>
                                    </span>
                                </td>

                            </tr>

                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->

        </div>
    </div>
    <div class="col-md-3 vl">
        <div class="card shadow-none">
            <div class="text-uppercase px-3 h5">
                <strong>
                    <b>Regions</b>
                </strong>
                <p class="mb-0 card-custom-description">KG CHERRY<br>BOUGHT</p>
            </div>
            <!-- /.card-header -->
            <div class="card-body pt-0">
                <table class="table table-borderless">
                    <!-- <thead>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <tr style="white-space:nowrap">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <th style="width: 10px">Sr#</th>

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <th>Region Name</th>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </tr>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </thead> -->
                    <tbody>
                        {{-- @if (count($regions) == 0)
                    @php
                    $loop = 5 - count($regions); @endphp
                    @foreach (App\Region::all()->take(5) as $region)
                        <tr style="white-space:nowrap">
                            <!-- <td>{{ $loop->iteration }}</td> -->
                            <td class="d-flex align-items-center px-0">
                                    <img class="rounded-circle" src="https://i.imgur.com/C4egmYM.jpg" width="50">
                                    <span class="ml-3">
                                    {{ $region->region_title }}
                                    </span>
                                </td>
                        </tr>
                    @endforeach
                @endif --}}
                        @if ($regions->count() == 0)

                            <td>
                                <center> <b class="mt-5">No Coffee Bought</b> </center>
                            </td>
                        @endif
                        @foreach ($regions as $region)
                            <tr style="white-space:nowrap">
                                <!-- <td>{{ $loop->iteration }}</td> -->

                                <td class="d-flex align-items-center px-0">
                                    <img class="rounded-circle" src="https://i.imgur.com/C4egmYM.jpg" width="50">
                                    <span class="ml-3">
                                        <b> {{ $region['region_title'] . '-' . $region['weight'] }}</b>
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->

        </div>
    </div>
    <div class="col-md-3 vl">
        <div class="card shadow-none h-100">

            <div class="text-center text-uppercase">
                <strong>
                    <b>SPECIALTY COFFEE IN STOCK</b>
                </strong>
            </div>
            <!-- /.card-header -->
            <input type="date" id="specialCoffee" name="endDate" class="form-control border-0">
            <div class="card-body d-flex flex-column" id="ajaxspecialCoffee">
                <div class="row">
                    <div class="text-center text-uppercase col-6 px-1">
                        <h6><b>Today</b></h6>
                    </div>
                    <div class="text-center text-uppercase col-6 px-1">
                        <h6><b>End Date</b></h6>
                    </div>
                </div>
                @foreach ($stock as $key => $s)
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
            </div>
            <!-- /.card-body -->

        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-none h-100">

            <div class="text-center text-uppercase">
                <strong>
                    <b>COMMERCIAL COFFEE IN STOCK</b>
                </strong>
            </div>
            <!-- /.card-header -->
            <input type="date" id="nonspecialCoffee" name="endDate" class="form-control border-0">
            <div class="card-body d-flex flex-column" id="ajaxnonspecialCoffee">

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



            </div>
            <!-- /.card-body -->

        </div>
    </div>
</div>
<hr class="col-">
<div class="row">

    <div class="col-md-5 ">
        <center>Governorate Wise</center>
        <canvas id="4rd" class="ml-md-2" style="width:100%; height:200px;"></canvas>

        <script>
            var xValues = @json($govName);
            var yValues = @json($govQuantity);
            var barColors = ["red", "green", "blue", "orange", "brown", "yellow", "purple", "black", "DeepPink",
                "DarkOrange",
            ];
            new Chart("4rd", {
                type: "line",
                data: {
                    labels: xValues,
                    datasets: [{
                        pointRadius: 3,
                        // backgroundColor: "#e755ba",
                        pointBackgroundColor: "white",
                        pointBorderColor: "black",
                        fill: false,
                        lineTension: 0.3,
                        borderWidth: 1,
                        // lineColor: "black",
                        borderColor: "black",
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

                            }
                        }],
                    }
                }
            });
        </script>
    </div>
    <div class="col-md-7">
        <h6 class='ml-2 mt-2'>OVERVIEW OF COFFEE BOUGHT BY GOVERNORATE</h6>
        <div class="row">

            <div class="col-sm-12">

                <table style=" height:200px; width: 100%; font-size: 12px ;">
                    <tr align="center" style="border-bottom: 1px solid black; border-right: 1px solid black">
                        <td align="center" style="border-right: 1px solid black;">GOVERNORATE</td>
                        <td align="center" style=" border-right: 1px solid black">QUANTITY</td>
                        <td align="center" style=" border-right: 1px solid black">FARMERS</td>
                        <td></td>
                    </tr>
                    <tbody>
                        @foreach ($govQuantityRegion as $gov)
                            <tr style="border-right: 1px solid black">
                                <td align="center" style="border-right: 1px solid black">
                                    {{ $gov['title'] }}
                                </td>
                                <td align="center" style="border-right: 1px solid black">
                                    {{ $gov['weight'] . 'KGs' }}
                                </td>
                                <td align="center" style="border-right: 1px solid black">
                                    {{ $gov['farmerCount'] }}</td>
                                <td>
                                    @foreach ($gov['region']->reverse() as $r)
                                        @php
                                            $width = $r['weight'] / 50;
                                            // echo $width;
                                        @endphp
                                        {{-- <p>{{ $r['regionTitle']}}</p> --}}
                                        <div class="flex-properties">
                                            <div style="width:{{ $width }}px;" class="background">

                                                <span class="hover-text">

                                                    {{ $r['regionTitle'] . ':' . $r['weight'] }}

                                                </span>

                                            </div>
                                        </div>
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>

    </div>
</div>
<div class="row">
    <div class="col-md-5">
        <center>Region Wise</center>
        <canvas id="3rd" class="ml-md-2" style="width:100%; height:200px;"></canvas>

        <script>
            var xValues = @json($regionName);
            var yValues = @json($regionQuantity);

            new Chart("3rd", {
                type: "line",
                data: {
                    labels: xValues,
                    datasets: [{
                        pointRadius: 3,
                        // backgroundColor: "#e755ba",
                        pointBackgroundColor: "white",
                        pointBorderColor: "black",
                        fill: false,
                        lineTension: 0.3,
                        borderWidth: 1,
                        // lineColor: "black",
                        borderColor: "black",
                        //  backgroundColor: 'black',
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

                            }
                        }],
                    }
                }
            });
        </script>
    </div>
    <div class="col-md-7">
        <center>Yemen Sales</center>
        <canvas id="6rd" class="ml-md-2" style="width:100%; height:200px;"></canvas>

        <script>
            var xValues = @json($yemenSalesDay);
            var yValues = @json($yemenSalesCoffee);

            new Chart("6rd", {
                type: "line",
                data: {
                    labels: xValues,
                    datasets: [{
                        pointRadius: 3,
                        // backgroundColor: "#e755ba",
                        pointBackgroundColor: "white",
                        pointBorderColor: "black",
                        fill: false,
                        lineTension: 0.3,
                        borderWidth: 1,
                        // lineColor: "black",
                        borderColor: "black",
                        //  backgroundColor: 'black',
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
                                max: 10000
                            }
                        }],
                    }
                }
            });
        </script>
    </div>
</div>
