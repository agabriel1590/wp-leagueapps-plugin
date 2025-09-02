<?php
/**
 * Plugin Name: WP LeagueApps Plugin
 * Description: Extends WordPress functionality with LeagueApps API.
 * Version: 0.0.2
 * Author: Alipio Gabriel
 * Text Domain: wp-leagueapps
 */

if (!defined('ABSPATH')) exit;

// Keep header Version and constant in sync
define('LA_PLUGIN_VERSION', '0.0.2');
define('LA_PLUGIN_FILE', __FILE__);
define('LA_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('LA_PLUGIN_URL', plugin_dir_url(__FILE__));

// Load classes explicitly (simpler than a custom autoloader for now)
require_once LA_PLUGIN_DIR . 'includes/class-leagueapps-features-manager.php';
require_once LA_PLUGIN_DIR . 'includes/class-leagueapps-admin-settings.php';
require_once LA_PLUGIN_DIR . 'includes/class-leagueapps-api-client.php';

// Boot the plugin
add_action('plugins_loaded', function () {
    Leagueapps_Features_Manager::get_instance(); // will spin up admin settings inside
});

// Activation/Deactivation
function leagueapps_features_manager_activate() {
    Leagueapps_Features_Manager::get_instance()->activate();
}
register_activation_hook(__FILE__, 'leagueapps_features_manager_activate');

function leagueapps_features_manager_deactivate() {
    Leagueapps_Features_Manager::get_instance()->deactivate();
}
register_deactivation_hook(__FILE__, 'leagueapps_features_manager_deactivate');
