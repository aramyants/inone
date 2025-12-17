<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function inone_properties_register_seeder_page(): void {
	add_management_page(
		__( 'InOne Properties Seeder', 'inone-properties' ),
		__( 'InOne Properties Seeder', 'inone-properties' ),
		'manage_options',
		'inone-properties-seeder',
		'inone_properties_render_seeder_page'
	);
}
add_action( 'admin_menu', 'inone_properties_register_seeder_page' );

function inone_properties_render_seeder_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Sorry, you are not allowed to access this page.', 'inone-properties' ) );
	}

	$status  = isset( $_GET['inone_seed_status'] ) ? sanitize_text_field( wp_unslash( $_GET['inone_seed_status'] ) ) : '';
	$message = isset( $_GET['inone_seed_message'] ) ? sanitize_text_field( wp_unslash( $_GET['inone_seed_message'] ) ) : '';

	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'InOne Properties Seeder', 'inone-properties' ); ?></h1>

		<?php if ( $status === 'success' && $message ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php echo esc_html( $message ); ?></p></div>
		<?php endif; ?>

		<p><?php esc_html_e( 'Generate sample Properties posts for testing filters, templates, and the featured shortcode.', 'inone-properties' ); ?></p>

		<h2><?php esc_html_e( 'Generate seed data', 'inone-properties' ); ?></h2>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<?php wp_nonce_field( 'inone_properties_seed_generate', 'inone_properties_seed_generate_nonce' ); ?>
			<input type="hidden" name="action" value="inone_properties_seed_generate">

			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row"><label for="inone-seed-count"><?php esc_html_e( 'How many properties?', 'inone-properties' ); ?></label></th>
						<td><input id="inone-seed-count" type="number" min="1" max="50" name="count" value="12" class="small-text"></td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Options', 'inone-properties' ); ?></th>
						<td>
							<label><input type="checkbox" name="with_media" value="1" checked> <?php esc_html_e( 'Create placeholder images (featured image + gallery)', 'inone-properties' ); ?></label><br>
							<label><input type="checkbox" name="reset_first" value="1"> <?php esc_html_e( 'Delete previously seeded properties first', 'inone-properties' ); ?></label>
						</td>
					</tr>
				</tbody>
			</table>

			<p><button type="submit" class="button button-primary"><?php esc_html_e( 'Generate Seed Data', 'inone-properties' ); ?></button></p>
		</form>

		<hr>

		<h2><?php esc_html_e( 'Delete seeded data', 'inone-properties' ); ?></h2>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<?php wp_nonce_field( 'inone_properties_seed_delete', 'inone_properties_seed_delete_nonce' ); ?>
			<input type="hidden" name="action" value="inone_properties_seed_delete">
			<p>
				<label><input type="checkbox" name="delete_media" value="1"> <?php esc_html_e( 'Also delete placeholder images created by the seeder', 'inone-properties' ); ?></label>
			</p>
			<p><button type="submit" class="button"><?php esc_html_e( 'Delete Seed Data', 'inone-properties' ); ?></button></p>
		</form>
	</div>
	<?php
}

function inone_properties_seed_redirect( string $message ): void {
	wp_safe_redirect(
		add_query_arg(
			array(
				'page'              => 'inone-properties-seeder',
				'inone_seed_status' => 'success',
				'inone_seed_message' => $message,
			),
			admin_url( 'tools.php' )
		)
	);
	exit;
}

function inone_properties_seed_get_or_create_terms(): array {
	$locations = array();
	$categories = array();

	$cities = array(
		'Yerevan'  => array( 'Kentron', 'Arabkir', 'Ajapnyak' ),
		'Gyumri'   => array( 'Center', 'Ani' ),
		'Vanadzor' => array( 'Taron', 'Shahumyan' ),
	);

	foreach ( $cities as $city => $neighborhoods ) {
		$city_result = term_exists( $city, INONE_PROPERTIES_TAX_LOCATION );
		if ( ! $city_result ) {
			$city_result = wp_insert_term( $city, INONE_PROPERTIES_TAX_LOCATION );
		}

		$city_id = is_array( $city_result ) ? (int) $city_result['term_id'] : (int) $city_result;
		if ( $city_id <= 0 ) {
			continue;
		}

		foreach ( $neighborhoods as $neighborhood ) {
			$child_result = term_exists( $neighborhood, INONE_PROPERTIES_TAX_LOCATION, $city_id );
			if ( ! $child_result ) {
				$child_result = wp_insert_term(
					$neighborhood,
					INONE_PROPERTIES_TAX_LOCATION,
					array( 'parent' => $city_id )
				);
			}

			$child_id = is_array( $child_result ) ? (int) $child_result['term_id'] : (int) $child_result;
			if ( $child_id > 0 ) {
				$locations[] = $child_id;
			}
		}
	}

	$category_tree = array(
		'Residential' => array( 'House', 'Apartment', 'Condo', 'Townhouse' ),
	);

	foreach ( $category_tree as $parent_name => $children ) {
		$parent_result = term_exists( $parent_name, INONE_PROPERTIES_TAX_CATEGORY );
		if ( ! $parent_result ) {
			$parent_result = wp_insert_term( $parent_name, INONE_PROPERTIES_TAX_CATEGORY );
		}

		$parent_id = is_array( $parent_result ) ? (int) $parent_result['term_id'] : (int) $parent_result;
		if ( $parent_id <= 0 ) {
			continue;
		}

		foreach ( $children as $child_name ) {
			$child_result = term_exists( $child_name, INONE_PROPERTIES_TAX_CATEGORY, $parent_id );
			if ( ! $child_result ) {
				$child_result = wp_insert_term(
					$child_name,
					INONE_PROPERTIES_TAX_CATEGORY,
					array( 'parent' => $parent_id )
				);
			}

			$child_id = is_array( $child_result ) ? (int) $child_result['term_id'] : (int) $child_result;
			if ( $child_id > 0 ) {
				$categories[ $child_name ] = $child_id;
			}
		}
	}

	return array(
		'locations'  => array_values( array_unique( $locations ) ),
		'categories' => $categories,
	);
}

function inone_properties_seed_create_placeholder_images(): array {
	$existing = get_option( 'inone_properties_seed_attachment_ids', array() );
	if ( is_array( $existing ) ) {
		$existing = array_values(
			array_filter(
				array_map( 'intval', $existing ),
				static function ( int $id ): bool {
					return $id > 0 && get_post_type( $id ) === 'attachment';
				}
			)
		);
	}

	if ( is_array( $existing ) && count( $existing ) >= 3 ) {
		return array_slice( $existing, 0, 3 );
	}

	$png = base64_decode(
		'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO3f7uQAAAAASUVORK5CYII=',
		true
	);
	if ( ! is_string( $png ) ) {
		return array();
	}

	$names = array( 'inone-property-1.png', 'inone-property-2.png', 'inone-property-3.png' );
	$ids   = array();

	foreach ( $names as $name ) {
		$upload = wp_upload_bits( $name, null, $png );
		if ( ! empty( $upload['error'] ) || empty( $upload['file'] ) ) {
			continue;
		}

		$filetype = wp_check_filetype( $upload['file'], null );
		$attachment = array(
			'post_mime_type' => $filetype['type'] ? $filetype['type'] : 'image/png',
			'post_title'     => sanitize_file_name( pathinfo( $name, PATHINFO_FILENAME ) ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		);

		$attach_id = wp_insert_attachment( $attachment, $upload['file'] );
		if ( is_wp_error( $attach_id ) ) {
			continue;
		}

		update_post_meta( $attach_id, '_inone_seed_asset', 1 );

		if ( file_exists( $upload['file'] ) ) {
			require_once ABSPATH . 'wp-admin/includes/image.php';
			$meta = wp_generate_attachment_metadata( $attach_id, $upload['file'] );
			if ( is_array( $meta ) ) {
				wp_update_attachment_metadata( $attach_id, $meta );
			}
		}

		$ids[] = (int) $attach_id;
	}

	if ( $ids ) {
		update_option( 'inone_properties_seed_attachment_ids', $ids, false );
	}

	return $ids;
}

function inone_properties_seed_delete_seeded_posts(): int {
	$posts = get_posts(
		array(
			'post_type'      => INONE_PROPERTIES_POST_TYPE,
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'meta_query'     => array(
				array(
					'key'   => '_inone_seed',
					'value' => '1',
				),
			),
		)
	);

	$deleted = 0;
	foreach ( $posts as $post_id ) {
		$result = wp_delete_post( (int) $post_id, true );
		if ( $result ) {
			$deleted++;
		}
	}

	return $deleted;
}

function inone_properties_handle_seed_generate(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Sorry, you are not allowed to do that.', 'inone-properties' ) );
	}

	$nonce = isset( $_POST['inone_properties_seed_generate_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['inone_properties_seed_generate_nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'inone_properties_seed_generate' ) ) {
		wp_die( esc_html__( 'Invalid nonce.', 'inone-properties' ) );
	}

	$count       = isset( $_POST['count'] ) ? (int) $_POST['count'] : 12;
	$count       = max( 1, min( 50, $count ) );
	$with_media  = isset( $_POST['with_media'] ) && (string) wp_unslash( $_POST['with_media'] ) === '1';
	$reset_first = isset( $_POST['reset_first'] ) && (string) wp_unslash( $_POST['reset_first'] ) === '1';

	if ( $reset_first ) {
		inone_properties_seed_delete_seeded_posts();
	}

	$terms = inone_properties_seed_get_or_create_terms();
	$location_ids = $terms['locations'];
	$category_ids = $terms['categories'];

	$attachments = $with_media ? inone_properties_seed_create_placeholder_images() : array();

	$types   = array( 'House', 'Apartment', 'Condo', 'Townhouse' );
	$statuses = array( 'For Sale', 'For Rent', 'Sold', 'Rented' );
	$features = array( 'Pool', 'Garage', 'Garden', 'Fireplace', 'Air Conditioning', 'Heating', 'Balcony', 'Basement' );

	$created = 0;
	for ( $i = 1; $i <= $count; $i++ ) {
		$type   = $types[ array_rand( $types ) ];
		$status = $statuses[ array_rand( $statuses ) ];

		$beds  = random_int( 1, 5 );
		$baths = random_int( 1, 4 );
		$sqft  = random_int( 600, 4200 );
		$year  = random_int( 1980, (int) gmdate( 'Y' ) );

		$is_rent = ( $status === 'For Rent' || $status === 'Rented' );
		$price   = $is_rent ? random_int( 900, 4500 ) : random_int( 90000, 1800000 );

		$city_state = array(
			array( 'Yerevan', 'Armenia' ),
			array( 'Gyumri', 'Armenia' ),
			array( 'Vanadzor', 'Armenia' ),
		);
		$pair    = $city_state[ array_rand( $city_state ) ];
		$city    = $pair[0];
		$state   = $pair[1];
		$zip     = (string) random_int( 10000, 99999 );
		$address = sprintf( '%d %s St', random_int( 10, 999 ), $type );

		$title = sprintf( '%s in %s (%d bd)', $type, $city, $beds );

		$post_id = wp_insert_post(
			array(
				'post_type'    => INONE_PROPERTIES_POST_TYPE,
				'post_status'  => 'publish',
				'post_title'   => $title,
				'post_content' => sprintf( '<p>%s</p>', esc_html( 'A beautiful sample property listing generated by the seeder.' ) ),
			),
			true
		);

		if ( is_wp_error( $post_id ) ) {
			continue;
		}

		$post_id = (int) $post_id;
		$created++;

		update_post_meta( $post_id, '_inone_seed', '1' );

		if ( $location_ids ) {
			wp_set_object_terms( $post_id, array( (int) $location_ids[ array_rand( $location_ids ) ] ), INONE_PROPERTIES_TAX_LOCATION );
		}

		if ( isset( $category_ids[ $type ] ) ) {
			wp_set_object_terms( $post_id, array( (int) $category_ids[ $type ] ), INONE_PROPERTIES_TAX_CATEGORY );
		}

		$is_featured = ( $i <= 3 );

		if ( function_exists( 'update_field' ) ) {
			update_field( 'property_type', $type, $post_id );
			update_field( 'price', $price, $post_id );
			update_field( 'bedrooms', $beds, $post_id );
			update_field( 'bathrooms', $baths, $post_id );
			update_field( 'square_footage', $sqft, $post_id );
			update_field( 'year_built', $year, $post_id );
			update_field( 'status', $status, $post_id );
			update_field( 'featured', $is_featured ? 1 : 0, $post_id );

			update_field( 'address', $address, $post_id );
			update_field( 'city', $city, $post_id );
			update_field( 'state_province', $state, $post_id );
			update_field( 'zip_postal_code', $zip, $post_id );
			update_field(
				'map_location',
				array(
					'address' => $address . ', ' . $city . ', ' . $state . ' ' . $zip,
					'lat'     => 40.1770 + ( random_int( -500, 500 ) / 10000 ),
					'lng'     => 44.5035 + ( random_int( -500, 500 ) / 10000 ),
				),
				$post_id
			);

			$feature_count = random_int( 2, 5 );
			shuffle( $features );
			update_field( 'features', array_slice( $features, 0, $feature_count ), $post_id );

			update_field( 'description', '<p>Sample description (ACF WYSIWYG).</p>', $post_id );
			update_field( 'virtual_tour_url', 'https://example.com/virtual-tour/' . $post_id, $post_id );

			if ( $with_media && $attachments ) {
				update_field( 'property_gallery', $attachments, $post_id );
				set_post_thumbnail( $post_id, (int) $attachments[0] );
			}
		}
	}

	inone_properties_seed_redirect( sprintf( 'Created %d properties.', (int) $created ) );
}
add_action( 'admin_post_inone_properties_seed_generate', 'inone_properties_handle_seed_generate' );

function inone_properties_handle_seed_delete(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Sorry, you are not allowed to do that.', 'inone-properties' ) );
	}

	$nonce = isset( $_POST['inone_properties_seed_delete_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['inone_properties_seed_delete_nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'inone_properties_seed_delete' ) ) {
		wp_die( esc_html__( 'Invalid nonce.', 'inone-properties' ) );
	}

	$deleted_posts = inone_properties_seed_delete_seeded_posts();

	$delete_media = isset( $_POST['delete_media'] ) && (string) wp_unslash( $_POST['delete_media'] ) === '1';
	if ( $delete_media ) {
		$ids = get_option( 'inone_properties_seed_attachment_ids', array() );
		if ( is_array( $ids ) ) {
			foreach ( $ids as $id ) {
				$id = (int) $id;
				if ( $id > 0 && get_post_type( $id ) === 'attachment' && get_post_meta( $id, '_inone_seed_asset', true ) ) {
					wp_delete_attachment( $id, true );
				}
			}
		}
		delete_option( 'inone_properties_seed_attachment_ids' );
	}

	inone_properties_seed_redirect( sprintf( 'Deleted %d properties.', (int) $deleted_posts ) );
}
add_action( 'admin_post_inone_properties_seed_delete', 'inone_properties_handle_seed_delete' );

