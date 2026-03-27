<?php
/**
 * Uninstall routine for AWDev Plugins Updater.
 *
 * WordPress executes this file directly when the plugin is deleted via the
 * Plugins screen. The main plugin file is NOT loaded, so no constants or
 * includes from awdev-plugins-updater.php are available here.
 *
 * @see https://developer.wordpress.org/plugins/plugin-basics/uninstall-methods/
 * @package AWDev_Updater
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$options = array(
	'awdev_auto_updates_global',
	'awdev_auto_updates',
	'awdev_cache_hours',
	'awdev_managed_plugins',
);

foreach ( $options as $option ) {
	delete_option( $option );
}

global $wpdb;

// Remove all cached update transients created by AWDev_Updater.
$wpdb->query( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
	$wpdb->prepare(
		"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
		'_transient_awdev_upd_%'
	)
);
$wpdb->query( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
	$wpdb->prepare(
		"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
		'_transient_timeout_awdev_upd_%'
	)
);
