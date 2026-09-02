<?php
/**
 * Plugin Name:       Featured Work
 * Description:       A dynamic Gutenberg block that renders an accessible, filterable grid of case studies. Ships with its own custom post type, taxonomy, and demo content.
 * Version:           1.0.0
 * Requires at least: 6.5
 * Requires PHP:      7.4
 * Author:            Kasun Wijethilaka
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       featured-work
 *
 * @package FeaturedWork
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

define( 'FEATURED_WORK_VERSION', '1.0.0' );
define( 'FEATURED_WORK_PATH', plugin_dir_path( __FILE__ ) );

require_once FEATURED_WORK_PATH . 'inc/post-types.php';
require_once FEATURED_WORK_PATH . 'inc/taxonomies.php';
require_once FEATURED_WORK_PATH . 'inc/meta.php';
require_once FEATURED_WORK_PATH . 'inc/seed.php';

/**
 * Register the block from its compiled metadata.
 *
 * The render callback lives in build/render.php (copied there by @wordpress/scripts),
 * which makes this a *dynamic* block: markup is generated on the server at request time
 * from a live WP_Query rather than being saved into post content.
 *
 * Guarded on the build directory so the plugin loads cleanly before the first
 * `npm run build` (e.g. while working through the earlier phases).
 */
function featured_work_register_block() {
	if ( file_exists( FEATURED_WORK_PATH . 'build/block.json' ) ) {
		register_block_type( FEATURED_WORK_PATH . 'build' );
	}
}
add_action( 'init', 'featured_work_register_block' );

/**
 * On activation: register the CPT + taxonomy, seed demo content, and flush rewrite
 * rules so the case-study permalinks resolve immediately.
 *
 * The registration functions are called directly here because `init` has already
 * fired by the time an activation hook runs, so the post type and taxonomy must be
 * registered explicitly before we seed content or flush rewrites.
 */
function featured_work_activate() {
	featured_work_register_post_type();
	featured_work_register_taxonomy();
	featured_work_seed_demo_content();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'featured_work_activate' );

/**
 * Clean up rewrite rules on deactivation. Demo content is intentionally left in place.
 */
function featured_work_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'featured_work_deactivate' );
