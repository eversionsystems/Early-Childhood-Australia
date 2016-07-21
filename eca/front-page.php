<?php
/**
 * The Sidebar containing the main widget area
 *
 * @package ECA
 * @subpackage ECA Theme
 * @since ECA 1.0
 */

get_header(); 
?>

<div id="eca-front-page" class="content">
	<div id="eca-front-page-panel">
		<div id="slider">
			<?php putRevSlider("home") ?>
		</div><!-- #slider -->
		<div id="botm">
			<div id="botm-title">
				<span class="title">Book of the<br />month</span>
			</div><!-- #botm-title -->
			<?php
				$result = do_shortcode_func( 'product', array( 'sku' => 'SUND606' ) );
				echo $result;
			?>
		</div><!-- #botm -->
		<div class="clearfix"></div>
		<div id="featured-products">
			<?php
				//$result = do_shortcode_func( 'featured_products', array( 'per_page' => '100', 'columns' => '100' ) );
				$result = do_shortcode_func( 'wpb-feature-product', array( 'title' => 'Featured Products' ) );
				echo $result;
				
				$result = do_shortcode_func( 'wpb-latest-product', array( 'title' => 'Latest Products', 'items' => '3' ) );
				echo $result;
			?>
		</div><!-- #featured-products -->
		<?php dynamic_sidebar( 'sidebar-4' ); ?>
	</div><!-- #eca-front-page-panel -->
	<div class="clearfix"></div>
</div><!-- #eca-front-page -->

<?php
get_footer();