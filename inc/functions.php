<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Set the excerpt length.
 *
 * @since 1.1
 */
function wpsp_excerpt_length() {
	global $wpsp_id;
	return absint( wpsp_get_setting( $wpsp_id, 'wpsp_excerpt_length' ) );
}

/**
 * Set the read more ellipses.
 *
 * @since 1.1
 */
function wpsp_excerpt_more() {
	return apply_filters( 'wpsp_ellipses', '...' );
}

if ( ! function_exists( 'wpsp_excerpt' ) ) {
	/**
	 * Build our excerpt
	 * @since 0.9
	 */
	function wpsp_excerpt() {
		add_filter( 'excerpt_length', 'wpsp_excerpt_length', 999 );
		add_filter( 'excerpt_more', 'wpsp_excerpt_more', 999 );
		the_excerpt();
		remove_filter( 'excerpt_length', 'wpsp_excerpt_length', 999 );
		remove_filter( 'excerpt_more', 'wpsp_excerpt_more', 999 );
	}
}

if ( ! function_exists( 'wpsp_meta' ) ) {
	/**
	 * Build our post meta.
	 *
	 * @since 0.1
	 */
	function wpsp_meta( $location, $settings ) {
		$output = array();

		if ( 'below-post' == $location ) {
			$post_meta_style = $settings[ 'post_meta_bottom_style' ];
		} elseif ( 'below-title' == $location )  {
			$post_meta_style = $settings[ 'post_meta_top_style' ];
		}

		if ( ( $settings[ 'include_author' ] && $location == $settings[ 'author_location' ] ) || ( $settings[ 'include_date' ] && $location == $settings[ 'date_location' ] ) || ( $settings[ 'include_terms' ] && $location == $settings[ 'terms_location' ] ) || ( $settings[ 'include_comments' ] && $location == $settings[ 'comments_location' ] ) ) {
			echo '<div class="wp-show-posts-entry-meta wp-show-posts-entry-meta-' . $location . ' post-meta-' . esc_attr( $post_meta_style ) . '">';
		}

		// If our author is enabled, show it
		if ( $settings[ 'include_author' ] && $location == $settings[ 'author_location' ] ) {
			$output[] = apply_filters( 'wpsp_author_output', sprintf(
				'<span class="wp-show-posts-byline wp-show-posts-meta">
					<span class="wp-show-posts-author vcard" itemtype="http://schema.org/Person" itemscope="itemscope" itemprop="author">
						<a class="url fn n" href="%1$s" title="%2$s" rel="author" itemprop="url">
							<span class="author-name" itemprop="name">%3$s</span>
						</a>
					</span>
				</span>',
				esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
				esc_attr( sprintf( __( 'View all posts by %s', 'wp-show-posts' ), get_the_author() ) ),
				esc_html( get_the_author() )
			) );
		}

		// Show the date
		if ( $settings[ 'include_date' ] && $location == $settings[ 'date_location' ] ) {
			$time_string = '<time class="wp-show-posts-entry-date published" datetime="%1$s" itemprop="datePublished">%2$s</time>';

			if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
				$time_string .= '<time class="wp-show-posts-updated" datetime="%3$s" itemprop="dateModified">%4$s</time>';
			}

			$time_string = sprintf( $time_string,
				esc_attr( get_the_date( 'c' ) ),
				esc_html( get_the_date() ),
				esc_attr( get_the_modified_date( 'c' ) ),
				esc_html( get_the_modified_date() )
			);

			// If our date is enabled, show it
			$output[] = apply_filters( 'wpsp_date_output', sprintf(
				'<span class="wp-show-posts-posted-on wp-show-posts-meta">
					<a href="%1$s" title="%2$s" rel="bookmark">%3$s</a>
				</span>',
				esc_url( get_permalink() ),
				esc_attr( get_the_time() ),
				$time_string
			) );
		}

		// Show the terms
		if ( $settings[ 'include_terms' ] && $location == $settings[ 'terms_location' ] ) {
			$output[] = apply_filters( 'wpsp_terms_output', sprintf( '<span class="wp-show-posts-terms wp-show-posts-meta">%1$s</span>',
				get_the_term_list( get_the_ID(), $settings[ 'taxonomy' ], '', apply_filters( 'wpsp_term_separator', ', ' ) )
			) );
		}

		if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) && ( $settings[ 'include_comments' ] && $location == $settings[ 'comments_location' ] ) ) {
				ob_start();
				echo '<span class="wp-show-posts-comments-link wp-show-posts-meta">';
					comments_popup_link( __( 'Leave a comment', 'wp-show-posts' ), __( '1 Comment', 'wp-show-posts' ), __( '% Comments', 'wp-show-posts' ) );
				echo '</span>';
				$comments_link = ob_get_clean();
				$output[] = $comments_link;
		}

		// Set up our separator
		$separator = ( 'inline' == $post_meta_style ) ? ' <span class="wp-show-posts-separator">|</span> ' : '<br />';

		// Echo our output
		echo implode( $separator, $output);

		if ( ( $settings[ 'include_author' ] && $location == $settings[ 'author_location' ] ) || ( $settings[ 'include_date' ] && $location == $settings[ 'date_location' ] ) || ( $settings[ 'include_terms' ] && $location == $settings[ 'terms_location' ] ) || ( $settings[ 'include_comments' ] && $location == $settings[ 'comments_location' ] ) ) {
			echo '</div>';
		}
	}
}

if ( ! function_exists( 'wpsp_add_post_meta_after_title' ) ) {
	add_action( 'wpsp_after_title','wpsp_add_post_meta_after_title' );
	function wpsp_add_post_meta_after_title( $settings ) {
		if ( ( $settings[ 'include_author' ] && 'below-title' == $settings[ 'author_location' ] ) || ( $settings[ 'include_date' ] && 'below-title' == $settings[ 'date_location' ] ) || ( $settings[ 'include_terms' ] && 'below-title' == $settings[ 'terms_location' ] ) || ( $settings[ 'include_comments' ] && 'below-title' == $settings[ 'comments_location' ] ) ) {
			wpsp_meta( 'below-title', $settings );
		}

	}
}

if ( ! function_exists( 'wpsp_add_post_meta_after_content' ) ) {
	add_action( 'wpsp_after_content','wpsp_add_post_meta_after_content', 10 );
	function wpsp_add_post_meta_after_content( $settings ) {
		if ( ( $settings[ 'include_author' ] && 'below-post' == $settings[ 'author_location' ] ) || ( $settings[ 'include_date' ] && 'below-post' == $settings[ 'date_location' ] ) || ( $settings[ 'include_terms' ] && 'below-post' == $settings[ 'terms_location' ] ) || ( $settings[ 'include_comments' ] && 'below-post' == $settings[ 'comments_location' ] ) ) {
			wpsp_meta( 'below-post', $settings );
		}
	}
}

if ( ! function_exists( 'wpsp_post_image' ) ) {
	/**
	 * Build our post image
	 * @since 0.1
	 */
	function wpsp_post_image( $settings ) {
		if ( ! has_post_thumbnail() ) {
			return;
		}

		if ( ! isset( $settings[ 'image' ] ) || ! $settings[ 'image' ] ) {
			return;
		}

		$image_id = get_post_thumbnail_id( get_the_ID(), 'full' );
		$image_url = wp_get_attachment_image_src( $image_id, 'full', true );
		$image_atts = wpsp_image_attributes( $image_url[1], $image_url[2], $settings[ 'image_width' ], $settings[ 'image_height' ] );

		// Set pro settings for old versions of WPSP Pro.
		if ( defined( 'WPSP_PRO_VERSION' ) && version_compare( WPSP_PRO_VERSION, '0.6', '<' ) ) {
			$settings[ 'image_overlay_color' ] = wpsp_sanitize_hex_color( wpsp_get_setting( $settings['list_id'], 'wpsp_image_overlay_color' ) );
			$settings[ 'image_overlay_icon' ] = sanitize_text_field( wpsp_get_setting( $settings['list_id'], 'wpsp_image_overlay_icon' ) );
			$hover = sanitize_text_field( wpsp_get_setting( $settings['list_id'], 'wpsp_image_hover_effect' ) );
		} else {
			$hover = ( isset( $settings[ 'image_hover_effect' ] ) && '' !== $settings[ 'image_hover_effect' ] ) ? $settings[ 'image_hover_effect' ] : '';
		}

		$disable_link = apply_filters( 'wpsp_disable_image_link', false, $settings );
		?>
		<div class="wp-show-posts-image <?php echo esc_attr( $hover . ' wpsp-image-' . $settings[ 'image_alignment' ] ); ?> ">
			<?php
			do_action( 'wpsp_inside_image_container', $settings );

			if ( ! $disable_link ) {
				printf(
					'<a href="%1$s" %2$s title="%3$s">',
					esc_url( apply_filters( 'wpsp_image_href', get_the_permalink(), $settings ) ),
					apply_filters( 'wpsp_image_data', '', $settings ),
					esc_attr( apply_filters( 'wpsp_image_title', the_title_attribute( 'echo=0' ), $settings ) )
				);
			}

				if ( ! empty( $image_atts ) ) : ?>
					<img src="<?php echo WPSP_Resize( $image_url[0], $image_atts[ 'width' ], $image_atts[ 'height' ], $image_atts[ 'crop' ], true, $image_atts[ 'upscale' ] ); ?>" alt="<?php esc_attr( the_title() ); ?>" itemprop="image" class="<?php echo esc_attr( $settings[ 'image_alignment' ] ); ?>" />
				<?php else :
					the_post_thumbnail( apply_filters( 'wpsp_default_image_size', 'full' ), array( 'itemprop' => 'image' ) );
				endif;

				if ( isset( $settings[ 'image_overlay_color' ] ) && ( '' !== $settings[ 'image_overlay_color' ] || '' !== $settings[ 'image_overlay_icon' ] ) ) {
					$color = ( $settings[ 'image_overlay_color' ] ) ? 'style="background-color:' . wpsp_hex2rgba( $settings[ 'image_overlay_color' ], apply_filters( 'wpsp_overlay_opacity', 0.7 ) ) . '"' : '';
					$icon = ( $settings[ 'image_overlay_icon' ] ) ? $settings[ 'image_overlay_icon' ] : 'no-icon';
					echo '<span class="wp-show-posts-image-overlay ' . esc_attr( $icon ) . '" ' . $color . '></span>';
				}

			if ( ! $disable_link ) {
				echo '</a>';
			}
			?>
		</div>
		<?php
	}
}

if ( ! function_exists( 'wpsp_add_post_image_before_title' ) ) {
	add_action( 'wpsp_before_header','wpsp_add_post_image_before_title' );

	function wpsp_add_post_image_before_title( $settings ) {
		if ( 'above-title' == $settings[ 'image_location' ] ) {
			wpsp_post_image( $settings );
		}
	}
}

if ( ! function_exists( 'wpsp_add_post_image_before_content' ) ) {
	add_action( 'wpsp_before_content','wpsp_add_post_image_before_content' );

	function wpsp_add_post_image_before_content( $settings ) {
		if ( 'below-title' == $settings[ 'image_location' ] ) {
			wpsp_post_image( $settings );
		}
	}
}

if ( ! function_exists( 'wpsp_read_more' ) ) {
	add_action( 'wpsp_after_content','wpsp_read_more', 5 );

	function wpsp_read_more( $settings ) {
		if ( $settings[ 'read_more_text' ] ) {
			echo apply_filters( 'wpsp_read_more_output', sprintf('<div class="wpsp-read-more"><a title="%1$s" class="%4$s" href="%2$s">%3$s</a></div>',
				the_title_attribute( 'echo=0' ),
				esc_url( get_permalink() ),
				wp_kses_post( $settings[ 'read_more_text' ] ),
				esc_attr( $settings['read_more_class'] )
			));
		}
	}
}

if ( ! function_exists( 'wpsp_hex2rgba' ) ) {
	/**
	 * Convert hex to RGBA
	 * @since 0.1
	 */
	function wpsp_hex2rgba($color, $opacity = false) {

		$default = 'rgb(0,0,0)';

		// Return default if no color provided
		if ( empty( $color ) ) {
	          return $default;
		}

		// Sanitize $color if "#" is provided
		if ($color[0] == '#' ) {
			$color = substr( $color, 1 );
		}

		// Check if color has 6 or 3 characters and get values
		if ( strlen( $color ) == 6) {
			$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
		} elseif ( strlen( $color ) == 3 ) {
			$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
		} else {
			return $default;
		}

		// Convert hexadec to rgb
		$rgb =  array_map('hexdec', $hex);

		// Check if opacity is set(rgba or rgb)
		if ( $opacity ){
			if( abs( $opacity ) > 1) {
				$opacity = 1.0;
			}
			$output = 'rgba('.implode(",",$rgb).','.$opacity.')';
		} else {
			$output = 'rgb('.implode(",",$rgb).')';
		}

		// Return rgb(a) color string
		return $output;
	}
}

if ( ! function_exists( 'wpsp_sanitize_hex_color' ) ) {
	function wpsp_sanitize_hex_color( $color ) {
	    if ( '' === $color ) {
	        return '';
		}

	    // 3 or 6 hex digits, or the empty string.
	    if ( preg_match('|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) ) {
	        return $color;
		}
	}
}

if ( ! function_exists( 'wpsp_image_attributes' ) ) {
	/**
	 * Build our image attributes
	 * @since 0.1
	 */
	function wpsp_image_attributes( $og_width = '', $og_height = '', $new_width = '', $new_height = '' ) {
		$ignore_crop = array( '', '0', '9999' );

		$image_atts = array(
			'width' => ( in_array( $new_width, $ignore_crop ) ) ? 9999 : intval( $new_width ),
			'height' => ( in_array( $new_height, $ignore_crop ) ) ? 9999 : intval( $new_height ),
			'crop' => ( in_array( $new_width, $ignore_crop ) || in_array( $new_height, $ignore_crop ) ) ? false : true
		);

		// If there's no height or width, empty the array
		if ( 9999 == $image_atts[ 'width' ] && 9999 == $image_atts[ 'height' ] ) {
			$image_atts = array();
		}

		if ( ! empty( $image_atts ) ) {
			// Is our width larger than the OG image and not proportional?
			$width_upscale = $image_atts[ 'width' ] > $og_width && $image_atts[ 'width' ] < 9999 ? true : false;

			// Is our height larger than the OG image and not proportional?
			$height_upscale = $image_atts[ 'height' ] > $og_height && $image_atts[ 'height' ] < 9999 ? true : false;

			// If both the height and width are larger than the OG image, upscale
			$image_atts[ 'upscale' ] = $width_upscale && $height_upscale ? true : false;

			// If the width is larger than the OG image and the height isn't proportional, upscale
			$image_atts[ 'upscale' ] = $width_upscale && $image_atts[ 'height' ] < 9999 ? true : $image_atts[ 'upscale' ];

			// If the height is larger than the OG image and width isn't proportional, upscale
			$image_atts[ 'upscale' ] = $height_upscale && $image_atts[ 'width' ] < 9999 ? true : $image_atts[ 'upscale' ];

			// If we're upscaling, set crop to true
			$image_atts[ 'crop' ] = $image_atts[ 'upscale' ] ? true : $image_atts[ 'crop' ];

			// If one of our sizes is upscaling but the other is proportional, show the full image
			if ( $width_upscale && $image_atts[ 'height' ] == 9999 || $height_upscale && $image_atts[ 'width' ] == 9999 ) {
				$image_atts = array();
			}
		}

		return apply_filters( 'wpsp_image_attributes', $image_atts );
	}
}

if ( ! function_exists( 'wpsp_pagination' ) ) {
	/**
	 * Build our regular pagination
	 * @since 0.1
	 */
	function wpsp_pagination( $max_num_pages ) {
		// Don't print empty markup if there's only one page.
		if ( $max_num_pages < 2 ) {
			return;
		}

		$paged_query = is_front_page() ? 'page' : 'paged';
		$paged        = get_query_var( $paged_query ) ? intval( get_query_var( $paged_query ) ) : 1;
		$pagenum_link = html_entity_decode( get_pagenum_link() );
		$query_args   = array();
		$url_parts    = explode( '?', $pagenum_link );

		if ( isset( $url_parts[1] ) ) {
			wp_parse_str( $url_parts[1], $query_args );
		}

		$pagenum_link = remove_query_arg( array_keys( $query_args ), $pagenum_link );
		$pagenum_link = trailingslashit( $pagenum_link ) . '%_%';

		$format  = $GLOBALS['wp_rewrite']->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
		$format .= $GLOBALS['wp_rewrite']->using_permalinks() ? user_trailingslashit( 'page/%#%', 'paged' ) : '?paged=%#%';

		// Set up paginated links.
		$links = paginate_links( array(
			'base'     => $pagenum_link,
			'format'   => $format,
			'total'    => $max_num_pages,
			'current'  => $paged,
			'mid_size' => apply_filters( 'wpsp_pagination_mid_size', 1 ),
			'add_args' => array_map( 'urlencode', $query_args ),
			'prev_text' => __( '&larr; Previous', 'wp-show-posts' ),
			'next_text' => __( 'Next &rarr;', 'wp-show-posts' ),
		) );

		if ( $links ) {
			echo '<div class="wpsp-load-more">' . $links . '</div>';
		}
	}
}
