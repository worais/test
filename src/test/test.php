<?php
/**
 * Plugin Name: Test
 * Description: Test transients and widget
 * Author: Morais Junior
 * Author URI: https://github.com/worais/
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define('PLUGIN_DIR', dirname( __FILE__ ));

class Plugin{
    public static function install(){
        update_option('plugin_permalinks_flushed', 0);
    }

    public static function init(){
        add_rewrite_endpoint( 'config', EP_ROOT | EP_PAGES );

        if( !get_option('plugin_permalinks_flushed') ) {
            flush_rewrite_rules(false);
            update_option('plugin_permalinks_flushed', 1);
        }
    }

    public static function tab_query_vars( $vars ){
        $vars[] = 'config';
        return $vars;
    }

    public static function tab_menu_items( $items ) {
        $items['config'] = 'User Config';
        return $items;
    }    

    public static function tab_content() {
        echo "content!";
    }   
}

register_activation_hook( __FILE__, ['Plugin','install']);

add_action('init',                                ['Plugin', 'init']);
add_action('woocommerce_account_config_endpoint', ['Plugin', 'tab_content']);
add_filter('query_vars',                          ['Plugin', 'tab_query_vars']);
add_filter('woocommerce_account_menu_items',      ['Plugin', 'tab_menu_items']);