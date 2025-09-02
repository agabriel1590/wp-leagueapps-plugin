<?php
if (!defined('ABSPATH')) {
    exit;
}

class Leagueapps_Admin_Settings {

    private $settings_option = 'leagueapps_features_settings';

    public function __construct() {
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_core_settings'));
    }

    // Add the main settings page for Blogtec Features
    public function add_settings_page() {
        add_options_page(
            __('LeagueApps Features Settings', 'leagueapps-features-manager'),
            __('LeagueApps Features', 'leagueapps-features-manager'),
            'manage_options',
            'leagueapps-features-settings',
            array($this, 'render_settings_page')
        );
    }

    // Register the core settings (feature section but not fields)
    public function register_core_settings() {
        register_setting(
            $this->settings_option,
            $this->settings_option,
            array($this, 'sanitize_settings')
        );

        add_settings_section(
            'leagueapps_features_section',
            __('Manage Plugin Features', 'leagueapps-features-manager'),
            null,
            'leagueapps-features-settings'
        );
    }

    // Sanitize settings input
    public function sanitize_settings($input) {
        $new_input = array();
        $new_input['leagueapps_api_key'] = isset($input['leagueapps_api_key']) ? 1 : 0;
        return $new_input;
    }

    // Render the settings page
    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__('LeagueApps Features Settings', 'leagueapps-features-manager'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields($this->settings_option);
                do_settings_sections('leagueapps-features-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}
