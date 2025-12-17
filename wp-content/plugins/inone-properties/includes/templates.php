<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Load plugin templates for properties pages (works with block themes too).
 */
function inone_properties_template_include( string $template ): string {
	if ( is_post_type_archive( INONE_PROPERTIES_POST_TYPE ) ) {
		$theme_template = locate_template(
			array(
				'archive-' . INONE_PROPERTIES_POST_TYPE . '.php',
			)
		);
		if ( $theme_template ) {
			return $theme_template;
		}
		return INONE_PROPERTIES_PATH . 'templates/archive-properties.php';
	}

	if ( is_singular( INONE_PROPERTIES_POST_TYPE ) ) {
		$theme_template = locate_template(
			array(
				'single-' . INONE_PROPERTIES_POST_TYPE . '.php',
			)
		);
		if ( $theme_template ) {
			return $theme_template;
		}
		return INONE_PROPERTIES_PATH . 'templates/single-properties.php';
	}

	if ( is_tax( INONE_PROPERTIES_TAX_LOCATION ) ) {
		$theme_template = locate_template(
			array(
				'taxonomy-' . INONE_PROPERTIES_TAX_LOCATION . '.php',
			)
		);
		if ( $theme_template ) {
			return $theme_template;
		}
		return INONE_PROPERTIES_PATH . 'templates/taxonomy-property-location.php';
	}

	if ( is_tax( INONE_PROPERTIES_TAX_CATEGORY ) ) {
		$theme_template = locate_template(
			array(
				'taxonomy-' . INONE_PROPERTIES_TAX_CATEGORY . '.php',
			)
		);
		if ( $theme_template ) {
			return $theme_template;
		}
		return INONE_PROPERTIES_PATH . 'templates/taxonomy-property-category.php';
	}

	return $template;
}
add_filter( 'template_include', 'inone_properties_template_include', 50 );

function inone_properties_render_document_open(): void {
	?>
	<!doctype html>
	<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>
	<?php
	wp_body_open();
}

function inone_properties_render_document_close(): void {
	wp_footer();
	?>
	</body>
	</html>
	<?php
}

function inone_properties_render_site_header(): void {
	if ( function_exists( 'wp_is_block_theme' ) && wp_is_block_theme() && function_exists( 'block_template_part' ) ) {
		block_template_part( 'header' );
		return;
	}

	get_header();
}

function inone_properties_render_site_footer(): void {
	if ( function_exists( 'wp_is_block_theme' ) && wp_is_block_theme() && function_exists( 'block_template_part' ) ) {
		block_template_part( 'footer' );
		return;
	}

	get_footer();
}
