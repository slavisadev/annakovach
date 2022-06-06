<?php

/**
 * shortcode for "Add To Cart!" button on homepage
 * @return string
 */
function clickbank_link_shortcode_handler()
{
    ob_start();

    $purchaseLinkGenerated = get_field('purchase_link', 'option');

    if (isset($_GET['vtid'])) {
        if (strpos($purchaseLinkGenerated, '?') !== -1) {
            $purchaseLinkGenerated = urldecode($purchaseLinkGenerated.'&vtid='.$_GET['vtid']);
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
function download_single_button_shortcode($atts)
{
    ob_start();
    $linkToDownload = $atts['link'];

    if (!empty($linkToDownload)) {

        if (isset($_GET['iv'])) {
            echo '<a class="download" target="_blank" href="'.$linkToDownload.'">Download</a>';
        } else {
            echo '<p>Download link will appear here once you complete the payment.</p>';
        }
    }

    return ob_get_clean();
}

add_shortcode('download-button', 'download_single_button_shortcode');

/**
 * @return string
 * This is shortcode for Add To My Order Button.
 */
function big_payment_button_shortcode()
{
    ob_start();

    $link = get_field('purchase_link', 'option');

    $imageLink = get_template_directory_uri().'/img/payment-methods.png';
    echo '<a class="addToMyOrder" href="'.$link.'">ADD TO MY ORDER</a>';
    echo '<img class="paymentMethods" src="'.$imageLink.'" alt="Payment Methods">';

    return ob_get_clean();
}

add_shortcode('add-to-my-order', 'big_payment_button_shortcode');

function download_button_shortcode($atts)
{
    ob_start();
    $linkToDownload = $atts['link'];
    $image          = $atts['image'];
    $altTag         = $atts['alt'];
    $class          = $atts['class'];

    if (!empty($linkToDownload)) {
        if (empty($altTag)) {
            $altTag = HORO_NAMESPACE_CAP.' Man Secrets';
        }
        if (empty($class)) {
            $class = 'bookCover';
        }

        if (!empty($image)) {
            echo '<img class="'.$class.'" src="'.$image.'" alt="'.$altTag.'">';
        }

        if (isset($_GET['iv'])) {
            echo '<a class="download" target="_blank" href="/downloader.php?file='.$linkToDownload.'">Download</a>';
        } else {
            echo '<p>Download link will appear here once you complete the payment.</p>';
        }
    }

    return ob_get_clean();
}

add_shortcode('download', 'download_button_shortcode');

/**
 * @return string
 * This is shortcode for Add To Cart on "30 Secrets Thank You" page.
 */
function add_to_my_card_payment_button_shortcode()
{
    ob_start();

    $link      = get_title('purchase_link', 'option').'/?vtid=typ1';
    $imageLink = get_template_directory_uri().'/img/payment-methods.png';
    echo '<a class="add-to-cart-button" href="'.$link.'">Add To Cart</a>';
    echo '<img class="payment-methods" src="'.$imageLink.'" alt="Payment Methods">';

    return ob_get_clean();
}

add_shortcode('add-to-cart-button', 'add_to_my_card_payment_button_shortcode');

/**
 * @return string
 */
function yqhoro_special_offer_form_shortcode()
{
    ob_start();

    ?>
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
        it. Just don't forget to enter your <i>correct email address</i> and click ''ADD TO MY ORDER'' once
        you're done.</p>
      <p><b>NOTE:</b> <i>Your Cosmo Compatibility Reading Package comes via email in PDF format, within 48 hours,
          at a one-time charge of just $97.</i></p>
      <input class="button" type="submit" value="ADD TO MY ORDER">
    </form>
    <p style="font-size: 16px; text-align: center;"><b><a href="<?php the_field('purchase_link', 'option') ?>/?cbur=d">NO
          THANKS, I DON'T WANT A READING DONE AND I'LL GIVE MY SPOT TO SOMEONE ELSE. TAKE ME TO
          MY <?php echo HORO_NAMESPACE_BIG ?> MAN
          SECRETS BOOKS.</a></b></p>
  </div>
    <?php
    return ob_get_clean();
}

add_shortcode('special_offer_form', 'yqhoro_special_offer_form_shortcode');
/**
 * @return string
 */
function yqhoro_thank_you_for_your_purchase_shortcode()
{
    ob_start();
    ?>
  <div class="form-section">
    <form action="https://www.aweber.com/scripts/addlead.pl" method="post">
      <div style="display: none;"><input name="meta_web_form_id" type="hidden" value="1493722293"/>
        <input name="meta_split_id" type="hidden" value=""/>
        <input name="listname" type="hidden" value="awlist4721833"/>
        <input id="redirect_e4d2470b7e4dcc3c14e83e8755d40c0c" name="redirect" type="hidden"
               value="https://www.aweber.com/thankyou-coi.htm?m=text"/><input name="meta_adtracking"
                                                                              type="hidden"
                                                                              value="Anna's_<?php echo HORO_NAMESPACE_CAP ?>_Man_Secrets"/>
        <input name="meta_message" type="hidden" value="1"/>
        <input name="meta_required" type="hidden" value="email"/><input name="meta_tooltip" type="hidden"
                                                                        value=""/></div>
      <div class="_form-content">
        <div class="label-input"><label for="awf_field-91693345">First name: </label><input
            id="awf_field-91693345" tabindex="500" name="name" required="" type="text" value=""
            placeholder="Enter your first name"/></div>
        <div class="label-input"><label for="awf_field-91693346">Email: </label><input id="awf_field-91693346"
                                                                                       tabindex="500"
                                                                                       name="email" required=""
                                                                                       type="text" value=""
                                                                                       placeholder="Enter your email address"/>
        </div>
        <button id="af-submit-image-1493722293" class="button" tabindex="501" name="submit">GIVE ME ACCESS
        </button>
      </div>
    </form>
  </div>
    <?php
    return ob_get_clean();
}

add_shortcode('thank_you_for_your_purchase', 'yqhoro_thank_you_for_your_purchase_shortcode');

/**
 * @return string
 * shortcode for "Claim For Free copy" on 30 secrets page
 */
function secrets_30_form_shortcode()
{
    ob_start();
    ?>
  <div class="form-section">
    <form method="post" action="https://www.aweber.com/scripts/addlead.pl">
      <div style="display: none;">
        <input type="hidden" name="meta_web_form_id" value="187371025"/>
        <input type="hidden" name="meta_split_id" value=""/>
        <input type="hidden" name="listname" value="awlist5183482"/>
        <input type="hidden" name="redirect" value="<?php bloginfo('url') ?>/30-secrets-confirmation"
               id="redirect_6079a9bb159d148d96eed29bd54100de"/>

        <input type="hidden" name="meta_adtracking" value="<?php echo HORO_NAMESPACE_CAP ?>_30_Secrets"/>
        <input type="hidden" name="meta_message" value="1"/>
        <input type="hidden" name="meta_required" value="name,email,custom Select your sign"/>

        <input type="hidden" name="meta_tooltip" value=""/>
      </div>
      <div class="_form-content">
        <p>Access Your FREE Copy Now!</p>
        <div class="label-input">
          <label for="awf_field-99717578">First name: </label>
          <input required id="awf_field-99717578" type="text" name="name" value="" tabindex="500"
                 onfocus=" if (this.value == '') { this.value = ''; }" onblur="if (this.value == '') { this.value='';} "
                 placeholder="Enter your first name"/>
        </div>
        <div class="label-input">
          <label for="awf_field-99717579">Email: </label>
          <input required id="awf_field-99717579" type="text" name="email" value="" tabindex="501"
                 onfocus=" if (this.value == '') { this.value = ''; }" onblur="if (this.value == '') { this.value='';} "
                 placeholder="Enter your email address"/>
        </div>
        <div class="label-input">
          <label for="awf_field-99717580">What's your sign: </label>
          <select required id="awf_field-99717580" name="custom Select your sign" tabindex="502">
            <option disabled selected value>Click to select your sign</option>
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
        <button name="submit" class="button" id="af-submit-image-187371025" tabindex="503">SEND ME MY COPY</button>

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

add_shortcode('30_secrets_form', 'secrets_30_form_shortcode');


/**
 * @return string
 * This is shortcode for Affiliates page, section "Why promote my Capricorn Man Secrets product"
 */
function affiliates_shortcode()
{
    ob_start();
    $post_id = get_the_ID();
    if (have_rows('affiliates_repeater', $post_id)):
        echo '<h3>'.get_field('aff_title', $post_id).'</h3>';
        echo '<ul class="check">';
        while (have_rows('affiliates_repeater', $post_id)) : the_row();
            echo '<li>'.get_sub_field('item_name', $post_id).'</li>';
        endwhile;
        echo '</ul>';
    endif;

    return ob_get_clean();
}

add_shortcode('why_promote_my_cms_product', 'affiliates_shortcode');
