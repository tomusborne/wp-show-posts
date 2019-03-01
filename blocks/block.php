<?php
/*
 * Here's a little sample plugin that shows how to easily convert an existing shortcode
 * to be a server-side rendered block. This lets you get your existing plugin functionality
 * running in the block editor as quickly as possible, you can always go back later and
 * improve the UX.
 *
 * In this case, we have an imaginary shortcode, [php_block], which accepts one argument, 'foo'.
 * This shortcode would be used like so:
 *
 * [php_block foo=abcde]
 *
 * Because the block editor uses the same function signature when doing server-side rendering, we
 * can reuse our entire shortcode logic when creating the block.
 */

add_action( 'init', 'wpsp_block_init' );
/**
 * Register our block and shortcode.
 */
function wpsp_block_init() {
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	// Register our block editor script.
	wp_register_script(
		'wpsp-block',
		plugins_url( 'wpsp-block.js', __FILE__ ),
		array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor', 'wp-i18n' )
	);

	wp_register_style(
		'wp-show-posts-block',
		plugins_url( "css/wp-show-posts.css", dirname( __FILE__ ) ),
		array(),
		WPSP_VERSION
	);

	// Register our block, and explicitly define the attributes we accept.
	register_block_type( 'wpshowposts/wpsp-display', array(
		'attributes'      => array(
			'id' => array(
				'type' => 'string',
			),
		),
		'editor_script'   => array( 'wpsp-block', 'wpsp-slick-carousel' ),
		'editor_style' => 'wp-show-posts-block',
		'render_callback' => 'wpsp_shortcode_function',
	) );

	// Define our shortcode, too, using the same render function as the block.
	add_shortcode( 'wp_show_posts', 'wpsp_shortcode_function' );
}
