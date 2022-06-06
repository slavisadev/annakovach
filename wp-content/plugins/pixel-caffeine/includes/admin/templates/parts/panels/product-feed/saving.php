<?php
/**
 * The product catalog saving status template
 *
 * @var AEPC_Admin_View $page
 * @var string $action
 * @var ProductCatalogManager $product_catalog
 *
 * @package Pixel Caffeine
 */

use PixelCaffeine\ProductCatalog\ProductCatalogManager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( empty( $product_catalog ) ) {
	return;
}

?>

<div class="panel panel-feed-info panel-feed-saving form-horizontal js-product-feed-info updating">
	<div class="panel-heading">
		<h2 class="tit"><?php esc_html_e( 'Generate Product Feed', 'pixel-caffeine' ); ?></h2>
	</div>

	<div class="panel-body">
		<?php esc_html_e( 'Saving...', 'pixel-caffeine' ); ?>
	</div>
</div>
