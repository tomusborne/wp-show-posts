<?php
if ( ! function_exists( 'wpsp_get_min_suffix' ) ) {
	/**
	 * Figure out if we should use minified scripts or not
	 * @since 0.1
	 */
	function wpsp_get_min_suffix() {
		return defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '-min';
	}
}
