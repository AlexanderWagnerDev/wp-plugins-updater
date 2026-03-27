<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'AWDEV_SETTINGS_SLUG', 'awdev-plugins-updater' );

/**
 * Single source of truth for all built-in AWDev plugins.
 * Add new plugins here only — every other place reads from this function.
 *
 * @return array<string, array{name: string, api_slug: string}>
 */
function awdev_built_in_plugins(): array {
	return [
		'awdev-plugins-updater/awdev-plugins-updater.php'  => [
			'name'     => 'AWDev Plugins Updater',
			'api_slug' => 'awdev-plugins-updater',
		],
		'darkadmin-dark-mode-for-adminpanel/darkadmin.php' => [
			'name'     => 'DarkAdmin - Dark Mode for Adminpanel',
			'api_slug' => 'darkadmin-dark-mode-for-adminpanel',
		],
	];
}

/**
 * Shared low-level helper: fetch and cache raw API data for a single plugin.
 *
 * Both AWDev_Updater::get_remote_data() and awdev_fetch_remote_version() are
 * built on top of this function so that HTTP/caching behaviour only needs to
 * be maintained in one place.
 *
 * @param string $transient_key  Transient key, e.g. 'awdev_upd_my-plugin'.
 * @param string $api_url        Full API endpoint URL.
 * @return object|null           Decoded JSON object, or null on failure.
 */
function awdev_fetch_api_data( string $transient_key, string $api_url ): ?object {
	$cached = get_transient( $transient_key );

	if ( $cached !== false ) {
		return $cached ?: null;
	}

	$cache_hours = (int) get_option( 'awdev_cache_hours', 6 );
	if ( $cache_hours < 1 ) {
		$cache_hours = 1;
	}

	$response = wp_remote_get( $api_url, [
		'timeout'    => 10,
		'user-agent' => 'AWDev-Plugin-Updater/' . AWDEV_UPDATER_VERSION . '; ' . get_bloginfo( 'url' ),
	] );

	if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
		set_transient( $transient_key, false, $cache_hours * HOUR_IN_SECONDS );
		return null;
	}

	$data = json_decode( wp_remote_retrieve_body( $response ) );

	// Treat invalid JSON or a null body as a failed response.
	if ( json_last_error() !== JSON_ERROR_NONE || $data === null ) {
		set_transient( $transient_key, false, $cache_hours * HOUR_IN_SECONDS );
		return null;
	}

	set_transient( $transient_key, $data, $cache_hours * HOUR_IN_SECONDS );

	return $data;
}

/**
 * Lightweight helper to fetch a single plugin's remote version string.
 *
 * Uses awdev_fetch_api_data() — no WP update filters are registered.
 * Safe to call in AJAX or admin contexts.
 *
 * @param string $basename   Plugin basename (folder/file.php).
 * @param string $api_url    Full API endpoint URL.
 * @return string            Version string, or '?' on failure.
 */
function awdev_fetch_remote_version( string $basename, string $api_url ): string {
	$key  = 'awdev_upd_' . sanitize_key( dirname( $basename ) );
	$data = awdev_fetch_api_data( $key, $api_url );

	return ( $data && isset( $data->version ) ) ? $data->version : '?';
}

/**
 * Set default option values on first activation.
 * Runs once via register_activation_hook() in the main plugin file.
 */
function awdev_activate(): void {
	awdev_sync_auto_update_defaults();

	if ( get_option( 'awdev_cache_hours' ) === false ) {
		update_option( 'awdev_cache_hours', 6 );
	}

	if ( get_option( 'awdev_auto_updates_global' ) === false ) {
		update_option( 'awdev_auto_updates_global', true );
	}
}

/**
 * Ensure every built-in plugin has an entry in awdev_auto_updates.
 *
 * Called both on activation and on every admin init so that newly added
 * built-in plugins are picked up for existing installs without requiring
 * a manual deactivate/activate cycle.
 * Existing entries are never overwritten.
 */
function awdev_sync_auto_update_defaults(): void {
	$auto_updates = (array) get_option( 'awdev_auto_updates', [] );
	$changed      = false;

	foreach ( array_keys( awdev_built_in_plugins() ) as $basename ) {
		if ( ! array_key_exists( $basename, $auto_updates ) ) {
			$auto_updates[ $basename ] = true;
			$changed                   = true;
		}
	}

	if ( $changed ) {
		update_option( 'awdev_auto_updates', $auto_updates );
	}
}

// Sync missing built-in entries on every admin page load (cheap: only writes when something is missing).
add_action( 'admin_init', 'awdev_sync_auto_update_defaults' );

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

/**
 * Handle main settings form save.
 */
add_action( 'admin_post_awdev_save_settings', function () {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Not allowed.', 'awdev-plugins-updater' ) );
	}
	check_admin_referer( 'awdev_save_settings' );

	$global = isset( $_POST['awdev_auto_updates_global'] );
	update_option( 'awdev_auto_updates_global', $global );

	$cache_hours = isset( $_POST['awdev_cache_hours'] ) ? absint( wp_unslash( $_POST['awdev_cache_hours'] ) ) : 6;
	if ( $cache_hours < 1 )   { $cache_hours = 1; }
	if ( $cache_hours > 168 ) { $cache_hours = 168; }
	update_option( 'awdev_cache_hours', $cache_hours );

	wp_safe_redirect( add_query_arg(
		[ 'page' => AWDEV_SETTINGS_SLUG, 'settings-updated' => '1' ],
		admin_url( 'options-general.php' )
	) );
	exit;
} );

/**
 * AJAX: instant-save a single per-plugin auto-update toggle.
 */
add_action( 'wp_ajax_awdev_toggle_auto_update', function () {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( 'not_allowed', 403 );
	}
	check_ajax_referer( 'awdev_toggle_auto_update' );

	$basename = sanitize_text_field( wp_unslash( $_POST['basename'] ?? '' ) );
	$enabled  = filter_var( wp_unslash( $_POST['enabled'] ?? false ), FILTER_VALIDATE_BOOLEAN );

	if ( ! $basename ) {
		wp_send_json_error( 'missing_basename', 400 );
	}

	$auto_updates              = (array) get_option( 'awdev_auto_updates', [] );
	$auto_updates[ $basename ] = $enabled;
	update_option( 'awdev_auto_updates', $auto_updates );

	wp_send_json_success( [ 'basename' => $basename, 'enabled' => $enabled ] );
} );

/**
 * AJAX: instant-save the global auto-update toggle and mirror to all per-plugin entries.
 */
add_action( 'wp_ajax_awdev_toggle_global_auto_update', function () {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( 'not_allowed', 403 );
	}
	check_ajax_referer( 'awdev_toggle_auto_update' );

	$enabled = filter_var( wp_unslash( $_POST['enabled'] ?? false ), FILTER_VALIDATE_BOOLEAN );

	update_option( 'awdev_auto_updates_global', $enabled );

	$auto_updates = (array) get_option( 'awdev_auto_updates', [] );
	foreach ( $auto_updates as $basename => $_ ) {
		$auto_updates[ $basename ] = $enabled;
	}
	update_option( 'awdev_auto_updates', $auto_updates );

	wp_send_json_success( [ 'enabled' => $enabled ] );
} );

/**
 * AJAX: fetch remote versions for all managed plugins in one request.
 * Returns a map of dirname_slug => version string (or '?' on failure).
 * Uses awdev_fetch_remote_version() — no WP update filters are registered.
 */
add_action( 'wp_ajax_awdev_get_remote_versions', function () {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( 'not_allowed', 403 );
	}
	check_ajax_referer( 'awdev_get_remote_versions' );

	$built_in = awdev_built_in_plugins();
	$managed  = (array) get_option( 'awdev_managed_plugins', [] );
	$versions = [];

	foreach ( array_merge( array_keys( $built_in ), array_keys( $managed ) ) as $basename ) {
		$dirname_slug = sanitize_key( dirname( $basename ) );
		$api_slug     = $managed[ $basename ] ?? ( $built_in[ $basename ]['api_slug'] ?? $dirname_slug );
		$api_url      = AWDEV_UPDATE_SERVER . '/' . sanitize_key( $api_slug ) . '.php';

		$versions[ $dirname_slug ] = awdev_fetch_remote_version( $basename, $api_url );
	}

	wp_send_json_success( $versions );
} );

/**
 * AJAX: clear the transient cache for a single plugin and return JSON.
 */
add_action( 'wp_ajax_awdev_check_plugin', function () {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( 'not_allowed', 403 );
	}
	check_ajax_referer( 'awdev_check_plugin' );

	$dirname_slug = sanitize_key( wp_unslash( $_POST['dirname_slug'] ?? '' ) );
	if ( ! $dirname_slug ) {
		wp_send_json_error( 'missing_slug', 400 );
	}

	delete_transient( 'awdev_upd_' . $dirname_slug );
	delete_site_transient( 'update_plugins' );

	wp_send_json_success( [ 'cleared' => $dirname_slug ] );
} );

/**
 * Auto-update hook: respect per-plugin toggle; global toggle overrides all when off.
 *
 * For AWDev-managed plugins not yet in the awdev_auto_updates option,
 * default to true so WP does not skip them due to a null/false return value.
 */
add_filter( 'auto_update_plugin', function ( $update, $item ) {
	$plugin_basename = $item->plugin ?? '';
	if ( ! $plugin_basename ) {
		return $update;
	}

	$managed  = (array) get_option( 'awdev_managed_plugins', [] );
	$built_in = awdev_built_in_plugins();

	$is_awdev = isset( $built_in[ $plugin_basename ] ) || isset( $managed[ $plugin_basename ] );
	if ( ! $is_awdev ) {
		return $update;
	}

	// Global toggle off - never auto-update any AWDev plugin.
	$global = (bool) get_option( 'awdev_auto_updates_global', true );
	if ( ! $global ) {
		return false;
	}

	// Per-plugin toggle: if explicitly set, honour it; otherwise default to true.
	$auto_updates = (array) get_option( 'awdev_auto_updates', [] );
	if ( isset( $auto_updates[ $plugin_basename ] ) ) {
		return (bool) $auto_updates[ $plugin_basename ];
	}

	return true;
}, 10, 2 );

/**
 * Enqueue settings page assets.
 * Passes all data needed by settings.js via wp_localize_script().
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
	// No jQuery dependency — settings.js uses only vanilla JS.
	wp_enqueue_script(
		'awdev-settings-js',
		AWDEV_UPDATER_URL . 'assets/js/settings.js',
		[],
		AWDEV_UPDATER_VERSION,
		true
	);

	// Build per-plugin update nonces for the one-click Update button.
	$built_in      = awdev_built_in_plugins();
	$managed       = (array) get_option( 'awdev_managed_plugins', [] );
	$update_nonces = [];

	foreach ( array_merge( array_keys( $built_in ), array_keys( $managed ) ) as $basename ) {
		$update_nonces[ $basename ] = wp_create_nonce( 'upgrade-plugin_' . $basename );
	}

	wp_localize_script( 'awdev-settings-js', 'awdevSettings', [
		'ajaxUrl'            => admin_url( 'admin-ajax.php' ),
		'nonce'              => wp_create_nonce( 'awdev_toggle_auto_update' ),
		'nonceRemoteVersion' => wp_create_nonce( 'awdev_get_remote_versions' ),
		'nonceCheckPlugin'   => wp_create_nonce( 'awdev_check_plugin' ),
		'updateBase'         => admin_url( 'update.php?action=upgrade-plugin' ),
		'updateNonces'       => $update_nonces,
		'i18n'               => [
			'update' => __( 'Update', 'awdev-plugins-updater' ),
		],
	] );
} );

/**
 * Handle manual cache flush.
 */
add_action( 'admin_post_awdev_flush_cache', function () {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Not allowed.', 'awdev-plugins-updater' ) );
	}
	check_admin_referer( 'awdev_flush_cache' );

	global $wpdb;
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
	delete_site_transient( 'update_plugins' );

	wp_safe_redirect( add_query_arg( [ 'page' => AWDEV_SETTINGS_SLUG, 'cache-flushed' => '1' ], admin_url( 'options-general.php' ) ) );
	exit;
} );

/**
 * Resolve local installed version for a plugin basename.
 *
 * Calls get_plugins() at most once per request by caching the full plugin list
 * in the WP object cache (group 'awdev_updater', key 'all_plugins').
 * get_plugins() scans the filesystem on every call when there is no persistent
 * object cache, so avoiding repeated calls keeps the settings page fast.
 *
 * Returns '?' when the plugin is not found.
 */
function awdev_get_local_version( string $basename ): string {
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	$plugins = wp_cache_get( 'all_plugins', 'awdev_updater' );
	if ( ! is_array( $plugins ) ) {
		$plugins = get_plugins();
		wp_cache_set( 'all_plugins', $plugins, 'awdev_updater' );
	}

	if ( isset( $plugins[ $basename ] ) ) {
		return $plugins[ $basename ]['Version'];
	}

	$folder     = dirname( $basename );
	$folder_key = sanitize_key( $folder );

	foreach ( $plugins as $key => $data ) {
		$key_folder = dirname( $key );
		if ( $key_folder === $folder ) {
			return $data['Version'];
		}
		if ( sanitize_key( $key_folder ) === $folder_key ) {
			return $data['Version'];
		}
	}

	return '?';
}

/**
 * Return human-readable "last checked" time from the transient timeout.
 */
function awdev_get_last_checked( string $dirname_slug ): string {
	$cache_hours = (int) get_option( 'awdev_cache_hours', 6 );
	if ( $cache_hours < 1 ) { $cache_hours = 1; }
	$key     = '_transient_timeout_awdev_upd_' . sanitize_key( $dirname_slug );
	$timeout = (int) get_option( $key, 0 );
	if ( ! $timeout ) {
		return __( 'Never', 'awdev-plugins-updater' );
	}
	$checked = $timeout - ( $cache_hours * HOUR_IN_SECONDS );
	$diff    = time() - $checked;
	if ( $diff < 60 ) {
		return __( 'Just now', 'awdev-plugins-updater' );
	}
	if ( $diff < HOUR_IN_SECONDS ) {
		$mins = (int) ( $diff / 60 );
		/* translators: %d = number of minutes */
		return sprintf( _n( '%d minute ago', '%d minutes ago', $mins, 'awdev-plugins-updater' ), $mins );
	}
	if ( $diff < DAY_IN_SECONDS ) {
		$hours = (int) ( $diff / HOUR_IN_SECONDS );
		/* translators: %d = number of hours */
		return sprintf( _n( '%d hour ago', '%d hours ago', $hours, 'awdev-plugins-updater' ), $hours );
	}
	$days = (int) ( $diff / DAY_IN_SECONDS );
	/* translators: %d = number of days */
	return sprintf( _n( '%d day ago', '%d days ago', $days, 'awdev-plugins-updater' ), $days );
}

/**
 * Render a single plugin row in the managed plugins table.
 *
 * @param string $basename    Plugin basename (folder/file.php).
 * @param string $name        Display name.
 * @param array  $st          Status array from awdev_render_settings_page().
 * @param string $badge_class CSS class for the type badge.
 */
function awdev_render_plugin_row( string $basename, string $name, array $st, string $badge_class ): void {
	?>
	<tr>
		<td><strong><?php echo esc_html( $name ); ?></strong></td>
		<td><?php echo esc_html( $st['local_version'] ); ?></td>
		<td>
			<span class="awdev-remote-version" data-slug="<?php echo esc_attr( $st['dirname_slug'] ); ?>">
				<span class="awdev-version-loading">...</span>
			</span>
			<span class="awdev-last-checked"><?php echo esc_html( $st['last_checked'] ); ?></span>
		</td>
		<td>
			<label class="awdev-toggle">
				<input type="checkbox"
					class="awdev-per-plugin-toggle"
					data-basename="<?php echo esc_attr( $basename ); ?>"
					value="1"
					<?php checked( $st['auto_update'] ); ?>
				/>
				<span class="awdev-toggle-slider"></span>
			</label>
		</td>
		<td class="awdev-actions-cell"
			data-basename="<?php echo esc_attr( $basename ); ?>"
			data-local="<?php echo esc_attr( $st['local_version'] ); ?>"
			data-slug="<?php echo esc_attr( $st['dirname_slug'] ); ?>">
			<button type="button"
				class="button button-small awdev-check-btn"
				title="<?php esc_attr_e( 'Re-check update', 'awdev-plugins-updater' ); ?>"
				data-slug="<?php echo esc_attr( $st['dirname_slug'] ); ?>">
				<span class="dashicons dashicons-update"></span>
			</button>
			<span class="awdev-update-btn-placeholder"></span>
		</td>
		<td><span class="awdev-badge <?php echo esc_attr( $badge_class ); ?>"><?php esc_html_e( 'AWDev', 'awdev-plugins-updater' ); ?></span></td>
	</tr>
	<?php
}

/**
 * Render the settings page.
 */
function awdev_render_settings_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$built_in     = awdev_built_in_plugins();
	$managed      = (array) get_option( 'awdev_managed_plugins', [] );
	$auto_updates = (array) get_option( 'awdev_auto_updates', [] );
	$global_auto  = get_option( 'awdev_auto_updates_global', true );
	$cache_hours  = (int) get_option( 'awdev_cache_hours', 6 );
	if ( $cache_hours < 1 ) { $cache_hours = 1; }

	// Build statuses: built-in first, then managed-only (skip duplicates).
	$statuses = [];

	foreach ( $built_in as $basename => $_ ) {
		$dirname_slug          = sanitize_key( dirname( $basename ) );
		$statuses[ $basename ] = [
			'dirname_slug'  => $dirname_slug,
			'local_version' => awdev_get_local_version( $basename ),
			'last_checked'  => awdev_get_last_checked( $dirname_slug ),
			'auto_update'   => $auto_updates[ $basename ] ?? true,
		];
	}

	foreach ( $managed as $basename => $_ ) {
		if ( isset( $built_in[ $basename ] ) ) {
			continue;
		}
		$dirname_slug          = sanitize_key( dirname( $basename ) );
		$statuses[ $basename ] = [
			'dirname_slug'  => $dirname_slug,
			'local_version' => awdev_get_local_version( $basename ),
			'last_checked'  => awdev_get_last_checked( $dirname_slug ),
			'auto_update'   => $auto_updates[ $basename ] ?? true,
		];
	}

	// Read-only GET flags - no nonce needed (no state change).
	$cache_flushed  = isset( $_GET['cache-flushed'] );    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$settings_saved = isset( $_GET['settings-updated'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	?>
	<div class="wrap awdev-settings-wrap">

		<div class="awdev-page-header">
			<div class="awdev-page-header-inner">
				<span class="awdev-header-icon dashicons dashicons-update-alt"></span>
				<div>
					<h1 class="awdev-page-title"><?php esc_html_e( 'AWDev Plugins Updater', 'awdev-plugins-updater' ); ?></h1>
					<p class="awdev-page-subtitle">
						<?php esc_html_e( 'Updater for AlexanderWagnerDev plugins', 'awdev-plugins-updater' ); ?>
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
		<div class="notice notice-success is-dismissible"><p>
			<span aria-hidden="true">&#10003;</span> <?php esc_html_e( 'Update cache flushed.', 'awdev-plugins-updater' ); ?>
		</p></div>
		<?php endif; ?>

		<?php if ( $settings_saved ) : ?>
		<div class="notice notice-success is-dismissible"><p>
			<span aria-hidden="true">&#10003;</span> <?php esc_html_e( 'Settings saved.', 'awdev-plugins-updater' ); ?>
		</p></div>
		<?php endif; ?>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="awdev_save_settings" />
			<?php wp_nonce_field( 'awdev_save_settings' ); ?>

			<div class="awdev-card">
				<div class="awdev-card-header">
					<span class="dashicons dashicons-clock"></span>
					<h2><?php esc_html_e( 'Auto-Update Settings', 'awdev-plugins-updater' ); ?></h2>
				</div>
				<div class="awdev-card-body">
					<p class="awdev-card-description">
						<?php esc_html_e( 'Configure how often the updater checks for new plugin versions.', 'awdev-plugins-updater' ); ?>
					</p>

					<div class="awdev-global-toggle-row">
						<label class="awdev-toggle">
							<input type="checkbox"
								id="awdev-global-auto-update"
								name="awdev_auto_updates_global"
								value="1"
								<?php checked( (bool) $global_auto ); ?>
							/>
							<span class="awdev-toggle-slider"></span>
						</label>
						<span class="awdev-global-toggle-label">
							<?php esc_html_e( 'Auto-Update (all plugins)', 'awdev-plugins-updater' ); ?>
						</span>
					</div>

					<div class="awdev-settings-row">
						<label for="awdev_cache_hours" class="awdev-settings-label">
							<?php esc_html_e( 'Check interval (hours)', 'awdev-plugins-updater' ); ?>
						</label>
						<input
							type="number"
							id="awdev_cache_hours"
							name="awdev_cache_hours"
							value="<?php echo esc_attr( $cache_hours ); ?>"
							min="1"
							max="168"
							step="1"
							class="small-text"
					/>
						<span class="description"><?php esc_html_e( 'Min: 1h - Max: 168h (7 days). Default: 6h.', 'awdev-plugins-updater' ); ?></span>
					</div>

					<div class="awdev-submit-row">
						<button type="submit" class="button button-primary"><?php esc_html_e( 'Save Settings', 'awdev-plugins-updater' ); ?></button>
					</div>
				</div>
			</div>
		</form>

		<div class="awdev-card">
			<div class="awdev-card-header">
				<span class="dashicons dashicons-plugins-checked"></span>
				<h2><?php esc_html_e( 'Managed Plugins', 'awdev-plugins-updater' ); ?></h2>
			</div>
			<div class="awdev-card-body">
				<p class="awdev-card-description">
					<?php esc_html_e( 'All AlexanderWagnerDev plugins managed by this updater.', 'awdev-plugins-updater' ); ?>
				</p>

				<table class="awdev-plugin-table">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Plugin', 'awdev-plugins-updater' ); ?></th>
							<th><?php esc_html_e( 'Installed', 'awdev-plugins-updater' ); ?></th>
							<th><?php esc_html_e( 'Remote', 'awdev-plugins-updater' ); ?></th>
							<th><?php esc_html_e( 'Auto-Update', 'awdev-plugins-updater' ); ?></th>
							<th><?php esc_html_e( 'Actions', 'awdev-plugins-updater' ); ?></th>
							<th><?php esc_html_e( 'Type', 'awdev-plugins-updater' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $built_in as $basename => $info ) : ?>
							<?php awdev_render_plugin_row( $basename, $info['name'], $statuses[ $basename ], 'awdev-badge-builtin' ); ?>
						<?php endforeach; ?>

						<?php foreach ( $managed as $basename => $slug ) :
							if ( isset( $built_in[ $basename ] ) ) { continue; }
							$plugin_name = sanitize_text_field( dirname( $basename ) );
						?>
							<?php awdev_render_plugin_row( $basename, $plugin_name, $statuses[ $basename ], 'awdev-badge-custom' ); ?>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>

		<div class="awdev-card">
			<div class="awdev-card-header">
				<span class="dashicons dashicons-performance"></span>
				<h2><?php esc_html_e( 'Cache Management', 'awdev-plugins-updater' ); ?></h2>
			</div>
			<div class="awdev-card-body">
				<p class="awdev-card-description">
					<?php
					printf(
						/* translators: %d = configured cache duration in hours */
						esc_html__( 'Update data is cached for %d hour(s) per plugin. Flush the cache to force an immediate re-check.', 'awdev-plugins-updater' ),
						(int) get_option( 'awdev_cache_hours', 6 )
					);
					?>
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
