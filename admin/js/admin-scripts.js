function wpsp_get_taxonomy( type ) {
	type = typeof type !== 'undefined' ? type : 'post';
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

function wpsp_get_terms( type ) {
	type = typeof type !== 'undefined' ? type : 'post';
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

function wpsp_get_option( key ) {
	key = typeof key !== 'undefined' ? key : 'wpsp_taxonomy';
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

	// Show any selected terms
	var terms = wpsp_get_terms( $( '#wpsp-taxonomy' ).val() );
	var term_values = wpsp_get_option( 'wpsp_tax_term' );

	$.each(terms, function(key, value) {
		if ( null !== value ) {
			if ( $.isArray( term_values ) ) {
				var checked = ( $.inArray( value, term_values ) > -1 ) ? 'checked="checked"' : '';
			} else {
				var checked = ( value === term_values ) ? 'checked="checked"' : '';
			}

			$('#butterbean-control-wpsp_tax_term .butterbean-checkbox-list').append( $( '<li><label><input ' + checked + ' type="checkbox" value="' + value + '" name="butterbean_wp_show_posts_setting_wpsp_tax_term[]" />' + value + '</label></li>' ) );
		}
	});

	// Hide the terms of taxonomy is empty on load
	if ( '' == $( '#wpsp-taxonomy' ).val() ) {
		$( '#butterbean-control-wpsp_tax_term' ).hide();
	}

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

		// Empty the list of terms
		$( '#butterbean-control-wpsp_tax_term .butterbean-checkbox-list' ).empty();

		// Get any selected terms
		var selectValues = wpsp_get_terms( $(this).val() );

		// For each selected term, add the checkbox
		$.each(selectValues, function(key, value) {
			if ( null !== value ) {
				$('#butterbean-control-wpsp_tax_term .butterbean-checkbox-list').append( $( '<li><label><input type="checkbox" value="' + value + '" name="butterbean_wp_show_posts_setting_wpsp_tax_term[]" />' + value + '</label></li>' ) );
			}
		});

		// Hide the terms area if we don't have any terms
		if ( '' == selectValues || ',' == selectValues ) {
			$( '#butterbean-control-wpsp_tax_term' ).hide();
		} else {
			$( '#butterbean-control-wpsp_tax_term' ).show();
		}
	});

	// Fix color label bug introduced in WP 4.9.
	$( '.butterbean-control-color' ).each( function() {
		var _this = $( this );
		_this.find( '.wp-picker-input-wrap.hidden .butterbean-label' ).prependTo( _this );
	} );

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

	// Title
	if ( ! $( '#wpsp-include-title' ).is( ':checked' ) ) {
		$( '#butterbean-control-wpsp_title_element' ).hide();
	}

	$( '#wpsp-include-title' ).change(function() {
		if ( ! this.checked ) {
			$( '#butterbean-control-wpsp_title_element' ).hide();
		} else {
			$( '#butterbean-control-wpsp_title_element' ).show();
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

	// Comments link location
	if ( ! $( '#wpsp-include-comments-link' ).is( ':checked' ) ) {
		$( '#butterbean-control-wpsp_comments_location' ).hide();
	}

	$( '#wpsp-include-comments-link' ).change(function() {
		if ( ! this.checked ) {
			$( '#butterbean-control-wpsp_comments_location' ).hide();
		} else {
			$( '#butterbean-control-wpsp_comments_location' ).show();
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
