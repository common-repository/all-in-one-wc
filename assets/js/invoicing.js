jQuery( document ).ready( function() {
	jQuery( 'a.aiow_need_confirmation' ).click( function() {
		return confirm( "Are you sure?" );
	} );
} );