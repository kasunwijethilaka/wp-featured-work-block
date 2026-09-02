<?php
/**
 * Server-side render for the Featured Work block. (Phase 2 stub.)
 *
 * This minimal version proves the editor↔server round-trip (ServerSideRender)
 * and the WP_Query wiring. Phase 3 replaces it with the full semantic,
 * accessible, filterable markup.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Inner block content (unused).
 * @var WP_Block $block      Block instance.
 *
 * @package FeaturedWork
 */

$posts_to_show = isset( $attributes['postsToShow'] ) ? (int) $attributes['postsToShow'] : 6;
$project_type  = isset( $attributes['projectType'] ) ? sanitize_title( $attributes['projectType'] ) : '';
$order         = isset( $attributes['order'] ) && 'asc' === $attributes['order'] ? 'ASC' : 'DESC';
$order_by      = isset( $attributes['orderBy'] ) ? sanitize_key( $attributes['orderBy'] ) : 'date';

$query_args = array(
	'post_type'           => 'case_study',
	'post_status'         => 'publish',
	'posts_per_page'      => $posts_to_show,
	'order'               => $order,
	'orderby'             => $order_by,
	'ignore_sticky_posts' => true,
);

if ( $project_type ) {
	$query_args['tax_query'] = array(
		array(
			'taxonomy' => 'project_type',
			'field'    => 'slug',
			'terms'    => $project_type,
		),
	);
}

$query = new WP_Query( $query_args );

if ( ! $query->have_posts() ) {
	printf(
		'<div %1$s><p>%2$s</p></div>',
		get_block_wrapper_attributes(), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already escaped by core.
		esc_html__( 'No case studies found.', 'featured-work' )
	);
	return;
}
?>
<div <?php echo get_block_wrapper_attributes(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already escaped by core. ?>>
	<ul>
		<?php
		while ( $query->have_posts() ) :
			$query->the_post();
			?>
			<li><?php echo esc_html( get_the_title() ); ?></li>
		<?php endwhile; ?>
	</ul>
</div>
<?php
wp_reset_postdata();
