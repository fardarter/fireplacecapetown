<?php if ( ! defined('BASEL_THEME_DIR')) exit('No direct script access allowed');

/**
 * ------------------------------------------------------------------------------------------------
 * Array of versions for dummy content import section
 * ------------------------------------------------------------------------------------------------
 */

return apply_filters( 'basel_get_versions_to_import', array(
	'base' => array(
		'title' => 'Base content (required)',
		'process' => 'xml,home,shop,menu,widgets,options,sliders',
		'type' => 'base'
	),
	// 'product_imagelarge' => array(
	// 	'title' => 'Large Image',
	// 	'process' => 'options',
	// 	'type' => 'product'
	// ),
	// 'product_imagesmall' => array(
	// 	'title' => 'Small Image',
	// 	'process' => 'options',
	// 	'type' => 'product'
	// ),
	// 'product_sidebar_right' => array(
	// 	'title' => 'Sidebar right',
	// 	'process' => 'options',
	// 	'type' => 'product'
	// ),
	// 'product_bg' => array(
	// 	'title' => 'Product with background',
	// 	'process' => 'options',
	// 	'type' => 'product'
	// ),
	// 'product_thumb_bottom' => array(
	// 	'title' => 'Thumbnails bottom',
	// 	'process' => 'options',
	// 	'type' => 'product'
	// ),
	// 'product_thumb_left' => array(
	// 	'title' => 'Thumbnails left',
	// 	'process' => 'options',
	// 	'type' => 'product'
	// ),
	// 'product_sticky' => array(
	// 	'title' => 'Sticky details',
	// 	'process' => 'options',
	// 	'type' => 'product'
	// ),
	// 'product_compact' => array(
	// 	'title' => 'Compact',
	// 	'process' => 'options',
	// 	'type' => 'product'
	// ),
	// 'product_alt' => array(
	// 	'title' => 'Alternative style',
	// 	'process' => 'options',
	// 	'type' => 'product'
	// ),
	// 'product_default' => array(
	// 	'title' => 'Default style',
	// 	'process' => 'options',
	// 	'type' => 'product'
	// ),
	// 'shop_fullwidth' => array(
	// 	'title' => 'Full width',
	// 	'process' => 'options',
	// 	'type' => 'shop'
	// ),
	// 'shop_hover8' => array(
	// 	'title' => 'Quick shop products',
	// 	'process' => 'options',
	// 	'type' => 'shop'
	// ),
	// 'shop_hover6' => array(
	// 	'title' => 'Standard button',
	// 	'process' => 'options',
	// 	'type' => 'shop'
	// ),
	// 'shop_hover4' => array(
	// 	'title' => 'Hover info',
	// 	'process' => 'options',
	// 	'type' => 'shop'
	// ),
	// 'shop_hover3' => array(
	// 	'title' => 'Button hover alt',
	// 	'process' => 'options',
	// 	'type' => 'shop'
	// ),
	// 'shop_hover2' => array(
	// 	'title' => 'Button on hover',
	// 	'process' => 'options',
	// 	'type' => 'shop'
	// ),
	// 'shop_hover1' => array(
	// 	'title' => 'Default style',
	// 	'process' => 'options',
	// 	'type' => 'shop'
	// ),
	// 'shop_alt' => array(
	// 	'title' => 'Alternative shop',
	// 	'process' => 'options',
	// 	'type' => 'shop'
	// ),
	// 'shop_masonry' => array(
	// 	'title' => 'Masonry grid',
	// 	'process' => 'options',
	// 	'type' => 'shop'
	// ),
	'list-element' => array(
		'title' => 'List Element',
		'process' => 'xml,page_menu',
		'type' => 'element',
		'parent_menu_title' => 'More Elements',
	),
	'extra-menu-list' => array(
		'title' => 'Extra Menu List',
		'process' => 'xml,page_menu',
		'type' => 'element',
		'parent_menu_title' => 'Xtemos Elements',
	),
	'brands-element' => array(
		'title' => 'Brands Element',
		'process' => 'xml,page_menu',
		'type' => 'element',
		'parent_menu_title' => 'WooCommerce',
	),
	'fashion-new' => array(
 		'title' => 'Fashion New',
 		'process' => 'xml,home,options,widgets,sliders',
 		'type' => 'version'
 	),
	'timeline' => array(
		'title' => 'Timeline',
		'process' => 'xml,page_menu',
		'type' => 'element',
		'parent_menu_title' => 'More Elements',
	),
	'gradients' => array(
		'title' => 'Gradients',
		'process' => 'xml,page_menu',
		'type' => 'element',
		'parent_menu_title' => 'Xtemos Elements',
	),
	'section-dividers' => array(
		'title' => 'Section Dividers',
		'process' => 'xml,page_menu',
		'type' => 'element',
		'parent_menu_title' => 'Xtemos Elements',
	),
	'lighting' => array(
 		'title' => 'Lighting',
 		'process' => 'xml,home,options,widgets,sliders',
 		'type' => 'version'
 	),
	'bakery' => array(
 		'title' => 'Bakery',
 		'process' => 'xml,home,options,widgets,sliders',
 		'type' => 'version'
 	),
	'medical' => array(
 		'title' => 'Medical',
 		'process' => 'xml,home,options,widgets,sliders',
 		'type' => 'version'
 	),
	'beer' => array(
 		'title' => 'Beer',
 		'process' => 'xml,home,options,widgets,sliders',
 		'type' => 'version'
 	),
	'sushi' => array(
 		'title' => 'Sushi',
 		'process' => 'xml,home,options,widgets,sliders',
 		'type' => 'version'
 	),
	'hookahs' => array(
 		'title' => 'Hookahs',
 		'process' => 'xml,home,options,widgets,sliders',
 		'type' => 'version'
 	),
	'posters' => array(
 		'title' => 'Posters',
 		'process' => 'xml,home,options,widgets,sliders',
 		'type' => 'version'
 	),
	'games' => array(
 		'title' => 'Games',
 		'process' => 'xml,home,options,widgets,sliders',
 		'type' => 'version'
 	),
	'pets' => array(
		'title' => 'Pets',
		'process' => 'xml,home,options,widgets,sliders',
		'type' => 'version'
	),
	'coming-soon' => array(
		'title' => 'Coming soon (maintenance)',
		'process' => 'xml',
		'type' => 'element'
	),
	'coming-soon-2' => array(
		'title' => 'Coming soon 2 (maintenance)',
		'process' => 'xml,sliders',
		'type' => 'element'
	),
	'countdown-timer' => array(
		'title' => 'Countdown Timer',
		'process' => 'xml,page_menu',
		'type' => 'element',
		'parent_menu_title' => 'More Elements',
	),
	'360-degree-view' => array(
		'title' => '360 Degree View',
		'process' => 'xml,page_menu',
		'type' => 'element',
		'parent_menu_title' => 'More Elements',
	),
	'menu-price' => array(
		'title' => 'Menu Price',
		'process' => 'xml,page_menu',
		'type' => 'element',
		'parent_menu_title' => 'More Elements',
	),
	'infobox' => array(
		'title' => 'Infobox',
		'process' => 'xml,page_menu',
		'type' => 'element',
		'parent_menu_title' => 'More Elements',
	),
	'pricing-tables' => array(
		'title' => 'Pricing Tables',
		'process' => 'xml,page_menu',
		'type' => 'element',
		'parent_menu_title' => 'More Elements',
	),
	'product-ajax-arrows' => array(
		'title' => 'Product AJAX Arrows',
		'process' => 'xml,page_menu',
		'type' => 'element',
		'parent_menu_title' => 'More Elements',
	),
	'product-load-more' => array(
		'title' => 'Product Load More',
		'process' => 'xml,page_menu',
		'type' => 'element',
		'parent_menu_title' => 'More Elements',
	),
	'portfolio-element' => array(
		'title' => 'Portfolio Element',
		'process' => 'xml,page_menu',
		'type' => 'element',
		'parent_menu_title' => 'More Elements',
	),
	'images-gallery' => array(
		'title' => 'Images Gallery',
		'process' => 'xml,page_menu',
		'type' => 'element',
		'parent_menu_title' => 'More Elements',
	),
	'ajax-products-tabs' => array(
		'title' => 'AJAX Products Tabs',
		'process' => 'xml,page_menu',
		'type' => 'element',
		'parent_menu_title' => 'More Elements',
	),
	'products-slider' => array(
		'title' => 'Products Slider',
		'process' => 'xml,page_menu',
		'type' => 'element',
		'parent_menu_title' => 'WooCommerce',
	),
	'google-maps' => array(
		'title' => 'Google Maps',
		'process' => 'xml,page_menu',
		'type' => 'element',
		'parent_menu_title' => 'Xtemos Elements',
	),
	'banners' => array(
		'title' => 'Banners',
		'process' => 'xml,page_menu',
		'type' => 'element',
		'parent_menu_title' => 'Xtemos Elements',
	),
	'titles' => array(
		'title' => 'Titles',
		'process' => 'xml,page_menu',
		'type' => 'element',
		'parent_menu_title' => 'Xtemos Elements',
	),
	'instagram' => array(
		'title' => 'Instagram',
		'process' => 'xml,page_menu',
		'type' => 'element',
		'parent_menu_title' => 'Xtemos Elements',
	),
	'social-buttons' => array(
		'title' => 'Social Buttons',
		'process' => 'xml,page_menu',
		'type' => 'element',
		'parent_menu_title' => 'Xtemos Elements',
	),
	'team-member' => array(
		'title' => 'Team Member',
		'process' => 'xml,page_menu',
		'type' => 'element',
		'parent_menu_title' => 'Xtemos Elements',
	),
	'testimonials' => array(
		'title' => 'Testimonials',
		'process' => 'xml,page_menu',
		'type' => 'element',
		'parent_menu_title' => 'Xtemos Elements',
	),
	'blog-element' => array(
		'title' => 'Blog Element',
		'process' => 'xml,page_menu',
		'type' => 'element',
		'parent_menu_title' => 'Xtemos Elements',
	),
	'animated-counter' => array(
		'title' => 'Animated Counter',
		'process' => 'xml,page_menu',
		'type' => 'element',
		'parent_menu_title' => 'Xtemos Elements',
	),
	'recent-products' => array(
		'title' => 'Recent Products',
		'process' => 'xml,page_menu',
		'type' => 'element',
		'parent_menu_title' => 'WooCommerce',
	),
	'featured-products' => array(
		'title' => 'Featured Products',
		'process' => 'xml,page_menu',
		'type' => 'element',
		'parent_menu_title' => 'WooCommerce',
	),
	'element-products' => array(
		'title' => 'Element Products',
		'process' => 'xml,page_menu',
		'type' => 'element',
		'parent_menu_title' => 'WooCommerce',
	),
	'single-product' => array(
		'title' => 'Single Product',
		'process' => 'xml,page_menu',
		'type' => 'element',
		'parent_menu_title' => 'WooCommerce',
	),
	'products-by-id' => array(
		'title' => 'Products by ID',
		'process' => 'xml,page_menu',
		'type' => 'element',
		'parent_menu_title' => 'WooCommerce',
	),
	'products-category' => array(
		'title' => 'Products Category',
		'process' => 'xml,page_menu',
		'type' => 'element',
		'parent_menu_title' => 'WooCommerce',
	),
	'products-categories' => array(
		'title' => 'Products Categories',
		'process' => 'xml,page_menu',
		'type' => 'element',
		'parent_menu_title' => 'WooCommerce',
	),
	'sale-products' => array(
		'title' => 'Sale Products',
		'process' => 'xml,page_menu',
		'type' => 'element',
		'parent_menu_title' => 'WooCommerce',
	),
	'top-rated-products' => array(
		'title' => 'Top Rated Products',
		'process' => 'xml,page_menu',
		'type' => 'element',
		'parent_menu_title' => 'WooCommerce',
	),
	'products-widgets' => array(
		'title' => 'Products Widgets',
		'process' => 'xml,page_menu,widgets',
		'type' => 'element',
		'parent_menu_title' => 'WooCommerce',
	),
	'faq' => array(
		'title' => 'FaQs',
		'process' => 'xml,page_menu',
		'type' => 'page',
		'parent_menu_title' => 'Additional',
	),
	'about-me' => array(
		'title' => 'About Me',
		'process' => 'xml,page_menu',
		'type' => 'page',
		'parent_menu_title' => 'Additional',
	),
	'our-shop' => array(
		'title' => 'Our Shop',
		'process' => 'xml,page_menu',
		'type' => 'page',
		'parent_menu_title' => 'Additional',
	),
	'our-services' => array(
		'title' => 'Our Service',
		'process' => 'xml,page_menu',
		'type' => 'page',
		'parent_menu_title' => 'Additional',
	),
	'our-company' => array(
		'title' => 'Our Company',
		'process' => 'xml,page_menu',
		'type' => 'page',
		'parent_menu_title' => 'Additional',
	),
	'contact-us' => array(
		'title' => 'Contact Us',
		'process' => 'xml,page_menu',
		'type' => 'page',
		'parent_menu_title' => 'Additional',
	),
	'contact-us-2' => array(
		'title' => 'Contact Us 2',
		'process' => 'xml,page_menu',
		'type' => 'page',
		'parent_menu_title' => 'Additional',
	),
	'our-gallery' => array(
		'title' => 'Our Gallery',
		'process' => 'xml,page_menu,sliders',
		'type' => 'page',
		'parent_menu_title' => 'Additional',
	),
	'furniture' => array(
		'title' => 'Furniture demo',
		'process' => 'xml,home,options,widgets,sliders',
		'type' => 'version'
	),
	'fashion' => array(
		'title' => 'Fashion demo',
		'process' => 'xml,home,options,widgets,sliders',
		'type' => 'version'
	),
	'bicycle' => array(
		'title' => 'Bicycle demo',
		'process' => 'xml,home,options,widgets,sliders',
		'type' => 'version'
	),
	'minimalist' => array(
		'title' => 'The Minimalist demo',
		'process' => 'xml,home,options,widgets,sliders',
		'type' => 'version'
	),
	'lingerie' => array(
		'title' => 'Lingerie demo',
		'process' => 'xml,home,options,widgets,sliders',
		'type' => 'version'
	),
	'jewellery' => array(
		'title' => 'Jewellery demo',
		'process' => 'xml,home,options,widgets,sliders',
		'type' => 'version'
	),
	'electronics' => array(
		'title' => 'Electronics demo',
		'process' => 'xml,home,options,widgets,sliders',
		'type' => 'version'
	),
	'wine' => array(
		'title' => 'Wine demo',
		'process' => 'xml,home,options,widgets,sliders',
		'type' => 'version'
	),
	'fashion-flat' => array(
		'title' => 'Fashion flat',
		'process' => 'xml,home,options,widgets,sliders',
		'type' => 'version'
	),
	'cosmetics' => array(
		'title' => 'Cosmetics',
		'process' => 'xml,home,options,widgets,sliders',
		'type' => 'version'
	),
	'sport' => array(
		'title' => 'Sport',
		'process' => 'xml,home,options,widgets,sliders',
		'type' => 'version'
	),
	'landing' => array(
		'title' => 'Landing',
		'process' => 'xml,home,options,widgets,sliders',
		'type' => 'version'
	),
	'flat-full-width' => array(
		'title' => 'Flat full-width',
		'process' => 'xml,home,options,widgets,sliders',
		'type' => 'version'
	),
	'coffee' => array(
		'title' => 'Coffee',
		'process' => 'xml,home,options,widgets,sliders',
		'type' => 'version'
	),
	'organic' => array(
		'title' => 'Organic',
		'process' => 'xml,home,options,widgets,sliders',
		'type' => 'version'
	),
	'parallax' => array(
		'title' => 'Parallax',
		'process' => 'xml,home,options,widgets,sliders',
		'type' => 'version'
	),
	'marketplace' => array(
		'title' => 'Marketplace',
		'process' => 'xml,home,options,widgets,sliders',
		'type' => 'version'
	),
	'portfolio' => array(
		'title' => 'Portfolio',
		'process' => 'xml,home,options,widgets,sliders',
		'type' => 'version'
	),
	'food' => array(
		'title' => 'Food',
		'process' => 'xml,home,options,widgets,sliders',
		'type' => 'version'
	),
	'dark' => array(
		'title' => 'Dark',
		'process' => 'xml,home,options,widgets,sliders',
		'type' => 'version'
	),
	'shoes' => array(
		'title' => 'Shoes',
		'process' => 'xml,home,options,widgets,sliders',
		'type' => 'version'
	),
	'corporate' => array(
		'title' => 'Corporate',
		'process' => 'xml,home,options,widgets,sliders',
		'type' => 'version'
	),
	'construction' => array(
		'title' => 'Construction',
		'process' => 'xml,home,options,widgets,sliders',
		'type' => 'version'
	),
	'agency' => array(
		'title' => 'Agency',
		'process' => 'xml,home,options,widgets,sliders',
		'type' => 'version'
	),
	'cars' => array(
		'title' => 'Cars',
		'process' => 'xml,home,options,widgets,sliders',
		'type' => 'version'
	),
	'hero-slider' => array(
		'title' => 'Hero slider',
		'process' => 'xml,home,options,widgets,sliders',
		'type' => 'version'
	),
	'watches' => array(
		'title' => 'Watches store',
		'process' => 'xml,home,options,widgets,sliders',
		'type' => 'version'
	),
	'flowers' => array(
		'title' => 'Flowers store',
		'process' => 'xml,home,options,widgets,sliders',
		'type' => 'version'
	),
) );