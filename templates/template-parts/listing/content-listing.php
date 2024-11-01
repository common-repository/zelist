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

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php
		//echo twentyseventeen_get_svg( array( 'icon' => 'thumb-tack' ) );
	?>
	<header class="entry-header">
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
	</header><!-- .entry-header -->

	<?php if ( '' !== get_the_post_thumbnail() ) : ?>
		<div class="post-image">
			<?php the_post_thumbnail( 'large' ); ?>
		</div><!-- .post-thumbnail -->
	<?php endif; ?>

	<div class="entry-content">
		<?php
		/* translators: %s: Name of current post */
		the_content( sprintf(
			__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'zelist'),
			get_the_title()
		) );
		?>
	</div><!-- .entry-content -->

	<?php zelist_listing_meta(); ?>

</article><!-- #post-## -->
