<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function inone_properties_ajax_search(): void {
	$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'inone_properties_ajax' ) ) {
		wp_send_json_error(
			array(
				'message' => 'Invalid nonce.',
			),
			403
		);
	}

	$filters = inone_properties_parse_filters( $_POST );
	$args    = inone_properties_build_query_args( $filters );
	$query   = new WP_Query( $args );

	ob_start();

	if ( $query->have_posts() ) {
		echo '<div class="inone-properties-grid" role="list">';
		while ( $query->have_posts() ) {
			$query->the_post();
			echo '<div class="inone-properties-grid__item" role="listitem">';
			inone_properties_render_property_card( get_the_ID() );
			echo '</div>';
		}
		echo '</div>';
	} else {
		echo '<div class="inone-properties-empty">No properties found.</div>';
	}

	wp_reset_postdata();

	$html = ob_get_clean();

	wp_send_json_success(
		array(
			'html'       => $html,
			'foundPosts' => (int) $query->found_posts,
			'maxPages'   => (int) $query->max_num_pages,
			'current'    => (int) $filters['paged'],
		)
	);
}
add_action( 'wp_ajax_inone_properties_search', 'inone_properties_ajax_search' );
add_action( 'wp_ajax_nopriv_inone_properties_search', 'inone_properties_ajax_search' );

