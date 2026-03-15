<?php
/**
 * Plugin Name: AWDev Plugins Updater
 * Plugin URI: https://github.com/AlexanderWagnerDev/wp-plugins-updater
 * Description: Keeps AlexanderWagnerDev plugins up to date — without WordPress.org. Updates are served from a self-hosted server, so every release ships on your own schedule.
 * Version: 0.0.3
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

define( 'AWDEV_UPDATER_VERSION', '0.0.3' );
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
 */
add_action( 'plugins_loaded', function () {
	$managed = (array) get_option( 'awdev_managed_plugins', [] );

	$built_in = [
		'awdev-plugins-updater/awdev-plugins-updater.php' => 'awdev-plugins-updater',
		'darkadmin-dark-mode-for-adminpanel/darkadmin.php' => 'darkadmin-dark-mode-for-adminpanel',
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
