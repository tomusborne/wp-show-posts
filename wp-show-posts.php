<?php
/*
Plugin Name: WP Show Posts
Plugin URI: https://wpshowposts.com
Description: WP Show Posts allows you to list posts (from any post type) anywhere on your site. This includes WooCommerce products or any other post type you might have! Check out the pro version for even more features at https://wpshowposts.com.
Version: 1.1.6
Author: Tom Usborne
Author URI: https://tomusborne.com
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: wp-show-posts
*/

// No direct access, please
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define the current version
define( 'WPSP_VERSION', '1.1.6' );

// Add resizer script
if ( ! class_exists( 'WPSP_Resize' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'inc/image-resizer.php';
}

require_once trailingslashit( plugin_dir_path( __FILE__ ) ) . 'inc/defaults.php';
require_once trailingslashit( plugin_dir_path( __FILE__ ) ) . 'inc/functions.php';
require_once trailingslashit( plugin_dir_path( __FILE__ ) ) . 'inc/compat.php';
require_once trailingslashit( plugin_dir_path( __FILE__ ) ) . 'inc/styling.php';
require_once trailingslashit( plugin_dir_path( __FILE__ ) ) . 'inc/deprecated.php';
require_once trailingslashit( plugin_dir_path( __FILE__ ) ) . 'admin/post-type.php';
require_once trailingslashit( plugin_dir_path( __FILE__ ) ) . 'admin/metabox.php';
require_once trailingslashit( plugin_dir_path( __FILE__ ) ) . 'admin/ajax.php';
require_once trailingslashit( plugin_dir_path( __FILE__ ) ) . 'admin/admin.php';
require_once trailingslashit( plugin_dir_path( __FILE__ ) ) . 'admin/widget.php';

add_action( 'plugins_loaded', 'wpsp_load_textdomain' );
/**
 * Load plugin textdomain.
 *
 * @since 0.5
 */
function wpsp_load_textdomain() {
	load_plugin_textdomain( 'wp-show-posts' );
}

add_action( 'wp_enqueue_scripts', 'wpsp_enqueue_scripts' );
/*
 * Enqueue our CSS to the front end
 *
 * @since 0.1
 */
function wpsp_enqueue_scripts() {
	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '-min';
	wp_enqueue_style( 'wp-show-posts', plugins_url( "css/wp-show-posts{$suffix}.css", __FILE__ ), array(), WPSP_VERSION );
}

/*
 * Create a helpful wrapper to get our settings and defaults
 *
 * @since 0.1
 *
 * @param int $id The post ID.
 * @param string $key The post meta key.
 * @return mixed The value of our key.
 */
function wpsp_get_setting( $id, $key ) {
	// Get our defaults
	$defaults = wpsp_get_defaults();

	// Bail if our default isn't set
	if ( ! isset( $defaults[ $key ] ) ) {
		return false;
	}

	// If we have a default, let's return a value
	return get_post_meta( $id, $key ) ? get_post_meta( $id, $key, true ) : $defaults[ $key ];
}

/**
 * Remove special characters from a string.
 *
 * @param string $string The string to remove special characters from.
 */
function wpsp_clean_string( $string ) {
	$string = str_replace( ' ', '-', $string );
	$string = preg_replace( '/[^A-Za-z0-9\-]/', '', $string );
	$string = str_replace( 'script', 'div', $string );

	return $string;
}

/*
 * Build the front end of the plugin
 * $id parameter needs to match ID of custom post type entry
 *
 * @since 0.1
 *
 * @param int $id The ID of the post.
 * @param string|array $custom_settings Custom settings we can pass to our list.
 */
function wpsp_display( $id, $custom_settings = false ) {
	// Set the global ID of our object
	global $wpsp_id;
	$wpsp_id = $id;

	// Build our setting variables
	$settings = apply_filters( 'wpsp_settings', array(
		'list_id'				 => absint( $id ),
		'author' 				 => sanitize_text_field( wpsp_get_setting( $id, 'wpsp_author' ) ),
		'columns'     			 => sanitize_text_field( wpsp_get_setting( $id, 'wpsp_columns' ) ),
		'columns_gutter'      	 => sanitize_text_field( wpsp_get_setting( $id, 'wpsp_columns_gutter' ) ),
		'content_type'        	 => sanitize_text_field( wpsp_get_setting( $id, 'wpsp_content_type' ) ),
		'exclude_current'     	 => wp_validate_boolean( wpsp_get_setting( $id, 'wpsp_exclude_current' ) ),
		'excerpt_length'      	 => absint( wpsp_get_setting( $id, 'wpsp_excerpt_length' ) ),
		'post_id'      		 	 => sanitize_text_field( wpsp_get_setting( $id, 'wpsp_post_id' ) ),
		'exclude_post_id'      	 => sanitize_text_field( wpsp_get_setting( $id, 'wpsp_exclude_post_id' ) ),
		'ignore_sticky_posts' 	 => wp_validate_boolean( wpsp_get_setting( $id, 'wpsp_ignore_sticky_posts' ) ),
		'include_title' 		 => wp_validate_boolean( get_post_meta( $id, 'wpsp_include_title', true ) ),
		'title_element'			 => sanitize_text_field( wpsp_get_setting( $id, 'wpsp_title_element' ) ),
		'image'					 => sanitize_text_field( get_post_meta( $id, 'wpsp_image', true ), FILTER_VALIDATE_BOOLEAN ),
		'image_location'		 => sanitize_text_field( wpsp_get_setting( $id, 'wpsp_image_location' ) ),
		'image_alignment'		 => sanitize_text_field( wpsp_get_setting( $id, 'wpsp_image_alignment' ) ),
		'image_height'			 => absint( wpsp_get_setting( $id, 'wpsp_image_height' ) ),
		'image_width'			 => absint( wpsp_get_setting( $id, 'wpsp_image_width' ) ),
		'include_author' 	 	 => wp_validate_boolean( wpsp_get_setting( $id, 'wpsp_include_author' ) ),
		'author_location'     	 => sanitize_text_field( wpsp_get_setting( $id, 'wpsp_author_location' ) ),
		'include_terms' 	     => wp_validate_boolean( wpsp_get_setting( $id, 'wpsp_include_terms' ) ),
		'terms_location'		 => sanitize_text_field( wpsp_get_setting( $id, 'wpsp_terms_location' ) ),
		'include_date' 	     	 => wp_validate_boolean( get_post_meta( $id, 'wpsp_include_date', true ) ),
		'date_location'       	 => sanitize_text_field( wpsp_get_setting( $id, 'wpsp_date_location' ) ),
		'include_comments' 	     => wp_validate_boolean( get_post_meta( $id, 'wpsp_include_comments', true ) ),
		'comments_location'      => sanitize_text_field( wpsp_get_setting( $id, 'wpsp_comments_location' ) ),
		'inner_wrapper'       	 => sanitize_text_field( wpsp_get_setting( $id, 'wpsp_inner_wrapper' ) ),
		'inner_wrapper_class' 	 => array_map( 'sanitize_html_class', ( explode( ' ', wpsp_get_setting( $id, 'wpsp_inner_wrapper_class' ) ) ) ),
		'inner_wrapper_style' 	 => explode( ' ', esc_attr( wpsp_get_setting( $id, 'wpsp_inner_wrapper_style' ) ) ),
		'itemtype'				 => sanitize_text_field( wpsp_get_setting( $id, 'wpsp_itemtype' ) ),
		'meta_key'   	     	 => sanitize_text_field( wpsp_get_setting( $id, 'wpsp_meta_key' ) ),
		'meta_value'   	     	 => sanitize_text_field( wpsp_get_setting( $id, 'wpsp_meta_value' ) ),
		'offset'   			 	 => absint( wpsp_get_setting( $id, 'wpsp_offset' ) ),
		'order'   			 	 => sanitize_key( wpsp_get_setting( $id, 'wpsp_order' ) ),
		'orderby'  			 	 => sanitize_key( wpsp_get_setting( $id, 'wpsp_orderby' ) ),
		'pagination'			 => wp_validate_boolean( wpsp_get_setting( $id, 'wpsp_pagination' ) ),
		'post_type'			 	 => sanitize_text_field( wpsp_get_setting( $id, 'wpsp_post_type' ) ),
		'post_status'		 	 => wpsp_get_setting( $id, 'wpsp_post_status' ), // Validated later
		'posts_per_page'		 => intval( wpsp_get_setting( $id, 'wpsp_posts_per_page' ) ),
		'tax_operator'		 	 => wpsp_get_setting( $id, 'wpsp_tax_operator' ), // Validated later
		'tax_term'		 	 	 => wpsp_get_setting( $id, 'wpsp_tax_term' ),
		'taxonomy'		 	 	 => sanitize_key( wpsp_get_setting( $id, 'wpsp_taxonomy' ) ),
		'read_more_text'		 => wp_kses_post( wpsp_get_setting( $id, 'wpsp_read_more_text' ) ),
		'wrapper'			 	 => sanitize_text_field( wpsp_get_setting( $id, 'wpsp_wrapper' ) ),
		'wrapper_class' 	 	 => array_map( 'sanitize_html_class', ( explode( ' ', wpsp_get_setting( $id, 'wpsp_wrapper_class' ) ) ) ),
		'wrapper_style' 		 => explode( ' ', esc_attr( wpsp_get_setting( $id, 'wpsp_wrapper_style' ) ) ),
		'no_results' 		 	 => wpsp_get_setting( $id, 'wpsp_no_results' ),
		'post_meta_bottom_style' => sanitize_text_field( wpsp_get_setting( $id, 'wpsp_post_meta_bottom_style' ) ),
		'post_meta_top_style' 	 => sanitize_text_field( wpsp_get_setting( $id, 'wpsp_post_meta_top_style' ) ),
		'read_more_class'	 	 => esc_attr( wpsp_get_setting( $id, 'wpsp_read_more_class' ) ),
	) );

	// Replace args with any custom args.
	if ( ! empty( $custom_settings ) ) {
		if ( is_array( $custom_settings ) ) {
			$settings = array_merge( $settings, $custom_settings );
		}

		if ( ! is_array( $custom_settings ) ) {
			$settings_string = parse_str( $custom_settings, $custom_settings );
			$settings = array_merge( $settings, $custom_settings );
		}
	}

	// Grab initiate args for query
	$args = array();

	if ( '' !== $settings[ 'order' ] ) {
		$args[ 'order' ] = esc_attr( $settings[ 'order' ] );
	}

	if ( '' !== $settings[ 'orderby' ] ) {
		$args[ 'orderby' ] = esc_attr( $settings[ 'orderby' ] );
	}

	if ( 'rand' == $settings[ 'orderby' ] && $settings[ 'pagination' ] ) {
		$args[ 'orderby' ] = 'rand(' . absint( $id ) . ')';
	}

	if ( '' !== $settings[ 'post_type' ] ) {
		$args[ 'post_type' ] = esc_attr( $settings[ 'post_type' ] );
	}

	if ( '' !== $settings[ 'posts_per_page' ] ) {
		$args[ 'posts_per_page' ] = intval( $settings[ 'posts_per_page' ] );
	}

	if ( $settings[ 'ignore_sticky_posts' ] ) {
		$args[ 'ignore_sticky_posts' ] = wp_validate_boolean( $settings[ 'ignore_sticky_posts' ] );
	}

	if ( '' !== $settings[ 'meta_key' ] ) {
		$args[ 'meta_key' ] = esc_html( $settings[ 'meta_key' ] );
	}

	if ( '' !== $settings[ 'meta_value' ] ) {
		$args[ 'meta_value' ] = esc_html( $settings[ 'meta_value' ] );
	}

	if ( $settings[ 'offset' ] > 0 ) {
		$args[ 'offset' ] = intval( $settings[ 'offset' ] );
	}

	if ( '' !== $settings[ 'author' ] ) {
		$args[ 'author' ] = esc_html( $settings[ 'author' ] );
	}

	if ( $settings[ 'pagination' ] && ! is_single() ) {
		$paged_query = is_front_page() ? 'page' : 'paged';
		$args[ 'paged' ] = get_query_var( $paged_query );
	}

	// Post Status
	$settings[ 'post_status' ] = explode( ', ', $settings[ 'post_status' ] );
	$validated = array();
	$available = array( 'publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash', 'any' );

	foreach ( $settings[ 'post_status' ] as $unvalidated ) {
		if ( in_array( $unvalidated, $available ) ) {
			$validated[] = $unvalidated;
		}
	}

	if ( ! empty( $validated ) ) {
		$args['post_status'] = $validated;
	}

	// Prevent private posts from showing up for non-authenticated users.
	if ( isset( $args['post_status'] ) && ! current_user_can( 'read_private_posts' ) ) {
		$args['post_status'] = array( 'publish' );
	}

	// If taxonomy attributes, create a taxonomy query
	if ( ! empty( $settings[ 'taxonomy' ] ) && ! empty( $settings[ 'tax_term' ] ) ) {

		if ( is_array( $settings[ 'tax_term' ] ) ) {
			$settings[ 'tax_term' ] = implode( ', ', $settings[ 'tax_term' ] );
		}

		if ( 'current' == $settings[ 'tax_term' ] ) {
			global $post;
			$terms = wp_get_post_terms(get_the_ID(), $settings[ 'taxonomy' ]);
			$settings[ 'tax_term' ] = array();
			foreach ($terms as $term) {
				$settings[ 'tax_term' ][] = $term->slug;
			}
		} else {
			// Term string to array
			$settings[ 'tax_term' ] = explode( ', ', $settings[ 'tax_term' ] );
		}

		// Validate operator
		if ( ! in_array( $settings[ 'tax_operator' ], array( 'IN', 'NOT IN', 'AND' ) ) ) {
			$settings[ 'tax_operator' ] = 'IN';
		}

		$tax_args = array(
			'tax_query' => array(
				array(
					'taxonomy' => $settings[ 'taxonomy' ],
					'field'    => 'slug',
					'terms'    => $settings[ 'tax_term' ],
					'operator' => $settings[ 'tax_operator' ]
				)
			)
		);

		$args = array_merge( $args, $tax_args );

	}

	// If Post IDs
	if ( $settings[ 'post_id' ] ) {
		$posts_in = array_map( 'intval', explode( ',', $settings[ 'post_id' ] ) );
		$args['post__in'] = $posts_in;
	}

	// If Exclude Post IDs
	if ( $settings[ 'exclude_post_id' ] ) {
		$posts_not_in = array_map( 'intval', explode( ',', $settings[ 'exclude_post_id' ] ) );
		$args['post__not_in'] = $posts_not_in;
	}

	// If Exclude Current
	if ( ( is_singular() && $settings[ 'exclude_current' ] ) || is_single() ) {
		$args['post__not_in'] = array( get_the_ID() );
	}

	// Border
	if ( defined( 'WPSP_PRO_VERSION' ) && version_compare( WPSP_PRO_VERSION, '0.6', '<' ) ) {
		$border = wpsp_sanitize_hex_color( wpsp_get_setting( $settings['list_id'], 'wpsp_border' ) );
		if ( '' !== $border ) {
			$settings['wrapper_class'][] = 'include-border';
			if ( ! function_exists( 'wpsp_styling' ) ) {
				$border = 'border-color: ' . $border . ';';
			}
		}
	}

	// Padding
	if ( defined( 'WPSP_PRO_VERSION' ) && version_compare( WPSP_PRO_VERSION, '0.6', '<' ) ) {
		$padding = sanitize_text_field( wpsp_get_setting( $settings['list_id'], 'wpsp_padding' ) );
		if ( '' !== $padding ) {
			$settings['wrapper_class'][] = 'include-padding';
			$padding = 'padding:' . $padding . ';';
		}
	}

	// Columns
	if ( 'col-12' !== $settings[ 'columns' ]  ) {
		wp_enqueue_script( 'wpsp-matchHeight', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js/jquery.matchHeight.js', array( 'jquery' ), WPSP_VERSION, true );
		$settings[ 'wrapper_class' ][] = 'wp-show-posts-columns';
	}

	// Featured post class
	$featured_post = wp_validate_boolean( wpsp_get_setting( $id, 'wpsp_featured_post' ) );

	$current_post = '';
	if ( 'col-12' !== $settings[ 'columns' ] && $featured_post ) {
		if ( $settings[ 'columns' ] == 'col-6' ) {
			$current_post = 'wpsp-col-12';
		}

		if ( $settings[ 'columns' ] == 'col-4' ) {
			$current_post = 'wpsp-col-8';
		}

		if ( $settings[ 'columns' ] == 'col-3' ) {
			$current_post = 'wpsp-col-6';
		}

		if ( $settings[ 'columns' ] == 'col-20' ) {
			$current_post = 'wpsp-col-6';
		}
	}

	// Masonry
	$masonry = wp_validate_boolean( wpsp_get_setting( $id, 'wpsp_masonry' ) );

	if ( $masonry ) {
		$settings[ 'wrapper_class' ][] = 'wp-show-posts-masonry';
		$settings[ 'inner_wrapper_class' ][] = ' wp-show-posts-masonry-' . esc_attr( $settings[ 'columns' ] );
		$settings[ 'inner_wrapper_class' ][] = ' wp-show-posts-masonry-block';

		wp_enqueue_script( 'wpsp-imagesloaded' );
		wp_enqueue_script( 'jquery-masonry' );
		wp_add_inline_script( 'jquery-masonry', 'jQuery(function($){var $container = $(".wp-show-posts-masonry");$container.imagesLoaded( function(){$container.fadeIn( 1000 ).masonry({itemSelector : ".wp-show-posts-masonry-block",columnWidth: ".grid-sizer"}).css("opacity","1");});});' );
	}

	// Add the default inner wrapper class
	// We don't create the class element up here like below, as we need to add classes inside the loop below as well
	$settings[ 'inner_wrapper_class' ][] = 'wp-show-posts-single';

	if ( 'col-12' == $settings[ 'columns' ] ) {
		$settings[ 'inner_wrapper_class' ][] = 'wpsp-clearfix';
	}

	// Add the default wrapper class
	$settings[ 'wrapper_class' ][] = 'wp-show-posts';

	// Get the wrapper class
	if ( ! empty( $settings[ 'wrapper_class' ] ) ) {
		$settings[ 'wrapper_class' ] = ' class="' . esc_attr( implode( ' ', $settings[ 'wrapper_class' ] ) ) . '"';
	}

	// Get the wrapper style
	if ( ! empty( $settings[ 'wrapper_style' ] ) ) {
		$settings[ 'wrapper_style' ] = ' style="' . esc_attr( implode( ' ', $settings[ 'wrapper_style' ] ) ) . '"';
	}

	// Get the inner wrapper class
	if ( ! empty( $settings[ 'inner_wrapper_style' ] ) ) {
		$settings[ 'inner_wrapper_style' ] = ' style="' . esc_attr( implode( ' ', $settings[ 'inner_wrapper_style' ] ) ) . '"';
	}

	$wrapper = wpsp_clean_string( $settings[ 'wrapper' ] );
	$inner_wrapper = wpsp_clean_string( $settings[ 'inner_wrapper' ] );

	// Get the wrapper ID
	$wrapper_id = ' id="wpsp-' . esc_attr( $id ) . '"';

	$wrapper_atts = apply_filters( 'wpsp_wrapper_atts', '', $settings );

	do_action( 'wpsp_before_wrapper', $settings );

	// Start the wrapper
	echo '<' . $wrapper . $wrapper_id . $settings[ 'wrapper_class' ] . $settings[ 'wrapper_style' ] . $wrapper_atts . '>';

	do_action( 'wpsp_inside_wrapper', $settings );

	if ( $masonry ) {
		echo '<div class="grid-sizer wpsp-' . esc_attr( $settings[ 'columns' ] ) . '"></div>';
	}

	// Filter kept for backward compatibility.
	$args = apply_filters( 'wp_show_posts_shortcode_args', $args, $settings );

	// Start the query.
	$query = new WP_Query( apply_filters( 'wpsp_query_args', $args, $settings ) );

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
			if ( $settings[ 'columns' ] !== 'col-12' && $featured_post ) {
				if ( $query->current_post == 0 && $paged == 1 ) {
					$featured = ' featured-column ' . $current_post;
				} else {
					$featured = ' wpsp-' . $settings[ 'columns' ];
				}
			} elseif ( $settings[ 'columns' ] !== 'col-12' ) {
				$column_class .= ' wpsp-' . $settings[ 'columns' ];
			}

			$post_classes = implode( ' ', $settings[ 'inner_wrapper_class' ] );

			// Merge our classes with the post classes.
			if ( has_filter( 'post_class', 'generate_blog_post_classes' ) ) {
				remove_filter( 'post_class', 'generate_blog_post_classes' ); // Remove GPP classes.
			}

			$post_classes .= ' ' . implode( ' ', get_post_class() );

			if ( function_exists( 'generate_blog_post_classes' ) ) {
				add_filter( 'post_class', 'generate_blog_post_classes' ); // Re-add them.
			}

			// Start inner container
			printf( '<%1$s class="%2$s" itemtype="http://schema.org/%3$s" itemscope>',
				$inner_wrapper,
				esc_attr( $post_classes . $column_class . $featured ),
				esc_attr( $settings[ 'itemtype' ] )
			);

				echo '<div class="wp-show-posts-inner"' . $settings[ 'inner_wrapper_style' ] . '>';

					do_action( 'wpsp_before_header', $settings );

					// The title
					if ( $settings[ 'include_title' ] || ( $settings[ 'include_author' ] && 'below-title' == $settings[ 'author_location' ] ) || ( $settings[ 'include_date' ] && 'below-title' == $settings[ 'date_location' ] ) || ( $settings[ 'include_terms' ] && 'below-title' == $settings[ 'terms_location' ] ) ) : ?>
						<header class="wp-show-posts-entry-header">
							<?php

							do_action( 'wpsp_before_title', $settings );

							$title_element = wpsp_clean_string( $settings[ 'title_element' ] );

							$before_title = sprintf(
								'<%1$s class="wp-show-posts-entry-title" itemprop="headline"><a href="%2$s" rel="bookmark">',
								$title_element,
								esc_url( get_permalink() )
							);

							$after_title = '</a></' . $title_element . '>';

							if ( apply_filters( 'wpsp_disable_title_link', false, $settings ) ) {
								$before_title = '<' . $title_element . ' class="wp-show-posts-entry-title" itemprop="headline">';
								$after_title = '</' . $title_element . '>';
							}

							if ( $settings[ 'include_title' ] ) {
								the_title( $before_title, $after_title );
							}

							do_action( 'wpsp_after_title', $settings );
							?>
						</header><!-- .entry-header -->
					<?php endif;

					do_action( 'wpsp_before_content', $settings );

					// Check to see if we have the more tag
					global $post;
					$more_tag = apply_filters( 'wpsp_more_tag', strpos( $post->post_content, '<!--more-->' ) );

					// The excerpt or full content
					if ( 'excerpt' == $settings[ 'content_type' ] && $settings[ 'excerpt_length' ] && ! $more_tag && 'none' !== $settings[ 'content_type' ] ) : ?>
						<div class="wp-show-posts-entry-summary" itemprop="text">
							<?php wpsp_excerpt( absint( $settings[ 'excerpt_length' ] ) ); ?>
						</div><!-- .entry-summary -->
					<?php elseif ( ( 'full' == $settings[ 'content_type' ] || $more_tag ) && 'none' !== $settings[ 'content_type' ] ) : ?>
						<div class="wp-show-posts-entry-content" itemprop="text">
							<?php the_content( false, false ); ?>
						</div><!-- .entry-content -->
					<?php endif;

					do_action( 'wpsp_after_content', $settings );

				echo '</div><!-- wp-show-posts-inner -->';

				if ( 'col-12' == $settings[ 'columns' ] ) {
					echo '<div class="wpsp-clear"></div>';
				}

			// End inner container
			echo '</' . $inner_wrapper . '>';
		}
	} else {
		// no posts found
		echo $settings[ 'columns' ] !== 'col-12' ? '<div class="wpsp-no-results" style="margin-left: ' . esc_attr( $settings[ 'columns_gutter' ] ) . ';">' : '';
			echo wpautop( wp_kses_post( $settings[ 'no_results' ] ) );
		echo $settings[ 'columns' ] !== 'col-12' ? '</div>' : '';
	}

	if ( $settings[ 'columns' ] !== 'col-12' ) {
		echo '<div class="wpsp-clear"></div>';
	}

	echo '</' . $wrapper . '><!-- .wp-show-posts -->';

	do_action( 'wpsp_after_wrapper', $settings );

	// Pagination
	if ( $settings[ 'pagination' ] && $query->have_posts() && ! is_single() ) {
		$ajax_pagination = wp_validate_boolean( wpsp_get_setting( $settings['list_id'], 'wpsp_ajax_pagination' ) );

		if ( $ajax_pagination && function_exists( 'wpsp_ajax_pagination' ) ) {

			$max_page = $query->max_num_pages;
			$nextpage = intval( $paged ) + 1;

			if ( $nextpage <= $max_page ) {
				$next_page_url = next_posts( $max_page, false );
			}

			wpsp_ajax_pagination( $next_page_url, $paged, $max_page );
			wp_enqueue_script( 'wpsp-imagesloaded' );
			wp_enqueue_script( 'wpsp-ajax-pagination' );
		} else {
			wpsp_pagination( $query->max_num_pages );
		}
	}

	if ( defined( 'WPSP_PRO_VERSION' ) && version_compare( WPSP_PRO_VERSION, '0.6', '<' ) ) {
		// Lightbox and gallery
		$image_lightbox = sanitize_text_field( wpsp_get_setting( $settings['list_id'], 'wpsp_image_lightbox' ) );
		if ( $image_lightbox ) {
			wp_enqueue_script( 'wpsp-featherlight' );
			wp_enqueue_style( 'wpsp-featherlight' );

			$image_gallery = sanitize_text_field( wpsp_get_setting( $settings['list_id'], 'wpsp_image_gallery' ) );
			if ( $image_gallery ) {
				wp_enqueue_script( 'wpsp-featherlight-gallery' );
				wp_enqueue_style( 'wpsp-featherlight-gallery' );
			}
		}
	}

	// Restore original Post Data
	wp_reset_postdata();
}

add_shortcode( 'wp_show_posts', 'wpsp_shortcode_function' );
/*
 * Build the shortcode
 *
 * @since 0.1
 *
 * @param array $atts The shortcode attributes.
 */
function wpsp_shortcode_function( $atts ) {
	$atts = shortcode_atts(
		array(
			'id' => '',
			'name' => '',
			'settings' => ''
		), $atts, 'wp_show_posts'
	);

	ob_start();

	// Get the ID from the list name if it's provided.
	if ( ! empty( $atts['name'] ) ) {
		$list = get_page_by_title( $atts['name'], 'OBJECT', 'wp_show_posts' );
		$atts['id'] = $list->ID;
	}

	if ( $atts['id'] ) {
		wpsp_display( $atts['id'], $atts['settings'] );
	}

	return ob_get_clean();
}
