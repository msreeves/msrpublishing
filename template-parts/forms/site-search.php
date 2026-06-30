<?php
/**
 * Site search form partial.
 *
 * @package msrsandbox
 *
 * @var array $args {
 *     @type string $input_id Unique input id.
 *     @type bool   $compact  Compact padding for header shell.
 * }
 */

$input_id    = isset( $args['input_id'] ) ? sanitize_html_class( (string) $args['input_id'] ) : 'msr-site-search-input';
$compact     = ! empty( $args['compact'] );
$wrapper_cls = 'msr-site-search' . ( $compact ? ' msr-site-search--compact' : ' p-5' );
$type_filter = is_search() && function_exists( 'msr_publishing_get_search_filter' ) ? msr_publishing_get_search_filter() : 'all';
$topic_slug  = is_search() && function_exists( 'msr_publishing_get_search_topic_slug' ) ? msr_publishing_get_search_topic_slug() : '';
$format_slug = is_search() && function_exists( 'msr_publishing_get_search_format_slug' ) ? msr_publishing_get_search_format_slug() : '';
$sort        = is_search() && function_exists( 'msr_publishing_get_search_sort' ) ? msr_publishing_get_search_sort() : 'relevance';
?>
<div class="<?php echo esc_attr( $wrapper_cls ); ?>">
	<form role="search" method="get" class="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'Site search', 'msrsandbox' ); ?>">
		<div class="input-group flex-wrap flex-md-nowrap">
			<label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>"><?php esc_html_e( 'Search', 'msrsandbox' ); ?></label>
			<input class="form-control" type="search" name="s" id="<?php echo esc_attr( $input_id ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search Atlas Briefing…', 'msrsandbox' ); ?>" autocomplete="off" />
			<?php if ( is_search() && 'all' !== $type_filter ) : ?>
				<input type="hidden" name="post_type" value="<?php echo esc_attr( $type_filter ); ?>" />
			<?php endif; ?>
			<?php if ( is_search() && '' !== $topic_slug ) : ?>
				<input type="hidden" name="topic" value="<?php echo esc_attr( $topic_slug ); ?>" />
			<?php endif; ?>
			<?php if ( is_search() && '' !== $format_slug ) : ?>
				<input type="hidden" name="resource_type" value="<?php echo esc_attr( $format_slug ); ?>" />
			<?php endif; ?>
			<?php if ( is_search() && 'relevance' !== $sort ) : ?>
				<input type="hidden" name="msr_sort" value="<?php echo esc_attr( $sort ); ?>" />
			<?php endif; ?>
			<input class="btn btn-primary" type="submit" value="<?php esc_attr_e( 'Search', 'msrsandbox' ); ?>" />
		</div>
	</form>
</div>
