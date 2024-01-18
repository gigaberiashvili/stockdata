<?php


namespace Giga\StockMarket;

class Fetch
{


    private static $instance = null;

    private $url = 'https://financialmodelingprep.com/api/v3/';

    private $apikey = 'a7960701c089b70c1bda4122bd50a4a5';


    public static function getInstance(): Fetch
    {
        if (self::$instance == null) {
            self::$instance = new Fetch();
        }
        return self::$instance;
    }

    private function fetchFromApi(string $url): array
    {

        $cachedResult = FetchCache::fetchCachedResult($url);
        if (false !== $cachedResult) {
            $cachedResult['from_cache'] = true;
            return $cachedResult;
        }
        $result = [
            'success' => false,
            'data' => [],
            'error' => ''
        ];

        $response = wp_remote_get($url, ['timeout' => 30]);

        if (is_wp_error($response)) {
            $errorMessage = $response->get_error_message();
            /* translators: %s: Error message text. */
            $result['error'] = sprintf(__('WP Error: %s', 'stockmarket'), $errorMessage);
            return $result;
        }

        $responseCode = wp_remote_retrieve_response_code($response);
        $responseMessage = wp_remote_retrieve_response_message($response);

        if ($responseCode !== 200) {
            $result['error'] = sprintf(__('API Error: %s', 'stockmarket'), $responseMessage);
            return $result;
        }

        $body = wp_remote_retrieve_body($response);
        $decodedBody = json_decode($body, true);
        if ($decodedBody === null) {
            $result['error'] = __('API Error: Invalid response body', 'stockmarket');
            return $result;
        }

        $results['data'] = $decodedBody;

        // change data to 20 array item

        return $results;
    }

    public function fetchmarketdata(): array
    {
        $marketurl = $this->url . 'stock/list?apikey=' . $this->apikey;
        $results = $this->fetchFromApi($marketurl);


        if ($results) {
            $results['data'] = array_filter($results['data'], function($stock) {
                return $stock['exchange'] === 'NASDAQ Global Select';
            });

            $results['data'] = array_slice($results['data'], 0, 50);
        }

        // Cache the result
        FetchCache::cacheResult($marketurl, $results);

        return $results;
    }


    public function fetchSingleStockData (string $string )
    {

        $singleurl = $this->url . '/profile/' . $string . '?apikey=' . $this->apikey;
        $results = $this->fetchFromApi($singleurl);
        FetchCache::cacheResult($singleurl, $results);
        return $results ;
    }


    public function fetchChartData(string $string)
    {
        $endDate = date('Y-m-d'); // today's date
        $startDate = date('Y-m-d', strtotime('-1 year')); // date one year ago

        $charturl = $this->url . '/historical-price-full/' . $string . '?from=' . $startDate . '&to=' . $endDate . '&apikey=' . $this->apikey;
        $results = $this->fetchFromApi($charturl);
        FetchCache::cacheResult($charturl, $results);
        return $results;
    }


}











