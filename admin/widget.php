<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds WPSP_Widget widget.
 */
if ( ! class_exists( 'WPSP_Widget' ) ) {
	class WPSP_Widget extends WP_Widget {

		/**
		 * Register widget with WordPress.
		 */
		function __construct() {
			parent::__construct(
				'wpsp_widget', // Base ID
				__( 'WP Show Posts', 'wp-show-posts' )
			);
		}

		/**
		 * Front-end display of widget.
		 *
		 * @see WP_Widget::widget()
		 *
		 * @param array $args     Widget arguments.
		 * @param array $instance Saved values from database.
		 */
		public function widget( $args, $instance ) {
			echo $args['before_widget'];

			if ( ! empty( $instance['title'] ) ) {
				echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
			}

			if ( function_exists( 'wpsp_display' ) ) {
				wpsp_display( absint( $instance['wpsp_id'] ) );
			}

			echo $args['after_widget'];
		}

		/**
		 * Back-end widget form.
		 *
		 * @see WP_Widget::form()
		 *
		 * @param array $instance Previously saved values from database.
		 */
		public function form( $instance ) {
			$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
			$id = ! empty( $instance['wpsp_id'] ) ? $instance['wpsp_id'] : '';
			?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( esc_attr( 'Title:' ) ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
			</p>
			<p>
				<select name="<?php echo $this->get_field_name( 'wpsp_id' );?>" id="<?php echo $this->get_field_id( 'wpsp_id' );?>">
				<option value=""></option>
				<?php
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
							echo '<option value="' . $post->ID . '"' . selected( $id, $post->ID ) . '>' . $post->post_title . '</option>';
						}
					}
				?>
				</select>
			</p>
			<?php
		}

		/**
		 * Sanitize widget form values as they are saved.
		 *
		 * @see WP_Widget::update()
		 *
		 * @param array $new_instance Values just sent to be saved.
		 * @param array $old_instance Previously saved values from database.
		 *
		 * @return array Updated safe values to be saved.
		 */
		public function update( $new_instance, $old_instance ) {
			$instance = array();
			$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
			$instance['wpsp_id'] = ( ! empty( $new_instance['wpsp_id'] ) ) ? absint( $new_instance['wpsp_id'] ) : '';

			return $instance;
		}

	} // class WPSP_Widget
}

if ( ! function_exists( 'wpsp_register_widget' ) ) {
	add_action( 'widgets_init', 'wpsp_register_widget' );
	
	function wpsp_register_widget() {
	    register_widget( 'WPSP_Widget' );
	}
}
