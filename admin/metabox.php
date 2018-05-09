<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! function_exists( 'wpsp_remove_metaboxes' ) ) {
	add_action( 'add_meta_boxes', 'wpsp_remove_metaboxes', 99 );
	/**
	 * Remove all metaboxes from our WP Show Posts post type
	 * @since 0.1
	 */
	function wpsp_remove_metaboxes( $post_type ){

	    // If we're not in the wp_show_posts post type, bail.
	    if ( ! in_array( $post_type, array( 'wp_show_posts' ) ) ) {
	        return false;
		}

	    global $wp_meta_boxes;

	    // Don't remove the below
	    $exceptions = array(
	        'submitdiv',
			'butterbean-ui-wp_show_posts',
			'wpsp_shortcode_metabox'
	    );

	    // Loop through all our metaboxes
	    if ( ! empty( $wp_meta_boxes ) ) {
			foreach( $wp_meta_boxes as $page => $page_boxes ) {
	            if ( ! empty( $page_boxes ) ) {
					foreach( $page_boxes as $context => $box_context ) {
	                    if ( ! empty( $box_context ) ) {
							foreach( $box_context as $box_type ) {
	                            if ( ! empty( $box_type ) ) {
									foreach( $box_type as $id => $box ) {
	                                    /** Check to see if the meta box should be removed... */
	                                    if ( ! in_array( $id, $exceptions ) ) {
	                                        remove_meta_box( $id, $page, $context );
	                                    }
	                                }
	                            }
	                        }
	                    }
	                }
	            }
	        }
	    }
	}
}

if ( ! function_exists( 'wpsp_get_post_types' ) ) {
	/**
	 * List of all our post types exluding our own
	 * @since 0.1
	 */
	function wpsp_get_post_types() {
		$post_types = get_post_types( array( 'public' => true ) );
		$types = array();
		foreach ( $post_types as $type ) {
			if ( 'wp_show_posts' !== $type && 'attachment' !== $type ) {
				$types[ $type ] = $type;
			}
		}

		return $types;
	}
}

if ( ! function_exists( 'wpsp_load_butterbean' ) ) {
	add_action( 'plugins_loaded', 'wpsp_load_butterbean' );
	/**
	 * Load butterbean inside our post type
	 * @since 0.1
	 */
	function wpsp_load_butterbean() {
		require_once( trailingslashit( dirname( __FILE__ ) ) . '/butterbean/butterbean.php'	);
	}
}

if ( ! function_exists( 'wpsp_register' ) ) {
	add_action( 'butterbean_register', 'wpsp_register', 10, 2 );
	/**
	 * Create all of our metabox options
	 * @since 0.1
	 */
	function wpsp_register( $butterbean, $post_type ) {

		$defaults = wpsp_get_defaults();

		// Register managers, sections, controls, and settings here.
		$butterbean->register_manager(
	        'wp_show_posts',
	        array(
	            'label'     => esc_html__( 'WP Show Posts', 'wp-show-posts' ),
	            'post_type' => 'wp_show_posts',
	            'context'   => 'normal',
	            'priority'  => 'high'
	        )
	    );

		$manager = $butterbean->get_manager( 'wp_show_posts' );

		$manager->register_section(
	        'wpsp_posts',
	        array(
	            'label' => esc_html__( 'Posts', 'wp-show-posts' ),
	            'icon'  => 'dashicons-admin-post'
	        )
	    );

		$manager->register_control(
	        'wpsp_post_type', // Same as setting name.
	        array(
	            'type'    => 'select',
	            'section' => 'wpsp_posts',
	            'label'   => esc_html__( 'Post type', 'wp-show-posts' ),
	            'choices' => wpsp_get_post_types(),
				'attr' => array( 'id' => 'wpsp-post-type' )
	        )
	    );

		$manager->register_setting(
	        'wpsp_post_type', // Same as control name.
	        array(
	            'sanitize_callback' => 'sanitize_text_field',
				'default' => $defaults[ 'wpsp_post_type' ] ? $defaults[ 'wpsp_post_type' ] : ''
	        )
	    );

		$manager->register_control(
	        'wpsp_taxonomy', // Same as setting name.
	        array(
	            'type'    => 'select',
	            'section' => 'wpsp_posts',
	            'label'   => esc_html__( 'Taxonomy', 'wp-show-posts' ),
	            'choices' => array(),
				'attr' => array( 'id' => 'wpsp-taxonomy' )
	        )
	    );

		$manager->register_setting(
	        'wpsp_taxonomy', // Same as control name.
	        array(
	            'sanitize_callback' => 'sanitize_text_field',
				'default' => $defaults[ 'wpsp_taxonomy' ] ? $defaults[ 'wpsp_taxonomy' ] : ''
	        )
	    );

		$manager->register_control(
	        'wpsp_tax_term', // Same as setting name.
	        array(
	            'type'    => 'checkboxes',
	            'section' => 'wpsp_posts',
	            'label'   => esc_html__( 'Terms', 'wp-show-posts' ),
				'choices' => array(),
	        )
	    );

		$manager->register_setting(
	        'wpsp_tax_term', // Same as control name.
	        array(
	            'sanitize_callback' => '',
				'default' => $defaults[ 'wpsp_tax_term' ] ? $defaults[ 'wpsp_tax_term' ] : ''
	        )
	    );

		$manager->register_control(
	        'wpsp_posts_per_page', // Same as setting name.
	        array(
	            'type'    => 'number',
	            'section' => 'wpsp_posts',
	            'label'   => esc_html__( 'Posts per page', 'wp-show-posts' )
	        )
	    );

		$manager->register_setting(
	        'wpsp_posts_per_page', // Same as control name.
	        array(
	            'sanitize_callback' => 'wpsp_sanitize_intval',
				'default' => $defaults[ 'wpsp_posts_per_page' ] ? $defaults[ 'wpsp_posts_per_page' ] : 10
	        )
	    );

		$manager->register_control(
			'wpsp_pagination',
			array(
				'type'        => 'checkbox',
				'section'     => 'wpsp_posts',
				'label'       => __( 'Pagination','wp-show-posts' ),
				'description' => __( 'Pagination should only be used if your posts are the only thing in the content area to prevent duplicate content issues.','wp-show-posts' ),
				'attr' => array( 'id' => 'wpsp-pagination' )
			)
		);

		$manager->register_setting(
			'wpsp_pagination',
			array(
				'sanitize_callback' => 'butterbean_validate_boolean',
				'default' => $defaults[ 'wpsp_pagination' ] ? $defaults[ 'wpsp_pagination' ] : false
			)
		);

		$manager->register_section(
	        'wpsp_columns',
	        array(
	            'label' => esc_html__( 'Columns', 'wp-show-posts' ),
	            'icon'  => 'dashicons-grid-view'
	        )
	    );

		$manager->register_control(
	        'wpsp_columns', // Same as setting name.
	        array(
	            'type'    => 'select',
	            'section' => 'wpsp_columns',
	            'label'   => esc_html__( 'Columns', 'wp-show-posts' ),
	            'choices' => array(
					'col-12' => '1',
					'col-6' => '2',
					'col-4' => '3',
					'col-3' => '4',
					'col-20' => '5'
				),
				'attr' => array( 'id' => 'wpsp-columns' )
	        )
	    );

		$manager->register_setting(
	        'wpsp_columns', // Same as control name.
	        array(
	            'sanitize_callback' => 'sanitize_text_field',
				'default' => $defaults[ 'wpsp_columns' ] ? $defaults[ 'wpsp_columns' ] : '12'
	        )
	    );

		$manager->register_control(
	        'wpsp_columns_gutter', // Same as setting name.
	        array(
	            'type'    => 'text',
	            'section' => 'wpsp_columns',
	            'label'   => esc_html__( 'Columns gutter', 'wp-show-posts' ),
				'attr'    => array( 'class' => 'widefat' ),
	        )
	    );

		$manager->register_setting(
	        'wpsp_columns_gutter', // Same as control name.
	        array(
	            'sanitize_callback' => 'sanitize_text_field',
				'default' => $defaults[ 'wpsp_columns_gutter' ] ? $defaults[ 'wpsp_columns_gutter' ] : ''
	        )
	    );

		$manager->register_section(
	        'wpsp_images',
	        array(
	            'label' => esc_html__( 'Images', 'wp-show-posts' ),
	            'icon'  => 'dashicons-format-image'
	        )
	    );

		$manager->register_control(
			'wpsp_image',
			array(
				'type'        => 'checkbox',
				'section'     => 'wpsp_images',
				'label'       => __( 'Images','wp-show-posts' ),
				'attr' => array( 'id' => 'wpsp-image' )
			)
		);

		$manager->register_setting(
			'wpsp_image',
			array(
				'sanitize_callback' => 'butterbean_validate_boolean',
				'default' => $defaults[ 'wpsp_image' ]
			)
		);

		$manager->register_control(
	        'wpsp_image_width', // Same as setting name.
	        array(
	            'type'    => 'number',
	            'section' => 'wpsp_images',
	            'label'   => esc_html__( 'Image width (px)', 'wp-show-posts' ),
				'attr' => array( 'id' => 'wpsp-image-width' )
	        )
	    );

		$manager->register_setting(
	        'wpsp_image_width', // Same as control name.
	        array(
	            'sanitize_callback' => 'wpsp_sanitize_absint',
				'default' => $defaults[ 'wpsp_image_width' ] ? $defaults[ 'wpsp_image_width' ] : ''
	        )
	    );

		$manager->register_control(
	        'wpsp_image_height', // Same as setting name.
	        array(
	            'type'    => 'number',
	            'section' => 'wpsp_images',
	            'label'   => esc_html__( 'Image height (px)', 'wp-show-posts' ),
				'attr' => array( 'id' => 'wpsp-image-height' )
	        )
	    );

		$manager->register_setting(
	        'wpsp_image_height', // Same as control name.
	        array(
	            'sanitize_callback' => 'wpsp_sanitize_absint',
				'default' => $defaults[ 'wpsp_image_height' ] ? $defaults[ 'wpsp_image_height' ] : ''
	        )
	    );

		$manager->register_control(
	        'wpsp_image_alignment', // Same as setting name.
	        array(
	            'type'    => 'select',
	            'section' => 'wpsp_images',
	            'label'   => esc_html__( 'Image alignment', 'wp-show-posts' ),
	            'choices' => array(
					'left' => __( 'Left','wp-show-posts' ),
					'center' => __( 'Center','wp-show-posts' ),
					'right' => __( 'Right','wp-show-posts' )
				),
				'attr' => array( 'id' => 'wpsp-image-alignment' )
	        )
	    );

		$manager->register_setting(
	        'wpsp_image_alignment', // Same as control name.
	        array(
	            'sanitize_callback' => 'sanitize_text_field',
				'default' => $defaults[ 'wpsp_image_alignment' ] ? $defaults[ 'wpsp_image_alignment' ] : ''
	        )
	    );

		$manager->register_control(
	        'wpsp_image_location', // Same as setting name.
	        array(
	            'type'    => 'select',
	            'section' => 'wpsp_images',
	            'label'   => esc_html__( 'Image location', 'wp-show-posts' ),
	            'choices' => array(
					'below-title' => __( 'Below title','wp-show-posts' ),
					'above-title' => __( 'Above title','wp-show-posts' )
				),
				'attr' => array( 'id' => 'wpsp-image-location' )
	        )
	    );

		$manager->register_setting(
	        'wpsp_image_location', // Same as control name.
	        array(
	            'sanitize_callback' => 'sanitize_text_field',
				'default' => $defaults[ 'wpsp_image_location' ] ? $defaults[ 'wpsp_image_location' ] : ''
	        )
	    );

		$manager->register_section(
	        'wpsp_content',
	        array(
	            'label' => esc_html__( 'Content', 'wp-show-posts' ),
	            'icon'  => 'dashicons-editor-alignleft'
	        )
	    );

		$manager->register_control(
	        'wpsp_content_type', // Same as setting name.
	        array(
	            'type'    => 'select',
	            'section' => 'wpsp_content',
	            'label'   => esc_html__( 'Content type', 'wp-show-posts' ),
	            'choices' => array(
					'excerpt' => __( 'Excerpt','wp-show-posts' ),
					'full' => __( 'Full','wp-show-posts' ),
					'none' => __( 'None','wp-show-posts' )
				),
				'attr' => array( 'id' => 'wpsp-content-type' )
	        )
	    );

		$manager->register_setting(
	        'wpsp_content_type', // Same as control name.
	        array(
	            'sanitize_callback' => 'sanitize_text_field',
				'default' => $defaults[ 'wpsp_content_type' ] ? $defaults[ 'wpsp_content_type' ] : ''
	        )
	    );

		$manager->register_control(
	        'wpsp_excerpt_length', // Same as setting name.
	        array(
	            'type'    => 'number',
	            'section' => 'wpsp_content',
	            'label'   => esc_html__( 'Excerpt length (words)', 'wp-show-posts' ),
				'attr' => array( 'id' => 'wpsp-excerpt-length' )
	        )
	    );

		$manager->register_setting(
	        'wpsp_excerpt_length', // Same as control name.
	        array(
	            'sanitize_callback' => 'wpsp_sanitize_absint',
				'default' => $defaults[ 'wpsp_excerpt_length' ] ? $defaults[ 'wpsp_excerpt_length' ] : ''
	        )
	    );

		$manager->register_control(
			'wpsp_include_title',
			array(
				'type'        => 'checkbox',
				'section'     => 'wpsp_content',
				'label'       => __( 'Include title','wp-show-posts' ),
				'attr' => array( 'id' => 'wpsp-include-title' )
			)
		);

		$manager->register_setting(
			'wpsp_include_title',
			array(
				'sanitize_callback' => 'butterbean_validate_boolean',
				'default' => true
			)
		);

		// Title element
		$manager->register_control(
	        'wpsp_title_element', // Same as setting name.
	        array(
	            'type'    => 'select',
	            'section' => 'wpsp_content',
	            'label'   => esc_html__( 'Title element', 'wp-show-posts-pro' ),
				'choices' => array(
					'' => '',
					'p' => 'p',
					'span' => 'span',
					'h1' => 'h1',
					'h2' => 'h2',
					'h3' => 'h3',
					'h4' => 'h4',
					'h5' => 'h5'
				),
				'attr' => array( 'id' => 'wpsp-title-element' )
	        )
	    );

		$manager->register_setting(
	        'wpsp_title_element', // Same as control name.
	        array(
	            'sanitize_callback' => 'sanitize_text_field',
				'default' => $defaults[ 'wpsp_title_element' ] ? $defaults[ 'wpsp_title_element' ] : ''
	        )
	    );

		$manager->register_control(
	        'wpsp_read_more_text', // Same as setting name.
	        array(
	            'type'    => 'text',
	            'section' => 'wpsp_content',
	            'label'   => esc_html__( 'Read more text', 'wp-show-posts' )
	        )
	    );

		$manager->register_setting(
	        'wpsp_read_more_text', // Same as control name.
	        array(
	            'sanitize_callback' => 'wp_kses_post',
				'default' => $defaults[ 'wpsp_read_more_text' ]
	        )
	    );

		$manager->register_control(
	        'wpsp_read_more_class', // Same as setting name.
	        array(
	            'type'    => 'text',
	            'section' => 'wpsp_content',
	            'label'   => esc_html__( 'Read more button class', 'wp-show-posts' ),
				'priority' => 75
	        )
	    );

		$manager->register_setting(
	        'wpsp_read_more_class', // Same as control name.
	        array(
	            'sanitize_callback' => 'esc_attr',
				'default' => $defaults[ 'wpsp_read_more_class' ] ? $defaults[ 'wpsp_read_more_class' ] : ''
	        )
	    );

		$manager->register_section(
	        'wpsp_post_meta',
	        array(
	            'label' => esc_html__( 'Meta', 'wp-show-posts' ),
	            'icon'  => 'dashicons-editor-ul'
	        )
	    );

		$manager->register_control(
			'wpsp_include_author',
			array(
				'type'        => 'checkbox',
				'section'     => 'wpsp_post_meta',
				'label'       => __( 'Include author','wp-show-posts' ),
				'attr' => array( 'id' => 'wpsp-include-author' )
			)
		);

		$manager->register_setting(
			'wpsp_include_author',
			array(
				'sanitize_callback' => 'butterbean_validate_boolean',
				'default' => $defaults[ 'wpsp_include_author' ] ? $defaults[ 'wpsp_include_author' ] : false
			)
		);

		$manager->register_control(
	        'wpsp_author_location', // Same as setting name.
	        array(
	            'type'    => 'select',
	            'section' => 'wpsp_post_meta',
	            'label'   => esc_html__( 'Author location', 'wp-show-posts' ),
	            'choices' => array(
					'below-title' => __( 'Below title','wp-show-posts' ),
					'below-post' => __( 'Below post','wp-show-posts' )
				),
				'attr' => array( 'id' => 'wpsp-author-location' )
	        )
	    );

		$manager->register_setting(
	        'wpsp_author_location', // Same as control name.
	        array(
	            'sanitize_callback' => 'sanitize_text_field',
				'default' => $defaults[ 'wpsp_author_location' ] ? $defaults[ 'wpsp_author_location' ] : ''
	        )
	    );

		$manager->register_control(
			'wpsp_include_date',
			array(
				'type'        => 'checkbox',
				'section'     => 'wpsp_post_meta',
				'label'       => __( 'Include date','wp-show-posts' ),
				'attr' => array( 'id' => 'wpsp-include-date' )
			)
		);

		$manager->register_setting(
			'wpsp_include_date',
			array(
				'sanitize_callback' => 'butterbean_validate_boolean',
				'default' => $defaults[ 'wpsp_include_date' ] ? $defaults[ 'wpsp_include_date' ] : false
			)
		);

		$manager->register_control(
	        'wpsp_date_location', // Same as setting name.
	        array(
	            'type'    => 'select',
	            'section' => 'wpsp_post_meta',
	            'label'   => esc_html__( 'Date location', 'wp-show-posts' ),
	            'choices' => array(
					'below-title' => __( 'Below title','wp-show-posts' ),
					'below-post' => __( 'Below post','wp-show-posts' )
				),
				'attr' => array( 'id' => 'wpsp-date-location' )
	        )
	    );

		$manager->register_setting(
	        'wpsp_date_location', // Same as control name.
	        array(
	            'sanitize_callback' => 'sanitize_text_field',
				'default' => $defaults[ 'wpsp_date_location' ] ? $defaults[ 'wpsp_date_location' ] : ''
	        )
	    );

		$manager->register_control(
			'wpsp_include_terms',
			array(
				'type'        => 'checkbox',
				'section'     => 'wpsp_post_meta',
				'label'       => __( 'Include terms','wp-show-posts' ),
				'attr' => array( 'id' => 'wpsp-include-terms' )
			)
		);

		$manager->register_setting(
			'wpsp_include_terms',
			array(
				'sanitize_callback' => 'butterbean_validate_boolean',
				'default' => $defaults[ 'wpsp_include_terms' ] ? $defaults[ 'wpsp_include_terms' ] : false
			)
		);

		$manager->register_control(
	        'wpsp_terms_location', // Same as setting name.
	        array(
	            'type'    => 'select',
	            'section' => 'wpsp_post_meta',
	            'label'   => esc_html__( 'Terms location', 'wp-show-posts' ),
	            'choices' => array(
					'below-title' => __( 'Below title','wp-show-posts' ),
					'below-post' => __( 'Below post','wp-show-posts' )
				),
				'attr' => array( 'id' => 'wpsp-terms-location' )
	        )
	    );

		$manager->register_setting(
	        'wpsp_terms_location', // Same as control name.
	        array(
	            'sanitize_callback' => 'sanitize_text_field',
				'default' => $defaults[ 'wpsp_terms_location' ] ? $defaults[ 'wpsp_terms_location' ] : ''
	        )
	    );

		$manager->register_control(
			'wpsp_include_comments',
			array(
				'type'        => 'checkbox',
				'section'     => 'wpsp_post_meta',
				'label'       => __( 'Include comments link','wp-show-posts' ),
				'attr' => array( 'id' => 'wpsp-include-comments-link' )
			)
		);

		$manager->register_setting(
			'wpsp_include_comments',
			array(
				'sanitize_callback' => 'butterbean_validate_boolean',
				'default' => $defaults[ 'wpsp_include_comments' ] ? $defaults[ 'wpsp_include_comments' ] : false
			)
		);

		$manager->register_control(
	        'wpsp_comments_location', // Same as setting name.
	        array(
	            'type'    => 'select',
	            'section' => 'wpsp_post_meta',
	            'label'   => esc_html__( 'Comments link location', 'wp-show-posts' ),
	            'choices' => array(
					'below-title' => __( 'Below title','wp-show-posts' ),
					'below-post' => __( 'Below post','wp-show-posts' )
				),
				'attr' => array( 'id' => 'wpsp-comments-link-location' )
	        )
	    );

		$manager->register_setting(
	        'wpsp_comments_location', // Same as control name.
	        array(
	            'sanitize_callback' => 'sanitize_text_field',
				'default' => $defaults[ 'wpsp_comments_location' ] ? $defaults[ 'wpsp_comments_location' ] : ''
	        )
	    );

		$manager->register_section(
	        'wpsp_query_args',
	        array(
	            'label' => esc_html__( 'More settings', 'wp-show-posts' ),
	            'icon'  => 'dashicons-admin-generic',
				'priority' => 999
	        )
	    );

		$manager->register_control(
	        'wpsp_author', // Same as setting name.
	        array(
	            'type'    => 'number',
	            'section' => 'wpsp_query_args',
	            'label'   => esc_html__( 'Author ID', 'wp-show-posts' )
	        )
	    );

		$manager->register_setting(
	        'wpsp_author', // Same as control name.
	        array(
	            'sanitize_callback' => 'wpsp_sanitize_absint',
				'default' => $defaults[ 'wpsp_author' ] ? $defaults[ 'wpsp_author' ] : ''
	        )
	    );

		$manager->register_control(
			'wpsp_exclude_current',
			array(
				'type'        => 'checkbox',
				'section'     => 'wpsp_query_args',
				'label'       => __( 'Exclude current','wp-show-posts' ),
				'attr' => array( 'id' => 'wpsp-exclude-current' )
			)
		);

		$manager->register_setting(
			'wpsp_exclude_current',
			array(
				'sanitize_callback' => 'butterbean_validate_boolean',
				'default' => $defaults[ 'wpsp_exclude_current' ] ? $defaults[ 'wpsp_exclude_current' ] : false
			)
		);

		$manager->register_control(
	        'wpsp_post_id', // Same as setting name.
	        array(
	            'type'    => 'text',
	            'section' => 'wpsp_query_args',
	            'label'   => esc_html__( 'Post IDs', 'wp-show-posts' )
	        )
	    );

		$manager->register_setting(
	        'wpsp_post_id', // Same as control name.
	        array(
	            'sanitize_callback' => 'sanitize_text_field',
				'default' => $defaults[ 'wpsp_post_id' ] ? $defaults[ 'wpsp_post_id' ] : ''
	        )
	    );

		$manager->register_control(
	        'wpsp_exclude_post_id', // Same as setting name.
	        array(
	            'type'    => 'text',
	            'section' => 'wpsp_query_args',
	            'label'   => esc_html__( 'Exclude Post IDs', 'wp-show-posts' )
	        )
	    );

		$manager->register_setting(
	        'wpsp_exclude_post_id', // Same as control name.
	        array(
	            'sanitize_callback' => 'sanitize_text_field',
				'default' => $defaults[ 'wpsp_exclude_post_id' ] ? $defaults[ 'wpsp_exclude_post_id' ] : ''
	        )
	    );

		$manager->register_control(
			'wpsp_ignore_sticky_posts',
			array(
				'type'        => 'checkbox',
				'section'     => 'wpsp_query_args',
				'label'       => __( 'Ignore sticky posts','wp-show-posts' ),
				'attr' => array( 'id' => 'wpsp-ignore-sticky-posts' )
			)
		);

		$manager->register_setting(
			'wpsp_ignore_sticky_posts',
			array(
				'sanitize_callback' => 'butterbean_validate_boolean',
				'default' => $defaults[ 'wpsp_ignore_sticky_posts' ] ? $defaults[ 'wpsp_ignore_sticky_posts' ] : false
			)
		);

		$manager->register_control(
	        'wpsp_offset', // Same as setting name.
	        array(
	            'type'    => 'number',
	            'section' => 'wpsp_query_args',
	            'label'   => esc_html__( 'Offset', 'wp-show-posts' )
	        )
	    );

		$manager->register_setting(
	        'wpsp_offset', // Same as control name.
	        array(
	            'sanitize_callback' => 'wpsp_sanitize_absint',
				'default' => $defaults[ 'wpsp_offset' ] ? $defaults[ 'wpsp_offset' ] : ''
	        )
	    );

		$manager->register_control(
	        'wpsp_order', // Same as setting name.
	        array(
	            'type'    => 'select',
	            'section' => 'wpsp_query_args',
	            'label'   => esc_html__( 'Order', 'wp-show-posts' ),
	            'choices' => array(
					'DESC' => __( 'Descending','wp-show-posts' ),
					'ASC' => __( 'Ascending','wp-show-posts' )
				),
				'attr' => array( 'id' => 'wpsp-order' )
	        )
	    );

		$manager->register_setting(
	        'wpsp_order', // Same as control name.
	        array(
	            'sanitize_callback' => 'sanitize_text_field',
				'default' => $defaults[ 'wpsp_order' ] ? $defaults[ 'wpsp_order' ] : 'DESC'
	        )
	    );

		$manager->register_control(
	        'wpsp_orderby', // Same as setting name.
	        array(
	            'type'    => 'select',
	            'section' => 'wpsp_query_args',
	            'label'   => esc_html__( 'Order by', 'wp-show-posts' ),
	            'choices' => array(
					'none' => __( 'No order','wp-show-posts' ),
					'ID' => __( 'ID','wp-show-posts' ),
					'author' => __( 'Author','wp-show-posts' ),
					'title' => __( 'Title','wp-show-posts' ),
					'name' => __( 'Slug','wp-show-posts' ),
					'type' => __( 'Post type','wp-show-posts' ),
					'date' => __( 'Date','wp-show-posts' ),
					'modified' => __( 'Modified','wp-show-posts' ),
					'parent' => __( 'Parent','wp-show-posts' ),
					'rand' => __( 'Random','wp-show-posts' ),
					'comment_count' => __( 'Comment count','wp-show-posts' )
				),
				'attr' => array( 'id' => 'wpsp-orderby' )
	        )
	    );

		$manager->register_setting(
	        'wpsp_orderby', // Same as control name.
	        array(
	            'sanitize_callback' => 'sanitize_text_field',
				'default' => $defaults[ 'wpsp_orderby' ] ? $defaults[ 'wpsp_orderby' ] : 'date'
	        )
	    );

		$manager->register_control(
	        'wpsp_post_status', // Same as setting name.
	        array(
	            'type'    => 'select',
	            'section' => 'wpsp_query_args',
	            'label'   => esc_html__( 'Status', 'wp-show-posts' ),
	            'choices' => array(
					'publish' => __( 'Published','wp-show-posts' ),
					'pending' => __( 'Pending','wp-show-posts' ),
					'draft' => __( 'Draft','wp-show-posts' ),
					'auto-draft' => __( 'Auto draft','wp-show-posts' ),
					'future' => __( 'Future','wp-show-posts' ),
					'private' => __( 'Private','wp-show-posts' ),
					'inherit' => __( 'Inherit','wp-show-posts' ),
					'trash' => __( 'Trash','wp-show-posts' ),
					'any' => __( 'Any','wp-show-posts' )
				),
				'attr' => array( 'id' => 'wpsp-post-status' )
	        )
	    );

		$manager->register_setting(
	        'wpsp_post_status', // Same as control name.
	        array(
	            'sanitize_callback' => 'sanitize_text_field',
				'default' => $defaults[ 'wpsp_post_status' ] ? $defaults[ 'wpsp_post_status' ] : 'publish'
	        )
	    );

		$manager->register_control(
	        'wpsp_meta_key', // Same as setting name.
	        array(
	            'type'    => 'text',
	            'section' => 'wpsp_query_args',
	            'label'   => esc_html__( 'Meta key', 'wp-show-posts' )
	        )
	    );

		$manager->register_setting(
	        'wpsp_meta_key', // Same as control name.
	        array(
	            'sanitize_callback' => 'sanitize_text_field',
				'default' => $defaults[ 'wpsp_meta_key' ] ? $defaults[ 'wpsp_meta_key' ] : ''
	        )
	    );

		$manager->register_control(
	        'wpsp_meta_value', // Same as setting name.
	        array(
	            'type'    => 'text',
	            'section' => 'wpsp_query_args',
	            'label'   => esc_html__( 'Meta value', 'wp-show-posts' )
	        )
	    );

		$manager->register_setting(
	        'wpsp_meta_value', // Same as control name.
	        array(
	            'sanitize_callback' => 'sanitize_text_field',
				'default' => $defaults[ 'wpsp_meta_value' ] ? $defaults[ 'wpsp_meta_value' ] : ''
	        )
	    );

		$manager->register_control(
	        'wpsp_tax_operator', // Same as setting name.
	        array(
	            'type'    => 'select',
	            'section' => 'wpsp_query_args',
	            'label'   => esc_html__( 'Tax operator', 'wp-show-posts' ),
	            'choices' => array(
					'IN' => 'IN',
					'NOT IN' => 'NOT IN',
					'AND' => 'AND',
					'EXISTS' => 'EXISTS',
					'NOT EXISTS' => 'NOT EXISTS'
				),
				'attr' => array( 'id' => 'wpsp-tax-operator' )
	        )
	    );

		$manager->register_setting(
	        'wpsp_tax_operator', // Same as control name.
	        array(
	            'sanitize_callback' => 'sanitize_text_field',
				'default' => $defaults[ 'wpsp_tax_operator' ] ? $defaults[ 'wpsp_tax_operator' ] : 'IN'
	        )
	    );

		$manager->register_control(
	        'wpsp_no_results', // Same as setting name.
	        array(
	            'type'    => 'text',
	            'section' => 'wpsp_query_args',
	            'label'   => esc_html__( 'No results message', 'wp-show-posts' )
	        )
	    );

		$manager->register_setting(
	        'wpsp_no_results', // Same as control name.
	        array(
	            'sanitize_callback' => 'wp_kses_post',
				'default' => $defaults[ 'wpsp_no_results' ] ? $defaults[ 'wpsp_no_results' ] : ''
	        )
	    );
	}
}

if ( ! function_exists( 'wpsp_sanitize_intval' ) ) {
	/**
	 * Sanitize our value so it has to be a positive integer
	 * @since 0.1
	 */
	function wpsp_sanitize_intval( $input ) {
		if ( '' == $input ) {
			return $input;
		}

		return intval( $input );
	}
}

if ( ! function_exists( 'wpsp_sanitize_absint' ) ) {
	/**
	 * Sanitize our value so it can be a negative or positive integer
	 * @since 0.1
	 */
	function wpsp_sanitize_absint( $input ) {
		if ( '' == $input ) {
			return $input;
		}

		return absint( $input );
	}
}

if ( ! function_exists( 'wpsp_add_meta_boxes' ) ) {
	add_action( 'add_meta_boxes_wp_show_posts', 'wpsp_add_meta_boxes' );
	/**
	 * Add our usage metabox
	 * @since 0.1
	 */
	function wpsp_add_meta_boxes( $post ){
		add_meta_box( 'wpsp_shortcode', __( 'Usage', 'wp-show-posts' ), 'wpsp_shortcode_metabox', 'wp_show_posts', 'side', 'low' );
	}
}

if ( ! function_exists( 'wpsp_shortcode_metabox' ) ) {
	/**
	 * Meta box display callback.
	 *
	 * @param WP_Post $post Current post object.
	 * @since 0.1
	 */
	function wpsp_shortcode_metabox( $post ) {
	    ?>
		<h4 style="margin-bottom:5px;"><?php _e( 'Shortcode','wp-show-posts' ); ?></h4>
		<input type="text" class="widefat" value='[wp_show_posts id="<?php echo $post->ID;?>"]' readonly />

		<h4 style="margin-bottom:5px;"><?php _e( 'Function','wp-show-posts' ); ?></h4>
		<input type="text" class="widefat" value='<?php echo esc_attr( "<?php if ( function_exists( 'wpsp_display' ) ) wpsp_display( " . $post->ID . " ); ?>" ); ?>' readonly />
		<?php
	}
}
