<?php

use Giga\StockMarket\Fetch;

$fetchInstance = Fetch::getInstance();
$marketData = $fetchInstance->fetchMarketData();


//get only first 20 marketdata

//create table foreach market data make it clickable to go single

?>



<!DOCTYPE html>
<html>
<head>
    <title>stocks</title>
    <?php wp_head(); ?>
</head>
<body>
<div class="container">
    <div class="row mt-3">
        <div class="col-12 col-lg-6 mx-auto">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col"><?php echo esc_html__('Symbol', 'stockdata'); ?></th>
                        <th scope="col"><?php echo esc_html__('Name', 'stockdata'); ?></th>
                        <th scope="col"><?php echo esc_html__('Price', 'stockdata'); ?></th>
                        <th scope="col"><?php echo esc_html__('exchange', 'stockdata'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($fetch['error'])) : ?>
                        <tr>
                            <td colspan="3">
                                <?php echo esc_html($fetch['error']); ?>
                            </td>
                        </tr>
                        <?php return;
                    endif;

                    foreach ($marketData['data'] as $data) :
                        $id = $data['symbol'];
                        $name = $data['name'];
                        $price = $data['price'];
                        $change = $data['exchange'];
                        ?>
                        <tr>
                            <th scope="row">
                                <a href="/stockdata/<?php echo $id?>" class="link-primary" data-id="<?php echo $id; ?>">
                                    <?php echo esc_html($id); ?>
                                </a>
                            </th>
                            <td>
                                <a href="/stockdata/<?php echo $id?>" class="link-primary" data-id="<?php echo $id; ?>">
                                    <?php echo esc_html($name); ?>
                                </a>
                            </td>
                            <td>
                                <a href="/stockdata/<?php echo $id?>" class="link-primary" data-id="<?php echo $id; ?>">
                                    <?php echo esc_html($price); ?>
                                </a>
                            </td>
                            <td>
                                <a href="/stockdata/<?php echo $id?>" class="link-primary" data-id="<?php echo $id; ?>">
                                    <?php echo esc_html($change); ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
<?php wp_footer(); ?>
</body>
</html>