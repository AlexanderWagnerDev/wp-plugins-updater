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
	 * Cache duration is configurable via awdev_cache_hours option (default: 6).
	 */
	public function get_remote_data(): ?object {
		$key    = 'awdev_upd_' . sanitize_key( $this->plugin_slug );
		$cached = get_transient( $key );

		if ( $cached !== false ) {
			return $cached ?: null;
		}

		$response = wp_remote_get( $this->api_url, [
			'timeout'    => 10,
			'user-agent' => 'AWDev-Plugin-Updater/' . AWDEV_UPDATER_VERSION . '; ' . get_bloginfo( 'url' ),
		] );

		if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
			set_transient( $key, false, HOUR_IN_SECONDS );
			return null;
		}

		$data         = json_decode( wp_remote_retrieve_body( $response ) );
		$cache_hours  = (int) get_option( 'awdev_cache_hours', 6 );
		if ( $cache_hours < 1 ) {
			$cache_hours = 1;
		}
		set_transient( $key, $data, $cache_hours * HOUR_IN_SECONDS );

		return $data;
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

		if ( $data && version_compare( $data->version, $current, '>' ) ) {
			$transient->response[ $this->plugin_basename ] = (object) [
				'slug'        => $this->plugin_slug,
				'plugin'      => $this->plugin_basename,
				'new_version' => $data->version,
				'url'         => $data->details_url ?? '',
				'package'     => $data->download_url,
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
		if ( ! $data ) {
			return $result;
		}

		return (object) [
			'name'          => $data->name          ?? $this->plugin_slug,
			'slug'          => $this->plugin_slug,
			'version'       => $data->version,
			'author'        => '<a href="https://alexanderwagnerdev.com">AlexanderWagnerDev</a>',
			'homepage'      => $data->details_url   ?? '',
			'sections'      => [ 'changelog' => $data->changelog ?? '' ],
			'download_link' => $data->download_url,
			'requires'      => $data->requires      ?? '6.0',
			'tested'        => $data->tested        ?? '6.9',
			'requires_php'  => $data->requires_php  ?? '7.4',
		];
	}

	/**
	 * Fix extracted folder name after update installation.
	 *
	 * Handles two scenarios:
	 * 1. Single-plugin update (Plugins list / AWDev Updater button):
	 *    hook_extra['plugin'] is set → match by basename directly.
	 * 2. Bulk/auto update (update-core.php, WP cron):
	 *    hook_extra['plugin'] is NOT set → match by source folder basename:
	 *    the folder must start with the plugin slug OR the GitHub repo name
	 *    (derived from download_url). Random WP suffixes like "-NKvWbz" are
	 *    tolerated because we use stripos, not an exact match.
	 *
	 * The unreliable parent-path check has been removed: $remote_source can
	 * be passed with or without trailing slash by WP core, making the
	 * dirname() comparison fragile across WP versions.
	 *
	 * Uses WP_Filesystem to avoid direct rename() call.
	 */
	public function fix_folder_name( string $source, string $remote_source, object $upgrader, array $hook_extra ): string {
		// Already correct — nothing to do.
		$corrected = trailingslashit( $remote_source ) . $this->plugin_slug . '/';
		if ( $source === $corrected ) {
			return $source;
		}

		// Scenario 1: single-plugin update — hook_extra['plugin'] is populated.
		$extra_plugin = $hook_extra['plugin'] ?? '';
		if ( $extra_plugin !== '' ) {
			if ( $extra_plugin !== $this->plugin_basename ) {
				return $source;
			}
			return $this->rename_source( $source, $remote_source );
		}

		// Scenario 2: bulk/auto update — hook_extra['plugin'] is empty.
		// Match solely by the extracted folder's basename.
		$source_dirname = basename( untrailingslashit( $source ) );

		// Derive GitHub repo name from download_url, e.g.:
		// https://github.com/Owner/my-repo/archive/refs/heads/main.zip → my-repo
		$repo_name    = '';
		$data         = $this->get_remote_data();
		$download_url = $data->download_url ?? '';
		if ( $download_url && preg_match( '#github\.com/[^/]+/([^/]+)/archive/#', $download_url, $m ) ) {
			$repo_name = strtolower( $m[1] );
		}

		$slug_match = ( stripos( $source_dirname, $this->plugin_slug ) !== false );
		$repo_match = ( $repo_name !== '' && stripos( $source_dirname, $repo_name ) !== false );

		if ( ! $slug_match && ! $repo_match ) {
			return $source;
		}

		return $this->rename_source( $source, $remote_source );
	}

	/**
	 * Perform the actual WP_Filesystem rename to the correct plugin slug folder.
	 */
	private function rename_source( string $source, string $remote_source ): string {
		$corrected = trailingslashit( $remote_source ) . $this->plugin_slug . '/';

		if ( $source === $corrected ) {
			return $source;
		}

		global $wp_filesystem;
		if ( ! $wp_filesystem ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
			WP_Filesystem();
		}

		if ( $wp_filesystem && $wp_filesystem->move( $source, $corrected ) ) {
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

	public function get_api_url(): string   { return $this->api_url; }
	public function get_slug(): string      { return $this->plugin_slug; }
	public function get_basename(): string  { return $this->plugin_basename; }
}
