<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

?>

<div class="thrive-no-products-placeholder">
	<div class="thrive-no-products-icon">
		<?php echo \Thrive_Utils::return_part( '/integrations/woocommerce/assets/no-products.svg' ); ?>
	</div>

	<p class="thrive-no-products-title">
		<?php echo esc_html__( 'No products available', THEME_DOMAIN ); ?>
	</p>

	<p>
		<?php echo esc_html__( 'Open your WordPress dashboard and go to WooCommerce > ', THEME_DOMAIN ); ?>
		<span class="thrive-no-products-blue-text">
			<?php echo esc_html__( 'Products.', THEME_DOMAIN ); ?>
		</span>
	</p>

	<p>
		<?php echo esc_html__( 'Then, click the ', THEME_DOMAIN ); ?>

		<span class="thrive-no-products-blue-text">
			<?php echo esc_html__( 'Add New', THEME_DOMAIN ); ?>
		</span>

		<?php echo esc_html__( ' button to create your first product.', THEME_DOMAIN ); ?>
	</p>

	<p>
		<?php echo esc_html__( 'Enter the name of the product, complete the description, and press Publish to make your product live.', THEME_DOMAIN ); ?>
	</p>
</div>
