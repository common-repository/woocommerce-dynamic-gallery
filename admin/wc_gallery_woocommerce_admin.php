<?php
function wc_dynamic_gallery_show() {
	\A3Rev\WCDynamicGallery\Main::wc_dynamic_gallery_display();
}

function wc_dynamic_gallery_install(){
	update_option('a3rev_woo_dgallery_lite_version', WOO_DYNAMIC_GALLERY_VERSION );
	update_option('a3_dynamic_gallery_db_version', WOO_DYNAMIC_GALLERY_DB_VERSION);

	delete_metadata( 'user', 0, $GLOBALS[WOO_DYNAMIC_GALLERY_PREFIX.'admin_init']->plugin_name . '-' . 'plugin_framework_global_box' . '-' . 'opened', '', true );

	update_option('a3rev_woo_dgallery_just_installed', true);
}

/**
 * Load languages file
 */
function wc_dynamic_gallery_init() {
	if ( get_option('a3rev_woo_dgallery_just_installed') ) {
		delete_option('a3rev_woo_dgallery_just_installed');

		// Set Settings Default from Admin Init
		$GLOBALS[WOO_DYNAMIC_GALLERY_PREFIX.'admin_init']->set_default_settings();

		// Build sass
		$GLOBALS[WOO_DYNAMIC_GALLERY_PREFIX.'less']->plugin_build_sass();
	}

	wc_dynamic_gallery_plugin_textdomain();
}

// Add language
add_action('init', 'wc_dynamic_gallery_init');

// Add custom style to dashboard
add_action( 'admin_enqueue_scripts', array( '\A3Rev\WCDynamicGallery\Functions', 'a3_wp_admin' ) );

// Add text on right of Visit the plugin on Plugin manager page
add_filter( 'plugin_row_meta', array('\A3Rev\WCDynamicGallery\Functions', 'plugin_extra_links'), 10, 2 );

// Need to call Admin Init to show Admin UI
$GLOBALS[WOO_DYNAMIC_GALLERY_PREFIX.'admin_init']->init();


function register_widget_wc_dynamic_gallery() {
	register_widget('\A3Rev\WCDynamicGallery\Widgets');
}
// Registry widget
add_action( 'widgets_init', 'register_widget_wc_dynamic_gallery' );

// Add upgrade notice to Dashboard pages
add_filter( $GLOBALS[WOO_DYNAMIC_GALLERY_PREFIX.'admin_init']->plugin_name . '_plugin_extension_boxes', array( '\A3Rev\WCDynamicGallery\Functions', 'plugin_extension_box' ) );

// Add extra link on left of Deactivate link on Plugin manager page
add_action('plugin_action_links_' . WOO_DYNAMIC_GALLERY_NAME, array( '\A3Rev\WCDynamicGallery\Functions', 'settings_plugin_links' ) );

add_action( 'wp', array( '\A3Rev\WCDynamicGallery\Main', 'frontend_register_scripts' ) );

// Force do_shortcode for the content from the shortcode & paragraph blocks
add_filter( 'render_block_core/shortcode', array('\A3Rev\WCDynamicGallery\Shortcodes', 'render_block_core_shortcode'), 10, 3);
add_filter( 'render_block_core/paragraph', array('\A3Rev\WCDynamicGallery\Shortcodes', 'render_block_core_shortcode'), 10, 3);

// Add shortcode [wc_product_dgallery product_id=0]
add_shortcode( 'wc_product_dgallery', array( '\A3Rev\WCDynamicGallery\Shortcodes', 'parse_shortcode_product_dynamic_gallery' ) );
add_shortcode( 'wc_product_dynamic_gallery', array( '\A3Rev\WCDynamicGallery\Shortcodes', 'parse_shortcode_product_dynamic_gallery' ) );

// Change the image show in cart page
add_filter( 'woocommerce_cart_item_thumbnail', array('\A3Rev\WCDynamicGallery\Variations', 'change_image_in_cart_page'), 50, 3 );

add_action( 'wp', 'setup_dynamic_gallery', 20);
function setup_dynamic_gallery() {
	global $post;
	$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
	if ( is_singular( array( 'product' ) ) || (! empty( $post->post_content ) && stristr($post->post_content, '[product_page') !== false ) ) {
		$global_wc_dgallery_activate = get_option( WOO_DYNAMIC_GALLERY_PREFIX.'activate' );
		$actived_d_gallery = get_post_meta($post->ID, '_actived_d_gallery',true);

		if ($actived_d_gallery == '' && $global_wc_dgallery_activate != 'no') {
			$actived_d_gallery = 1;
		}

		if($actived_d_gallery == 1){
			\A3Rev\WCDynamicGallery\Main::init_dynamic_gallery( $post->ID, true );
		}
	}
}

add_filter( 'wc_get_template_part', function( $template, $slug, $name ) {
	if ( 'content' !== $slug || 'single-product' !== $name ) return $template;

	global $post;
	if ( is_singular( array( 'product' ) ) || (! empty( $post->post_content ) && stristr($post->post_content, '[product_page') !== false ) ) {
		$global_wc_dgallery_activate = get_option( WOO_DYNAMIC_GALLERY_PREFIX.'activate' );
		$actived_d_gallery = get_post_meta($post->ID, '_actived_d_gallery',true);

		if ($actived_d_gallery == '' && $global_wc_dgallery_activate != 'no') {
			$actived_d_gallery = 1;
		}

		if($actived_d_gallery == 1){
			remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);
			remove_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20 );
			add_action( 'woocommerce_before_single_product_summary', 'wc_dynamic_gallery_show', 30);
		}

		if ( in_array( 'woocommerce-professor-cloud/woocommerce-professor-cloud.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) && get_option('woocommerce_cloud_enableCloud') == 'true' ) :
				remove_action( 'woocommerce_before_single_product_summary', 'wc_dynamic_gallery_show', 30);
		endif;
	}

	return $template;
}, 10, 3 );

// Check upgrade functions
add_action('init', 'woo_dgallery_lite_upgrade_plugin');
function woo_dgallery_lite_upgrade_plugin () {

	// Upgrade to 1.5.0
	if( version_compare(get_option('a3rev_woo_dgallery_lite_version'), '1.5.0') === -1 ){
		update_option('a3rev_woo_dgallery_lite_version', '1.5.0');

		// Build sass
		$GLOBALS[WOO_DYNAMIC_GALLERY_PREFIX.'less']->plugin_build_sass();
	}

	// Upgrade to 1.6.0
	if ( version_compare(get_option('a3rev_woo_dgallery_lite_version'), '1.6.0') === -1 ) {
		update_option('a3rev_woo_dgallery_lite_version', '1.6.0');
		include( WOO_DYNAMIC_GALLERY_FILE_PATH. '/includes/updates/update-1.6.0.php' );
	}

	// Upgrade to 1.8.0
	if ( version_compare(get_option('a3rev_woo_dgallery_lite_version'), '1.8.0') === -1 ) {
		update_option('a3rev_woo_dgallery_lite_version', '1.8.0');
		include( WOO_DYNAMIC_GALLERY_FILE_PATH. '/includes/updates/update-1.8.0.php' );
	}

	// Upgrade to 2.0.0
	if ( version_compare(get_option('a3rev_woo_dgallery_lite_version'), '2.0.0') === -1 ) {
		update_option('a3rev_woo_dgallery_lite_version', '2.0.0');
		include( WOO_DYNAMIC_GALLERY_FILE_PATH. '/includes/updates/update-2.0.0.php' );
	}

	// Upgrade to 2.1.0
	if ( version_compare(get_option('a3rev_woo_dgallery_lite_version'), '2.1.0') === -1 ) {
		update_option('a3rev_woo_dgallery_lite_version', '2.1.0');
		update_option('wc_dgallery_lite_clean_on_deletion', 'no');
		update_option('a3_dynamic_gallery_db_updated', 'no');
	}

	// Upgrade to 2.1.1
	if ( version_compare(get_option('a3rev_woo_dgallery_lite_version'), '2.2.0') === -1 ) {
		update_option('a3rev_woo_dgallery_lite_version', '2.2.0');
		update_option('woo_dynamic_gallery_style_version', time() );
	}

	// Upgrade to 2.3.0
	if( version_compare(get_option('a3rev_woo_dgallery_lite_version'), '2.3.0') === -1 ){
		update_option('a3rev_woo_dgallery_lite_version', '2.3.0');

		// Build sass
		$GLOBALS[WOO_DYNAMIC_GALLERY_PREFIX.'less']->plugin_build_sass();
	}

	if( version_compare( get_option('a3rev_woo_dgallery_lite_version'), '2.5.8', '<' ) ){
		update_option('a3rev_woo_dgallery_lite_version', '2.5.8');

		// Build sass
		$GLOBALS[WOO_DYNAMIC_GALLERY_PREFIX.'less']->plugin_build_sass();
	}

	update_option('a3rev_woo_dgallery_lite_version', WOO_DYNAMIC_GALLERY_VERSION );
}
