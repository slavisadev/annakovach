<?php

/**
 * @return string
 */
function get_body_data()
{
    if (
        !is_page('Blog') &&
        !is_single() &&
        !is_search() &&
        !is_page('30 Secrets') &&
        !is_page('30 Secrets Confirmation') &&
        !is_page('30 Secrets Thank You')
    ) {
        return "style=\"
        background-image: url(".get_field('website_background', 'option').");
        background-repeat:  no-repeat;
        background-attachment: fixed;
        background-size: cover\"";
    }
}

if (!function_exists('yqhoro_theme_setup')) :

    function yqhoro_theme_setup()
    {
        $prefix = HORO_NAMESPACE_SMALL;

        load_theme_textdomain('yqhorotheme', get_template_directory().'/languages');

        add_theme_support('automatic-feed-links');

        add_theme_support('title-tag');

        add_theme_support('post-thumbnails');
        register_nav_menus([
            'menu-1' => esc_html__('Primary', $prefix.'mansecrets'),
        ]);

        add_theme_support('html5', [
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
        ]);

        add_theme_support('custom-background', apply_filters($prefix.'mansecrets_custom_background_args', [
            'default-color' => 'ffffff',
            'default-image' => '',
        ]));

        add_theme_support('customize-selective-refresh-widgets');

        add_theme_support('custom-logo', [
            'height'      => 250,
            'width'       => 250,
            'flex-width'  => true,
            'flex-height' => true,
        ]);
    }
endif;

add_action('after_setup_theme', 'yqhoro_theme_setup');

function content_width()
{
    $_GLOBALS['content_width'] = apply_filters('content_width', 640);
}

add_action('after_setup_theme', 'content_width', 0);

function yqhoro_widgets_init()
{
    register_sidebar([
        'name'          => esc_html__('Sidebar', 'yqhorotheme'),
        'id'            => 'sidebar-1',
        'description'   => esc_html__('Add widgets here.', 'yqhorotheme'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<p class="widget_title">',
        'after_title'   => '</p>',
    ]);
}

add_action('widgets_init', 'yqhoro_widgets_init');

/**
 * Enqueue scripts and styles.
 */
function yqhoro_scripts()
{
    $prefix = HORO_NAMESPACE_SMALL;

    wp_register_script('modernizejs', get_template_directory_uri().'/js/modernizr-2.8.3.min.js', ['jquery'], '', false);
    wp_register_script($prefix.'mansecrets-datepicker', get_template_directory_uri().'/js/datepicker.js', ['jquery'], '', true);
    wp_register_script($prefix.'mansecrets-datetimepicker', get_template_directory_uri().'/js/datetimepicker.js', ['jquery'], '', true);
    wp_register_script($prefix.'mansecrets-validate', get_template_directory_uri().'/js/validate.js', ['jquery'], '', true);
    wp_register_script(
        $prefix.'mansecrets-main-js',
        get_template_directory_uri().'/js/main.js',
        [
            'jquery',
            $prefix.'mansecrets-datepicker',
            $prefix.'mansecrets-validate',
        ],
        version_id(),
        true
    );

    wp_register_script($prefix.'mansecrets-blogstyle-js', get_template_directory_uri().'/js/app.min.js', ['jquery'], version_id(), true);

    wp_register_script('custom-clickbank-block', get_template_directory_uri().'/js/clickbank.js', ['jquery'], version_id(), true);
    wp_localize_script($prefix.'mansecrets-main-js', 'globalWpJavascriptObject',
        [
            'ajax_url'      => admin_url('admin-ajax.php'),
            'clickbank_url' => get_field('purchase_link', 'option'),
        ]
    );

    wp_register_style('allpages-30-secrets', get_template_directory_uri().'/css/30-secrets.css');
    wp_register_style($prefix.'mansecrets-blog-css', get_template_directory_uri().'/css/blog.css');
    wp_register_style($prefix.'mansecrets-blogstyle-css', get_template_directory_uri().'/css/blogstyle.css');
    wp_register_style($prefix.'mansecrets-main-css', get_template_directory_uri().'/css/main.css');
    wp_register_style($prefix.'mansecrets-datetimepicker-css', get_template_directory_uri().'/css/datetimepicker.css');

    if (is_page('30 Secrets') || is_page('30 Secrets Confirmation') || is_page('30 Secrets Thank You')) {
        wp_enqueue_style('allpages-30-secrets');
        wp_enqueue_script($prefix.'mansecrets-modernize-js');
    } elseif (is_page('Special offer')) {
        wp_enqueue_style($prefix.'mansecrets-datetimepicker-css');
        wp_enqueue_style($prefix.'mansecrets-main-css');
        wp_enqueue_script($prefix.'mansecrets-datepicker');
        wp_enqueue_script($prefix.'mansecrets-validate');
        wp_enqueue_script($prefix.'mansecrets-modernize-js');
        wp_enqueue_script($prefix.'mansecrets-form-js');
        wp_enqueue_script($prefix.'mansecrets-main-js');

    } elseif (is_page('Blog') || is_single() || is_search() || is_tag()) {
        wp_enqueue_style($prefix.'mansecrets-blogstyle-css');
        wp_enqueue_script($prefix.'mansecrets-blogstyle-js');
    } else {
        wp_enqueue_style($prefix.'mansecrets-main-css');

        wp_enqueue_script($prefix.'mansecrets-modernize-js');
        wp_enqueue_script($prefix.'mansecrets-main-js');
    }
    if (
        !is_page('Blog')
        && !is_single()
        && !is_search()
    ) {
        wp_enqueue_script('custom-clickbank-block');
    }

    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }

}

add_action('wp_enqueue_scripts', 'yqhoro_scripts');

define('VERSION', '1.1');

function version_id()
{
    if (WP_DEBUG) {
        return time();
    }
    return VERSION;
}

/**
 * Add New Image Size for Blog
 */
add_image_size('blog_featured', '673');
add_image_size('blog_featured_long', '760');
add_image_size('blog_featured_square', 360, 425, true);
add_image_size('blog_featured_regular', 360, 270, true);
add_image_size('full_width', 780);

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
        case 'Affiliates':
            $pageSlug = 'affiliates';
            break;
        case 'Thank You for Your Purchase':
            $pageSlug = 'thankYou';
            break;
        case 'Privacy policy':
            $pageSlug = 'privacy';
            break;
        case 'Special offer thank you for your purchase':
            $pageSlug = 'thankYou';
            break;
        default:
            $pageSlug = '';
    }

    return $pageSlug;
}


function enhance_body_class($classes)
{
    is_page('Blog') || is_single() || is_search() ? $blogClass = 'template-single' : $blogClass = '';
    $classes[] = $blogClass;

    return $classes;
}

add_filter('body_class', 'enhance_body_class');

/**
 * remove [...] from post excerpt
 * @return string
 */
function new_excerpt_more()
{
    return '...';
}

add_filter('excerpt_more', 'new_excerpt_more');


include 'inc/shortcodes.php';
include 'inc/ajax.php';
include 'inc/widgets.php';
