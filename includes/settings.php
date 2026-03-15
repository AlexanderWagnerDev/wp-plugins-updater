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
	register_setting( 'awdev_settings', 'awdev_auto_updates_global', [
		'type'              => 'boolean',
		'sanitize_callback' => 'rest_sanitize_boolean',
		'default'           => true,
	] );
	register_setting( 'awdev_settings', 'awdev_cache_hours', [
		'type'              => 'integer',
		'sanitize_callback' => 'awdev_sanitize_cache_hours',
		'default'           => 6,
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

function awdev_sanitize_cache_hours( $input ): int {
	$val = (int) $input;
	if ( $val < 1 ) {
		$val = 1;
	}
	if ( $val > 168 ) {
		$val = 168;
	}
	return $val;
}

/**
 * Auto-update hook: respect per-plugin toggle; global toggle overrides all when off.
 */
add_filter( 'auto_update_plugin', function ( $update, $item ) {
	$global = (bool) get_option( 'awdev_auto_updates_global', true );
	if ( ! $global ) {
		return false;
	}
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
 * 2. Match by raw dirname.
 * 3. Match by sanitize_key(dirname) - handles hyphens/underscores/case differences.
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

		// 3. sanitize_key folder match.
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
	$cache_hours = (int) get_option( 'awdev_cache_hours', 6 );
	if ( $cache_hours < 1 ) {
		$cache_hours = 1;
	}
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
			$data        = json_decode( wp_remote_retrieve_body( $response ) );
			$cache_hours = (int) get_option( 'awdev_cache_hours', 6 );
			if ( $cache_hours < 1 ) {
				$cache_hours = 1;
			}
			set_transient( $key, $data, $cache_hours * HOUR_IN_SECONDS );
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

	$managed       = (array) get_option( 'awdev_managed_plugins', [] );
	$auto_updates  = (array) get_option( 'awdev_auto_updates', [] );
	$global_auto   = get_option( 'awdev_auto_updates_global' );
	$cache_hours   = (int) get_option( 'awdev_cache_hours', 6 );

	// Correct basenames matching the actual folder/file names on disk.
	$built_in = [
		'awdev-plugins-updater/awdev-plugins-updater.php' => [
			'name'     => 'AWDev Plugins Updater',
			'api_slug' => 'awdev-plugin-updater',
		],
		'darkadmin/darkadmin.php' => [
			'name'     => 'DarkAdmin – Dark Mode for Adminpanel',
			'api_slug' => 'darkadmin',
		],
	];

	// Default: set global auto-update ON and per-plugin ON for built-ins on first load.
	if ( $global_auto === false ) {
		$global_auto = true;
		update_option( 'awdev_auto_updates_global', true );
	}
	if ( get_option( 'awdev_auto_updates' ) === false ) {
		$defaults = [];
		foreach ( array_keys( $built_in ) as $basename ) {
			$defaults[ $basename ] = true;
		}
		update_option( 'awdev_auto_updates', $defaults );
		$auto_updates = $defaults;
	}

	$statuses        = [];
	$pending_updates = [];

	foreach ( array_merge( array_keys( $built_in ), array_keys( $managed ) ) as $basename ) {
		$dirname_slug = sanitize_key( dirname( $basename ) );
		$api_slug     = $managed[ $basename ] ?? ( $built_in[ $basename ]['api_slug'] ?? $dirname_slug );
		$api_url      = AWDEV_UPDATE_SERVER . '/' . sanitize_key( $api_slug ) . '.php';

		$local        = awdev_get_local_version( $basename );
		$remote       = awdev_get_remote_version( $api_url, $dirname_slug );
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

					<!-- Global auto-update toggle -->
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
											class="awdev-per-plugin-toggle"
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
								$plugin_name = sanitize_text_field( dirname( $basename ) );
							?>
							<tr class="awdev-dynamic-row">
								<td><strong><?php echo esc_html( $plugin_name ); ?></strong></td>
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
											class="awdev-per-plugin-toggle"
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
									<span class="awdev-badge awdev-badge-custom"><?php esc_html_e( 'AWDev', 'awdev-plugins-updater' ); ?></span>
								</td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>

			<!-- Auto-Update Settings Card -->
			<div class="awdev-card">
				<div class="awdev-card-header">
					<span class="dashicons dashicons-clock"></span>
					<h2><?php esc_html_e( 'Auto-Update Settings', 'awdev-plugins-updater' ); ?></h2>
				</div>
				<div class="awdev-card-body">
					<p class="awdev-card-description">
						<?php esc_html_e( 'Configure how often the updater checks for new plugin versions. The cache duration controls the interval between remote API requests.', 'awdev-plugins-updater' ); ?>
					</p>
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
						<span class="description"><?php esc_html_e( 'Min: 1h &mdash; Max: 168h (7 days). Default: 6h.', 'awdev-plugins-updater' ); ?></span>
					</div>
				</div>
			</div>

			<div class="awdev-submit-row">
				<?php submit_button( __( 'Save Settings', 'awdev-plugins-updater' ), 'primary', 'submit', false ); ?>
				<?php if ( ! empty( $pending_updates ) ) :
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
					<?php
					printf(
						esc_html__( 'Update data is cached for %d hour(s) per plugin. Flush the cache to force WordPress to re-check all endpoints immediately.', 'awdev-plugins-updater' ),
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
