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
		__( 'AWDev Plugin Updater', 'awdev-plugin-updater' ),
		__( 'AWDev Updater', 'awdev-plugin-updater' ),
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
		wp_die( esc_html__( 'Not allowed.', 'awdev-plugin-updater' ) );
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
 * Render the settings page.
 */
function awdev_render_settings_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$managed = (array) get_option( 'awdev_managed_plugins', [] );

	$built_in = [
		'awdev-plugin-updater/awdev-plugin-updater.php' => [
			'name'     => 'AWDev Plugin Updater',
			'api_slug' => 'awdev-plugin-updater',
		],
		'darkadmin-dark-mode-for-adminpanel/darkadmin.php' => [
			'name'     => 'DarkAdmin – Dark Mode for Adminpanel',
			'api_slug' => 'darkadmin',
		],
	];

	$statuses = [];
	foreach ( array_merge( array_keys( $built_in ), array_keys( $managed ) ) as $basename ) {
		$slug = $managed[ $basename ] ?? ( $built_in[ $basename ]['api_slug'] ?? '' );
		$key  = 'awdev_upd_' . sanitize_key( dirname( $basename ) );
		$data = get_transient( $key );
		$statuses[ $basename ] = [
			'version' => $data ? ( $data->version ?? '–' ) : '–',
			'api_url' => AWDEV_UPDATE_SERVER . '/' . sanitize_key( $slug ) . '.php',
		];
	}

	$cache_flushed = isset( $_GET['cache-flushed'] );
	?>
	<div class="wrap awdev-settings-wrap">

		<div class="awdev-page-header">
			<div class="awdev-page-header-inner">
				<span class="awdev-header-icon dashicons dashicons-update-alt"></span>
				<div>
					<h1 class="awdev-page-title"><?php esc_html_e( 'AWDev Plugin Updater', 'awdev-plugin-updater' ); ?></h1>
					<p class="awdev-page-subtitle">
						<?php esc_html_e( 'Self-hosted update manager for AlexanderWagnerDev plugins', 'awdev-plugin-updater' ); ?>
						&mdash; v<?php echo esc_html( AWDEV_UPDATER_VERSION ); ?>
					</p>
				</div>
			</div>
			<div class="awdev-status-badge awdev-status-active">
				<span class="awdev-status-dot"></span>
				<?php esc_html_e( 'Active', 'awdev-plugin-updater' ); ?>
			</div>
		</div>

		<?php if ( $cache_flushed ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( '✓ Update cache flushed. WordPress will re-check all plugins on the next update cycle.', 'awdev-plugin-updater' ); ?></p></div>
		<?php endif; ?>

		<?php if ( isset( $_GET['settings-updated'] ) ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( '✓ Settings saved.', 'awdev-plugin-updater' ); ?></p></div>
		<?php endif; ?>

		<form method="post" action="options.php">
			<?php settings_fields( 'awdev_settings' ); ?>

			<!-- Managed Plugins Card -->
			<div class="awdev-card">
				<div class="awdev-card-header">
					<span class="dashicons dashicons-plugins-checked"></span>
					<h2><?php esc_html_e( 'Managed Plugins', 'awdev-plugin-updater' ); ?></h2>
				</div>
				<div class="awdev-card-body">
					<p class="awdev-card-description">
						<?php esc_html_e( 'All AlexanderWagnerDev plugins that receive updates from the self-hosted server.', 'awdev-plugin-updater' ); ?>
					</p>

					<table class="awdev-plugin-table">
						<thead>
							<tr>
								<th><?php esc_html_e( 'Plugin', 'awdev-plugin-updater' ); ?></th>
								<th><?php esc_html_e( 'API Slug', 'awdev-plugin-updater' ); ?></th>
								<th><?php esc_html_e( 'Endpoint', 'awdev-plugin-updater' ); ?></th>
								<th><?php esc_html_e( 'Remote Version', 'awdev-plugin-updater' ); ?></th>
								<th><?php esc_html_e( 'Type', 'awdev-plugin-updater' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $built_in as $basename => $info ) : ?>
							<tr>
								<td><strong><?php echo esc_html( $info['name'] ); ?></strong><br><code><?php echo esc_html( $basename ); ?></code></td>
								<td><code><?php echo esc_html( $info['api_slug'] ); ?></code></td>
								<td><a href="<?php echo esc_url( $statuses[ $basename ]['api_url'] ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $statuses[ $basename ]['api_url'] ); ?></a></td>
								<td><?php echo esc_html( $statuses[ $basename ]['version'] ); ?></td>
								<td><span class="awdev-badge awdev-badge-builtin"><?php esc_html_e( 'Built-in', 'awdev-plugin-updater' ); ?></span></td>
							</tr>
							<?php endforeach; ?>

							<?php foreach ( $managed as $basename => $slug ) : ?>
								<?php if ( isset( $built_in[ $basename ] ) ) continue; ?>
							<tr class="awdev-dynamic-row">
								<td><input type="text" name="awdev_managed_plugins[<?php echo esc_attr( $basename ); ?>][basename]" value="<?php echo esc_attr( $basename ); ?>" class="awdev-input-basename" placeholder="folder/plugin.php" /></td>
								<td><input type="text" name="awdev_managed_plugins[<?php echo esc_attr( $basename ); ?>][slug]" value="<?php echo esc_attr( $slug ); ?>" class="awdev-input-slug" placeholder="my-plugin" /></td>
								<td><a href="<?php echo esc_url( $statuses[ $basename ]['api_url'] ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $statuses[ $basename ]['api_url'] ); ?></a></td>
								<td><?php echo esc_html( $statuses[ $basename ]['version'] ); ?></td>
								<td>
									<span class="awdev-badge awdev-badge-custom"><?php esc_html_e( 'Custom', 'awdev-plugin-updater' ); ?></span>
									<button type="button" class="awdev-remove-row button-link" title="<?php esc_attr_e( 'Remove', 'awdev-plugin-updater' ); ?>"><span class="dashicons dashicons-trash"></span></button>
								</td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>

					<div class="awdev-add-row">
						<button type="button" id="awdev-add-plugin" class="button">
							<span class="dashicons dashicons-plus-alt2"></span>
							<?php esc_html_e( 'Add Plugin', 'awdev-plugin-updater' ); ?>
						</button>
					</div>
				</div>
			</div>

			<div class="awdev-submit-row">
				<?php submit_button( __( 'Save Settings', 'awdev-plugin-updater' ), 'primary', 'submit', false ); ?>
			</div>
		</form>

		<!-- Cache Management Card -->
		<div class="awdev-card">
			<div class="awdev-card-header">
				<span class="dashicons dashicons-performance"></span>
				<h2><?php esc_html_e( 'Cache Management', 'awdev-plugin-updater' ); ?></h2>
			</div>
			<div class="awdev-card-body">
				<p class="awdev-card-description">
					<?php esc_html_e( 'Update data is cached for 6 hours per plugin. Flush the cache to force WordPress to re-check all endpoints immediately.', 'awdev-plugin-updater' ); ?>
				</p>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="awdev_flush_cache" />
					<?php wp_nonce_field( 'awdev_flush_cache' ); ?>
					<button type="submit" class="button button-secondary">
						<span class="dashicons dashicons-image-rotate"></span>
						<?php esc_html_e( 'Flush Update Cache', 'awdev-plugin-updater' ); ?>
					</button>
				</form>
			</div>
		</div>

		<div class="awdev-footer">
			<p>
				<?php esc_html_e( 'AWDev Plugin Updater', 'awdev-plugin-updater' ); ?> &ndash;
				<a href="https://alexanderwagnerdev.com" target="_blank" rel="noopener">AlexanderWagnerDev</a>
				&mdash;
				<a href="https://github.com/AlexanderWagnerDev/wp-plugins-updater" target="_blank" rel="noopener">GitHub</a>
			</p>
		</div>

	</div>
	<?php
}
