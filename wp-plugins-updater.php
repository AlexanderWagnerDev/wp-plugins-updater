<?php
/**
 * Plugin Name: AWDev Plugin Updater
 * Plugin URI: https://github.com/AlexanderWagnerDev/wp-plugins-updater
 * Description: Self-hosted updater for AlexanderWagnerDev plugins. Manages updates from wp-plugins-updates.awdev.space instead of WordPress.org.
 * Version: 1.0.0
 * Requires at least: 6.0
 * Tested up to: 6.9
 * Requires PHP: 7.4
 * Author: AlexanderWagnerDev
 * Author URI: https://alexanderwagnerdev.com
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: awdev-plugin-updater
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'AWDEV_UPDATER_VERSION', '1.0.0' );
define( 'AWDEV_UPDATER_URL', plugin_dir_url( __FILE__ ) );
define( 'AWDEV_UPDATER_PATH', plugin_dir_path( __FILE__ ) );
define( 'AWDEV_UPDATE_SERVER', 'https://wp-plugins-updates.awdev.space/api' );

require_once AWDEV_UPDATER_PATH . 'includes/class-awdev-updater.php';
require_once AWDEV_UPDATER_PATH . 'includes/settings.php';

/**
 * Register all managed AlexanderWagnerDev plugins.
 * Each entry: new AWDev_Updater( 'plugin-folder/plugin-file.php', 'slug' );
 * The slug maps to: AWDEV_UPDATE_SERVER . '/{slug}.php'
 */
add_action( 'init', function () {
	$managed = (array) get_option( 'awdev_managed_plugins', [] );

	// Built-in: always register DarkAdmin if installed
	$built_in = [
		'darkadmin-dark-mode-for-adminpanel/darkadmin.php' => 'darkadmin',
	];

	foreach ( $built_in as $basename => $api_slug ) {
		if ( is_plugin_active( $basename ) || array_key_exists( $basename, $managed ) ) {
			new AWDev_Updater( $basename, AWDEV_UPDATE_SERVER . '/' . $api_slug . '.php' );
		}
	}

	// Dynamically registered plugins via settings page
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
	$url = admin_url( 'options-general.php?page=awdev-updater' );
	$actions['settings'] = '<a href="' . esc_url( $url ) . '">' . __( 'Settings', 'awdev-plugin-updater' ) . '</a>';
	return $actions;
} );
