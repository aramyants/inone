<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
	<div class="site-header__inner">
		<a class="site-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>
		<nav class="site-nav" aria-label="<?php echo esc_attr__( 'Primary menu', 'inone' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'fallback_cb'    => false,
				)
			);
			?>
		</nav>
	</div>
</header>

<div class="site-content">

