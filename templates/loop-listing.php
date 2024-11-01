<?php
/**
 * Template part for displaying posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.2
 */

?>

<article id="listing-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php
		//echo twentyseventeen_get_svg( array( 'icon' => 'thumb-tack' ) );
	?>

	<header class="entry-header">
		<?php
		$internal_or_external = zeListConfiguration::whichLinkShouldWeDisplayOnArchive();
		if($internal_or_external == 'external') {
			$field_name = zeListConfiguration::getURLFieldName();
			$url = get_post_meta(get_the_ID(), $field_name);
			if($url && !empty($url)) {
				the_title( '<h3 class="entry-title"><a href="' . esc_url( $url ) . '" rel="external">', '</a></h3>' );
			}
			else {
				the_title( '<h3 class="entry-title">', '</h3>' );
			}
			
		}
		else {
//			plouf(get_permalink(get_the_ID()), " PERMALINK pour " . get_the_ID());
			the_title( '<h3 class="entry-title"><a href="' . esc_url( get_permalink(get_the_ID()) ) . '" rel="bookmark">', '</a></h3>' );
		}
		?>
	</header><!-- .entry-header -->

	<?php if ( '' !== get_the_post_thumbnail() && ! is_single() ) : ?>
		<div class="post-thumbnail">
			<a href="<?php the_permalink(); ?>">
				<?php the_post_thumbnail('thumbnail' ); ?>
			</a>
		</div><!-- .post-thumbnail -->
	<?php endif; ?>

	<?php 
	//the_terms(get_the_ID(), 'listing_category', __('Categories: ', 'zelist')); 
	?>
	<?php 
	//the_terms(get_the_ID(), 'listing_tag', __('Tags: ', 'zelist')); 
	?>
	<?php zelist_listing_meta(); ?>

	<?php
//		twentyseventeen_entry_footer();
	?>

</article><!-- #post-## -->
