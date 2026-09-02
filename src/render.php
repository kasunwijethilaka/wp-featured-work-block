<?php
/**
 * Server-side render for the Featured Work block.
 *
 * Renders an accessible, filterable grid of case studies from a WP_Query.
 * All dynamic output is escaped; the global query is reset after the loop.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Inner block content (unused).
 * @var WP_Block $block      Block instance.
 *
 * @package FeaturedWork
 */

$posts_to_show = isset( $attributes['postsToShow'] ) ? (int) $attributes['postsToShow'] : 6;
$columns       = isset( $attributes['columns'] ) ? (int) $attributes['columns'] : 3;
$project_type  = isset( $attributes['projectType'] ) ? sanitize_title( $attributes['projectType'] ) : '';
$order         = isset( $attributes['order'] ) && 'asc' === $attributes['order'] ? 'ASC' : 'DESC';
$order_by      = isset( $attributes['orderBy'] ) ? sanitize_key( $attributes['orderBy'] ) : 'date';
$show_excerpt  = ! isset( $attributes['showExcerpt'] ) || (bool) $attributes['showExcerpt'];
$show_filter   = ! isset( $attributes['showFilter'] ) || (bool) $attributes['showFilter'];

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

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'columns-' . $columns ) );

// Empty state.
if ( ! $query->have_posts() ) {
	printf(
		'<div %1$s><p class="fw-empty">%2$s</p></div>',
		$wrapper_attributes, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by core.
		esc_html__( 'No case studies found yet.', 'featured-work' )
	);
	return;
}

// Collect items and the set of project types actually present, so the filter
// bar only lists types that appear in the results.
$items         = array();
$present_terms = array();

while ( $query->have_posts() ) {
	$query->the_post();
	$post_id = get_the_ID();

	$terms      = get_the_terms( $post_id, 'project_type' );
	$term_slugs = array();
	$first_term = null;

	if ( $terms && ! is_wp_error( $terms ) ) {
		foreach ( $terms as $term ) {
			$term_slugs[]                 = $term->slug;
			$present_terms[ $term->slug ] = $term->name;
			if ( null === $first_term ) {
				$first_term = $term;
			}
		}
	}

	$items[] = array(
		'id'         => $post_id,
		'title'      => get_the_title(),
		'permalink'  => get_permalink(),
		'excerpt'    => get_the_excerpt(),
		'client'     => get_post_meta( $post_id, '_case_study_client', true ),
		'thumbnail'  => has_post_thumbnail( $post_id )
			? get_the_post_thumbnail( $post_id, 'large', array( 'class' => 'fw-card__image', 'loading' => 'lazy' ) )
			: '',
		'term_slugs' => $term_slugs,
		'first_term' => $first_term,
	);
}
wp_reset_postdata();

asort( $present_terms );

$grid_id    = wp_unique_id( 'fw-grid-' );
$total      = count( $items );
$has_filter = $show_filter && ! $project_type && count( $present_terms ) > 1;
?>
<div <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by core. ?>>

	<?php if ( $has_filter ) : ?>
		<div class="fw-filter" role="toolbar" aria-label="<?php esc_attr_e( 'Filter case studies by project type', 'featured-work' ); ?>">
			<button type="button" class="fw-filter__btn is-active" data-filter="" aria-pressed="true" aria-controls="<?php echo esc_attr( $grid_id ); ?>">
				<?php esc_html_e( 'All', 'featured-work' ); ?>
			</button>
			<?php foreach ( $present_terms as $slug => $name ) : ?>
				<button type="button" class="fw-filter__btn" data-filter="<?php echo esc_attr( $slug ); ?>" aria-pressed="false" aria-controls="<?php echo esc_attr( $grid_id ); ?>">
					<?php echo esc_html( $name ); ?>
				</button>
			<?php endforeach; ?>
		</div>
		<p class="fw-status" role="status" aria-live="polite">
			<?php
			/* translators: %d: number of case studies shown. */
			printf( esc_html( _n( '%d project shown', '%d projects shown', $total, 'featured-work' ) ), (int) $total );
			?>
		</p>
	<?php endif; ?>

	<ul id="<?php echo esc_attr( $grid_id ); ?>" class="fw-grid" role="list" style="--fw-columns: <?php echo esc_attr( $columns ); ?>;">
		<?php foreach ( $items as $item ) : ?>
			<li class="fw-grid__item" data-project-types="<?php echo esc_attr( implode( ' ', $item['term_slugs'] ) ); ?>">
				<article class="fw-card">
					<figure class="fw-card__media">
						<?php if ( $item['thumbnail'] ) : ?>
							<?php echo wp_kses_post( $item['thumbnail'] ); ?>
						<?php else : ?>
							<?php
							$hue      = (int) ( hexdec( substr( md5( $item['title'] ), 0, 2 ) ) / 255 * 360 );
							$style    = sprintf( 'background:linear-gradient(135deg, hsl(%1$ddeg 55%% 52%%), hsl(%2$ddeg 55%% 42%%));', $hue, ( $hue + 40 ) % 360 );
							$words    = preg_split( '/\s+/', trim( $item['title'] ) );
							$initials = '';
							foreach ( array_slice( $words, 0, 2 ) as $word ) {
								$initials .= mb_substr( $word, 0, 1 );
							}
							?>
							<span class="fw-card__placeholder" style="<?php echo esc_attr( $style ); ?>" aria-hidden="true">
								<span class="fw-card__initials"><?php echo esc_html( mb_strtoupper( $initials ) ); ?></span>
							</span>
						<?php endif; ?>
					</figure>

					<div class="fw-card__body">
						<?php if ( $item['first_term'] ) : ?>
							<span class="fw-card__tag"><?php echo esc_html( $item['first_term']->name ); ?></span>
						<?php endif; ?>

						<h3 class="fw-card__title">
							<a class="fw-card__link" href="<?php echo esc_url( $item['permalink'] ); ?>">
								<?php echo esc_html( $item['title'] ); ?>
							</a>
						</h3>

						<?php if ( $item['client'] ) : ?>
							<p class="fw-card__client"><?php echo esc_html( $item['client'] ); ?></p>
						<?php endif; ?>

						<?php if ( $show_excerpt && $item['excerpt'] ) : ?>
							<p class="fw-card__excerpt"><?php echo esc_html( $item['excerpt'] ); ?></p>
						<?php endif; ?>
					</div>
				</article>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
