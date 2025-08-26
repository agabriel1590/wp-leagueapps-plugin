<?php
class LeagueApps_Shortcodes {
    public function __construct() {
        add_shortcode( 'leagueapps_teams', [ $this, 'render_teams' ] );
    }

    public function render_teams() {
        $api = new LeagueApps_API();
        $teams = $api->request( 'teams' );

        if ( empty( $teams ) ) {
            return '<p>No teams found.</p>';
        }

        $output = '<ul class="leagueapps-teams">';
        foreach ( $teams as $team ) {
            $output .= '<li>' . esc_html( $team['name'] ) . '</li>';
        }
        $output .= '</ul>';

        return $output;
    }
}
