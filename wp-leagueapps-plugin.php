<?php
/**
 * Plugin Name: WP LeagueApps Plugin
 * Description: Extends WordPress functionality with LeagueApps API.
 * Version: 0.0.1
 * Author: Alipio Gabriel
 * Text Domain: wp-leagueapps
 */

if (!defined('ABSPATH')) {
    exit;
}

define('LA_PLUGIN_VERSION', '0.0.1');
define('LA_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('LA_PLUGIN_URL', plugin_dir_url(__FILE__));

// Autoload Feature Classes
spl_autoload_register(function ($class_name) {
    if (false !== strpos($class_name, 'Leagueapps')) {
        $file_name = str_replace('_', '-', strtolower($class_name)) . '.php';

        $paths = [
            LA_PLUGIN_DIR . 'includes/',              
            LA_PLUGIN_DIR . 'includes/features/',    
            LA_PLUGIN_DIR . 'includes/features/leagueapps-widgets/',
            LA_PLUGIN_DIR . 'includes/interfaces/',
        ];

        foreach ($paths as $path) {
            $file_path = $path . 'class-' . $file_name;
            if (file_exists($file_path)) {
                require_once $file_path;
                break;
            }
        }
    }
});

function leagueapps_features_manager_init() {
    Leagueapps_Features_Manager::get_instance();
}
add_action('plugins_loaded', 'leagueapps_features_manager_init');

function leagueapps_features_manager_activate() {
    Leagueapps_Features_Manager::get_instance()->activate();
}
register_activation_hook(__FILE__, 'Leagueapps_features_manager_activate');

function leagueapps_features_manager_deactivate() {
    Leagueapps_Features_Manager::get_instance()->deactivate();
}
register_deactivation_hook(__FILE__, 'leagueapps_features_manager_deactivate');