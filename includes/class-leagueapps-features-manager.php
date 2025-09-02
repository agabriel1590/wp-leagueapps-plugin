<?php
// Autoload GitHub Update Checker
require LA_PLUGIN_DIR . 'includes/plugin-update-checker-master/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

require LA_PLUGIN_DIR . 'includes/features/leagueapps-widgets/class-la-feature-leagueapps-widgets.php';

class Leagueapps_Features_Manager {

    private static $instance = null;

    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Admin settings (Settings → LeagueApps)
        if (is_admin()) {
            new Leagueapps_Admin_Settings();
            $this->load_update_checker();
        }
        // Any other bootstrapping you need can go here
        
        // Initialise the feature (front end + back end safe)
        add_action('init', ['LA_Feature_LeagueApps_Widgets', 'init']);
    }

    public function activate() {
        // Place any option initialisation here if needed later
    }

    public function deactivate() {
        // No-op, keep settings.
    }

    public function load_update_checker() {
        $updateChecker = PucFactory::buildUpdateChecker(
            'https://github.com/agabriel1590/wp-leagueapps-plugin',
            LA_PLUGIN_FILE,                     // ← important: main plugin file
            'wp-leagueapps-plugin'
        );
        $updateChecker->setBranch('main');
    }
}
