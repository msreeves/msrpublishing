<?php
/**
 * Subscribe preferences — topic/format choices for demo signup (P49).
 *
 * @package msrpublishing
 */

/**
 * Canonical format choices for subscribe preferences (nav-visible formats).
 *
 * @return array<int, array{slug: string, label: string}>
 */
function msr_publishing_get_subscribe_format_choices() {
	$registry = msr_publishing_get_format_registry();
	$choices  = array();

	foreach ( msr_publishing_get_format_slugs() as $slug ) {
		if ( empty( $registry[ $slug ] ) || empty( $registry[ $slug ]['nav'] ) ) {
			continue;
		}
		$choices[] = array(
			'slug'  => $slug,
			'label' => $registry[ $slug ]['label'],
		);
	}

	return $choices;
}

/**
 * Topic hub choices for subscribe preferences.
 *
 * @return array<int, array{slug: string, label: string}>
 */
function msr_publishing_get_subscribe_topic_choices() {
	$terms = function_exists( 'msr_publishing_get_footer_topic_terms' )
		? msr_publishing_get_footer_topic_terms()
		: get_terms(
			array(
				'taxonomy'   => 'topic',
				'hide_empty' => true,
				'orderby'    => 'name',
				'order'      => 'ASC',
				'number'     => 6,
			)
		);

	if ( is_wp_error( $terms ) || ! is_array( $terms ) ) {
		return array();
	}

	$choices = array();
	foreach ( $terms as $term ) {
		$choices[] = array(
			'slug'  => $term->slug,
			'label' => $term->name,
		);
	}

	return $choices;
}

/**
 * Copy helper — all live resource formats for subscribe CTAs.
 *
 * @return string
 */
function msr_publishing_get_subscribe_formats_summary() {
	return __( 'whitepapers, webinars, briefings, playbooks, case studies, videos, and podcasts', 'msrpublishing' );
}

/**
 * Render topic + format preference checkboxes on the subscribe form.
 *
 * @return void
 */
function msr_publishing_render_subscribe_preferences() {
	$topics  = msr_publishing_get_subscribe_topic_choices();
	$formats = msr_publishing_get_subscribe_format_choices();

	if ( ! $topics && ! $formats ) {
		return;
	}

	$formats_summary = msr_publishing_get_subscribe_formats_summary();
	?>
	<fieldset class="publishing-subscribe-preferences mb-4" data-subscribe-preferences>
		<legend class="form-label fw-semibold mb-1"><?php esc_html_e( 'Email preferences', 'msrpublishing' ); ?></legend>
		<p class="small text-muted mb-3">
			<?php
			printf(
				/* translators: %s: comma-separated list of resource formats */
				esc_html__( 'Optional — choose topic hubs and formats. We cover %s across Atlas Briefing.', 'msrpublishing' ),
				esc_html( $formats_summary )
			);
			?>
		</p>
		<div class="row g-3">
			<?php if ( $topics ) : ?>
				<div class="col-md-6">
					<p class="small fw-semibold mb-2" id="msr-subscribe-topics-label"><?php esc_html_e( 'Topic interests', 'msrpublishing' ); ?></p>
					<div class="publishing-subscribe-preferences__group" role="group" aria-labelledby="msr-subscribe-topics-label">
						<?php foreach ( $topics as $choice ) : ?>
							<?php
							$input_id = 'msr-subscribe-topic-' . sanitize_html_class( $choice['slug'] );
							?>
							<div class="form-check">
								<input
									class="form-check-input"
									type="checkbox"
									name="subscribe_topics[]"
									value="<?php echo esc_attr( $choice['slug'] ); ?>"
									id="<?php echo esc_attr( $input_id ); ?>"
								/>
								<label class="form-check-label small" for="<?php echo esc_attr( $input_id ); ?>">
									<?php echo esc_html( $choice['label'] ); ?>
								</label>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>
			<?php if ( $formats ) : ?>
				<div class="col-md-6">
					<p class="small fw-semibold mb-2" id="msr-subscribe-formats-label"><?php esc_html_e( 'Format interests', 'msrpublishing' ); ?></p>
					<div class="publishing-subscribe-preferences__group" role="group" aria-labelledby="msr-subscribe-formats-label">
						<?php foreach ( $formats as $choice ) : ?>
							<?php
							$input_id = 'msr-subscribe-format-' . sanitize_html_class( $choice['slug'] );
							?>
							<div class="form-check">
								<input
									class="form-check-input"
									type="checkbox"
									name="subscribe_formats[]"
									value="<?php echo esc_attr( $choice['slug'] ); ?>"
									id="<?php echo esc_attr( $input_id ); ?>"
								/>
								<label class="form-check-label small" for="<?php echo esc_attr( $input_id ); ?>">
									<?php echo esc_html( $choice['label'] ); ?>
								</label>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</fieldset>
	<?php
}
