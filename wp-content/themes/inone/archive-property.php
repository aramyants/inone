<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$filters = inone_properties_parse_filters( $_GET );
$args    = inone_properties_build_query_args( $filters );
$query   = new WP_Query( $args );

$bounds = inone_properties_get_price_bounds();
$min    = $bounds['min'];
$max    = $bounds['max'];

$locations = get_terms(
	array(
		'taxonomy'   => INONE_PROPERTIES_TAX_LOCATION,
		'hide_empty' => false,
	)
);

$breadcrumbs = array(
	array(
		'label' => 'Home',
		'url'   => home_url( '/' ),
	),
	array(
		'label' => 'Properties',
		'url'   => get_post_type_archive_link( INONE_PROPERTIES_POST_TYPE ),
	),
);

?>
<div class="site-layout">
	<main class="inone-properties-wrap">
		<?php inone_properties_render_breadcrumbs( $breadcrumbs ); ?>

		<header class="inone-properties-header">
			<h1 class="inone-properties-title">Properties</h1>
			<div class="inone-properties-result-count"><span data-inone-properties-count><?php echo esc_html( (int) $query->found_posts ); ?> results</span></div>
		</header>

		<form class="inone-properties-filters" data-inone-properties-filters>
			<div class="inone-properties-filters__grid">
				<div class="inone-properties-field inone-properties-field--keyword">
					<label for="inone-keyword">Keyword</label>
					<input id="inone-keyword" type="search" name="keyword" placeholder="Search title or address" value="<?php echo esc_attr( $filters['keyword'] ); ?>">
				</div>

				<div class="inone-properties-field inone-properties-field--type">
					<label for="inone-type">Type</label>
					<select id="inone-type" name="type">
						<option value="">Any</option>
						<?php foreach ( array( 'House', 'Apartment', 'Condo', 'Townhouse' ) as $type ) : ?>
							<option value="<?php echo esc_attr( $type ); ?>" <?php selected( $filters['type'], $type ); ?>><?php echo esc_html( $type ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="inone-properties-field inone-properties-field--status">
					<label for="inone-status">Status</label>
					<select id="inone-status" name="status">
						<option value="">Any</option>
						<?php foreach ( array( 'For Sale', 'For Rent', 'Sold', 'Rented' ) as $status ) : ?>
							<option value="<?php echo esc_attr( $status ); ?>" <?php selected( $filters['status'], $status ); ?>><?php echo esc_html( $status ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="inone-properties-field inone-properties-field--location">
					<label for="inone-location">Location</label>
					<select id="inone-location" name="location">
						<option value="">Any</option>
						<?php if ( is_array( $locations ) ) : ?>
							<?php foreach ( $locations as $term ) : ?>
								<option value="<?php echo esc_attr( (string) $term->term_id ); ?>" <?php selected( (int) $filters['location'], (int) $term->term_id ); ?>>
									<?php echo esc_html( $term->name ); ?>
								</option>
							<?php endforeach; ?>
						<?php endif; ?>
					</select>
				</div>

				<div class="inone-properties-field inone-properties-field--beds">
					<label for="inone-beds">Beds</label>
					<select id="inone-beds" name="bedrooms_min">
						<option value="0">Any</option>
						<?php foreach ( array( 1, 2, 3, 4, 5 ) as $n ) : ?>
							<option value="<?php echo esc_attr( (string) $n ); ?>" <?php selected( (int) $filters['bedrooms_min'], $n ); ?>><?php echo esc_html( $n ); ?>+</option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="inone-properties-field inone-properties-field--baths">
					<label for="inone-baths">Baths</label>
					<select id="inone-baths" name="bathrooms_min">
						<option value="0">Any</option>
						<?php foreach ( array( 1, 2, 3, 4, 5 ) as $n ) : ?>
							<option value="<?php echo esc_attr( (string) $n ); ?>" <?php selected( (int) $filters['bathrooms_min'], $n ); ?>><?php echo esc_html( $n ); ?>+</option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="inone-properties-field inone-properties-field--price">
					<label>Price Range</label>
					<div class="inone-properties-price">
						<div class="inone-properties-field">
							<label for="inone-min-price">Min</label>
							<input id="inone-min-price" type="number" min="0" step="1" name="min_price" value="<?php echo esc_attr( (string) max( 0, (int) $filters['min_price'] ) ); ?>">
						</div>
						<div class="inone-properties-field">
							<label for="inone-max-price">Max</label>
							<input id="inone-max-price" type="number" min="0" step="1" name="max_price" value="<?php echo esc_attr( (string) max( 0, (int) $filters['max_price'] ) ); ?>">
						</div>

						<div class="inone-properties-slider">
							<div class="inone-properties-slider__values">
								<span data-inone-price-label>
									<?php echo esc_html( '$' . number_format_i18n( max( $min, (int) $filters['min_price'] ) ) . ' – $' . number_format_i18n( (int) ( $filters['max_price'] ? $filters['max_price'] : $max ) ) ); ?>
								</span>
							</div>
							<div class="inone-properties-slider__range">
								<input type="range" name="min_price_range" min="<?php echo esc_attr( (string) $min ); ?>" max="<?php echo esc_attr( (string) $max ); ?>" value="<?php echo esc_attr( (string) max( $min, (int) $filters['min_price'] ) ); ?>">
								<input type="range" name="max_price_range" min="<?php echo esc_attr( (string) $min ); ?>" max="<?php echo esc_attr( (string) $max ); ?>" value="<?php echo esc_attr( (string) ( $filters['max_price'] ? (int) $filters['max_price'] : $max ) ); ?>">
							</div>
						</div>
					</div>
				</div>

				<div class="inone-properties-field inone-properties-field--status">
					<label for="inone-sort">Sort</label>
					<select id="inone-sort" name="sort">
						<option value="newest" <?php selected( $filters['sort'], 'newest' ); ?>>Newest First</option>
						<option value="oldest" <?php selected( $filters['sort'], 'oldest' ); ?>>Oldest First</option>
						<option value="price_asc" <?php selected( $filters['sort'], 'price_asc' ); ?>>Price (low to high)</option>
						<option value="price_desc" <?php selected( $filters['sort'], 'price_desc' ); ?>>Price (high to low)</option>
					</select>
				</div>
			</div>

			<div class="inone-properties-actions">
				<button type="submit" class="inone-properties-button">Apply Filters</button>
				<button type="button" class="inone-properties-button inone-properties-button--secondary" data-inone-reset>Reset Filters</button>
			</div>
		</form>

		<section data-inone-properties-results>
			<?php if ( $query->have_posts() ) : ?>
				<div class="inone-properties-grid" role="list">
					<?php while ( $query->have_posts() ) : ?>
						<?php $query->the_post(); ?>
						<div class="inone-properties-grid__item" role="listitem">
							<?php inone_properties_render_property_card( get_the_ID() ); ?>
						</div>
					<?php endwhile; ?>
				</div>
			<?php else : ?>
				<div class="inone-properties-empty">No properties found.</div>
			<?php endif; ?>
		</section>

		<?php wp_reset_postdata(); ?>
	</main>

	<?php get_sidebar(); ?>
</div>
<?php

get_footer();

