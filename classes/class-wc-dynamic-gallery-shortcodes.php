<?php

namespace A3Rev\WCDynamicGallery;

class Shortcodes 
{
	public static function render_block_core_shortcode( $content, $parsed_block, $block ) {
		if ( has_shortcode( $content, 'wc_product_dynamic_gallery' ) || has_shortcode( $content, 'wc_product_dgallery' ) ) {
			$content = do_shortcode( $content );
		}
	    return $content;
	}

	public static function parse_shortcode_product_dynamic_gallery( $attributes ) {
		// Don't show content for shortcode on Dashboard, still support for admin ajax
		if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) return;

		if ( ! is_array( $attributes ) ) {
			$attributes = array();
		}

		$attr = array_merge(array(
			'product_id' => '', // leave empty for current product
        ), $attributes );

        $product_id = esc_attr( $attr['product_id'] );	// XSS ok

        $product_id = Functions::get_current_product_id( $product_id );

        if ( empty( $product_id ) ) {
        	return '';
        }

        $gallery_html = '';

		$global_wc_dgallery_activate = get_option( WOO_DYNAMIC_GALLERY_PREFIX . 'activate' );

		$actived_d_gallery = 0;
		if ( 'no' != $global_wc_dgallery_activate ) {
			$actived_d_gallery = 1;
		}

		if ( 1 == $actived_d_gallery ) {
			Main::init_dynamic_gallery( $product_id, is_product() );

			ob_start();
			Main::wc_dynamic_gallery_display( $product_id, true );

			$gallery_html = ob_get_clean();
		}

        return $gallery_html;
	}
}
