<?php
// Autoload github Update Checker
require BLOGTEC_PLUGIN_DIR . 'includes/plugin-update-checker-master/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

class Leagueapps_Features_Manager {

    private static $instance = null;

    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Hook the load_textdomain function to init action
        $this->load_textdomain();

        //load update checker github
        $this->load_update_checker();

        // Load admin settings for enabling/disabling features
        $this->load_admin_settings();
        $this->load_features();
    }

    public function activate() {
    }

    public function deactivate() {
        // Delegate the deactivation to each feature's admin settings class
    }

    private function load_admin_settings() {
    }


    private function load_features() {
        $options = get_option('leagueapps_features_settings', array());
    }

    public function load_update_checker() {
        $updateChecker = PucFactory::buildUpdateChecker(
            'https://github.com/agabriel1590/wp-leagueapps-plugin',
            plugin_dir_path(__DIR__) . 'wp-leagueapps-plugin.php',
            'wp-leagueapps-plugin'
        );

        // Set the branch to check the plugin updates from
        $updateChecker->setBranch('main');

        // Optional: Add an authentication token if your GitHub repository is private.
        // $updateChecker->setAuthentication('your-github-token-here');
    }
}
