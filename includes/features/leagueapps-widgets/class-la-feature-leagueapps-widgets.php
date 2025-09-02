<?php
if (!defined('ABSPATH')) exit;

final class LA_Feature_LeagueApps_Widgets {

    public static function init(): void {
        add_shortcode('la_program_table', [__CLASS__, 'shortcode_program_table']);
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_styles']);
    }

    public static function enqueue_styles() {
        $handle = 'la-widgets';
        if (!wp_style_is($handle, 'enqueued')) {
            $css = '.la-program-table{width:100%;border-collapse:collapse}
            .la-program-table th,.la-program-table td{padding:.6rem .8rem;border-bottom:1px solid #e5e7eb;text-align:left}
            .la-program-table th{font-weight:600}';
            wp_register_style($handle, false, [], LA_PLUGIN_VERSION);
            wp_enqueue_style($handle);
            wp_add_inline_style($handle, $css);
        }
    }

    public static function shortcode_program_table($atts = []): string {
        $atts = shortcode_atts([
            // Comma separated. Docs require includeState.
            'states'          => 'LIVE',
            // Pagination
            'page_size'       => '',
            'page_num'        => '',
            // Optional flags
            'include_deleted' => '',
            'include_private' => '',
            // Limit rows after fetch
            'limit'           => '',
        ], $atts, 'la_program_table');

        $settings = get_option('leagueapps_features_settings', ['api_key' => '', 'site_id' => '']);
        $api_key  = (string) ($settings['api_key'] ?? '');
        $site_id  = (string) ($settings['site_id'] ?? '');

        if (!$api_key || !$site_id) {
            return '<div class="notice notice-warning" role="alert">' .
                esc_html__('LeagueApps API credentials are not configured. Add them in Settings → LeagueApps.', 'wp-leagueapps') .
            '</div>';
        }

        if (!class_exists('LeagueApps_Api_Client')) {
            return esc_html__('LeagueApps API client is not available.', 'wp-leagueapps');
        }

        $client = new LeagueApps_Api_Client($api_key, $site_id);

        $args = [
            'states'          => $atts['states'],
            'page_size'       => $atts['page_size'] !== '' ? (int) $atts['page_size'] : null,
            'page_num'        => $atts['page_num']  !== '' ? (int) $atts['page_num']  : null,
            'include_deleted' => self::to_bool_or_null($atts['include_deleted']),
            'include_private' => self::to_bool_or_null($atts['include_private']),
        ];

        $data = $client->get_programs($args);
        if (is_wp_error($data)) {
            return sprintf('<div class="notice notice-error" role="alert">%s</div>', esc_html($data->get_error_message()));
        }



        $limit = absint($atts['limit']);
        if ($limit > 0) $data = array_slice($data, 0, $limit);

        if (empty($data)) return '<div>' . esc_html__('No programs found.', 'wp-leagueapps') . '</div>';

        ob_start(); ?>
        
        <table class="la-program-table">
            <thead>
                <tr>
                    <th><?php echo esc_html__('Program', 'wp-leagueapps'); ?></th>
                    <th><?php echo esc_html__('Sign Up', 'wp-leagueapps'); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($data as $row): ?>
                <tr>
                    <td><?php echo esc_html((string) ($row['name'] ?? '')); ?></td>
                    <td>
                        <?php  $url = (string) $row['_raw']['programUrlHtml'];  ?>
                        <?php if (!empty($url)): ?>
                            <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener">
                                <?php echo esc_html__('Register', 'wp-leagueapps'); ?>
                            </a>
                        <?php else: ?>
                            <em><?php echo esc_html__('N/A', 'wp-leagueapps'); ?></em>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php
        return trim(ob_get_clean());
    }

    private static function to_bool_or_null($val) {
        if ($val === '' || $val === null) return null;
        $val = strtolower((string) $val);
        return in_array($val, ['1','true','yes','y','on'], true);
    }
}
