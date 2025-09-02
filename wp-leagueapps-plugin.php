<?php
/**
 * Plugin Name: WP LeagueApps Plugin
 * Description: Extends WordPress functionality with LeagueApps API.
 * Version: 0.0.2
 * Author: Alipio Gabriel
 * Text Domain: wp-leagueapps
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Prevent direct access
}

// Define constants
define( 'WP_LEAGUEAPPS_PATH', plugin_dir_path( __FILE__ ) );
define( 'WP_LEAGUEAPPS_URL', plugin_dir_url( __FILE__ ) );

// Include core files
require_once WP_LEAGUEAPPS_PATH . 'includes/updater.php';
require_once WP_LEAGUEAPPS_PATH . 'includes/class-leagueapps-api.php';
require_once WP_LEAGUEAPPS_PATH . 'includes/class-leagueapps-admin.php';
require_once WP_LEAGUEAPPS_PATH . 'includes/class-leagueapps-shortcodes.php';
require_once WP_LEAGUEAPPS_PATH . 'includes/helpers.php';

// Init plugin
function wp_leagueapps_init() {
    new LeagueApps_Admin();
    new LeagueApps_Shortcodes();
    if (is_admin()) { // note the use of is_admin() to double check that this is happening in the admin
    $config = array(
        'slug' => plugin_basename(__FILE__), // this is the slug of your plugin
        'proper_folder_name' => 'wp-leagueapps-plugin', // this is the name of the folder your plugin lives in
        'api_url' => 'https://api.github.com/repos/agabriel1590/wp-leagueapps-plugin', // the GitHub API url of your GitHub repo
        'raw_url' => 'https://raw.github.com/agabriel1590/wp-leagueapps-plugin/master', // the GitHub raw url of your GitHub repo
        'github_url' => 'https://github.com/agabriel1590/wp-leagueapps-plugin', // the GitHub url of your GitHub repo
        'zip_url' => 'https://github.com/agabriel1590/wp-leagueapps-plugin/zipball/master', // the zip url of the GitHub repo
        'sslverify' => true, // whether WP should check the validity of the SSL cert when getting an update, see https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/2 and https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/4 for details
        'requires' => '6.8.2', // which version of WordPress does your plugin require?
        'tested' => '6.8.2', // which version of WordPress is your plugin tested up to?
        'readme' => 'README.md', // which file to use as the readme for the version number
        'access_token' => '', // Access private repositories by authorizing under Plugins > GitHub Updates when this example plugin is installed
    );
    new WP_GitHub_Updater($config);
    }
}
add_action( 'plugins_loaded', 'wp_leagueapps_init' );
