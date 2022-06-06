<?php

function yqhoro_child_enqueue_styles()
{
    $prefix = HORO_NAMESPACE_SMALL;
    wp_enqueue_style($prefix.'colors', get_stylesheet_directory_uri().'/css/colors.css');
    wp_enqueue_style($prefix.'testimonials', get_stylesheet_directory_uri().'/css/testimonials.css');
    wp_enqueue_style($prefix.'additional', get_stylesheet_directory_uri().'/css/additional.css');

    wp_register_style('annakovach-datetimepicker-css', get_template_directory_uri() . '/css/datetimepicker.css');
    wp_enqueue_style('annakovach-datetimepicker-css');
}

add_action('wp_enqueue_scripts', 'yqhoro_child_enqueue_styles', PHP_INT_MAX);


/**
 * @return string
 */
function kovach_form_shortcode() {
    ob_start(); ?>
    <div class="form-section">
        <form id="special-offer" action="" method="post" novalidate="novalidate">
            <div class="her-info">
                <div class="form-box">
                    <label for="her_email">Your Email Address</label>
                    <input type="email" id="her_email" name="her_email">
                </div>
                <div class="form-box">
                    <label for="her_name">Your Name</label>
                    <input type="text" id="her_name" name="her_name">
                </div>
                <div class="form-box">
                    <label for="her_date">Your Exact Date of Birth</label>
                    <input type="text" placeholder="mm/dd/yyyy" class="datepicker hasDatepicker" id="her_date"
                           name="her_date">
                </div>
                <div class="form-box">
                    <label for="her_place">Your Exact Place of Birth</label>
                    <input type="text" placeholder="Country, City" id="her_place" name="her_place">
                </div>
                <div class="form-box">
                    <label for="her_time">Your Exact Time of Birth</label>
                    <input class="timepicker" type="time" placeholder="Choose a time" id="her_time" name="her_time">
                </div>
            </div>
            <div class="his-info">
                <div class="form-box">
                    <label for="his_name">His Name</label>
                    <input type="text" id="his_name" name="his_name">
                </div>
                <div class="form-box">
                    <label for="his_date">His Exact Date of Birth</label>
                    <input type="text" placeholder="mm/dd/yyyy" class="datepicker hasDatepicker" id="his_date"
                           name="his_date">
                </div>
                <div class="form-box">
                    <label for="his_place">His Exact Place of Birth</label>
                    <input type="text" placeholder="Country, City" id="his_place" name="his_place">
                </div>
                <div class="form-box">
                    <label for="his_time">His Exact Time of Birth</label>
                    <input class="timepicker" type="time" placeholder="Choose a time" id="his_time" name="his_time">
                </div>
                <div class="form-box">
                    <label for="afternoon_morning">Not sure? Do you know if he was born in the morning or the
                        afternoon?</label>
                    <div style="    width: 30%;
    vertical-align: top;
    display: inline-block;" class="block">
                        <label style="    display: inline-block;vertical-align: top;" for="morning_">Morning</label>
                        <input style="    width: 16px;
    position: relative;
    top: -3px;" type="radio" id="morning_" name="afternoon_morning[]" value="Morning">
                    </div>
                    <div style="    width: 30%;
    vertical-align: top;
    display: inline-block;" class="block">
                        <label style="    display: inline-block;vertical-align: top;" for="afternoon_">Afternoon</label>
                        <input style="    width: 16px;
    position: relative;
    top: -3px;" type="radio" id="afternoon_" name="afternoon_morning[]" value="Afternoon">
                    </div>
                </div>
            </div>
            <div class="additional-info">
                <div class="form-box">
                    <label for="info_one">Tell me as much as you can about your relationship:</label>
                    <textarea name="message_1" id="info_one" rows="5"></textarea>
                </div>
                <div class="form-box">
                    <label for="info_two">What's your biggest concern and what would you like to know:</label>
                    <textarea name="message_2" id="info_two" rows="5"></textarea>
                </div>
            </div>
            <p>If you don't know <i>everything</i> that's OK, and if I need to know anything extra, I'll email you about
                it. Just don't forget to enter your <i>correct email address</i> and click ''ADD TO CART'' once you're
                done.</p>
            <p><b>NOTE:</b> <i>Your Cosmo Compatibility Reading Package comes via email in PDF format, within 48 hours,
                    at a one-time charge of just $149.</i></p>
            <input style="opacity:0.5" disabled class="button" type="submit" value="ADD TO CART">
            <img class="paymentMethods" src="<?php echo get_template_directory_uri(); ?>/img/payment-methods.png"
                 alt="Payment Methods">
        </form>
    </div>
    <?php
    return ob_get_clean();
}

add_shortcode( 'kovach_form', 'kovach_form_shortcode' );
