<?php
/**
 * Plugin Name: AWDev Plugin Updater
 * Plugin URI: https://github.com/AlexanderWagnerDev/wp-plugins-updater
 * Description: Self-hosted updater for AlexanderWagnerDev plugins. Fetches updates from a custom domain instead of WordPress.org.
 * Version: 1.0.0
 * Requires at least: 6.0
 * Tested up to: 6.9
 * Requires PHP: 7.4
 * Author: AlexanderWagnerDev
 * Author URI: https://alexanderwagnerdev.com
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: awdev-plugin-updater
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once plugin_dir_path( __FILE__ ) . 'includes/class-awdev-updater.php';

/**
 * Register all managed AlexanderWagnerDev plugins here.
 * Format: new AWDev_Updater( 'plugin-folder/plugin-file.php', 'https://updates.example.com/api/plugin.php' );
 */
add_action( 'init', function () {
	new AWDev_Updater(
		'darkadmin-dark-mode-for-adminpanel/darkadmin.php',
		'https://updates.alexanderwagnerdev.com/api/darkadmin.php'
	);
	// Add more plugins here as needed:
	// new AWDev_Updater( 'other-plugin/other-plugin.php', 'https://updates.alexanderwagnerdev.com/api/other-plugin.php' );
} );
