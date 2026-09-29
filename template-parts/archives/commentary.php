<?php
/**
 * Commentary archive — category and generic post archives.
 *
 * @package msrpublishing
 */

$is_category = is_category();
?>
<main id="site-content" class="publishing-commentary-archive">
	<div class="container py-4">
		<header class="publishing-section-header text-center mb-4">
			<?php if ( $is_category ) : ?>
				<p class="small text-uppercase fw-semibold text-muted mb-2"><?php esc_html_e( 'Commentary', 'msrpublishing' ); ?></p>
				<h1 class="page-title msr-reveal"><?php single_cat_title(); ?></h1>
				<?php
				$desc = term_description();
				if ( $desc ) {
					echo '<div class="taxonomy-description text-muted msr-reveal">' . wp_kses_post( $desc ) . '</div>';
				} else {
					?>
					<p class="text-muted msr-reveal">
						<?php esc_html_e( 'Editorial posts from Atlas Briefing — analysis alongside the resource library.', 'msrpublishing' ); ?>
					</p>
					<?php
				}
				?>
			<?php else : ?>
				<h1 class="page-title msr-reveal"><?php the_archive_title(); ?></h1>
				<?php the_archive_description( '<div class="taxonomy-description text-muted">', '</div>' ); ?>
			<?php endif; ?>
		</header>
	</div>
	<?php
	if ( $is_category ) {
		msr_publishing_render_commentary_discovery_panel( 'commentary' );
	}
	?>
	<div class="container pb-4">
		<?php if ( have_posts() ) : ?>
			<div class="row g-4">
				<?php
				while ( have_posts() ) {
					the_post();
					if ( ! $is_category && 'post' !== get_post_type() ) {
						continue;
					}
					echo '<div class="col-md-6 col-lg-4">';
					get_template_part( 'template-parts/cards/post', 'card' );
					echo '</div>';
				}
				?>
			</div>
			<div class="mt-4">
				<?php
				the_posts_pagination(
					array(
						'mid_size'  => 2,
						'prev_text' => __( 'Previous', 'msrpublishing' ),
						'next_text' => __( 'Next', 'msrpublishing' ),
					)
				);
				?>
			</div>
		<?php else : ?>
			<?php get_template_part( 'template-parts/content', 'none' ); ?>
		<?php endif; ?>
	</div>
	<?php
	if ( $is_category ) {
		msr_publishing_render_commentary_subscribe_band();
	}
	?>
</main>
