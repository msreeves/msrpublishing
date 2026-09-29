<?php
/**
 * Author archive — expert profiles + attributed commentary and resources.
 *
 * @package msrpublishing
 */

get_header();

$author = get_queried_object();
$expert = msr_publishing_get_expert_from_author_object( $author );
?>
<main id="site-content" class="publishing-author-archive container py-5">
	<?php if ( $author instanceof WP_User ) : ?>
		<?php msr_publishing_render_author_profile_header( $author ); ?>

		<?php if ( have_posts() ) : ?>
			<section class="publishing-author-archive__commentary mb-5" aria-labelledby="author-commentary-heading">
				<h2 id="author-commentary-heading" class="h4 mb-3"><?php esc_html_e( 'Commentary', 'msrpublishing' ); ?></h2>
				<div class="row g-3 msr-reveal-stagger">
					<?php
					while ( have_posts() ) {
						the_post();
						echo '<div class="col-md-6 col-lg-4">';
						get_template_part( 'template-parts/cards/post', 'card' );
						echo '</div>';
					}
					?>
				</div>
			</section>
			<?php
			the_posts_pagination(
				array(
					'mid_size'  => 1,
					'prev_text' => __( 'Previous', 'msrpublishing' ),
					'next_text' => __( 'Next', 'msrpublishing' ),
				)
			);
			?>
		<?php endif; ?>

		<?php if ( $expert ) : ?>
			<?php
			$resources = msr_publishing_get_expert_resources_query( $expert );
			if ( $resources->have_posts() ) :
				?>
				<section class="publishing-author-archive__resources" aria-labelledby="author-resources-heading">
					<h2 id="author-resources-heading" class="h4 mb-3"><?php esc_html_e( 'Resources', 'msrpublishing' ); ?></h2>
					<div class="row g-3 msr-reveal-stagger">
						<?php
						while ( $resources->have_posts() ) {
							$resources->the_post();
							echo '<div class="col-md-6">';
							get_template_part( 'template-parts/cards/resource', 'card' );
							echo '</div>';
						}
						wp_reset_postdata();
						?>
					</div>
				</section>
			<?php endif; ?>
		<?php endif; ?>
	<?php else : ?>
		<p class="text-muted"><?php esc_html_e( 'Author not found.', 'msrpublishing' ); ?></p>
	<?php endif; ?>
</main>
<?php
get_footer();
