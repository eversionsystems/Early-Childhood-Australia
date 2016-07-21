<?php
/**
 * The template for displaying 404 pages (Not Found)
 *
 * @package ECA
 * @subpackage ECA Theme
 * @since ECA 1.0
 */

get_header(); 
?>

<div id="main-content" class="main-content">
	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">
			<header class="page-header">
				<h1 class="page-title"><?php _e( 'Not Found', 'eca' ); ?></h1>
			</header>
			
			<div class="page-content">
				<p>Oops! You have requested a page that has been moved or no longer exists. Please visit our new website <a href="http://www.earlychildhoodaustralia.org.au">click here</a>. 
				Alternatively, we have included some links below for commonly searched items:</p>
				<ul>
					<li><a href="http://www.earlychildhoodaustralia.org.au/our-publications/eca-code-ethics/">Code of Ethics</a></li>
					<li><a href="http://www.earlychildhoodaustralia.org.au/learning-hub/">ECA Learning Hub</a></li>
					<li><a href="http://www.earlychildhoodaustralia.org.au/our-publications/">ECA Publications</a></li>
					<li><a href="http://www.earlychildhoodaustralia.org.au/shop/">ECA Shop</a></li>
				</ul>
				<p>&nbsp;</p>
			</div><!-- .page-content -->
			
		</div><!-- #content -->
	</div><!-- #primary -->
</div><!-- #main-content -->

<?php 
get_footer();
