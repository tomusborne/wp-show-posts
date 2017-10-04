<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! function_exists( 'wpsp_pro_defaults' ) ) {
	add_filter( 'wpsp_defaults', 'wpsp_pro_compat_defaults' );
	/**
	 * Set all of our pro defaults
	 * This will only run if wpsp_pro_defaults() doesn't exist
	 * WPSP Pro 0.5 and above won't need this
	 */
	function wpsp_pro_compat_defaults( $defaults ) {
		$defaults[ 'wpsp_image_lightbox' ] = false;
		$defaults[ 'wpsp_image_gallery' ] = false;
		$defaults[ 'wpsp_image_overlay_color' ] = '';
		$defaults[ 'wpsp_image_overlay_icon' ] = '';
		$defaults[ 'wpsp_ajax_pagination' ] = false;
		$defaults[ 'wpsp_masonry' ] = false;
		$defaults[ 'wpsp_social_sharing' ] = false;
		$defaults[ 'wpsp_social_sharing_alignment' ] = 'right';
		$defaults[ 'wpsp_twitter' ]	= false;
		$defaults[ 'wpsp_facebook' ] = false;
		$defaults[ 'wpsp_googleplus' ] = false;
		$defaults[ 'wpsp_pinterest' ] = false;
		$defaults[ 'wpsp_love' ] = false;
		$defaults[ 'wpsp_featured_post' ] = false;
		$defaults[ 'wpsp_image_hover_effect' ] = '';
		$defaults[ 'wpsp_read_more_style' ] = 'hollow';
		$defaults[ 'wpsp_read_more_color' ] = 'black';
		$defaults[ 'wpsp_border' ] = '';
		$defaults[ 'wpsp_border_hover' ] = '';
		$defaults[ 'wpsp_filter' ] = false;
		$defaults[ 'wpsp_background' ] = '';
		$defaults[ 'wpsp_background_hover' ] = '';
		$defaults[ 'wpsp_title_color' ] = '';
		$defaults[ 'wpsp_title_color_hover' ] = '';
		$defaults[ 'wpsp_meta_color' ] = '';
		$defaults[ 'wpsp_meta_color_hover' ] = '';
		$defaults[ 'wpsp_text' ] = '';
		$defaults[ 'wpsp_link' ] = '';
		$defaults[ 'wpsp_link_hover' ] = '';
		$defaults[ 'wpsp_padding' ] = '';

		return $defaults;
	}
}