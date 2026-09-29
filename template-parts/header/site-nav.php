<?php
/**
 * Site header — brand row, desktop nav + search row, mobile overlay.
 *
 * @package msrpublishing
 */

$nav_links     = msr_publishing_get_primary_nav_links();
$subscribe_url = msr_publishing_subscribe_url();
$explore_url   = get_post_type_archive_link( 'resource' );
if ( ! $explore_url && function_exists( 'msr_publishing_topics_url' ) ) {
	$explore_url = msr_publishing_topics_url();
}
?>
<header id="masthead" class="site-header site-header--sticky">
	<nav class="navbar navbar-light site-header__navbar p-0" aria-label="<?php esc_attr_e( 'Site', 'msrpublishing' ); ?>">
		<div class="site-header__shell">
			<div class="site-header__row site-header__row--brand">
			<?php get_template_part( 'template-parts/header/site-brand' ); ?>
			<?php
			$insights_shortcut = function_exists( 'msr_publishing_insights_url' ) ? msr_publishing_insights_url() : '';
			if ( $explore_url || $insights_shortcut ) :
				?>
				<nav class="site-header__shortcuts d-none d-md-flex d-lg-none" aria-label="<?php esc_attr_e( 'Quick links', 'msrpublishing' ); ?>">
					<?php if ( $explore_url ) : ?>
						<a class="site-header__shortcut" href="<?php echo esc_url( $explore_url ); ?>"><?php esc_html_e( 'Resources', 'msrpublishing' ); ?></a>
					<?php endif; ?>
					<?php if ( $insights_shortcut ) : ?>
						<a class="site-header__shortcut" href="<?php echo esc_url( $insights_shortcut ); ?>"><?php esc_html_e( 'Insights', 'msrpublishing' ); ?></a>
					<?php endif; ?>
				</nav>
			<?php endif; ?>
			<div class="site-header__utilities">
				<?php if ( $explore_url ) : ?>
					<a class="btn btn-sm btn-outline-primary site-header__explore d-none d-xl-inline-flex" href="<?php echo esc_url( $explore_url ); ?>">
						<?php esc_html_e( 'Explore resources', 'msrpublishing' ); ?>
					</a>
				<?php endif; ?>
				<?php get_template_part( 'template-parts/header/site-subscribe-cta' ); ?>
				<div class="site-header__tools d-lg-none">
					<?php if ( $explore_url ) : ?>
						<a
							class="site-header__explore-icon btn btn-link p-2"
							href="<?php echo esc_url( $explore_url ); ?>"
							aria-label="<?php esc_attr_e( 'Explore resources', 'msrpublishing' ); ?>"
						>
							<i class="fa-solid fa-compass" aria-hidden="true"></i>
						</a>
					<?php endif; ?>
					<?php get_template_part( 'template-parts/header/site-header-search-toggle' ); ?>
					<?php get_template_part( 'template-parts/header/site-header-menu-toggle' ); ?>
				</div>
			</div>
		</div>
			<div class="site-header__row site-header__row--desktop d-none d-lg-flex">
			<?php msr_publishing_render_desktop_primary_nav(); ?>
			<div id="site-header-search-region" class="site-header__search-slot" aria-label="<?php esc_attr_e( 'Search the site', 'msrpublishing' ); ?>">
				<?php
				get_template_part(
					'template-parts/forms/site-search',
					null,
					array(
						'input_id' => 'msr-header-search-input-desktop',
						'layout'   => 'header',
					)
				);
				?>
			</div>
		</div>
		</div>
	</nav>
	<div
		id="msr-header-search-panel"
		class="site-header__search-panel d-lg-none"
		role="region"
		aria-label="<?php esc_attr_e( 'Search', 'msrpublishing' ); ?>"
		hidden
	>
		<div class="site-header__shell py-3">
			<div class="site-header__search site-header__search--panel">
				<?php
				get_template_part(
					'template-parts/forms/site-search',
					null,
					array(
						'input_id' => 'msr-header-search-input',
						'layout'   => 'header',
					)
				);
				?>
			</div>
			<p class="site-header__search-hint small text-muted mb-0 mt-2">
				<?php esc_html_e( 'Search insights and resources across Atlas Briefing.', 'msrpublishing' ); ?>
			</p>
		</div>
	</div>
	<div id="site-header-mobile-nav" class="site-header__mobile-nav" aria-hidden="true" inert>
		<div class="site-header__mobile-nav__inner">
			<div class="site-header__mobile-nav__search d-lg-none">
				<?php
				get_template_part(
					'template-parts/forms/site-search',
					null,
					array(
						'input_id' => 'msr-header-search-input-menu',
						'layout'   => 'header',
					)
				);
				?>
			</div>
			<nav aria-label="<?php esc_attr_e( 'Primary', 'msrpublishing' ); ?>">
				<ul class="site-header__mobile-nav__list">
					<?php foreach ( $nav_links as $link ) : ?>
						<?php
						if ( empty( $link['url'] ) ) {
							continue;
						}
						$title          = (string) $link['title'];
						$url            = (string) $link['url'];
						$mega           = msr_publishing_get_nav_mega_type( $title, $url );
						$is_current     = msr_publishing_nav_link_is_current( $url );
						$section_active = $mega !== '' && msr_publishing_nav_section_is_active( $mega );
						$item_class     = 'site-header__mobile-nav__item';
						$link_class     = 'site-header__mobile-nav__link';

						if ( $mega !== '' ) {
							$item_class .= ' site-header__mobile-nav__item--group';
						}
						if ( $is_current || $section_active ) {
							$item_class .= ' is-current';
							$link_class  .= ' is-current';
						}
						?>
						<li class="<?php echo esc_attr( $item_class ); ?>">
							<a
								class="<?php echo esc_attr( $link_class ); ?>"
								href="<?php echo esc_url( $url ); ?>"
								<?php echo ( $is_current || $section_active ) ? ' aria-current="page"' : ''; ?>
							>
								<?php echo esc_html( $title ); ?>
							</a>
							<?php
							if ( $mega !== '' ) {
								msr_publishing_render_fullscreen_nav_sublist( $mega );
							}
							?>
						</li>
					<?php endforeach; ?>
				</ul>
			</nav>
		</div>
		<div class="site-header__mobile-nav__footer container">
			<?php if ( $subscribe_url ) : ?>
				<?php
				$subscribe_current = msr_publishing_nav_link_is_current( $subscribe_url );
				$cta_class         = 'site-header__mobile-nav__cta';
				if ( $subscribe_current ) {
					$cta_class .= ' is-current';
				}
				?>
				<a
					class="<?php echo esc_attr( $cta_class ); ?>"
					href="<?php echo esc_url( $subscribe_url ); ?>"
					<?php echo $subscribe_current ? ' aria-current="page"' : ''; ?>
				>
					<?php esc_html_e( 'Subscribe to Atlas Briefing', 'msrpublishing' ); ?>
				</a>
			<?php endif; ?>
		</div>
	</div>
</header>
