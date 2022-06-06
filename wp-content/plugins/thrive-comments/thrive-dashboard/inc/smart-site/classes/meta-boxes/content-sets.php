<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-dashboard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}
?>
<div id="tvd-contents-sets">
	<?php wp_nonce_field( TVD_Content_Sets::META_BOX_NONCE, 'tvd_content_sets_meta_box' ); ?>

    <p>
        <?php echo sprintf( __( 'If this post has been added to a content set by matching a dynamic rule, the content set will not be displayed here. View your content set rules from %s.', TVE_DASH_TRANSLATE_DOMAIN ),
            '<strong>' . __( 'Thrive Dashboard > Smart Site', TVE_DASH_TRANSLATE_DOMAIN ) . '</strong>' ); ?>
    </p>

	<p>
        <?php echo esc_html__( 'This post has been added directly to the following content sets:', TVE_DASH_TRANSLATE_DOMAIN ); ?>
    </p>

	<div>
		<input placeholder="<?php echo esc_html__( 'Search content sets', TVE_DASH_TRANSLATE_DOMAIN ) ?>" id="tvd-content-sets-autocomplete"/>
	</div>
	<div id="tvd-matched-content-sets"></div>
</div>
