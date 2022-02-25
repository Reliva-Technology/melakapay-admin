<canvas id="transaction" height="400"></canvas>
<script>
$(function () {

    var config = {
        type: 'bar',
        data: {
            datasets: [{
                data: [
                    @foreach($transaction as $key => $value)
                    {{ $value }},
                    @endforeach
                ],
                backgroundColor: [
                    'rgb(2, 71, 181)',
                    'rgb(193, 253, 111)',
                    'rgb(172, 127, 203)',
                    'rgb(203, 53, 175)',
                    'rgb(226, 45, 44)',
                    'rgb(102, 181, 19)',
                    'rgb(92, 165, 221)',
                    'rgb(250, 40, 162)',
                    'rgb(250, 252, 120)',
                    'rgb(203, 53, 175)',
                    'rgb(226, 45, 44)',
                    'rgb(102, 181, 19)',
                    'rgb(92, 165, 221)',
                    'rgb(172, 127, 203)',
                    'rgb(203, 53, 175)',
                    'rgb(226, 45, 44)',
                    'rgb(102, 181, 19)',
                    'rgb(67, 59, 246)' 
                ]
            }],
            labels: [
                @foreach($transaction as $key => $value)
                '{{ $key }}',
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
                        labelString: 'Agency'
                    }
                }],
                yAxes: [{
                    scaleLabel: {
                        display: true,
                        labelString: 'Transaction'
                    }
                }]
            }
        }
    };

    var ctx = document.getElementById('transaction').getContext('2d');
    new Chart(ctx, config);
});
</script>