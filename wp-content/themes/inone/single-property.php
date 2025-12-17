<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

?>
<div class="site-layout">
	<main class="inone-properties-wrap">
		<?php
		while ( have_posts() ) :
			the_post();

			$post_id = get_the_ID();

			$price     = inone_properties_format_price( inone_properties_get_field( 'price', $post_id ) );
			$type      = (string) inone_properties_get_field( 'property_type', $post_id );
			$status    = (string) inone_properties_get_field( 'status', $post_id );
			$bedrooms  = inone_properties_get_field( 'bedrooms', $post_id );
			$bathrooms = inone_properties_get_field( 'bathrooms', $post_id );
			$sqft      = inone_properties_get_field( 'square_footage', $post_id );
			$year      = inone_properties_get_field( 'year_built', $post_id );

			$address = (string) inone_properties_get_field( 'address', $post_id );
			$city    = (string) inone_properties_get_field( 'city', $post_id );
			$state   = (string) inone_properties_get_field( 'state_province', $post_id );
			$zip     = (string) inone_properties_get_field( 'zip_postal_code', $post_id );
			$map     = inone_properties_get_field( 'map_location', $post_id );

			$features     = inone_properties_get_field( 'features', $post_id );
			$description  = inone_properties_get_field( 'description', $post_id );
			$gallery      = inone_properties_get_field( 'property_gallery', $post_id );
			$virtual_tour = (string) inone_properties_get_field( 'virtual_tour_url', $post_id );

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
					'label' => get_the_title(),
					'url'   => '',
				),
			);
			?>

			<?php inone_properties_render_breadcrumbs( $breadcrumbs ); ?>

			<header class="inone-properties-header">
				<h1 class="inone-properties-title"><?php the_title(); ?></h1>
				<?php if ( $price ) : ?>
					<div class="inone-properties-card__price"><?php echo esc_html( $price ); ?></div>
				<?php endif; ?>
			</header>

			<div class="inone-property-hero">
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="inone-property-hero__image">
						<?php the_post_thumbnail( 'large' ); ?>
					</div>
				<?php endif; ?>
			</div>

			<div class="inone-property-meta">
				<section class="inone-property-panel">
					<h2>Property Details</h2>
					<dl class="inone-property-kv">
						<?php if ( $type ) : ?><dt>Type</dt><dd><?php echo esc_html( $type ); ?></dd><?php endif; ?>
						<?php if ( $status ) : ?><dt>Status</dt><dd><?php echo esc_html( $status ); ?></dd><?php endif; ?>
						<?php if ( $bedrooms !== '' && $bedrooms !== null ) : ?><dt>Bedrooms</dt><dd><?php echo esc_html( (int) $bedrooms ); ?></dd><?php endif; ?>
						<?php if ( $bathrooms !== '' && $bathrooms !== null ) : ?><dt>Bathrooms</dt><dd><?php echo esc_html( (int) $bathrooms ); ?></dd><?php endif; ?>
						<?php if ( $sqft !== '' && $sqft !== null ) : ?><dt>Square Footage</dt><dd><?php echo esc_html( number_format_i18n( (int) $sqft ) ); ?></dd><?php endif; ?>
						<?php if ( $year !== '' && $year !== null ) : ?><dt>Year Built</dt><dd><?php echo esc_html( (int) $year ); ?></dd><?php endif; ?>
					</dl>
				</section>

				<section class="inone-property-panel">
					<h2>Location</h2>
					<dl class="inone-property-kv">
						<?php if ( $address ) : ?><dt>Address</dt><dd><?php echo esc_html( $address ); ?></dd><?php endif; ?>
						<?php if ( $city ) : ?><dt>City</dt><dd><?php echo esc_html( $city ); ?></dd><?php endif; ?>
						<?php if ( $state ) : ?><dt>State/Province</dt><dd><?php echo esc_html( $state ); ?></dd><?php endif; ?>
						<?php if ( $zip ) : ?><dt>ZIP/Postal</dt><dd><?php echo esc_html( $zip ); ?></dd><?php endif; ?>
					</dl>

					<?php if ( is_array( $map ) && isset( $map['lat'], $map['lng'] ) ) : ?>
						<p>
							<a href="<?php echo esc_url( 'https://www.google.com/maps?q=' . rawurlencode( $map['lat'] . ',' . $map['lng'] ) ); ?>" target="_blank" rel="noopener noreferrer">
								View on Google Maps
							</a>
						</p>
					<?php endif; ?>
				</section>
			</div>

			<?php if ( is_array( $features ) && ! empty( $features ) ) : ?>
				<section class="inone-property-panel">
					<h2>Features</h2>
					<ul class="inone-properties-card__meta">
						<?php foreach ( $features as $feature ) : ?>
							<li><?php echo esc_html( (string) $feature ); ?></li>
						<?php endforeach; ?>
					</ul>
				</section>
			<?php endif; ?>

			<section class="inone-property-panel">
				<h2>Description</h2>
				<div class="inone-property-description">
					<?php
					if ( $description ) {
						echo wp_kses_post( (string) $description );
					} else {
						the_content();
					}
					?>
				</div>
			</section>

			<?php if ( is_array( $gallery ) && ! empty( $gallery ) ) : ?>
				<section class="inone-property-panel">
					<h2>Gallery</h2>
					<div class="inone-property-gallery">
						<?php foreach ( $gallery as $image ) : ?>
							<?php
							$img_url = isset( $image['sizes']['medium_large'] ) ? $image['sizes']['medium_large'] : ( $image['url'] ?? '' );
							if ( ! $img_url ) {
								continue;
							}
							?>
							<img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $image['alt'] ?? '' ); ?>" loading="lazy">
						<?php endforeach; ?>
					</div>
				</section>
			<?php endif; ?>

			<?php if ( $virtual_tour ) : ?>
				<section class="inone-property-panel">
					<h2>Virtual Tour</h2>
					<p><a href="<?php echo esc_url( $virtual_tour ); ?>" target="_blank" rel="noopener noreferrer">Open virtual tour</a></p>
				</section>
			<?php endif; ?>

			<?php inone_properties_render_social_share( $post_id ); ?>
		<?php endwhile; ?>
	</main>

	<?php get_sidebar(); ?>
</div>
<?php

get_footer();

