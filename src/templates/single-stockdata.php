<?php

use Giga\StockMarket\Fetch;
$variable = get_query_var('stockdata');

$fetchInstance = Fetch::getInstance();
$stockdata = $fetchInstance->fetchSingleStockData($variable)['data']['0'];
$stockdatachart = $fetchInstance->fetchChartData($variable)['data']['historical'];
$labels = array_reverse(array_column($stockdatachart, 'date'));
$data = array_reverse(array_column($stockdatachart, 'close'));
?>



<!DOCTYPE html>
<html>
<head>
    <title>Stock Data</title>
    <?php wp_head(); ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>
<body>
<div class="container">
    <div class="row mt-3">
        <div class="col-12 col-lg-6 mx-auto">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">Field</th>
                        <th scope="col">Value</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($stockdata as $key => $value) : ?>
                        <tr>
                            <th scope="row"><?php echo esc_html($key); ?></th>
                            <td><?php echo esc_html($value); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <canvas id="myChart"></canvas>
            </div>
        </div>
    </div>
</div>
<script>
    var ctx = document.getElementById('myChart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($labels); ?>,
            datasets: [{
                label: 'Stock Price last year',
                data: <?php echo json_encode($data); ?>,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
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
<?php wp_footer(); ?>
</body>
</html>