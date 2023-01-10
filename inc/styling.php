<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wpsp_before_wrapper', 'wpsp_basic_styling' );
function wpsp_basic_styling( $settings ) {
	// Start the magic
	$visual_css = array (

		'.wp-show-posts-columns#wpsp-' . $settings[ 'list_id' ] => array(
			'margin-left' => ( '' !== $settings[ 'columns_gutter' ] && '12' !== $settings[ 'columns' ] ) ? '-' . esc_attr( $settings[ 'columns_gutter' ] ) : null
		),

		'.wp-show-posts-columns#wpsp-' . $settings[ 'list_id' ] . ' .wp-show-posts-inner' => array(
			'margin' => ( '' !== $settings[ 'columns_gutter' ] && '12' !== $settings[ 'columns' ] ) ? '0 0 ' . esc_attr( $settings[ 'columns_gutter' ] ) . ' ' . esc_attr( $settings[ 'columns_gutter' ] ) : null,
		),

	);

	// Output the above CSS
	$output = '';
	foreach( $visual_css as $k => $properties ) {

		if ( !count( $properties ) ) {
			continue;
		}

		$temporary_output = $k . ' {';
		$elements_added = 0;

		foreach( $properties as $p => $v ) {

			if ( empty( $v ) ) {
				continue;
			}

			$elements_added++;
			$temporary_output .= $p . ': ' . $v . '; ';

		}

		$temporary_output .= "}";

		if ( $elements_added > 0 ) {
			$output .= $temporary_output;
		}

	}

	$output = str_replace(array("\r", "\n"), '', $output);

	if ( '' !== $output ) {
		echo '<style>';
		    echo $output;
		echo '</style>';
	}
}
