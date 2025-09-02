<?php
if (!defined('ABSPATH')) exit;

final class LeagueApps_Api_Client {

    private string $api_key;
    private string $site_id;

    public function __construct(string $api_key, string $site_id) {
        $this->api_key = $api_key;
        $this->site_id = $site_id;
    }

    /**
     * Fetch programs from public API.
     * $args supports:
     *  - states: array|string  e.g. ['LIVE','COMPLETED'] or 'LIVE,COMPLETED'
     *  - page_num: int
     *  - page_size: int
     *  - include_deleted: bool
     *  - include_private: bool
     */
    public function get_programs(array $args = []) {
        if (!$this->api_key || !$this->site_id) {
            return new WP_Error('la_missing_credentials', __('LeagueApps API Key or Site ID is missing', 'wp-leagueapps'));
        }

        $base     = apply_filters('la/api_base', 'https://public.leagueapps.io');
        $endpoint = trailingslashit($base) . 'v1/sites/' . rawurlencode((string) $this->site_id) . '/programs';

        // Normalise args
        $states = $this->normalise_states($args['states'] ?? $args['includeState'] ?? 'LIVE'); // includeState is required
        $page_num   = isset($args['page_num']) ? (int) $args['page_num'] : null;
        $page_size  = isset($args['page_size']) ? (int) $args['page_size'] : null;
        $inc_deleted = isset($args['include_deleted']) ? (bool) $args['include_deleted'] : null;
        $inc_private = isset($args['include_private']) ? (bool) $args['include_private'] : null;

        // Build base params. API requires la-api-key in the query.
        $params = [
            'la-api-key'     => $this->api_key,
            'includeDeleted' => isset($inc_deleted) ? $this->bool_to_string($inc_deleted) : null,
            'includePrivate' => isset($inc_private) ? $this->bool_to_string($inc_private) : null,
            'pageNum'        => $page_num,
            // The docs show both pageSize and page-size in different places, so we send both for safety.
            'pageSize'       => $page_size,
            'page-size'      => $page_size,
        ];
        // Remove nulls
        $params = array_filter($params, static fn($v) => $v !== null && $v !== '');

        // Build query string with repeated includeState keys as required by docs
        $query = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
        foreach ($states as $state) {
            $query .= ($query === '' ? '' : '&') . 'includeState=' . rawurlencode($state);
        }

        $url = $endpoint . ($query ? '?' . $query : '');

        // Cache
        $cache_key = 'la_prog_' . md5($this->site_id . '|' . $url);
        $ttl = (int) apply_filters('la/widgets/cache_ttl', 300);
        if (($cached = get_transient($cache_key)) !== false) {
            return $cached;
        }

        $response = wp_remote_get($url, [
            'headers' => ['Accept' => 'application/json'],
            'timeout' => (int) apply_filters('la/api_timeout', 12),
        ]);

        if (is_wp_error($response)) {
            return $response;
        }

        $code = (int) wp_remote_retrieve_response_code($response);
        if ($code < 200 || $code >= 300) {
            return new WP_Error('la_bad_status', sprintf(__('LeagueApps API responded with status %d', 'wp-leagueapps'), $code));
        }

        $body = wp_remote_retrieve_body($response);
        $json = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($json)) {
            return new WP_Error('la_bad_json', __('Invalid JSON from LeagueApps API', 'wp-leagueapps'));
        }

        $items = $this->extract_program_items($json);
        set_transient($cache_key, $items, $ttl);
        return $items;
    }

    private function normalise_states($states): array {
        if (is_string($states)) {
            $states = array_map('trim', explode(',', $states));
        }
        $states = is_array($states) ? $states : ['LIVE'];
        // Uppercase and de-duplicate
        $states = array_values(array_unique(array_map(static fn($s) => strtoupper((string) $s), $states)));
        // Ensure at least one state
        return !empty($states) ? $states : ['LIVE'];
    }

    private function bool_to_string(bool $b): string {
        return $b ? 'true' : 'false';
    }

    private function extract_program_items(array $json): array {
        // Common shapes
        $candidates = [];
        if (isset($json[0]) && is_array($json[0])) {
            $candidates = $json;
        } elseif (isset($json['items']) && is_array($json['items'])) {
            $candidates = $json['items'];
        } elseif (isset($json['programs']) && is_array($json['programs'])) {
            $candidates = $json['programs'];
        } elseif (isset($json['data']) && is_array($json['data'])) {
            $candidates = $json['data'];
        }

        $candidates = apply_filters('la/programs_items_raw', $candidates, $json);

        $out = [];
        foreach ($candidates as $item) {
            if (!is_array($item)) continue;
            $out[] = [
                'name'       => $this->first_non_empty($item, ['name','programName','title','displayName']),
                'signup_url' => $this->guess_signup_url($item),
                '_raw'       => $item,
            ];
        }

        return array_values(array_filter(
            apply_filters('la/programs_items_normalised', $out, $json),
            static fn($row) => !empty($row['name'])
        ));
    }

    private function first_non_empty(array $item, array $keys): string {
        foreach ($keys as $k) {
            if (isset($item[$k]) && is_string($item[$k]) && $item[$k] !== '') {
                return (string) $item[$k];
            }
        }
        return '';
    }

    private function guess_signup_url(array $item): string {
        $keys = [
            'registrationUrl','registrationURL','registration_url',
            'regUrl','regURL','signup_url','signupUrl','signUpUrl',
            'url','programUrl','programURL'
        ];
        foreach ($keys as $k) {
            if (!empty($item[$k]) && is_string($item[$k])) return $item[$k];
        }
        // Nested variants
        $paths = ['urls.registration','links.register','_links.register.href'];
        foreach ($paths as $p) {
            $val = $this->get_by_dotpath($item, $p);
            if (is_string($val) && $val !== '') return $val;
        }
        return '';
    }

    private function get_by_dotpath(array $arr, string $path) {
        $ref = $arr;
        foreach (explode('.', $path) as $seg) {
            if (is_array($ref) && array_key_exists($seg, $ref)) $ref = $ref[$seg];
            else return null;
        }
        return $ref;
    }
}
