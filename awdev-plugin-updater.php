<?php
/**
 * Plugin Name: AWDev Plugins Updater
 * Plugin URI: https://github.com/AlexanderWagnerDev/wp-plugins-updater
 * Description: Self-hosted updater for AlexanderWagnerDev plugins. Manages updates from a self-hosted server instead of WordPress.org.
 * Version: 0.0.2
 * Requires at least: 6.0
 * Tested up to: 6.9
 * Requires PHP: 7.4
 * Author: AlexanderWagnerDev
 * Author URI: https://alexanderwagnerdev.com
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: awdev-plugins-updater
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'AWDEV_UPDATER_VERSION', '0.0.2' );
define( 'AWDEV_UPDATER_URL', plugin_dir_url( __FILE__ ) );
define( 'AWDEV_UPDATER_PATH', plugin_dir_path( __FILE__ ) );
define( 'AWDEV_UPDATE_SERVER', 'https://wp-plugins-updates.awdev.space/api' );

/**
 * Load plugin text domain for translations.
 */
add_action( 'init', function () {
	load_plugin_textdomain(
		'awdev-plugins-updater',
		false,
		dirname( plugin_basename( __FILE__ ) ) . '/languages'
	);
} );

require_once AWDEV_UPDATER_PATH . 'includes/class-awdev-updater.php';
require_once AWDEV_UPDATER_PATH . 'includes/settings.php';

/**
 * Register all managed AlexanderWagnerDev plugins.
 *
 * Built-in plugins are always registered automatically.
 * This plugin registers itself so it receives self-updates from the update server.
 * Additional plugins can be added via the Settings page.
 */
add_action( 'plugins_loaded', function () {
	$managed = (array) get_option( 'awdev_managed_plugins', [] );

	$built_in = [
		// Self-update: this plugin updates itself via the update server.
		'awdev-plugin-updater/awdev-plugin-updater.php' => 'awdev-plugin-updater',
		// DarkAdmin - Dark Mode for Adminpanel.
		'wp-darkadmin-plugin/darkadmin.php' => 'darkadmin',
	];

	foreach ( $built_in as $basename => $api_slug ) {
		new AWDev_Updater( $basename, AWDEV_UPDATE_SERVER . '/' . $api_slug . '.php' );
	}

	foreach ( $managed as $basename => $api_slug ) {
		if ( ! isset( $built_in[ $basename ] ) ) {
			new AWDev_Updater( $basename, AWDEV_UPDATE_SERVER . '/' . sanitize_key( $api_slug ) . '.php' );
		}
	}
} );

/**
 * Add settings link in the Plugins list.
 */
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), function ( $actions ) {
	$url                 = admin_url( 'options-general.php?page=awdev-plugins-updater' );
	$actions['settings'] = '<a href="' . esc_url( $url ) . '">' . __( 'Settings', 'awdev-plugins-updater' ) . '</a>';
	return $actions;
} );
