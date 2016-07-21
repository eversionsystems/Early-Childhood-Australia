<?php
/**
 * The template for displaying the footer
 *
 * Contains footer content and the closing of the #eca-page, #eca-page-container and #eca-content div elements.
 *
 * @package ECA
 * @subpackage ECA Theme
 * @since ECA 1.0
 */
?>
		</div><!-- #eca-content -->
		
		<footer id="eca-footer" class="site-footer content clearfix" role="contentinfo">
			<div id="eca-footer-color-bar">
				<div class="footer-color-bar our-work-color-bg"></div>
				<div class="footer-color-bar educator-resources-color-bg"></div>
				<div class="footer-color-bar parent-resources-color-bg"></div>
				<div class="footer-color-bar our-publications-color-bg"></div>
				<div class="clearfix"></div>
			</div><!-- #eca-footer-color-bar -->
			
			<div id="eca-footer-top-shadow"></div>
			<div id="eca-footer-top">
				<?php
				if (   is_active_sidebar( 'first-footer-widget-area'  )
					&& is_active_sidebar( 'second-footer-widget-area' )
					&& is_active_sidebar( 'third-footer-widget-area'  )
					&& is_active_sidebar( 'fourth-footer-widget-area' )
				) : ?>
				 
				<aside class="first quarter left widget-area">
					<?php dynamic_sidebar( 'first-footer-widget-area' ); ?>
				</aside><!-- .first .widget-area -->
			 
				<aside class="second quarter widget-area">
					<?php dynamic_sidebar( 'second-footer-widget-area' ); ?>
				</aside><!-- .second .widget-area -->
			 
				<aside class="third quarter widget-area">
					<?php dynamic_sidebar( 'third-footer-widget-area' ); ?>
				</aside><!-- .third .widget-area -->
			 
				<aside class="fourth quarter right widget-area">
					<?php dynamic_sidebar( 'fourth-footer-widget-area' ); ?>
				</aside><!-- .fourth .widget-area -->
				<?php 
				elseif ( is_active_sidebar( 'first-footer-widget-area'  )
					&& is_active_sidebar( 'second-footer-widget-area' )
					&& is_active_sidebar( 'third-footer-widget-area'  )
					&& ! is_active_sidebar( 'fourth-footer-widget-area' )
				) : ?>
				<aside class="fatfooter" role="complementary">
					<div class="first one-third left widget-area">
						<?php dynamic_sidebar( 'first-footer-widget-area' ); ?>
					</div><!-- .first .widget-area -->
				 
					<div class="second one-third widget-area">
						<?php dynamic_sidebar( 'second-footer-widget-area' ); ?>
					</div><!-- .second .widget-area -->
				 
					<div class="third one-third right widget-area">
						<?php dynamic_sidebar( 'third-footer-widget-area' ); ?>
					</div><!-- .third .widget-area -->
				 
				</aside><!-- #fatfooter -->
				<?php
				elseif ( is_active_sidebar( 'first-footer-widget-area'  )
					&& is_active_sidebar( 'second-footer-widget-area' )
					&& ! is_active_sidebar( 'third-footer-widget-area'  )
					&& ! is_active_sidebar( 'fourth-footer-widget-area' )
				) : ?>
				<aside class="fatfooter" role="complementary">
					<div class="first half left widget-area">
						<?php dynamic_sidebar( 'first-footer-widget-area' ); ?>
					</div><!-- .first .widget-area -->
				 
					<div class="second half right widget-area">
						<?php dynamic_sidebar( 'second-footer-widget-area' ); ?>
					</div><!-- .second .widget-area -->
				 
				</aside><!-- #fatfooter -->
				<?php
				elseif ( is_active_sidebar( 'first-footer-widget-area'  )
					&& ! is_active_sidebar( 'second-footer-widget-area' )
					&& ! is_active_sidebar( 'third-footer-widget-area'  )
					&& ! is_active_sidebar( 'fourth-footer-widget-area' )
				) :
				?>
				<aside class="fatfooter" role="complementary">
					<div class="first full-width widget-area">
						<?php dynamic_sidebar( 'first-footer-widget-area' ); ?>
					</div><!-- .first .widget-area -->
				</aside><!-- #fatfooter -->
				
				<?php endif; ?>
				
				<div class="clearfix"></div>
			</div><!-- #eca-footer-top -->
			
			<div id="eca-footer-bottom">
				<ul id="eca-footer-bottom-links">
					<li><a href="http://www.earlychildhoodaustralia.org.au/about-us/">About</a></li><li>
					<a href="http://www.earlychildhoodaustralia.org.au/privacy-statement/">Privacy Statement</a></li><li>
					<a href="http://www.earlychildhoodaustralia.org.au/copyright/">Copyright</a></li><li>
					<a href="http://www.earlychildhoodaustralia.org.au/disclaimer/">Disclaimer</a></li><li>
				</ul><!-- #eca-footer-bottom-links -->
				<div id="eca-footer-bottom-copyright">
					&copy; Copyright Early Childhood Australia Inc. <?php echo date('Y'); ?>
				</div><!-- #eca-footer-bottom-copyright -->
				<div class="clearfix"></div>
			</div><!-- #eca-footer-bottom -->
			
			<div id="eca-footer-bottom-shadow"></div>
		</footer><!-- #eca-footer-->
		
	</div><!-- #eca-page-container -->
</div><!-- #eca-page -->
<a id="toTop" href="javascript:;">
	<span id="toTopHover"></span>
	<img alt="To Top" src="<?php echo get_template_directory_uri(); ?>/images/to-top.png" height="40" width="40" />
</a><!-- #toTop -->
<?php wp_footer(); ?>
</body>
</html>