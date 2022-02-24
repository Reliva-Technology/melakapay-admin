<canvas id="visitor" height="200"></canvas>
<script>
$(function () {

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
                {{ $key }},
                @endforeach
            ]
        },
        options: {
            maintainAspectRatio: false,
            legend: {
                display: false
            }
        }
    };

    var ctx = document.getElementById('visitor').getContext('2d');
    new Chart(ctx, config);
});
</script>