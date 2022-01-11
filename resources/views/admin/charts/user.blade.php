<canvas id="doughnut" width="200" height="200"></canvas>
<script>
$(function () {

    var config = {
        type: 'bar',
        data: {
            datasets: [{
                data: [
                    @foreach($created as $data)
                    {{ $data }},
                    @endforeach
                ],
                backgroundColor: [
                    'rgb(54, 162, 235)',
                    'rgb(255, 99, 132)'
                ]
            }],
            labels: [
                @foreach($created as $data)
                {{ $data }},
                @endforeach
            ]
        },
        options: {
            maintainAspectRatio: false
        }
    };

    var ctx = document.getElementById('doughnut').getContext('2d');
    new Chart(ctx, config);
});
</script>