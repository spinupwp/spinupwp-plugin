jQuery( document ).ready( function( $ ) {
	$( '.spinupwp.notice.is-dismissible' ).on( 'click', '.notice-dismiss', function ( e ) {
		e.preventDefault();

		$.post( window.ajaxurl, {
			action: 'spinupwp_dismiss_notice',
			nonce: $( this ).parent().data( 'nonce' ),
			notice: $( this ).parent().data( 'notice' ),
		} );
	} );
} );
