<?php
/**
 * Header toolbar: search + menu toggles (no anchor tags — programme-health P14-03).
 *
 * @package msrsandbox
 */
?>
<div class="site-header__actions ms-auto d-flex align-items-center gap-2 gap-lg-3">
	<form role="search" method="get" class="site-header__search d-none d-lg-flex" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<label class="visually-hidden" for="msr-header-search"><?php esc_html_e( 'Search', 'msrsandbox' ); ?></label>
		<div class="input-group input-group-sm">
			<input type="search" class="form-control" id="msr-header-search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search…', 'msrsandbox' ); ?>" />
			<button class="btn btn-outline-secondary" type="submit" aria-label="<?php esc_attr_e( 'Submit search', 'msrsandbox' ); ?>">
				<i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
			</button>
		</div>
	</form>
	<button
		type="button"
		class="site-header__search-toggle d-lg-none"
		aria-expanded="false"
		aria-controls="msr-header-search-panel"
		aria-label="<?php esc_attr_e( 'Open search', 'msrsandbox' ); ?>"
		data-search-close-label="<?php esc_attr_e( 'Close search', 'msrsandbox' ); ?>"
	>
		<i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
	</button>
	<button
		type="button"
		class="publishing-menu-toggle d-lg-none"
		aria-controls="publishingFullscreenNav"
		aria-expanded="false"
		aria-label="<?php esc_attr_e( 'Open menu', 'msrsandbox' ); ?>"
	>
		<span class="publishing-menu-toggle__label" data-close-label="<?php esc_attr_e( 'Close', 'msrsandbox' ); ?>">
			<span class="publishing-menu-toggle__label-text"><?php esc_html_e( 'Menu', 'msrsandbox' ); ?></span>
		</span>
		<span class="publishing-menu-toggle__bars" aria-hidden="true">
			<span class="publishing-menu-toggle__bar publishing-menu-toggle__bar--top"></span>
			<span class="publishing-menu-toggle__bar publishing-menu-toggle__bar--mid"></span>
			<span class="publishing-menu-toggle__bar publishing-menu-toggle__bar--bot"></span>
		</span>
	</button>
</div>
