<canvas id="myChart" width="100%"></canvas>
<script>
    new Chart(document.getElementById('myChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: @json($managerName),
            datasets: [{
                    label: "Year To Date",


                    backgroundColor: [
                        'rgb(172, 0, 0 )',
                        'rgb(122, 33, 29)',
                        'rgb(102, 0, 0)',
                        'rgb(135, 93, 65)',
                        'rgb(175, 151, 112)',
                        'rgb(176, 216, 173)',
                        'rgb(70, 125, 52)',
                        'rgb(139, 101, 20)',
                        'rgb(179, 203, 69)',
                        'rgb(119, 50, 94)',
                        'rgb(120, 66, 128)',
                        'rgb(119, 97, 130)'
                    ],
                    data: @json($weightLabel)
                },
                {
                    label: @json($barLabel),

                    backgroundColor: [
                        'rgb(172, 0, 0 )',
                        'rgb(122, 33, 29)',
                        'rgb(102, 0, 0)',
                        'rgb(135, 93, 65)',
                        'rgb(175, 151, 112)',
                        'rgb(176, 216, 173)',
                        'rgb(70, 125, 52)',
                        'rgb(139, 101, 20)',
                        'rgb(179, 203, 69)',
                        'rgb(119, 50, 94)',
                        'rgb(120, 66, 128)',
                        'rgb(119, 97, 130)'
                    ],
                    data: @json($weightToday)
                },
            ]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
