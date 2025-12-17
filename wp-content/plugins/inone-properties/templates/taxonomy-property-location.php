<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$is_block_theme = function_exists( 'wp_is_block_theme' ) && wp_is_block_theme();
if ( $is_block_theme ) {
	inone_properties_render_document_open();
	inone_properties_render_site_header();
} else {
	get_header();
}

$term = get_queried_object();

$filters             = inone_properties_parse_filters( $_GET );
$filters['location'] = ( $term instanceof WP_Term ) ? (int) $term->term_id : 0;
$args                = inone_properties_build_query_args( $filters );
$query               = new WP_Query( $args );

$breadcrumbs = array(
	array(
		'label' => 'Home',
		'url'   => home_url( '/' ),
	),
	array(
		'label' => 'Properties',
		'url'   => get_post_type_archive_link( INONE_PROPERTIES_POST_TYPE ),
	),
	array(
		'label' => $term instanceof WP_Term ? $term->name : 'Location',
		'url'   => '',
	),
);

?>
<main class="inone-properties-wrap">
	<?php inone_properties_render_breadcrumbs( $breadcrumbs ); ?>

	<header class="inone-properties-header">
		<h1 class="inone-properties-title"><?php echo esc_html( $term instanceof WP_Term ? $term->name : 'Location' ); ?></h1>
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
				<input id="inone-location" type="text" value="<?php echo esc_attr( $term instanceof WP_Term ? $term->name : '' ); ?>" disabled>
				<input type="hidden" name="location" value="<?php echo esc_attr( (string) (int) $filters['location'] ); ?>">
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
<?php

if ( $is_block_theme ) {
	inone_properties_render_site_footer();
	inone_properties_render_document_close();
} else {
	get_footer();
}
