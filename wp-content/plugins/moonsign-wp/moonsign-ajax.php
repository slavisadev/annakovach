<?php

function moonsign_ajax_enqueue()
{
    // Enqueue javascript on the frontend.
    wp_enqueue_script(
        'moonsign-ajax-script',
        plugins_url('/js/moonsign-ajax-script.js', __FILE__),
        ['jquery']
    );

    // The wp_localize_script allows us to output the ajax_url path for our script to use.
    wp_localize_script(
        'moonsign-ajax-script',
        'moonsign_ajax_obj',
        [
            'ajaxurl'          => admin_url('admin-ajax.php'),
            'signs'            => getSigns(),
            'moonsign_api_url' => get_option('moonsign_calculator_api_url'),
        ]
    );

    wp_register_style(
        'moonsign-style',
        plugins_url('/css/moonsign-style.css', __FILE__)
    );

    // Enqueue javascript on the frontend.
    wp_enqueue_script(
        'moonsign-ajax-quiz',
        plugins_url('/js/moonsign-ajax-quiz.js', __FILE__),
        ['jquery']
    );
}

add_action('wp_enqueue_scripts', 'moonsign_ajax_enqueue');

function moonsign_ajax_request()
{
    if (isset($_REQUEST)) {
        $sign = $_REQUEST['sign'];
        $postId = get_option('moonsign_calculator_' . strtolower($sign));

        $post = get_post($postId);
        $content = apply_filters('the_content', $post->post_content);
        echo $content;
    }

    // Always die in functions echoing ajax content
    die();
}

add_action('wp_ajax_moonsign_ajax_request', 'moonsign_ajax_request');
add_action('wp_ajax_nopriv_moonsign_ajax_request', 'moonsign_ajax_request');

function getSigns()
{
    $zodiac = ['Aquarius', 'Pisces', 'Aries', 'Taurus', 'Gemini', 'Cancer', 'Leo', 'Virgo', 'Libra', 'Scorpio', 'Sagittarius', 'Capricorn'];
    $signs = [];
    foreach ($zodiac as $item) {
        $postId = get_option('moonsign_calculator_' . strtolower($item));
        $signs[strtolower($item)] = get_permalink($postId);
    }
    return $signs;
}