/* AWDev Plugins Updater - Settings Page JS */
( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {

		var s                  = window.awdevSettings || {};
		var ajaxUrl            = s.ajaxUrl            || '';
		var nonce              = s.nonce              || '';
		var nonceRemoteVersion = s.nonceRemoteVersion || '';
		var nonceCheckPlugin   = s.nonceCheckPlugin   || '';

		// Send an AJAX request to instantly save a toggle state.
		// On failure the checkbox is visually reverted so the user knows the save did not persist.
		function saveToggle( action, data, checkboxEl ) {
			var params = new URLSearchParams();
			params.append( 'action', action );
			params.append( '_ajax_nonce', nonce );
			Object.keys( data ).forEach( function ( k ) { params.append( k, data[ k ] ); } );

			fetch( ajaxUrl, {
				method  : 'POST',
				headers : { 'Content-Type': 'application/x-www-form-urlencoded' },
				body    : params.toString(),
			} )
			.then( function ( res ) {
				if ( ! res.ok ) { throw new Error( 'HTTP ' + res.status ); }
				return res.json();
			} )
			.then( function ( json ) {
				if ( ! json.success ) { throw new Error( 'wp_send_json_error' ); }
			} )
			.catch( function () {
				// Revert the toggle visually if the save failed.
				if ( checkboxEl ) {
					checkboxEl.checked = ! checkboxEl.checked;
				}
			} );
		}

		// Fetch all remote versions via AJAX and update the table cells.
		function loadRemoteVersions() {
			if ( ! ajaxUrl || ! nonceRemoteVersion ) { return; }

			var params = new URLSearchParams();
			params.append( 'action', 'awdev_get_remote_versions' );
			params.append( '_ajax_nonce', nonceRemoteVersion );

			fetch( ajaxUrl, {
				method  : 'POST',
				headers : { 'Content-Type': 'application/x-www-form-urlencoded' },
				body    : params.toString(),
			} )
			.then( function ( res ) { return res.json(); } )
			.then( function ( json ) {
				if ( ! json.success || ! json.data ) { return; }

				var versions = json.data;

				document.querySelectorAll( '.awdev-remote-version' ).forEach( function ( cell ) {
					var slug    = cell.dataset.slug;
					var remote  = versions[ slug ] || '?';

					var row         = cell.closest( 'tr' );
					var actionsCell = row ? row.querySelector( '.awdev-actions-cell' ) : null;
					var local       = actionsCell ? actionsCell.dataset.local   : '';
					var basename    = actionsCell ? actionsCell.dataset.basename : '';

					var needsUpdate = ( remote !== '?' && local && local !== '?' && compareVersions( remote, local ) > 0 );

					if ( needsUpdate ) {
						cell.innerHTML = '<span class="awdev-version-new">' + escHtml( remote ) + '</span>';
					} else {
						cell.textContent = remote;
					}

					// Inject Update button when a newer version is available.
					if ( needsUpdate && actionsCell && basename ) {
						var placeholder = actionsCell.querySelector( '.awdev-update-btn-placeholder' );
						if ( placeholder && ! placeholder.dataset.injected ) {
							placeholder.dataset.injected = '1';
							var updateNonce = s.updateNonces && s.updateNonces[ basename ] ? s.updateNonces[ basename ] : '';
							if ( updateNonce && s.updateBase ) {
								var href = s.updateBase + '&plugin=' + encodeURIComponent( basename ) + '&_wpnonce=' + updateNonce;
								placeholder.innerHTML =
									'<a href="' + escHtml( href ) + '" class="button button-small button-primary awdev-update-btn">' +
									'<span class="dashicons dashicons-arrow-up-alt"></span> ' +
									escHtml( s.i18n && s.i18n.update ? s.i18n.update : 'Update' ) +
									'</a>';
							}
						}
					}
				} );
			} )
			.catch( function () {
				document.querySelectorAll( '.awdev-version-loading' ).forEach( function ( el ) {
					el.textContent = '?';
				} );
			} );
		}

		// Handle re-check button: clear transient cache then reload versions.
		document.addEventListener( 'click', function ( e ) {
			var btn = e.target.closest( '.awdev-check-btn' );
			if ( ! btn || ! nonceCheckPlugin ) { return; }

			e.preventDefault();

			var slug = btn.dataset.slug || '';
			if ( ! slug ) { return; }

			var params = new URLSearchParams();
			params.append( 'action',       'awdev_check_plugin' );
			params.append( '_ajax_nonce',  nonceCheckPlugin );
			params.append( 'dirname_slug', slug );

			var icon = btn.querySelector( '.dashicons' );
			btn.disabled = true;
			if ( icon ) { icon.classList.add( 'awdev-spin' ); }

			// Reset the version cell to the loading indicator before the new fetch.
			var remoteCell = document.querySelector( '.awdev-remote-version[data-slug="' + slug + '"]' );
			if ( remoteCell ) {
				remoteCell.innerHTML = '<span class="awdev-version-loading">\u2026</span>';
			}

			fetch( ajaxUrl, {
				method  : 'POST',
				headers : { 'Content-Type': 'application/x-www-form-urlencoded' },
				body    : params.toString(),
			} )
			.then( function ( res ) {
				if ( ! res.ok ) { throw new Error( 'HTTP ' + res.status ); }
				return res.json();
			} )
			.then( function ( json ) {
				if ( ! json.success ) { throw new Error( 'wp_send_json_error' ); }
				loadRemoteVersions();
			} )
			.catch( function () {
				// Show '?' in the version cell so the user knows the check failed.
				if ( remoteCell ) {
					var loadingEl = remoteCell.querySelector( '.awdev-version-loading' );
					if ( loadingEl ) { loadingEl.textContent = '?'; }
				}
			} )
			.finally( function () {
				btn.disabled = false;
				if ( icon ) { icon.classList.remove( 'awdev-spin' ); }
			} );
		} );

		// Compare two semver strings. Returns >0 if a>b, <0 if a<b, 0 if equal.
		// Uses parseInt() so pre-release suffixes like "-beta" are safely ignored.
		function compareVersions( a, b ) {
			var pa = a.split( '.' ).map( function ( n ) { return parseInt( n, 10 ) || 0; } );
			var pb = b.split( '.' ).map( function ( n ) { return parseInt( n, 10 ) || 0; } );
			for ( var i = 0; i < Math.max( pa.length, pb.length ); i++ ) {
				var diff = ( pa[ i ] || 0 ) - ( pb[ i ] || 0 );
				if ( diff !== 0 ) { return diff; }
			}
			return 0;
		}

		// Minimal HTML-escape for dynamic content inserted via innerHTML.
		// Escapes &, <, >, ", and ' to cover both attribute and text contexts.
		function escHtml( str ) {
			return String( str )
				.replace( /&/g,  '&amp;' )
				.replace( /</g,  '&lt;' )
				.replace( />/g,  '&gt;' )
				.replace( /"/g,  '&quot;' )
				.replace( /'/g,  '&#039;' );
		}

		// Global auto-update toggle: update all per-plugin toggles visually and save everything.
		var globalToggle = document.getElementById( 'awdev-global-auto-update' );
		if ( globalToggle ) {
			globalToggle.addEventListener( 'change', function () {
				var enabled = globalToggle.checked;
				document.querySelectorAll( '.awdev-per-plugin-toggle' ).forEach( function ( cb ) {
					cb.checked = enabled;
				} );
				saveToggle( 'awdev_toggle_global_auto_update', { enabled: enabled ? '1' : '0' }, globalToggle );
			} );
		}

		// Per-plugin toggles: save instantly on change.
		document.querySelectorAll( '.awdev-per-plugin-toggle' ).forEach( function ( cb ) {
			cb.addEventListener( 'change', function () {
				saveToggle( 'awdev_toggle_auto_update', {
					basename : cb.dataset.basename,
					enabled  : cb.checked ? '1' : '0',
				}, cb );
			} );
		} );

		// Add plugin row.
		var addBtn = document.getElementById( 'awdev-add-plugin' );
		if ( addBtn ) {
			addBtn.addEventListener( 'click', function () {
				var tbody = document.querySelector( '.awdev-plugin-table tbody' );
				var ts    = Date.now();
				var row   = document.createElement( 'tr' );
				row.className = 'awdev-dynamic-row';
				row.innerHTML =
					'<td><input type="text" name="awdev_managed_plugins_basename[' + ts + ']" placeholder="folder/plugin-file.php" class="awdev-input-basename" /></td>' +
					'<td>\u2013</td>' +
					'<td>\u2013</td>' +
					'<td>' +
					  '<label class="awdev-toggle">' +
					  '<input type="checkbox" class="awdev-per-plugin-toggle" data-basename="" value="1" checked />' +
					  '<span class="awdev-toggle-slider"></span>' +
					  '</label>' +
					'</td>' +
					'<td>\u2013</td>' +
					'<td>' +
					  '<input type="text" name="awdev_managed_plugins[__new_' + ts + ']" placeholder="api-slug" class="awdev-input-slug" />' +
					  '<button type="button" class="awdev-remove-row button-link" title="Remove"><span class="dashicons dashicons-trash"></span></button>' +
					'</td>';
				tbody.appendChild( row );
				row.querySelector( '.awdev-input-basename' ).focus();
			} );
		}

		// Remove plugin row.
		document.addEventListener( 'click', function ( e ) {
			var btn = e.target.closest( '.awdev-remove-row' );
			if ( btn ) {
				btn.closest( 'tr' ).remove();
			}
		} );

		// Kick off remote version fetch after DOM is ready.
		loadRemoteVersions();

	} );
} )();
