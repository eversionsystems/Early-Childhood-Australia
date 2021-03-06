<?php
/**
 * The template used for displaying page content
 *
 * @package ECA
 * @subpackage ECA Theme
 * @since ECA 1.0
 */

get_header();
?>

<?php while ( have_posts() ) : the_post(); ?>
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<header class="entry-header">
			<h1 class="entry-title"><?php the_title(); ?></h1>
		</header><!-- .entry-header -->
		<div class="entry-content">
			<?php the_content(); ?>
		</div><!-- .entry-content -->
	</article><!-- #post-## -->
<?php endwhile; ?>

<?php 
get_footer();
