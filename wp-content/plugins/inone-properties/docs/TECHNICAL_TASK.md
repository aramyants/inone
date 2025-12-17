# WordPress Developer Task - Implementation Guide

This repo satisfies the "Custom Property Listings with ACF" task using:

- **ACF (admin-managed)** for the Post Type, Taxonomies, and Field Groups (importable JSON)
- **Custom code** (this plugin) for templates, rendering, AJAX filtering, sorting, and shortcode output
- **Custom theme** (optional) to replace the default themes

## 1) Setup (no manual ACF clicking)

1. Install + activate **ACF PRO**.
2. Import the ACF config:
   - WP Admin -> **ACF -> Tools -> Import**
   - Import: `wp-content/plugins/inone-properties/acf-import.json`
3. Activate the plugin:
   - WP Admin -> **Plugins** -> activate **InOne Properties**
4. Activate the custom theme (optional but recommended if you will remove the default themes):
   - WP Admin -> **Appearance -> Themes** -> activate **InOne**
5. Flush permalinks:
   - WP Admin -> **Settings -> Permalinks** -> **Save Changes**

### Google Maps field (ACF)
If you want the "Map Location" field to work, set a Google Maps API key in ACF settings.

## 2) How to add property data

1. WP Admin -> **Properties -> Add New**
2. Fill:
   - **Title**, **Featured Image**, and **Editor**
   - Field Groups:
     - **Property Details** (type, price, beds, baths, sqft, year, status, featured)
     - **Location Information** (address, city, state/province, ZIP, map)
     - **Property Features** (checkboxes + description)
     - **Media** (gallery min 3 images, optional virtual tour URL)
   - Taxonomies:
     - **Property Locations** (City/Neighborhood)
     - **Property Categories**
3. Publish.

### Add the search widget (optional)

1. WP Admin -> **Appearance -> Widgets**
2. Add **Property Search (AJAX)** to the **Sidebar**.

## 3) Feature checklist (matches the PDF requirements)

- **Custom Post Type**: "Properties" (ACF post type key: `property`) with title/editor/thumbnail + archives enabled.
- **Taxonomies**:
  - `property-location` (hierarchical for City/Neighborhood)
  - `property-category` (hierarchical)
- **ACF Field Groups**:
  - Property Details, Location Information, Property Features, Media
  - Includes `featured` flag for the shortcode system
- **Templates**:
  - Archive list + single view + taxonomy templates
  - Breadcrumbs on all property views
  - Social sharing links on single property pages
  - Gallery displayed as a grid
- **Search + Filtering**:
  - Keyword (title OR address), type, status, min/max price, beds/baths, location
  - Price slider + sorting
  - AJAX updates, result count, "No properties found"
  - Filter state is reflected in the URL query string
- **Shortcode**:
  - `[featured_properties limit="3" columns="3" order="DESC" orderby="date"]`

## 4) Generate demo data (seed)

If you want sample listings for testing the archive filters and the featured shortcode:

1. WP Admin -> **Tools -> InOne Properties Seeder**
2. Choose how many properties to create.
3. Click **Generate Seed Data**.

To remove the demo data:

1. WP Admin -> **Tools -> InOne Properties Seeder**
2. Click **Delete Seed Data**
