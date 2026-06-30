<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">
        <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>
	<a class="publishing-skip-link" href="#site-content"><?php esc_html_e( 'Skip to content', 'msrsandbox' ); ?></a>
	<script>document.documentElement.classList.add('js-reveal');</script>
	<noscript><style>.msr-reveal{opacity:1!important;transform:none!important;transition:none!important}</style></noscript>
	<?php
	if ( msr_publishing_show_leaderboard_ads() ) {
		get_template_part( 'templates/partials/leaderboard/header' );
	}
	get_template_part( 'template-parts/header/site', 'nav' );
	if ( ! is_front_page() ) {
		msr_publishing_the_breadcrumbs();
	}
	?>
