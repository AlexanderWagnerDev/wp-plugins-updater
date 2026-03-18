<?php
/**
 * Tests for the AWDev_Updater class and related functions.
 */

class AwdevUpdaterTest extends WP_UnitTestCase {

    private AWDev_Updater $updater;
    private string $basename = 'test-plugin/test-plugin.php';
    private string $api_url  = 'https://example.com/api/test-plugin.php';

    public function set_up(): void {
        parent::set_up();
        $this->updater = new AWDev_Updater( $this->basename, $this->api_url );
    }

    // -------------------------------------------------------------------------
    // Constructor / Getters
    // -------------------------------------------------------------------------

    public function test_get_basename_returns_correct_value(): void {
        $this->assertSame( $this->basename, $this->updater->get_basename() );
    }

    public function test_get_slug_is_dirname_of_basename(): void {
        $this->assertSame( 'test-plugin', $this->updater->get_slug() );
    }

    public function test_get_api_url_returns_correct_value(): void {
        $this->assertSame( $this->api_url, $this->updater->get_api_url() );
    }

    // -------------------------------------------------------------------------
    // check_update: transient injection
    // -------------------------------------------------------------------------

    public function test_check_update_returns_transient_unchanged_when_checked_empty(): void {
        $transient = (object) [ 'checked' => [] ];
        $result    = $this->updater->check_update( $transient );
        $this->assertSame( $transient, $result );
        $this->assertArrayNotHasKey( $this->basename, (array) ( $result->response ?? [] ) );
    }

    public function test_check_update_injects_response_when_remote_version_is_newer(): void {
        $remote = (object) [
            'version'      => '9.9.9',
            'download_url' => 'https://example.com/test-plugin.zip',
            'details_url'  => 'https://example.com/test-plugin',
        ];
        set_transient( 'awdev_upd_test-plugin', $remote, HOUR_IN_SECONDS );

        $transient          = (object) [
            'checked'  => [ $this->basename => '0.0.1' ],
            'response' => [],
        ];
        $result = $this->updater->check_update( $transient );

        $this->assertArrayHasKey( $this->basename, (array) $result->response );
        $this->assertSame( '9.9.9', $result->response[ $this->basename ]->new_version );
        $this->assertSame( 'test-plugin', $result->response[ $this->basename ]->slug );
    }

    public function test_check_update_does_not_inject_when_version_is_equal(): void {
        $remote = (object) [ 'version' => '1.0.0' ];
        set_transient( 'awdev_upd_test-plugin', $remote, HOUR_IN_SECONDS );

        $transient = (object) [
            'checked'  => [ $this->basename => '1.0.0' ],
            'response' => [],
        ];
        $result = $this->updater->check_update( $transient );

        $this->assertArrayNotHasKey( $this->basename, (array) $result->response );
    }

    public function test_check_update_does_not_inject_when_version_is_older(): void {
        $remote = (object) [ 'version' => '0.5.0' ];
        set_transient( 'awdev_upd_test-plugin', $remote, HOUR_IN_SECONDS );

        $transient = (object) [
            'checked'  => [ $this->basename => '1.0.0' ],
            'response' => [],
        ];
        $result = $this->updater->check_update( $transient );

        $this->assertArrayNotHasKey( $this->basename, (array) $result->response );
    }

    public function test_check_update_skips_when_remote_data_missing_version(): void {
        $remote = (object) [ 'name' => 'Test Plugin' ]; // no version field
        set_transient( 'awdev_upd_test-plugin', $remote, HOUR_IN_SECONDS );

        $transient = (object) [
            'checked'  => [ $this->basename => '1.0.0' ],
            'response' => [],
        ];
        $result = $this->updater->check_update( $transient );

        $this->assertArrayNotHasKey( $this->basename, (array) $result->response );
    }

    // -------------------------------------------------------------------------
    // plugin_info
    // -------------------------------------------------------------------------

    public function test_plugin_info_returns_original_result_for_different_action(): void {
        $original = new stdClass();
        $args     = (object) [ 'slug' => 'test-plugin' ];
        $result   = $this->updater->plugin_info( $original, 'query_plugins', $args );
        $this->assertSame( $original, $result );
    }

    public function test_plugin_info_returns_original_result_for_different_slug(): void {
        $original = new stdClass();
        $args     = (object) [ 'slug' => 'another-plugin' ];
        $result   = $this->updater->plugin_info( $original, 'plugin_information', $args );
        $this->assertSame( $original, $result );
    }

    public function test_plugin_info_returns_object_with_version_when_data_available(): void {
        $remote = (object) [
            'version'      => '2.0.0',
            'name'         => 'Test Plugin',
            'download_url' => 'https://example.com/test-plugin.zip',
            'details_url'  => 'https://example.com/test-plugin',
            'changelog'    => '<p>Changelog</p>',
        ];
        set_transient( 'awdev_upd_test-plugin', $remote, HOUR_IN_SECONDS );

        $args   = (object) [ 'slug' => 'test-plugin' ];
        $result = $this->updater->plugin_info( false, 'plugin_information', $args );

        $this->assertIsObject( $result );
        $this->assertSame( '2.0.0', $result->version );
        $this->assertSame( 'test-plugin', $result->slug );
        $this->assertSame( 'Test Plugin', $result->name );
        $this->assertArrayHasKey( 'changelog', $result->sections );
    }

    public function test_plugin_info_returns_original_when_no_remote_data(): void {
        delete_transient( 'awdev_upd_test-plugin' );
        $original = new stdClass();
        $args     = (object) [ 'slug' => 'test-plugin' ];

        add_filter( 'pre_http_request', fn() => new WP_Error( 'http_error', 'Mocked failure' ) );
        $result = $this->updater->plugin_info( $original, 'plugin_information', $args );
        remove_all_filters( 'pre_http_request' );

        $this->assertSame( $original, $result );
    }

    // -------------------------------------------------------------------------
    // clear_cache
    // -------------------------------------------------------------------------

    public function test_clear_cache_deletes_transient(): void {
        set_transient( 'awdev_upd_test-plugin', (object) [ 'version' => '1.0.0' ], HOUR_IN_SECONDS );
        $this->updater->clear_cache();
        $this->assertFalse( get_transient( 'awdev_upd_test-plugin' ) );
    }

    // -------------------------------------------------------------------------
    // fix_folder_name
    // -------------------------------------------------------------------------

    public function test_fix_folder_name_returns_source_unchanged_for_other_plugin(): void {
        $upgrader   = $this->createMock( WP_Upgrader::class );
        $hook_extra = [ 'plugin' => 'other-plugin/other-plugin.php' ];
        $source     = '/tmp/upgrade/test-plugin-AbCdE/';
        $remote     = '/tmp/upgrade/';

        $result = $this->updater->fix_folder_name( $source, $remote, $upgrader, $hook_extra );
        $this->assertSame( $source, $result );
    }

    // -------------------------------------------------------------------------
    // awdev_built_in_plugins
    // -------------------------------------------------------------------------

    public function test_built_in_plugins_returns_array(): void {
        $plugins = awdev_built_in_plugins();
        $this->assertIsArray( $plugins );
        $this->assertNotEmpty( $plugins );
    }

    public function test_built_in_plugins_contains_updater_itself(): void {
        $plugins = awdev_built_in_plugins();
        $this->assertArrayHasKey( 'awdev-plugins-updater/awdev-plugins-updater.php', $plugins );
    }

    public function test_built_in_plugins_entries_have_required_keys(): void {
        foreach ( awdev_built_in_plugins() as $basename => $info ) {
            $this->assertArrayHasKey( 'name', $info, "Missing 'name' for $basename" );
            $this->assertArrayHasKey( 'api_slug', $info, "Missing 'api_slug' for $basename" );
            $this->assertNotEmpty( $info['name'] );
            $this->assertNotEmpty( $info['api_slug'] );
        }
    }

    // -------------------------------------------------------------------------
    // awdev_fetch_api_data
    // -------------------------------------------------------------------------

    public function test_fetch_api_data_returns_cached_transient(): void {
        $cached = (object) [ 'version' => '3.0.0' ];
        set_transient( 'awdev_upd_cached-plugin', $cached, HOUR_IN_SECONDS );

        $result = awdev_fetch_api_data( 'awdev_upd_cached-plugin', 'https://example.com/api.php' );
        $this->assertEquals( $cached, $result );
    }

    public function test_fetch_api_data_returns_null_on_http_error(): void {
        delete_transient( 'awdev_upd_fail-plugin' );
        add_filter( 'pre_http_request', fn() => new WP_Error( 'http_error', 'Mocked failure' ) );

        $result = awdev_fetch_api_data( 'awdev_upd_fail-plugin', 'https://example.com/api.php' );

        remove_all_filters( 'pre_http_request' );
        $this->assertNull( $result );
    }

    // -------------------------------------------------------------------------
    // awdev_activate defaults
    // -------------------------------------------------------------------------

    public function test_activate_sets_default_cache_hours(): void {
        delete_option( 'awdev_cache_hours' );
        awdev_activate();
        $this->assertSame( 6, (int) get_option( 'awdev_cache_hours' ) );
    }

    public function test_activate_sets_default_global_auto_update(): void {
        delete_option( 'awdev_auto_updates_global' );
        awdev_activate();
        $this->assertTrue( (bool) get_option( 'awdev_auto_updates_global' ) );
    }

    public function test_activate_does_not_overwrite_existing_cache_hours(): void {
        update_option( 'awdev_cache_hours', 12 );
        awdev_activate();
        $this->assertSame( 12, (int) get_option( 'awdev_cache_hours' ) );
    }

    // -------------------------------------------------------------------------
    // awdev_sync_auto_update_defaults
    // -------------------------------------------------------------------------

    public function test_sync_auto_update_defaults_adds_missing_entries(): void {
        delete_option( 'awdev_auto_updates' );
        awdev_sync_auto_update_defaults();

        $auto_updates = (array) get_option( 'awdev_auto_updates', [] );
        foreach ( array_keys( awdev_built_in_plugins() ) as $basename ) {
            $this->assertArrayHasKey( $basename, $auto_updates );
            $this->assertTrue( $auto_updates[ $basename ] );
        }
    }

    public function test_sync_auto_update_defaults_does_not_overwrite_existing(): void {
        $existing = [];
        foreach ( array_keys( awdev_built_in_plugins() ) as $basename ) {
            $existing[ $basename ] = false;
        }
        update_option( 'awdev_auto_updates', $existing );

        awdev_sync_auto_update_defaults();

        $auto_updates = (array) get_option( 'awdev_auto_updates', [] );
        foreach ( array_keys( awdev_built_in_plugins() ) as $basename ) {
            $this->assertFalse( $auto_updates[ $basename ] );
        }
    }
}
