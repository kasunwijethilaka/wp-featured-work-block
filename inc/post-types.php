<?php
/**
 * Registers the `case_study` custom post type.
 *
 * @package FeaturedWork
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the Case Study post type.
 *
 * `show_in_rest` is required so the post type is editable in the block editor and
 * exposed over the REST API (which the block's editor controls rely on).
 */
function featured_work_register_post_type() {
	$labels = array(
		'name'                  => _x( 'Case Studies', 'post type general name', 'featured-work' ),
		'singular_name'         => _x( 'Case Study', 'post type singular name', 'featured-work' ),
		'menu_name'             => _x( 'Case Studies', 'admin menu', 'featured-work' ),
		'add_new'               => __( 'Add New', 'featured-work' ),
		'add_new_item'          => __( 'Add New Case Study', 'featured-work' ),
		'edit_item'             => __( 'Edit Case Study', 'featured-work' ),
		'new_item'              => __( 'New Case Study', 'featured-work' ),
		'view_item'             => __( 'View Case Study', 'featured-work' ),
		'view_items'            => __( 'View Case Studies', 'featured-work' ),
		'search_items'          => __( 'Search Case Studies', 'featured-work' ),
		'not_found'             => __( 'No case studies found.', 'featured-work' ),
		'not_found_in_trash'    => __( 'No case studies found in Trash.', 'featured-work' ),
		'all_items'             => __( 'All Case Studies', 'featured-work' ),
		'featured_image'        => __( 'Cover Image', 'featured-work' ),
		'set_featured_image'    => __( 'Set cover image', 'featured-work' ),
		'remove_featured_image' => __( 'Remove cover image', 'featured-work' ),
		'use_featured_image'    => __( 'Use as cover image', 'featured-work' ),
	);

	$args = array(
		'labels'        => $labels,
		'public'        => true,
		'has_archive'   => true,
		'show_in_rest'  => true,
		'menu_icon'     => 'dashicons-portfolio',
		'menu_position' => 20,
		'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ),
		'rewrite'       => array( 'slug' => 'case-studies' ),
		'hierarchical'  => false,
	);

	register_post_type( 'case_study', $args );
}
add_action( 'init', 'featured_work_register_post_type' );
