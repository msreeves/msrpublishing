<?php
/**
 * Single UX — share tools, key takeaways, sticky TOC, dates (P39).
 *
 * @package msrsandbox
 */

/**
 * Minimum H2/H3 count before sticky TOC renders on whitepapers.
 */
function msr_publishing_sticky_toc_min_headings() {
	return 3;
}

/**
 * @param string $url Page URL.
 * @return string
 */
function msr_publishing_get_linkedin_share_url( $url ) {
	return 'https://www.linkedin.com/sharing/share-offsite/?url=' . rawurlencode( (string) $url );
}

/**
 * @param string $text Heading text.
 * @param int    $index 0-based index for duplicate slugs.
 * @return string
 */
function msr_publishing_heading_anchor_id( $text, $index = 0 ) {
	$base = sanitize_title( (string) $text );
	if ( $base === '' ) {
		$base = 'section';
	}

	return $index > 0 ? $base . '-' . ( (int) $index + 1 ) : $base;
}

/**
 * @param int $post_id Post ID.
 * @return array<int, array{id: string, text: string, level: int}>
 */
function msr_publishing_get_content_headings_with_ids( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$content = (string) get_post_field( 'post_content', $post_id );
	if ( $content === '' ) {
		return array();
	}

	if ( ! preg_match_all( '/<h([2-3])[^>]*>(.*?)<\/h\1>/is', $content, $matches, PREG_SET_ORDER ) ) {
		return array();
	}

	$slug_counts = array();
	$headings    = array();

	foreach ( $matches as $match ) {
		$text = trim( wp_strip_all_tags( (string) ( $match[2] ?? '' ) ) );
		if ( $text === '' ) {
			continue;
		}

		$base  = sanitize_title( $text );
		$base  = $base !== '' ? $base : 'section';
		$count = $slug_counts[ $base ] ?? 0;
		$slug_counts[ $base ] = $count + 1;

		$headings[] = array(
			'id'    => msr_publishing_heading_anchor_id( $text, $count ),
			'text'  => $text,
			'level' => (int) $match[1],
		);
	}

	return $headings;
}

/**
 * Add id attributes to h2/h3 in singular content for TOC anchors.
 *
 * @param string $content Post content HTML.
 * @return string
 */
function msr_publishing_add_heading_ids_to_content( $content ) {
	if ( ! is_singular( array( 'resource', 'post' ) ) || ! is_string( $content ) || $content === '' ) {
		return $content;
	}

	$slug_counts = array();

	return preg_replace_callback(
		'/<h([2-3])(\s[^>]*)?>(.*?)<\/h\1>/is',
		static function ( $match ) use ( &$slug_counts ) {
			$level = (int) $match[1];
			$attrs = (string) ( $match[2] ?? '' );
			$text  = trim( wp_strip_all_tags( (string) ( $match[3] ?? '' ) ) );
			if ( $text === '' ) {
				return $match[0];
			}

			if ( preg_match( '/\sid=(["\'])([^"\']+)\1/i', $attrs, $id_match ) ) {
				return $match[0];
			}

			$base  = sanitize_title( $text );
			$base  = $base !== '' ? $base : 'section';
			$count = $slug_counts[ $base ] ?? 0;
			$slug_counts[ $base ] = $count + 1;
			$id    = msr_publishing_heading_anchor_id( $text, $count );

			return sprintf(
				'<h%d%s id="%s">%s</h%d>',
				$level,
				$attrs,
				esc_attr( $id ),
				$match[3],
				$level
			);
		},
		$content
	);
}
add_filter( 'the_content', 'msr_publishing_add_heading_ids_to_content', 12 );

/**
 * @param int $post_id Post ID.
 * @return string[]
 */
function msr_publishing_get_key_takeaways( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$items   = array();

	if ( ! function_exists( 'get_field' ) ) {
		return $items;
	}

	$rows = get_field( 'key_takeaways', $post_id );
	if ( ! is_array( $rows ) ) {
		return $items;
	}

	foreach ( $rows as $row ) {
		$text = is_array( $row ) ? trim( (string) ( $row['text'] ?? '' ) ) : '';
		if ( $text !== '' ) {
			$items[] = $text;
		}
	}

	return $items;
}

/**
 * @param int $post_id Post ID.
 * @return bool
 */
function msr_publishing_should_render_sticky_toc( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	if ( 'resource' !== get_post_type( $post_id ) ) {
		return false;
	}

	$format = function_exists( 'msr_publishing_get_primary_resource_type_slug' )
		? msr_publishing_get_primary_resource_type_slug( $post_id )
		: '';

	if ( 'whitepaper' !== $format ) {
		return false;
	}

	return count( msr_publishing_get_content_headings_with_ids( $post_id ) ) >= msr_publishing_sticky_toc_min_headings();
}

/**
 * @param int $post_id Post ID.
 * @return bool
 */
function msr_publishing_post_has_updated_date( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$published = get_post_time( 'U', true, $post_id );
	$modified  = get_post_modified_time( 'U', true, $post_id );

	return $published > 0 && $modified > $published + DAY_IN_SECONDS;
}

/**
 * @param int $post_id Post ID.
 * @return void
 */
function msr_publishing_render_single_dates( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	if ( ! $post_id ) {
		return;
	}
	?>
	<p class="publishing-single-dates resource-single__meta small text-muted mb-0">
		<span class="publishing-single-dates__published">
			<span class="visually-hidden"><?php esc_html_e( 'Published', 'msrsandbox' ); ?></span>
			<time datetime="<?php echo esc_attr( get_the_date( 'c', $post_id ) ); ?>"><?php echo esc_html( get_the_date( '', $post_id ) ); ?></time>
		</span>
		<?php if ( msr_publishing_post_has_updated_date( $post_id ) ) : ?>
			<span class="publishing-single-dates__separator" aria-hidden="true"> · </span>
			<span class="publishing-single-dates__updated">
				<span class="visually-hidden"><?php esc_html_e( 'Updated', 'msrsandbox' ); ?></span>
				<time datetime="<?php echo esc_attr( get_the_modified_date( 'c', $post_id ) ); ?>"><?php echo esc_html( get_the_modified_date( '', $post_id ) ); ?></time>
			</span>
		<?php endif; ?>
	</p>
	<?php
}

/**
 * @param int $post_id Post ID.
 * @return void
 */
function msr_publishing_render_single_share_tools( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$url     = get_permalink( $post_id );
	if ( ! $url ) {
		return;
	}

	$linkedin = msr_publishing_get_linkedin_share_url( $url );
	?>
	<div class="publishing-single-share" data-share-url="<?php echo esc_url( $url ); ?>">
		<p class="publishing-single-share__label small text-uppercase fw-semibold text-muted mb-2"><?php esc_html_e( 'Share', 'msrsandbox' ); ?></p>
		<div class="publishing-single-share__actions d-flex flex-wrap gap-2 align-items-center">
			<button type="button" class="btn btn-sm btn-outline-secondary publishing-single-share__copy" data-copy-url="<?php echo esc_url( $url ); ?>">
				<i class="fa-solid fa-link me-1" aria-hidden="true"></i>
				<?php esc_html_e( 'Copy link', 'msrsandbox' ); ?>
			</button>
			<a class="btn btn-sm btn-outline-primary publishing-single-share__linkedin" href="<?php echo esc_url( $linkedin ); ?>" target="_blank" rel="noopener noreferrer">
				<i class="fa-brands fa-linkedin me-1" aria-hidden="true"></i>
				<?php esc_html_e( 'LinkedIn', 'msrsandbox' ); ?>
			</a>
			<span class="publishing-single-share__status small text-success" role="status" aria-live="polite" hidden></span>
		</div>
	</div>
	<?php
}

/**
 * @param int $post_id Post ID.
 * @return void
 */
function msr_publishing_render_key_takeaways( $post_id = 0 ) {
	$items = msr_publishing_get_key_takeaways( $post_id );
	if ( ! $items ) {
		return;
	}
	?>
	<section class="publishing-key-takeaways mb-4" aria-labelledby="publishing-key-takeaways-title">
		<h2 id="publishing-key-takeaways-title" class="h5 mb-3"><?php esc_html_e( 'Key takeaways', 'msrsandbox' ); ?></h2>
		<ul class="publishing-key-takeaways__list mb-0">
			<?php foreach ( $items as $item ) : ?>
				<li><?php echo esc_html( $item ); ?></li>
			<?php endforeach; ?>
		</ul>
	</section>
	<?php
}

/**
 * @param int $post_id Post ID.
 * @return void
 */
function msr_publishing_render_sticky_toc( $post_id = 0 ) {
	$post_id  = $post_id ? (int) $post_id : get_the_ID();
	$headings = msr_publishing_get_content_headings_with_ids( $post_id );

	if ( count( $headings ) < msr_publishing_sticky_toc_min_headings() ) {
		return;
	}
	?>
	<nav class="publishing-single-toc publishing-single-toc--sticky" aria-labelledby="publishing-single-toc-title">
		<p id="publishing-single-toc-title" class="publishing-single-toc__label small text-uppercase fw-semibold text-muted mb-2"><?php esc_html_e( 'On this page', 'msrsandbox' ); ?></p>
		<ol class="publishing-single-toc__list small mb-0">
			<?php foreach ( $headings as $heading ) : ?>
				<li class="publishing-single-toc__item publishing-single-toc__item--h<?php echo esc_attr( (string) $heading['level'] ); ?>">
					<a class="publishing-single-toc__link" href="#<?php echo esc_attr( $heading['id'] ); ?>"><?php echo esc_html( $heading['text'] ); ?></a>
				</li>
			<?php endforeach; ?>
		</ol>
	</nav>
	<?php
}

/**
 * Share + dates band below single headers.
 *
 * @param int $post_id Post ID.
 * @return void
 */
function msr_publishing_render_single_utility_band( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	?>
	<div class="publishing-single-utility row g-3 align-items-end mb-4">
		<div class="col-md-auto">
			<?php msr_publishing_render_single_share_tools( $post_id ); ?>
		</div>
		<div class="col-md ms-md-auto text-md-end">
			<?php msr_publishing_render_single_dates( $post_id ); ?>
		</div>
	</div>
	<?php
}

/**
 * Outcome metric rows for case study resources.
 *
 * @param int $post_id Post ID.
 * @return array<int, array{value: string, label: string}>
 */
function msr_publishing_get_case_study_outcome_metrics( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	if ( ! $post_id || ! function_exists( 'get_field' ) ) {
		return array();
	}

	$rows = get_field( 'case_study_outcome_metrics', $post_id );
	if ( ! is_array( $rows ) ) {
		return array();
	}

	$metrics = array();
	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}
		$value = trim( (string) ( $row['metric_value'] ?? '' ) );
		$label = trim( (string) ( $row['metric_label'] ?? '' ) );
		if ( $value === '' && $label === '' ) {
			continue;
		}
		$metrics[] = array(
			'value' => $value,
			'label' => $label,
		);
	}

	return $metrics;
}

/**
 * @param int $post_id Post ID.
 * @return void
 */
function msr_publishing_render_case_study_industry( $post_id = 0 ) {
	$post_id  = $post_id ? (int) $post_id : get_the_ID();
	$industry = function_exists( 'get_field' ) ? trim( (string) get_field( 'case_study_client_industry', $post_id ) ) : '';
	if ( $industry === '' ) {
		return;
	}
	?>
	<p class="resource-case-study__industry mb-4">
		<span class="badge text-bg-light border resource-case-study__industry-badge">
			<i class="fa-solid fa-building me-1" aria-hidden="true"></i>
			<?php echo esc_html( $industry ); ?>
		</span>
	</p>
	<?php
}

/**
 * @param int $post_id Post ID.
 * @return void
 */
function msr_publishing_render_case_study_metrics( $post_id = 0 ) {
	$metrics = msr_publishing_get_case_study_outcome_metrics( $post_id );
	if ( ! $metrics ) {
		return;
	}
	?>
	<section class="resource-case-study__metrics mb-4" aria-labelledby="resource-case-study-metrics-title">
		<h2 id="resource-case-study-metrics-title" class="h6 text-uppercase fw-semibold text-muted mb-3"><?php esc_html_e( 'Outcome metrics', 'msrsandbox' ); ?></h2>
		<div class="row g-3 msr-reveal-stagger">
			<?php foreach ( $metrics as $metric ) : ?>
				<div class="col-6 col-md-4">
					<div class="resource-case-study__metric card h-100 text-center">
						<?php if ( $metric['value'] !== '' ) : ?>
							<p class="resource-case-study__metric-value h4 mb-1"><?php echo esc_html( $metric['value'] ); ?></p>
						<?php endif; ?>
						<?php if ( $metric['label'] !== '' ) : ?>
							<p class="resource-case-study__metric-label small text-muted mb-0"><?php echo esc_html( $metric['label'] ); ?></p>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</section>
	<?php
}

/**
 * @param int $post_id Post ID.
 * @return void
 */
function msr_publishing_render_case_study_narrative( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	if ( ! function_exists( 'get_field' ) ) {
		return;
	}

	$sections = array(
		'challenge' => array(
			'label' => __( 'Challenge', 'msrsandbox' ),
			'value' => trim( (string) get_field( 'case_study_challenge', $post_id ) ),
		),
		'approach'  => array(
			'label' => __( 'Approach', 'msrsandbox' ),
			'value' => trim( (string) get_field( 'case_study_approach', $post_id ) ),
		),
		'results'   => array(
			'label' => __( 'Results', 'msrsandbox' ),
			'value' => trim( (string) get_field( 'case_study_results', $post_id ) ),
		),
	);

	$has_content = false;
	foreach ( $sections as $section ) {
		if ( $section['value'] !== '' ) {
			$has_content = true;
			break;
		}
	}

	if ( ! $has_content ) {
		return;
	}
	?>
	<div class="resource-case-study__narrative mb-4">
		<?php foreach ( $sections as $slug => $section ) : ?>
			<?php if ( $section['value'] === '' ) { continue; } ?>
			<section class="resource-case-study__section mb-4" aria-labelledby="resource-case-study-<?php echo esc_attr( $slug ); ?>">
				<h2 id="resource-case-study-<?php echo esc_attr( $slug ); ?>" class="h5 mb-2"><?php echo esc_html( $section['label'] ); ?></h2>
				<div class="resource-case-study__section-body">
					<?php echo wp_kses_post( wpautop( $section['value'] ) ); ?>
				</div>
			</section>
		<?php endforeach; ?>
	</div>
	<?php
}
