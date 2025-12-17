<?php
/**
 * Plugin Name: InOne Properties
 * Description: Custom "Properties" listing system with ACF fields, templates, AJAX filtering, and a featured properties shortcode.
 * Version: 1.0.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: Aram Kamalyan
 * Author URI: https://aramyants.com
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'INONE_PROPERTIES_VERSION', '1.0.0' );
define( 'INONE_PROPERTIES_PATH', plugin_dir_path( __FILE__ ) );
define( 'INONE_PROPERTIES_URL', plugin_dir_url( __FILE__ ) );
define( 'INONE_PROPERTIES_POST_TYPE', 'property' );
define( 'INONE_PROPERTIES_TAX_LOCATION', 'property-location' );
define( 'INONE_PROPERTIES_TAX_CATEGORY', 'property-category' );

require_once INONE_PROPERTIES_PATH . 'includes/render.php';
require_once INONE_PROPERTIES_PATH . 'includes/query.php';
require_once INONE_PROPERTIES_PATH . 'includes/templates.php';
require_once INONE_PROPERTIES_PATH . 'includes/ajax.php';
require_once INONE_PROPERTIES_PATH . 'includes/shortcodes.php';
require_once INONE_PROPERTIES_PATH . 'includes/widget.php';
require_once INONE_PROPERTIES_PATH . 'includes/assets.php';
require_once INONE_PROPERTIES_PATH . 'includes/admin-seed.php';
