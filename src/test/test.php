<?php
/**
 * Plugin Name: Test
 * Description: Test transients and widget
 * Author: Morais Junior
 * Author URI: https://github.com/worais/
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define('PLUGIN_DIR', dirname( __FILE__ ));
require PLUGIN_DIR . "/consts/configs.php";
require PLUGIN_DIR . "/includes/api.php";

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
        include PLUGIN_DIR . "/templates/tab-content.php";
    }   

    public static function get_user_config(){
        global $current_user;

        if ( ! is_user_logged_in() ) return false;
        if ( false !== ( $data = get_transient('user_config_' . $current_user->ID) ) ) {
            return $data;
        }

        $api = new Api();

        $result = $api->post(USER_CONFIGS);

        if(!$result || !isset($result["form"])) return false;

        $data = $result["form"];
        set_transient('user_config_' . $current_user->ID, $data, 60*60*4 );
        return $data;
    }

    public static function set_user_config(){
        global $current_user;

        if(!check_admin_referer('set_user_config')){
            die();
        }

        $data = [];
        foreach( USER_CONFIGS as $key => $value ){
            $data[$key] = esc_html(sanitize_text_field($_POST[$key]));
        }

        $api = new Api();
        $result = $api->post($data);
        if(!$result || !isset($result["form"])){
            echo '<div class="woocommerce-error">Error :(</div>';
            exit;
        }

        echo '<div class="woocommerce-info">Saved!</div>';
        set_transient('user_config_' . $current_user->ID, $data, 60*60*4 );
        exit;
    }    
}

register_activation_hook( __FILE__, ['Plugin','install']);

add_action('init',                                ['Plugin', 'init']);
add_action('woocommerce_account_config_endpoint', ['Plugin', 'tab_content']);
add_filter('query_vars',                          ['Plugin', 'tab_query_vars']);
add_filter('woocommerce_account_menu_items',      ['Plugin', 'tab_menu_items']);

add_filter('get_user_config', ['Plugin', 'get_user_config']);
add_action('wp_ajax_set_user_config', ['Plugin', 'set_user_config']);