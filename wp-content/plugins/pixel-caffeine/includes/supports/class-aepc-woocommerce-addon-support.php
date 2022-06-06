<?php
/**
 * WooCommerce support
 *
 * @package Pixel Caffeine
 */

use PixelCaffeine\Dependencies\FacebookAds\Object\ServerSide\UserData;
use PixelCaffeine\Interfaces\ECommerceAddOnInterface;
use PixelCaffeine\ProductCatalog\Admin\Metaboxes;
use PixelCaffeine\ProductCatalog\Configuration;
use PixelCaffeine\ProductCatalog\DbProvider;
use PixelCaffeine\ProductCatalog\ProductCatalogManager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WooCommerce support
 *
 * @class AEPC_Woocommerce_Addon_Support
 */
class AEPC_Woocommerce_Addon_Support extends AEPC_Addon_Factory implements ECommerceAddOnInterface {

	const FEED_STATUS_META = '_product_feed_status';

	/**
	 * The slug of addon, useful to identify some common resources
	 *
	 * @var string
	 */
	protected $addon_slug = 'woocommerce';

	/**
	 * Store the name of addon. It doesn't need a translate.
	 *
	 * @var string
	 */
	protected $addon_name = 'WooCommerce';

	/**
	 * Store the main file of rthe plugin
	 *
	 * @var string
	 */
	protected $main_file = 'woocommerce/woocommerce.php';

	/**
	 * Store the URL of plugin website
	 *
	 * @var string
	 */
	protected $website_url = 'https://wordpress.org/plugins/woocommerce/';

	/**
	 * List of standard events supported for pixel firing by PHP (it's not included the events managed by JS)
	 *
	 * @var array
	 */
	protected $events_support = array( 'ViewContent', 'ViewCategory', 'AddToCart', 'Purchase', 'InitiateCheckout', 'AddPaymentInfo', 'CompleteRegistration' );

	/**
	 * Temporary save the product catalog for the current query, needed for the woocommerce filters
	 *
	 * @var ProductCatalogManager|null
	 */
	private $current_query_product_catalog = null;

	/**
	 * Save temporary the product query in order to access to special parameters (like feed status key)
	 * from the WP_Query filter
	 *
	 * @var array|null
	 */
	private $current_query = null;

	/**
	 * Method where set all necessary hooks launched from 'init' action
	 *
	 * @return void
	 */
	public function setup() {
		// Hooks when pixel is enabled.
		if ( version_compare( WC()->version, '3.3', '<' ) ) {
			add_filter( 'woocommerce_params', array( $this, 'add_currency_param' ) );
		} else {
			add_filter( 'woocommerce_get_script_data', array( $this, 'add_currency_param' ), 10, 2 );
		}
		add_action( 'woocommerce_after_shop_loop_item_title', array( $this, 'add_content_category_meta' ), 99 );
		add_filter( 'woocommerce_blocks_product_grid_item_html', array( $this, 'add_content_category_meta_in_block_grid' ), 99, 3 );
		add_action( 'woocommerce_registration_redirect', array( $this, 'save_registration_data' ), 5 );
		add_action( 'wp_footer', array( $this, 'register_add_payment_info_params' ), 10 );
		add_action( 'woocommerce_payment_complete', array( $this, 'send_server_side_purchase_event' ) );
		add_action( 'wp_footer', array( $this, 'register_add_to_wishlist_params' ), 10 );

		// AddToCart.
		add_action( 'woocommerce_add_to_cart', array( $this, 'send_add_to_cart' ), 99, 4 );
		add_action( 'woocommerce_add_to_cart', array( $this, 'set_last_product_added_to_cart_upon_redirect' ), 99, 4 );
		add_action(
			'woocommerce_ajax_added_to_cart',
			function() {
				if ( 'yes' !== get_option( 'woocommerce_cart_redirect_after_add' ) ) {
					$this->unset_last_product_added_to_cart();
				}
			}
		);
	}

	/**
	 * Check if the plugin is active by checking the main function is existing
	 *
	 * @return bool
	 */
	public function is_active() {
		return function_exists( 'WC' );
	}

	/**
	 * Check if we are in a place to fire the ViewContent event
	 *
	 * @return bool
	 */
	protected function can_fire_view_content() {
		return is_product();
	}

	/**
	 * Check if we are in a place to fire the ViewCategory event
	 *
	 * @return bool
	 */
	protected function can_fire_view_category() {
		return is_product_category();
	}

	/**
	 * Check if we are in a place to fire the AddToCart event
	 *
	 * @return bool
	 */
	protected function can_fire_add_to_cart() {
		return doing_action( 'woocommerce_add_to_cart' )
			|| ( null !== WC()->session && WC()->session->get( 'aepc_last_product_added_to_cart' ) );
	}

	/**
	 * Check if we are in a place to fire the InitiateCheckout event
	 *
	 * @return bool
	 */
	protected function can_fire_initiate_checkout() {
		return is_checkout() && ! is_order_received_page();
	}

	/**
	 * Check if we are in a place to fire the Purchase event
	 *
	 * @return bool
	 */
	protected function can_fire_purchase() {
		return doing_action( 'woocommerce_payment_complete' ) || is_order_received_page();
	}

	/**
	 * Check if we are in a place to fire the CompleteRegistration event
	 *
	 * @return bool
	 */
	protected function can_fire_complete_registration() {
		/**
		 * Tell phpstan that session might be also null
		 *
		 * @var null|WC_Session|WC_Session_Handler $session
		 */
		$session = WC()->session;
		return get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' && $session && false !== $session->get( 'aepc_complete_registration_data', false );
	}

	/**
	 * Send the Purchase event from server
	 *
	 * @param int $order_id Post object or post ID of the order.
	 *
	 * @return void
	 */
	public function send_server_side_purchase_event( $order_id ) {
		$this->send_event(
			'Purchase',
			array(
				$order_id,
			)
		);
	}

	/**
	 * This is an alternative method that register the add to cart parameters in a JS variable
	 *
	 * Because of AddToCart is managed by JS, we pass all product parameters to JS with all info of products. If the
	 * product is variable there are also info of all variations
	 *
	 * @return void
	 */
	public function register_add_to_wishlist_params() {
		global $post;

		if ( is_product() ) {
			$product_id = get_the_ID();
		} elseif ( ! empty( $post->post_content ) && preg_match( '/\[product_page id=["]?([0-9]+)/', $post->post_content, $matches ) ) {
			$product_id = (int) $matches[1];
		} else {
			$product_id = null;
		}

		$product = wc_get_product( $product_id );

		if ( empty( $product ) ) {
			return;
		}

		$args = array();

		$args[ $product_id ] = $this->get_add_to_cart_params( (int) $product_id );

		foreach ( $product->get_children() as $child_id ) {
			$variant_args = $this->get_add_to_cart_params( $child_id );
			if ( ! empty( $variant_args ) ) {
				$args[ $child_id ] = $variant_args;
			}
		}

		wp_localize_script( 'aepc-pixel-events', 'aepc_wc_add_to_wishlist', $args );
	}

	/**
	 * Get info from product when added to cart for AddToCart event
	 *
	 * @param int|string|null $product_id The product ID (if null, it will be fetched automatically).
	 * @param int             $quantity The quantity of the item added into the cart.
	 *
	 * @return array
	 */
	protected function get_add_to_cart_params( $product_id = null, $quantity = 1 ) {
		if ( empty( $product_id ) && null !== WC()->session && ! empty( WC()->session->aepc_last_product_added_to_cart ) ) {
			$product_id = (int) WC()->session->aepc_last_product_added_to_cart;
			unset( WC()->session->aepc_last_product_added_to_cart );
		}

		$product = wc_get_product( $product_id );

		if ( empty( $product ) ) {
			return array();
		}

		return array(
			'content_type'     => 'product',
			'content_ids'      => array( $this->maybe_sku( (int) $product_id ) ),
			'content_category' => AEPC_Pixel_Scripts::content_category_list( (int) $product_id ),
			'value'            => (float) $product->get_price() * $quantity,
			'currency'         => get_woocommerce_currency(),
			'event_id'         => WC()->cart->get_cart_hash(),
			'unique'           => true,
		);
	}

	/**
	 * Send the AddToCart event programmatically
	 *
	 * @param string $cart_item_key The cart hash used as event ID.
	 * @param int    $product_id The product ID added to the cart.
	 * @param int    $quantity The quantity of item added to the cart.
	 * @param int    $variation_id The ID of variation added if any.
	 *
	 * @return void
	 */
	public function send_add_to_cart( $cart_item_key, $product_id, $quantity, $variation_id ) {
		$this->send_event(
			'AddToCart',
			array(
				$variation_id ?: $product_id,
				$quantity,
			)
		);
	}

	/**
	 * Sets last product added to cart to session when adding to cart a product and redirection to cart is enabled.
	 *
	 * @param string $cart_item_key The cart hash used as event ID.
	 * @param int    $product_id The product ID added to the cart.
	 * @param int    $quantity The quantity of item added to the cart.
	 * @param int    $variation_id The ID of variation added if any.
	 *
	 * @return void
	 */
	public function set_last_product_added_to_cart_upon_redirect( $cart_item_key, $product_id, $quantity, $variation_id ) {
		WC()->session->set( 'aepc_last_product_added_to_cart', $variation_id ?: $product_id );
	}

	/**
	 * Unset the last product added to cart
	 *
	 * @return void
	 */
	public function unset_last_product_added_to_cart() {
		if ( null !== WC()->session && ! empty( WC()->session->aepc_last_product_added_to_cart ) ) {
			unset( WC()->session->aepc_last_product_added_to_cart );
		}
	}

	/**
	 * Register the AddPaymentInfo parameters fired by JS when the checkout is submitted
	 *
	 * @return void
	 */
	public function register_add_payment_info_params() {
		if ( ! is_checkout() ) {
			return;
		}

		$args = AEPC_Track::check_event_parameters( 'AddPaymentInfo', $this->get_initiate_checkout_params() );
		wp_localize_script( 'aepc-pixel-events', 'aepc_add_payment_info_params', $args );
	}

	/**
	 * Get product info from single page for ViewContent event
	 *
	 * @return array
	 */
	protected function get_view_content_params() {
		$product = wc_get_product();

		if ( empty( $product ) ) {
			return array();
		}

		$product_id = $this->get_product_id( $product );

		$params = array(
			'content_type' => 'product',
			'content_ids'  => array( $this->maybe_sku( $product_id ) ),
		);

		if ( $product->is_type( 'variable' ) && AEPC_Track::can_use_product_group() ) {
			$params['content_type'] = 'product_group';
		}

		$params['content_name']     = $this->get_product_name( $product );
		$params['content_category'] = AEPC_Pixel_Scripts::content_category_list( $product_id );
		$params['value']            = floatval( $product->get_price() );
		$params['currency']         = get_woocommerce_currency();

		return $params;
	}

	/**
	 * Get product info from single page for ViewCategory event
	 *
	 * @return array
	 */
	protected function get_view_category_params() {
		global $wp_query;

		if ( ! $wp_query instanceof WP_Query ) {
			return array();
		}

		$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );

		if ( ! $term instanceof WP_Term ) {
			return array();
		}

		$product_ids = array_values(
			array_map(
				function( $item ) {
					return $this->maybe_sku( $item->ID );
				},
				$wp_query->posts
			)
		);

		return array(
			'content_type'     => 'product',
			'content_name'     => $term->name,
			'content_category' => AEPC_Pixel_Scripts::content_category_path( $term ),
			'content_ids'      => array_slice( $product_ids, 0, 10 ),
		);
	}

	/**
	 * Get info from checkout page for InitiateCheckout event
	 *
	 * @return array
	 */
	protected function get_initiate_checkout_params() {
		$product_ids = array();
		$num_items   = 0;

		foreach ( WC()->cart->get_cart() as $values ) {
			$_product = $values['data'];

			if ( empty( $_product ) ) {
				continue;
			}

			$product_ids[] = $this->maybe_sku( $this->get_product_id( $_product ) );
			$num_items    += $values['quantity'];
		}

		// Get cart.
		$cart = WC()->cart;

		if ( ! $cart instanceof WC_Cart ) {
			return array();
		}

		// Order value.
		$cart_total = WC()->cart->total; // @phpstan-ignore-line

		// Remove shipping costs.
		if ( ! AEPC_Track::can_track_shipping_costs() ) {
			$cart_total -= WC()->cart->shipping_total; // @phpstan-ignore-line
		}

		return array(
			'content_type' => 'product',
			'content_ids'  => array_unique( $product_ids ),
			'num_items'    => $num_items,
			'value'        => $cart_total,
			'currency'     => get_woocommerce_currency(),
		);
	}

	/**
	 * Get product info from purchase succeeded page for Purchase event
	 *
	 * @param int $order_id The order_id.
	 *
	 * @return array
	 */
	protected function get_purchase_params( $order_id = null ) {
		if ( $order_id ) {
			$order = wc_get_order( $order_id );
		} else {
			global $wp;
			$order = wc_get_order( ! empty( $wp->query_vars['order-received'] ) ? (int) $wp->query_vars['order-received'] : (int) filter_input( INPUT_GET, 'order-received', FILTER_SANITIZE_NUMBER_INT ) );
		}

		if ( ! $order instanceof WC_Order ) {
			return array();
		}

		$product_ids = array_map(
			/**
			 * The WC Order Item instance
			 *
			 * @var WC_Order_Item $item
			 */
			function( $item ) use ( $order ) {
				$_product = $item instanceof WC_Order_Item ? $item->get_product() : $order->get_product_from_item( $item );  // @phpstan-ignore-line

				if ( empty( $_product ) ) {
					return array();
				}

				$_product_id = $this->get_product_id( $_product );

				if ( ! empty( $_product_id ) ) {
					return $this->maybe_sku( $_product_id );
				} else {
					return $item['product_id'];
				}
			},
			array_values(
				$order->get_items()
			)
		);

		// Order value.
		$order_value = $order->get_total();

		// Remove shipping costs.
		if ( ! AEPC_Track::can_track_shipping_costs() ) {
			$order_value -= method_exists( $order, 'get_shipping_total' ) ? $order->get_shipping_total() : $order->get_total_shipping();
		}

		return array(
			'unique'       => true,
			'content_ids'  => array_unique( $product_ids ),
			'content_type' => 'product',
			'value'        => $order_value,
			'currency'     => method_exists( $order, 'get_currency' ) ? $order->get_currency() : $order->get_order_currency(),
			'event_id'     => hash( 'sha256', $order->get_id() . $order_value . wp_json_encode( array_unique( $product_ids ) ), false ),
			'user_data'    => ( $this->create_user_data_from_order( $order ) ),
		);
	}

	/**
	 * Create the UserData instance from the order
	 *
	 * @param WC_Order $order The order instance.
	 *
	 * @return UserData
	 */
	public function create_user_data_from_order( WC_Order $order ) {
		return ( new UserData() )
			->setFirstName( $order->get_billing_first_name() )
			->setLastName( $order->get_billing_last_name() )
			->setExternalId( (string) $order->get_customer_id() )
			->setClientIpAddress( $order->get_customer_ip_address() )
			->setClientUserAgent( $order->get_customer_user_agent() )
			->setCity( $order->get_billing_city() )
			->setCountryCode( $order->get_billing_country() )
			->setEmail( $order->get_billing_email() )
			->setPhone( $order->get_billing_phone() )
			->setState( $order->get_shipping_state() )
			->setZipCode( $order->get_billing_postcode() );
	}

	/**
	 * Save CompleteRegistration data event in session, because of redirect after woo registration
	 *
	 * @param string $redirect The redirect.
	 *
	 * @return string
	 */
	public function save_registration_data( $redirect ) {
		if ( ! AEPC_Track::is_completeregistration_active() ) {
			return $redirect;
		}

		$session_class = apply_filters( 'woocommerce_session_handler', 'WC_Session_Handler' );
		WC()->session  = new $session_class();
		WC()->session->set( 'aepc_complete_registration_data', apply_filters( 'aepc_complete_registration', array() ) );

		// I had to hook into the filter for decide what URL use for redirect after registration. I need to pass it.
		return $redirect;
	}

	/**
	 * Get info from when a registration form is completed, such as signup for a service, for CompleteRegistration event
	 *
	 * @return array|bool
	 */
	protected function get_complete_registration_params() {
		/**
		 * Params might set as array or false if not complete registration event
		 *
		 * @var array|bool $params
		 */
		$params = WC()->session->get( 'aepc_complete_registration_data', array() );

		// Delete session key.
		unset( WC()->session->aepc_complete_registration_data );

		return $params ?: array();
	}

	/**
	 * Add currency value on params list on woocommerce localize
	 *
	 * @param array  $data Add currency param in the data stack.
	 * @param string $handle The handle from woocommerce filter.
	 *
	 * @return array
	 */
	public function add_currency_param( $data, $handle = 'woocommerce' ) {
		if (
			'woocommerce' !== $handle
			|| ! function_exists( 'get_woocommerce_currency' )
			|| ! PixelCaffeine()->is_pixel_enabled()
		) {
			return $data;
		}

		return array_merge(
			$data,
			array(
				'currency' => get_woocommerce_currency(),
			)
		);
	}

	/**
	 * Add a meta info inside each product of loop, to have content_category for each product
	 *
	 * @return void
	 */
	public function add_content_category_meta() {
		if ( is_admin() || ! PixelCaffeine()->is_pixel_enabled() ) {
			// is_admin is necessary in order to avoid that this function is called by admin pages from some extension.
			return;
		}

		$product = wc_get_product();

		if ( empty( $product ) ) {
			return;
		}

		$product_id = $this->get_product_id( $product );

		/**
		 * Ignore escaping because the output is safe.
		 *
		 * Additionally wp_kses cannot be used because it strips the data-attributes in WP <5.0.
		 */
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $this->get_category_content( $product_id );
	}

	/**
	 * Add the content category meta in the product grid from the gutenberg block.
	 *
	 * @param string     $li The li tag HTML.
	 * @param stdClass   $data The data.
	 * @param WC_Product $product The product instance.
	 *
	 * @return string
	 */
	public function add_content_category_meta_in_block_grid( $li, $data, $product ) {
		return str_replace(
			$data->button,
			$data->button . $this->get_category_content( $product->get_id() ),
			$li
		);
	}

	/**
	 * Generate the HTML with the data content_category
	 *
	 * @param int $product_id The product ID.
	 *
	 * @return string
	 */
	protected function get_category_content( $product_id ) {
		return sprintf(
			'<span data-content_category="%s"></span>',
			esc_attr( AEPC_Pixel_Scripts::content_category_list( $product_id ) )
		);
	}

	/**
	 * Extend UserData entity if needed
	 *
	 * @param UserData $user_data User Data.
	 *
	 * @return UserData
	 */
	public function extend_user_data( UserData $user_data ) {
		$user = wp_get_current_user();

		return $user_data
			->setPhone( $user->billing_phone )
			->setCity( $user->billing_city )
			->setState( $user->billing_state )
			->setZipCode( $user->billing_postcode );
	}

	/**
	 * Returns SKU if exists, otherwise the product ID
	 *
	 * @param int $product_id The product ID.
	 *
	 * @return string
	 */
	protected function maybe_sku( $product_id ) {
		$sku = get_post_meta( $product_id, '_sku', true );

		if ( AEPC_Track::can_use_sku() && $sku ) {
			return (string) $sku;
		}

		return (string) $product_id;
	}

	/**
	 * Retrieve the product name
	 *
	 * @param int|WC_Product $product The ID of product or the product woo object where get its name.
	 *
	 * @return string
	 */
	public function get_product_name( $product ) {
		if ( ! is_object( $product ) ) {
			$product = wc_get_product( $product );
		}

		return $product instanceof WC_Product ? $product->get_title() : '';
	}

	/**
	 * Says if the product is of addon type
	 *
	 * @param int $product_id The product ID.
	 *
	 * @return bool
	 */
	public function is_product_of_this_addon( $product_id ) {
		return 'product' === get_post_type( $product_id );
	}

	/**
	 * Returns the types supported by this plugin
	 *
	 * @return array
	 */
	public function get_product_types() {
		return wc_get_product_types();
	}

	/**
	 * Returns the checkout URL where the items may be purcahsed
	 *
	 * @return string
	 */
	public function get_checkout_url() {
		return function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : wc_get_page_permalink( 'checkout' );
	}

	/**
	 * Helper method to get the description from a product by checking first description and then short one if the full
	 * one is empty
	 *
	 * @param WC_Product $product The product instance.
	 *
	 * @return string
	 */
	protected function get_description_from_product( $product ) {
		return $product->get_description();
	}

	/**
	 * Get the short description from the product
	 *
	 * @param WC_Product $product The product instance.
	 *
	 * @return mixed
	 */
	protected function get_short_description_from_product( $product ) {
		return $product->get_short_description();
	}

	/**
	 * Return the AEPC_Addon_Product_item instance for the product
	 *
	 * @param WC_Product|WC_Product_Simple|WC_Product_Variable|WC_Product_Variation $product The product instance.
	 * @param Metaboxes                                                             $metaboxes The metabox manager instance.
	 * @param Configuration                                                         $configuration The product catalog configuration instance.
	 *
	 * @return AEPC_Addon_Product_Item
	 */
	public function get_product_item( $product, Metaboxes $metaboxes, Configuration $configuration ) {
		$product_item = new AEPC_Addon_Product_Item( $this );
		preg_match( '/src="([^"]+)"/', $product->get_image( $configuration->get( Configuration::OPTION_IMAGE_SIZE ) ), $image_parts );

		// Backwards helper variables.
		$product_is_variation         = $product->is_type( 'variation' );
		$product_title                = $product->get_title();
		$product_id                   = $product->get_id();
		$product_slug                 = $product->get_slug();
		$product_description          = $this->get_description_from_product( $product );
		$product_short_description    = $this->get_short_description_from_product( $product );
		$product_additional_image_ids = array_map( 'wp_get_attachment_url', method_exists( $product, 'get_gallery_image_ids' ) ? $product->get_gallery_image_ids() : $product->get_gallery_attachment_ids() );
		$product_parent_id            = $product->get_parent_id();
		$product_parent               = $product_parent_id ? wc_get_product( $product_parent_id ) : null;
		$product_image_link           = isset( $image_parts[1] ) ? $image_parts[1] : null;
		$product_price                = floatval( $product instanceof WC_Product_Variable ? $product->get_variation_regular_price() : $product->get_regular_price() );
		$product_sale_price           = floatval( $product instanceof WC_Product_Variable ? $product->get_variation_sale_price() : $product->get_sale_price() );

		if ( wc_prices_include_tax() ) {
			$product_price_tax       = $product_price;
			$product_sale_price_tax  = $product_sale_price;
			$product_price           = wc_get_price_excluding_tax( $product, array( 'price' => $product_price ) );
			$product_sale_price      = wc_get_price_excluding_tax( $product, array( 'price' => $product_sale_price ) );
			$product_price_tax      -= $product_price;
			$product_sale_price_tax -= $product_sale_price;
		} else {
			$product_price_tax      = wc_get_price_including_tax( $product, array( 'price' => $product_price ) ) - $product_price;
			$product_sale_price_tax = wc_get_price_including_tax( $product, array( 'price' => $product_sale_price ) ) - $product_sale_price;
		}

		// Add variation name into product title.
		if ( $product_is_variation ) {
			$product_title .= sprintf( ' (#%s)', $product_id );
		}

		// If variation description is empty get it from parent.
		if ( $product_is_variation && empty( $product_description ) ) {
			$_parent_product     = wc_get_product( $product_parent_id );
			$product_description = $_parent_product instanceof WC_Product ? $this->get_description_from_product( $_parent_product ) : '';
		}

		// If variation description is empty get it from parent.
		if ( $product_is_variation && empty( $product_short_description ) ) {
			$_parent_product           = wc_get_product( $product_parent_id );
			$product_short_description = $_parent_product instanceof WC_Product ? $this->get_short_description_from_product( $_parent_product ) : '';
		}

		if ( method_exists( $product, 'get_date_on_sale_from' ) && method_exists( $product, 'get_date_on_sale_to' ) ) {
			$product_date_on_sale_from = $product->get_date_on_sale_from();
			$product_date_on_sale_to   = $product->get_date_on_sale_to();
		} else {
			$product_date_on_sale_from = new DateTime();
			$product_date_on_sale_to   = new DateTime();
			$date_sale_from            = get_post_meta( $product_id, '_sale_price_dates_from', true );
			$date_sale_to              = get_post_meta( $product_id, '_sale_price_dates_to', true );
			$product_date_on_sale_from = $date_sale_from ? $product_date_on_sale_from->setTimestamp( $date_sale_from ) : null;
			$product_date_on_sale_to   = $date_sale_to ? $product_date_on_sale_to->setTimestamp( $date_sale_to ) : null;
		}

		$add_to_cart_url = $product->add_to_cart_url();
		if ( strpos( $add_to_cart_url, $product->get_permalink() ) === false && '?' === $add_to_cart_url[0] ) {
			parse_str( str_replace( '?', '', $add_to_cart_url ), $add_to_cart_qs );
			$add_to_cart_url = add_query_arg( $add_to_cart_qs, $product->get_permalink() );
		}

		$product_item
			->set_id( $product_id )
			->set_sku( $product->get_sku() )
			->set_slug( $product_slug )
			->set_permalink( $product->get_permalink() )
			->set_admin_url(
				add_query_arg(
					array(
						'post'   => $product_id,
						'action' => 'edit',
					),
					admin_url( 'post.php' )
				)
			)
			->set_parent_admin_url(
				add_query_arg(
					array(
						'post'   => $product_parent_id,
						'action' => 'edit',
					),
					admin_url( 'post.php' )
				)
			)
			->set_title( $product_title )
			->set_description( $product_description ? $product_description : $product_short_description )
			->set_short_description( $product_short_description )
			->set_link( $product->get_permalink() )
			->set_image_url( $product_image_link )
			->set_additional_image_urls( array_filter( $product_additional_image_ids ) )
			->set_currency( get_woocommerce_currency() )
			->set_price( $this->format_price( $product_price ) )
			->set_price_tax( $this->format_price( $product_price_tax ) )
			->set_sale_price( $product_sale_price < $product_price ? $this->format_price( $product_sale_price ) : '0' )
			->set_sale_price_tax( $product_sale_price < $product_price ? $this->format_price( $product_sale_price_tax ) : '0' )
			->set_checkout_url( $add_to_cart_url )
			->set_if_needs_shipping( $product->needs_shipping() )
			->set_shipping_weight( $product->get_weight() )
			->set_shipping_weight_unit( get_option( 'woocommerce_weight_unit' ) )
			->set_if_variation( $product_is_variation )
			->set_group_id( $product_parent_id )
			->set_group_sku( $product_parent ? $product_parent->get_sku() : null )
			->set_google_category(
				$metaboxes->get_google_category(
					$product_is_variation ? $product_parent_id : $product_id
				)
			);

		// Set sale date if defined.
		if ( $product_date_on_sale_from instanceof Datetime && $product_date_on_sale_to instanceof Datetime ) {
			$product_item->set_sale_price_effective_date( $product_date_on_sale_from, $product_date_on_sale_to );
		}

		// Set availability.
		$availability = $product->get_availability();
		switch ( $availability['class'] ) {
			case 'in-stock':
			default:
				$product_item->set_in_stock();
				break;
			case 'out-of-stock':
				$product_item->set_out_of_stock();
				break;
			case 'available-on-backorder':
				$product_item->set_in_preorder();
				break;
		}

		// Get categories.
		$terms = get_terms(
			array(
				'object_ids'   => $product_item->is_variation() ? $product_parent_id : $product_id,
				'taxonomy'     => 'product_cat',
				'hierarchical' => true,
				'fields'       => 'id=>parent',
			)
		);

		if ( is_array( $terms ) && ! empty( $terms ) ) {
			$product_item->set_categories( $terms );
		}

		return $product_item;
	}

	/**
	 * Customize the WP Query in wc_get_products
	 *
	 * @param \WP_Query $wp_query The WP Query instance.
	 *
	 * @return void
	 */
	public function customize_wp_query( \WP_Query &$wp_query ) {
		$products_query  = $this->current_query;
		$product_catalog = $this->current_query_product_catalog;
		$meta_query      = $wp_query->get( 'meta_query', array() );

		// Add meta query manually for versions before of 3.1, when no 'stock_status' was available.
		if ( isset( $products_query['stock_status'] ) && version_compare( WC()->version, '3.1.0', '<' ) ) {
			$meta_query[] = array(
				'key'     => '_stock_status',
				'compare' => 'IN',
				'value'   => $products_query['stock_status'],
			);
		}

		// Change compare condition in _stock_status meta query for newest WOO versions that don't allow values in a array.
		foreach ( $meta_query as &$query ) {
			if ( '_stock_status' === $query['key'] && is_array( $query['value'] ) ) {
				$query['compare'] = 'IN';
			}
		}

		// Include variation items manually for 3.0.x version of Woo.
		if ( isset( $products_query['type'] ) && in_array( 'variation', $products_query['type'], true ) && ! is_array( $wp_query->get( 'post_type' ) ) ) {
			$wp_query->set( 'post_type', array( 'product', 'product_variation' ) );
		}

		// Add feed status meta query.
		if ( $product_catalog instanceof ProductCatalogManager ) {
			$key = $this->get_feed_status_meta_key( $product_catalog );
			if ( isset( $products_query[ $key ] ) ) {
				$meta_query[] = array(
					'key'     => $this->get_feed_status_meta_key( $product_catalog ),
					'compare' => $products_query[ $key ] ? '=' : 'NOT EXISTS',
					'value'   => $products_query[ $key ],
				);
			}
		}

		$wp_query->set( 'meta_query', $meta_query );
	}

	/**
	 * Ensure to not include orphaned variations.
	 *
	 * @param string $where The where statement.
	 *
	 * @return string
	 */
	public function ensure_not_orphaned_variations( $where ) {
		global $wpdb;

		$variables = $wpdb->prepare(
			"
SELECT ID
FROM {$wpdb->posts} p
INNER JOIN {$wpdb->term_relationships} tr ON tr.object_id = p.ID
INNER JOIN {$wpdb->term_taxonomy} tt USING(term_taxonomy_id)
INNER JOIN {$wpdb->terms} t USING(term_id)
WHERE p.post_type = %s
AND p.post_status = %s
AND tt.taxonomy = %s
AND t.slug = %s
		",
			'product',
			'publish',
			'product_type',
			'variable'
		);

		return "$where AND ( {$wpdb->posts}.post_parent = 0 OR {$wpdb->posts}.post_parent IN ($variables) ) ";
	}

	/**
	 * Get the arguments of the items query
	 *
	 * @param string                $filter One of 'all', 'edited' or 'saved'.
	 * @param ProductCatalogManager $product_catalog The product catalog manager instance.
	 *
	 * @return array
	 */
	protected function query_items_args( $filter, ProductCatalogManager $product_catalog ) {
		$products_query = array(
			'status'   => array( 'publish' ),
			'limit'    => $product_catalog->configuration()->get( Configuration::OPTION_CHUNK_LIMIT ),
			'orderby'  => 'ID',
			'order'    => 'ASC',
			'category' => array(),
			'tag'      => array(),
			'include'  => array(),
			'exclude'  => array(),
		);

		// Collect the product types to use in the query.
		$product_types = array_filter( (array) $product_catalog->configuration()->get( Configuration::OPTION_FILTER_BY_TYPE ) );
		if ( $product_types ) {
			$products_query['type'] = $product_types;
		} else {
			$products_query['type'] = array_merge( array_keys( wc_get_product_types() ) );
		}

		// Add variations if the option is disabled.
		if ( in_array( 'variable', $products_query['type'], true ) && ! $product_catalog->configuration()->get( Configuration::OPTION_NO_VARIATIONS ) ) {
			$products_query['type'][] = 'variation';
		}

		$filter_cat = array_map( 'intval', array_filter( (array) $product_catalog->configuration()->get( Configuration::OPTION_FILTER_BY_CATEGORY ) ) );
		if ( ! empty( $filter_cat ) ) {
			foreach ( $filter_cat as $cat_id ) {
				$term = get_term( $cat_id );
				if ( $term instanceof WP_Term ) {
					$products_query['category'][] = $term->slug;
				}
			}
		}

		$filter_tag = array_map( 'intval', array_filter( (array) $product_catalog->configuration()->get( Configuration::OPTION_FILTER_BY_TAG ) ) );
		if ( ! empty( $filter_tag ) ) {
			foreach ( $filter_tag as $tag_id ) {
				$term = get_term( $tag_id );
				if ( $term instanceof WP_Term ) {
					$products_query['tag'][] = $term->slug;
				}
			}
		}

		if ( $product_catalog->configuration()->get( Configuration::OPTION_FILTER_ON_SALE ) ) {
			$products_query['include'] = wc_get_product_ids_on_sale();
		}

		$filter_stock = array_filter( (array) $product_catalog->configuration()->get( Configuration::OPTION_FILTER_BY_STOCK ) );
		if ( ! empty( $filter_stock ) ) {
			$filter_stock = array_map(
				function( $status ) {
					$stock_map = array(
						AEPC_Addon_Product_Item::IN_STOCK => 'instock',
						AEPC_Addon_Product_Item::OUT_OF_STOCK => 'outofstock',
					);
					return $stock_map[ $status ];
				},
				$filter_stock
			);

			$products_query['stock_status'] = $filter_stock;
		}

		return $this->filter_items_query( $filter, $products_query, $product_catalog );
	}

	/**
	 * Query the items from DB in base of if get edited, saved or all
	 *
	 * @param string                $filter One of 'all', 'edited' or 'saved'.
	 * @param ProductCatalogManager $product_catalog The product catalog manager instance.
	 * @param Metaboxes             $metaboxes The metaboxes manager instance.
	 *
	 * @return AEPC_Addon_Product_Item[]
	 */
	protected function query_items( $filter, ProductCatalogManager $product_catalog, Metaboxes $metaboxes ) {
		$products_query = $this->query_items_args( $filter, $product_catalog );
		return $this->do_query( $products_query, $product_catalog, $metaboxes );
	}

	/**
	 * Perform the query from the array of arguments for wc_get_products()
	 *
	 * Args and usage: https://github.com/woocommerce/woocommerce/wiki/wc_get_products-and-WC_Product_Query
	 *
	 * @param array                 $products_query Array of args (above).
	 * @param ProductCatalogManager $product_catalog The product catalog manager instance.
	 * @param Metaboxes             $metaboxes The metaboxes manager instance.
	 *
	 * @return AEPC_Addon_Product_Item[]
	 */
	protected function do_query( $products_query, ProductCatalogManager $product_catalog, Metaboxes $metaboxes ) {
		$this->current_query                 = $products_query;
		$this->current_query_product_catalog = $product_catalog;

		// Add hook to customize the query.
		add_action( 'pre_get_posts', array( $this, 'customize_wp_query' ) );
		add_filter( 'posts_where_request', array( $this, 'ensure_not_orphaned_variations' ), 10 );

		// Map WC objects.
		$products = wc_get_products( $products_query );

		if ( ! is_array( $products ) ) {
			return array();
		}

		// Remove previous hook.
		remove_action( 'pre_get_posts', array( $this, 'customize_wp_query' ) );
		remove_filter( 'posts_where_request', array( $this, 'ensure_not_orphaned_variations' ), 10 );

		// Map the product item object.
		foreach ( $products as $i => &$item ) {
			$item = $this->get_product_item( $item, $metaboxes, $product_catalog->configuration() );

			// If variant and parent is 0, go ahead.
			if ( $item->is_variation() && $item->get_group_id() === 0 ) {
				unset( $products[ $i ] );
			}
		}

		$this->current_query_product_catalog = null;
		$this->current_query                 = null;
		return $products;
	}

	/**
	 * Filter the WP_Query arguments with the necessary arguments in order to filter the query in base of the status
	 * of the product in the feed
	 *
	 * Args and usage: https://github.com/woocommerce/woocommerce/wiki/wc_get_products-and-WC_Product_Query
	 *
	 * @param string                $filter One of 'all', 'edited' or 'saved'.
	 * @param array                 $products_query Array of args (above).
	 * @param ProductCatalogManager $product_catalog The product catalog manager instance.
	 *
	 * @return array
	 */
	protected function filter_items_query( $filter, array $products_query, ProductCatalogManager $product_catalog ) {
		$key = $this->get_feed_status_meta_key( $product_catalog );

		switch ( $filter ) {

			case 'not-saved':
				$products_query[ $key ] = false;
				break;

			case 'saved':
				$products_query[ $key ] = DbProvider::FEED_STATUS_SAVED;
				break;

			case 'edited':
				$products_query[ $key ] = DbProvider::FEED_STATUS_EDITED;
				break;

		}

		return $products_query;
	}

	/**
	 * Returns the array of all term objects id=>name for all categories of the shop
	 *
	 * @return array
	 */
	public function get_product_categories() {
		$categories = array();
		$terms      = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => false,
			)
		);

		if ( ! is_array( $terms ) ) {
			return array();
		}

		foreach ( $terms as $term ) {
			$categories[ $term->term_id ] = $term->name;
		}

		return $categories;
	}

	/**
	 * Returns the array of all term objects id=>name for all tags of the shop
	 *
	 * @return array
	 */
	public function get_product_tags() {
		$categories = array();
		$terms      = get_terms(
			array(
				'taxonomy'   => 'product_tag',
				'hide_empty' => false,
			)
		);

		if ( ! is_array( $terms ) ) {
			return array();
		}

		foreach ( $terms as $term ) {
			$categories[ $term->term_id ] = $term->name;
		}

		return $categories;
	}

	/**
	 * Return the array with all AEPC_Addon_Product_item instances for the products to include inside the XML feed
	 *
	 * @param ProductCatalogManager $product_catalog The product catalog manager instance.
	 * @param Metaboxes             $metaboxes The metaboxes manager instance.
	 *
	 * @return AEPC_Addon_Product_Item[]
	 */
	public function get_feed_entries( ProductCatalogManager $product_catalog, Metaboxes $metaboxes ) {
		return $this->query_items( ProductCatalogManager::FILTER_ALL, $product_catalog, $metaboxes );
	}

	/**
	 * Get the feed entries to save into the feed
	 *
	 * @param ProductCatalogManager $product_catalog The product catalog manager instance.
	 * @param Metaboxes             $metaboxes The metaboxes manager instance.
	 *
	 * @return AEPC_Addon_Product_Item[]
	 */
	public function get_feed_entries_to_save( ProductCatalogManager $product_catalog, Metaboxes $metaboxes ) {
		return $this->query_items( ProductCatalogManager::FILTER_NOT_SAVED, $product_catalog, $metaboxes );
	}

	/**
	 * Get the feed entries to edit in the feed
	 *
	 * @param ProductCatalogManager $product_catalog The product catalog manager instance.
	 * @param Metaboxes             $metaboxes The metaboxes manager instance.
	 *
	 * @return AEPC_Addon_Product_Item[]
	 */
	public function get_feed_entries_to_edit( ProductCatalogManager $product_catalog, Metaboxes $metaboxes ) {
		return $this->query_items( ProductCatalogManager::FILTER_EDITED, $product_catalog, $metaboxes );
	}

	/**
	 * Get the key associated to the product catalog
	 *
	 * @param ProductCatalogManager $product_catalog The product catalog manager instance.
	 *
	 * @return string
	 */
	protected function get_feed_status_meta_key( ProductCatalogManager $product_catalog ) {
		return sprintf( '%s_%s', self::FEED_STATUS_META, $product_catalog->get_entity()->get_id() );
	}

	/**
	 * Save a meta in the product post that set the product as saved in the product feed
	 *
	 * @param ProductCatalogManager    $product_catalog The product catalog manager instance.
	 * @param \AEPC_Addon_Product_Item $item The product item instance from Add-on.
	 *
	 * @return void
	 */
	public function set_product_saved_in_feed( ProductCatalogManager $product_catalog, \AEPC_Addon_Product_Item $item ) {
		update_post_meta( intval( $item->get_id() ), $this->get_feed_status_meta_key( $product_catalog ), DbProvider::FEED_STATUS_SAVED );
	}

	/**
	 * Save the meta in the product post that set the product as edited in the product feed
	 *
	 * @param ProductCatalogManager    $product_catalog The product catalog manager instance.
	 * @param \AEPC_Addon_Product_Item $item The product item instance from Add-on.
	 *
	 * @return void
	 */
	public function set_product_edited_in_feed( ProductCatalogManager $product_catalog, \AEPC_Addon_Product_Item $item ) {
		update_post_meta( intval( $item->get_id() ), $this->get_feed_status_meta_key( $product_catalog ), DbProvider::FEED_STATUS_EDITED );
	}

	/**
	 * Delete the meta in the product post that set the product as saved in the product feed
	 *
	 * @param ProductCatalogManager    $product_catalog The product catalog manager instance.
	 * @param \AEPC_Addon_Product_Item $item The product item instance from Add-on.
	 *
	 * @return void
	 */
	public function set_product_not_saved_in_feed( ProductCatalogManager $product_catalog, \AEPC_Addon_Product_Item $item ) {
		delete_post_meta( intval( $item->get_id() ), $this->get_feed_status_meta_key( $product_catalog ) );
	}

	/**
	 * Perform a global delete in one query ideally for all feed status associated to the product catalog
	 *
	 * @param ProductCatalogManager $product_catalog The product catalog manager instance.
	 *
	 * @return void
	 */
	public function remove_all_feed_status( ProductCatalogManager $product_catalog ) {
		delete_post_meta_by_key( $this->get_feed_status_meta_key( $product_catalog ) );
	}

	/**
	 * Detect if there are items to save yet or not
	 *
	 * @param ProductCatalogManager $product_catalog The product catalog manager instance.
	 *
	 * @return bool
	 */
	public function there_are_items_to_save( ProductCatalogManager $product_catalog ) {
		$products_query = $this->query_items_args( ProductCatalogManager::FILTER_NOT_SAVED, $product_catalog );

		// Get only counter.
		$products_query['limit'] = 1;

		// Query.
		$products = $this->do_query( $products_query, $product_catalog, new Metaboxes() );

		return ! empty( $products );
	}

	/**
	 * Format the price with fixed decimals following the WooCommerce settings
	 *
	 * @param float $price The price to format.
	 *
	 * @return string
	 */
	protected function format_price( $price ) {
		$decimals = wc_get_price_decimals();
		$negative = $price < 0;
		$price    = apply_filters( 'raw_woocommerce_price', floatval( $negative ? $price * -1 : $price ) );
		$price    = round( $price, $decimals );

		if ( apply_filters( 'woocommerce_price_trim_zeros', false ) && $decimals > 0 ) {
			$price = wc_trim_zeros( $price );
		}

		return (string) $price;
	}

	/**
	 * Get the right product ID
	 *
	 * @param WC_Product $product The product instance.
	 *
	 * @return int
	 */
	protected function get_product_id( WC_Product $product ) {
		if ( ! AEPC_Track::can_track_variations() && $product->is_type( 'variation' ) ) {
			$product_id = $product->get_parent_id();
		} else {
			$product_id = $product->get_id();
		}

		return $product_id;
	}
}
