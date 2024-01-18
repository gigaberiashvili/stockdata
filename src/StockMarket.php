<?php

declare(strict_types=1);

namespace Giga\StockMarket;


/**
 * Class stockmarket
 *
 * @package Giga\StockMarket
 */
final class StockMarket
{
    private $fetch;
    private $isCustomTemplate = false;

    public function __construct(Fetch $fetch)
    {
        $this->fetch = Fetch::getInstance();
    }

    public function init()
    {
        add_filter('rewrite_rules_array', [$this, 'stockMarketRules']);
        add_filter('query_vars', [$this, 'stockMarketQueryVar']);
        add_filter('template_include', [$this, 'stockMarketTemplate']);
        register_activation_hook(__FILE__, [$this, 'flushPermalinks']);
        register_deactivation_hook(__FILE__, [$this, 'flushPermalinks']);
        add_action('admin_menu', [$this, 'createStockOptionsPage']);
        add_action('admin_init', [$this, 'registerStockSettings']);
        add_action('admin_post_update_stock_options', [$this, 'handleFormSubmission']);



    }

    public function stockMarketRules(array $rules): array
    {
        $customRules = [
            'index.php/(.+/)?stockdata/?$' => 'index.php?stockdata=1',
            '(.+/)?stockdata/?$' => 'index.php?stockdata=1',
            'stockdata/([^/]+)/?$' => 'index.php?stockdata=$matches[1]',
        ];



        $newRules = $customRules + $rules;


        return $newRules;
    }


    public function stockMarketQueryVar (array $vars):array
    {
        $vars[] = 'stockdata';
        return $vars;
    }

    public function stockMarketTemplate($template)
    {
        if (get_query_var('stockdata')) {
            $this->isCustomTemplate = true;
            if (get_query_var('stockdata') == 1) {
                return __DIR__ . '/templates/stockdata.php';
            } else {
                return __DIR__ . '/templates/single-stockdata.php';
            }
        }
        return $template;
    }
    public function flushPermalinks()
    {
        flush_rewrite_rules();
    }

    public function createStockOptionsPage()
    {
        add_menu_page(
            'Stock Options', // page_title
            'Stock', // menu_title
            'manage_options', // capability
            'stock', // menu_slug
            [$this, 'renderStockOptionsPage'], // callback
            '', // icon_url
            20 // position
        );
    }

    public function renderStockOptionsPage(): void
    {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                <input type="hidden" name="action" value="update_stock_options">
                <?php
                settings_fields('stock_options');
                do_settings_sections('stock');
                ?>
                <input type="text" name="api_key" id="api_key" value="">
                <input type="submit" name="save_api_key" id="save_api_key" class="button button-primary" value="Save API Key">
                <input type="submit" name="flush_transients" id="flush_transients" class="button button-primary" value="Flush All Transients">
            </form>
        </div>
        <?php
    }

    public function registerStockSettings()
    {
        register_setting('stock_options', 'api_key');
    }
    public function handleFormSubmission()
    {
        if (isset($_POST['save_api_key'])) {
            update_option('api_key', sanitize_text_field($_POST['api_key']));
        } elseif (isset($_POST['flush_transients'])) {
            // Flush all transients
            global $wpdb;
            $wpdb->query("DELETE FROM `$wpdb->options` WHERE `option_name` LIKE ('_transient_%' OR '_site_transient_%')");
        }

        // Redirect back to the options page
        wp_redirect(admin_url('admin.php?page=stock'));
        exit;
    }



}