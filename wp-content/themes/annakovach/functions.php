<?php
/**
 * annakovach functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package annakovach
 */
if (!function_exists('annakovach_setup')) :
    /**
     * Sets up theme defaults and registers support for various WordPress features.
     *
     * Note that this function is hooked into the after_setup_theme hook, which
     * runs before the init hook. The init hook is too late for some features, such
     * as indicating support for post thumbnails.
     */
    function annakovach_setup()
    {

        load_theme_textdomain('annakovach', get_template_directory() . '/languages');

        add_theme_support('automatic-feed-links');
        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');
        register_nav_menus(array(
            'menu-1' => esc_html__('Primary', 'annakovach'),
        ));

        add_theme_support('html5', array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
        ));
        add_theme_support('customize-selective-refresh-widgets');
        add_theme_support('custom-logo', array(
            'height' => 250,
            'width' => 250,
            'flex-width' => true,
            'flex-height' => true,
        ));
    }
endif;




add_action('after_setup_theme', 'annakovach_setup');

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function annakovach_content_width()
{
    $_GLOBALS['content_width'] = apply_filters('annakovach_content_width', 640);
}

add_action('after_setup_theme', 'annakovach_content_width', 0);

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
/**

function annakovach_widgets_init()
{
    register_sidebar(array(
        'name' => esc_html__('Sidebar', 'annakovach'),
        'id' => 'sidebar-1',
        'description' => esc_html__('Add widgets here.', 'annakovach'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<p class="widget_title">',
        'after_title' => '</p>',
    ));
}

add_action('widgets_init', 'annakovach_widgets_init');
*/
function yqhoro_widgets_init()
{
       register_sidebar(array(
        'name' => esc_html__('Sidebar', 'annakovach'),
        'id' => 'sidebar-1',
        'description' => esc_html__('Add widgets here.', 'annakovach'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<p class="widget_title">',
        'after_title' => '</p>',
    ));
}

add_action('widgets_init', 'yqhoro_widgets_init');

/**
 * Enqueue scripts and styles.
 */
function annakovach_scripts()
{
    wp_register_script('modernizejs', get_template_directory_uri() . '/js/modernizr-2.8.3.min.js', ['jquery'], '', false);
    wp_register_script('annakovach-datepicker', get_template_directory_uri() . '/js/datepicker.js', ['jquery'], '', true);
    wp_register_script('annakovach-validate', get_template_directory_uri() . '/js/validate.js', ['jquery'], '', true);
    wp_register_script('annakovach-form-js', get_template_directory_uri() . '/js/form.js', ['jquery'], version_id(), true);
    wp_register_script('annakovach-main-js', get_template_directory_uri() . '/js/main.js', ['jquery'], version_id(), true);
    wp_localize_script('annakovach-main-js', 'globalWpJavascriptObject',
        ['ajax_url' => admin_url('admin-ajax.php')]
    );
	wp_register_script($prefix.'annakovach-blogstyle-js', get_template_directory_uri().'/js/app.min.js', ['jquery'], version_id(), true);
	
    wp_register_script('custom-clickbank-block', 'https://cbtb.clickbank.net/?vendor=annakovach');

    wp_register_style('allpages-cheat-sheet', get_template_directory_uri() . '/css/cheat-sheet.css');
    wp_register_style('annakovach-blog-css', get_template_directory_uri() . '/css/blog.css');
	wp_register_style('mansecrets-blogstyle-css', get_template_directory_uri().'/css/blogstyle.css');
    wp_register_style('annakovach-main-css', get_template_directory_uri() . '/css/main.css');
    wp_register_style('annakovach-datetimepicker-css', get_template_directory_uri() . '/css/datetimepicker.css');

    if (is_page('Cheat sheet') || is_page('Cheat sheet confirmation')) {
        wp_enqueue_style('allpages-cheat-sheet');
        wp_enqueue_script('annakovach-modernize-js');
    } elseif (is_page('Blog') || is_single() || is_search() || is_category() || is_tag() || is_author() || is_404()) {
        wp_enqueue_style('annakovach-blogstyle-css');
        wp_enqueue_script('annakovach-blogstyle-js');
		wp_enqueue_script('annakovach-main-js');
    } elseif (is_front_page()) {
        wp_enqueue_style('annakovach-datetimepicker-css');
        wp_enqueue_style('annakovach-main-css');
        wp_enqueue_script('annakovach-datepicker');
        wp_enqueue_script('annakovach-validate');
        wp_enqueue_script('annakovach-modernize-js');
        wp_enqueue_script('annakovach-main-js');
        wp_enqueue_script('annakovach-form-js');
    } else {
        wp_enqueue_style('annakovach-main-css');
        wp_enqueue_script('annakovach-modernize-js');
        wp_enqueue_script('annakovach-main-js');
    }

    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }

    if (
        !is_page('Blog')
        && !is_single()
        && !is_search()
	    && !is_category()
        && !is_tag()
        && !is_author()
        && !is_404()
    ) {
        wp_enqueue_script('custom-clickbank-block');
    }

}

define('VERSION', '1.1');
function version_id()
{
    if (WP_DEBUG)
        return time();
    return VERSION;
}

add_action('wp_enqueue_scripts', 'annakovach_scripts');

/**
 * Add New Image Size for Blog
 */
add_image_size('blog_featured', '673');

/**
 * Site options
 */
if (function_exists('acf_add_options_page')) {
    $args = ['page_title' => 'Site options'];
    acf_add_options_page($args);
}
/**
 * @param $mimes
 *
 * @return mixed
 * Allow SVG images to be uploaded!
 */
function cc_mime_types($mimes)
{
    $mimes['svg'] = 'image/svg+xml';

    return $mimes;
}

add_filter('upload_mimes', 'cc_mime_types');

/**
 * print page id for section block on page.php
 *
 * @param $pageName
 *
 * @return string
 */
function clbs_get_page_id($pageName)
{

    switch ($pageName) {
        case 'Terms of Use':
            $pageSlug = 'terms';
            break;
        case 'Contact':
            $pageSlug = 'contact';
            break;
        case 'Refund Policy':
            $pageSlug = 'refund';
            break;
        case 'Privacy Policy':
            $pageSlug = 'privacy';
            break;
	    case 'Thank You For Your Order!':
		    $pageSlug = 'thankYou';
		    break;

        default:
            $pageSlug = '';
    }

    return $pageSlug;
}

/**
 * remove [...] from post excerpt
 *
 * @return string
 */
function new_excerpt_more()
{
    return '...';
}

add_filter('excerpt_more', 'new_excerpt_more');

add_filter('body_class', 'enhance_body_class');

function enhance_body_class($classes)
{
    is_page('Blog') || is_single() || is_search() ? $blogClass = 'template-single' : $blogClass = '';
    $classes[] = $blogClass;
    return $classes;
}

include 'inc/shortcodes/shortcodes.php';
include 'inc/ajax.php';
include 'inc/widgets.php';