<?php
/**
 * Registers the `project_type` taxonomy for case studies.
 *
 * @package FeaturedWork
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the Project Type taxonomy.
 *
 * `show_in_rest` is required so the block's editor control can list the available
 * terms (via getEntityRecords) and so the taxonomy is available in the block editor.
 */
function featured_work_register_taxonomy() {
	$labels = array(
		'name'          => _x( 'Project Types', 'taxonomy general name', 'featured-work' ),
		'singular_name' => _x( 'Project Type', 'taxonomy singular name', 'featured-work' ),
		'menu_name'     => __( 'Project Types', 'featured-work' ),
		'all_items'     => __( 'All Project Types', 'featured-work' ),
		'edit_item'     => __( 'Edit Project Type', 'featured-work' ),
		'update_item'   => __( 'Update Project Type', 'featured-work' ),
		'add_new_item'  => __( 'Add New Project Type', 'featured-work' ),
		'new_item_name' => __( 'New Project Type Name', 'featured-work' ),
		'search_items'  => __( 'Search Project Types', 'featured-work' ),
		'not_found'     => __( 'No project types found.', 'featured-work' ),
	);

	$args = array(
		'labels'            => $labels,
		'public'            => true,
		'hierarchical'      => true,
		'show_in_rest'      => true,
		'show_admin_column' => true,
		'rewrite'           => array( 'slug' => 'project-type' ),
	);

	register_taxonomy( 'project_type', array( 'case_study' ), $args );
}
add_action( 'init', 'featured_work_register_taxonomy' );
