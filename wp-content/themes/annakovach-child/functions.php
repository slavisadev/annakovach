<?php
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );
define('CHILDTHEMEURL', get_stylesheet_directory_uri() . '/');

function my_theme_enqueue_styles() {

    $parent_style = 'parent-style';

    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        wp_get_theme()->get('Version')
    );
}

add_action('wp_enqueue_scripts', 'enqueue_childtheme_scripts', 100);

function enqueue_childtheme_scripts()
{
    if (!wp_script_is('google-map', $list = 'enqueued')) {
        wp_deregister_script('google-map');
        wp_dequeue_script('google-map');;
        wp_enqueue_script('google-map', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyChssKOKL5TMiFghN6i3FRf_p5-8O2_cEY&libraries=places', null, null, true);
    }

    function wpmix_get_version() {
        $theme_data = wp_get_theme();
        return $theme_data->Version;
    }
    $theme_version = wpmix_get_version();

    function wpmix_get_random() {
        $randomizr = '-' . rand(100,999);
        return $randomizr;
    }
    $random_number = wpmix_get_random();

    wp_enqueue_script('handlebars.js', CHILDTHEMEURL . 'js/handlebars.js', null, $theme_version . $random_number, true);

    if (is_page(array('free-birth-chart','love-forecast'))) {
        wp_enqueue_script('script.js', CHILDTHEMEURL . 'js/f-script.js', null, $theme_version . $random_number, true);
    }
    if (is_page(array('get-your-reading'))) {
        wp_enqueue_script('horoscope.js', CHILDTHEMEURL . 'js/horoscope.js', null, $theme_version . $random_number, true);
    }
    if (is_page(array('get-your-love-forecast-reading'))) {
        wp_enqueue_script('love-forecast.js', CHILDTHEMEURL . 'js/love-forecast.js', null, $theme_version . $random_number, true);
    }


}


?>