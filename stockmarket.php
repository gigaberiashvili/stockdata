<?php

/**
 * Plugin Name: Stock Market
 * Plugin URI:
 * Description: Stock Market plugin for WordPress
 * Version:     1.0.0
 * Author:      Giga beriashvili
 */



declare(strict_types=1);

namespace Giga\StockMarket;



if(!class_exists(StockMarket::class) && is_readable(__DIR__ . '/vendor/autoload.php')){
    require_once __DIR__ . '/vendor/autoload.php';
}



if (class_exists(StockMarket::class) && class_exists(Fetch::class)) {
    $fetchInstance = Fetch::getInstance();
    $stockMarket = new StockMarket(new Fetch(), __FILE__);
    $stockMarket->init();
}
