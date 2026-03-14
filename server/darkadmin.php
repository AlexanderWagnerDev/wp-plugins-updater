<?php
/**
 * Self-hosted update API endpoint for: DarkAdmin - Dark Mode for Adminpanel
 * Deploy to: https://wp-plugins-updates.awdev.space/api/darkadmin.php
 *
 * Update 'version' and place the new ZIP at the download_url path on each release.
 */
header( 'Content-Type: application/json; charset=utf-8' );
header( 'Cache-Control: no-store' );

echo json_encode( [
	'slug'         => 'darkadmin-dark-mode-for-adminpanel',
	'name'         => 'DarkAdmin – Dark Mode for Adminpanel',
	'version'      => '0.0.6',
	'download_url' => 'https://wp-plugins-updates.awdev.space/zips/darkadmin-dark-mode-for-adminpanel.zip',
	'details_url'  => 'https://alexanderwagnerdev.com/plugins/darkadmin',
	'changelog'    => '<h4>0.0.6</h4><ul><li>Initial self-hosted release</li></ul>',
	'tested'       => '6.9',
	'requires'     => '6.0',
	'requires_php' => '7.4',
], JSON_PRETTY_PRINT );
