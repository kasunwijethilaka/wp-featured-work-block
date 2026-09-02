<?php
/**
 * Seeds demo case studies + project types on plugin activation.
 *
 * @package FeaturedWork
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Insert demo content once, on activation.
 *
 * Guarded by the `featured_work_seeded` option so re-activating the plugin never
 * creates duplicates. The CPT and taxonomy are registered by the activation routine
 * before this runs (see featured_work_activate()).
 */
function featured_work_seed_demo_content() {
	if ( get_option( 'featured_work_seeded' ) ) {
		return;
	}

	// 1. Ensure the project-type terms exist.
	$terms = array(
		'web-design'  => __( 'Web Design', 'featured-work' ),
		'branding'    => __( 'Branding', 'featured-work' ),
		'mobile-apps' => __( 'Mobile Apps', 'featured-work' ),
	);
	foreach ( $terms as $slug => $name ) {
		if ( ! term_exists( $slug, 'project_type' ) ) {
			wp_insert_term( $name, 'project_type', array( 'slug' => $slug ) );
		}
	}

	// 2. The demo case studies.
	$samples = array(
		array(
			'title'   => 'Aurora Analytics Dashboard',
			'client'  => 'Aurora Labs',
			'url'     => 'https://example.com/aurora',
			'types'   => array( 'web-design' ),
			'excerpt' => 'A real-time analytics dashboard with accessible data tables and a keyboard-navigable command palette.',
		),
		array(
			'title'   => 'Meridian Banking App',
			'client'  => 'Meridian Financial',
			'url'     => 'https://example.com/meridian',
			'types'   => array( 'mobile-apps' ),
			'excerpt' => 'A mobile banking experience focused on clarity, security, and WCAG-compliant contrast throughout.',
		),
		array(
			'title'   => 'Verdant Rebrand',
			'client'  => 'Verdant Foods',
			'url'     => 'https://example.com/verdant',
			'types'   => array( 'branding' ),
			'excerpt' => 'A full identity refresh — logo system, type scale, and a living brand-guidelines site.',
		),
		array(
			'title'   => 'Coastline E-Commerce Platform',
			'client'  => 'Coastline Outfitters',
			'url'     => 'https://example.com/coastline',
			'types'   => array( 'web-design' ),
			'excerpt' => 'A headless storefront with a fast, accessible checkout and a component-driven design system.',
		),
		array(
			'title'   => 'Pulse Fitness Companion',
			'client'  => 'Pulse Health',
			'url'     => 'https://example.com/pulse',
			'types'   => array( 'mobile-apps' ),
			'excerpt' => 'A companion app pairing workout tracking with an inclusive, reduced-motion-friendly UI.',
		),
		array(
			'title'   => 'Northwind Identity System',
			'client'  => 'Northwind Studio',
			'url'     => 'https://example.com/northwind',
			'types'   => array( 'branding', 'web-design' ),
			'excerpt' => 'A cross-disciplinary project spanning brand identity and the marketing site that showcases it.',
		),
	);

	// 3. Create the posts and wire up terms + meta.
	foreach ( $samples as $sample ) {
		$post_id = wp_insert_post(
			array(
				'post_type'    => 'case_study',
				'post_status'  => 'publish',
				'post_title'   => $sample['title'],
				'post_excerpt' => $sample['excerpt'],
				'post_content' => $sample['excerpt'],
			),
			true
		);

		if ( is_wp_error( $post_id ) || ! $post_id ) {
			continue;
		}

		wp_set_object_terms( $post_id, $sample['types'], 'project_type' );
		update_post_meta( $post_id, '_case_study_client', $sample['client'] );
		update_post_meta( $post_id, '_case_study_url', esc_url_raw( $sample['url'] ) );
	}

	update_option( 'featured_work_seeded', 1 );
}
