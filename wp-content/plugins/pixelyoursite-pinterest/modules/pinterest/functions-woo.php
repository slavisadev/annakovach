<?php

namespace PixelYourSite\Pinterest;

use PixelYourSite;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function getWooCustomAudiencesOptimizationParams( $post_id ) {
	
	$post = get_post( $post_id );
	
	$params = array(
		'content_name'  => '',
		'category_name' => '',
	);
	
	if ( ! $post ) {
		return $params;
	}
	
	if ( $post->post_type == 'product_variation' ) {
		$post_id = $post->post_parent; // get terms from parent
	}
	
	$params['content_name']  = $post->post_title;
	$params['category_name'] = implode( ', ', PixelYourSite\getObjectTerms( 'product_cat', $post_id ) );
	
	return $params;
	
}

function getWooSingleAddToCartParams( $product_id, $qty = 1, $is_external = false ) {
	
	$params = array(
		'post_type'        => 'product',
		'product_id'       => getWooProductContentId($product_id),
		'product_quantity' => $qty,
	);
	
	//@todo: track "product_variant_id"
	
	// content_name, category_name, tags
	$params['tags'] = implode( ', ', PixelYourSite\getObjectTerms( 'product_tag', $product_id ) );
	$params = array_merge( $params, getWooCustomAudiencesOptimizationParams( $product_id ) );
	
	// set option names
	$value_enabled_option = $is_external ? 'woo_affiliate_value_enabled' : 'woo_add_to_cart_value_enabled';
	$value_option_option  = $is_external ? 'woo_affiliate_value_option' : 'woo_add_to_cart_value_option';
	$value_global_option  = $is_external ? 'woo_affiliate_value_global' : 'woo_add_to_cart_value_global';
	$value_percent_option = $is_external ? '' : 'woo_add_to_cart_value_percent';
	
	// currency, value
	if ( PixelYourSite\PYS()->getOption( $value_enabled_option ) ) {

		$value_option   = PixelYourSite\PYS()->getOption( $value_option_option );
		$global_value   = PixelYourSite\PYS()->getOption( $value_global_option, 0 );
		$percents_value = PixelYourSite\PYS()->getOption( $value_percent_option, 100 );
		
		$params['value']    = PixelYourSite\getWooEventValue( $value_option, $global_value, $percents_value,$product_id,$qty );
		$params['currency'] = get_woocommerce_currency();
		
	}
	
	$params['product_price'] = PixelYourSite\getWooProductPriceToDisplay( $product_id );
	
	if ( $is_external ) {
		$params['action'] = 'affiliate button click';
	}
	
	return $params;
	
}

/**
 * @param PixelYourSite\SingleEvent $event
 */
function getWooEventCartSubtotal($event) {
    $subTotal = 0;
    $include_tax = get_option( 'woocommerce_tax_display_cart' ) == 'incl';
    foreach ($event->args['products'] as $product) {
        $subTotal += $product['subtotal'];
        if($include_tax) {
            $subTotal += $product['subtotal_tax'];
        }
    }
    return pinterest_round($subTotal);
}

/**
 * @param PixelYourSite\SingleEvent $event
 */
function getWooEventCartTotal($event) {

    return getWooEventCartSubtotal($event);
}
/**
 * @param PixelYourSite\SingleEvent $event
 */
function getWooEventOrderTotal( $event ) {

    if(PixelYourSite\PYS()->getOption( 'woo_event_value' ) != 'custom') {
        $total = 0;
        // $include_tax = get_option( 'woocommerce_tax_display_cart' ) == 'incl';
        foreach ($event->args['products'] as $product) {
            $total += $product['total'] + $product['total_tax'];
        }
        $total+=$event->args['shipping_cost'] + $event->args['shipping_tax'];
        return pinterest_round($total);
    }

    $include_tax = PixelYourSite\PYS()->getOption( 'woo_tax_option' ) == 'included' ? true : false;
    $include_shipping = PixelYourSite\PYS()->getOption( 'woo_shipping_option' ) == 'included' ? true : false;


    $total = 0;
    foreach ($event->args['products'] as $product) {
        $total += $product['total'];
        if($include_tax) {
            $total += $product['total_tax'];
        }
    }

    if($include_shipping) {
        $total += $event->args['shipping_cost'];
    }
    if($include_tax) {
        $total += $product['shipping_cost_tax'];
    }

    return pinterest_round($total );

}

/**
 * @param PixelYourSite\SingleEvent $event
 * @return array
 */
function getWooCartParams( $event ) {
	
	$params = array(
		'post_type' => 'product',
	);
	
	$line_items = array();
	$withTax = 'incl' === get_option( 'woocommerce_tax_display_cart' );

	foreach ($event->args['products'] as $product ) {

        $product_id = getWooCartItemId( $product );
        $content_id = getWooProductContentId( $product_id );

        $price = $product['subtotal'];
        if($withTax) {
            $price += $product['subtotal_tax'];
        }

        $line_items[] = array(
			'product_id' => $content_id,
			'product_quantity' => $product['quantity'],
			'product_price' => pinterest_round($price/$product['quantity']),
			'product_name' => $product['name'],
			'product_category' => implode( ', ', array_column($product['categories'],'name') ),
			'tags' => implode( ', ', $product['tags'] )
		);
	}
	
	$params['line_items'] = $line_items;
	$subtotal = getWooEventCartTotal($event);
	if ( $event->getId() == 'woo_initiate_checkout' ) {
	
		$params['num_items'] = WC()->cart->get_cart_contents_count();

		$value_enabled_option = 'woo_initiate_checkout_value_enabled';
		$value_option_option  = 'woo_initiate_checkout_value_option';
		$value_global_option  = 'woo_initiate_checkout_value_global';
		$value_percent_option = 'woo_initiate_checkout_value_percent';

		$params['subtotal'] = $subtotal;
	
	} elseif ( $event->getId() == 'woo_paypal' ) {
	
		$params['num_items'] = WC()->cart->get_cart_contents_count();

		$value_enabled_option = 'woo_paypal_value_enabled';
		$value_option_option  = 'woo_paypal_value_option';
		$value_global_option  = 'woo_paypal_value_global';
		$value_percent_option = '';

		$params['subtotal'] = $subtotal;

		$params['action'] = 'PayPal';
	
	} else {
		
		$value_enabled_option = 'woo_add_to_cart_value_enabled';
		$value_option_option  = 'woo_add_to_cart_value_option';
		$value_global_option  = 'woo_add_to_cart_value_global';
		$value_percent_option = 'woo_add_to_cart_value_percent';
		
	}
	
	if ( PixelYourSite\PYS()->getOption( $value_enabled_option ) ) {
		

		$value_option   = PixelYourSite\PYS()->getOption( $value_option_option );
		$global_value   = PixelYourSite\PYS()->getOption( $value_global_option, 0 );
		$percents_value = PixelYourSite\PYS()->getOption( $value_percent_option, 100 );

        if(function_exists('PixelYourSite\getWooEventValueProducts')) {
            $params['value'] = PixelYourSite\getWooEventValueProducts( $value_option, $global_value, $percents_value,$subtotal,$event->args);
        } else {
            $params['value'] = PixelYourSite\getWooEventValueCart( $value_option, $global_value, $percents_value);
        }

		$params['currency'] = get_woocommerce_currency();
		
	}
	
	return $params;
	
}

function getWooPurchaseParams( $context ) {

    $order_key = sanitize_key($_REQUEST['key']);
	$order_id = (int) wc_get_order_id_by_order_key( $order_key );
	$order    = new \WC_Order( $order_id );
	
	$params = array(
		'post_type' => 'product',
	);
	
	$num_items = 0;
	$line_items = array();
	
	foreach ( $order->get_items( 'line_item' ) as $item ) {

        $product_id = getWooCartItemId( $item );
        $content_id = getWooProductContentId( $product_id );
		
		// content_name, category_name, tags
		$cd_params = getWooCustomAudiencesOptimizationParams( $product_id );
		$tags      = PixelYourSite\getObjectTerms( 'product_tag', $product_id );
		
		$line_item = array(
			'product_id'       => $content_id,
			'product_quantity' => $item['qty'],
			'product_price'    => PixelYourSite\getWooProductPriceToDisplay( $product_id, 1 ),
			'product_name'     => $cd_params['content_name'],
			'product_category' => $cd_params['category_name'],
			'tags'             => implode( ', ', $tags )
		);
		
		$line_items[] = $line_item;
		$num_items += $item['qty'];

	}
	
	$params['line_items'] = $line_items;
	$params['order_quantity'] = $num_items;
	$params['currency']  = get_woocommerce_currency();
	
	// add "value" only on Purchase event
	if ( $context == 'Purchase' ) {

		$value_option   = PixelYourSite\PYS()->getOption( 'woo_purchase_value_option' );
		$global_value   = PixelYourSite\PYS()->getOption( 'woo_purchase_value_global', 0 );
		$percents_value = PixelYourSite\PYS()->getOption( 'woo_purchase_value_percent', 100 );
		
		$params['value'] = PixelYourSite\getWooEventValueOrder( $value_option, $order, $global_value, $percents_value );
		
	}
	
	if ( PixelYourSite\isWooCommerceVersionGte( '3.0.0' ) ) {

		$params['town']    = $order->get_billing_city();
		$params['state']   = $order->get_billing_state();
		$params['country'] = $order->get_billing_country();
		$params['payment'] = $order->get_payment_method_title();

	} else {

		$params['town']    = $order->billing_city;
		$params['state']   = $order->billing_state;
		$params['country'] = $order->billing_country;
		$params['payment'] = $order->payment_method_title;

	}
	
	// shipping method
	if ( $shipping_methods = $order->get_items( 'shipping' ) ) {

		$labels = array();
		foreach ( $shipping_methods as $shipping ) {
			$labels[] = $shipping['name'] ? $shipping['name'] : null;
		}

		$params['shipping'] = implode( ', ', $labels );

	}
	
	// coupons
	if ( $coupons = $order->get_items( 'coupon' ) ) {

		$labels = array();
		foreach ( $coupons as $coupon ) {
			$labels[] = $coupon['name'] ? $coupon['name'] : null;
		}

		$params['promo_code_used'] = 'yes';
		$params['promo_code'] = implode( ', ', $labels );

	} else {

		$params['promo_code_used'] = 'no';

	}
	
	$params['total'] = (float) $order->get_total( 'edit' );
	$params['tax']   = (float) $order->get_total_tax( 'edit' );
	
	if ( PixelYourSite\isWooCommerceVersionGte( '2.7' ) ) {
		$params['shipping_cost'] = (float) $order->get_shipping_total( 'edit' ) + (float) $order->get_shipping_tax( 'edit' );
	} else {
		$params['shipping_cost'] = (float) $order->get_total_shipping() + (float) $order->get_shipping_tax();
	}
    if( PixelYourSite\PYS()->getOption("enable_woo_transactions_count_param")
        || PixelYourSite\PYS()->getOption("enable_woo_predicted_ltv_param")
        || PixelYourSite\PYS()->getOption("enable_woo_average_order_param")) {
        $customer_params = PixelYourSite\PYS()->getEventsManager()->getWooCustomerTotals();

        $params['lifetime_value']     = $customer_params['ltv'];
        $params['average_order']      = $customer_params['avg_order_value'];
        $params['transactions_count'] = $customer_params['orders_count'];
    }

	
	return $params;
}

/**
 * @param $product_id
 * @return string
 */

function getWooProductContentId( $product_id ) {

    if ( PixelYourSite\Pinterest()->getOption( 'woo_content_id' ) == 'product_sku' ) {
        $content_id = get_post_meta( $product_id, '_sku', true );
    } else {
        $content_id = $product_id;
    }

    $prefix = PixelYourSite\Pinterest()->getOption( 'woo_content_id_prefix' );
    $suffix = PixelYourSite\Pinterest()->getOption( 'woo_content_id_suffix' );

    $value = $prefix . $content_id . $suffix;

    return $value;
}

function getWooCartItemId( $product ) {

    if ( PixelYourSite\Pinterest()->getOption( 'woo_variable_as_simple' )
        && isset( $product['parent_id'] )
        && $product['parent_id'] !== 0
    ) {
        $product_id = $product['parent_id'];
    } else {
        $product_id = $product['product_id'];
    }

    return $product_id;

}

/**
 * @deprecated
 * @param string $context
 * @return string[]
 */
function getWooCartParamsOld( $context = 'cart' ) {

    $params = array(
        'post_type' => 'product',
    );

    $line_items = array();

    foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_item ) {

        $product_id = getWooCartItemId( $cart_item );
        $content_id = getWooProductContentId( $product_id );


        // content_name, category_name, tags
        $cd_params = getWooCustomAudiencesOptimizationParams( $product_id );
        $tags = PixelYourSite\getObjectTerms( 'product_tag', $product_id );

        $line_item = array(
            'product_id' => $content_id,
            'product_quantity' => $cart_item['quantity'],
            'product_price' => PixelYourSite\getWooProductPriceToDisplay( $product_id, 1 ),
            'product_name' => $cd_params['content_name'],
            'product_category' => $cd_params['category_name'],
            'tags' => implode( ', ', $tags )
        );

        $line_items[] = $line_item;

    }

    $params['line_items'] = $line_items;

    if ( $context == 'InitiateCheckout' ) {

        $params['num_items'] = WC()->cart->get_cart_contents_count();

        $value_enabled_option = 'woo_initiate_checkout_value_enabled';
        $value_option_option  = 'woo_initiate_checkout_value_option';
        $value_global_option  = 'woo_initiate_checkout_value_global';
        $value_percent_option = 'woo_initiate_checkout_value_percent';

        $params['subtotal'] = PixelYourSite\getWooCartSubtotal();

    } elseif ( $context == 'PayPal' ) {

        $params['num_items'] = WC()->cart->get_cart_contents_count();

        $value_enabled_option = 'woo_paypal_value_enabled';
        $value_option_option  = 'woo_paypal_value_option';
        $value_global_option  = 'woo_paypal_value_global';
        $value_percent_option = '';

        $params['subtotal'] = PixelYourSite\getWooCartSubtotal();

        $params['action'] = 'PayPal';

    } else {

        $value_enabled_option = 'woo_add_to_cart_value_enabled';
        $value_option_option  = 'woo_add_to_cart_value_option';
        $value_global_option  = 'woo_add_to_cart_value_global';
        $value_percent_option = 'woo_add_to_cart_value_percent';

    }

    if ( PixelYourSite\PYS()->getOption( $value_enabled_option ) ) {

        if ( PixelYourSite\PYS()->getOption( 'woo_event_value' ) == 'custom' ) {
            $amount = PixelYourSite\getWooCartTotal();
        } else {
            $amount = $params['value'] = WC()->cart->subtotal;
        }

        $value_option   = PixelYourSite\PYS()->getOption( $value_option_option );
        $global_value   = PixelYourSite\PYS()->getOption( $value_global_option, 0 );
        $percents_value = PixelYourSite\PYS()->getOption( $value_percent_option, 100 );

        $params['value']    = PixelYourSite\getWooEventValueCart( $value_option, $global_value, $percents_value);
        $params['currency'] = get_woocommerce_currency();

    }

    return $params;

}

