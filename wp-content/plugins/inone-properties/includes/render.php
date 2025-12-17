<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Read an ACF field with a safe fallback to post meta.
 *
 * @return mixed
 */
function inone_properties_get_field( string $field_name, int $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();

	if ( function_exists( 'get_field' ) ) {
		return get_field( $field_name, $post_id );
	}

	return get_post_meta( $post_id, $field_name, true );
}

function inone_properties_format_price( $price ): string {
	if ( $price === null || $price === '' ) {
		return '';
	}

	$price = is_numeric( $price ) ? (float) $price : 0;
	if ( $price <= 0 ) {
		return '';
	}

	$formatted = number_format_i18n( $price, 0 );
	return '$' . $formatted;
}

function inone_properties_is_featured( int $post_id ): bool {
	$value = inone_properties_get_field( 'featured', $post_id );
	return (bool) $value;
}

function inone_properties_render_breadcrumbs( array $items ): void {
	if ( empty( $items ) ) {
		return;
	}

	echo '<nav class="inone-properties-breadcrumbs" aria-label="Breadcrumbs"><ol>';
	foreach ( $items as $index => $item ) {
		$is_last = ( $index === array_key_last( $items ) );
		$label   = isset( $item['label'] ) ? (string) $item['label'] : '';
		$url     = isset( $item['url'] ) ? (string) $item['url'] : '';

		echo '<li class="inone-properties-breadcrumbs__item">';
		if ( ! $is_last && $url ) {
			printf(
				'<a href="%s">%s</a>',
				esc_url( $url ),
				esc_html( $label )
			);
		} else {
			echo '<span aria-current="page">' . esc_html( $label ) . '</span>';
		}
		echo '</li>';
	}
	echo '</ol></nav>';
}

/**
 * Render a property card (used in archive results and shortcodes).
 */
function inone_properties_render_property_card( int $post_id ): void {
	$title = get_the_title( $post_id );
	$link  = get_permalink( $post_id );

	$type     = (string) inone_properties_get_field( 'property_type', $post_id );
	$status   = (string) inone_properties_get_field( 'status', $post_id );
	$price    = inone_properties_format_price( inone_properties_get_field( 'price', $post_id ) );
	$bedrooms = inone_properties_get_field( 'bedrooms', $post_id );
	$baths    = inone_properties_get_field( 'bathrooms', $post_id );

	$location_terms = get_the_terms( $post_id, INONE_PROPERTIES_TAX_LOCATION );
	$location_name  = ( is_array( $location_terms ) && ! empty( $location_terms ) ) ? $location_terms[0]->name : '';

	$is_featured = inone_properties_is_featured( $post_id );

	echo '<article class="inone-properties-card">';
	echo '<a class="inone-properties-card__thumb" href="' . esc_url( $link ) . '">';
	if ( has_post_thumbnail( $post_id ) ) {
		echo get_the_post_thumbnail( $post_id, 'medium_large' );
	} else {
		echo '<div class="inone-properties-card__thumb--placeholder" aria-hidden="true"></div>';
	}
	if ( $is_featured ) {
		echo '<span class="inone-properties-badge" aria-label="Featured property">Featured</span>';
	}
	echo '</a>';

	echo '<div class="inone-properties-card__body">';
	printf( '<h3 class="inone-properties-card__title"><a href="%s">%s</a></h3>', esc_url( $link ), esc_html( $title ) );

	if ( $price ) {
		echo '<div class="inone-properties-card__price">' . esc_html( $price ) . '</div>';
	}

	echo '<ul class="inone-properties-card__meta">';
	if ( $type ) {
		echo '<li>' . esc_html( $type ) . '</li>';
	}
	if ( $status ) {
		echo '<li>' . esc_html( $status ) . '</li>';
	}
	if ( $location_name ) {
		echo '<li>' . esc_html( $location_name ) . '</li>';
	}
	if ( $bedrooms !== '' && $bedrooms !== null ) {
		echo '<li>' . esc_html( (int) $bedrooms ) . ' bd</li>';
	}
	if ( $baths !== '' && $baths !== null ) {
		echo '<li>' . esc_html( (int) $baths ) . ' ba</li>';
	}
	echo '</ul>';

	echo '</div>';
	echo '</article>';
}

function inone_properties_render_social_share( int $post_id ): void {
	$url   = get_permalink( $post_id );
	$title = get_the_title( $post_id );

	$share = array(
		array(
			'key'   => 'facebook',
			'label' => 'Facebook',
			'url'   => 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode( $url ),
		),
		array(
			'key'   => 'x',
			'label' => 'X',
			'url'   => 'https://twitter.com/intent/tweet?url=' . rawurlencode( $url ) . '&text=' . rawurlencode( $title ),
		),
		array(
			'key'   => 'linkedin',
			'label' => 'LinkedIn',
			'url'   => 'https://www.linkedin.com/sharing/share-offsite/?url=' . rawurlencode( $url ),
		),
		array(
			'key'   => 'email',
			'label' => 'Email',
			'url'   => 'mailto:?subject=' . rawurlencode( $title ) . '&body=' . rawurlencode( $url ),
		),
	);

	echo '<div class="inone-properties-share" aria-label="Share this property">';
	echo '<span class="inone-properties-share__label">Share:</span>';
	foreach ( $share as $item ) {
		$key        = isset( $item['key'] ) ? (string) $item['key'] : '';
		$label      = isset( $item['label'] ) ? (string) $item['label'] : '';
		$target_url = isset( $item['url'] ) ? (string) $item['url'] : '';

		if ( ! $key || ! $label || ! $target_url ) {
			continue;
		}

		$icon = inone_properties_get_share_icon_svg( $key );
		$aria = sprintf( 'Share on %s', $label );

		printf(
			'<a class="inone-properties-share__link inone-properties-share__link--%1$s" href="%2$s" target="_blank" rel="noopener noreferrer" aria-label="%3$s" title="%3$s">%4$s<span class="inone-properties-share__text">%5$s</span></a>',
			esc_attr( $key ),
			esc_url( $target_url ),
			esc_attr( $aria ),
			$icon,
			esc_html( $label )
		);
	}
	echo '</div>';
}

function inone_properties_get_share_icon_svg( string $key ): string {
	$common = 'class="inone-properties-share__icon" aria-hidden="true" focusable="false" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"';

	switch ( $key ) {
		case 'facebook':
			return '<svg ' . $common . '><path d="M14 8.5h2.2V6H14c-2.3 0-4 1.8-4 4v2H8v3h2v6h3v-6h2.4l.6-3H13v-2c0-.9.3-1.5 1-1.5z"/></svg>';
		case 'x':
			return '<svg ' . $common . '><path d="M18.7 2H22l-7.2 8.3L22.5 22h-6.2l-4.8-6.4L6 22H2.7l7.8-9L2 2h6.3l4.3 5.7L18.7 2z"/></svg>';
		case 'linkedin':
			return '<svg ' . $common . '><path d="M4.5 3.5C3.7 3.5 3 4.2 3 5s.7 1.5 1.5 1.5S6 5.8 6 5s-.7-1.5-1.5-1.5zM3.2 8h2.6v12H3.2V8zm6 0h2.5v1.6h.1c.4-.7 1.5-1.8 3.2-1.8 3.4 0 4 2.2 4 5v7.2h-2.6v-6.4c0-1.5 0-3.5-2.2-3.5s-2.5 1.6-2.5 3.4V20H9.2V8z"/></svg>';
		case 'email':
			return '<svg ' . $common . '><path d="M20 5H4c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 4-8 5L4 9V7l8 5 8-5v2z"/></svg>';
		default:
			return '';
	}
}
