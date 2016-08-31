<?php
if ( ! function_exists( 'wpsp_excerpt' ) ) :
/** 
 * Create our own excerpt function
 * @since 0.1
 */
function wpsp_excerpt( $text = '', $excerpt_length = 55 ) {
	if ( ! $excerpt_length )
		return;
	
	$raw_excerpt = $text;
	if ( '' == $text ) {
		$text = get_the_content('');

		$text = strip_shortcodes( $text );

		/** This filter is documented in wp-includes/post-template.php */
		$text = apply_filters( 'the_content', $text );
		$text = str_replace(']]>', ']]&gt;', $text);

		/**
		 * Filter the string in the "more" link displayed after a trimmed excerpt.
		 *
		 * @since 2.9.0
		 *
		 * @param string $more_string The string shown within the more link.
		 */
		$excerpt_more = apply_filters( 'wpsp_excerpt_more', '...' );
		$text = wp_trim_words( $text, $excerpt_length, $excerpt_more );
	}
	/**
	 * Filter the trimmed excerpt string.
	 *
	 * @since 2.8.0
	 *
	 * @param string $text        The trimmed text.
	 * @param string $raw_excerpt The text prior to trimming.
	 */
	echo apply_filters( 'wp_trim_excerpt', $text, $raw_excerpt );
}
endif;

if ( ! function_exists( 'wpsp_meta' ) ) :
/** 
 * Build our post meta
 * @since 0.1
 */
function wpsp_meta( $location, $post_meta_style )
{	
	global $wpsp_id;
	
	if ( ! isset( $wpsp_id ) )
		return;
	
	$author_location     	= sanitize_text_field( wpsp_get_setting( $wpsp_id, 'wpsp_author_location' ) );
	$date_location       	= sanitize_text_field( wpsp_get_setting( $wpsp_id, 'wpsp_date_location' ) );
	$include_author 	 	= filter_var( wpsp_get_setting( $wpsp_id, 'wpsp_include_author' ), FILTER_VALIDATE_BOOLEAN );
	$include_terms 	     	= filter_var( wpsp_get_setting( $wpsp_id, 'wpsp_include_terms' ), FILTER_VALIDATE_BOOLEAN );
	$include_date 	     	= filter_var( get_post_meta( $wpsp_id, 'wpsp_include_date', true ), FILTER_VALIDATE_BOOLEAN );
	$terms_location		 	= sanitize_text_field( wpsp_get_setting( $wpsp_id, 'wpsp_terms_location' ) );
	$taxonomy		 	 	= sanitize_key( wpsp_get_setting( $wpsp_id, 'wpsp_taxonomy' ) );
	
	$output = array();
	if ( ( $include_author && $location == $author_location ) || ( $include_date && $location == $date_location ) || ( $include_terms && $location == $terms_location ) )
		echo '<div class="wp-show-posts-entry-meta wp-show-posts-entry-meta-' . $location . ' post-meta-' . $post_meta_style . '">';
	
	// If our author is enabled, show it
	if ( $include_author && $location == $author_location ) :
		$output[] = sprintf( '<span class="wp-show-posts-byline wp-show-posts-meta">%1$s</span>',
			sprintf( '<span class="wp-show-posts-author vcard" itemtype="http://schema.org/Person" itemscope="itemscope" itemprop="author"><a class="url fn n" href="%1$s" title="%2$s" rel="author" itemprop="url"><span class="author-name" itemprop="name">%3$s</span></a></span>',
				esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
				esc_attr( sprintf( __( 'View all posts by %s', 'wp-show-posts' ), get_the_author() ) ),
				esc_html( get_the_author() )
			)
		);
	endif;
	
	// Show the date
	if ( $include_date && $location == $date_location ) :
		$time_string = '<time class="wp-show-posts-entry-date published" datetime="%1$s" itemprop="datePublished">%2$s</time>';
		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) )
			$time_string .= '<time class="wp-show-posts-updated" datetime="%3$s" itemprop="dateModified">%4$s</time>';

		$time_string = sprintf( $time_string,
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( 'c' ) ),
			esc_html( get_the_modified_date() )
		);
		
		// If our date is enabled, show it
		$output[] = sprintf( '<span class="wp-show-posts-posted-on wp-show-posts-meta">%1$s</span>',
			sprintf( '<a href="%1$s" title="%2$s" rel="bookmark">%3$s</a>',
				esc_url( get_permalink() ),
				esc_attr( get_the_time() ),
				$time_string
			)
		);
	endif;
	
	// Show the terms
	if ( $include_terms && $location == $terms_location ) :
		$output[] = sprintf( '<span class="wp-show-posts-terms wp-show-posts-meta">%1$s</span>',
			get_the_term_list( get_the_ID(), $taxonomy, '', ', ' )
		);
	endif;
	
	$separator = ( 'inline' == $post_meta_style ) ? ' <span class="wp-show-posts-separator">|</span> ' : '<br />';
	echo implode( $separator, $output);
	
	if ( ( $include_author && $location == $author_location ) || ( $include_date && $location == $date_location ) || ( $include_terms && $location == $terms_location ) )
		echo '</div>';
}
endif;

if ( ! function_exists( 'wpsp_add_post_meta_after_title' ) ) :
add_action( 'wpsp_after_title','wpsp_add_post_meta_after_title' );
function wpsp_add_post_meta_after_title()
{
	global $wpsp_id;
	
	if ( ! isset( $wpsp_id ) )
		return;
	
	$author_location     	= sanitize_text_field( wpsp_get_setting( $wpsp_id, 'wpsp_author_location' ) );
	$date_location       	= sanitize_text_field( wpsp_get_setting( $wpsp_id, 'wpsp_date_location' ) );
	$terms_location		 	= sanitize_text_field( wpsp_get_setting( $wpsp_id, 'wpsp_terms_location' ) );
	$include_author 	 	= filter_var( wpsp_get_setting( $wpsp_id, 'wpsp_include_author' ), FILTER_VALIDATE_BOOLEAN );
	$include_terms 	     	= filter_var( wpsp_get_setting( $wpsp_id, 'wpsp_include_terms' ), FILTER_VALIDATE_BOOLEAN );
	$include_date 	     	= filter_var( get_post_meta( $wpsp_id, 'wpsp_include_date', true ), FILTER_VALIDATE_BOOLEAN );
	$post_meta_top_style 	= sanitize_text_field( wpsp_get_setting( $wpsp_id, 'wpsp_post_meta_top_style' ) );
	
	if ( ( $include_author && 'below-title' == $author_location ) || ( $include_date && 'below-title' == $date_location ) || ( $include_terms && 'below-title' == $terms_location ) )
		wpsp_meta( 'below-title', $post_meta_top_style );
	
}
endif;

if ( ! function_exists( 'wpsp_add_post_meta_after_content' ) ) :
add_action( 'wpsp_after_content','wpsp_add_post_meta_after_content', 10 );
function wpsp_add_post_meta_after_content()
{
	global $wpsp_id;
	
	if ( ! isset( $wpsp_id ) )
		return;
	
	$author_location     	= sanitize_text_field( wpsp_get_setting( $wpsp_id, 'wpsp_author_location' ) );
	$date_location       	= sanitize_text_field( wpsp_get_setting( $wpsp_id, 'wpsp_date_location' ) );
	$terms_location		 	= sanitize_text_field( wpsp_get_setting( $wpsp_id, 'wpsp_terms_location' ) );
	$include_author 	 	= filter_var( wpsp_get_setting( $wpsp_id, 'wpsp_include_author' ), FILTER_VALIDATE_BOOLEAN );
	$include_terms 	     	= filter_var( wpsp_get_setting( $wpsp_id, 'wpsp_include_terms' ), FILTER_VALIDATE_BOOLEAN );
	$include_date 	     	= filter_var( get_post_meta( $wpsp_id, 'wpsp_include_date', true ), FILTER_VALIDATE_BOOLEAN );
	$post_meta_bottom_style = sanitize_text_field( wpsp_get_setting( $wpsp_id, 'wpsp_post_meta_bottom_style' ) );
	
	if ( ( $include_author && 'below-post' == $author_location ) || ( $include_date && 'below-post' == $date_location ) || ( $include_terms && 'below-post' == $terms_location ) )
		wpsp_meta( 'below-post', $post_meta_bottom_style );
}
endif;

if ( ! function_exists( 'wpsp_post_image' ) ) :
/** 
 * Build our post image
 * @since 0.1
 */
function wpsp_post_image()
{
	if ( ! has_post_thumbnail() )
		return;
	
	global $wpsp_id;
	
	if ( ! isset( $wpsp_id ) )
		return;
	
	$image = sanitize_text_field( get_post_meta( $wpsp_id, 'wpsp_image', true ), FILTER_VALIDATE_BOOLEAN );
	if ( ! isset( $image ) || ! $image )
		return;
	
	$image_alignment = sanitize_text_field( wpsp_get_setting( $wpsp_id, 'wpsp_image_alignment' ) );
	$image_height = absint( wpsp_get_setting( $wpsp_id, 'wpsp_image_height' ) );
	$image_width = absint( wpsp_get_setting( $wpsp_id, 'wpsp_image_width' ) );
	$image_overlay_color = wpsp_sanitize_hex_color( wpsp_get_setting( $wpsp_id, 'wpsp_image_overlay_color' ) );
	$image_overlay_icon = sanitize_text_field( wpsp_get_setting( $wpsp_id, 'wpsp_image_overlay_icon' ) );
	$image_hover_effect = sanitize_text_field( wpsp_get_setting( $wpsp_id, 'wpsp_image_hover_effect' ) );
	$columns     			= sanitize_text_field( wpsp_get_setting( $wpsp_id, 'wpsp_columns' ) );
	
	$image_id = get_post_thumbnail_id( get_the_ID(), 'full' );
	$image_url = wp_get_attachment_image_src( $image_id, 'full', true );
	$image_atts = wpsp_image_attributes( $image_url[1], $image_url[2], $image_width, $image_height );
	$hover = ( '' !== $image_hover_effect ) ? $image_hover_effect : '';
	?>
	<div class="wp-show-posts-image <?php echo $hover . ' wpsp-image-' . $image_alignment; ?> ">
		<?php printf( '<a href="%1$s" %2$s title="%3$s">',
		apply_filters( 'wpsp_image_href', esc_url( get_the_permalink() ) ),
		apply_filters( 'wpsp_image_data', '' ),
		apply_filters( 'wpsp_image_title', esc_attr( get_the_title() ) )
		); ?>
			<?php if ( ! empty( $image_atts ) ) : ?>
				<img src="<?php echo WPSP_Resize( $image_url[0], $image_atts[ 'width' ], $image_atts[ 'height' ], $image_atts[ 'crop' ], true, $image_atts[ 'upscale' ] ); ?>" alt="<?php esc_attr( the_title() ); ?>" itemprop="image" class="<?php echo $image_alignment; ?>" />
			<?php else :
				the_post_thumbnail( apply_filters( 'wpsp_default_image_size', 'full' ), array( 'itemprop' => 'image' ) );
			endif;
			
			if ( '' !== $image_overlay_color || '' !== $image_overlay_icon ) :
				$color = ( $image_overlay_color ) ? 'style="background-color:' . wpsp_hex2rgba( $image_overlay_color, apply_filters( 'wpsp_overlay_opacity', 0.7 ) ) . '"' : '';
				$icon = ( $image_overlay_icon ) ? $image_overlay_icon : 'no-icon';
				echo '<span class="wp-show-posts-image-overlay ' . $icon . '" ' . $color . '></span>';
			endif; 
			?>
		</a>
	</div>
	<?php
}
endif;

if ( ! function_exists( 'wpsp_add_post_image_before_title' ) ) :
add_action( 'wpsp_before_header','wpsp_add_post_image_before_title' );
function wpsp_add_post_image_before_title()
{
	global $wpsp_id;
	
	if ( ! isset( $wpsp_id ) )
		return;
	
	$image_location = sanitize_text_field( wpsp_get_setting( $wpsp_id, 'wpsp_image_location' ) );
	
	if ( 'above-title' == $image_location )
		wpsp_post_image();
}
endif;

if ( ! function_exists( 'wpsp_add_post_image_before_content' ) ) :
add_action( 'wpsp_before_content','wpsp_add_post_image_before_content' );
function wpsp_add_post_image_before_content()
{
	global $wpsp_id;
	
	if ( ! isset( $wpsp_id ) )
		return;
	
	$image_location = sanitize_text_field( wpsp_get_setting( $wpsp_id, 'wpsp_image_location' ) );
	
	if ( 'below-title' == $image_location )
		wpsp_post_image();
}
endif;

if ( ! function_exists( 'wpsp_read_more' ) ) :
add_action( 'wpsp_after_content','wpsp_read_more', 5 );
function wpsp_read_more()
{
	global $wpsp_id;
	
	if ( ! isset( $wpsp_id ) )
		return;
	
	$read_more_text = wp_kses_post( wpsp_get_setting( $wpsp_id, 'wpsp_read_more_text' ) );
	$read_more_style = esc_attr( wpsp_get_setting( $wpsp_id, 'wpsp_read_more_style' ) );
	$read_more_color = sanitize_text_field( wpsp_get_setting( $wpsp_id, 'wpsp_read_more_color' ) );
	
	// The read more button
	if ( $read_more_text ) : ?>
		<div class="wpsp-read-more">
			<a title="<?php echo esc_attr( get_the_title() ); ?>" class="wp-show-posts-read-more <?php echo $read_more_style; ?> <?php echo $read_more_color; ?>" href="<?php esc_url( the_permalink() ); ?>"><?php echo $read_more_text; ?></a>
		</div>
	<?php endif;
}
endif;

if ( ! function_exists( 'wpsp_hex2rgba' ) ) :
/** 
 * Convert hex to RGBA
 * @since 0.1
 */
function wpsp_hex2rgba($color, $opacity = false) {
 
	$default = 'rgb(0,0,0)';
 
	// Return default if no color provided
	if(empty($color))
          return $default; 
 
	// Sanitize $color if "#" is provided 
	if ($color[0] == '#' ) {
		$color = substr( $color, 1 );
	}

	// Check if color has 6 or 3 characters and get values
	if (strlen($color) == 6) {
			$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
	} elseif ( strlen( $color ) == 3 ) {
			$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
	} else {
			return $default;
	}

	// Convert hexadec to rgb
	$rgb =  array_map('hexdec', $hex);

	// Check if opacity is set(rgba or rgb)
	if($opacity){
		if(abs($opacity) > 1)
			$opacity = 1.0;
		$output = 'rgba('.implode(",",$rgb).','.$opacity.')';
	} else {
		$output = 'rgb('.implode(",",$rgb).')';
	}

	// Return rgb(a) color string
	return $output;
}
endif;

if ( ! function_exists( 'wpsp_sanitize_hex_color' ) ) :
function wpsp_sanitize_hex_color( $color ) {
    if ( '' === $color )
        return '';
 
    // 3 or 6 hex digits, or the empty string.
    if ( preg_match('|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) )
        return $color;
}
endif;

if ( ! function_exists( 'wpsp_image_attributes' ) ) :
/** 
 * Build our image attributes
 * @since 0.1
 */
function wpsp_image_attributes( $og_width = '', $og_height = '', $new_width = '', $new_height = '' )
{
	$ignore_crop = array( '', '0', '9999' );
	
	$image_atts = array();
	
	$image_atts = array(
		'width' => ( in_array( $new_width, $ignore_crop ) ) ? 9999 : intval( $new_width ),
		'height' => ( in_array( $new_height, $ignore_crop ) ) ? 9999 : intval( $new_height ),
		'crop' => ( in_array( $new_width, $ignore_crop ) || in_array( $new_height, $ignore_crop ) ) ? false : true
	);
	
	// If there's no height or width, empty the array
	if ( 9999 == $image_atts[ 'width' ] && 9999 == $image_atts[ 'height' ] )
		$image_atts = array();
	
	if ( ! empty( $image_atts ) ) :
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
		if ( $width_upscale && $image_atts[ 'height' ] == 9999 || $height_upscale && $image_atts[ 'width' ] == 9999 )
			$image_atts = array();
	endif;
	
	return apply_filters( 'generate_blog_image_attributes', $image_atts );
}
endif;

if ( ! function_exists( 'wpsp_pagination' ) ) :
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

	if ( $links ) :

		echo $links; 

	endif;
}
endif;