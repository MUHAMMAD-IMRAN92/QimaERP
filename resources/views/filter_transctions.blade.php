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
