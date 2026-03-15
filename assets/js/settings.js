/* AWDev Plugins Updater - Settings Page JS */
( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {

		var ajaxUrl = ( window.awdevSettings && awdevSettings.ajaxUrl ) ? awdevSettings.ajaxUrl : '';
		var nonce   = ( window.awdevSettings && awdevSettings.nonce )   ? awdevSettings.nonce   : '';

		// Send an AJAX request to instantly save a toggle state.
		function saveToggle( action, data ) {
			var params = new URLSearchParams();
			params.append( 'action', action );
			params.append( '_ajax_nonce', nonce );
			Object.keys( data ).forEach( function ( k ) { params.append( k, data[ k ] ); } );

			fetch( ajaxUrl, {
				method  : 'POST',
				headers : { 'Content-Type': 'application/x-www-form-urlencoded' },
				body    : params.toString(),
			} );
		}

		// Global auto-update toggle: update all per-plugin toggles visually and save everything.
		var globalToggle = document.getElementById( 'awdev-global-auto-update' );
		if ( globalToggle ) {
			globalToggle.addEventListener( 'change', function () {
				var enabled = globalToggle.checked;
				document.querySelectorAll( '.awdev-per-plugin-toggle' ).forEach( function ( cb ) {
					cb.checked = enabled;
				} );
				saveToggle( 'awdev_toggle_global_auto_update', { enabled: enabled ? '1' : '0' } );
			} );
		}

		// Per-plugin toggles: save instantly on change.
		document.querySelectorAll( '.awdev-per-plugin-toggle' ).forEach( function ( cb ) {
			cb.addEventListener( 'change', function () {
				saveToggle( 'awdev_toggle_auto_update', {
					basename : cb.dataset.basename,
					enabled  : cb.checked ? '1' : '0',
				} );
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
					'<td>–</td>' +
					'<td>–</td>' +
					'<td>' +
					  '<label class="awdev-toggle">' +
					  '<input type="checkbox" class="awdev-per-plugin-toggle" data-basename="" value="1" checked />' +
					  '<span class="awdev-toggle-slider"></span>' +
					  '</label>' +
					'</td>' +
					'<td>–</td>' +
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

	} );
} )();
