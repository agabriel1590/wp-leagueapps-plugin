<?php
class LeagueApps_Admin {
    public function __construct() {
        add_action( 'admin_menu', [ $this, 'add_admin_page' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
    }

    public function add_admin_page() {
        add_menu_page(
            'LeagueApps Settings',
            'LeagueApps',
            'manage_options',
            'leagueapps-settings',
            [ $this, 'settings_page_html' ],
            'dashicons-groups'
        );
    }

    public function register_settings() {
        register_setting( 'leagueapps_settings', 'leagueapps_api_key' );
    }

    public function settings_page_html() {
        ?>
        <div class="wrap">
            <h1>LeagueApps Settings</h1>
            <form method="post" action="options.php">
                <?php settings_fields( 'leagueapps_settings' ); ?>
                <?php do_settings_sections( 'leagueapps_settings' ); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">API Key</th>
                        <td><input type="text" name="leagueapps_api_key" 
                                   value="<?php echo esc_attr( get_option('leagueapps_api_key') ); ?>" /></td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}