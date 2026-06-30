<?php
/**
 * Author / byline helpers for resources and commentary.
 *
 * @package msrsandbox
 */

/**
 * @param int $post_id Post ID.
 * @return array{name: string, title: string, image_id: int, source: string}
 */
function msr_publishing_get_content_author_meta( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$meta    = array(
		'name'     => '',
		'title'    => '',
		'image_id' => 0,
		'source'   => '',
	);

	if ( ! $post_id ) {
		return $meta;
	}

	if ( function_exists( 'get_field' ) ) {
		$guest = trim( (string) get_field( 'guest_author_name', $post_id ) );
		if ( $guest !== '' ) {
			$meta['name']     = $guest;
			$meta['title']    = trim( (string) get_field( 'guest_author_title', $post_id ) );
			$meta['image_id'] = (int) get_field( 'guest_author_image', $post_id );
			$meta['source']   = 'guest';
			return $meta;
		}
	}

	$author_id = (int) get_post_field( 'post_author', $post_id );
	if ( $author_id > 0 ) {
		$user    = get_userdata( $author_id );
		$display = trim( (string) get_the_author_meta( 'display_name', $author_id ) );
		$login   = $user ? (string) $user->user_login : '';
		$generic = in_array( strtolower( $display ), array( 'admin', 'msr', 'msreeves' ), true )
			|| ( $login !== '' && strtolower( $display ) === strtolower( $login ) );
		if ( $display !== '' && ! $generic ) {
			$meta['name']   = $display;
			$meta['source'] = 'wp';
			return $meta;
		}
	}

	if ( 'post' === get_post_type( $post_id ) ) {
		$meta['name']   = __( 'Atlas Briefing editorial', 'msrsandbox' );
		$meta['source'] = 'default';
	}

	return $meta;
}

/**
 * @param int  $post_id   Post ID.
 * @param bool $show_date Whether to show published date (commentary).
 * @return void
 */
function msr_publishing_render_content_byline( $post_id = 0, $show_date = false ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$author  = msr_publishing_get_content_author_meta( $post_id );

	if ( '' === $author['name'] && ! $show_date ) {
		return;
	}

	$mins = function_exists( 'get_field' ) ? (int) get_field( 'read_time_minutes', $post_id ) : 0;
	?>
	<div class="publishing-byline resource-single__byline text-muted small mb-2">
		<?php if ( $author['name'] !== '' ) : ?>
			<div class="publishing-byline__author d-flex align-items-center gap-2">
				<?php if ( $author['image_id'] > 0 ) : ?>
					<?php
					echo wp_get_attachment_image(
						$author['image_id'],
						'thumbnail',
						false,
						array(
							'class' => 'publishing-byline__avatar',
							'alt'   => '',
						)
					);
					?>
				<?php endif; ?>
				<p class="mb-0">
					<span class="publishing-byline__label visually-hidden"><?php esc_html_e( 'Author', 'msrsandbox' ); ?></span>
					<?php
					$profile_url = msr_publishing_get_expert_profile_url_for_name( $author['name'] );
					if ( $profile_url !== '' ) :
						?>
						<a class="publishing-byline__name publishing-byline__link fw-semibold text-body" href="<?php echo esc_url( $profile_url ); ?>"><?php echo esc_html( $author['name'] ); ?></a>
					<?php else : ?>
						<span class="publishing-byline__name fw-semibold text-body"><?php echo esc_html( $author['name'] ); ?></span>
					<?php endif; ?>
					<?php if ( $author['title'] !== '' ) : ?>
						<span class="publishing-byline__role"> — <?php echo esc_html( $author['title'] ); ?></span>
					<?php endif; ?>
				</p>
			</div>
		<?php endif; ?>
		<?php if ( $show_date ) : ?>
			<?php msr_publishing_render_single_dates( $post_id ); ?>
		<?php elseif ( $mins > 0 && 'resource' === get_post_type( $post_id ) ) : ?>
			<p class="resource-single__meta mb-0 mt-1">
				<?php echo esc_html( sprintf( _n( '%d min read', '%d min read', $mins, 'msrsandbox' ), $mins ) ); ?>
			</p>
		<?php endif; ?>
	</div>
	<?php
}
