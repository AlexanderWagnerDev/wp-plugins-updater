<?php
/**
 * Self-hosted update API endpoint for: DarkAdmin - Dark Mode for Adminpanel
 * Deploy this file to: https://updates.alexanderwagnerdev.com/api/darkadmin.php
 *
 * Update 'version' and 'download_url' on every new release.
 */
header( 'Content-Type: application/json; charset=utf-8' );

echo json_encode( [
	'slug'         => 'darkadmin-dark-mode-for-adminpanel',
	'version'      => '0.0.6',
	'download_url' => 'https://updates.alexanderwagnerdev.com/zips/darkadmin-dark-mode-for-adminpanel.zip',
	'details_url'  => 'https://alexanderwagnerdev.com/plugins/darkadmin',
	'changelog'    => '<h4>0.0.6</h4><ul><li>Initial self-hosted release</li></ul>',
	'tested'       => '6.9',
	'requires'     => '6.0',
	'requires_php' => '7.4',
], JSON_PRETTY_PRINT );
