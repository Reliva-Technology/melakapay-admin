<canvas id="visitor" height="400"></canvas>
<script>
$(function () {

    var count = 1;
    var config = {
        type: 'line',
        data: {
            datasets: [{
                data: [
                    @foreach($visitor as $key => $value)
                    {{ $value }},
                    @endforeach
                ],
                backgroundColor: [
                    'rgba(0, 0, 0, 0.1)'
                ]
            }],
            labels: [
                @foreach($visitor as $key => $value)
                count++,
                @endforeach
            ]
        },
        options: {
            maintainAspectRatio: false,
            legend: {
                display: false
            },
            scales:     {
                xAxes: [{
                    scaleLabel: {
                        display: true,
                        labelString: 'Day'
                    }
                }],
                yAxes: [{
                    scaleLabel: {
                        display: true,
                        labelString: 'Visitor'
                    }
                }]
            }
        }
    };

    var ctx = document.getElementById('visitor').getContext('2d');
    new Chart(ctx, config);
});
</script>