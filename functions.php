<?php
/**
 * Author: Future Media 
 * URL: http://futuremedia.gr
 *
 * @package atres
 * @since 	atres 1.0
 *
 */

// LOAD CORE 
require_once( 'core/enque.php' );
require_once( 'core/acf-fields.php' ); // custom fields
require_once( 'core/extras.php' );
require_once( 'core/search-queries.php' );
require_once( 'core/admin.php' ); // Admin customization


// LAUNCH THEME
function theme_start() {

	load_theme_textdomain( 'fmedia', get_template_directory() . '/languages/' );  // let's get language support going, if you need it

	add_editor_style( get_stylesheet_directory_uri() . '/assets/css/editor-style.css' ); //Allow editor style.
	
	add_action( 'init', 'fmedia_head_cleanup' );                                  // launching operation cleanup
	
	add_filter( 'the_generator', 'fmedia_rss_version' );                          // remove WP version from RSS
	add_filter( 'wp_head', 'fmedia_remove_wp_widget_recent_comments_style', 1 );  // remove pesky injected css for recent comments widget
	add_action( 'wp_head', 'fmedia_remove_recent_comments_style', 1 );            // clean up comment styles in the head
	add_filter( 'gallery_style', 'fmedia_gallery_style' );                        // clean up gallery output in wp

	add_filter( 'wp_title', 'rw_title', 10, 3 );                                  // A better title
	add_action( 'wp_enqueue_scripts', 'fmedia_scripts_and_styles', 999 );         // // enqueue base scripts and styles
	// ie conditional wrapper

	fmedia_theme_support();														  // launching this stuff after theme setup

	add_action( 'widgets_init', 'fmedia_register_sidebars' );                     // adding sidebars to Wordpress (these are created in functions.php)
	add_filter( 'the_content', 'fmedia_filter_ptags_on_images' );                 // cleaning up random code around images
	add_filter( 'excerpt_more', 'fmedia_excerpt_more' );                          // cleaning up excerpt

}

add_action( 'after_setup_theme', 'theme_start' );


// REMOVE EMOJI CLUTTER
// http://wordpress.stackexchange.com/questions/185577/disable-emojicons-introduced-with-wp-4-2
function disable_wp_emojicons() {

  // all actions related to emojis
  remove_action( 'admin_print_styles', 'print_emoji_styles' );
  remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
  remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
  remove_action( 'wp_print_styles', 'print_emoji_styles' );
  remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
  remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
  remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );

  add_filter( 'tiny_mce_plugins', 'disable_emojicons_tinymce' ); // filter to remove TinyMCE emojis
  add_filter( 'emoji_svg_url', '__return_false' ); // remove emoji prefetch
}
add_action( 'init', 'disable_wp_emojicons' );

// THEME SUPPORT
function fmedia_theme_support() {
	add_theme_support( 'post-thumbnails' ); 	// wp thumbnails (sizes handled in functions.php)
	set_post_thumbnail_size(300, 300, true); 	// default thumb size
	add_theme_support('automatic-feed-links');	// rss thingy

	// adding post format support
	add_theme_support( 'post-formats',
		array(
			'gallery',           // gallery of images
			'image',             // an image
			'quote',             // a quick quote
			'video',             // video
		)
	);

	add_theme_support( 'menus' ); // wp menus

	// Enable support for HTML5 markup.
	$supports = array(
		'search-form',
		// 'comment-list',
		//'comment-form'
	);

add_theme_support( 'html5', $supports );

	register_nav_menus(
		array(
			'main-nav' 		=> __( 'Top Menu', 'fmedia' ),   		// main nav in header
			'mobile-nav' 	=> __( 'Mobile Menu', 'fmedia' ),    	// The hidden mobile menu
			'footer-nav' 	=> __( 'Footer Links', 'fmedia' ) 		// secondary nav in footer
		)
	);
} 


// OEMBED SIZE 
if ( ! isset( $content_width ) ) {
	$content_width = 640;
}

// THUMBNAIL SIZES
add_image_size( 'prop-widget', 240, 151, array( 'center', 'center' ) );
add_image_size( 'thumb-300', 300, 300, array( 'center', 'center' ) ); 
add_image_size( 'thumb-600', 600, 600, array( 'center', 'center' ) );
add_image_size( 'xlarge', 1240 );

// ACESS THUMBNAIL SIZES FROM MEDIA MANAGER 
/*
The function above adds the ability to use the dropdown menu to select
the new images sizes you have just created from within the media manager
when you add media to your content blocks. If you add more image sizes,
duplicate one of the lines in the array and name it according to your
new image size.
*/

add_filter( 'image_size_names_choose', 'fmedia_custom_image_sizes' );

function fmedia_custom_image_sizes( $sizes ) {
	return array_merge( $sizes, array(
		'prop-widget' 	=> __('240px by 151px'),
		'thumb-300' 	=> __('300px by 300px'),
		'thumb-600' 	=> __('600px by 600px'),
		'xlarge' 		=> __('1240px min width'),
	) );
}


// ACTIVE SIDEBARS 
// Sidebars & Widgetizes Areas
function fmedia_register_sidebars() {
	
	register_sidebar(array(
		'id' => 'sidebar1',
		'name' => __( 'Sidebar 1', 'fmedia' ),
		'description' => __( 'The first (primary) sidebar.', 'fmedia' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4 class="widgettitle">',
		'after_title' => '</h4>',
	));
}


// ACF OPTION PAGE
if( function_exists('acf_add_options_page') ) {

	$opt_args = array(
		'page_title' 	=> 'Contact Info',
		'menu_title'	=> 'Contact Info',
		'menu_slug' 	=> 'fmedia-contact-info',
		'capability'	=> 'edit_posts',
		'position' 		=> '6.5', // '50.5' false
		// 'parent_slug' 	=> '',
		'icon_url' 		=> 'dashicons-email',
		// 'redirect'		=> true
	);

	acf_add_options_page( $opt_args );	
}
