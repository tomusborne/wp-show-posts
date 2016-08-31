<?php
/*
Plugin Name: WP Show Posts
Plugin URI: https://wpshowposts.com
Description: WP Show Posts allows you to list posts (from any post type) anywhere on your site. This includes WooCommerce products or any other post type you might have! Check out the pro version for even more features at https://wpshowposts.com.
Version: 0.6
Author: Tom Usborne
Author URI: https://tomusborne.com
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: wp-show-posts
*/

// Define the current version
define( 'WPSP_VERSION', 0.5 );

// Add defaults
require_once trailingslashit( plugin_dir_path( __FILE__ ) ) . 'inc/defaults.php';

// Add post type
require_once trailingslashit( plugin_dir_path( __FILE__ ) ) . 'admin/post-type.php';

// Add admin metabox
require_once trailingslashit( plugin_dir_path( __FILE__ ) ) . 'admin/metabox.php';

// Add admin AJAX
require_once trailingslashit( plugin_dir_path( __FILE__ ) ) . 'admin/ajax.php';

// Add resizer script
if ( ! class_exists( 'WPSP_Resize' ) ) require_once plugin_dir_path( __FILE__ ) . 'inc/image-resizer.php';

// Add functions
require_once trailingslashit( plugin_dir_path( __FILE__ ) ) . 'inc/functions.php';

// Add admin functions
require_once trailingslashit( plugin_dir_path( __FILE__ ) ) . 'admin/admin.php';

// Add widget
require_once trailingslashit( plugin_dir_path( __FILE__ ) ) . 'admin/widget.php';

if ( ! function_exists( 'wpsp_load_textdomain' ) ) :
/**
 * Load plugin textdomain.
 *
 * @since 0.5
 */
add_action( 'plugins_loaded', 'wpsp_load_textdomain' );
function wpsp_load_textdomain() 
{
	load_plugin_textdomain( 'wp-show-posts' ); 
}
endif;

if ( ! function_exists( 'wpsp_get_min_suffix' ) ) :
/** 
 * Figure out if we should use minified scripts or not
 * @since 0.1
 */
function wpsp_get_min_suffix() 
{
	return defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '-min';
}
endif;

if ( ! function_exists( 'wpsp_enqueue_scripts' ) ) :
/*
 * Enqueue our CSS to the front end
 * @since 0.1
 */
add_action( 'wp_enqueue_scripts', 'wpsp_enqueue_scripts' );
function wpsp_enqueue_scripts() 
{
	$suffix = wpsp_get_min_suffix();
	wp_enqueue_style( 'wp-show-posts', plugins_url( "css/wp-show-posts{$suffix}.css", __FILE__ ), array(), WPSP_VERSION );
}
endif;

if ( ! function_exists( 'wpsp_get_setting' ) ) :
/*
 * Create a helpful wrapper to get our settings and defaults
 * @since 0.1
 */
function wpsp_get_setting( $id, $key )
{
	$defaults = wpsp_get_defaults();
	return get_post_meta( $id, $key ) ? get_post_meta( $id, $key, true ) : $defaults[ $key ];
}
endif;

if ( ! function_exists( 'wpsp_display' ) ) :
/*
 * Build the front end of the plugin
 * $id parameter needs to match ID of custom post type entry
 * @since 0.1
 */
function wpsp_display( $id ) 
{
	// Set the global ID of our object
	global $wpsp_id;
	$wpsp_id = $id;
	
	// Build our setting variables
	$author              	= sanitize_text_field( wpsp_get_setting( $id, 'wpsp_author' ) );
	$columns     			= sanitize_text_field( wpsp_get_setting( $id, 'wpsp_columns' ) );
	$columns_gutter      	= sanitize_text_field( wpsp_get_setting( $id, 'wpsp_columns_gutter' ) );
	$content_type        	= sanitize_text_field( wpsp_get_setting( $id, 'wpsp_content_type' ) );
	$exclude_current     	= wp_validate_boolean( wpsp_get_setting( $id, 'wpsp_exclude_current' ) );
	$excerpt_length      	= absint( wpsp_get_setting( $id, 'wpsp_excerpt_length' ) );
	$post_id      		 	= sanitize_text_field( wpsp_get_setting( $id, 'wpsp_post_id' ) );
	$exclude_post_id      	= sanitize_text_field( wpsp_get_setting( $id, 'wpsp_exclude_post_id' ) );
	$ignore_sticky_posts 	= wp_validate_boolean( wpsp_get_setting( $id, 'wpsp_ignore_sticky_posts' ) );
	$image_gallery      	= sanitize_text_field( wpsp_get_setting( $id, 'wpsp_image_gallery' ) );
	$image_lightbox      	= sanitize_text_field( wpsp_get_setting( $id, 'wpsp_image_lightbox' ) );
	$include_title 		 	= wp_validate_boolean( get_post_meta( $id, 'wpsp_include_title', true ) );
	$author_location     	= sanitize_text_field( wpsp_get_setting( $id, 'wpsp_author_location' ) );
	$date_location       	= sanitize_text_field( wpsp_get_setting( $id, 'wpsp_date_location' ) );
	$terms_location		 	= sanitize_text_field( wpsp_get_setting( $id, 'wpsp_terms_location' ) );
	$include_author 	 	= wp_validate_boolean( wpsp_get_setting( $id, 'wpsp_include_author' ) );
	$include_terms 	     	= wp_validate_boolean( wpsp_get_setting( $id, 'wpsp_include_terms' ) );
	$include_date 	     	= wp_validate_boolean( get_post_meta( $id, 'wpsp_include_date', true ) );
	$inner_wrapper       	= sanitize_text_field( wpsp_get_setting( $id, 'wpsp_inner_wrapper' ) );
	$inner_wrapper_class 	= array_map( 'sanitize_html_class', ( explode( ' ', wpsp_get_setting( $id, 'wpsp_inner_wrapper_class' ) ) ) );
	$inner_wrapper_style 	= explode( ' ', esc_attr( wpsp_get_setting( $id, 'wpsp_inner_wrapper_style' ) ) );
	$itemtype				= sanitize_text_field( wpsp_get_setting( $id, 'wpsp_itemtype' ) );
	$meta_key   	     	= sanitize_text_field( wpsp_get_setting( $id, 'wpsp_meta_key' ) );
	$meta_value   	     	= sanitize_text_field( wpsp_get_setting( $id, 'wpsp_meta_value' ) );
	$offset   			 	= absint( wpsp_get_setting( $id, 'wpsp_offset' ) );
	$order   			 	= sanitize_key( wpsp_get_setting( $id, 'wpsp_order' ) );
	$orderby   			 	= sanitize_key( wpsp_get_setting( $id, 'wpsp_orderby' ) );
	$pagination				= wp_validate_boolean( wpsp_get_setting( $id, 'wpsp_pagination' ) );
	$post_type			 	= sanitize_text_field( wpsp_get_setting( $id, 'wpsp_post_type' ) );
	$post_status		 	= wpsp_get_setting( $id, 'wpsp_post_status' ); // Validated later
	$posts_per_page		 	= intval( wpsp_get_setting( $id, 'wpsp_posts_per_page' ) );
	$tax_operator		 	= wpsp_get_setting( $id, 'wpsp_tax_operator' ); // Validated later
	$tax_term		 	 	= sanitize_text_field( wpsp_get_setting( $id, 'wpsp_tax_term' ) );
	$taxonomy		 	 	= sanitize_key( wpsp_get_setting( $id, 'wpsp_taxonomy' ) );
	$wrapper			 	= sanitize_text_field( wpsp_get_setting( $id, 'wpsp_wrapper' ) );
	$wrapper_id			 	= sanitize_html_class( wpsp_get_setting( $id, 'wpsp_wrapper_id' ) );
	$wrapper_class 	 	 	= array_map( 'sanitize_html_class', ( explode( ' ', wpsp_get_setting( $id, 'wpsp_wrapper_class' ) ) ) );
	$wrapper_style 		 	= explode( ' ', esc_attr( wpsp_get_setting( $id, 'wpsp_wrapper_style' ) ) );
	$no_results 		 	= wp_kses_post( wpsp_get_setting( $id, 'wpsp_no_results' ) );
	$ajax_pagination 		= wp_validate_boolean( wpsp_get_setting( $id, 'wpsp_ajax_pagination' ) );
	$masonry 	     		= wp_validate_boolean( wpsp_get_setting( $id, 'wpsp_masonry' ) );
	$featured_post   		= wp_validate_boolean( wpsp_get_setting( $id, 'wpsp_featured_post' ) );
	$border					= wpsp_sanitize_hex_color( wpsp_get_setting( $id, 'wpsp_border' ) );
	$padding				= sanitize_text_field( wpsp_get_setting( $id, 'wpsp_padding' ) );
	$filter					= wp_validate_boolean( wpsp_get_setting( $id, 'wpsp_filter' ) );
	
	// Grab initiate args for query
	$args = array();
	
	if ( '' !== $order )
		$args[ 'order' ] = $order;
	
	if ( '' !== $orderby )
		$args[ 'orderby' ] = $orderby;
	
	if ( '' !== $post_type )
		$args[ 'post_type' ] = $post_type;
	
	if ( '' !== $posts_per_page )
		$args[ 'posts_per_page' ] = $posts_per_page;
	
	if ( $ignore_sticky_posts )
		$args[ 'ignore_sticky_posts' ] = $ignore_sticky_posts;
	
	if ( '' !== $meta_key )
		$args[ 'meta_key' ] = $meta_key;
	
	if ( '' !== $meta_value )
		$args[ 'meta_value' ] = $meta_value;
	
	if ( $offset > 0 )
		$args[ 'offset' ] = $offset;
	
	if ( '' !== $author )
		$args[ 'author' ] = array( $author );
	
	if ( $pagination && ! is_single() ) :
		$paged_query = is_front_page() ? 'page' : 'paged';
		$args[ 'paged' ] = get_query_var( $paged_query );
	endif;
	
	// Post Status	
	$post_status = explode( ', ', $post_status );		
	$validated = array();
	$available = array( 'publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash', 'any' );
	
	foreach ( $post_status as $unvalidated )
		if ( in_array( $unvalidated, $available ) )
			$validated[] = $unvalidated;
		
	if( !empty( $validated ) )		
		$args['post_status'] = $validated;
	
	// If taxonomy attributes, create a taxonomy query
	if ( !empty( $taxonomy ) && !empty( $tax_term ) ) {
	
		if ( 'current' == $tax_term ) {
			global $post;
			$terms = wp_get_post_terms(get_the_ID(), $taxonomy);
			$tax_term = array();
			foreach ($terms as $term) {
				$tax_term[] = $term->slug;
			}
		} else {
			// Term string to array
			$tax_term = explode( ', ', $tax_term );
		}
		
		// Validate operator
		if( !in_array( $tax_operator, array( 'IN', 'NOT IN', 'AND' ) ) )
			$tax_operator = 'IN';
					
		$tax_args = array(
			'tax_query' => array(
				array(
					'taxonomy' => $taxonomy,
					'field'    => 'slug',
					'terms'    => $tax_term,
					'operator' => $tax_operator
				)
			)
		);
		
		$args = array_merge( $args, $tax_args );
	}
	
	// If Post IDs
	if( $post_id ) {
		$posts_in = array_map( 'intval', explode( ',', $post_id ) );
		$args['post__in'] = $posts_in;
	}
	
	// If Exclude Post IDs
	if( $exclude_post_id ) {
		$posts_not_in = array_map( 'intval', explode( ',', $exclude_post_id ) );
		$args['post__not_in'] = $posts_not_in;
	}
	
	// If Exclude Current
	if( is_singular() && $exclude_current )
		$args['post__not_in'] = array( get_the_ID() );
	
	// Border
	if ( '' !== $border ) {
		$wrapper_class[] = 'include-border';
		$border = 'border-color: ' . $border . ';';
	}
	
	// Padding
	if ( '' !== $padding ) {
		$wrapper_class[] = 'include-padding';
		$padding = 'padding:' . $padding . ';';
	}
	
	// Columns
	if ( $columns !== 'col-12' ) :
		wp_enqueue_script( 'wpsp-matchHeight', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js/jquery.matchHeight.js', array( 'jquery' ), WPSP_VERSION, true );
		$wrapper_class[] = 'wp-show-posts-columns';
		$wrapper_style[] = 'margin-left:-' . $columns_gutter . ';';
		$inner_wrapper_style[] = 'margin: 0 0 ' . $columns_gutter . ' ' . $columns_gutter . ';' . $border . $padding;
	endif;

	// Featured post class
	$current_post = '';
	if ( $columns !== 'col-12' && $featured_post ) :
		if ( $columns == 'col-6' )
			$current_post = 'col-12';

		if ( $columns == 'col-4' )
			$current_post = 'col-8';

		if ( $columns == 'col-3' )
			$current_post = 'col-6';

		if ( $columns == 'col-20' )
			$current_post = 'col-6';
	endif;

	// Masonry
	if ( $masonry ) :
		$wrapper_class[] = 'wp-show-posts-masonry';
		$inner_wrapper_class[] = ' wp-show-posts-masonry-' . $columns;
		$inner_wrapper_class[] = ' wp-show-posts-masonry-block';

		wp_enqueue_script( 'wpsp-imagesloaded' );
		wp_enqueue_script( 'jquery-masonry' );
		wp_add_inline_script( 'jquery-masonry', 'jQuery(function($){var $container = $(".wp-show-posts-masonry");$container.imagesLoaded( function(){$container.fadeIn( 1000 ).masonry({itemSelector : ".wp-show-posts-masonry-block",columnWidth: ".grid-sizer"}).css("opacity","1");});});' );
	endif;
	
	// Filter
	// if ( $filter ) :
		// wp_enqueue_script( 'wpsp-imagesloaded' );
		// wp_enqueue_script( 'wpsp-filterizr' );
		// wp_add_inline_script( 'wpsp-filterizr', 'jQuery(function($){ var $filterizd = $( ".wp-show-posts" );$filterizd.imagesLoaded( function(){ $( ".wp-show-posts" ).filterizr("setOptions", {layout: "sameWidth"}); } )});' );
		// $inner_wrapper_class[] = 'filtr-item';
	// endif;

	// Add the default inner wrapper class
	// We don't create the class element up here like below, as we need to add classes inside the loop below as well
	$inner_wrapper_class[] = 'wp-show-posts-single';
	
	if ( 'col-12' == $columns )
		$inner_wrapper_class[] = 'wpsp-clearfix';

	// Add the default wrapper class
	$wrapper_class[] = 'wp-show-posts';

	// Get the wrapper class
	if( !empty( $wrapper_class ) )
		$wrapper_class = ' class="' . implode( ' ', $wrapper_class ) . '"';

	// Get the wrapper style
	if( !empty( $wrapper_style ) )
		$wrapper_style = ' style="' . implode( ' ', $wrapper_style ) . '"';

	// Get the inner wrapper class
	if( !empty( $inner_wrapper_style ) )
		$inner_wrapper_style = ' style="' . implode( ' ', $inner_wrapper_style ) . '"';

	// Get the wrapper ID
	if( !empty( $wrapper_id ) )
		$wrapper_id = ' id="' . $wrapper_id . '"';
	
	$wrapper_atts = apply_filters( 'wpsp_wrapper_atts', '' );

	do_action( 'wpsp_before_wrapper' );
	
	// if ( $filter ) :
		// $post_terms = get_terms( sanitize_text_field( $taxonomy ), 'orderby=count&hide_empty=1' );
		// echo '<ul>';
			// foreach ( $post_terms as $term ) {
				// echo '<li data-filter="' . $term->term_id . '">' . $term->name . '</li>';
			// }
		// echo '</ul>';
	// endif;
	
	// Start the wrapper
	echo '<' . $wrapper . $wrapper_id . $wrapper_class . $wrapper_style . $wrapper_atts . '>';

	if ( $masonry )
		echo '<div class="grid-sizer ' . $columns . '"></div>';

	// Start the query
	$query = new WP_Query( apply_filters( 'wp_show_posts_shortcode_args', $args ) );

	// Start the loop
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			
			// Get page			
			$paged_query = is_front_page() ? 'page' : 'paged';
			$paged = ( get_query_var( $paged_query ) ) ? get_query_var( $paged_query ) : 1;
			
			$featured = '';
			$column_class = '';
			// Featured post
			if ( $columns !== 'col-12' && $featured_post ) :
				if ( $query->current_post == 0 && $paged == 1 ) {
					$featured = ' featured-column ' . $current_post;
				} else {
					$featured = ' ' . $columns;
				}
			elseif ( $columns !== 'col-12' ) :
				$column_class .= ' ' . $columns;
			endif;
			
			$terms_list = '';
			// if ( $filter ) :
				// $terms_list = wp_get_post_terms( get_the_ID(), $taxonomy );
				// $output_terms = array();
				// foreach ( $terms_list as $term ) {
					// $output_terms[] = $term->term_id;
				// }
				// $terms_list = 'data-category="' . implode( ', ', $output_terms ) . '"';
			// endif;
			
			// Start inner container
			echo '<' . $inner_wrapper . ' class="' . implode( ' ', $inner_wrapper_class ) . $column_class . $featured . '" itemtype="http://schema.org/' . $itemtype . '" itemscope ' . $terms_list . '>';
				echo '<div class="wp-show-posts-inner"' . $inner_wrapper_style . '>';
					
					do_action( 'wpsp_before_header' );
						
					// The title
					if ( $include_title || ( $include_author && 'below-title' == $author_location ) || ( $include_date && 'below-title' == $date_location ) || ( $include_terms && 'below-title' == $terms_location ) ) : ?>
						<header class="wp-show-posts-entry-header">
							<?php 
							
							do_action( 'wpsp_before_title' );
							
							if ( $include_title ) 
								the_title( sprintf( '<h2 class="wp-show-posts-entry-title" itemprop="headline"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); 
							
							do_action( 'wpsp_after_title' );
							?>
						</header><!-- .entry-header -->
					<?php endif;
					
					do_action( 'wpsp_before_content' );
					
					// The excerpt or full content
					if ( 'excerpt' == $content_type && $excerpt_length ) : ?>
						<div class="wp-show-posts-entry-summary" itemprop="text">
							<?php wpsp_excerpt( '', $excerpt_length ); ?>
						</div><!-- .entry-summary -->
					<?php elseif ( 'full' == $content_type ) : ?>
						<div class="wp-show-posts-entry-content" itemprop="text">
							<?php the_content( false, false ); ?>
						</div><!-- .entry-content -->
					<?php endif;
					
					do_action( 'wpsp_after_content' );
					
				echo '</div><!-- wp-show-posts-inner -->';
				if ( 'col-12' == $columns ) echo '<div class="wpsp-clear"></div>';
			// End inner container
			echo '</' . $inner_wrapper . '>';
		}
	} else {
		// no posts found
		echo ( $columns !== 'col-12' ) ? '<div class="wpsp-no-results" style="margin-left: ' . $columns_gutter . ';">' : '';
			echo wpautop( $no_results );
		echo ( $columns !== 'col-12' ) ? '</div>' : '';
	}
		if ( $columns !== 'col-12' ) echo '<div class="wpsp-clear"></div>';
	echo '</' . $wrapper . '><!-- .wp-show-posts -->';

	do_action( 'wpsp_after_wrapper' );
	
	// Pagination
	if ( $pagination && $query->have_posts() && ! is_single() ) :
		if ( $ajax_pagination && function_exists( 'wpsp_ajax_pagination' ) ) :
			
			$max_page = $query->max_num_pages;
			$nextpage = intval( $paged ) + 1;

			if ( $nextpage <= $max_page )
				$next_page_url = next_posts( $max_page, false );
			
			wpsp_ajax_pagination( $next_page_url, $paged, $max_page );
			wp_enqueue_script( 'wpsp-imagesloaded' );
			wp_enqueue_script( 'wpsp-ajax-pagination' );
		else :
			wpsp_pagination( $query->max_num_pages );
		endif;
	endif;
	
	// Lightbox and gallery
	if ( $image_lightbox ) {
		wp_enqueue_script( 'wpsp-featherlight' );
		wp_enqueue_style( 'wpsp-featherlight' );
		
		if ( $image_gallery ) {
			wp_enqueue_script( 'wpsp-featherlight-gallery' );
			wp_enqueue_style( 'wpsp-featherlight-gallery' );
		}
	}

	// Restore original Post Data
	wp_reset_postdata();
}
endif;

if ( ! function_exists( 'wpsp_shortcode_function' ) ) :
/*
 * Build the shortcode
 * @since 0.1
 */
add_shortcode( 'wp_show_posts', 'wpsp_shortcode_function' );
function wpsp_shortcode_function( $atts , $content = null ) {
	// Attributes
	$atts = shortcode_atts(
		array(
			'id' => '',
		), $atts, 'wp_show_posts'
	);
	ob_start();
	
	if ( $atts[ 'id' ] )
		wpsp_display( $atts[ 'id' ] );
	
	return ob_get_clean();
}
endif;