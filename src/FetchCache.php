<?php

declare(strict_types=1);

namespace Giga\StockMarket;

class FetchCache
{
 public static function fetchCachedResult(string $url):array|false
 {
     $transientKey = self::generateCacheKey($url);
     return get_transient($transientKey);
 }

 public static function cacheResult(string $url, array $result):void
 {
     $transientKey = self::generateCacheKey($url);
     set_transient($transientKey, $result, 60 * 60 * 24);
 }

    private static function generateCacheKey(string $url):string
    {
        return 'stockmarket_' . md5($url);
    }
}