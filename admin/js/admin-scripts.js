function wpsp_get_taxonomy( type = 'post' ) {
    var response = jQuery.getJSON({
		type: 'POST',
		url: ajaxurl,
		data: {
			action: 'wpsp_get_taxonomies',
			wpsp_nonce: wpsp_object.nonce,
			post_type: type
		},
		async: false,
		dataType: 'json'
	});
				
	return response.responseJSON;
}

function wpsp_get_terms( type = 'post' ) {
    var response = jQuery.getJSON({
		type: 'POST',
		url: ajaxurl,
		data: {
			action: 'wpsp_get_terms',
			wpsp_nonce: wpsp_object.nonce,
			taxonomy: type
		},
		async: false,
		dataType: 'json'
	});
				
	return response.responseJSON;
}

function wpsp_get_option( key = 'wpsp_taxonomy' ) {
	var response = jQuery.getJSON({
		type: 'POST',
		url: ajaxurl,
		data: {
			action: 'wpsp_get_json_option',
			wpsp_nonce: wpsp_object.nonce,
			key: key,
			id: wpsp_object.post_id
		},
		async: false,
		dataType: 'json',
	}).done( function() {
		jQuery( '#butterbean-control-wpsp_taxonomy' ).css( 'display', 'block' );
		jQuery( '#butterbean-control-wpsp_tax_term' ).css( 'display', 'block' );
	});
				
	return response.responseJSON;
}

jQuery( document ).ready( function( $ ) {
	
	// Populate taxonomy select based on current post type value
	var taxonomies = wpsp_get_taxonomy( $( '#wpsp-post-type' ).val() );

	$('#wpsp-taxonomy').append( $( '<option></option>' ) );
	$.each(taxonomies, function(key, value) {
		$('#wpsp-taxonomy').append( $( '<option></option>' ).attr( 'value', value ).text( value ) );
	});

	// Set the selected taxonomy value on load
	$( '#wpsp-taxonomy' ).val( wpsp_get_option( 'wpsp_taxonomy' ) );

	var terms = wpsp_get_terms( $( '#wpsp-taxonomy' ).val() );
	$('#wpsp-terms').append( $( '<option></option>' ) );
	$.each(terms, function(key, value) {
		$('#wpsp-terms').append( $( '<option></option>' ).attr( 'value', value ).text( value ) );
	});
	
	// Set the selected term value on load
	$( '#wpsp-terms' ).val( wpsp_get_option( 'wpsp_tax_term' ) );
	
	// Hide the terms of taxonomy is empty on load
	if ( '' == $( '#wpsp-taxonomy' ).val() )
		$( '#butterbean-control-wpsp_tax_term' ).hide();
	
	// When changing the post type option
	$( '#wpsp-post-type' ).change(function() {
		
		$( '#butterbean-control-wpsp_tax_term' ).hide();
		
		$( '#wpsp-taxonomy' ).empty();
		
		$( '#wpsp-terms' ).empty();
		$( '#wpsp-terms' ).append( $( '<option></option>' ) );
		
		var selectValues = wpsp_get_taxonomy( $(this).val(), false );

		$('#wpsp-taxonomy').append( $( '<option></option>' ) );
		$.each(selectValues, function(key, value) {
			 $('#wpsp-taxonomy').append( $( '<option></option>' ).attr( 'value', value ).text( value ) );
		});
		if ( '' == selectValues ) {
			$( '#butterbean-control-wpsp_taxonomy' ).hide();
		} else {
			$( '#butterbean-control-wpsp_taxonomy' ).show();
		}
	});
	
	// When changing the taxonomy option
	$( '#wpsp-taxonomy' ).change(function() {
		
		$( '#wpsp-terms' ).empty();
		var selectValues = wpsp_get_terms( $(this).val() );

		$('#wpsp-terms').append( $( '<option></option>' ) );
		$.each(selectValues, function(key, value) {
			 $('#wpsp-terms').append( $( '<option></option>' ).attr( 'value', value ).text( value ) );
		});

		if ( '' == selectValues || ',' == selectValues )
			$( '#butterbean-control-wpsp_tax_term' ).hide();
		else
			$( '#butterbean-control-wpsp_tax_term' ).show();
	});
	
	// Dealing with the image options
	if ( ! $( '#wpsp-image' ).is( ':checked' ) ) {
		$( this ).parent().parent().siblings().hide();
	}
	
	$( '#wpsp-image' ).change(function() {
		if ( ! this.checked ) {
			$( this ).parent().parent().siblings().hide();
		} else {
			$( this ).parent().parent().siblings().show();
		}
	});
	
	// Excerpt or full content	
	$( '#wpsp-content-type' ).change(function() {
		if ( 'excerpt' == $( this ).val() ) {
			$( '#butterbean-control-wpsp_excerpt_length' ).show();
		} else {
			$( '#butterbean-control-wpsp_excerpt_length' ).hide();
		}
	});
	
	// Author location
	if ( ! $( '#wpsp-include-author' ).is( ':checked' ) ) {
		$( '#butterbean-control-wpsp_author_location' ).hide();
	}
	
	$( '#wpsp-include-author' ).change(function() {
		if ( ! this.checked ) {
			$( '#butterbean-control-wpsp_author_location' ).hide();
		} else {
			$( '#butterbean-control-wpsp_author_location' ).show();
		}
	});
	
	// Date location
	if ( ! $( '#wpsp-include-date' ).is( ':checked' ) ) {
		$( '#butterbean-control-wpsp_date_location' ).hide();
	}
	
	$( '#wpsp-include-date' ).change(function() {
		if ( ! this.checked ) {
			$( '#butterbean-control-wpsp_date_location' ).hide();
		} else {
			$( '#butterbean-control-wpsp_date_location' ).show();
		}
	});
	
	// Terms location
	if ( ! $( '#wpsp-include-terms' ).is( ':checked' ) ) {
		$( '#butterbean-control-wpsp_terms_location' ).hide();
	}
	
	$( '#wpsp-include-terms' ).change(function() {
		if ( ! this.checked ) {
			$( '#butterbean-control-wpsp_terms_location' ).hide();
		} else {
			$( '#butterbean-control-wpsp_terms_location' ).show();
		}
	});
	
	// Dealing with the social options
	$( '#wpsp-social-sharing' ).parent().parent().siblings().hide();
	if ( $( '#wpsp-social-sharing' ).is( ':checked' ) ) {
		$( '#wpsp-social-sharing' ).parent().parent().siblings().show();
	}
	
	$( '#wpsp-social-sharing' ).change(function() {
		if ( ! this.checked ) {
			$( this ).parent().parent().siblings().hide();
		} else {
			$( this ).parent().parent().siblings().show();
		}
	});
	
	// Pagination
	if ( ! $( '#wpsp-pagination' ).is( ':checked' ) ) {
		$( '#butterbean-control-wpsp_ajax_pagination' ).hide();
	}
	
	$( '#wpsp-pagination' ).change(function() {
		if ( ! this.checked ) {
			$( '#butterbean-control-wpsp_ajax_pagination' ).hide();
		} else {
			$( '#butterbean-control-wpsp_ajax_pagination' ).show();
		}
	});
});