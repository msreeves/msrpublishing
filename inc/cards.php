<?php
/**
 * Card metadata, home hero featured visual, whitepaper preview (audit closure).
 *
 * @package msrsandbox
 */

/**
 * Site-wide featured resource ID (`featured` meta), cached per request.
 *
 * @return int
 */
function msr_publishing_get_site_featured_resource_id() {
	static $cached = null;

	if ( null !== $cached ) {
		return (int) $cached;
	}

	if ( function_exists( 'get_field' ) ) {
		$pick = get_field( 'home_featured_resource', 'option' );
		$from_option = 0;
		if ( is_object( $pick ) && isset( $pick->ID ) ) {
			$from_option = (int) $pick->ID;
		} elseif ( is_numeric( $pick ) ) {
			$from_option = (int) $pick;
		}
		if ( $from_option > 0 && 'resource' === get_post_type( $from_option ) && 'publish' === get_post_status( $from_option ) ) {
			$cached = $from_option;
			return (int) $cached;
		}
	}

	$query = new WP_Query(
		array(
			'post_type'           => 'resource',
			'posts_per_page'      => 1,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'fields'              => 'ids',
			'orderby'             => 'date',
			'order'               => 'DESC',
			'meta_query'          => array(
				array(
					'key'     => 'featured',
					'value'   => '1',
					'compare' => '=',
				),
			),
		)
	);

	$cached = $query->have_posts() ? (int) $query->posts[0] : 0;
	return (int) $cached;
}

/**
 * Stored hero/thumbnail on a post or resource (sanitized; no display fallback).
 *
 * @param int $post_id Post ID.
 * @return int
 */
function msr_publishing_get_stored_hero_attachment_id( $post_id ) {
	$post_id = (int) $post_id;
	if ( ! $post_id ) {
		return 0;
	}

	$hero_id = 0;
	if ( 'resource' === get_post_type( $post_id ) && function_exists( 'get_field' ) ) {
		$hero_id = (int) get_field( 'hero_image', $post_id );
	}
	if ( ! $hero_id ) {
		$hero_id = (int) get_post_thumbnail_id( $post_id );
	}
	if ( function_exists( 'msr_publishing_sanitize_hero_attachment_id' ) ) {
		$hero_id = msr_publishing_sanitize_hero_attachment_id( $hero_id );
	}

	return (int) $hero_id;
}

/**
 * Card/single hero attachment — stored value or deterministic editorial stock fallback.
 *
 * @param int $post_id Post ID.
 * @return int
 */
function msr_publishing_get_card_hero_attachment_id( $post_id ) {
	$post_id = (int) $post_id;
	$hero_id = msr_publishing_get_stored_hero_attachment_id( $post_id );
	if ( $hero_id ) {
		return $hero_id;
	}

	if ( ! function_exists( 'msr_publishing_get_editorial_stock_image_ids' ) ) {
		return 0;
	}

	$stock = msr_publishing_get_editorial_stock_image_ids( 20 );
	if ( ! $stock ) {
		return 0;
	}

	return (int) $stock[ $post_id % count( $stock ) ];
}

/**
 * Font Awesome icon class for card media placeholder bands.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function msr_publishing_get_card_placeholder_icon( $post_id ) {
	$post_id = (int) $post_id;
	if ( 'resource' === get_post_type( $post_id ) && function_exists( 'msr_publishing_get_primary_resource_type' ) ) {
		$primary = msr_publishing_get_primary_resource_type( $post_id );
		if ( $primary && function_exists( 'msr_publishing_resolve_format_slug' ) && function_exists( 'msr_publishing_get_format_registry' ) ) {
			$canonical = msr_publishing_resolve_format_slug( $primary->slug );
			$registry  = msr_publishing_get_format_registry();
			if ( isset( $registry[ $canonical ]['icon'] ) ) {
				return (string) $registry[ $canonical ]['icon'];
			}
		}

		return 'fa-file-lines';
	}

	return 'fa-newspaper';
}

/**
 * Linked card media band (image or format placeholder).
 *
 * @param array{
 *   post_id?: int,
 *   href?: string,
 *   newtab?: bool,
 *   title?: string,
 *   aria_label?: string,
 *   size?: string,
 *   img_class?: string,
 *   media_class?: string,
 *   loading?: string
 * } $args Render args.
 * @return void
 */
function msr_publishing_render_card_media( array $args = array() ) {
	$post_id = isset( $args['post_id'] ) ? (int) $args['post_id'] : (int) get_the_ID();
	if ( ! $post_id ) {
		return;
	}

	$title      = isset( $args['title'] ) ? (string) $args['title'] : get_the_title( $post_id );
	$href       = isset( $args['href'] ) ? (string) $args['href'] : get_permalink( $post_id );
	$newtab     = ! empty( $args['newtab'] );
	$aria_label = isset( $args['aria_label'] )
		? (string) $args['aria_label']
		: sprintf(
			/* translators: %s: card title */
			__( 'View: %s', 'msrsandbox' ),
			$title
		);
	$size        = isset( $args['size'] ) ? (string) $args['size'] : 'medium_large';
	$img_class   = isset( $args['img_class'] ) ? (string) $args['img_class'] : 'msr-card-media__img card-img-top';
	$media_class = isset( $args['media_class'] ) ? (string) $args['media_class'] : 'msr-card-media';
	$loading     = isset( $args['loading'] ) ? (string) $args['loading'] : 'lazy';
	$hero_id     = msr_publishing_get_card_hero_attachment_id( $post_id );
	$placeholder = ! $hero_id;
	if ( $placeholder ) {
		$media_class .= ' msr-card-media--placeholder';
	}
	?>
	<a
		class="<?php echo esc_attr( $media_class ); ?>"
		href="<?php echo esc_url( $href ); ?>"
		aria-label="<?php echo esc_attr( $aria_label ); ?>"
		<?php echo $newtab ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>
	>
		<?php if ( $hero_id ) : ?>
			<?php
			echo wp_get_attachment_image(
				$hero_id,
				$size,
				false,
				array(
					'class'   => $img_class,
					'loading' => $loading,
					'alt'     => $title,
				)
			);
			?>
		<?php else : ?>
			<span class="msr-card-media__placeholder" aria-hidden="true">
				<i class="fa-solid <?php echo esc_attr( msr_publishing_get_card_placeholder_icon( $post_id ) ); ?>"></i>
			</span>
		<?php endif; ?>
	</a>
	<?php
}

/**
 * Hero attachment for the site featured resource.
 *
 * @return int
 */
function msr_publishing_get_site_featured_resource_hero_id() {
	$post_id = msr_publishing_get_site_featured_resource_id();
	if ( ! $post_id ) {
		return 0;
	}

	return msr_publishing_get_card_hero_attachment_id( $post_id );
}

/**
 * Primary topic term for a post/resource.
 *
 * @param int    $post_id Post ID.
 * @param string $taxonomy Taxonomy slug.
 * @return WP_Term|null
 */
function msr_publishing_get_primary_topic_term( $post_id, $taxonomy = 'topic' ) {
	$terms = get_the_terms( (int) $post_id, $taxonomy );
	if ( ! $terms || is_wp_error( $terms ) ) {
		return null;
	}

	return $terms[0];
}

/**
 * @param array<int, array{label: string, datetime?: string, class?: string}> $segments Card meta segments.
 * @return void
 */
function msr_publishing_render_card_meta( array $segments ) {
	$segments = array_values(
		array_filter(
			$segments,
			static function ( $segment ) {
				if ( is_array( $segment ) ) {
					return trim( (string) ( $segment['label'] ?? '' ) ) !== '';
				}
				return trim( (string) $segment ) !== '';
			}
		)
	 );

	if ( ! $segments ) {
		return;
	}
	?>
	<p class="card-meta mb-0 mt-auto">
		<?php
		$total = count( $segments );
		foreach ( $segments as $index => $segment ) {
			$label = is_array( $segment ) ? (string) $segment['label'] : (string) $segment;
			$class = is_array( $segment ) ? (string) ( $segment['class'] ?? '' ) : '';
			$datetime = is_array( $segment ) ? (string) ( $segment['datetime'] ?? '' ) : '';

			if ( $datetime !== '' ) {
				printf(
					'<time class="card-meta__date%s" datetime="%s">%s</time>',
					$class !== '' ? ' ' . esc_attr( $class ) : '',
					esc_attr( $datetime ),
					esc_html( $label )
				);
			} else {
				printf(
					'<span class="card-meta__item%s">%s</span>',
					$class !== '' ? ' ' . esc_attr( $class ) : '',
					esc_html( $label )
				);
			}

			if ( $index < $total - 1 ) {
				echo '<span class="card-meta__sep" aria-hidden="true"> · </span>';
			}
		}
		?>
	</p>
	<?php
}

/**
 * @param int $post_id Resource ID.
 * @return void
 */
function msr_publishing_render_resource_card_meta( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$segments = array(
		array(
			'label'    => get_the_date( '', $post_id ),
			'datetime' => get_the_date( 'c', $post_id ),
			'class'    => 'card-meta__date',
		),
	);

	$mins = function_exists( 'get_field' ) ? (int) get_field( 'read_time_minutes', $post_id ) : 0;
	if ( $mins > 0 ) {
		$segments[] = array(
			'label' => sprintf(
				/* translators: %d: minutes */
				_n( '%d min read', '%d min read', $mins, 'msrsandbox' ),
				$mins
			),
			'class' => 'card-meta__reading',
		);
	}

	$format = msr_publishing_get_primary_resource_type( $post_id );
	if ( $format ) {
		$segments[] = array(
			'label' => $format->name,
			'class' => 'card-meta__format',
		);
	}

	$topic = msr_publishing_get_primary_topic_term( $post_id );
	if ( $topic ) {
		$segments[] = array(
			'label' => $topic->name,
			'class' => 'card-meta__topic',
		);
	}

	msr_publishing_render_card_meta( $segments );
}

/**
 * @param int $post_id Post ID.
 * @return void
 */
function msr_publishing_render_post_card_meta( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$segments = array(
		array(
			'label'    => get_the_date( '', $post_id ),
			'datetime' => get_the_date( 'c', $post_id ),
			'class'    => 'card-meta__date',
		),
		array(
			'label' => __( 'Commentary', 'msrsandbox' ),
			'class' => 'card-meta__type',
		),
	);

	$topic = msr_publishing_get_primary_topic_term( $post_id );
	if ( $topic ) {
		$segments[] = array(
			'label' => $topic->name,
			'class' => 'card-meta__topic',
		);
	}

	msr_publishing_render_card_meta( $segments );
}

/**
 * Heading outline from post HTML for whitepaper preview TOC.
 *
 * @param int $post_id Post ID.
 * @param int $limit   Max headings.
 * @return array<int, string>
 */
function msr_publishing_get_content_heading_outline( $post_id, $limit = 6 ) {
	$content = (string) get_post_field( 'post_content', (int) $post_id );
	if ( $content === '' ) {
		return array();
	}

	if ( ! preg_match_all( '/<h[2-3][^>]*>(.*?)<\/h[2-3]>/is', $content, $matches ) ) {
		return array();
	}

	$headings = array();
	foreach ( $matches[1] as $raw ) {
		$text = trim( wp_strip_all_tags( (string) $raw ) );
		if ( $text !== '' ) {
			$headings[] = $text;
		}
		if ( count( $headings ) >= $limit ) {
			break;
		}
	}

	return $headings;
}

/**
 * Abstract + TOC preview above whitepaper gate.
 *
 * @param int $post_id Resource ID.
 * @return void
 */
function msr_publishing_render_whitepaper_preview( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$excerpt = get_the_excerpt( $post_id );
	if ( $excerpt === '' ) {
		$excerpt = wp_trim_words( wp_strip_all_tags( (string) get_post_field( 'post_content', $post_id ) ), 42, '…' );
	}

	$outline = msr_publishing_get_content_heading_outline( $post_id );
	if ( $excerpt === '' && ! $outline ) {
		return;
	}
	?>
	<section class="resource-whitepaper-preview mb-4" aria-labelledby="resource-whitepaper-preview-title">
		<h2 id="resource-whitepaper-preview-title" class="h5 mb-3"><?php esc_html_e( 'In this whitepaper', 'msrsandbox' ); ?></h2>
		<?php if ( $excerpt !== '' ) : ?>
			<p class="resource-whitepaper-preview__abstract text-muted mb-3"><?php echo esc_html( wp_strip_all_tags( $excerpt ) ); ?></p>
		<?php endif; ?>
		<?php if ( $outline ) : ?>
			<ul class="resource-whitepaper-preview__toc small mb-0">
				<?php foreach ( $outline as $heading ) : ?>
					<li><?php echo esc_html( $heading ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</section>
	<?php
}
