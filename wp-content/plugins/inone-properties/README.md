# InOne Properties

Custom "Properties" listing system for WordPress using ACF-managed fields/content types, custom templates, AJAX filtering (vanilla JS), and a featured properties shortcode.

## Requirements

- WordPress 6.0+
- PHP 7.4+
- Advanced Custom Fields PRO (required)

## Installation

1. Ensure ACF PRO is installed and activated.
2. WP Admin -> ACF -> Tools -> Import: import `wp-content/plugins/inone-properties/acf-import.json`.
3. WP Admin -> Plugins: activate "InOne Properties".
4. WP Admin -> Settings -> Permalinks: click "Save Changes".

## Adding Properties

1. WP Admin -> Properties -> Add New
2. Add:
   - Title + Featured Image
   - Content (optional; used if the ACF "Description" field is empty)
   - ACF fields under "Property Details", "Location Information", "Property Features", and "Media"
   - Taxonomies:
     - Property Location (City/Neighborhood)
     - Property Category
3. For the gallery, add at least 3 images.
4. Mark as "Featured" to show in the shortcode output.

## Demo Data (Seeder)

To generate sample Properties for testing:

1. WP Admin -> Tools -> InOne Properties Seeder
2. Click "Generate Seed Data"

## Frontend Templates

This plugin provides PHP templates (and loads them via `template_include`) so it works with block themes too:

- `wp-content/plugins/inone-properties/templates/archive-properties.php`
- `wp-content/plugins/inone-properties/templates/single-properties.php`
- `wp-content/plugins/inone-properties/templates/taxonomy-property-location.php`

## Archive Filtering (AJAX)

On the Properties archive (created by ACF - typically `/property/`), filters update without a full page reload:

- Keyword search (title OR address)
- Type, Status
- Min/Max Price (plus a range slider)
- Bedrooms/Bathrooms (minimum)
- Location taxonomy
- Sorting (newest/oldest/price asc/desc)

Filter state is reflected in the URL query string (e.g. `?type=House&min_price=250000`).

## Shortcode: Featured Properties

Use:

`[featured_properties]`

Options:

- `limit` (default `3`, max `24`)
- `columns` (default `3`, max `6`)
- `order` (`ASC` or `DESC`)
- `orderby` (`date`, `title`, `rand`, `meta_value_num`)

Examples:

- `[featured_properties limit="6" columns="3"]`
- `[featured_properties limit="4" columns="4" order="ASC" orderby="title"]`
- `[featured_properties limit="3" columns="3" orderby="meta_value_num" order="DESC"]` (orders by price)

## Customization Guide

- Results per page: adjust `posts_per_page` in `wp-content/plugins/inone-properties/includes/query.php` (`inone_properties_build_query_args()`).
- Field groups / post type / taxonomies: manage in WP Admin via ACF (or re-import the JSON).
- Filter choices (types/status/options): edit `wp-content/plugins/inone-properties/templates/archive-properties.php`.
- Styling: `wp-content/plugins/inone-properties/assets/css/properties.css`
- Filtering JS: `wp-content/plugins/inone-properties/assets/js/properties-filters.js`
