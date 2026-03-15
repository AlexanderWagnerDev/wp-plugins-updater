/* AWDev Plugins Updater - Settings Page JS */
( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {

		// --- Global auto-update toggle: check/uncheck all per-plugin toggles ---
		var globalToggle = document.getElementById( 'awdev-global-auto-update' );
		if ( globalToggle ) {
			globalToggle.addEventListener( 'change', function () {
				document.querySelectorAll( '.awdev-per-plugin-toggle' ).forEach( function ( cb ) {
					cb.checked = globalToggle.checked;
				} );
			} );
		}

		// --- Add plugin row ---
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
					  '<input type="checkbox" class="awdev-per-plugin-toggle" name="awdev_auto_updates[__new_' + ts + ']" value="1" checked />' +
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

		// --- Remove plugin row ---
		document.addEventListener( 'click', function ( e ) {
			var btn = e.target.closest( '.awdev-remove-row' );
			if ( btn ) {
				btn.closest( 'tr' ).remove();
			}
		} );

	} );
} )();
