<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! function_exists( 'wpsp_admin_scripts' ) ) {
	add_action( 'admin_print_scripts-post-new.php', 'wpsp_admin_scripts', 11 );
	add_action( 'admin_print_scripts-post.php', 'wpsp_admin_scripts', 11 );
	/**
	 * Add our admin scripts and styles
	 * @since 0.1
	 */
	function wpsp_admin_scripts() {
		global $post_type, $post;
	    if ( 'wp_show_posts' == $post_type ) {
			wp_enqueue_script( 'wpsp-admin-scripts', plugin_dir_url( __FILE__ ) . "js/admin-scripts.js", array( 'jquery' ), WPSP_VERSION, true );
			wp_localize_script( 'wpsp-admin-scripts', 'wpsp_object', array (
				'post_id' => ( isset( $post ) ) ? $post->ID : false,
				'nonce' => wp_create_nonce( 'wpsp_nonce' )
			));
		}

		wp_enqueue_style( 'wpsp-admin', plugin_dir_url( __FILE__ ) . "css/admin.css", array(), WPSP_VERSION );
	}
}

if ( ! function_exists( 'wpsp_translatable_strings' ) ) {
	add_action( 'admin_head','wpsp_translatable_strings', 0 );
	/**
	 * Add some javascript variables to the admin head
	 * @since 0.1
	 */
	function wpsp_translatable_strings() {
		?>
		<script type="text/javascript">
			var wpsp_add_posts = '<?php _e( 'WP Show Posts','wp-show-posts' );?>';
			var wpsp_nonce = '<?php echo wp_create_nonce( 'wpsp_nonce' ); ?>';
		</script>
		<?php
	}
}

if ( ! function_exists( 'wpsp_add_shortcode_button' ) ) {
	add_action( 'admin_init', 'wpsp_add_shortcode_button' );
	/*
	 * Set it up so we can register our TinyMCE button
	 * @since 0.1
	 */
	function wpsp_add_shortcode_button() {
		// check user permissions
		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			return;
		}

		// check if WYSIWYG is enabled
		if ( get_user_option( 'rich_editing' ) == 'true') {
			add_filter( 'mce_external_plugins', 'wpsp_shortcodes_add_tinymce_plugin' );
			add_filter( 'mce_buttons', 'wpsp_shortcodes_register_button' );
		}
	}
}

if ( ! function_exists( 'wpsp_shortcodes_add_tinymce_plugin' ) ) {
	/*
	 * Register our tinyMCE button javascript
	 * @since 0.1
	 */
	function wpsp_shortcodes_add_tinymce_plugin( $plugin_array ) {
		$plugin_array[ 'wpsp_shortcode_button' ] = plugin_dir_url( __FILE__ ) . '/js/button.js';
		return $plugin_array;
	}
}

if ( ! function_exists( 'wpsp_shortcodes_register_button' ) ) {
	/*
	 * Register our TinyMCE button
	 * @since 0.1
	 */
	function wpsp_shortcodes_register_button( $buttons ) {
		array_push( $buttons, 'wpsp_shortcode_button' );
		return $buttons;
	}
}
