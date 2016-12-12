<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/ishaqj
 * @since             1.0.0
 * @package           Wp_flash_games
 *
 * @wordpress-plugin
 * Plugin Name:       WP Flash Games
 * Plugin URI:        https://github.com/ishaqj
 * Description:       WP Flash Games lets you build online arcade website.
 * Version:           1.0.0
 * Author:            Ishaq Jound
 * Author URI:        https://github.com/ishaqj
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp_flash_games
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp_flash_games-activator.php
 */
function activate_wp_flash_games() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp_flash_games-activator.php';
	Wp_flash_games_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp_flash_games-deactivator.php
 */
function deactivate_wp_flash_games() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp_flash_games-deactivator.php';
	Wp_flash_games_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wp_flash_games' );
register_deactivation_hook( __FILE__, 'deactivate_wp_flash_games' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp_flash_games.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_flash_games() {

	$plugin = new Wp_flash_games();
	$plugin->run();

}
run_wp_flash_games();
