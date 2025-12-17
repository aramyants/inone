<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Inone_Properties_Search_Widget extends WP_Widget {
	public function __construct() {
		parent::__construct(
			'inone_properties_search_widget',
			__( 'Property Search (AJAX)', 'inone-properties' ),
			array( 'description' => __( 'Property search filters (links to the Properties archive).', 'inone-properties' ) )
		);
	}

	public function widget( $args, $instance ) {
		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		$title = isset( $instance['title'] ) ? (string) $instance['title'] : '';
		if ( $title ) {
			echo $args['before_title'] . esc_html( $title ) . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		$archive_url = get_post_type_archive_link( INONE_PROPERTIES_POST_TYPE );
		$filters     = inone_properties_parse_filters( $_GET );
		$locations   = get_terms(
			array(
				'taxonomy'   => 'property-location',
				'hide_empty' => false,
			)
		);

		?>
		<form class="inone-properties-widget" method="get" action="<?php echo esc_url( $archive_url ); ?>">
			<p class="inone-properties-field">
				<label for="<?php echo esc_attr( $this->get_field_id( 'keyword' ) ); ?>"><?php esc_html_e( 'Keyword', 'inone-properties' ); ?></label>
				<input id="<?php echo esc_attr( $this->get_field_id( 'keyword' ) ); ?>" type="search" name="keyword" value="<?php echo esc_attr( $filters['keyword'] ); ?>" placeholder="<?php echo esc_attr__( 'Title or address', 'inone-properties' ); ?>">
			</p>

			<p class="inone-properties-field">
				<label for="<?php echo esc_attr( $this->get_field_id( 'type' ) ); ?>"><?php esc_html_e( 'Type', 'inone-properties' ); ?></label>
				<select id="<?php echo esc_attr( $this->get_field_id( 'type' ) ); ?>" name="type">
					<option value=""><?php esc_html_e( 'Any', 'inone-properties' ); ?></option>
					<?php foreach ( array( 'House', 'Apartment', 'Condo', 'Townhouse' ) as $type ) : ?>
						<option value="<?php echo esc_attr( $type ); ?>" <?php selected( $filters['type'], $type ); ?>><?php echo esc_html( $type ); ?></option>
					<?php endforeach; ?>
				</select>
			</p>

			<p class="inone-properties-field">
				<label for="<?php echo esc_attr( $this->get_field_id( 'status' ) ); ?>"><?php esc_html_e( 'Status', 'inone-properties' ); ?></label>
				<select id="<?php echo esc_attr( $this->get_field_id( 'status' ) ); ?>" name="status">
					<option value=""><?php esc_html_e( 'Any', 'inone-properties' ); ?></option>
					<?php foreach ( array( 'For Sale', 'For Rent', 'Sold', 'Rented' ) as $status ) : ?>
						<option value="<?php echo esc_attr( $status ); ?>" <?php selected( $filters['status'], $status ); ?>><?php echo esc_html( $status ); ?></option>
					<?php endforeach; ?>
				</select>
			</p>

			<p class="inone-properties-field">
				<label for="<?php echo esc_attr( $this->get_field_id( 'location' ) ); ?>"><?php esc_html_e( 'Location', 'inone-properties' ); ?></label>
				<select id="<?php echo esc_attr( $this->get_field_id( 'location' ) ); ?>" name="location">
					<option value=""><?php esc_html_e( 'Any', 'inone-properties' ); ?></option>
					<?php if ( is_array( $locations ) ) : ?>
						<?php foreach ( $locations as $term ) : ?>
							<option value="<?php echo esc_attr( (string) $term->term_id ); ?>" <?php selected( (int) $filters['location'], (int) $term->term_id ); ?>>
								<?php echo esc_html( $term->name ); ?>
							</option>
						<?php endforeach; ?>
					<?php endif; ?>
				</select>
			</p>

			<p class="inone-properties-field">
				<label for="<?php echo esc_attr( $this->get_field_id( 'min_price' ) ); ?>"><?php esc_html_e( 'Min Price', 'inone-properties' ); ?></label>
				<input id="<?php echo esc_attr( $this->get_field_id( 'min_price' ) ); ?>" type="number" min="0" step="1" name="min_price" value="<?php echo esc_attr( (string) (int) $filters['min_price'] ); ?>">
			</p>

			<p class="inone-properties-field">
				<label for="<?php echo esc_attr( $this->get_field_id( 'max_price' ) ); ?>"><?php esc_html_e( 'Max Price', 'inone-properties' ); ?></label>
				<input id="<?php echo esc_attr( $this->get_field_id( 'max_price' ) ); ?>" type="number" min="0" step="1" name="max_price" value="<?php echo esc_attr( (string) (int) $filters['max_price'] ); ?>">
			</p>

			<p class="inone-properties-field">
				<label for="<?php echo esc_attr( $this->get_field_id( 'beds' ) ); ?>"><?php esc_html_e( 'Bedrooms', 'inone-properties' ); ?></label>
				<select id="<?php echo esc_attr( $this->get_field_id( 'beds' ) ); ?>" name="bedrooms_min">
					<option value="0"><?php esc_html_e( 'Any', 'inone-properties' ); ?></option>
					<?php foreach ( array( 1, 2, 3, 4, 5 ) as $n ) : ?>
						<option value="<?php echo esc_attr( (string) $n ); ?>" <?php selected( (int) $filters['bedrooms_min'], $n ); ?>><?php echo esc_html( $n ); ?>+</option>
					<?php endforeach; ?>
				</select>
			</p>

			<p class="inone-properties-field">
				<label for="<?php echo esc_attr( $this->get_field_id( 'baths' ) ); ?>"><?php esc_html_e( 'Bathrooms', 'inone-properties' ); ?></label>
				<select id="<?php echo esc_attr( $this->get_field_id( 'baths' ) ); ?>" name="bathrooms_min">
					<option value="0"><?php esc_html_e( 'Any', 'inone-properties' ); ?></option>
					<?php foreach ( array( 1, 2, 3, 4, 5 ) as $n ) : ?>
						<option value="<?php echo esc_attr( (string) $n ); ?>" <?php selected( (int) $filters['bathrooms_min'], $n ); ?>><?php echo esc_html( $n ); ?>+</option>
					<?php endforeach; ?>
				</select>
			</p>

			<p class="inone-properties-field">
				<label for="<?php echo esc_attr( $this->get_field_id( 'sort' ) ); ?>"><?php esc_html_e( 'Sort', 'inone-properties' ); ?></label>
				<select id="<?php echo esc_attr( $this->get_field_id( 'sort' ) ); ?>" name="sort">
					<option value="newest" <?php selected( $filters['sort'], 'newest' ); ?>><?php esc_html_e( 'Newest First', 'inone-properties' ); ?></option>
					<option value="oldest" <?php selected( $filters['sort'], 'oldest' ); ?>><?php esc_html_e( 'Oldest First', 'inone-properties' ); ?></option>
					<option value="price_asc" <?php selected( $filters['sort'], 'price_asc' ); ?>><?php esc_html_e( 'Price (low to high)', 'inone-properties' ); ?></option>
					<option value="price_desc" <?php selected( $filters['sort'], 'price_desc' ); ?>><?php esc_html_e( 'Price (high to low)', 'inone-properties' ); ?></option>
				</select>
			</p>

			<div class="inone-properties-actions">
				<button type="submit" class="inone-properties-button"><?php esc_html_e( 'Search', 'inone-properties' ); ?></button>
				<a class="inone-properties-button inone-properties-button--secondary" href="<?php echo esc_url( $archive_url ); ?>"><?php esc_html_e( 'Reset', 'inone-properties' ); ?></a>
			</div>
		</form>
		<?php

		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	public function form( $instance ) {
		$title = isset( $instance['title'] ) ? (string) $instance['title'] : __( 'Property Search', 'inone-properties' );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'inone-properties' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance          = $old_instance;
		$instance['title'] = isset( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';
		return $instance;
	}
}

function inone_properties_register_widgets(): void {
	register_widget( 'Inone_Properties_Search_Widget' );
}
add_action( 'widgets_init', 'inone_properties_register_widgets' );
