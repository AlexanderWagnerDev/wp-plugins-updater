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
	 * Prevents mismatches when the ZIP root dir has a hash-suffixed name.
	 */
	public function fix_folder_name( string $source, string $remote_source, object $upgrader, array $hook_extra ): string {
		if ( ( $hook_extra['plugin'] ?? '' ) !== $this->plugin_basename ) {
			return $source;
		}

		$corrected = trailingslashit( $remote_source ) . $this->plugin_slug . '/';

		if ( $source !== $corrected && rename( $source, $corrected ) ) {
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
