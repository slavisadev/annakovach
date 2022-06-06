<?php

// /**
//  * @return string
//  * shortcode for "Add To Cart!" button on homepage
//  * only one button has '' after original link
//  */
// function clickbank_link_shortcode_handler() {
// 	ob_start();

// 	$purchaseLinkGenerated = 'http://1.annakovach.pay.clickbank.net/';

// 	if ( isset( $_GET['vtid'] ) ) {
// 		$purchaseLinkGenerated = urldecode( $purchaseLinkGenerated . '?vtid=' . $_GET['vtid'] );
// 	}
/* 	?>
//     <a class="purchaseLink" href="<?php echo $purchaseLinkGenerated; ?>"
//        style="text-align: center;display: inline-block;width: 100%">
//         <img alt="Order Button" class="orderButton"
//              src="<?php echo get_template_directory_uri(); ?>/img/order-button.png"/>
//     </a>
// 	<?php
*/ //	return ob_get_clean();
// }

// add_shortcode( 'clickbank_link', 'clickbank_link_shortcode_handler' );


/**
 * shortcode for "Add To Cart!" button on homepage
 * @return string
 */
function clickbank_link_shortcode_handler($atts)
{
    ob_start();
    $purchaseLinkGenerated = $atts['link'];
	
	if($atts['link'] == '') {
    	$purchaseLinkGenerated = get_field('purchase_link', 'option');
	}

    if (isset($_GET['vtid'])) {
        if (strpos($purchaseLinkGenerated, '?') !== -1) {
            $purchaseLinkGenerated = urldecode($purchaseLinkGenerated.'&vtid='.$_GET['vtid']);
        }
    }
	
    if (isset($_GET['hop'])) {
        if (strpos($purchaseLinkGenerated, '?') !== -1) {
            $purchaseLinkGenerated = urldecode($purchaseLinkGenerated.'&hop='.$_GET['hop']);
        }
    }

    ?>
  <a class="purchaseLink" href="<?php echo $purchaseLinkGenerated; ?>">
    <img
      class="orderButton"
      src="<?php the_field('order_image', 'option'); ?>"/>
  </a>
    <?php
    return ob_get_clean();
}

add_shortcode('clickbank_link', 'clickbank_link_shortcode_handler');

/**
 * @param $atts
 *
 * @return string
 * This is shortcode for displaying download button.
 */
function download_single_button_shortcode( $atts ) {
	ob_start();
	$linkToDownload = $atts['link'];

	if ( ! empty( $linkToDownload ) ) {
		echo '<a class="download-button" target="_blank" href="' . $linkToDownload . '">Download</a>';
	}

	return ob_get_clean();
}

add_shortcode( 'download-button', 'download_single_button_shortcode' );

/**
 * @param $atts
 *
 * @return string
 * This is shortcode for displaying download button with image. Link is required for button to be display.
 */
function download_button_shortcode( $atts ) {
	ob_start();
	$linkToDownload = $atts['link'];
	$image          = $atts['image'];
	$altTag         = $atts['alt'];
	$class          = $atts['class'];

	if ( ! empty( $linkToDownload ) ) {
		if ( empty( $altTag ) ) {
			$altTag = 'Anna Kovach';
		}
		if ( empty( $class ) ) {
			$class = 'bookCover';
		}

		if ( ! empty( $image ) ) {
			echo '<img class="' . $class . '" src="' . $image . '" alt="' . $altTag . '">';
		}
		echo '<a class="download" target="_blank" href="' . $linkToDownload . '">Download</a>';
	}

	return ob_get_clean();
}

add_shortcode( 'download', 'download_button_shortcode' );

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

/**
 * @return string
 * shortcode for "Claim For Free copy"
 */
function cheat_sheet_form_shortcode() {
	ob_start(); ?>
    <div class="form-section">
        <form method="post" action="https://www.aweber.com/scripts/addlead.pl">
            <div style="display: none;">
                <input type="hidden" name="meta_web_form_id" value="1843081213">
                <input type="hidden" name="meta_split_id" value="">
                <input type="hidden" name="listname" value="awlist4741024">
                <input type="hidden" name="redirect" value="<?php bloginfo('url'); ?>/cheat-sheet-confirmation" id="redirect_c4839e96279c37c6ca423ac1e34b4248">

                <input type="hidden" name="meta_adtracking" value="Zodiac_Seduction_Cheat_Sheet">
                <input type="hidden" name="meta_message" value="1">
                <input type="hidden" name="meta_required" value="name,email,custom Select Your Sign,custom Select His Sign">

                <input type="hidden" name="meta_tooltip" value="">
            </div>
            <div class="_form-content">
                <p>Claim Your Free Copy Here</p>
                <div class="label-input">
                    <label for="awf_field-91811642">First name: </label>
                    <input required="" id="awf_field-91811642" type="text" name="name" value="" tabindex="500" onfocus=" if (this.value == '') { this.value = ''; }" onblur="if (this.value == '') { this.value='';} " placeholder="Enter your first name">
                </div>
                <div class="label-input">
                    <label for="awf_field-91811643">Email: </label>
                    <input required="" id="awf_field-91811643" type="text" name="email" value="" tabindex="500" onfocus=" if (this.value == '') { this.value = ''; }" onblur="if (this.value == '') { this.value='';} " placeholder="Enter your email address">
                </div>
                <div class="label-input">
                    <label for="awf_field-91811644">What's YOUR sign? </label>
                    <select required="" id="awf_field-91811644" name="custom Select Your Sign" tabindex="502">
                        <option disabled="" selected="" value="">Click to select your sign</option>
                        <option class="multiChoice" value="Aries">Aries</option>
                        <option class="multiChoice" value="Taurus">Taurus</option>
                        <option class="multiChoice" value="Gemini">Gemini</option>
                        <option class="multiChoice" value="Cancer">Cancer</option>
                        <option class="multiChoice" value="Leo">Leo</option>
                        <option class="multiChoice" value="Virgo">Virgo</option>
                        <option class="multiChoice" value="Libra">Libra</option>
                        <option class="multiChoice" value="Scorpio">Scorpio</option>
                        <option class="multiChoice" value="Sagittarius">Sagittarius</option>
                        <option class="multiChoice" value="Capricorn">Capricorn</option>
                        <option class="multiChoice" value="Aquarius">Aquarius</option>
                        <option class="multiChoice" value="Pisces">Pisces</option>
                    </select>
                </div>
                <div class="label-input">
                    <label for="awf_field-91811645">What's HIS sign? </label>
                    <select required="" id="awf_field-91811645" name="custom Select His Sign" tabindex="502">
                        <option disabled="" selected="" value="">Click to select his sign</option>
                        <option class="multiChoice" value="Aries">Aries</option>
                        <option class="multiChoice" value="Taurus">Taurus</option>
                        <option class="multiChoice" value="Gemini">Gemini</option>
                        <option class="multiChoice" value="Cancer">Cancer</option>
                        <option class="multiChoice" value="Leo">Leo</option>
                        <option class="multiChoice" value="Virgo">Virgo</option>
                        <option class="multiChoice" value="Libra">Libra</option>
                        <option class="multiChoice" value="Scorpio">Scorpio</option>
                        <option class="multiChoice" value="Sagittarius">Sagittarius</option>
                        <option class="multiChoice" value="Capricorn">Capricorn</option>
                        <option class="multiChoice" value="Aquarius">Aquarius</option>
                        <option class="multiChoice" value="Pisces">Pisces</option>
                    </select>
                </div>

                <button onclick="if(document.getElementById('agree').checked !== true) {return false}" name="submit"
                        class="button" id="af-submit-image-1843081213" tabindex="501">SEND ME MY COPY
                </button>
                <small>
                    <b> <span class="agree_checkbox"><input type="checkbox" id="agree" checked></span>
                        By clicking the button above, you agree to the <a href="/terms-of-use">Terms and Conditions</a>
                        of Anna Kovach LTD. Your
                        safety is our first priority. We won't disclose your Name & Email under any circumstances. You
                        won't receive any promotional or spam mail from Third-Party.
                    </b>
                </small>
            </div>
        </form>
    </div>
	<?php
	return ob_get_clean();
}

add_shortcode( 'cheat_sheet_form', 'cheat_sheet_form_shortcode' );

/**
 * @return string
 */
function search_blog_shortcode_function() {
	ob_start();
	?>

    <div class="widget search-form" id="thesis-search-widget-2">
        <p class="widget_title">SEARCH BLOG</p>
        <form class="search_form" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
            <p>
                <input class="input_text" type="text" id="s" name="s" value=""
                       onfocus="if (this.value == '') {this.value = '';}"
                       onblur="if (this.value == '') {this.value = '';}">
                <input type="submit" id="searchsubmit" value="Search">
            </p>
        </form>
    </div>
	<?php

	return ob_get_clean();
}

add_shortcode( 'search_blog_shortcode', 'search_blog_shortcode_function' );