# InOne - WordPress Properties (ACF + AJAX)

Real-estate "Properties" listings for WordPress built with:

- ACF PRO (admin-managed) for the post type, taxonomies, and field groups (JSON import)
- Custom plugin for templates, rendering, AJAX filtering, sorting, and featured shortcode
- Lightweight custom theme (optional) for a clean, non-default frontend

## Repo Contents

- Plugin: `wp-content/plugins/inone-properties`
- Theme: `wp-content/themes/inone`

## Requirements

- WordPress 6.0+
- PHP 7.4+
- Advanced Custom Fields PRO (required;)

## Quick Start

1. Install WordPress (LocalWP, Docker, hosting, etc).
2. Copy this repo's `wp-content` into your WP install.
3. Install + activate **ACF PRO**.
4. Import ACF configuration:
   - WP Admin -> ACF -> Tools -> Import
   - Import `wp-content/plugins/inone-properties/acf-import.json`
5. Activate:
   - WP Admin -> Plugins -> **InOne Properties**
   - WP Admin -> Appearance -> Themes -> **InOne** (optional)
6. WP Admin -> Settings -> Permalinks -> **Save Changes**.

## Where to See It

Default URLs (based on the ACF post type/taxonomy keys):

- Archive listing + filters: `/property/`
- Single property: `/property/<property-slug>/`
- Location taxonomy: `/property-location/<term-slug>/`
- Category taxonomy: `/property-category/<term-slug>/`

## Demo Data (Seeder)

WP Admin -> Tools -> **InOne Properties Seeder**

- Generate sample Properties (with optional placeholder media)
- Delete seeded data

## Features

- ACF fields (details, location, features, media) + `featured` flag
- Styled archive + single templates with breadcrumbs and social share links
- AJAX filtering (vanilla JS) + result count + URL state + "no results" message
- Price range slider + sorting (newest/oldest/price asc/desc)
- Shortcode: `[featured_properties limit="3" columns="3" order="DESC" orderby="date"]`

## More Documentation

- Full task checklist + setup notes: `wp-content/plugins/inone-properties/docs/TECHNICAL_TASK.md`

