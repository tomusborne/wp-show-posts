<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! function_exists( 'wpsp_get_json_option' ) ) {
	add_action( 'wp_ajax_wpsp_get_json_option', 'wpsp_get_json_option' );
	/**
	 * Get the current option value
	 * @since 0.1
	 */
	function wpsp_get_json_option() {
		if ( ! isset( $_POST[ 'wpsp_nonce' ] ) || ! wp_verify_nonce( $_POST[ 'wpsp_nonce' ], 'wpsp_nonce' ) ) {
			wp_die( 'Permission declined' );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Permission declined' );
		}

		$option = ( get_post_meta( intval( $_POST[ 'id' ] ), sanitize_text_field( $_POST[ 'key' ] ) ) ) ? get_post_meta( intval( $_POST[ 'id' ] ), sanitize_text_field( $_POST[ 'key' ] ), true ) : false;

		if ( $option ) {
			echo wp_json_encode( $option );
		}

		die();
	}
}

if ( ! function_exists( 'wpsp_get_terms' ) ) {
	add_action( 'wp_ajax_wpsp_get_terms', 'wpsp_get_terms' );
	/**
	 * Get all of our terms depending on the set taxonomy
	 * @since 0.1
	 */
	function wpsp_get_terms() {
		if ( ! isset( $_POST[ 'wpsp_nonce' ] ) || ! wp_verify_nonce( $_POST[ 'wpsp_nonce' ], 'wpsp_nonce' ) ) {
			wp_die( 'Permission declined' );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Permission declined' );
		}

		if ( empty( $_POST['taxonomy'] ) ) {
			die();
		}

		$terms = get_terms( sanitize_key( $_POST[ 'taxonomy' ] ), 'orderby=count&hide_empty=1' );
		$count = count( $terms );
		$types = array();
		if ( $count > 0 ) {
			foreach ( $terms as $term ) {
				$types[] = $term->slug;
			}
		}

		echo wp_json_encode( $types );

		die();
	}
}

if ( ! function_exists( 'wpsp_get_taxonomies' ) ) {
	add_action( 'wp_ajax_wpsp_get_taxonomies', 'wpsp_get_taxonomies' );
	/**
	 * Get out taxonomies based on the set post type
	 * @since 0.1
	 */
	function wpsp_get_taxonomies() {
		if ( ! isset( $_POST[ 'wpsp_nonce' ] ) || ! wp_verify_nonce( $_POST[ 'wpsp_nonce' ], 'wpsp_nonce' ) ) {
			wp_die( 'Permission declined' );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Permission declined' );
		}

		$terms = get_object_taxonomies( sanitize_text_field( $_POST[ 'post_type' ] ) );
		$count = count( $terms );
		$types = array();
		if ( $count > 0 ) {
			foreach ( $terms as $term ) {
				$types[] = $term;
			}
		}

		echo wp_json_encode( $types );

		die();
	}
}

if ( ! function_exists( 'wpsp_get_post_lists' ) ) {
	add_action( 'wp_ajax_wpsp_get_post_lists', 'wpsp_get_post_lists' );
	/**
	 * Get all of our post lists
	 * @since 0.1
	 */
	function wpsp_get_post_lists() {
		if ( ! isset( $_POST[ 'wpsp_nonce' ] ) || ! wp_verify_nonce( $_POST[ 'wpsp_nonce' ], 'wpsp_nonce' ) ) {
			wp_die( 'Permission declined' );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Permission declined' );
		}

		$args = array(
			'posts_per_page'   => -1,
			'post_type'        => 'wp_show_posts',
			'post_status'      => 'publish',
			'showposts'		   => -1
		);
		$posts = get_posts( $args );

		$count = count( $posts );
		$types = array();
		if ( $count > 0 ) {
			foreach ( $posts as $post ) {
				$types[] = array( 'text' => $post->post_title, 'value' => $post->ID );
			}
		}

		echo wp_json_encode( $types );

		die();
	}
}
