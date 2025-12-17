<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function inone_theme_setup(): void {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'inone' ),
		)
	);
}
add_action( 'after_setup_theme', 'inone_theme_setup' );

function inone_theme_widgets_init(): void {
	register_sidebar(
		array(
			'name'          => __( 'Sidebar', 'inone' ),
			'id'            => 'sidebar-1',
			'description'   => __( 'Add widgets here.', 'inone' ),
			'before_widget' => '<section class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'inone_theme_widgets_init' );

function inone_theme_enqueue_assets(): void {
	wp_enqueue_style( 'inone-theme', get_stylesheet_uri(), array(), '1.0.0' );
}
add_action( 'wp_enqueue_scripts', 'inone_theme_enqueue_assets' );

