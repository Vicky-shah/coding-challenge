<?php
/**
 * Block class.
 *
 * @package SiteCounts
 */

namespace XWP\SiteCounts;

use WP_Block;

/**
 * The Site Counts dynamic block.
 *
 * Registers and renders the dynamic block.
 */
class Block {

	/**
	 * The Plugin instance.
	 *
	 * @var Plugin
	 */
	protected $plugin;

	/**
	 * Instantiates the class.
	 *
	 * @param Plugin $plugin The plugin object.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Adds the action to register the block.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'init', [ $this, 'register_block' ] );
	}

	/**
	 * Registers the block.
	 */
	public function register_block() {
		register_block_type_from_metadata(
			$this->plugin->dir(),
			[
				'render_callback' => [ $this, 'render_callback' ],
			]
		);
	}

	/**
	 * Renders the block.
	 *
	 * @param array    $attributes The attributes for the block.
	 * @param string   $content    The block content, if any.
	 * @param WP_Block $block      The instance of this block.
	 * @return string The markup of the block.
	 */
	public function render_callback( $attributes, $content, $block ) {
		$post_types = get_post_types( [ 'public' => true ] );
        $class_name = $attributes['className'];
		$class_name = isset($attributes['className'])? isset($attributes['className']):'';
		$class_name = apply_filters( 'filterposts_classname', $class_name );
        $class_name = esc_attr__( $attributes['className'], 'site-counts' );
		ob_start();

		?>
		<div class="<?php echo $class_name; ?>">
			<h2><?php esc_html_e( 'Post Counts', 'site-counts' ); ?></h2>
			<?php
			if(is_array($post_types)):
				foreach ( $post_types as $post_type_slug ) :
					$post_type_object = get_post_type_object( $post_type_slug );
					$post_count = count(
						get_posts(
							[
								'post_type'      => $post_type_slug,
								'posts_per_page' => -1,
							]
						)
					);

					?>
					<?php $posts_details = sprintf( esc_html__( 'There are  1%d. 2%s.', 'site-counts' ), intval( $post_count ), esc_html( $post_type_object->labels->name ) );
							$posts_details = apply_filters( 'filter_postdetails', $posts_details );
							$current_post_details = sprintf( esc_html__( 'The current post ID is %d.', 'site-counts' ), sanitize_text_field( wp_unslash( $_GET['post_id'] ) );
							$current_post_details = apply_filters( 'fiter_currentpost_details', $current_post_details );
					?>
					<p><?php echo $posts_details;?> </p>
				<?php endforeach; ?>
				<?php echo $current_post_details;?>
			<?php endif; ?>
		</div>
		<?php

		return ob_get_clean();
	}
}
