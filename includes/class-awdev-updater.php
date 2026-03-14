<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles self-hosted update checks for a single plugin.
 * Registers WordPress hooks to inject update data from a custom API endpoint.
 */
class AWDev_Updater {

	private string $plugin_basename;
	private string $plugin_slug;
	private string $api_url;

	/**
	 * @param string $plugin_basename Plugin file relative to plugins dir (e.g. 'my-plugin/my-plugin.php').
	 * @param string $api_url         URL of the self-hosted JSON/PHP endpoint returning update data.
	 */
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
	 * Results are cached as a WordPress transient for 6 hours.
	 */
	private function get_remote_data(): ?object {
		$transient_key = 'awdev_updater_' . sanitize_key( $this->plugin_slug );
		$cached        = get_transient( $transient_key );

		if ( $cached !== false ) {
			return $cached ?: null;
		}

		$response = wp_remote_get( $this->api_url, [
			'timeout'    => 10,
			'user-agent' => 'AWDev-Plugin-Updater/1.0; ' . get_bloginfo( 'url' ),
		] );

		if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
			set_transient( $transient_key, false, HOUR_IN_SECONDS );
			return null;
		}

		$data = json_decode( wp_remote_retrieve_body( $response ) );
		set_transient( $transient_key, $data, 6 * HOUR_IN_SECONDS );

		return $data;
	}

	/**
	 * Inject update data into the WordPress plugin update transient.
	 */
	public function check_update( object $transient ): object {
		if ( empty( $transient->checked ) ) {
			return $transient;
		}

		$data              = $this->get_remote_data();
		$installed_version = $transient->checked[ $this->plugin_basename ] ?? '0.0.0';

		if ( $data && version_compare( $data->version, $installed_version, '>' ) ) {
			$transient->response[ $this->plugin_basename ] = (object) [
				'slug'        => $this->plugin_slug,
				'plugin'      => $this->plugin_basename,
				'new_version' => $data->version,
				'url'         => $data->details_url ?? $this->api_url,
				'package'     => $data->download_url,
			];
		}

		return $transient;
	}

	/**
	 * Provide plugin details for the "View version details" popup in WP admin.
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
			'name'          => $data->slug ?? $this->plugin_slug,
			'slug'          => $this->plugin_slug,
			'version'       => $data->version,
			'author'        => 'AlexanderWagnerDev',
			'homepage'      => $data->details_url ?? '',
			'sections'      => [ 'changelog' => $data->changelog ?? '' ],
			'download_link' => $data->download_url,
			'requires'      => $data->requires     ?? '6.0',
			'tested'        => $data->tested       ?? '6.9',
			'requires_php'  => $data->requires_php ?? '7.4',
		];
	}

	/**
	 * Fix extracted folder name after update installation.
	 * Prevents issues when a ZIP contains a hash-suffixed folder (e.g. from GitHub releases).
	 */
	public function fix_folder_name( string $source, string $remote_source, object $upgrader, array $hook_extra ): string {
		if ( ( $hook_extra['plugin'] ?? '' ) !== $this->plugin_basename ) {
			return $source;
		}

		$corrected = trailingslashit( $remote_source ) . $this->plugin_slug . '/';

		if ( $source !== $corrected ) {
			rename( $source, $corrected );
			return $corrected;
		}

		return $source;
	}
}
