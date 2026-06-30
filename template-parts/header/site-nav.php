<?php
/**
 * Site header bar + fullscreen navigation (< lg) + desktop primary nav (lg+).
 *
 * @package msrsandbox
 */

$nav_links     = msr_publishing_get_primary_nav_links();
$subscribe_url = msr_publishing_subscribe_url();
?>
<header id="masthead" class="site-header site-header--sticky publishing-site-header">
	<nav class="navbar navbar-light p-3 p-lg-4" aria-label="<?php esc_attr_e( 'Site', 'msrsandbox' ); ?>">
		<div class="container-fluid publishing-site-header__bar">
			<?php get_template_part( 'template-parts/header/site-brand' ); ?>
			<?php msr_publishing_render_desktop_primary_nav(); ?>
			<?php get_template_part( 'template-parts/header/site-header-actions' ); ?>
			<?php get_template_part( 'template-parts/header/site-subscribe-cta' ); ?>
		</div>
		<div id="msr-header-search-panel" class="site-header__search-panel d-lg-none" hidden>
			<div class="container-fluid pt-0 pb-3">
				<form role="search" method="get" class="site-header__search-mobile" action="<?php echo esc_url( home_url( '/' ) ); ?>">
					<label class="visually-hidden" for="msr-header-search-mobile"><?php esc_html_e( 'Search Atlas Briefing', 'msrsandbox' ); ?></label>
					<div class="input-group">
						<input type="search" class="form-control" id="msr-header-search-mobile" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search Atlas Briefing…', 'msrsandbox' ); ?>" />
						<button class="btn btn-outline-secondary" type="submit" aria-label="<?php esc_attr_e( 'Submit search', 'msrsandbox' ); ?>">
							<i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
						</button>
					</div>
				</form>
			</div>
		</div>
	</nav>
	<div id="publishingFullscreenNav" class="publishing-fullscreen-nav" aria-hidden="true" inert>
		<div class="publishing-fullscreen-nav__inner">
			<nav aria-label="<?php esc_attr_e( 'Primary', 'msrsandbox' ); ?>">
				<ul class="publishing-fullscreen-nav__list">
					<?php foreach ( $nav_links as $link ) : ?>
						<?php
						if ( empty( $link['url'] ) ) {
							continue;
						}
						$title           = (string) $link['title'];
						$url             = (string) $link['url'];
						$mega            = msr_publishing_get_nav_mega_type( $title, $url );
						$is_current      = msr_publishing_nav_link_is_current( $url );
						$section_active  = $mega !== '' && msr_publishing_nav_section_is_active( $mega );
						$item_class      = 'publishing-fullscreen-nav__item';
						$link_class      = 'publishing-fullscreen-nav__link';

						if ( $mega !== '' ) {
							$item_class .= ' publishing-fullscreen-nav__item--group';
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
		<div class="publishing-fullscreen-nav__footer container">
			<?php if ( $subscribe_url ) : ?>
				<?php
				$subscribe_current = msr_publishing_nav_link_is_current( $subscribe_url );
				$cta_class         = 'publishing-fullscreen-nav__cta';
				if ( $subscribe_current ) {
					$cta_class .= ' is-current';
				}
				?>
				<a
					class="<?php echo esc_attr( $cta_class ); ?>"
					href="<?php echo esc_url( $subscribe_url ); ?>"
					<?php echo $subscribe_current ? ' aria-current="page"' : ''; ?>
				>
					<?php esc_html_e( 'Subscribe to Atlas Briefing', 'msrsandbox' ); ?>
				</a>
			<?php endif; ?>
		</div>
	</div>
</header>
