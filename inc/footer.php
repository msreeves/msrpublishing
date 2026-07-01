<?php
/**
 * Publishing site footer — IA columns, topics, brand (not menu-only).
 *
 * @package msrsandbox
 */

/**
 * Footer Explore column — WP menu when assigned, else link list.
 *
 * @return void
 */
function msr_publishing_render_footer_explore_nav() {
	$search = home_url( '/?s=' );
	?>
	<h2 class="publishing-site-footer__heading h6 text-uppercase"><?php esc_html_e( 'Explore', 'msrsandbox' ); ?></h2>
	<ul class="publishing-site-footer__links list-unstyled mb-0">
		<?php if ( has_nav_menu( 'footer' ) ) : ?>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'footer',
					'container'      => false,
					'items_wrap'     => '%3$s',
					'depth'          => 1,
					'fallback_cb'    => false,
				)
			);
			?>
		<?php else : ?>
			<?php foreach ( msr_publishing_get_footer_explore_links() as $link ) : ?>
				<?php if ( empty( $link['url'] ) ) { continue; } ?>
				<li>
					<a href="<?php echo esc_url( $link['url'] ); ?>"><?php echo esc_html( $link['title'] ); ?></a>
				</li>
			<?php endforeach; ?>
		<?php endif; ?>
		<li>
			<a href="<?php echo esc_url( $search ); ?>"><?php esc_html_e( 'Search', 'msrsandbox' ); ?></a>
		</li>
	</ul>
	<?php
}

/**
 * Format terms for footer column.
 *
 * @return WP_Term[]
 */
function msr_publishing_get_footer_format_terms() {
	return msr_publishing_get_resource_type_nav_terms( array( 'hide_empty' => true ) );
}

/**
 * Footer Formats column.
 *
 * @return void
 */
function msr_publishing_render_footer_formats_nav() {
	$formats = msr_publishing_get_footer_format_terms();
	if ( ! $formats ) {
		return;
	}
	?>
	<h2 class="publishing-site-footer__heading h6 text-uppercase"><?php esc_html_e( 'Formats', 'msrsandbox' ); ?></h2>
	<ul class="publishing-site-footer__links list-unstyled mb-0">
		<?php foreach ( $formats as $format ) : ?>
			<li>
				<a href="<?php echo esc_url( get_term_link( $format ) ); ?>"><?php echo esc_html( $format->name ); ?></a>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php
}

/**
 * Topic terms for footer column.
 *
 * @return WP_Term[]
 */
function msr_publishing_get_footer_topic_terms() {
	$topics = get_terms(
		array(
			'taxonomy'   => 'topic',
			'hide_empty' => true,
			'number'     => 6,
		)
	);

	return ( $topics && ! is_wp_error( $topics ) ) ? $topics : array();
}

/**
 * Full publishing footer markup.
 *
 * @return void
 */
function msr_publishing_render_site_footer() {
	$topics = msr_publishing_get_footer_topic_terms();
	$brand   = msr_publishing_brand_name();
	$tagline = msr_publishing_brand_tagline();
	$resources = get_post_type_archive_link( 'resource' );
	?>
	<div class="publishing-site-footer__main">
		<div class="container">
			<div class="row g-4 publishing-site-footer__grid">
				<div class="col-md-6 col-lg-4 publishing-site-footer__brand">
					<p class="publishing-site-footer__name h5 mb-2"><?php echo esc_html( $brand ); ?></p>
					<p class="publishing-site-footer__tagline small mb-3"><?php echo esc_html( $tagline ); ?></p>
					<?php if ( $resources ) : ?>
						<a class="btn btn-sm btn-outline-light publishing-site-footer__cta" href="<?php echo esc_url( $resources ); ?>">
							<?php esc_html_e( 'Browse resources', 'msrsandbox' ); ?>
						</a>
					<?php endif; ?>
				</div>
				<div class="col-6 col-md-3 col-lg-2 publishing-site-footer__col">
					<?php msr_publishing_render_footer_explore_nav(); ?>
				</div>
				<div class="col-6 col-md-3 col-lg-2 publishing-site-footer__col">
					<?php msr_publishing_render_footer_formats_nav(); ?>
				</div>
				<?php if ( $topics ) : ?>
					<div class="col-6 col-md-3 col-lg-2 publishing-site-footer__col">
						<h2 class="publishing-site-footer__heading h6 text-uppercase"><?php esc_html_e( 'Topics', 'msrsandbox' ); ?></h2>
						<ul class="publishing-site-footer__links list-unstyled mb-0">
							<?php foreach ( $topics as $topic ) : ?>
								<li>
									<a href="<?php echo esc_url( get_term_link( $topic ) ); ?>"><?php echo esc_html( $topic->name ); ?></a>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>
				<div class="col-md-12 col-lg-4 publishing-site-footer__col publishing-site-footer__connect">
					<?php if ( is_front_page() ) : ?>
						<h2 class="publishing-site-footer__heading h6 text-uppercase"><?php esc_html_e( 'Explore more', 'msrsandbox' ); ?></h2>
						<p class="small mb-2"><?php esc_html_e( 'Browse the resource library or open the subscribe page for the full briefing signup flow.', 'msrsandbox' ); ?></p>
						<a class="publishing-site-footer__text-link" href="<?php echo esc_url( msr_publishing_subscribe_url() ); ?>">
							<?php esc_html_e( 'Subscribe page', 'msrsandbox' ); ?>
						</a>
					<?php else : ?>
						<h2 class="publishing-site-footer__heading h6 text-uppercase"><?php esc_html_e( 'Stay briefed', 'msrsandbox' ); ?></h2>
						<p class="small mb-2"><?php esc_html_e( 'Portfolio demonstration signup — no ESP connected.', 'msrsandbox' ); ?></p>
						<a class="btn btn-sm publishing-site-footer__subscribe" href="<?php echo esc_url( msr_publishing_subscribe_url() ); ?>">
							<?php esc_html_e( 'Subscribe', 'msrsandbox' ); ?>
						</a>
					<?php endif; ?>
					<p class="publishing-site-footer__about small mt-3 mb-0">
						<a href="<?php echo esc_url( msr_publishing_about_url() ); ?>"><?php esc_html_e( 'About & methodology', 'msrsandbox' ); ?></a>
						<?php
						$privacy_url = msr_publishing_get_page_url( 'privacy', '/privacy/' );
						if ( $privacy_url ) :
							?>
							<span aria-hidden="true"> · </span>
							<a href="<?php echo esc_url( $privacy_url ); ?>"><?php esc_html_e( 'Privacy (demo)', 'msrsandbox' ); ?></a>
						<?php endif; ?>
					</p>
				</div>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Social icons row (optional WP menu).
 *
 * @return void
 */
function msr_publishing_render_footer_social() {
	if ( ! has_nav_menu( 'social' ) ) {
		return;
	}

	$menu_locations = get_nav_menu_locations();
	if ( empty( $menu_locations['social'] ) ) {
		return;
	}

	$menu = wp_get_nav_menu_object( (int) $menu_locations['social'] );
	if ( ! $menu ) {
		return;
	}

	$menu_items = wp_get_nav_menu_items( $menu->term_id );
	if ( ! $menu_items ) {
		return;
	}

	$preferred = array( 'linkedin', 'youtube', 'x-twitter' );
	$by_title  = array();
	foreach ( $menu_items as $item ) {
		$key = sanitize_html_class( $item->title );
		$by_title[ $key ] = $item;
	}

	$filtered = array();
	foreach ( $preferred as $icon ) {
		if ( isset( $by_title[ $icon ] ) ) {
			$filtered[] = $by_title[ $icon ];
		}
	}
	if ( ! $filtered ) {
		$filtered = array_slice( $menu_items, 0, 3 );
	}
	$menu_items = $filtered;
	?>
	<div class="publishing-site-footer__social">
		<div class="container">
			<h2 class="visually-hidden"><?php echo esc_html( $menu->name ); ?></h2>
			<div class="d-flex flex-wrap gap-3 justify-content-center">
				<?php foreach ( $menu_items as $item ) : ?>
					<?php
					$icon = sanitize_html_class( $item->title );
					printf(
						'<a class="publishing-site-footer__social-link" href="%s" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-%s fa-lg" aria-hidden="true"></i><span class="visually-hidden">%s</span></a>',
						esc_url( $item->url ),
						esc_attr( $icon ),
						esc_html( $item->title )
					);
					?>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
	<?php
}
