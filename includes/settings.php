<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'AWDEV_SETTINGS_SLUG', 'awdev-plugins-updater' );

/**
 * Register settings and menu.
 */
add_action( 'admin_menu', function () {
	add_options_page(
		__( 'AWDev Plugins Updater', 'awdev-plugins-updater' ),
		__( 'AWDev Plugins Updater', 'awdev-plugins-updater' ),
		'manage_options',
		AWDEV_SETTINGS_SLUG,
		'awdev_render_settings_page'
	);
} );

add_action( 'admin_init', function () {
	register_setting( 'awdev_settings', 'awdev_managed_plugins', [
		'type'              => 'array',
		'sanitize_callback' => 'awdev_sanitize_managed_plugins',
		'default'           => [],
	] );
} );

function awdev_sanitize_managed_plugins( $input ): array {
	if ( ! is_array( $input ) ) {
		return [];
	}
	$clean = [];
	foreach ( $input as $basename => $slug ) {
		$b = sanitize_text_field( $basename );
		$s = sanitize_key( $slug );
		if ( $b && $s ) {
			$clean[ $b ] = $s;
		}
	}
	return $clean;
}

/**
 * Enqueue settings page assets.
 */
add_action( 'admin_enqueue_scripts', function ( $hook ) {
	if ( $hook !== 'settings_page_' . AWDEV_SETTINGS_SLUG ) {
		return;
	}
	wp_enqueue_style(
		'awdev-settings',
		AWDEV_UPDATER_URL . 'assets/css/settings.css',
		[],
		AWDEV_UPDATER_VERSION
	);
	wp_enqueue_script(
		'awdev-settings-js',
		AWDEV_UPDATER_URL . 'assets/js/settings.js',
		[],
		AWDEV_UPDATER_VERSION,
		true
	);
} );

/**
 * Handle manual cache flush via POST action.
 */
add_action( 'admin_post_awdev_flush_cache', function () {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Not allowed.', 'awdev-plugins-updater' ) );
	}
	check_admin_referer( 'awdev_flush_cache' );

	global $wpdb;
	$wpdb->query(
		$wpdb->prepare(
			"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
			'_transient_awdev_upd_%'
		)
	);
	$wpdb->query(
		$wpdb->prepare(
			"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
			'_transient_timeout_awdev_upd_%'
		)
	);

	wp_redirect( add_query_arg( [ 'page' => AWDEV_SETTINGS_SLUG, 'cache-flushed' => '1' ], admin_url( 'options-general.php' ) ) );
	exit;
} );

/**
 * Resolve local installed version for a plugin basename.
 */
function awdev_get_local_version( string $basename ): string {
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	$plugins = get_plugins();
	return $plugins[ $basename ]['Version'] ?? '–';
}

/**
 * Return human-readable "last checked" time from the transient timeout.
 * The timeout is set to now + 6h, so last_checked = timeout - 6h.
 * Uses the dirname-based slug (matching AWDev_Updater::$plugin_slug).
 */
function awdev_get_last_checked( string $dirname_slug ): string {
	$key     = '_transient_timeout_awdev_upd_' . sanitize_key( $dirname_slug );
	$timeout = (int) get_option( $key, 0 );
	if ( ! $timeout ) {
		return __( 'Never', 'awdev-plugins-updater' );
	}
	$checked = $timeout - ( 6 * HOUR_IN_SECONDS );
	$diff    = time() - $checked;
	if ( $diff < 60 ) {
		return __( 'Just now', 'awdev-plugins-updater' );
	}
	if ( $diff < HOUR_IN_SECONDS ) {
		$mins = (int) ( $diff / 60 );
		/* translators: %d = number of minutes */
		return sprintf( _n( '%d minute ago', '%d minutes ago', $mins, 'awdev-plugins-updater' ), $mins );
	}
	$hours = (int) ( $diff / HOUR_IN_SECONDS );
	/* translators: %d = number of hours */
	return sprintf( _n( '%d hour ago', '%d hours ago', $hours, 'awdev-plugins-updater' ), $hours );
}

/**
 * Fetch remote version directly from API if transient is not yet populated.
 * This ensures the settings page always shows the latest available version.
 */
function awdev_get_remote_version( string $api_url, string $dirname_slug ): string {
	$key  = 'awdev_upd_' . sanitize_key( $dirname_slug );
	$data = get_transient( $key );

	if ( $data === false ) {
		$response = wp_remote_get( $api_url, [
			'timeout'    => 10,
			'user-agent' => 'AWDev-Plugin-Updater/' . AWDEV_UPDATER_VERSION . '; ' . get_bloginfo( 'url' ),
		] );

		if ( ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) === 200 ) {
			$data = json_decode( wp_remote_retrieve_body( $response ) );
			set_transient( $key, $data, 6 * HOUR_IN_SECONDS );
		} else {
			set_transient( $key, false, HOUR_IN_SECONDS );
			return '–';
		}
	}

	return ( $data && isset( $data->version ) ) ? $data->version : '–';
}

/**
 * Render the settings page.
 */
function awdev_render_settings_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$managed = (array) get_option( 'awdev_managed_plugins', [] );

	$built_in = [
		'awdev-plugin-updater/awdev-plugin-updater.php' => [
			'name'     => 'AWDev Plugins Updater',
			'api_slug' => 'awdev-plugin-updater',
		],
		'wp-darkadmin-plugin/darkadmin.php' => [
			'name'     => 'DarkAdmin – Dark Mode for Adminpanel',
			'api_slug' => 'darkadmin',
		],
	];

	$statuses = [];
	foreach ( array_merge( array_keys( $built_in ), array_keys( $managed ) ) as $basename ) {
		// AWDev_Updater uses dirname(basename) as the transient slug key, not the api_slug.
		$dirname_slug = sanitize_key( dirname( $basename ) );
		$api_slug     = $managed[ $basename ] ?? ( $built_in[ $basename ]['api_slug'] ?? $dirname_slug );
		$api_url      = AWDEV_UPDATE_SERVER . '/' . sanitize_key( $api_slug ) . '.php';

		$statuses[ $basename ] = [
			'remote_version' => awdev_get_remote_version( $api_url, $dirname_slug ),
			'local_version'  => awdev_get_local_version( $basename ),
			'last_checked'   => awdev_get_last_checked( $dirname_slug ),
		];
	}

	$cache_flushed = isset( $_GET['cache-flushed'] );
	?>
	<div class="wrap awdev-settings-wrap">

		<div class="awdev-page-header">
			<div class="awdev-page-header-inner">
				<span class="awdev-header-icon dashicons dashicons-update-alt"></span>
				<div>
					<h1 class="awdev-page-title"><?php esc_html_e( 'AWDev Plugins Updater', 'awdev-plugins-updater' ); ?></h1>
					<p class="awdev-page-subtitle">
						<?php esc_html_e( 'Self-hosted update manager for AlexanderWagnerDev plugins', 'awdev-plugins-updater' ); ?>
						&mdash; v<?php echo esc_html( AWDEV_UPDATER_VERSION ); ?>
					</p>
				</div>
			</div>
			<div class="awdev-status-badge awdev-status-active">
				<span class="awdev-status-dot"></span>
				<?php esc_html_e( 'Active', 'awdev-plugins-updater' ); ?>
			</div>
		</div>

		<?php if ( $cache_flushed ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( '✓ Update cache flushed. WordPress will re-check all plugins on the next update cycle.', 'awdev-plugins-updater' ); ?></p></div>
		<?php endif; ?>

		<?php if ( isset( $_GET['settings-updated'] ) ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( '✓ Settings saved.', 'awdev-plugins-updater' ); ?></p></div>
		<?php endif; ?>

		<form method="post" action="options.php">
			<?php settings_fields( 'awdev_settings' ); ?>

			<!-- Managed Plugins Card -->
			<div class="awdev-card">
				<div class="awdev-card-header">
					<span class="dashicons dashicons-plugins-checked"></span>
					<h2><?php esc_html_e( 'Managed Plugins', 'awdev-plugins-updater' ); ?></h2>
				</div>
				<div class="awdev-card-body">
					<p class="awdev-card-description">
						<?php esc_html_e( 'All AlexanderWagnerDev plugins that receive updates from the self-hosted server.', 'awdev-plugins-updater' ); ?>
					</p>

					<table class="awdev-plugin-table">
						<thead>
							<tr>
								<th><?php esc_html_e( 'Plugin', 'awdev-plugins-updater' ); ?></th>
								<th><?php esc_html_e( 'Installed', 'awdev-plugins-updater' ); ?></th>
								<th><?php esc_html_e( 'Remote Version', 'awdev-plugins-updater' ); ?></th>
								<th><?php esc_html_e( 'Type', 'awdev-plugins-updater' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $built_in as $basename => $info ) : ?>
							<tr>
								<td><strong><?php echo esc_html( $info['name'] ); ?></strong></td>
								<td><?php echo esc_html( $statuses[ $basename ]['local_version'] ); ?></td>
								<td>
									<?php echo esc_html( $statuses[ $basename ]['remote_version'] ); ?><br>
									<span class="awdev-last-checked"><?php echo esc_html( $statuses[ $basename ]['last_checked'] ); ?></span>
								</td>
								<td><span class="awdev-badge awdev-badge-builtin"><?php esc_html_e( 'Built-in', 'awdev-plugins-updater' ); ?></span></td>
							</tr>
							<?php endforeach; ?>

							<?php foreach ( $managed as $basename => $slug ) : ?>
								<?php if ( isset( $built_in[ $basename ] ) ) continue; ?>
							<tr class="awdev-dynamic-row">
								<td><input type="text" name="awdev_managed_plugins[<?php echo esc_attr( $basename ); ?>][basename]" value="<?php echo esc_attr( $basename ); ?>" class="awdev-input-basename" placeholder="folder/plugin.php" /></td>
								<td><?php echo esc_html( $statuses[ $basename ]['local_version'] ); ?></td>
								<td>
									<?php echo esc_html( $statuses[ $basename ]['remote_version'] ); ?><br>
									<span class="awdev-last-checked"><?php echo esc_html( $statuses[ $basename ]['last_checked'] ); ?></span>
								</td>
								<td>
									<span class="awdev-badge awdev-badge-custom"><?php esc_html_e( 'Custom', 'awdev-plugins-updater' ); ?></span>
									<button type="button" class="awdev-remove-row button-link" title="<?php esc_attr_e( 'Remove', 'awdev-plugins-updater' ); ?>"><span class="dashicons dashicons-trash"></span></button>
								</td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>

					<div class="awdev-add-row">
						<button type="button" id="awdev-add-plugin" class="button">
							<span class="dashicons dashicons-plus-alt2"></span>
							<?php esc_html_e( 'Add Plugin', 'awdev-plugins-updater' ); ?>
						</button>
					</div>
				</div>
			</div>

			<div class="awdev-submit-row">
				<?php submit_button( __( 'Save Settings', 'awdev-plugins-updater' ), 'primary', 'submit', false ); ?>
			</div>
		</form>

		<!-- Cache Management Card -->
		<div class="awdev-card">
			<div class="awdev-card-header">
				<span class="dashicons dashicons-performance"></span>
				<h2><?php esc_html_e( 'Cache Management', 'awdev-plugins-updater' ); ?></h2>
			</div>
			<div class="awdev-card-body">
				<p class="awdev-card-description">
					<?php esc_html_e( 'Update data is cached for 6 hours per plugin. Flush the cache to force WordPress to re-check all endpoints immediately.', 'awdev-plugins-updater' ); ?>
				</p>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="awdev_flush_cache" />
					<?php wp_nonce_field( 'awdev_flush_cache' ); ?>
					<button type="submit" class="button button-secondary">
						<span class="dashicons dashicons-image-rotate"></span>
						<?php esc_html_e( 'Flush Update Cache', 'awdev-plugins-updater' ); ?>
					</button>
				</form>
			</div>
		</div>

		<div class="awdev-footer">
			<p>
				<?php esc_html_e( 'AWDev Plugins Updater', 'awdev-plugins-updater' ); ?> &ndash;
				<a href="https://alexanderwagnerdev.com" target="_blank" rel="noopener">AlexanderWagnerDev</a>
				&mdash;
				<a href="https://github.com/AlexanderWagnerDev/wp-plugins-updater" target="_blank" rel="noopener">GitHub</a>
			</p>
		</div>

	</div>
	<?php
}
