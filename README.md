# WP LeagueApps Plugin

A custom WordPress plugin that extends functionality of WordPress using the [LeagueApps API](https://leagueapps.notion.site/LeagueApps-API-Docs-for-Developers-e32aa52e1b0a47bfa20e4c0563fafeca).  
This plugin allows site admins to connect their LeagueApps account, fetch data (teams, events, tryouts, etc.), and display it on their WordPress site via shortcodes.

---

## Features

- Connect WordPress to **LeagueApps API** using your API key
- Admin settings page for API key management
- Shortcodes to display LeagueApps data (e.g. `[leagueapps_teams]`)
- Extendable structure for additional endpoints (camps, tryouts, programs, etc.)
- Organized plugin architecture for easy maintenance

---

## Installation

1. Download or clone this repository into your WordPress `plugins` directory:
   ```bash
   git clone https://github.com/yourusername/wp-leagueapps-plugin.git


Or upload as a .zip file in the WordPress dashboard.

Activate the plugin in WordPress Admin → Plugins.

Go to Settings → LeagueApps and enter your API Key.

Usage
Shortcodes

Teams List

[leagueapps_teams]


Displays a list of teams fetched from your LeagueApps account.

More shortcodes will be added for events, tryouts, camps, and rosters.

File Structure
wp-leagueapps-plugin/
│
├── wp-leagueapps-plugin.php       # Main plugin bootstrap
├── includes/
│   ├── class-leagueapps-api.php   # Handles API requests
│   ├── class-leagueapps-admin.php # Admin settings page
│   ├── class-leagueapps-shortcodes.php # Shortcode registration
│   └── helpers.php                # Utility functions
├── assets/
│   ├── css/style.css              # Custom styles
│   └── js/script.js               # Custom scripts
└── README.md                      # Documentation

Development

PHP: 7.4+

WordPress: 5.8+

LeagueApps API Key required

Hooks

plugins_loaded – initializes plugin classes.

add_shortcode – registers shortcodes.

Roadmap

 Add shortcode for Events

 Add shortcode for Camps & Tryouts

 Create Custom Post Type sync option for offline caching

 Add Gutenberg block integration

 Improve error handling & logging

License

GPL-2.0-or-later
Free to use and modify under the terms of the GPL license.

Author

Developed by [Your Name] for LeagueApps Design Shop.


---

Do you want me to also draft a **Usage Examples section** with multiple sample shortcodes (`teams`, `events`, `tryouts`) so future contributors know how to expand it?


ChatGPT can make mistakes. Check important info.
