<?php
/**
 * The Header for our theme
 *
 * Displays all of the <head> section and everything up till <div id="eca-content">
 *
 * @package ECA
 * @subpackage ECA Theme
 * @since ECA 1.0
 */
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8) ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php wp_title( '|', true, 'right' ); ?></title><link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/images/favicon.ico" />
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,700,400italic,700italic' rel='stylesheet' type='text/css'>
<!--[if lt IE 9]>
<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js"></script>
<![endif]-->
<?php wp_head(); ?>
<script type="text/javascript">
/**
 * Initialize the buttons.
 * 
 * Convert the cart button to jquery ui button.
 * Convert the login button to jquery ui button.
 * 
 * @since ECA 1.0
 */
function initButtons() {
	jQuery("#eca-header-search button").button({
		icons: {
			primary: "ui-icon-search"
		},
		text: false	
	});
	
	jQuery("#button-cart").button({
		icons: {
			primary: "ui-icon-cart",
			secondary: "ui-icon-triangle-1-e"
		}
	});
	
	jQuery("#button-login").button({
		icons: {
			primary: "",
			secondary: "ui-icon-triangle-1-e"
		}
	});
}

/**
 * Initialize the site fontsizer.
 * 
 * Font switcher, change the font by adding a base font-size to the page body.
 * 
 * @since ECA 1.0
 */
function initFontsize() {
	
	jQuery('.fontsize').click(function() {
		var fontSize = jQuery(this).data("size");
		jQuery('body').css({'font-size': fontSize + 'px'});
	});
	
}

/**
 * Customise the main menu system.
 * 
 * Add classes to the parent node. (example: 'menu-parent our-work')
 * Add active to the current_page_item or current_page_ancestor.
 * Add the arrow class.
 * Add background color class to the page title div. (example: eca-header-title)
 * 
 * Hide all menu items after eca shop by adding the `links` class.
 * 
 * @since ECA 1.0
 */
function initMainMenu() {
	
	jQuery('div.menu > ul > li').each(function(i) {
		var txt = jQuery(this).find('a:first').text();
		var currentNode = txt.replace(/\s+/g, '-').toLowerCase();
		
		jQuery(this).addClass(currentNode);
		
		if ( jQuery(this).hasClass('current_page_item') || jQuery(this).hasClass('current_page_ancestor') ) {
			jQuery(this).addClass(currentNode + '-active');
		}
		
	});
	
}

var touchLoaded = false;
var touchMenu = null;
var touchMenuLink = null;
var touchMenuTrigger = null;
var touchLastState = null;
/**
 * Touch and screen resolution less than 768px menu setup.
 * 
 * http://responsivenavigation.net/examples/multi-toggle/
 * 
 * @since ECA 1.0
 */
function initTouchMenu() {
	if (touchLoaded == false) {
		
		touchMenu = jQuery('div.menu-main-menu-container');
		touchMenuLink = jQuery('a.menu-link');
		touchMenuTrigger = jQuery('div.menu-main-menu-container li.menu-item-has-children > a');
		
		touchMenuLink.click(function(e) {
			e.preventDefault();
			touchMenu.toggleClass('active');
			touchMenuLink.toggleClass('active');
		});
		
		touchLoaded = true;
	}
	
	if (touchLastState != touchMenuLink.css('display')) {
		touchLastState = touchMenuLink.css('display');
		
		if (touchLastState == 'block') {
			touchMenuTrigger.click(function(e) {
				if (jQuery(this).next('ul').length >= 1) {
					e.preventDefault();
				}
				jQuery(this).toggleClass('active').next('ul').toggleClass('active');
			});
		} else {
			touchMenuTrigger.off('click');
		}
	}
}

/**
 * Setup the scroll to top
 * 
 * @since ECA 1.0
 */
function initToTop() {
	var settings = {
		text: 'To Top',
		min: 200,
		inDelay: 600,
		outDelay: 400,
		containerID: 'toTop',
		containerHoverID: 'toTopHover',
		scrollSpeed: 400,
		easingType: 'linear'
	};
	
	var toTopHidden = true;
	var toTop = jQuery('#' + settings.containerID);
	
	toTop.click(function(e) {
		e.preventDefault();
		jQuery.scrollTo(0, settings.scrollSpeed, {easing: settings.easingType});
	});
	
	jQuery(window).scroll(function() {
		var sd = jQuery(this).scrollTop();
		if (sd > settings.min && toTopHidden) {
			toTop.fadeIn(settings.inDelay);
			toTopHidden = false;
		} else if(sd <= settings.min && ! toTopHidden) {
			toTop.fadeOut(settings.outDelay);
			toTopHidden = true;
		}
	});   
}

/** 
 * Fix the "Sign Up Now" adding an item to the cart with no user feedback.
 * Without this function, the product is added to the cart, but because the
 * page reloads, no user feedback is provided.
 * 
 * @since ECA 1.0
 */
function ajaxFixForMembershipAndSubscriptions() {
	jQuery('a.product_type_subscription').each(function(index) {
		jQuery(this).addClass('product_type_simple');
	});
}

jQuery(document).ready(function() {
	initButtons();
	initFontsize();
	initMainMenu();
	initTouchMenu();
	initToTop();
	//initGoogle();
	ajaxFixForMembershipAndSubscriptions();
	/*
	var i = jQuery('a[href*="hidden-front-end-user"]').parent();
	if (window.console.log) {
		console.log(i);
	}
	*/
});

/**
 * Google Custom search engine
 * 
 * @since ECA 1.0 
 */
function parseParamsFromUrl() {	
	var params = {};
	var parts = window.location.search.substr(1).split('\x26');	
	for (var i = 0; i < parts.length; i++) {
		var keyValuePair = parts[i].split('=');
		var key = decodeURIComponent(keyValuePair[0]);
		params[key] = keyValuePair[1] ? decodeURIComponent(keyValuePair[1].replace(/\+/g, ' ')) : keyValuePair[1];
	}
	return params;
}
function gcseCallback() {
	if (document.readyState != 'complete') {
		return google.setOnLoadCallback(gcseCallback, true);
	}
	google.search.cse.element.render({
		gname: 'gsrch',
 		div: 'results',
 		tag: 'searchresults-only',
 		attributes: {
			linkTarget: ''
		}
	});
	var urlParams = parseParamsFromUrl();
	var element = google.search.cse.element.getElement('gsrch');
	var queryParamName = "search";
	if (urlParams[queryParamName]) {
		element.execute(urlParams[queryParamName]);	
	}
}

//function initGoogle() {
	window.__gcse = {
		parsetags: 'explicit',
		callback: gcseCallback
	};
//}

(function() {
	var cx = '011188576967674765517:uyc3tuh6xtq';
	var gcse = document.createElement('script');
	gcse.type = 'text/javascript';
	gcse.async = true;
	gcse.src = (document.location.protocol == 'https:' ? 'https:' : 'http:') + '//www.google.com/cse/cse.js?cx=' + cx;
	var s = document.getElementsByTagName('script')[0];
	s.parentNode.insertBefore(gcse, s);
})();

/** {{{
* jQuery.ScrollTo - Easy element scrolling using jQuery.
* Copyright (c) 2007-2013 Ariel Flesler - aflesler<a>gmail<d>com | http://flesler.blogspot.com
* Dual licensed under MIT and GPL.
* @author Ariel Flesler
* @version 1.4.6
*/
(function($){var h=$.scrollTo=function(a,b,c){$(window).scrollTo(a,b,c)};h.defaults={axis:'xy',duration:parseFloat($.fn.jquery)>=1.3?0:1,limit:true};h.window=function(a){return $(window)._scrollable()};$.fn._scrollable=function(){return this.map(function(){var a=this,isWin=!a.nodeName||$.inArray(a.nodeName.toLowerCase(),['iframe','#document','html','body'])!=-1;if(!isWin)return a;var b=(a.contentWindow||a).document||a.ownerDocument||a;return/webkit/i.test(navigator.userAgent)||b.compatMode=='BackCompat'?b.body:b.documentElement})};$.fn.scrollTo=function(e,f,g){if(typeof f=='object'){g=f;f=0}if(typeof g=='function')g={onAfter:g};if(e=='max')e=9e9;g=$.extend({},h.defaults,g);f=f||g.duration;g.queue=g.queue&&g.axis.length>1;if(g.queue)f/=2;g.offset=both(g.offset);g.over=both(g.over);return this._scrollable().each(function(){if(e==null)return;var d=this,$elem=$(d),targ=e,toff,attr={},win=$elem.is('html,body');switch(typeof targ){case'number':case'string':if(/^([+-]=?)?\d+(\.\d+)?(px|%)?$/.test(targ)){targ=both(targ);break}targ=$(targ,this);if(!targ.length)return;case'object':if(targ.is||targ.style)toff=(targ=$(targ)).offset()}$.each(g.axis.split(''),function(i,a){var b=a=='x'?'Left':'Top',pos=b.toLowerCase(),key='scroll'+b,old=d[key],max=h.max(d,a);if(toff){attr[key]=toff[pos]+(win?0:old-$elem.offset()[pos]);if(g.margin){attr[key]-=parseInt(targ.css('margin'+b))||0;attr[key]-=parseInt(targ.css('border'+b+'Width'))||0}attr[key]+=g.offset[pos]||0;if(g.over[pos])attr[key]+=targ[a=='x'?'width':'height']()*g.over[pos]}else{var c=targ[pos];attr[key]=c.slice&&c.slice(-1)=='%'?parseFloat(c)/100*max:c}if(g.limit&&/^\d+$/.test(attr[key]))attr[key]=attr[key]<=0?0:Math.min(attr[key],max);if(!i&&g.queue){if(old!=attr[key])animate(g.onAfterFirst);delete attr[key]}});animate(g.onAfter);function animate(a){$elem.animate(attr,f,g.easing,a&&function(){a.call(this,targ,g)})}}).end()};h.max=function(a,b){var c=b=='x'?'Width':'Height',scroll='scroll'+c;if(!$(a).is('html,body'))return a[scroll]-$(a)[c.toLowerCase()]();var d='client'+c,html=a.ownerDocument.documentElement,body=a.ownerDocument.body;return Math.max(html[scroll],body[scroll])-Math.min(html[d],body[d])};function both(a){return typeof a=='object'?a:{top:a,left:a}}})(jQuery);
/*}}}*/

var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-93370-1']);
_gaq.push(['_trackPageview']);

(function() {
	var ga = document.createElement('script'); 
	ga.type = 'text/javascript'; 
	ga.async = true;
	ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	var s = document.getElementsByTagName('script')[0]; 
	s.parentNode.insertBefore(ga, s);
})();
</script>
</head>

<body <?php body_class(); ?>>

<div id="eca-page" class="hfeed site">
	<div id="eca-page-container">
		
		<?php 
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		$woocommerce_active = is_plugin_active('woocommerce/woocommerce.php');
		
		if($woocommerce_active) {
			if (get_option( 'woocommerce_demo_store' ) == 'yes') { ?>
			<header id="eca-header" class="site-header site-header-woo-notice-text" role="banner">
			<?php 
			}
			else { ?>
				<header id="eca-header" class="site-header" role="banner">
			<?php
			}
		}
		else { ?>
			<header id="eca-header" class="site-header" role="banner">
			<?php 
		}
		?>
			<div id="eca-header-border"></div>
			
			<div id="eca-header-top">
				<ul id="eca-header-links" class="">
					<li><div><span class="eca-icon eca-icon-phone"></span>+61 2 6242 1800</div></li><li id="link-email">
					<a href="mailto:sales@earlychildhood.org.au"><span class="eca-icon eca-icon-email"></span>sales@earlychildhood.org.au</a></li><li id="link-our-work">
					<a href="http://www.earlychildhoodaustralia.org.au/our-work/">OUR WORK</a></li><li id="link-learning-hub">
					<a href="http://www.earlychildhoodaustralia.org.au/learning-hub/">LEARNING HUB</a></li><li id="link-parent-resources">
					<a href="http://www.earlychildhoodaustralia.org.au/parent-resources/">PARENT RESOURCES</a></li><li id="link-our-publications">
					<a href="http://www.earlychildhoodaustralia.org.au/our-publications/">OUR PUBLICATIONS</a></li><li id="link-become-a-member">
					<a href="http://www.earlychildhoodaustralia.org.au/become-a-member/">BECOME A MEMBER</a></li>
				</ul><!-- #eca-header-links -->
				
				<ul id="eca-header-social" class="">
					<li><a href="http://www.facebook.com/earlychildhoodaustralia" target="_blank" title="Facebook"><span class="eca-icon eca-icon-facebook"></span></a></li>
					<li><a href="http://twitter.com/EarlyChildAust" target="_blank" title="Twitter"><span class="eca-icon eca-icon-twitter"></span></a></li>
					<li><a href="http://www.linkedin.com/company/early-childhood-australia" target="_blank" title="Linkedin"><span class="eca-icon eca-icon-linkedin"></span></a></li>
					<li><a href="http://www.pinterest.com/ecaustralia/" target="_blank" title="Pinterest"><span class="eca-icon eca-icon-pinterest"></span></a></li>
				</ul><!-- #eca-header-social -->
				
				<div class="clearfix"></div>
			</div><!-- #eca-header-top -->
			
			<div id="eca-header-banner" class="content">
				<div id="eca-header-logo">
					<a href="http://www.earlychildhoodaustralia.org.au/shop/" rel="home">
						<img src="<?php echo get_template_directory_uri(); ?>/images/eca-shop-logo-561x85px.png" width="561" height="85" alt="" />
					</a>
				</div><!-- #eca-header-logo -->
				
				<div id="eca-header-tools">
					<div id="eca-header-search">
						<form method="get" action="<?php echo home_url('/search-results/');?>">
							<input type="text" id="search" name="search" class="ui-corner-all" />
							<button type="submit">Search</button>
						</form>
					</div><!-- #eca-header-search -->
					<div id="eca-header-buttons">
					<?php 
						global $woocommerce; 
						$current_user = wp_get_current_user();
					?>
						<a class="cart-contents" id="button-cart" href="<?php echo $woocommerce->cart->get_cart_url(); ?>" title="<?php _e('View your shopping cart', 'eca'); ?>"><?php echo sprintf(_n('%d ITEM', '%d ITEMS', $woocommerce->cart->cart_contents_count, 'eca'), $woocommerce->cart->cart_contents_count);?> - <?php echo $woocommerce->cart->get_cart_total(); ?></a>
					<?php if ( is_user_logged_in() ) : ?>
						<a class="cart-login" id="button-login" href="<?php echo home_url('/my-account/customer-logout/');?>" title="<?php _e('Logout of your account', 'eca'); ?>">LOGOUT<?php //echo ' (' . $current_user->user_login . ')'; ?></a>
					<?php else : ?>
						<a class="cart-login" id="button-login" href="<?php echo home_url('/my-account/');?>" title="<?php _e('Login to your account', 'eca'); ?>">LOGIN</a>
					<?php endif; ?>
					</div><!-- #eca-header-buttons -->
				</div><!-- #eca-header-tools -->
			</div><!-- #eca-header-banner -->
			
			<nav id="primary-navigation" class="site-navigation primary-navigation content" role="navigation">
				<a class="menu-link" href="#menu">Menu&nbsp;&nbsp;(touch to start)</a>
				<?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_class' => 'menu', 'depth' => 2 ) ); ?>
				<div class="clearfix"></div>
			</nav><!-- #primary-navigation -->
			
		<?php if ( !is_front_page() ) : ?>
			<div class="content clearfix">
				<div id="eca-header-breadcrumbs">
					<?php if ( function_exists('yoast_breadcrumb') ) : ?>
					<?php yoast_breadcrumb(); ?>
					<?php endif; ?>
				</div><!-- #eca-header-breadcrumbs -->
				<div id="eca-header-textsize">
					<ul>
						<li class="fontsize">TEXT SIZE</li>
						<li class="fontsize" data-size="10">
							<span class="fontsize-1">A</span>
						</li>
						<li class="fontsize" data-size="12">
							<span class="fontsize-2">A</span>
						</li>
						<li class="fontsize" data-size="14">
							<span class="fontsize-3">A</span>
						</li>
					</ul>
				</div><!-- eca-header-textsize -->
			</div>
		<?php endif; ?>
			
			<div class="clearfix"></div>
		</header><!-- #eca-header -->
		
		<div id="eca-content" class="site-main content">
			