<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function inone_properties_should_enqueue_assets(): bool {
	if ( is_admin() ) {
		return false;
	}

	if ( is_post_type_archive( INONE_PROPERTIES_POST_TYPE ) || is_singular( INONE_PROPERTIES_POST_TYPE ) || is_tax( INONE_PROPERTIES_TAX_LOCATION ) || is_tax( INONE_PROPERTIES_TAX_CATEGORY ) ) {
		return true;
	}

	$post = get_post();
	if ( $post instanceof WP_Post ) {
		if ( has_shortcode( $post->post_content, 'featured_properties' ) ) {
			return true;
		}
	}

	return false;
}

function inone_properties_enqueue_assets(): void {
	if ( ! inone_properties_should_enqueue_assets() ) {
		return;
	}

	wp_enqueue_style(
		'inone-properties',
		INONE_PROPERTIES_URL . 'assets/css/properties.css',
		array(),
		INONE_PROPERTIES_VERSION
	);

	wp_enqueue_script(
		'inone-properties-filters',
		INONE_PROPERTIES_URL . 'assets/js/properties-filters.js',
		array(),
		INONE_PROPERTIES_VERSION,
		true
	);

	$bounds = inone_properties_get_price_bounds();

	wp_add_inline_script(
		'inone-properties-filters',
		'window.InoneProperties = ' . wp_json_encode(
			array(
				'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
				'nonce'         => wp_create_nonce( 'inone_properties_ajax' ),
				'archiveUrl'    => get_post_type_archive_link( INONE_PROPERTIES_POST_TYPE ),
				'priceMinBound' => (int) $bounds['min'],
				'priceMaxBound' => (int) $bounds['max'],
			)
		) . ';',
		'before'
	);
}
add_action( 'wp_enqueue_scripts', 'inone_properties_enqueue_assets' );
