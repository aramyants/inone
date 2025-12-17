<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function inone_properties_featured_properties_shortcode( array $atts ): string {
	$atts = shortcode_atts(
		array(
			'limit'   => 3,
			'columns' => 3,
			'order'   => 'DESC',
			'orderby' => 'date',
		),
		$atts,
		'featured_properties'
	);

	$limit   = max( 1, min( 24, (int) $atts['limit'] ) );
	$columns = max( 1, min( 6, (int) $atts['columns'] ) );

	$order = strtoupper( (string) $atts['order'] );
	if ( ! in_array( $order, array( 'ASC', 'DESC' ), true ) ) {
		$order = 'DESC';
	}

	$orderby = (string) $atts['orderby'];
	$allowed = array( 'date', 'title', 'rand', 'meta_value_num' );
	if ( ! in_array( $orderby, $allowed, true ) ) {
		$orderby = 'date';
	}

	$args = array(
		'post_type'      => INONE_PROPERTIES_POST_TYPE,
		'post_status'    => 'publish',
		'posts_per_page' => $limit,
		'orderby'        => $orderby,
		'order'          => $order,
		'meta_query'     => array(
			array(
				'key'     => 'featured',
				'value'   => '1',
				'compare' => '=',
			),
		),
	);

	if ( $orderby === 'meta_value_num' ) {
		$args['meta_key'] = 'price';
	}

	$query = new WP_Query( $args );

	ob_start();
	echo '<div class="inone-featured-properties inone-featured-properties--cols-' . esc_attr( (string) $columns ) . '">';
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			echo '<div class="inone-featured-properties__item">';
			inone_properties_render_property_card( get_the_ID() );
			echo '</div>';
		}
	} else {
		echo '<div class="inone-properties-empty">No featured properties found.</div>';
	}
	echo '</div>';
	wp_reset_postdata();

	return (string) ob_get_clean();
}
add_shortcode( 'featured_properties', 'inone_properties_featured_properties_shortcode' );
