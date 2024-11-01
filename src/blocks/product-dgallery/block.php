<?php
/**
 * Server-side rendering of the `core/post-title` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/post-title` block on the server.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 *
 * @return string Returns the filtered post title for the current post wrapped inside "h1" tags.
 */
function render_block_wc_dgallery( $attributes, $content, $block ) {
	$product_id = ! empty( $attributes['postID'] ) ? $attributes['postID'] : '';
	$product_id = \A3Rev\WCDynamicGallery\Functions::get_current_product_id( $product_id );
	if ( empty( $product_id ) ) {
    	return '';
    }

    $global_wc_dgallery_activate = get_option( WOO_DYNAMIC_GALLERY_PREFIX . 'activate' );

	$actived_d_gallery = 0;
	if ( 'no' != $global_wc_dgallery_activate ) {
		$actived_d_gallery = 1;
	}

	if ( 1 == $actived_d_gallery ) {
		\A3Rev\WCDynamicGallery\Main::init_dynamic_gallery( $product_id, is_product() );

		ob_start();
		\A3Rev\WCDynamicGallery\Main::wc_dynamic_gallery_display( $product_id, true );

		$gallery_html = ob_get_clean();

		$wrapper_attributes = get_block_wrapper_attributes();

		return sprintf( '<div %1$s>%2$s</div>', $wrapper_attributes, $gallery_html );
	} else {
		return '';
	}
}

/**
 * Registers the `core/post-title` block on the server.
 */
function register_block_wc_dgallery() {
	register_block_type(
		__DIR__ . '/block.json',
		array(
			'render_callback' => 'render_block_wc_dgallery',
		)
	);
}
add_action( 'init', 'register_block_wc_dgallery' );
