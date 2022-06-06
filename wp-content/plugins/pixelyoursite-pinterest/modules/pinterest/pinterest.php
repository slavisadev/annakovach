<?php

/**
 * @link: https://developers.pinterest.com/docs/ad-tools/conversion-tag/
 */

namespace PixelYourSite;

use function PixelYourSite\Pinterest\getWooProductContentId;
use function PixelYourSite\Pinterest\pinterest_round;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Pinterest extends Settings implements Pixel, Plugin {

	private static $_instance;

	private $configured;

    private $core_compatible;

	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;

	}

	public function __construct() {

        // cache status
        if ( Pinterest\isPysProActive()) {
            $this->core_compatible = Pinterest\pysProVersionIsCompatible();
        } else {
            $this->core_compatible = Pinterest\pysFreeVersionIsCompatible();
        }

		parent::__construct( 'pinterest' );

		$this->locateOptions(
			PYS_PINTEREST_PATH . '/modules/pinterest/options_fields.json',
			PYS_PINTEREST_PATH . '/modules/pinterest/options_defaults.json'
		);

		// migrate after event post type registered
		add_action( 'pys_register_pixels', 'PixelYourSite\Pinterest\maybeMigrate' );

		add_action( 'pys_register_plugins', function( $core ) {
			/** @var PYS $core */
			$core->registerPlugin( $this );
		} );

        if ( ! $this->core_compatible ) {
            return;
        }

		add_action( 'pys_register_pixels', function( $core ) {
			/** @var PYS $core */
			$core->registerPixel( $this );
		} );

		if ( $this->configured() ) {

			// output debug info
			add_action( 'wp_head', function() {
				echo "<script type='text/javascript'>console.log('PixelYourSite Pinterest version " . PYS_PINTEREST_VERSION . "');</script>\r\n";
			}, 2 );

			// load addon's public JS
			add_action( 'wp_enqueue_scripts', function() {
				wp_enqueue_script( 'pys-pinterest', PYS_PINTEREST_URL . '/dist/scripts/public.js', array( 'pys' ),
					PYS_PINTEREST_VERSION );
			} );

		}

        add_action( 'pys_admin_pixel_ids', 'PixelYourSite\Pinterest\renderPixelIdField' );
        add_filter( 'pys_admin_secondary_nav_tabs', 'PixelYourSite\Pinterest\adminSecondaryNavTabs' );
        add_action( 'pys_admin_pinterest_settings', 'PixelYourSite\Pinterest\renderSettingsPage' );
        add_action( 'wp_head', array( $this, 'output_meta_tag' ) );
	}

    /**
     * Returns cached core compatibility status.
     *
     * @return bool
     */
    public function getCoreCompatible() {
        return $this->core_compatible;
    }

	public function enabled() {
		return $this->getOption( 'enabled' );
	}

	public function configured() {

        $license_status = $this->getOption( 'license_status' );
        $pixel_id = $this->getAllPixels();
        $disabledPixel =  apply_filters( 'pys_pixel_disabled', '', $this->getSlug() );
        $this->configured = $this->enabled()
                            && ! empty( $license_status ) // license was activated before
                            && ! empty( $pixel_id )
                            && $disabledPixel != '1' && $disabledPixel != 'all';

		return $this->configured;

	}

	public function getPixelIDs() {

		$ids = (array) $this->getOption( 'pixel_id' );

		if ( isSuperPackActive() && SuperPack()->getOption( 'enabled' ) && SuperPack()->getOption( 'additional_ids_enabled' ) ) {
			return $ids;
		} else {
			return (array) reset( $ids ); // return first id only
		}

	}

    public function getAllPixels() {
        return $this->getPixelIDs();
    }

    /**
     * @param SingleEvent $event
     */
    public function getAllPixelsForEvent($event) {
        return $this->getPixelIDs();
    }

	public function getPixelOptions() {

		return array(
			'pixelIds'            => $this->getPixelIDs(),
			'advancedMatching'    => $this->getOption( 'enhanced_matching_enabled' ) ? Pinterest\getEnhancedMatchingParams() : array(),
			'contentParams'       => Pinterest\getTheContentParams(),
            'wooVariableAsSimple' => $this->getOption( 'woo_variable_as_simple' ),
		);

	}

	public function getPluginName() {
		return 'PixelYourSite Pinterest Add-On';
	}

	public function getPluginFile() {
		return PYS_PINTEREST_PLUGIN_FILE;
	}

	public function getPluginVersion() {
		return PYS_PINTEREST_VERSION;
	}

	public function adminUpdateLicense() {

		if ( PYS()->adminSecurityCheck() ) {
			updateLicense( $this );
		}

	}

    public function adminRenderPluginOptions() {
        // for backward compatibility with PRO < 7.0.6
    }

    public function updatePlugin() {
        // for backward compatibility with PRO < 7.0.6
    }

	/**
	 * @param CustomEvent $event
	 */
	public function renderCustomEventOptions( $event ) {

		/** @noinspection PhpIncludeInspection */
		include PYS_PINTEREST_PATH . '/modules/pinterest/views/html-main-events-edit.php';

	}

    /**
     * Create pixel event and fill it
     * @param SingleEvent $event
     */
    public function generateEvents($event) {
        $pixelEvents = [];
        $pixelIds = $this->getAllPixelsForEvent($event);

        $disabledPixel =  apply_filters( 'pys_pixel_disabled', '', $this->getSlug() );

        // filter disabled pixels
        if(!empty($disabledPixel)) {
            foreach ($pixelIds as $key => $value) {
                if($value == $disabledPixel) {
                    array_splice($pixelIds,$key,1);
                }
            }
        }

        if(count($pixelIds) > 0) {
            $pixelEvent = clone $event;
            if($this->addParamsToEvent($pixelEvent)) {
                $pixelEvent->addPayload([ 'pixel_ids' => $pixelIds ]);
                $pixelEvents[] = $pixelEvent;
            }
        }

        return $pixelEvents;
    }

    /**
     * @param SingleEvent $event
     * @return bool
     */
    public function addParamsToEvent(&$event) {
        if ( ! $this->configured() ) {
            return false;
        }
        $isActive = false;
        switch ($event->getId()) {
            
            case 'woo_view_content':{
                $eventData = $this->getWooPageVisitEventParams();
                if ($eventData) {
                    $isActive = true;
                    $this->addDataToEvent($eventData, $event);
                }
            }break;

            case 'woo_add_to_cart_on_cart_page':
            case 'woo_add_to_cart_on_checkout_page':{
                if($event->args == null) {
                    $eventData = $this->getWooAddToCartOnCartEventParams();
                    if ($eventData) {
                        $isActive = true;
                        $this->addDataToEvent($eventData, $event);
                    }
                } else {
                    $isActive = $this->setWooAddToCartOnCartEventParams($event);
                }

            }break;

            case 'woo_remove_from_cart':{

                if(is_a($event,GroupedEvent::class)) { //deprecate
                    foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                        $eventData =  $this->getWooRemoveFromCartParams( $cart_item );
                        if ($eventData) {
                            $child = new SingleEvent($cart_item_key,EventTypes::$DYNAMIC);
                            $isActive = true;
                            $this->addDataToEvent($eventData, $child);
                            $event->addEvent($child);
                        }
                    }
                } else {
                    $isActive =  $this->setWooRemoveFromCartParams( $event );
                }

            }break;

            case 'woo_view_category':{
                $eventData = $this->getWooViewCategoryEventParams();
                if ($eventData) {
                    $isActive = true;
                    $this->addDataToEvent($eventData, $event);
                }
            }break;

            case 'woo_initiate_checkout':{
                if($event->args == null) {
                    $eventData = $this->getWooInitiateCheckoutEventParams();
                    if ($eventData) {
                        $isActive = true;
                        $this->addDataToEvent($eventData, $event);
                    }

                } else {
                    $isActive = $this->setWooInitiateCheckoutEventParams($event);
                }


            }break;

            case 'woo_purchase':{
                if(empty($event->args['order_id'])) {
                    $isActive = $this->getWooCheckoutEventParams($event);
                } else {
                    $isActive = $this->addWooCheckoutEventParams($event);
                }

            }break;

            case 'woo_paypal':{
                $isActive = $this->setWooPayPalEventParams($event);

            }break;

            case 'woo_frequent_shopper':
            case 'woo_vip_client':
            case 'woo_big_whale':{
                $eventData = $this->getWooAdvancedMarketingEventParams( $event->getId() );
                if ($eventData) {
                    $isActive = true;
                    $this->addDataToEvent($eventData, $event);
                }
            }break;
            case 'edd_view_content':{
                $eventData = $this->getEddPageVisitEventParams();
                if ($eventData) {
                    $isActive = true;
                    $this->addDataToEvent($eventData, $event);
                }
            }break;

            case 'edd_add_to_cart_on_checkout_page':{
                $isActive = $this->setEddCartEventParams( $event );//'AddToCart'

            }break;

            case 'edd_remove_from_cart':{
                if(is_a($event,GroupedEvent::class)) {
                    foreach ( edd_get_cart_contents() as $cart_item_key => $cart_item ) {
                        $eventData =  $this->getEddRemoveFromCartParams( $cart_item );
                        if ($eventData) {
                            $child = new SingleEvent($cart_item_key,EventTypes::$DYNAMIC);
                            $isActive = true;
                            $this->addDataToEvent($eventData, $child);
                            $event->addEvent($child);
                        }
                    }
                } else {
                    $isActive = $this->setEddRemoveFromCartParams( $event );
                }
            }break;

            case 'edd_view_category':{
                $eventData = $this->getEddViewCategoryEventParams();
                if ($eventData) {
                    $isActive = true;
                    $this->addDataToEvent($eventData, $event);
                }
            }break;

            case 'edd_initiate_checkout':{
                $isActive = $this->setEddCartEventParams( $event );//InitiateCheckout

            }break;

            case 'edd_purchase':{
                $isActive = $this->setEddCartEventParams( $event ); //'Checkout'

            }break;

            case 'edd_frequent_shopper':
            case 'edd_vip_client':
            case 'edd_big_whale':{
                $isActive = $this->setEddCartEventParams( $event );

            }break;
            case 'search_event':{
                $eventData = $this->getSearchEventParams();
                if ($eventData) {
                    $isActive = true;
                    $this->addDataToEvent($eventData, $event);
                }
            }break;

            case 'custom_event':{
                $eventData =  $this->getCustomEventParams( $event->args );
                if ($eventData) {
                    $isActive = true;
                    $this->addDataToEvent($eventData, $event);
                }
            }break;
            case 'woo_add_to_cart_on_button_click':{
                if (  $this->getOption( 'woo_add_to_cart_enabled' ) && PYS()->getOption( 'woo_add_to_cart_on_button_click' ) ) {
                    $isActive = true;

                    if(isset($event->args['productId'])) { // use for old main pixel plugin
                        $productId = $event->args['productId'];
                        $quantity = $event->args['quantity'];
                        $eventData =  $this->getWooAddToCartOnButtonClickEventParams( $productId,$quantity );

                        if($eventData) {
                            $event->addParams($eventData["params"]);
                            unset($eventData["params"]);
                            $event->addPayload($eventData);
                        }
                    }


                    $event->addPayload(array(
                        'name'=>"AddToCart"
                    ));
                }
            }break;

            case 'woo_affiliate':{
                if($this->getOption( 'woo_affiliate_enabled' )){
                    $isActive = true;
                    if(isset($event->args['productId'])) {
                        $productId = $event->args['productId'];
                        $quantity = $event->args['quantity'];
                        $eventData = $this->getWooAffiliateEventParams( $productId,$quantity );
                        if($eventData) {
                            $event->addParams($eventData["params"]);
                            unset($eventData["params"]);
                            $event->addPayload($eventData);
                        }
                    }

                }
            }break;

            case 'edd_add_to_cart_on_button_click':{
                if (  $this->getOption( 'edd_add_to_cart_enabled' ) && PYS()->getOption( 'edd_add_to_cart_on_button_click' ) ) {
                    $isActive = true;
                    if($event->args != null) {
                        $event->addParams($this->getEddAddToCartOnButtonClickEventParams( $event->args ));
                    }
                    $event->addPayload(array(
                        'name'=>"AddToCart"
                    ));
                }
            }break;

            case 'wcf_view_content': {
                $isActive =  $this->getWcfViewContentEventParams($event);
            }break;

            case 'wcf_add_to_cart_on_bump_click':
            case 'wcf_add_to_cart_on_next_step_click': {
                $isActive = $this->prepare_wcf_add_to_cart($event);
            }break;

            case 'wcf_remove_from_cart_on_bump_click': {
                $isActive = $this->prepare_wcf_remove_from_cart($event);
            }break;

            case 'wcf_lead': {
                $isActive = PYS()->getOption('wcf_lead_enabled');
            }break;

            case 'wcf_step_page': {
                $isActive = $this->getOption('wcf_step_event_enabled');
            }break;

            case 'wcf_bump': {
                $isActive = $this->getOption('wcf_bump_event_enabled');
            }break;

            case 'wcf_page': {
                $isActive = $this->getOption('wcf_cart_flows_event_enabled');
            }break;
        }


        return $isActive;
    }

    private function addDataToEvent($eventData,&$event) {
        $params = $eventData["data"];
        unset($eventData["data"]);
        //unset($eventData["name"]);
        $event->addParams($params);
        $event->addPayload($eventData);
    }

	public function getEventData( $eventType, $args = null ) {
        return false;
	}

	public function outputNoScriptEvents() {

		if ( ! $this->configured() ) {
			return;
		}

		$eventsManager = PYS()->getEventsManager();

		foreach ( $eventsManager->getStaticEvents( 'pinterest' ) as $eventId => $events ) {

			foreach ( $events as $event ) {
				foreach ( $this->getPixelIDs() as $pixelID ) {

					$args = array(
						'tid'      => $pixelID,
						'event'    => urlencode( $event['name'] ),
						'noscript' => 1,
					);

					if(isset($event['params']['post_type']) &&
                        isset($event['params']["line_items"]) &&
                        $event['params']['post_type'] == "product")
					{

					    $lineItems = $event['params']["line_items"];
					    foreach ( $lineItems as $index => $product) {
                            foreach ($product as $param => $value) {
                                @$args['ed[line_items][' . $index . '][' . $param . ']'] = urlencode($value);
                            }
                        }
					    //  InitiateCheckout event
					    if(isset($event['params']['num_items'])) {
                            @$args['ed[order_quantity]'] = urlencode($event['params']['num_items']);
                        }
                        if(isset($event['params']['subtotal'])) {
                            @$args['ed[value]'] = urlencode($event['params']['subtotal']);
                        }

                        // FrequentShopper,VipClient,BigWhale,Checkout
                        if(isset($event['params']['order_quantity'])) {
                            @$args['ed[order_quantity]'] = urlencode($event['params']['order_quantity']);
                        }
                        if(isset($event['params']['total'])) {
                            @$args['ed[value]'] = urlencode($event['params']['total']);
                        }
                        if(isset($event['params']['currency'])) {
                            @$args['ed[currency]'] = urlencode($event['params']['currency']);
                        }
                    } else {
                        foreach ( $event['params'] as $param => $value ) {
                            if(is_array($value))
                                $value = json_encode($value);
                            @$args[ 'ed[' . $param . ']' ] = urlencode( $value );
                        }
                    }

                    $src = add_query_arg( $args, 'https://ct.pinterest.com/v3/' );
                    $src = str_replace("[","%5B",$src);
                    $src = str_replace("]","%5D",$src);

					// ALT tag used to pass ADA compliance
					printf( '<noscript><img height="1" width="1" style="display: none;" src="%s" alt="pinterest_pixel"></noscript>',
                        $src);

					echo "\r\n";

				}
			}
		}


	}

	public function renderAddonNotice() {}



	private function getSearchEventParams() {

		if ( ! $this->getOption( 'search_event_enabled' ) ) {
			return false;
		}
        $params = array();
        $params['search'] = empty( $_GET['s'] ) ? null : $_GET['s'];

		return array(
			'name'  => 'search',
			'data'  => $params,
		);

	}

    /**
     * @param SingleEvent $event
     * @return bool
     */
	private function getWcfViewContentEventParams(&$event) {
        if ( ! $this->getOption( 'woo_view_content_enabled' )
            || empty($event->args['products'])
        ) {
            return false;
        }
        $product_data = $event->args['products'][0];
        $params = array(
            'post_type' => 'product',
            'product_id' => getWooProductContentId( $product_data['id'] ) ,
            'product_price' => getWooProductPriceToDisplay( $product_data['id'],1,$product_data['price'] ),
            'tags' => implode( ', ', $product_data['tags'] ),
            'content_name' => $product_data['name'],
            'category_name' => implode( ', ', array_column($product_data['categories'],"name") ),
            'currency'      => $event->args['currency']
        );
        if ( PYS()->getOption( 'woo_view_content_value_enabled' ) ) {
            $value_option   = PYS()->getOption( 'woo_view_content_value_option' );
            $global_value   = PYS()->getOption( 'woo_view_content_value_global', 0 );
            $percents_value = PYS()->getOption( 'woo_view_content_value_percent', 100 );

            if(function_exists('PixelYourSite\getWooProductValue')) { // new api, can remove old
                $params['value']    = getWooProductValue( [
                    "valueOption" => $value_option,
                    "global" => $global_value,
                    "percent" => $percents_value,
                    "product_id" => $product_data['id'],
                    "qty" => $product_data['quantity'],
                    "price" => $product_data['price'],
                ] );
            } else { // old
                $params['value']    = getWooEventValue( $value_option,
                    $global_value,
                    $percents_value,
                    $product_data['id'],
                    $product_data['quantity'] );
            }


        }

        $event->addParams($params);
        $event->addPayload([
            'name'  => 'PageVisit',
            'delay' => (int) PYS()->getOption( 'woo_view_content_delay' ),
        ]);
        return true;
    }

	private function getWooPageVisitEventParams() {
		global $post;

		if ( ! $this->getOption( 'woo_view_content_enabled' ) ) {
			return false;
		}

		$productId = $post->ID;

		$params = array(
			'post_type' => 'product',
			'product_id' => getWooProductContentId( $productId ) ,
			'product_price' => getWooProductPriceToDisplay( $post->ID )
		);

		// content_name, category_name, tags
		$params['tags'] = implode( ', ', getObjectTerms( 'product_tag', $post->ID ) );
		$params = array_merge( $params, Pinterest\getWooCustomAudiencesOptimizationParams( $post->ID ) );

		// currency, value
		if ( PYS()->getOption( 'woo_view_content_value_enabled' ) ) {

			$value_option   = PYS()->getOption( 'woo_view_content_value_option' );
			$global_value   = PYS()->getOption( 'woo_view_content_value_global', 0 );
			$percents_value = PYS()->getOption( 'woo_view_content_value_percent', 100 );

			$params['value']    = getWooEventValue( $value_option, $global_value, $percents_value,$productId,1 );
			$params['currency'] = get_woocommerce_currency();

		}

		return array(
			'name'  => 'PageVisit',
			'data'  => $params,
			'delay' => (int) PYS()->getOption( 'woo_view_content_delay' ),
		);

	}

    /**
     * @param SingleEvent $event
     */
	private function prepare_wcf_add_to_cart(&$event) {
        if(  !$this->getOption( 'woo_add_to_cart_enabled' )
            || empty($event->args['products'])
        ) {
            return false; // return if args is empty
        }

        $params = array(
            'post_type'        => 'product',
        );
        $value = 0;
        $line_items = [];
        // set option names
        $value_enabled_option =  'woo_add_to_cart_value_enabled';
        $value_option_option  =  'woo_add_to_cart_value_option';
        $value_global_option  =  'woo_add_to_cart_value_global';
        $value_percent_option =  'woo_add_to_cart_value_percent';

        foreach ($event->args['products'] as $product_data) {
            $content_id = getWooProductContentId($product_data['id']);

            $line_item = array(
                'product_id' => $content_id,
                'product_quantity' => $product_data['quantity'],
                'product_price' => getWooProductPriceToDisplay( $product_data['id'], 1,$product_data['price'] ),
                'product_name' => $product_data['name'],
                'product_category' => implode( ', ',array_column($product_data['categories'],"name")),
                'tags' => implode( ', ', $product_data['tags'] )
            );



            // currency, value
            if ( PYS()->getOption( $value_enabled_option ) ) {

                $value_option   = PYS()->getOption( $value_option_option );
                $global_value   = PYS()->getOption( $value_global_option, 0 );
                $percents_value = PYS()->getOption( $value_percent_option, 100 );


                if(function_exists('PixelYourSite\getWooProductValue')) { // new api, can remove old
                    $value    += getWooProductValue( [
                        "valueOption" => $value_option,
                        "global" => $global_value,
                        "percent" => $percents_value,
                        "product_id" => $product_data['id'],
                        "qty" => $product_data['quantity'],
                        "price" => $product_data['price'],
                    ] );

                } else { // old use in free

                    $value    += getWooEventValue( $value_option,
                        $global_value,
                        $percents_value,
                        $product_data['id'],
                        $product_data['quantity'] );
                    $line_item['val2'] = $value;
                }



            }

            $line_items[] = $line_item;
        }

        if(count($line_items)  == 1) {
            $params = array_merge($params,$line_items[0]);
        }  else {
            $params['line_items'] = $line_items;
        }
        if ( PYS()->getOption( $value_enabled_option ) ) {
            $params["value"] = $value ;
            $params['currency'] = get_woocommerce_currency();
        }


        $event->addParams($params);
        $event->addPayload([
            'name' => 'AddToCart',
        ]);

        return true;
    }

	private function getWooAddToCartOnButtonClickEventParams( $product_id ,$quantity) {

		$params = Pinterest\getWooSingleAddToCartParams( $product_id, $quantity, false );

		$data = array(
            'params' => $params,
        );

		$product = wc_get_product($product_id);
        if($product->get_type() == 'grouped') {
            $grouped = array();
            foreach ($product->get_children() as $childId) {
                $grouped[$childId] = array(
                    'content_id' => getWooProductContentId( $childId ),
                    'price' => getWooProductPriceToDisplay( $childId )
                );
            }
            $data['grouped'] = $grouped;
        }

		return $data;

	}

    /**
     * @deprecated
     * @return array|false
     */
    private function getWooAddToCartOnCartEventParams() {

        if ( ! $this->getOption( 'woo_add_to_cart_enabled' ) ) {
            return false;
        }

        $params = Pinterest\getWooCartParamsOld();

        return array(
            'name' => 'AddToCart',
            'data' => $params,
        );

    }

    /**
     * @param SingleEvent $event
     * @return boolean
     */
	private function setWooAddToCartOnCartEventParams(&$event) {

		if ( ! $this->getOption( 'woo_add_to_cart_enabled' ) ) {
			return false;
		}

		$params = Pinterest\getWooCartParams($event);

        $event->addParams($params);
        $event->addPayload(['name' => 'AddToCart']);

        return true;

	}

    /**
     * @param SingleEvent $event
     * @return bool
     */
	private function prepare_wcf_remove_from_cart(&$event) {
        if ( ! $this->getOption( 'woo_remove_from_cart_enabled' )
            || empty($event->args['products'])
        ) {
            return false;
        }

        $product_data = $event->args['products'][0];
        $product_id = $product_data['id'];
        $content_id = getWooProductContentId( $product_id );
        $price = getWooProductPriceToDisplay( $product_data['id'],1,$product_data['price'] );

        $params = array(
            'post_type'        => 'product',
            'product_id'       => $content_id,
            'product_quantity' => $product_data['quantity'],
            'product_price' => $price,
            'product_name'     => $product_data['name'],
            'product_category' => implode( ', ', array_column($product_data['categories'],"name") ),
            'tags' => implode( ', ', $product_data['tags'] )
        );

        $event->addParams($params);

        $event->addPayload([
            'name' => "RemoveFromCart",
        ]);

        return true;
    }

    /**
     * @param SingleEvent $event
     * @return bool
     */
    private function setWooRemoveFromCartParams(&$event) {
        if ( ! $this->getOption( 'woo_remove_from_cart_enabled' ) ) {
            return false;
        }

        $data = $this->getWooRemoveFromCartParams($event->args['item']);

        $event->addParams($data['data']);
        $event->addPayload(['name' => $data['name']]);
        return true;
    }

	private function getWooRemoveFromCartParams( $cart_item ) {

		if ( ! $this->getOption( 'woo_remove_from_cart_enabled' ) ) {
			return false;
		}
        $product_id = Pinterest\getWooCartItemId( $cart_item );
        $content_id = getWooProductContentId( $product_id );


        $price = getWooProductPriceToDisplay( $product_id );

		// content_name, category_name, tags
		$cd_params = Pinterest\getWooCustomAudiencesOptimizationParams( $product_id );

		$params = array(
			'post_type'        => 'product',
			'product_id'       => $content_id,
			'product_quantity' => $cart_item['quantity'],
			'product_price' => $price,
			'product_name'     => $cd_params['content_name'],
			'product_category' => $cd_params['category_name'],
			'tags' => implode( ', ', getObjectTerms( 'product_tag', $product_id ) )
		);

		return array(
		    'name' => "RemoveFromCart",
		    'data' => $params );

	}

	private function getWooViewCategoryEventParams() {
		global $posts;

		if ( ! $this->getOption( 'woo_view_category_enabled' ) ) {
			return false;
		}

		$params = array(
			'post_type' => 'product',
		);

		$term = get_term_by( 'slug', get_query_var( 'term' ), 'product_cat' );

		if(!$term) return false;

		$params['content_name'] = $term->name;

		$parent_ids = get_ancestors( $term->term_id, 'product_cat', 'taxonomy' );
		$params['content_category'] = array();

		foreach ( $parent_ids as $term_id ) {
			$term = get_term_by( 'id', $term_id, 'product_cat' );
			$params['content_category'][] = $term->name;
		}

		$params['content_category'] = implode( ', ', $params['content_category'] );

		$product_ids = array();
		$limit = min( count( $posts ), 5 );

		for ( $i = 0; $i < $limit; $i ++ ) {
			$product_ids[] = Pinterest\getWooProductContentId($posts[ $i ]->ID);
		}

		$params['product_ids'] = implode( ', ', $product_ids);

		return array(
			'name' => 'ViewCategory',
			'data' => $params,
		);

	}



    /**
     * @param SingleEvent $event
     * @return boolean
     */
	private function setWooInitiateCheckoutEventParams(&$event) {

		if ( ! $this->getOption( 'woo_initiate_checkout_enabled' ) ) {
			return false;
		}

		$params = Pinterest\getWooCartParams( $event );

        $event->addParams($params);
        $event->addPayload(['name' => 'InitiateCheckout',]);

        return true;
	}

    /**
     * @deprecated
     * @return array|false
     */
    private function getWooInitiateCheckoutEventParams() {

        if ( ! $this->getOption( 'woo_initiate_checkout_enabled' ) ) {
            return false;
        }

        $params = Pinterest\getWooCartParamsOld( 'InitiateCheckout' );

        return array(
            'name' => 'InitiateCheckout',
            'data' => $params,
        );

    }

    /**
     * @deprecated use addWooCheckoutEventParams
     * @param SingleEvent $event
     * @return array|false
     */
    private function getWooCheckoutEventParams($event) {

        if ( ! $this->getOption( 'woo_purchase_enabled' ) ) {
            return false;
        }
        $order_key = sanitize_key($_REQUEST['key']);
        $order_id = (int) wc_get_order_id_by_order_key( $order_key );

        $order    = new \WC_Order( $order_id );

        $params = array(
            'post_type' => 'product',
        );

        $num_items = 0;
        $line_items = array();
        $order_total = (float) $order->get_total( 'edit' );
        $order_tax = (float) $order->get_total_tax( 'edit' );

        foreach ( $order->get_items( 'line_item' ) as $item ) {

            $product_id = Pinterest\getWooCartItemId( $item );
            $content_id = getWooProductContentId( $product_id );

            // content_name, category_name, tags
            $cd_params = Pinterest\getWooCustomAudiencesOptimizationParams( $product_id );
            $tags      = getObjectTerms( 'product_tag', $product_id );

            $line_item = array(
                'product_id'       => $content_id,
                'product_quantity' => $item['qty'],
                'product_price'    => getWooProductPriceToDisplay( $product_id, 1 ),
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


        $value_option   = PYS()->getOption( 'woo_purchase_value_option' );
        $global_value   = PYS()->getOption( 'woo_purchase_value_global', 0 );
        $percents_value = PYS()->getOption( 'woo_purchase_value_percent', 100 );

        $params['value'] = getWooEventValueOrder( $value_option, $order, $global_value, $percents_value );


        $params['town']    = $order->get_billing_city();
        $params['state']   = $order->get_billing_state();
        $params['country'] = $order->get_billing_country();
        $params['payment'] = $order->get_payment_method_title();

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

        $params['total'] = $order_total;
        $params['tax']   = $order_tax;

        $params['shipping_cost'] = (float) $order->get_shipping_total( 'edit' ) + (float) $order->get_shipping_tax( 'edit' );

        if( PYS()->getOption("enable_woo_transactions_count_param")
            || PYS()->getOption("enable_woo_predicted_ltv_param")
            || PYS()->getOption("enable_woo_average_order_param")) {
            $customer_params = PYS()->getEventsManager()->getWooCustomerTotals();

            $params['lifetime_value']     = $customer_params['ltv'];
            $params['average_order']      = $customer_params['avg_order_value'];
            $params['transactions_count'] = $customer_params['orders_count'];
        }


        $event->addParams($params);
        $event->addPayload([
                'name' => 'Checkout',
            ]
        );
        return true;

    }

    /**
     * @param SingleEvent $event
     * @return array|false
     */
	private function addWooCheckoutEventParams(&$event){
        if ( ! $this->getOption( 'woo_purchase_enabled' ) ) {
            return false;
        }

        $line_items = [];
        $order_quantity = 0;
        $value_option   = PYS()->getOption( 'woo_purchase_value_option' );
        $global_value   = PYS()->getOption( 'woo_purchase_value_global', 0 );
        $percents_value = PYS()->getOption( 'woo_purchase_value_percent', 100 );
        $withTax = 'incl' === get_option( 'woocommerce_tax_display_cart' );
        $tax = 0;
        foreach ($event->args['products'] as $product_data) {

            $product_id = Pinterest\getWooCartItemId( $product_data );
            $content_id = getWooProductContentId( $product_id );
            $price = $product_data['subtotal'];
            if($withTax) {
                $price+=$product_data['subtotal_tax'];
            }
            $line_items[] = [
                'product_id'       => $content_id,
                'product_quantity' => $product_data['quantity'],
                'product_price'    => pinterest_round($price/$product_data['quantity']),
                'product_name'     => $product_data['name'],
                'product_category' => implode( ', ', array_column($product_data['categories'],'name') ),
                'tags'             => implode( ', ', $product_data['tags'] )
            ];
            $order_quantity += $product_data['quantity'];
            $tax += $product_data['total_tax'];
        }
        $tax+=$event->args['shipping_tax'];
        $shipping_cost = $event->args['shipping_cost'];
        if($withTax) {
            $shipping_cost += $event->args['shipping_tax'];
        }
        $total = Pinterest\getWooEventOrderTotal($event);
        if(function_exists('PixelYourSite\getWooEventValueProducts')) {
            $value = getWooEventValueProducts($value_option,$global_value,$percents_value,$total,$event->args);
        } else {
            // remove after update free
            $value = getWooEventValueOrder( $value_option,wc_get_order($event->args['order_id']), $global_value, $percents_value);
        }


        $params = array(
            'post_type'         => 'product',
            'line_items'        => $line_items,
            'order_quantity'    => $order_quantity,
            'currency'          => $event->args['currency'],
            'town'              => $event->args['town'],
            'state'             => $event->args['state'],
            'country'           => $event->args['country'],
            'payment'           => $event->args['payment_method'],
            'shipping'          => $event->args['shipping'],
            'value'             => pinterest_round($value),
            'promo_code_used'   => $event->args['coupon_used'],
            'promo_code'        => $event->args['coupon_name'],
            'total'             => pinterest_round($total),
            'tax'               => pinterest_round($tax),
            'shipping_cost'     => $shipping_cost,
            'lifetime_value'    => isset($event->args['predicted_ltv']) ? $event->args['predicted_ltv'] : "",
            'average_order'     => isset($event->args['average_order']) ? $event->args['average_order'] : "",
            'transactions_count'=> isset($event->args['transactions_count']) ? $event->args['transactions_count'] : "",
        );
        $event->addParams($params);
        $event->addPayload([
                'name' => 'Checkout',
            ]
        );
        return true;
    }


	private function getWooAffiliateEventParams( $product_id ,$quantity) {

		if ( ! $this->getOption( 'woo_affiliate_enabled' ) ) {
			return false;
		}

		$params = Pinterest\getWooSingleAddToCartParams( $product_id, $quantity, true );

		return array(
			'params' => $params,
		);

	}

    /**
     * @param SingleEvent $event
     * @return bool
     */
	private function setWooPayPalEventParams(&$event) {

		if ( ! $this->getOption( 'woo_paypal_enabled' ) ) {
			return false;
		}

		// we're using Cart date as of Order not exists yet
		$params = Pinterest\getWooCartParams( $event );

        $event->addParams($params);
        $event->addPayload(['name' => getWooPayPalEventName()]);

        return true;
	}

	private function getWooAdvancedMarketingEventParams( $eventType ) {

		if ( ! $this->getOption( $eventType . '_enabled' ) ) {
			return false;
		}

		$params = Pinterest\getWooPurchaseParams( $eventType );

		switch ( $eventType ) {
			case 'woo_frequent_shopper':
				$eventName = 'FrequentShopper';
				break;

			case 'woo_vip_client':
				$eventName = 'VipClient';
				break;

			default:
				$eventName = 'BigWhale';
		}

		return array(
			'name' => $eventName,
			'data' => $params,
		);

	}

	/**
	 * @param CustomEvent $customEvent
	 *
	 * @return array|bool
	 */
	private function getCustomEventParams( $customEvent ) {

		$event_type = $customEvent->getPinterestEventType();

		if ( ! $customEvent->isPinterestEnabled() || empty( $event_type ) ) {
			return false;
		}

		$params = array();

		// add pixel params
		if ( $customEvent->isPinterestParamsEnabled() ) {

			// add custom params
            $customParams = $customEvent->getPinterestCustomParams();

			foreach ( $customParams as $custom_param ) {
				$params[ $custom_param['name'] ] = $custom_param['value'];
			}

		}

		// SuperPack Dynamic Params feature
		$params = apply_filters( 'pys_superpack_dynamic_params', $params, 'pinterest' );

		return array(
			'name'  => $customEvent->getPinterestEventType(),
			'data'  => $params,
			'delay' => $customEvent->getDelay(),
		);

	}


	private function getEddPageVisitEventParams() {
		global $post;

		if ( ! $this->getOption( 'edd_page_visit_enabled' ) ) {
			return false;
		}

		$params = array(
			'post_type'  => 'product',
			'product_id' => Pinterest\getEddDownloadContentId($post->ID)
		);

		// content_name, category_name, tags
		$params['tags'] = implode( ', ', getObjectTerms( 'download_tag', $post->ID ) );
		$params = array_merge( $params, Pinterest\getEddCustomAudiencesOptimizationParams( $post->ID ) );

		// currency, value
		if ( PYS()->getOption( 'edd_view_content_value_enabled' ) ) {

			if( PYS()->getOption( 'edd_event_value' ) == 'custom' ) {
				$amount = getEddDownloadPrice( $post->ID );
			} else {
				$amount = getEddDownloadPriceToDisplay( $post->ID );
			}

			$value_option   = PYS()->getOption( 'edd_view_content_value_option' );
			$global_value   = PYS()->getOption( 'edd_view_content_value_global', 0 );
			$percents_value = PYS()->getOption( 'edd_view_content_value_percent', 100 );

			$params['value'] = getEddEventValue( $value_option, $amount, $global_value, $percents_value );
			$params['currency'] = edd_get_currency();

		}

		$params['product_price'] = getEddDownloadPriceToDisplay( $post->ID );

		return array(
			'name'  => 'PageVisit',
			'data'  => $params,
			'delay' => (int) PYS()->getOption( 'edd_view_content_delay' ),
		);

	}

	private function getEddAddToCartOnButtonClickEventParams( $download_id ) {
		global $post;

		// maybe extract download price id
		if ( strpos( $download_id, '_') !== false ) {
			list( $download_id, $price_index ) = explode( '_', $download_id );
		} else {
			$price_index = null;
		}

		// content_name, category_name, tags
		$cd_params = Pinterest\getEddCustomAudiencesOptimizationParams( $download_id );

		$params = array(
			'post_type'        => 'product',
			'product_id'       => Pinterest\getEddDownloadContentId($download_id),
			'product_quantity' => 1,
			'product_name'     => $cd_params['content_name'],
			'product_category' => $cd_params['category_name'],
			'product_price' => getEddDownloadPriceToDisplay( $download_id, $price_index ),
			'tags' => implode( ', ', getObjectTerms( 'download_tag', $download_id ) ),
		);

		// currency, value
		if ( PYS()->getOption( 'edd_add_to_cart_value_enabled' ) ) {

			if( PYS()->getOption( 'edd_event_value' ) == 'custom' ) {
				$amount = getEddDownloadPrice( $download_id, $price_index );
			} else {
				$amount = getEddDownloadPriceToDisplay( $download_id, $price_index );
			}

			$value_option   = PYS()->getOption( 'edd_add_to_cart_value_option' );
			$percents_value = PYS()->getOption( 'edd_add_to_cart_value_percent', 100 );
			$global_value   = PYS()->getOption( 'edd_add_to_cart_value_global', 0 );

			$params['value'] = getEddEventValue( $value_option, $amount, $global_value, $percents_value );
			$params['currency'] = edd_get_currency();

		}

		$license = getEddDownloadLicenseData( $download_id );
		$params  = array_merge( $params, $license );

		return $params;

	}

    function getEddCartEventParams($context,$value_enabled,$value_option, $global_value, $percents_value) {
        $params = [];
        if ( $context == 'AddToCart' || $context == 'InitiateCheckout' ) {
            $cart = edd_get_cart_contents();
        } else {
            $cart = edd_get_payment_meta_cart_details( edd_get_purchase_id_by_key( getEddPaymentKey() ), true );
        }

        $line_items = array();

        $num_items   = 0;
        $total       = 0;
        $total_as_is = 0;

        $licenses = array(
            'transaction_type'   => null,
            'license_site_limit' => null,
            'license_time_limit' => null,
            'license_version'    => null
        );

        foreach ( $cart as $cart_item_key => $cart_item ) {

            $download_id = (int) $cart_item['id'];

            $price_index = ! empty( $cart_item['options'] ) ? $cart_item['options']['price_id'] : null;

            // content_name, category_name, tags
            $cd_params = Pinterest\getEddCustomAudiencesOptimizationParams( $download_id );
            $tags = getObjectTerms( 'download_tag', $download_id );

            $line_item = array(
                'product_id'       => Pinterest\getEddDownloadContentId($download_id),
                'product_quantity' => $cart_item['quantity'],
                'product_price'    => getEddDownloadPriceToDisplay( $download_id, $price_index ),
                'product_name'     => $cd_params['content_name'],
                'product_category' => $cd_params['category_name'],
                'tags'             => implode( ', ', $tags )
            );

            $line_items[] = $line_item;

            $num_items += $cart_item['quantity'];

            // calculate cart items total
            if ( $value_enabled ) {

                if ( $context == 'Checkout' ) {

                    if ( PYS()->getOption( 'edd_tax_option' ) == 'included' ) {
                        $total += $cart_item['subtotal'] + $cart_item['tax'] - $cart_item['discount'];
                    } else {
                        $total += $cart_item['subtotal'] - $cart_item['discount'];
                    }

                    $total_as_is += $cart_item['price'];

                } else {

                    $total += getEddDownloadPrice( $download_id, $price_index ) * $cart_item['quantity'];
                    $total_as_is += edd_get_cart_item_final_price( $cart_item_key );

                }

            }

            // get download license data
            array_walk( $licenses, function( &$value, $key, $license ) {

                if ( ! isset( $license[ $key ] ) ) {
                    return;
                }

                if ( $value ) {
                    $value = $value . ', ' . $license[ $key ];
                } else {
                    $value = $license[ $key ];
                }

            }, getEddDownloadLicenseData( $download_id ) );

        }

        $params['line_items'] = $line_items;
        $params['num_items'] = $num_items;

        // currency, value
        if ( $value_enabled ) {

            if( PYS()->getOption( 'edd_event_value' ) == 'custom' ) {
                $amount = $total;
            } else {
                $amount = $total_as_is;
            }

            $params['value']    = getEddEventValue( $value_option, $amount, $global_value, $percents_value );
            $params['currency'] = edd_get_currency();

        }

        $params = array_merge( $params, $licenses );

        if ( $context == 'Checkout' ) {

            $payment_key = getEddPaymentKey();
            $payment_id = (int) edd_get_purchase_id_by_key( $payment_key );
            $session  = edd_get_purchase_session();

            $user = edd_get_payment_meta_user_info( $payment_id );
            $meta = edd_get_payment_meta( $payment_id );

            // town, state, country
            if ( isset( $user['address'] ) ) {

                if ( ! empty( $user['address']['city'] ) ) {
                    $params['town'] = $user['address']['city'];
                }

                if ( ! empty( $user['address']['state'] ) ) {
                    $params['state'] = $user['address']['state'];
                }

                if ( ! empty( $user['address']['country'] ) ) {
                    $params['country'] = $user['address']['country'];
                }

            }

            // payment method
            if ( isset( $session['gateway'] ) ) {
                $params['payment'] = $session['gateway'];
            }

            // coupons
            $coupons = isset( $user['discount'] ) && $user['discount'] != 'none' ? $user['discount'] : null;

            if ( ! empty( $coupons ) ) {
                $coupons = explode( ', ', $coupons );
                $params['coupon'] = $coupons[0];
            }

            // add transaction date
            $params['transaction_year']  = strftime( '%Y', strtotime( $meta['date'] ) );
            $params['transaction_day']   = strftime( '%d', strtotime( $meta['date'] ) );

            $params['order_id'] = $payment_id;
            $params['currency'] = edd_get_currency();

            // calculate value
            if ( PYS()->getOption( 'edd_event_value' ) == 'custom' ) {
                $params['value'] = getEddOrderTotal( $payment_id );
            } else {
                $params['value'] = edd_get_payment_amount( $payment_id );
            }

            if ( edd_use_taxes() ) {
                $params['tax'] = edd_get_payment_tax( $payment_id );
            } else {
                $params['tax'] = 0;
            }

        }

        return $params;
    }
    /**
     * @param SingleEvent $event
     * @return bool
     */
	private function setEddCartEventParams( $event  ) {

        $data = [];
        $params = [
            'post_type' => 'product',
        ];
        $value_enabled  = false;
        $value_option   = '';
        $percents_value = 100;
        $global_value   = 0;
        switch ($event->getId()) {
            case 'edd_frequent_shopper': {
                if ( ! $this->getOption( $event->getId() . '_enabled' ) )  return false;
                $data['name'] = 'FrequentShopper';
            }break;
            case 'edd_vip_client': {
                if ( ! $this->getOption( $event->getId() . '_enabled' ) )  return false;
                $data['name'] = 'VipClient';
            }break;
            case 'edd_big_whale': {
                if ( ! $this->getOption( $event->getId() . '_enabled' ) )  return false;
                $data['name'] = 'BigWhale';
            }break;
            case 'edd_add_to_cart_on_checkout_page': {
                if ( ! $this->getOption( 'edd_add_to_cart_enabled' ) )  return false;
                $data['name'] = 'AddToCart';
                $value_enabled  = PYS()->getOption( 'edd_add_to_cart_value_enabled' );
                $value_option   = PYS()->getOption( 'edd_add_to_cart_value_option' );
                $percents_value = PYS()->getOption( 'edd_add_to_cart_value_percent', 100 );
                $global_value   = PYS()->getOption( 'edd_add_to_cart_value_global', 0 );
            }break;
            case 'edd_initiate_checkout': {
                if ( ! $this->getOption( 'edd_initiate_checkout_enabled' ) )  return false;
                $data['name'] = 'InitiateCheckout';
                $value_enabled  = PYS()->getOption( 'edd_initiate_checkout_value_enabled' );
                $value_option   = PYS()->getOption( 'edd_initiate_checkout_value_option' );
                $percents_value = PYS()->getOption( 'edd_initiate_checkout_value_percent', 100 );
                $global_value   = PYS()->getOption( 'edd_initiate_checkout_global', 0 );
            }break;
            case 'edd_purchase': {
                if ( ! $this->getOption( 'edd_checkout_enabled' ) )  return false;
                $data['name'] = 'Checkout';
                $value_enabled  = PYS()->getOption( 'edd_purchase_value_enabled' );
                $value_option   = PYS()->getOption( 'edd_purchase_value_option' );
                $percents_value = PYS()->getOption( 'edd_purchase_value_percent', 100 );
                $global_value   = PYS()->getOption( 'edd_purchase_value_global', 0 );
            }break;
        }

        if($event->args == null) { // remove when update free
            $params = array_merge($params,
                $this->getEddCartEventParams($data['name'],$value_enabled,$value_option,$global_value,$percents_value));
        } else {

            $line_items = array();
            $num_items = 0;
            $total = 0;
            $total_as_is = 0;
            $tax = 0;
            $licenses = array(
                'transaction_type' => null,
                'license_site_limit' => null,
                'license_time_limit' => null,
                'license_version' => null
            );

            foreach ($event->args['products'] as $product) {

                $download_id = (int)$product['product_id'];
                $price_index = $product['price_index'];

                // content_name, category_name, tags


                $line_items[] = array(
                    'product_id' => Pinterest\getEddDownloadContentId($download_id),
                    'product_quantity' => $product['quantity'],
                    'product_price' => getEddDownloadPriceToDisplay($download_id, $price_index),
                    'product_name' => $product['name'],
                    'product_category' => implode(', ', array_column($product['categories'], 'name')),
                    'tags' => implode(', ', $product['tags'])
                );

                $num_items += $product['quantity'];

                // calculate cart items total


                if ($event->getId() == 'edd_purchase') {

                    if (PYS()->getOption('edd_tax_option') == 'included') {
                        $total += $product['subtotal'] + $product['tax'] - $product['discount'];
                    } else {
                        $total += $product['subtotal'] - $product['discount'];
                    }
                    $tax += $product['tax'];
                    $total_as_is += $product['price'];

                } else {

                    $total += getEddDownloadPrice($download_id, $price_index) * $product['quantity'];
                    $total_as_is += edd_get_cart_item_final_price($product['cart_item_key']);

                }


                // get download license data
                array_walk($licenses, function (&$value, $key, $license) {

                    if (!isset($license[$key])) {
                        return;
                    }

                    if ($value) {
                        $value = $value . ', ' . $license[$key];
                    } else {
                        $value = $license[$key];
                    }

                }, getEddDownloadLicenseData($download_id));

            }

            $params['line_items'] = $line_items;
            $params['num_items'] = $num_items;

            // currency, value
            if ($value_enabled) {

                if (PYS()->getOption('edd_event_value') == 'custom') {
                    $amount = $total;
                } else {
                    $amount = $total_as_is;
                }

                $params['value'] = getEddEventValue($value_option, $amount, $global_value, $percents_value);
                $params['currency'] = edd_get_currency();

            }

            $params = array_merge($params, $licenses);

            if ($event->getId() == 'edd_purchase') {

                $payment_id = (int)$event->args['order_id'];
                $session = edd_get_purchase_session();

                $user = edd_get_payment_meta_user_info($payment_id);
                $meta = edd_get_payment_meta($payment_id);

                // town, state, country
                if (isset($user['address'])) {

                    if (!empty($user['address']['city'])) {
                        $params['town'] = $user['address']['city'];
                    }

                    if (!empty($user['address']['state'])) {
                        $params['state'] = $user['address']['state'];
                    }

                    if (!empty($user['address']['country'])) {
                        $params['country'] = $user['address']['country'];
                    }

                }

                // payment method
                if (isset($session['gateway'])) {
                    $params['payment'] = $session['gateway'];
                }

                // coupons
                $params['coupon'] = $event->args['coupon'];

                // add transaction date
                $params['transaction_year'] = strftime('%Y', strtotime($meta['date']));
                $params['transaction_day'] = strftime('%d', strtotime($meta['date']));

                $params['order_id'] = $payment_id;
                $params['currency'] = edd_get_currency();

                // calculate value
                if (PYS()->getOption('edd_event_value') == 'custom') {
                    $params['value'] = $total;
                } else {
                    $params['value'] = $total_as_is;
                }

                if (edd_use_taxes()) {
                    $params['tax'] = $tax;
                } else {
                    $params['tax'] = 0;
                }

            }
        }
        $event->addParams($params);
        $event->addPayload($data);
        return true;
	}

    /**
     * @param SingleEvent $event
     * @return bool
     */
    private function setEddRemoveFromCartParams(&$event) {
        if ( ! $this->getOption( 'edd_remove_from_cart_enabled' ) ) {
            return false;
        }
        $data = $this->getEddRemoveFromCartParams($event->args['item']);
        $event->addParams($data['data']);
        $event->addPayload(['name' => $data['name']]);
        return true;
    }

	private function getEddRemoveFromCartParams( $cart_item ) {

		if ( ! $this->getOption( 'edd_remove_from_cart_enabled' ) ) {
			return false;
		}

		$download_id = $cart_item['id'];
		$price_index = ! empty( $cart_item['options'] ) ? $cart_item['options']['price_id'] : null;

		// content_name, category_name, tags
		$cd_params = Pinterest\getEddCustomAudiencesOptimizationParams( $download_id );

		$params = array(
			'post_type'        => 'product',
			'product_id'       => Pinterest\getEddDownloadContentId($download_id),
			'product_quantity' => $cart_item['quantity'],
			'product_price'    => getEddDownloadPriceToDisplay( $download_id, $price_index ),
			'product_name'     => $cd_params['content_name'],
			'product_category' => $cd_params['category_name'],
			'tags'             => implode( ', ', getObjectTerms( 'download_tag', $download_id ) )
		);

		return array( 'name' => 'RemoveFromCart',
            'data' => $params );

	}

	private function getEddViewCategoryEventParams() {
		global $posts;

		if ( ! $this->getOption( 'edd_view_category_enabled' ) ) {
			return false;
		}

		$params = array(
			'post_type' => 'product',
		);

		$term = get_term_by( 'slug', get_query_var( 'term' ), 'download_category' );
		$params['content_name'] = $term->name;

		$parent_ids = get_ancestors( $term->term_id, 'download_category', 'taxonomy' );
		$params['content_category'] = array();

		foreach ( $parent_ids as $term_id ) {
			$term = get_term_by( 'id', $term_id, 'download_category' );
			$params['content_category'][] = $term->name;
		}

		$params['content_category'] = implode( ', ', $params['content_category'] );

		$product_ids = array();
		$limit       = min( count( $posts ), 5 );

		for ( $i = 0; $i < $limit; $i ++ ) {
			$product_ids[] = Pinterest\getEddDownloadContentId($posts[ $i ]->ID);
		}

		$params['product_ids'] = implode( ', ', $product_ids );

		return array(
			'name' => 'ViewCategory',
			'data' => $params,
		);

	}

    function output_meta_tag() {
        $metaTags = (array) $this->getOption( 'verify_meta_tag' );
        foreach ($metaTags as $tag) {
            echo $tag;
        }
    }

}

/**
 * @return Pinterest
 */
function Pinterest() {
	return Pinterest::instance();
}

Pinterest();
