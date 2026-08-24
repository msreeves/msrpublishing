<?php
/**
 * MSR Events programme links (hub, awards, seminars) for Atlas Briefing resources.
 *
 * Defaults are production URLs (safe when the theme ships). Local requests remap to
 * MAMP; non-local requests scrub any leftover MAMP hosts. See
 * config/programme-urls.json + docs/plans/programmes/msr-estate-url-lock-playbook.md.
 *
 * @package msrsandbox
 */

/**
 * Local → prod programme URL pairs (longest local first).
 *
 * @return array<int, array{local: string, prod: string}>
 */
function msr_publishing_get_programme_url_pairs() {
	static $pairs = null;
	if ( null !== $pairs ) {
		return $pairs;
	}

	$pairs = array(
		array(
			'local' => 'http://127.0.0.1:8888/sites/wp/events/msrseminars',
			'prod'  => 'https://www.msreeves.co.uk/events/msrseminars',
		),
		array(
			'local' => 'http://127.0.0.1:8888/sites/wp/events/msrawards',
			'prod'  => 'https://www.msreeves.co.uk/events/msrawards',
		),
		array(
			'local' => 'http://127.0.0.1:8888/sites/wp/events',
			'prod'  => 'https://www.msreeves.co.uk/events',
		),
		array(
			'local' => 'http://msrevents.local:8888/msrseminars',
			'prod'  => 'https://www.msreeves.co.uk/events/msrseminars',
		),
		array(
			'local' => 'http://msrevents.local:8888/msrawards',
			'prod'  => 'https://www.msreeves.co.uk/events/msrawards',
		),
		array(
			'local' => 'http://msrevents.local:8888',
			'prod'  => 'https://www.msreeves.co.uk/events',
		),
	);

	/**
	 * Filter programme URL local↔prod pairs (longest local first).
	 *
	 * @param array<int, array{local: string, prod: string}> $pairs Pairs.
	 */
	$pairs = apply_filters( 'msr_publishing_programme_url_pairs', $pairs );

	return $pairs;
}

/**
 * Whether the current request looks like local MAMP / .local.
 *
 * @return bool
 */
function msr_publishing_is_local_request() {
	if ( getenv( 'MSR_PUBLISHING_FORCE_LOCAL' ) === '1' || getenv( 'MSR_PUBLISHING_FORCE_LOCAL' ) === 'true' ) {
		return true;
	}
	if ( getenv( 'MSR_PUBLISHING_FORCE_PROD' ) === '1' || getenv( 'MSR_PUBLISHING_FORCE_PROD' ) === 'true' ) {
		return false;
	}

	$host = isset( $_SERVER['HTTP_HOST'] ) ? (string) $_SERVER['HTTP_HOST'] : '';
	if ( $host === '' ) {
		return (bool) apply_filters( 'msr_publishing_is_local_request', false );
	}

	$local = (
		strpos( $host, '127.0.0.1' ) !== false
		|| strpos( $host, 'localhost' ) !== false
		|| strpos( $host, '.local' ) !== false
		|| strpos( $host, ':8888' ) !== false
	);

	/**
	 * Filter whether programme URLs should use the local MAMP map.
	 *
	 * @param bool $local Detected local host.
	 */
	return (bool) apply_filters( 'msr_publishing_is_local_request', $local );
}

/**
 * Default programme registry (production URLs — ship-safe).
 *
 * @return array<string, array{label: string, url: string, description: string, cta: string}>
 */
function msr_publishing_get_programme_registry_defaults() {
	return array(
		'hub'      => array(
			'label'       => __( 'MSR Events hub', 'msrsandbox' ),
			'url'         => 'https://www.msreeves.co.uk/events/',
			'description' => __( 'Programmes, events, and editorial from the central hub.', 'msrsandbox' ),
			'cta'         => __( 'Visit the events hub', 'msrsandbox' ),
			'icon'        => 'fa-solid fa-calendar-days',
			'meta'        => __( 'Live programmes', 'msrsandbox' ),
		),
		'awards'   => array(
			'label'       => __( 'MSR Awards', 'msrsandbox' ),
			'url'         => 'https://www.msreeves.co.uk/events/msrawards/',
			'description' => __( 'Awards programme, nominees, and industry recognition.', 'msrsandbox' ),
			'cta'         => __( 'Explore MSR Awards', 'msrsandbox' ),
			'icon'        => 'fa-solid fa-trophy',
			'meta'        => __( 'Awards season', 'msrsandbox' ),
		),
		'seminars' => array(
			'label'       => __( 'MSR Seminars', 'msrsandbox' ),
			'url'         => 'https://www.msreeves.co.uk/events/msrseminars/',
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
		'http://msrevents.local:8888/'             => 'http://127.0.0.1:8888/sites/wp/events/',
		'http://msrevents.local:8888/msrawards/'   => 'http://127.0.0.1:8888/sites/wp/events/msrawards/',
		'http://msrevents.local:8888/msrseminars/' => 'http://127.0.0.1:8888/sites/wp/events/msrseminars/',
	);
}

/**
 * Replace URL prefix (slash-tolerant).
 *
 * @param string $url  Subject URL.
 * @param string $from Prefix to replace.
 * @param string $to   Replacement prefix.
 * @return string|null New URL or null if no match.
 */
function msr_publishing_replace_url_prefix( $url, $from, $to ) {
	$from = rtrim( (string) $from, '/' );
	$to   = rtrim( (string) $to, '/' );

	if ( $url === $from || $url === $from . '/' ) {
		return $to . '/';
	}
	if ( strpos( $url, $from . '/' ) === 0 ) {
		return $to . substr( $url, strlen( $from ) );
	}
	if ( strpos( $url, $from . '?' ) === 0 ) {
		return $to . substr( $url, strlen( $from ) );
	}

	return null;
}

/**
 * Resolve programme URL for the current environment.
 *
 * - Local: prod defaults → MAMP; msrevents.local → 127 path when needed.
 * - Live: any MAMP host → prod (never emit 127.0.0.1 / .local / :8888).
 *
 * @param string $url Programme URL.
 * @return string
 */
function msr_publishing_resolve_programme_url( $url ) {
	$url = trim( (string) $url );
	if ( $url === '' ) {
		return '';
	}

	$pairs = msr_publishing_get_programme_url_pairs();

	if ( msr_publishing_is_local_request() ) {
		foreach ( $pairs as $pair ) {
			$replaced = msr_publishing_replace_url_prefix( $url, $pair['prod'], $pair['local'] );
			if ( null !== $replaced ) {
				$url = $replaced;
				break;
			}
		}

		if ( msr_publishing_use_programme_ip_fallback() ) {
			foreach ( msr_publishing_get_programme_ip_fallback_map() as $from => $to ) {
				$replaced = msr_publishing_replace_url_prefix( $url, $from, $to );
				if ( null !== $replaced ) {
					return $replaced;
				}
			}
		}

		return $url;
	}

	foreach ( $pairs as $pair ) {
		$replaced = msr_publishing_replace_url_prefix( $url, $pair['local'], $pair['prod'] );
		if ( null !== $replaced ) {
			return $replaced;
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
	$defaults   = msr_publishing_get_programme_registry_defaults();
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
		$resolved = msr_publishing_resolve_programme_url( $programme['url'] );
		// Empty after resolve → omit URL (no local absolute fallback on live).
		$defaults[ $slug ]['url'] = $resolved;
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
		if ( empty( $programme['url'] ) ) {
			continue;
		}
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
	if ( empty( $data['url'] ) ) {
		return null;
	}
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
