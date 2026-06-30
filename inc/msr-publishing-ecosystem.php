<?php
/**
 * MSR Events programme links (hub, awards, seminars) for Atlas Briefing resources.
 *
 * @package msrsandbox
 */

/**
 * Default programme registry (local demo URLs).
 *
 * @return array<string, array{label: string, url: string, description: string, cta: string}>
 */
function msr_publishing_get_programme_registry_defaults() {
	return array(
		'hub'      => array(
			'label'       => __( 'MSR Events hub', 'msrsandbox' ),
			'url'         => 'http://msrevents.local:8888/',
			'description' => __( 'Programmes, events, and editorial from the central hub.', 'msrsandbox' ),
			'cta'         => __( 'Visit the events hub', 'msrsandbox' ),
			'icon'        => 'fa-solid fa-calendar-days',
			'meta'        => __( 'Live programmes', 'msrsandbox' ),
		),
		'awards'   => array(
			'label'       => __( 'MSR Awards', 'msrsandbox' ),
			'url'         => 'http://msrevents.local:8888/msrawards/',
			'description' => __( 'Awards programme, nominees, and industry recognition.', 'msrsandbox' ),
			'cta'         => __( 'Explore MSR Awards', 'msrsandbox' ),
			'icon'        => 'fa-solid fa-trophy',
			'meta'        => __( 'Awards season', 'msrsandbox' ),
		),
		'seminars' => array(
			'label'       => __( 'MSR Seminars', 'msrsandbox' ),
			'url'         => 'http://msrevents.local:8888/msrseminars/',
			'description' => __( 'Delegate seminars, agendas, and speaker content.', 'msrsandbox' ),
			'cta'         => __( 'View MSR Seminars', 'msrsandbox' ),
			'icon'        => 'fa-solid fa-chalkboard-user',
			'meta'        => __( 'Delegate seminars', 'msrsandbox' ),
		),
	);
}

/**
 * Whether programme links should use 127.0.0.1 paths (no msrevents.local vhost).
 *
 * @return bool
 */
function msr_publishing_use_programme_ip_fallback() {
	if ( getenv( 'MSR_PROGRAMME_IP_FALLBACK' ) === '1' || getenv( 'MSR_PROGRAMME_IP_FALLBACK' ) === 'true' ) {
		return true;
	}

	if ( isset( $_SERVER['HTTP_HOST'] ) && strpos( (string) $_SERVER['HTTP_HOST'], '127.0.0.1' ) === 0 ) {
		return true;
	}

	return (bool) apply_filters( 'msr_publishing_use_programme_ip_fallback', false );
}

/**
 * Map msrevents.local programme URLs to MAMP docroot paths.
 *
 * @return array<string, string>
 */
function msr_publishing_get_programme_ip_fallback_map() {
	return array(
		'http://msrevents.local:8888/'           => 'http://127.0.0.1:8888/sites/wp/events/',
		'http://msrevents.local:8888/msrawards/' => 'http://127.0.0.1:8888/sites/wp/events/msrawards/',
		'http://msrevents.local:8888/msrseminars/' => 'http://127.0.0.1:8888/sites/wp/events/msrseminars/',
	);
}

/**
 * Resolve programme URL for the current environment.
 *
 * @param string $url Programme URL.
 * @return string
 */
function msr_publishing_resolve_programme_url( $url ) {
	$url = trim( (string) $url );
	if ( $url === '' || ! msr_publishing_use_programme_ip_fallback() ) {
		return $url;
	}

	foreach ( msr_publishing_get_programme_ip_fallback_map() as $from => $to ) {
		if ( strpos( $url, $from ) === 0 ) {
			return $to . substr( $url, strlen( $from ) );
		}
	}

	return $url;
}

/**
 * Programme registry — ACF options first, then defaults.
 *
 * @return array<string, array{label: string, url: string, description: string, cta: string}>
 */
function msr_publishing_get_programme_registry() {
	$defaults = msr_publishing_get_programme_registry_defaults();
	$option_map = array(
		'hub'      => 'msr_programme_hub_url',
		'awards'   => 'msr_programme_awards_url',
		'seminars' => 'msr_programme_seminars_url',
	);

	foreach ( $option_map as $slug => $field ) {
		if ( ! function_exists( 'get_field' ) ) {
			continue;
		}
		$url = (string) get_field( $field, 'option' );
		if ( '' !== trim( $url ) ) {
			$defaults[ $slug ]['url'] = esc_url_raw( $url );
		}
	}

	foreach ( $defaults as $slug => $programme ) {
		$defaults[ $slug ]['url'] = msr_publishing_resolve_programme_url( $programme['url'] );
	}

	return $defaults;
}

/**
 * List shape for archive ecosystem band.
 *
 * @return array<int, array{label: string, url: string, description: string, icon?: string, meta?: string}>
 */
function msr_publishing_get_ecosystem_programmes() {
	$list = array();
	foreach ( msr_publishing_get_programme_registry() as $programme ) {
		$item = array(
			'label'       => $programme['label'],
			'url'         => $programme['url'],
			'description' => $programme['description'],
		);
		if ( ! empty( $programme['icon'] ) ) {
			$item['icon'] = (string) $programme['icon'];
		}
		if ( ! empty( $programme['meta'] ) ) {
			$item['meta'] = (string) $programme['meta'];
		}
		$list[] = $item;
	}
	return $list;
}

/**
 * @param int $post_id Resource post ID.
 * @return string hub|awards|seminars or empty.
 */
function msr_publishing_get_resource_programme_slug( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$slug    = function_exists( 'get_field' ) ? (string) get_field( 'msr_programme', $post_id ) : '';
	if ( $slug === '' ) {
		$slug = (string) get_post_meta( $post_id, 'msr_programme', true );
	}
	$slug     = sanitize_key( $slug );
	$registry = msr_publishing_get_programme_registry();
	return isset( $registry[ $slug ] ) ? $slug : '';
}

/**
 * @param int $post_id Resource post ID.
 * @return array{slug: string, label: string, url: string, description: string, cta: string}|null
 */
function msr_publishing_get_resource_programme( $post_id = 0 ) {
	$slug = msr_publishing_get_resource_programme_slug( $post_id );
	if ( $slug === '' ) {
		return null;
	}
	$registry = msr_publishing_get_programme_registry();
	$data     = $registry[ $slug ];
	return array_merge( array( 'slug' => $slug ), $data );
}

/**
 * Programme CTA on resource singles when `msr_programme` is set.
 *
 * @param int $post_id Resource post ID.
 * @return void
 */
function msr_publishing_render_resource_programme_cta( $post_id = 0 ) {
	$programme = msr_publishing_get_resource_programme( $post_id );
	if ( ! $programme ) {
		return;
	}
	?>
	<aside class="resource-single__programme card border mb-4" aria-label="<?php esc_attr_e( 'MSR programme link', 'msrsandbox' ); ?>">
		<div class="card-body d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
			<div>
				<p class="small text-uppercase fw-semibold text-muted mb-1"><?php esc_html_e( 'MSR programme link', 'msrsandbox' ); ?></p>
				<p class="mb-0"><?php echo esc_html( $programme['description'] ); ?></p>
			</div>
			<a class="btn btn-outline-primary" href="<?php echo esc_url( $programme['url'] ); ?>">
				<?php echo esc_html( $programme['cta'] ); ?>
			</a>
		</div>
	</aside>
	<?php
}
