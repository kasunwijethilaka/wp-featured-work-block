<?php
/**
 * Registers custom meta (client + project URL) for case studies,
 * with an accessible meta box and a secure save handler.
 *
 * @package FeaturedWork
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the case-study meta keys.
 *
 * Registered with `show_in_rest` so the values are available to the REST API and the
 * block editor. The underscore prefix keeps them out of the generic custom-fields UI,
 * so an `auth_callback` is required to allow editing.
 */
function featured_work_register_meta() {
	$can_edit = function () {
		return current_user_can( 'edit_posts' );
	};

	register_post_meta(
		'case_study',
		'_case_study_client',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback'     => $can_edit,
		)
	);

	register_post_meta(
		'case_study',
		'_case_study_url',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'esc_url_raw',
			'auth_callback'     => $can_edit,
		)
	);
}
add_action( 'init', 'featured_work_register_meta' );

/**
 * Add the "Case Study Details" meta box.
 */
function featured_work_add_meta_box() {
	add_meta_box(
		'featured_work_details',
		__( 'Case Study Details', 'featured-work' ),
		'featured_work_render_meta_box',
		'case_study',
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes', 'featured_work_add_meta_box' );

/**
 * Render the meta box fields.
 *
 * @param WP_Post $post The current post object.
 */
function featured_work_render_meta_box( $post ) {
	wp_nonce_field( 'featured_work_save_meta', 'featured_work_meta_nonce' );

	$client = get_post_meta( $post->ID, '_case_study_client', true );
	$url    = get_post_meta( $post->ID, '_case_study_url', true );
	?>
	<p>
		<label for="featured_work_client"><strong><?php esc_html_e( 'Client', 'featured-work' ); ?></strong></label>
		<input
			type="text"
			id="featured_work_client"
			name="featured_work_client"
			value="<?php echo esc_attr( $client ); ?>"
			class="widefat"
		/>
	</p>
	<p>
		<label for="featured_work_url"><strong><?php esc_html_e( 'Project URL', 'featured-work' ); ?></strong></label>
		<input
			type="url"
			id="featured_work_url"
			name="featured_work_url"
			value="<?php echo esc_url( $url ); ?>"
			placeholder="https://example.com"
			class="widefat"
		/>
	</p>
	<?php
}

/**
 * Save the meta box fields securely.
 *
 * Runs the full guard chain before writing: nonce -> autosave -> capability -> sanitize.
 *
 * @param int $post_id The post being saved.
 */
function featured_work_save_meta( $post_id ) {
	if ( ! isset( $_POST['featured_work_meta_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['featured_work_meta_nonce'] ) ), 'featured_work_save_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['featured_work_client'] ) ) {
		update_post_meta(
			$post_id,
			'_case_study_client',
			sanitize_text_field( wp_unslash( $_POST['featured_work_client'] ) )
		);
	}

	if ( isset( $_POST['featured_work_url'] ) ) {
		update_post_meta(
			$post_id,
			'_case_study_url',
			esc_url_raw( wp_unslash( $_POST['featured_work_url'] ) )
		);
	}
}
add_action( 'save_post_case_study', 'featured_work_save_meta' );
