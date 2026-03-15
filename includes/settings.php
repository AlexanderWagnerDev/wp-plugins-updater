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
	register_setting( 'awdev_settings', 'awdev_auto_updates', [
		'type'              => 'array',
		'sanitize_callback' => 'awdev_sanitize_auto_updates',
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

function awdev_sanitize_auto_updates( $input ): array {
	if ( ! is_array( $input ) ) {
		return [];
	}
	$clean = [];
	foreach ( $input as $basename => $val ) {
		$b = sanitize_text_field( $basename );
		if ( $b ) {
			$clean[ $b ] = (bool) $val;
		}
	}
	return $clean;
}

/**
 * Auto-update hook: allow automatic updates for plugins that have it enabled.
 */
add_filter( 'auto_update_plugin', function ( $update, $item ) {
	$auto_updates = (array) get_option( 'awdev_auto_updates', [] );
	if ( isset( $item->plugin ) && isset( $auto_updates[ $item->plugin ] ) ) {
		return (bool) $auto_updates[ $item->plugin ];
	}
	return $update;
}, 10, 2 );

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

	delete_site_transient( 'update_plugins' );

	wp_redirect( add_query_arg( [ 'page' => AWDEV_SETTINGS_SLUG, 'cache-flushed' => '1' ], admin_url( 'options-general.php' ) ) );
	exit;
} );

/**
 * Handle manual single-plugin update check via POST action.
 */
add_action( 'admin_post_awdev_check_plugin', function () {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Not allowed.', 'awdev-plugins-updater' ) );
	}
	check_admin_referer( 'awdev_check_plugin' );

	$dirname_slug = sanitize_key( wp_unslash( $_POST['dirname_slug'] ?? '' ) );
	if ( $dirname_slug ) {
		delete_transient( 'awdev_upd_' . $dirname_slug );
		delete_site_transient( 'update_plugins' );
	}

	wp_redirect( add_query_arg( [ 'page' => AWDEV_SETTINGS_SLUG, 'plugin-checked' => '1' ], admin_url( 'options-general.php' ) ) );
	exit;
} );

/**
 * Resolve local installed version for a plugin basename.
 *
 * Strategy:
 * 1. Direct match on exact basename.
 * 2. Match by raw dirname (e.g. "my-plugin" === dirname of key).
 * 3. Match by sanitize_key(dirname) — handles hyphens/underscores/case differences.
 */
function awdev_get_local_version( string $basename ): string {
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	$plugins = get_plugins();

	// 1. Direct match.
	if ( isset( $plugins[ $basename ] ) ) {
		return $plugins[ $basename ]['Version'];
	}

	$folder     = dirname( $basename );
	$folder_key = sanitize_key( $folder );

	foreach ( $plugins as $key => $data ) {
		$key_folder = dirname( $key );

		// 2. Raw folder name match.
		if ( $key_folder === $folder ) {
			return $data['Version'];
		}

		// 3. sanitize_key folder match (covers hyphens vs underscores, uppercase).
		if ( sanitize_key( $key_folder ) === $folder_key ) {
			return $data['Version'];
		}
	}

	return '–';
}

/**
 * Return human-readable "last checked" time from the transient timeout.
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
		return sprintf( _n( '%d minute ago', '%d minutes ago', $mins, 'awdev-plugins-updater' ), $mins );
	}
	$hours = (int) ( $diff / HOUR_IN_SECONDS );
	return sprintf( _n( '%d hour ago', '%d hours ago', $hours, 'awdev-plugins-updater' ), $hours );
}

/**
 * Fetch remote version from API, using transient cache.
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

	$managed      = (array) get_option( 'awdev_managed_plugins', [] );
	$auto_updates = (array) get_option( 'awdev_auto_updates', [] );

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

	// Default auto-update to TRUE for built-in plugins on first load (option not yet set).
	$auto_updates_saved = get_option( 'awdev_auto_updates' );
	if ( $auto_updates_saved === false ) {
		$defaults = [];
		foreach ( array_keys( $built_in ) as $basename ) {
			$defaults[ $basename ] = true;
		}
		update_option( 'awdev_auto_updates', $defaults );
		$auto_updates = $defaults;
	}

	$statuses     = [];
	$pending_updates = []; // basenames that have a pending update available

	foreach ( array_merge( array_keys( $built_in ), array_keys( $managed ) ) as $basename ) {
		$dirname_slug = sanitize_key( dirname( $basename ) );
		$api_slug     = $managed[ $basename ] ?? ( $built_in[ $basename ]['api_slug'] ?? $dirname_slug );
		$api_url      = AWDEV_UPDATE_SERVER . '/' . sanitize_key( $api_slug ) . '.php';

		$local  = awdev_get_local_version( $basename );
		$remote = awdev_get_remote_version( $api_url, $dirname_slug );
		$needs_update = ( $local !== '–' && $remote !== '–' && version_compare( $remote, $local, '>' ) );

		$statuses[ $basename ] = [
			'dirname_slug'   => $dirname_slug,
			'remote_version' => $remote,
			'local_version'  => $local,
			'last_checked'   => awdev_get_last_checked( $dirname_slug ),
			'auto_update'    => $auto_updates[ $basename ] ?? true,
			'needs_update'   => $needs_update,
		];

		if ( $needs_update ) {
			$pending_updates[] = $basename;
		}
	}

	$cache_flushed  = isset( $_GET['cache-flushed'] );
	$plugin_checked = isset( $_GET['plugin-checked'] );
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
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( '✓ Update cache flushed.', 'awdev-plugins-updater' ); ?></p></div>
		<?php endif; ?>

		<?php if ( $plugin_checked ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( '✓ Plugin cache cleared. WordPress will re-check on next load.', 'awdev-plugins-updater' ); ?></p></div>
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
								<th><?php esc_html_e( 'Remote', 'awdev-plugins-updater' ); ?></th>
								<th><?php esc_html_e( 'Auto-Update', 'awdev-plugins-updater' ); ?></th>
								<th><?php esc_html_e( 'Actions', 'awdev-plugins-updater' ); ?></th>
								<th><?php esc_html_e( 'Type', 'awdev-plugins-updater' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $built_in as $basename => $info ) :
								$st = $statuses[ $basename ];
							?>
							<tr>
								<td><strong><?php echo esc_html( $info['name'] ); ?></strong></td>
								<td><?php echo esc_html( $st['local_version'] ); ?></td>
								<td>
									<?php if ( $st['needs_update'] ) : ?>
										<span class="awdev-version-new"><?php echo esc_html( $st['remote_version'] ); ?></span>
									<?php else : ?>
										<?php echo esc_html( $st['remote_version'] ); ?>
									<?php endif; ?>
									<span class="awdev-last-checked"><?php echo esc_html( $st['last_checked'] ); ?></span>
								</td>
								<td>
									<label class="awdev-toggle">
										<input type="checkbox"
											name="awdev_auto_updates[<?php echo esc_attr( $basename ); ?>]"
											value="1"
											<?php checked( $st['auto_update'] ); ?>
										/>
										<span class="awdev-toggle-slider"></span>
									</label>
								</td>
								<td>
									<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:inline">
										<input type="hidden" name="action" value="awdev_check_plugin" />
										<input type="hidden" name="dirname_slug" value="<?php echo esc_attr( $st['dirname_slug'] ); ?>" />
										<?php wp_nonce_field( 'awdev_check_plugin' ); ?>
										<button type="submit" class="button button-small awdev-check-btn" title="<?php esc_attr_e( 'Re-check update', 'awdev-plugins-updater' ); ?>">
											<span class="dashicons dashicons-update"></span>
										</button>
									</form>
									<?php if ( $st['needs_update'] ) : ?>
										<a href="<?php echo esc_url( admin_url( 'update.php?action=upgrade-plugin&plugin=' . urlencode( $basename ) . '&_wpnonce=' . wp_create_nonce( 'upgrade-plugin_' . $basename ) ) ); ?>" class="button button-small button-primary awdev-update-btn">
											<span class="dashicons dashicons-arrow-up-alt"></span>
											<?php esc_html_e( 'Update', 'awdev-plugins-updater' ); ?>
										</a>
									<?php endif; ?>
								</td>
								<td><span class="awdev-badge awdev-badge-builtin"><?php esc_html_e( 'Built-in', 'awdev-plugins-updater' ); ?></span></td>
							</tr>
							<?php endforeach; ?>

							<?php foreach ( $managed as $basename => $slug ) :
								if ( isset( $built_in[ $basename ] ) ) continue;
								$st = $statuses[ $basename ];
							?>
							<tr class="awdev-dynamic-row">
								<td><input type="text" name="awdev_managed_plugins[<?php echo esc_attr( $basename ); ?>]" value="<?php echo esc_attr( $slug ); ?>" class="awdev-input-basename" placeholder="api-slug" /></td>
								<td><?php echo esc_html( $st['local_version'] ); ?></td>
								<td>
									<?php if ( $st['needs_update'] ) : ?>
										<span class="awdev-version-new"><?php echo esc_html( $st['remote_version'] ); ?></span>
									<?php else : ?>
										<?php echo esc_html( $st['remote_version'] ); ?>
									<?php endif; ?>
									<span class="awdev-last-checked"><?php echo esc_html( $st['last_checked'] ); ?></span>
								</td>
								<td>
									<label class="awdev-toggle">
										<input type="checkbox"
											name="awdev_auto_updates[<?php echo esc_attr( $basename ); ?>]"
											value="1"
											<?php checked( $st['auto_update'] ); ?>
										/>
										<span class="awdev-toggle-slider"></span>
									</label>
								</td>
								<td>
									<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:inline">
										<input type="hidden" name="action" value="awdev_check_plugin" />
										<input type="hidden" name="dirname_slug" value="<?php echo esc_attr( $st['dirname_slug'] ); ?>" />
										<?php wp_nonce_field( 'awdev_check_plugin' ); ?>
										<button type="submit" class="button button-small awdev-check-btn" title="<?php esc_attr_e( 'Re-check update', 'awdev-plugins-updater' ); ?>">
											<span class="dashicons dashicons-update"></span>
										</button>
									</form>
									<?php if ( $st['needs_update'] ) : ?>
										<a href="<?php echo esc_url( admin_url( 'update.php?action=upgrade-plugin&plugin=' . urlencode( $basename ) . '&_wpnonce=' . wp_create_nonce( 'upgrade-plugin_' . $basename ) ) ); ?>" class="button button-small button-primary awdev-update-btn">
											<span class="dashicons dashicons-arrow-up-alt"></span>
											<?php esc_html_e( 'Update', 'awdev-plugins-updater' ); ?>
										</a>
									<?php endif; ?>
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
				<?php if ( ! empty( $pending_updates ) ) :
					// Build update.php URL for the first pending plugin; WP will chain the rest.
					$first  = reset( $pending_updates );
					$others = array_slice( $pending_updates, 1 );
					// Build a comma-joined list of all basenames for WP bulk upgrade.
					$plugins_param = implode( ',', array_map( 'urlencode', $pending_updates ) );
					$bulk_url = admin_url(
						'update.php?action=update-selected&plugins=' . $plugins_param
						. '&_wpnonce=' . wp_create_nonce( 'bulk-update-plugins' )
					);
				?>
				<a href="<?php echo esc_url( $bulk_url ); ?>" class="button button-primary awdev-update-all-btn">
					<span class="dashicons dashicons-update"></span>
					<?php
					$count = count( $pending_updates );
					echo esc_html(
						sprintf(
							/* translators: %d: number of plugins with available updates */
							_n( 'Update %d plugin', 'Update all %d plugins', $count, 'awdev-plugins-updater' ),
							$count
						)
					);
					?>
				</a>
				<?php endif; ?>
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
