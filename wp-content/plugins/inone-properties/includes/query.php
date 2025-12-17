<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Parse filters from a request array (GET/POST).
 */
function inone_properties_parse_filters( array $source ): array {
	$filters = array(
		'keyword'       => isset( $source['keyword'] ) ? sanitize_text_field( wp_unslash( $source['keyword'] ) ) : '',
		'type'          => isset( $source['type'] ) ? sanitize_text_field( wp_unslash( $source['type'] ) ) : '',
		'status'        => isset( $source['status'] ) ? sanitize_text_field( wp_unslash( $source['status'] ) ) : '',
		'min_price'     => isset( $source['min_price'] ) ? (int) $source['min_price'] : 0,
		'max_price'     => isset( $source['max_price'] ) ? (int) $source['max_price'] : 0,
		'bedrooms_min'  => isset( $source['bedrooms_min'] ) ? (int) $source['bedrooms_min'] : 0,
		'bathrooms_min' => isset( $source['bathrooms_min'] ) ? (int) $source['bathrooms_min'] : 0,
		'location'      => isset( $source['location'] ) ? (int) $source['location'] : 0,
		'sort'          => isset( $source['sort'] ) ? sanitize_text_field( wp_unslash( $source['sort'] ) ) : '',
		'paged'         => isset( $source['paged'] ) ? max( 1, (int) $source['paged'] ) : 1,
	);

	if ( $filters['min_price'] < 0 ) {
		$filters['min_price'] = 0;
	}

	if ( $filters['max_price'] < 0 ) {
		$filters['max_price'] = 0;
	}

	return $filters;
}

/**
 * Build WP_Query args from filters.
 */
function inone_properties_build_query_args( array $filters, array $overrides = array() ): array {
	$meta_query = array( 'relation' => 'AND' );
	$tax_query  = array();

	if ( ! empty( $filters['type'] ) ) {
		$meta_query[] = array(
			'key'     => 'property_type',
			'value'   => $filters['type'],
			'compare' => '=',
		);
	}

	if ( ! empty( $filters['status'] ) ) {
		$meta_query[] = array(
			'key'     => 'status',
			'value'   => $filters['status'],
			'compare' => '=',
		);
	}

	if ( ! empty( $filters['bedrooms_min'] ) ) {
		$meta_query[] = array(
			'key'     => 'bedrooms',
			'value'   => $filters['bedrooms_min'],
			'type'    => 'NUMERIC',
			'compare' => '>=',
		);
	}

	if ( ! empty( $filters['bathrooms_min'] ) ) {
		$meta_query[] = array(
			'key'     => 'bathrooms',
			'value'   => $filters['bathrooms_min'],
			'type'    => 'NUMERIC',
			'compare' => '>=',
		);
	}

	if ( ! empty( $filters['min_price'] ) || ! empty( $filters['max_price'] ) ) {
		if ( ! empty( $filters['min_price'] ) && ! empty( $filters['max_price'] ) ) {
			$meta_query[] = array(
				'key'     => 'price',
				'value'   => array( $filters['min_price'], $filters['max_price'] ),
				'type'    => 'NUMERIC',
				'compare' => 'BETWEEN',
			);
		} elseif ( ! empty( $filters['min_price'] ) ) {
			$meta_query[] = array(
				'key'     => 'price',
				'value'   => $filters['min_price'],
				'type'    => 'NUMERIC',
				'compare' => '>=',
			);
		} elseif ( ! empty( $filters['max_price'] ) ) {
			$meta_query[] = array(
				'key'     => 'price',
				'value'   => $filters['max_price'],
				'type'    => 'NUMERIC',
				'compare' => '<=',
			);
		}
	}

	if ( ! empty( $filters['location'] ) ) {
		$tax_query[] = array(
			'taxonomy' => INONE_PROPERTIES_TAX_LOCATION,
			'field'    => 'term_id',
			'terms'    => array( $filters['location'] ),
		);
	}

	$order_by = 'date';
	$order    = 'DESC';
	$meta_key = '';

	switch ( $filters['sort'] ) {
		case 'price_asc':
			$order_by = 'meta_value_num';
			$order    = 'ASC';
			$meta_key = 'price';
			break;
		case 'price_desc':
			$order_by = 'meta_value_num';
			$order    = 'DESC';
			$meta_key = 'price';
			break;
		case 'oldest':
			$order_by = 'date';
			$order    = 'ASC';
			break;
		case 'newest':
		default:
			$order_by = 'date';
			$order    = 'DESC';
			break;
	}

	$args = array(
		'post_type'           => INONE_PROPERTIES_POST_TYPE,
		'post_status'         => 'publish',
		'posts_per_page'      => 9,
		'paged'               => max( 1, (int) $filters['paged'] ),
		'orderby'             => $order_by,
		'order'               => $order,
		'meta_query'          => count( $meta_query ) > 1 ? $meta_query : array(),
		'tax_query'           => $tax_query,
		'no_found_rows'       => false,
		'ignore_sticky_posts' => true,
		'inone_keyword'       => $filters['keyword'],
	);

	if ( $meta_key ) {
		$args['meta_key'] = $meta_key;
	}

	return array_merge( $args, $overrides );
}

/**
 * Extend keyword search to include the property address meta field.
 *
 * Note: We intentionally do not use WP_Query's "s" because it would combine with other
 * meta_query clauses in a way that's hard to OR with the address field.
 */
function inone_properties_keyword_search_join( string $join, WP_Query $query ): string {
	global $wpdb;

	$keyword = (string) $query->get( 'inone_keyword' );
	if ( $keyword === '' ) {
		return $join;
	}

	$post_type = $query->get( 'post_type' );
	if ( ( is_string( $post_type ) && $post_type !== INONE_PROPERTIES_POST_TYPE ) || ( is_array( $post_type ) && ! in_array( INONE_PROPERTIES_POST_TYPE, $post_type, true ) ) ) {
		return $join;
	}

	if ( strpos( $join, 'inone_pm_address' ) !== false ) {
		return $join;
	}

	return $join . " LEFT JOIN {$wpdb->postmeta} inone_pm_address ON ({$wpdb->posts}.ID = inone_pm_address.post_id AND inone_pm_address.meta_key = 'address') ";
}
add_filter( 'posts_join', 'inone_properties_keyword_search_join', 10, 2 );

function inone_properties_keyword_search_where( string $where, WP_Query $query ): string {
	global $wpdb;

	$keyword = (string) $query->get( 'inone_keyword' );
	if ( $keyword === '' ) {
		return $where;
	}

	$post_type = $query->get( 'post_type' );
	if ( ( is_string( $post_type ) && $post_type !== INONE_PROPERTIES_POST_TYPE ) || ( is_array( $post_type ) && ! in_array( INONE_PROPERTIES_POST_TYPE, $post_type, true ) ) ) {
		return $where;
	}

	$like = '%' . $wpdb->esc_like( $keyword ) . '%';
	$where .= $wpdb->prepare(
		" AND ( {$wpdb->posts}.post_title LIKE %s OR inone_pm_address.meta_value LIKE %s ) ",
		$like,
		$like
	);

	return $where;
}
add_filter( 'posts_where', 'inone_properties_keyword_search_where', 10, 2 );

function inone_properties_keyword_search_distinct( string $distinct, WP_Query $query ): string {
	$keyword = (string) $query->get( 'inone_keyword' );
	if ( $keyword === '' ) {
		return $distinct;
	}

	$post_type = $query->get( 'post_type' );
	if ( ( is_string( $post_type ) && $post_type !== INONE_PROPERTIES_POST_TYPE ) || ( is_array( $post_type ) && ! in_array( INONE_PROPERTIES_POST_TYPE, $post_type, true ) ) ) {
		return $distinct;
	}

	return 'DISTINCT';
}
add_filter( 'posts_distinct', 'inone_properties_keyword_search_distinct', 10, 2 );

/**
 * Get min/max price for slider bounds.
 */
function inone_properties_get_price_bounds(): array {
	$min = 0;
	$max = 0;

	$min_query = new WP_Query(
		array(
			'post_type'      => INONE_PROPERTIES_POST_TYPE,
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => 'price',
			'orderby'        => 'meta_value_num',
			'order'          => 'ASC',
		)
	);
	if ( ! empty( $min_query->posts ) ) {
		$min = (int) get_post_meta( $min_query->posts[0], 'price', true );
	}

	$max_query = new WP_Query(
		array(
			'post_type'      => INONE_PROPERTIES_POST_TYPE,
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => 'price',
			'orderby'        => 'meta_value_num',
			'order'          => 'DESC',
		)
	);
	if ( ! empty( $max_query->posts ) ) {
		$max = (int) get_post_meta( $max_query->posts[0], 'price', true );
	}

	if ( $min < 0 ) {
		$min = 0;
	}

	if ( $max < 0 ) {
		$max = 0;
	}

	if ( $max < $min ) {
		$max = $min;
	}

	return array(
		'min' => $min,
		'max' => $max,
	);
}
