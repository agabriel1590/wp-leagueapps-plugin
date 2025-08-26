<?php
class LeagueApps_API {
    private $api_key;
    private $base_url = 'https://api.leagueapps.com/v1/'; // Example endpoint

    public function __construct() {
        $this->api_key = get_option( 'leagueapps_api_key' );
    }

    public function request( $endpoint, $args = [] ) {
        $url = $this->base_url . $endpoint;
        $response = wp_remote_get( $url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->api_key
            ]
        ]);

        if ( is_wp_error( $response ) ) {
            return [];
        }

        return json_decode( wp_remote_retrieve_body( $response ), true );
    }
}
