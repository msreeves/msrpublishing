<?php
/**
 * Mobile fullscreen menu toggle.
 *
 * @package msrpublishing
 */
?>
<button
	type="button"
	class="site-header__menu-toggle"
	aria-controls="site-header-mobile-nav"
	aria-expanded="false"
	aria-label="<?php esc_attr_e( 'Open menu', 'msrpublishing' ); ?>"
>
	<span class="site-header__menu-toggle__label" data-close-label="<?php esc_attr_e( 'Close', 'msrpublishing' ); ?>">
		<span class="site-header__menu-toggle__label-text"><?php esc_html_e( 'Menu', 'msrpublishing' ); ?></span>
	</span>
	<span class="site-header__menu-toggle__bars" aria-hidden="true">
		<span class="site-header__menu-toggle__bar site-header__menu-toggle__bar--top"></span>
		<span class="site-header__menu-toggle__bar site-header__menu-toggle__bar--mid"></span>
		<span class="site-header__menu-toggle__bar site-header__menu-toggle__bar--bot"></span>
	</span>
</button>
