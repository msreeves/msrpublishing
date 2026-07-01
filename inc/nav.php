<?php
/**
 * Primary navigation — WP menu (menu-1) first; PHP fallback only when unassigned.
 *
 * @package msrsandbox
 */

/**
 * Commentary hub URL (`/insights/`).
 *
 * @return string
 */
function msr_publishing_insights_url() {
	return msr_publishing_get_page_url( 'insights', '/insights/' );
}

/**
 * About / methodology page URL (`/about/`).
 *
 * @return string
 */
function msr_publishing_about_url() {
	return msr_publishing_get_page_url( 'about', '/about/' );
}

/**
 * Primary topic hub URL (admin: topic taxonomy).
 *
 * @return string
 */
function msr_publishing_topics_url() {
	return msr_publishing_get_topic_hub_url();
}

/**
 * Primary nav links — from assigned menu-1, else install fallback.
 *
 * @return array<int, array{title: string, url: string}>
 */
function msr_publishing_get_primary_nav_links() {
	$from_menu = msr_publishing_get_nav_links_from_location( 'menu-1' );
	if ( ! empty( $from_menu ) ) {
 return $from_menu;
	}

	return msr_publishing_get_primary_nav_fallback_links();
}

/**
 * Megamenu panel type for a primary nav item (Resources / Topics only).
 *
 * @param string $title Link label.
 * @param string $url Link URL.
 * @return string resources|topics|''
 */
function msr_publishing_get_nav_mega_type( $title, $url ) {
	$label = strtolower( trim( (string) $title ) );
	if ( 'resources' === $label ) {
 return 'resources';
	}
	if ( 'topics' === $label ) {
 return 'topics';
	}

	$path = (string) wp_parse_url( (string) $url, PHP_URL_PATH );
	if ( $path !== '' && str_contains( $path, '/resources' ) ) {
 return 'resources';
	}
	if ( $path !== '' && ( str_contains( $path, '/topics' ) || str_contains( $path, 'briefing-topic' ) ) ) {
 return 'topics';
	}

	return '';
}

/**
 * Whether a nav URL matches the current request (active trail).
 *
 * @param string $url Nav item URL.
 * @return bool
 */
function msr_publishing_nav_link_is_current( $url ) {
	$url = (string) $url;
	if ( $url === '' ) {
 return false;
	}

	if ( is_front_page() && trailingslashit( $url ) === trailingslashit( home_url( '/' ) ) ) {
 return true;
	}

	$target = untrailingslashit( (string) wp_parse_url( $url, PHP_URL_PATH ) );
	$current = untrailingslashit( (string) wp_parse_url( msr_publishing_get_current_request_url(), PHP_URL_PATH ) );

	if ( $target === '' || $current === '' ) {
 return false;
	}

	if ( $target === $current ) {
 return true;
	}

	if ( is_post_type_archive( 'resource' ) && str_contains( $target, '/resources' ) ) {
 return true;
	}

	if ( ( is_tax( 'topic' ) || is_page( 'topics' ) ) && str_contains( $target, '/topics' ) ) {
 return true;
	}

	if ( is_page( 'insights' ) && str_contains( $target, '/insights' ) ) {
 return true;
	}

	if ( is_page( 'about' ) && str_contains( $target, '/about' ) ) {
 return true;
	}

	if ( is_page( 'subscribe' ) && str_contains( $target, '/subscribe' ) ) {
 return true;
	}

	if ( is_singular( 'post' ) && str_contains( $target, '/insights' ) ) {
 return true;
	}

	if ( is_tax( 'resource_type' ) && str_contains( $target, '/resources' ) ) {
 return true;
	}

	if ( is_singular( 'resource' ) && str_contains( $target, '/resources' ) ) {
 return true;
	}

	return false;
}

/**
 * Whether a Resources or Topics nav section matches the current route.
 *
 * @param string $type resources|topics.
 * @return bool
 */
function msr_publishing_nav_section_is_active( $type ) {
	if ( 'resources' === $type ) {
 return is_post_type_archive( 'resource' ) || is_tax( 'resource_type' ) || is_singular( 'resource' );
	}
	if ( 'topics' === $type ) {
 return is_page( 'topics' ) || is_tax( 'topic' );
	}

	return false;
}

/**
 * Sub-links for Resources / Topics in the fullscreen overlay.
 *
 * @param string $type resources|topics.
 * @return void
 */
function msr_publishing_render_fullscreen_nav_sublist( $type ) {
	$type = (string) $type;

	if ( 'resources' === $type ) {
 $terms = msr_publishing_get_nav_megamenu_resource_formats();
 $view_all = get_post_type_archive_link( 'resource' );
 $eyebrow = __( 'Browse by format', 'msrsandbox' );
 $view_label = __( 'View all resources', 'msrsandbox' );
	} elseif ( 'topics' === $type ) {
 $terms = msr_publishing_get_nav_megamenu_topics();
 $view_all = msr_publishing_get_topic_hub_url();
 $eyebrow = __( 'Browse by topic', 'msrsandbox' );
 $view_label = __( 'View topics hub', 'msrsandbox' );
	} else {
 return;
	}

	if ( empty( $terms ) && ! $view_all ) {
 return;
	}
	?>
	<div class="site-header__mobile-nav__subgroup">
 <p class="site-header__mobile-nav__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
 <?php if ( ! empty( $terms ) ) : ?>
 <ul class="site-header__mobile-nav__sublist">
 <?php foreach ( $terms as $term ) : ?>
 <?php
 $term_link = get_term_link( $term );
 if ( is_wp_error( $term_link ) ) {
 continue;
 }
 $sub_current = msr_publishing_nav_link_is_current( (string) $term_link );
 $sub_class = 'site-header__mobile-nav__sublink';
 if ( $sub_current ) {
 $sub_class .= ' is-current';
 }
 ?>
 <li class="site-header__mobile-nav__subitem">
 <a
 class="<?php echo esc_attr( $sub_class ); ?>"
 href="<?php echo esc_url( $term_link ); ?>"
 <?php echo $sub_current ? ' aria-current="page"' : ''; ?>
 ><?php echo esc_html( $term->name ); ?></a>
 </li>
 <?php endforeach; ?>
 </ul>
 <?php endif; ?>
 <?php if ( $view_all ) : ?>
 <a class="site-header__mobile-nav__view-all" href="<?php echo esc_url( $view_all ); ?>"><?php echo esc_html( $view_label ); ?></a>
 <?php endif; ?>
	</div>
	<?php
}

/**
 * Current front-end URL for nav active-state checks.
 *
 * @return string
 */
function msr_publishing_get_current_request_url() {
	if ( is_singular() ) {
 return (string) get_permalink();
	}
	if ( is_post_type_archive() ) {
 $post_type = get_query_var( 'post_type' );
 if ( is_array( $post_type ) ) {
 $post_type = reset( $post_type );
 }
 if ( is_string( $post_type ) && $post_type !== '' ) {
 return (string) get_post_type_archive_link( $post_type );
 }
	}
	if ( is_tax() || is_category() ) {
 $term = get_queried_object();
 if ( $term instanceof WP_Term ) {
 $link = get_term_link( $term );
 return is_wp_error( $link ) ? '' : (string) $link;
 }
	}
	if ( is_search() ) {
 return (string) home_url( '/?s=' . rawurlencode( get_search_query() ) );
	}

	return (string) home_url( add_query_arg( array() ) );
}

/**
 * Resource format links for the Resources megamenu.
 *
 * @param int $limit Max terms.
 * @return WP_Term[]
 */
function msr_publishing_get_nav_megamenu_resource_formats( $limit = 0 ) {
	return msr_publishing_get_resource_type_nav_terms(
 array(
 'hide_empty' => true,
 'nav_only' => true,
 'limit' => max( 0, (int) $limit ),
 )
	);
}

/**
 * Topic links for the Topics megamenu.
 *
 * @param int $limit Max terms.
 * @return WP_Term[]
 */
function msr_publishing_get_nav_megamenu_topics( $limit = 6 ) {
	$terms = get_terms(
 array(
 'taxonomy' => 'topic',
 'hide_empty' => true,
 'number' => max( 1, (int) $limit ),
 )
	);

	return ( $terms && ! is_wp_error( $terms ) ) ? $terms : array();
}

/**
 * Render a Resources or Topics megamenu panel.
 *
 * @param string $type resources|topics.
 * @param string $panel_id Unique panel id.
 * @param string $parent_url Parent nav URL.
 * @param string $parent_label Parent nav label.
 * @return void
 */
function msr_publishing_render_nav_megamenu_panel( $type, $panel_id, $parent_url, $parent_label ) {
	$panel_id = sanitize_html_class( (string) $panel_id );
	$type = (string) $type;

	if ( 'resources' === $type ) {
 $terms = msr_publishing_get_nav_megamenu_resource_formats();
 $view_all = get_post_type_archive_link( 'resource' );
 $eyebrow = __( 'Browse by format', 'msrsandbox' );
 $view_label = __( 'View all resources', 'msrsandbox' );
	} elseif ( 'topics' === $type ) {
 $terms = msr_publishing_get_nav_megamenu_topics();
 $view_all = msr_publishing_get_topic_hub_url();
 $eyebrow = __( 'Browse by topic', 'msrsandbox' );
 $view_label = __( 'View topics hub', 'msrsandbox' );
	} else {
 return;
	}
	?>
	<div
 id="<?php echo esc_attr( $panel_id ); ?>"
 class="site-header__megamenu"
 role="region"
 aria-label="<?php echo esc_attr( sprintf( /* translators: %s: nav section */ __( '%s menu', 'msrsandbox' ), $parent_label ) ); ?>"
 hidden
	>
 <p class="site-header__megamenu__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
 <ul class="site-header__megamenu__list">
 <?php foreach ( $terms as $term ) : ?>
 <li class="site-header__megamenu__item">
 <a class="site-header__megamenu__link" href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a>
 </li>
 <?php endforeach; ?>
 </ul>
 <?php if ( $view_all ) : ?>
 <p class="site-header__megamenu__footer mb-0">
 <a class="site-header__megamenu__view-all" href="<?php echo esc_url( $view_all ); ?>"><?php echo esc_html( $view_label ); ?></a>
 </p>
 <?php endif; ?>
	</div>
	<?php
}

/**
 * Desktop primary navigation with Resources / Topics megamenus (lg+).
 *
 * @return void
 */
function msr_publishing_render_desktop_primary_nav() {
	$links = msr_publishing_get_primary_nav_links();
	if ( ! $links ) {
 return;
	}
	?>
	<nav class="site-header__desktop-nav d-none d-lg-flex" aria-label="<?php esc_attr_e( 'Primary', 'msrsandbox' ); ?>">
 <ul class="site-header__desktop-nav__list">
 <?php foreach ( $links as $index => $link ) : ?>
 <?php
 if ( empty( $link['url'] ) ) {
 continue;
 }
 $title = (string) $link['title'];
 $url = (string) $link['url'];
 $mega = msr_publishing_get_nav_mega_type( $title, $url );
 $is_current = msr_publishing_nav_link_is_current( $url );
 $panel_id = 'site-header-megamenu-' . sanitize_html_class( $mega !== '' ? $mega : 'link-' . $index );
 $item_class = 'site-header__desktop-nav__item';
 if ( $mega !== '' ) {
 $item_class .= ' site-header__desktop-nav__item--mega';
 }
 if ( $is_current ) {
 $item_class .= ' is-current';
 }
 ?>
 <li class="<?php echo esc_attr( $item_class ); ?>"<?php echo $mega !== '' ? ' data-site-header-megamenu' : ''; ?>>
 <?php if ( $mega !== '' ) : ?>
 <span class="site-header__desktop-nav__row">
 <button
 type="button"
 class="site-header__desktop-nav__trigger"
 aria-expanded="false"
 aria-controls="<?php echo esc_attr( $panel_id ); ?>"
 aria-label="<?php echo esc_attr( sprintf( /* translators: %s: nav section */ __( 'Show %s sections', 'msrsandbox' ), $title ) ); ?>"
 >
 <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
 </button>
 <a
 class="site-header__desktop-nav__link"
 href="<?php echo esc_url( $url ); ?>"
 <?php echo $is_current ? ' aria-current="page"' : ''; ?>
 ><?php echo esc_html( $title ); ?></a>
 </span>
 <?php
 msr_publishing_render_nav_megamenu_panel( $mega, $panel_id, $url, $title );
 ?>
 <?php else : ?>
 <a
 class="site-header__desktop-nav__link"
 href="<?php echo esc_url( $url ); ?>"
 <?php echo $is_current ? ' aria-current="page"' : ''; ?>
 ><?php echo esc_html( $title ); ?></a>
 <?php endif; ?>
 </li>
 <?php endforeach; ?>
 </ul>
	</nav>
	<?php
}

/**
 * Footer “Explore” links — footer menu, else primary menu, else fallback.
 *
 * @return array<int, array{title: string, url: string}>
 */
function msr_publishing_get_footer_explore_links() {
	foreach ( array( 'footer', 'menu-1' ) as $location ) {
 $links = msr_publishing_get_nav_links_from_location( $location );
 if ( ! empty( $links ) ) {
 return $links;
 }
	}

	return msr_publishing_get_primary_nav_fallback_links();
}

/**
 * Fallback primary menu when no menu is assigned to menu-1.
 *
 * @return void
 */
function msr_publishing_primary_menu_fallback() {
	echo '<div id="cssmenu"><ul>';
	foreach ( msr_publishing_get_primary_nav_fallback_links() as $link ) {
 if ( empty( $link['url'] ) ) {
 continue;
 }
 printf(
 '<li><a href="%s"><span>%s</span></a></li>',
 esc_url( $link['url'] ),
 esc_html( $link['title'] )
 );
	}
	echo '</ul></div>';
}

/**
 * Hide legacy demo footer links on the publishing theme.
 *
 * @param WP_Post[] $items Menu items.
 * @param stdClass $args Menu args.
 * @return WP_Post[]
 */
function msr_publishing_filter_legacy_footer_menu_items( $items, $args ) {
	if ( empty( $args->theme_location ) || 'footer' !== $args->theme_location ) {
 return $items;
	}

	$legacy_paths = array( '/members', '/partners', '/events' );
	$filtered = array();

	foreach ( $items as $item ) {
 $path = (string) wp_parse_url( $item->url, PHP_URL_PATH );
 $skip = false;

 foreach ( $legacy_paths as $fragment ) {
 if ( $path !== '' && str_contains( $path, $fragment ) ) {
 $skip = true;
 break;
 }
 }

 if ( ! $skip ) {
 $filtered[] = $item;
 }
	}

	return $filtered;
}
add_filter( 'wp_nav_menu_objects', 'msr_publishing_filter_legacy_footer_menu_items', 10, 2 );

/**
 * Register legacy demo CPT admin parent (event, publication, advert grouped here).
 *
 * @return void
 */
function msr_publishing_register_legacy_admin_menu() {
	add_menu_page(
 __( 'Legacy demos', 'msrsandbox' ),
 __( 'Legacy demos', 'msrsandbox' ),
 'edit_posts',
 'msr-legacy-demos',
 '__return_null',
 'dashicons-archive',
 26
	);
}
add_action( 'admin_menu', 'msr_publishing_register_legacy_admin_menu' );
