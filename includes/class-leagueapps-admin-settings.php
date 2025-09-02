<?php
if (!defined('ABSPATH')) exit;

class Leagueapps_Admin_Settings {

    private $option_name     = 'leagueapps_features_settings'; // stores an array
    private $settings_group  = 'leagueapps_features_group';    // used by settings_fields()
    private $settings_page   = 'leagueapps-features-settings'; // slug for options page
    private $settings_section = 'leagueapps_features_section'; // section id

    public function __construct() {
        add_action('admin_menu',  [$this, 'add_settings_page']);
        add_action('admin_init',  [$this, 'register_settings']);
    }

    public function add_settings_page() {
        add_options_page(
            __('LeagueApps Features Settings', 'wp-leagueapps'),
            __('LeagueApps', 'wp-leagueapps'),
            'manage_options',
            $this->settings_page,
            [$this, 'render_settings_page']
        );
    }

    public function register_settings() {
        // Register the single array option
        register_setting(
            $this->settings_group,
            $this->option_name,
            [
                'type'              => 'array',
                'sanitize_callback' => [$this, 'sanitize_settings'],
                'default'           => [
                    'api_key' => '',
                    'site_id' => '',
                ],
                'show_in_rest'      => false,
            ]
        );

        // Section
        add_settings_section(
            $this->settings_section,
            __('Credentials', 'wp-leagueapps'),
            function () {
                echo '<p>' . esc_html__('Enter your LeagueApps API credentials.', 'wp-leagueapps') . '</p>';
            },
            $this->settings_page
        );

        // Field: API Key
        add_settings_field(
            'leagueapps_api_key',
            __('Public API Key', 'wp-leagueapps'),
            [$this, 'render_api_key_field'],
            $this->settings_page,
            $this->settings_section
        );

        // Field: Site ID
        add_settings_field(
            'leagueapps_site_id',
            __('Site ID', 'wp-leagueapps'),
            [$this, 'render_site_id_field'],
            $this->settings_page,
            $this->settings_section
        );
    }

    public function sanitize_settings($value) {
        $out = [
            'api_key' => '',
            'site_id' => '',
        ];
        if (is_array($value)) {
            $out['api_key'] = isset($value['api_key']) ? sanitize_text_field($value['api_key']) : '';
            // Site IDs are integers in LA, but keep text_field for flexibility then cast later when used
            $out['site_id'] = isset($value['site_id']) ? sanitize_text_field($value['site_id']) : '';
        }
        return $out;
    }

    private function get_settings() {
        $defaults = ['api_key' => '', 'site_id' => ''];
        $opt = get_option($this->option_name, $defaults);
        return wp_parse_args(is_array($opt) ? $opt : [], $defaults);
    }

    public function render_api_key_field() {
        $settings = $this->get_settings();
        printf(
            '<input type="text" class="regular-text" name="%1$s[api_key]" value="%2$s" placeholder="%3$s" />',
            esc_attr($this->option_name),
            esc_attr($settings['api_key']),
            esc_attr__('Your LeagueApps API Key', 'wp-leagueapps')
        );
        echo '<p class="description">' . esc_html__('Generate this in your LeagueApps admin.', 'wp-leagueapps') . '</p>';
    }

    public function render_site_id_field() {
        $settings = $this->get_settings();
        printf(
            '<input type="text" class="regular-text" name="%1$s[site_id]" value="%2$s" placeholder="%3$s" />',
            esc_attr($this->option_name),
            esc_attr($settings['site_id']),
            esc_attr__('e.g. 12345', 'wp-leagueapps')
        );
    }

    public function render_settings_page() { ?>
        <div class="wrap">
            <h1><?php echo esc_html__('LeagueApps Features Settings', 'wp-leagueapps'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields($this->settings_group);        // prints nonce & group
                do_settings_sections($this->settings_page);     // prints section + fields
                submit_button();
                ?>
            </form>
        </div>
    <?php }
}
