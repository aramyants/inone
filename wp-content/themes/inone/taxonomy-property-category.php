<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$term = get_queried_object();

$args  = array(
	'post_type'      => INONE_PROPERTIES_POST_TYPE,
	'post_status'    => 'publish',
	'posts_per_page' => 9,
	'tax_query'      => array(
		array(
			'taxonomy' => INONE_PROPERTIES_TAX_CATEGORY,
			'field'    => 'term_id',
			'terms'    => $term instanceof WP_Term ? array( (int) $term->term_id ) : array(),
		),
	),
);
$query = new WP_Query( $args );

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
		'label' => $term instanceof WP_Term ? $term->name : 'Category',
		'url'   => '',
	),
);

?>
<div class="site-layout">
	<main class="inone-properties-wrap">
		<?php inone_properties_render_breadcrumbs( $breadcrumbs ); ?>

		<header class="inone-properties-header">
			<h1 class="inone-properties-title"><?php echo esc_html( $term instanceof WP_Term ? $term->name : 'Category' ); ?></h1>
			<div class="inone-properties-result-count"><span><?php echo esc_html( (int) $query->found_posts ); ?> results</span></div>
		</header>

		<section>
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

