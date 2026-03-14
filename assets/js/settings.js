( function () {
	'use strict';

	const table  = document.querySelector( '.awdev-plugin-table tbody' );
	const addBtn = document.getElementById( 'awdev-add-plugin' );
	let   rowIdx = Date.now();

	// Add new plugin row dynamically
	if ( addBtn && table ) {
		addBtn.addEventListener( 'click', function () {
			const idx = rowIdx++;
			const tr  = document.createElement( 'tr' );
			tr.classList.add( 'awdev-dynamic-row' );
			tr.innerHTML = `
				<td><input type="text" name="awdev_managed_plugins[new_${idx}][basename]" class="awdev-input-basename" placeholder="folder/plugin.php" /></td>
				<td><input type="text" name="awdev_managed_plugins[new_${idx}][slug]" class="awdev-input-slug" placeholder="my-plugin" /></td>
				<td><span class="awdev-field-desc">—</span></td>
				<td><span class="awdev-field-desc">—</span></td>
				<td>
					<span class="awdev-badge awdev-badge-custom">Custom</span>
					<button type="button" class="awdev-remove-row button-link" title="Remove"><span class="dashicons dashicons-trash"></span></button>
				</td>
			`;
			table.appendChild( tr );
			tr.querySelector( '.awdev-input-basename' ).focus();
		} );
	}

	// Remove row on trash click (delegated)
	if ( table ) {
		table.addEventListener( 'click', function ( e ) {
			const btn = e.target.closest( '.awdev-remove-row' );
			if ( btn ) {
				btn.closest( 'tr' ).remove();
			}
		} );
	}
} )();
