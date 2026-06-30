<?php
/**
 * Named expert profiles for author archives (P42).
 *
 * @package msrsandbox
 */

/**
 * Canonical expert registry (slug => profile).
 *
 * @return array<string, array{slug: string, name: string, title: string, bio: string, login: string}>
 */
function msr_publishing_get_expert_registry() {
	return array(
		'jordan-ellis' => array(
			'slug'  => 'jordan-ellis',
			'name'  => 'Jordan Ellis',
			'title' => 'Workforce analyst (demonstration)',
			'bio'   => 'Jordan Ellis covers workforce planning, hiring signals, and leadership review rituals for Atlas Briefing demonstration assets.',
			'login' => 'jordan-ellis',
		),
		'morgan-reid'  => array(
			'slug'  => 'morgan-reid',
			'name'  => 'Morgan Reid',
			'title' => 'Managing editor (demonstration)',
			'bio'   => 'Morgan Reid shapes leadership briefing formats and editorial standards across commentary and resource singles.',
			'login' => 'morgan-reid',
		),
		'priya-nair'   => array(
			'slug'  => 'priya-nair',
			'name'  => 'Priya Nair',
			'title' => 'Workforce editor (demonstration)',
			'bio'   => 'Priya Nair edits workforce commentary and connects editorial posts to topic hubs and flagship resources.',
			'login' => 'priya-nair',
		),
	);
}

/**
 * @param string $slug Expert nicename / login slug.
 * @return array<string, mixed>|null
 */
function msr_publishing_get_expert_by_slug( $slug ) {
	$slug     = sanitize_title( (string) $slug );
	$registry = msr_publishing_get_expert_registry();

	return $registry[ $slug ] ?? null;
}

/**
 * @param string $name Display name.
 * @return array<string, mixed>|null
 */
function msr_publishing_get_expert_by_name( $name ) {
	$name = trim( (string) $name );
	if ( $name === '' ) {
		return null;
	}

	foreach ( msr_publishing_get_expert_registry() as $expert ) {
		if ( strcasecmp( $expert['name'], $name ) === 0 ) {
			return $expert;
		}
	}

	return null;
}

/**
 * Profile archive URL for an expert slug or profile array.
 *
 * @param string|array<string, mixed> $expert_or_slug Slug or registry row.
 * @return string
 */
function msr_publishing_get_expert_profile_url( $expert_or_slug ) {
	$expert = is_array( $expert_or_slug )
		? $expert_or_slug
		: msr_publishing_get_expert_by_slug( (string) $expert_or_slug );

	if ( ! $expert ) {
		return '';
	}

	$user = get_user_by( 'login', $expert['login'] );
	if ( $user instanceof WP_User ) {
		return get_author_posts_url( (int) $user->ID );
	}

	return home_url( user_trailingslashit( 'author/' . $expert['slug'] ) );
}

/**
 * Profile URL for a guest author display name when mapped to the registry.
 *
 * @param string $name Guest author name.
 * @return string
 */
function msr_publishing_get_expert_profile_url_for_name( $name ) {
	$expert = msr_publishing_get_expert_by_name( $name );

	return $expert ? msr_publishing_get_expert_profile_url( $expert ) : '';
}

/**
 * Resolve the active expert from a WP author archive query.
 *
 * @param WP_User|null $author Queried author.
 * @return array<string, mixed>|null
 */
function msr_publishing_get_expert_from_author_object( $author ) {
	if ( ! $author instanceof WP_User ) {
		return null;
	}

	$expert = msr_publishing_get_expert_by_slug( $author->user_nicename );
	if ( $expert ) {
		return $expert;
	}

	return msr_publishing_get_expert_by_slug( $author->user_login );
}

/**
 * Resources attributed to an expert via guest_author_name.
 *
 * @param array<string, mixed> $expert Registry row.
 * @param int                  $limit  Max posts.
 * @return WP_Query
 */
function msr_publishing_get_expert_resources_query( $expert, $limit = 12 ) {
	$name = trim( (string) ( $expert['name'] ?? '' ) );

	return new WP_Query(
		array(
			'post_type'           => 'resource',
			'post_status'         => 'publish',
			'posts_per_page'      => max( 1, (int) $limit ),
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'orderby'             => 'date',
			'order'               => 'DESC',
			'meta_query'          => array(
				array(
					'key'     => 'guest_author_name',
					'value'   => $name,
					'compare' => '=',
				),
			),
		)
	);
}

/**
 * @param WP_User $author Author user.
 * @return void
 */
function msr_publishing_render_author_profile_header( $author ) {
	$expert = msr_publishing_get_expert_from_author_object( $author );
	$name   = $expert['name'] ?? $author->display_name;
	$title  = $expert['title'] ?? get_user_meta( $author->ID, '_msr_expert_title', true );
	$bio    = $expert['bio'] ?? (string) get_the_author_meta( 'description', $author->ID );
	?>
	<header class="publishing-author-archive__header mb-5">
		<p class="publishing-author-archive__eyebrow text-uppercase small fw-semibold text-muted mb-2"><?php esc_html_e( 'Atlas Briefing expert', 'msrsandbox' ); ?></p>
		<h1 class="publishing-author-archive__name h2 mb-2"><?php echo esc_html( $name ); ?></h1>
		<?php if ( $title !== '' ) : ?>
			<p class="publishing-author-archive__title lead text-muted mb-3"><?php echo esc_html( $title ); ?></p>
		<?php endif; ?>
		<?php if ( $bio !== '' ) : ?>
			<p class="publishing-author-archive__bio text-muted mb-0"><?php echo esc_html( $bio ); ?></p>
		<?php endif; ?>
	</header>
	<?php
}

/**
 * Expert cards for the About page.
 *
 * @return void
 */
function msr_publishing_render_about_experts_grid() {
	$experts = msr_publishing_get_expert_registry();
	?>
	<section class="publishing-about-experts mb-5" aria-labelledby="about-experts-heading">
		<h2 id="about-experts-heading" class="h3 mb-2"><?php esc_html_e( 'Meet the editorial team', 'msrsandbox' ); ?></h2>
		<p class="text-muted mb-4"><?php esc_html_e( 'Named experts behind flagship resources and commentary — demonstration profiles for portfolio review.', 'msrsandbox' ); ?></p>
		<div class="row g-3 publishing-about-experts__grid">
			<?php foreach ( $experts as $expert ) : ?>
				<?php $profile_url = msr_publishing_get_expert_profile_url( $expert ); ?>
				<div class="col-md-4">
					<article class="publishing-expert-card panel h-100">
						<h3 class="h5 publishing-expert-card__name mb-1">
							<?php if ( $profile_url !== '' ) : ?>
								<a class="publishing-expert-card__link" href="<?php echo esc_url( $profile_url ); ?>"><?php echo esc_html( $expert['name'] ); ?></a>
							<?php else : ?>
								<?php echo esc_html( $expert['name'] ); ?>
							<?php endif; ?>
						</h3>
						<p class="publishing-expert-card__title small text-muted mb-2"><?php echo esc_html( $expert['title'] ); ?></p>
						<p class="publishing-expert-card__bio small mb-3"><?php echo esc_html( $expert['bio'] ); ?></p>
						<?php if ( $profile_url !== '' ) : ?>
							<a class="btn btn-sm btn-outline-primary publishing-expert-card__cta" href="<?php echo esc_url( $profile_url ); ?>">
								<?php esc_html_e( 'View profile', 'msrsandbox' ); ?>
							</a>
						<?php endif; ?>
					</article>
				</div>
			<?php endforeach; ?>
		</div>
	</section>
	<?php
}
