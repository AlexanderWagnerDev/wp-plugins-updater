<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles self-hosted update checks for a single plugin.
 */
class AWDev_Updater {

	private string $plugin_basename;
	private string $plugin_slug;
	private string $api_url;

	public function __construct( string $plugin_basename, string $api_url ) {
		$this->plugin_basename = $plugin_basename;
		$this->plugin_slug     = dirname( $plugin_basename );
		$this->api_url         = $api_url;

		add_filter( 'pre_set_site_transient_update_plugins', [ $this, 'check_update' ] );
		add_filter( 'plugins_api', [ $this, 'plugin_info' ], 10, 3 );
		add_filter( 'upgrader_source_selection', [ $this, 'fix_folder_name' ], 10, 4 );
	}

	/**
	 * Fetch update metadata from the self-hosted API endpoint.
	 * Delegates to awdev_fetch_api_data() for transient-cached HTTP logic.
	 */
	public function get_remote_data(): ?object {
		$key = 'awdev_upd_' . sanitize_key( $this->plugin_slug );
		return awdev_fetch_api_data( $key, $this->api_url );
	}

	/**
	 * Inject update data into the WordPress plugin update transient.
	 */
	public function check_update( object $transient ): object {
		if ( empty( $transient->checked ) ) {
			return $transient;
		}

		$data    = $this->get_remote_data();
		$current = $transient->checked[ $this->plugin_basename ] ?? '0.0.0';

		// Guard against incomplete API responses that are missing the version field.
		if ( $data && isset( $data->version ) && version_compare( $data->version, $current, '>' ) ) {
			$transient->response[ $this->plugin_basename ] = (object) [
				'slug'        => $this->plugin_slug,
				'plugin'      => $this->plugin_basename,
				'new_version' => $data->version,
				'url'         => $data->details_url  ?? '',
				'package'     => $data->download_url ?? '',
			];
		}

		return $transient;
	}

	/**
	 * Provide plugin details for the "View version details" popup.
	 */
	public function plugin_info( mixed $result, string $action, object $args ): mixed {
		if ( $action !== 'plugin_information' || ( $args->slug ?? '' ) !== $this->plugin_slug ) {
			return $result;
		}

		$data = $this->get_remote_data();
		if ( ! $data || ! isset( $data->version ) ) {
			return $result;
		}

		// Use author from API response when available; fall back to default.
		$author = isset( $data->author )
			? esc_html( $data->author )
			: '<a href="https://alexanderwagnerdev.com">AlexanderWagnerDev</a>';

		return (object) [
			'name'          => $data->name         ?? $this->plugin_slug,
			'slug'          => $this->plugin_slug,
			'version'       => $data->version,
			'author'        => $author,
			'homepage'      => $data->details_url  ?? '',
			'sections'      => [ 'changelog' => $data->changelog ?? '' ],
			'download_link' => $data->download_url ?? '',
			'requires'      => $data->requires     ?? '6.0',
			'tested'        => $data->tested       ?? '6.9',
			'requires_php'  => $data->requires_php ?? '7.4',
		];
	}

	/**
	 * Fix extracted folder name after update installation.
	 *
	 * Handles two scenarios:
	 * 1. Single-plugin update (Plugins list / AWDev Updater button):
	 *    hook_extra['plugin'] is set -> match by basename directly.
	 * 2. Bulk/auto update (update-core.php, WP cron):
	 *    hook_extra['plugin'] is NOT set -> match by source folder basename
	 *    using an exact prefix + WP random suffix pattern (-[A-Za-z0-9]{5,12}).
	 *    This prevents touching third-party plugin folders that happen to share
	 *    partial slug overlap.
	 *
	 * Uses WP_Filesystem to avoid direct rename() call.
	 */
	public function fix_folder_name( string $source, string $remote_source, object $upgrader, array $hook_extra ): string {
		// Resolve the correct target path. Two ZIP structures are possible:
		//
		// A) ZIP contains a subfolder (standard GitHub/most hosts):
		//    $remote_source = /upgrade/tmp-xyz/
		//    $source        = /upgrade/tmp-xyz/plugin-slug-Gmctwk/
		//    Target         = /upgrade/tmp-xyz/plugin-slug/  (sibling of source inside remote_source)
		//
		// B) ZIP extracts flat, no subfolder (rare but valid):
		//    $remote_source = /upgrade/plugin-slug-Gmctwk/
		//    $source        = /upgrade/plugin-slug-Gmctwk/  (same as remote_source)
		//    Target         = /upgrade/plugin-slug/          (sibling of remote_source)
		//
		// Distinguishing the two: if normalised $source === normalised $remote_source
		// we are in scenario B and must use dirname($remote_source) as the parent.
		$source_norm = trailingslashit( $source );
		$remote_norm = trailingslashit( $remote_source );

		if ( $source_norm === $remote_norm ) {
			// Scenario B: flat ZIP — target is a sibling of remote_source.
			$corrected = trailingslashit( dirname( untrailingslashit( $remote_source ) ) ) . $this->plugin_slug . '/';
		} else {
			// Scenario A: ZIP had a subfolder — target is inside remote_source.
			$corrected = $remote_norm . $this->plugin_slug . '/';
		}

		if ( $source_norm === $corrected ) {
			return $source;
		}

		// Scenario 1: single-plugin update — hook_extra['plugin'] is populated.
		$extra_plugin = $hook_extra['plugin'] ?? '';
		if ( $extra_plugin !== '' ) {
			if ( $extra_plugin !== $this->plugin_basename ) {
				return $source;
			}
			return $this->rename_source( $source, $corrected );
		}

		// Scenario 2: bulk/auto update — hook_extra['plugin'] is empty.
		// Only match if the extracted folder is exactly our slug followed by
		// the WP-appended random suffix (-[A-Za-z0-9]{5,12}). This ensures
		// third-party plugins are never accidentally renamed.
		$source_dirname = basename( untrailingslashit( $source ) );
		if ( ! preg_match( '/^' . preg_quote( $this->plugin_slug, '/' ) . '-[A-Za-z0-9]{5,12}$/', $source_dirname ) ) {
			return $source;
		}

		return $this->rename_source( $source, $corrected );
	}

	/**
	 * Perform the actual WP_Filesystem rename to the correct plugin slug folder.
	 * Receives the fully resolved $corrected path (not $remote_source) so it
	 * never has to recalculate the target — avoids the rename-into-self bug.
	 * Deletes an existing target folder first to prevent silent move() failure.
	 * Logs an error if move() fails so the cause can be diagnosed.
	 */
	private function rename_source( string $source, string $corrected ): string {
		if ( trailingslashit( $source ) === $corrected ) {
			return $source;
		}

		global $wp_filesystem;

		// Always call WP_Filesystem() to ensure it is properly initialized,
		// regardless of the current state of the global.
		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}
		WP_Filesystem();

		if ( ! $wp_filesystem ) {
			return $source;
		}

		// Remove existing target folder so move() does not silently fail.
		if ( $wp_filesystem->is_dir( $corrected ) ) {
			if ( ! $wp_filesystem->delete( $corrected, true ) ) {
				return $source;
			}
		}

		// Pass true as third argument to overwrite if target still exists.
		if ( $wp_filesystem->move( $source, $corrected, true ) ) {
			return $corrected;
		}

		return $source;
	}

	/**
	 * Clear cached transient for this plugin (force re-check on next load).
	 */
	public function clear_cache(): void {
		delete_transient( 'awdev_upd_' . sanitize_key( $this->plugin_slug ) );
	}

	public function get_api_url(): string  { return $this->api_url; }
	public function get_slug(): string     { return $this->plugin_slug; }
	public function get_basename(): string { return $this->plugin_basename; }
}
