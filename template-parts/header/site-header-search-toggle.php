<?php
/**
 * Mobile search open control (pairs with #msr-header-search-panel).
 *
 * @package msrpublishing
 */
?>
<button
	type="button"
	class="site-header__search-toggle"
	aria-expanded="false"
	aria-controls="msr-header-search-panel"
	data-search-close-label="<?php esc_attr_e( 'Close search', 'msrpublishing' ); ?>"
	aria-label="<?php esc_attr_e( 'Open search', 'msrpublishing' ); ?>"
>
	<i class="fa-solid fa-magnifying-glass site-header__search-toggle__icon" aria-hidden="true"></i>
	<span class="site-header__search-toggle__label"><?php esc_html_e( 'Search', 'msrpublishing' ); ?></span>
</button>
