<?php
/*
Plugin Name: Moonsign Calculator
Plugin URI:  http://link to your plugin homepage
Description: Moonsign Calculator
Version:     1.0
Author:      Slavisa Perisic
Author URI:  http://cerseilabs.com
License:     GPL2 etc
License URI: http://cerseilabs.com
*/

function moonsign_calculator_register_settings()
{
    $zodiac = ['Aquarius', 'Pisces', 'Aries', 'Taurus', 'Gemini', 'Cancer', 'Leo', 'Virgo', 'Libra', 'Scorpio', 'Sagittarius', 'Capricorn'];

    foreach ($zodiac as $item) {
        register_setting('moonsign_calculator_options_group', 'moonsign_calculator_' . strtolower($item));
    }

    register_setting('moonsign_calculator_options_group', 'moonsign_calculator_api_url');
    register_setting('moonsign_calculator_options_group', 'moonsign_calculator_heading_size');
    register_setting('moonsign_calculator_options_group', 'moonsign_calculator_heading_position');
    register_setting('moonsign_calculator_options_group', 'moonsign_calculator_heading_margin_bottom');
    register_setting('moonsign_calculator_options_group', 'moonsign_calculator_heading_margin_top');
    register_setting('moonsign_calculator_options_group', 'moonsign_calculator_heading_button_color');
    register_setting('moonsign_calculator_options_group', 'moonsign_calculator_heading_button_padding');
    register_setting('moonsign_calculator_options_group', 'moonsign_calculator_heading_color');
}

add_action('admin_init', 'moonsign_calculator_register_settings');

function moonsign_calculator_register_options_page()
{
    add_options_page('Moonsign Calculator', 'Moonsign Calculator', 'manage_options', 'moonsign_calculator', 'moonsign_calculator_options_page');
}

add_action('admin_menu', 'moonsign_calculator_register_options_page');

function moonsign_calculator_options_page()
{
    ?>
    <div>
        <?php screen_icon(); ?>

        <h2>Moonsign Calculator</h2>

        <form method="post" action="options.php">

            <?php settings_fields('moonsign_calculator_options_group'); ?>

            <h3>Pair horoscope signs with posts</h3>

            <p>It's simple</p>

            <table>
                <?php

                $zodiac = ['Aquarius', 'Pisces', 'Aries', 'Taurus', 'Gemini', 'Cancer', 'Leo', 'Virgo', 'Libra', 'Scorpio', 'Sagittarius', 'Capricorn'];
                $posts = new WP_Query(['post_type' => ['post', 'page'], 'showposts' => -1]);

                if (!$posts->have_posts()) {
                    echo "There are no posts";
                } else {
                    foreach ($zodiac as $sign) {
                        ?>

                        <tr valign="top">
                            <th scope="row"><label
                                        for="moonsign_calculator_<?php echo strtolower($sign); ?>>"><?php echo $sign; ?></label>
                            </th>
                            <td>
                                <select
                                        id="moonsign_calculator_<?php echo strtolower($sign) ?>"
                                        name="moonsign_calculator_<?php echo strtolower($sign) ?>"
                                >
                                    <?php
                                    while ($posts->have_posts()) {
                                        $posts->the_post();
                                        ?>
                                        <option value="<?php the_ID(); ?>" <?php selected(get_option('moonsign_calculator_' . strtolower($sign)), get_the_ID()); ?>><?php the_title() ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <?php
                    }
                }
                ?>
                <tr>
                    <th scope="row"><label
                                for="moonsign_calculator_api_url">API Url for sign recognition</label>
                    </th>
                    <td>
                        <input
                                placeholder="Example: https://slavisa.co.uk/get-sign"
                                type="text"
                                name="moonsign_calculator_api_url"
                                id="moonsign_calculator_api_url"
                                style="width:100%;"
                                value="<?php echo get_option('moonsign_calculator_api_url') ? get_option('moonsign_calculator_api_url') : ''; ?>"
                        >
                    </td>
                </tr>
                <tr>
                    <td>Heading settings</td>
                </tr>
                <tr>
                    <th scope="row"><label
                                for="moonsign_calculator_heading_size">Font size</label>
                    </th>
                    <td>
                        <input
                                placeholder="default: 32"
                                type="text"
                                name="moonsign_calculator_heading_size"
                                id="moonsign_calculator_heading_size"
                                style="width:100%;"
                                value="<?php echo get_option('moonsign_calculator_heading_size') ? get_option('moonsign_calculator_heading_size') : ''; ?>"
                        >
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label
                                for="moonsign_calculator_heading_position">Horizontal position</label>
                    </th>
                    <td>
                        <input
                                placeholder="default: left"
                                type="text"
                                name="moonsign_calculator_heading_position"
                                id="moonsign_calculator_heading_position"
                                style="width:100%;"
                                value="<?php echo get_option('moonsign_calculator_heading_position') ? get_option('moonsign_calculator_heading_position') : ''; ?>"
                        >
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label
                                for="moonsign_calculator_heading_color">Color</label>
                    </th>
                    <td>
                        <input
                                placeholder="default: #333"
                                type="text"
                                name="moonsign_calculator_heading_color"
                                id="moonsign_calculator_heading_color"
                                style="width:100%;"
                                value="<?php echo get_option('moonsign_calculator_heading_color') ? get_option('moonsign_calculator_heading_color') : ''; ?>"
                        >
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label
                                for="moonsign_calculator_heading_margin_bottom">Margin bottom</label>
                    </th>
                    <td>
                        <input
                                placeholder="default: 12"
                                type="text"
                                name="moonsign_calculator_heading_margin_bottom"
                                id="moonsign_calculator_heading_margin_bottom"
                                style="width:100%;"
                                value="<?php echo get_option('moonsign_calculator_heading_margin_bottom') ? get_option('moonsign_calculator_heading_margin_bottom') : ''; ?>"
                        >
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label
                                for="moonsign_calculator_heading_margin_top">Margin top</label>
                    </th>
                    <td>
                        <input
                                placeholder="default: 25"
                                type="text"
                                name="moonsign_calculator_heading_margin_top"
                                id="moonsign_calculator_heading_margin_top"
                                style="width:100%;"
                                value="<?php echo get_option('moonsign_calculator_heading_margin_top') ? get_option('moonsign_calculator_heading_margin_top') : ''; ?>"
                        >
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label
                                for="moonsign_calculator_heading_button_color">Button color</label>
                    </th>
                    <td>
                        <input
                                placeholder="default: pink"
                                type="text"
                                name="moonsign_calculator_heading_button_color"
                                id="moonsign_calculator_heading_button_color"
                                style="width:100%;"
                                value="<?php echo get_option('moonsign_calculator_heading_button_color') ? get_option('moonsign_calculator_heading_button_color') : ''; ?>"
                        >
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label
                                for="moonsign_calculator_heading_button_padding">Button padding</label>
                    </th>
                    <td>
                        <input
                                placeholder="default: 3px 8px"
                                type="text"
                                name="moonsign_calculator_heading_button_padding"
                                id="moonsign_calculator_heading_button_padding"
                                style="width:100%;"
                                value="<?php echo get_option('moonsign_calculator_heading_button_padding') ? get_option('moonsign_calculator_heading_button_padding') : ''; ?>"
                        >
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

add_shortcode('moonsign', 'moonsign_calculator_shortcode');

function moonsign_calculator_shortcode($atts)
{
    $attributes = shortcode_atts([
        'redirect' => 1
    ], $atts);

    $redirect = $attributes['redirect'];

    wp_enqueue_style('moonsign-style');

    ob_start();
    ?>
    <script>redirect = <?php echo $redirect; ?>;</script>
    <?php
    echo buildForm();
    $content = ob_get_contents();
    ob_end_clean();
    return $content;

}

require 'moonsign-form.php';
require 'moonsign-ajax.php';
require 'moonsign-custom-posts.php';
require 'moonsign-shortcode-quiz.php';
require 'moonsign-adminpage-quiz.php';
require 'moonsign-metaboxes.php';
