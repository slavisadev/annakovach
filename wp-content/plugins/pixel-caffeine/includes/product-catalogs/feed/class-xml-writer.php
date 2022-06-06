<?php
/**
 * The XML writer service for the product feed
 *
 * @package Pixel Caffeine
 */

namespace PixelCaffeine\ProductCatalog\Feed;

use PixelCaffeine\ProductCatalog\DbProvider;
use PixelCaffeine\ProductCatalog\Dictionary\FeedSaver;
use PixelCaffeine\ProductCatalog\Entity\ProductCatalog as Entity;
use PixelCaffeine\ProductCatalog\Exception\EntityException;
use PixelCaffeine\ProductCatalog\Exception\FeedException;
use PixelCaffeine\ProductCatalog\FeedMapper;
use PixelCaffeine\ProductCatalog\Helper\FeedDirectoryHelper;
use PixelCaffeine\ProductCatalog\ProductCatalogManager;
use PixelCaffeine\Dependencies\Symfony\Component\Filesystem\Filesystem;

/**
 * Class XMLWriter
 *
 * @package PixelCaffeine\ProductCatalog\Feed
 */
class XMLWriter implements WriterInterface {

	const XML_NS_ATOM = 'http://www.w3.org/2005/Atom';
	const XML_NS_G    = 'http://base.google.com/ns/1.0';

	/**
	 * The product catalog entity manager
	 *
	 * @var ProductCatalogManager
	 */
	protected $product_catalog;

	/**
	 * The DB provider
	 *
	 * @var DbProvider
	 */
	protected $db_provider;

	/**
	 * The product catalog entity
	 *
	 * @var Entity
	 */
	protected $entity;

	/**
	 * The DOM of the feed content
	 *
	 * @var \DOMDocument
	 */
	protected $feed_dom;

	/**
	 * The feed directory helper
	 *
	 * @var FeedDirectoryHelper
	 */
	protected $directory_helper;

	/**
	 * The filesystem instance
	 *
	 * @var Filesystem
	 */
	protected $filesystem;

	/**
	 * WriterInterface constructor.
	 *
	 * Inizialize the Iterator useful to retrieve the items to include in the feed object
	 *
	 * @param ProductCatalogManager $product_catalog The product catalog entity manager.
	 * @param DbProvider            $db_provider The DB provider.
	 * @param FeedDirectoryHelper   $directory_helper The feed directory helper instance.
	 */
	public function __construct(
		ProductCatalogManager $product_catalog,
		DbProvider $db_provider,
		FeedDirectoryHelper $directory_helper
	) {
		$this->product_catalog  = $product_catalog;
		$this->db_provider      = $db_provider;
		$this->directory_helper = $directory_helper;

		$this->entity = $this->product_catalog->get_entity();
	}

	/**
	 * Initializate the Feed instance
	 *
	 * If the feed file is existing, it returns the \DOMDocument object of the file
	 * otherwise it returns the new \DOMDocument starting from the main feed element tag
	 *
	 * @return \DOMDocument
	 */
	protected function initFeedObject() {
		if ( ! empty( $this->feed_dom ) ) {
			return $this->get_feed_dom();
		}

		$dom               = new \DOMDocument( '1.0', get_option( 'blog_charset' ) );
		$dom->formatOutput = true; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

		if ( $this->filesystem()->exists( $this->directory_helper->get_feed_path_tmp() ) ) {
			$dom->load( $this->directory_helper->get_feed_path_tmp() );
		} else {
			$feed = $dom->createElementNS( self::XML_NS_ATOM, 'feed' );
			$feed->setAttribute( 'xmlns', self::XML_NS_ATOM );
			$feed->setAttribute( 'xmlns:g', self::XML_NS_G );
			$feed = $dom->appendChild( $feed );

			// Title.
			$title = $dom->createElement( 'title', get_bloginfo_rss( 'name' ) );
			$feed->appendChild( $title );

			// Link self.
			$link = $dom->createElement( 'link' );
			$link->setAttribute( 'rel', 'self' );
			$link->setAttribute( 'href', get_bloginfo_rss( 'url' ) );
			$feed->appendChild( $link );
		}

		return $dom;
	}

	/**
	 * Returns the feed object.
	 *
	 * @return \DOMDocument
	 */
	public function get_feed_dom() {
		return $this->feed_dom;
	}

	/**
	 * Set the feed object.
	 *
	 * @param \DOMDocument $feed_dom The feed content DOM.
	 *
	 * @return void
	 */
	public function setFeedDom( \DOMDocument $feed_dom ) {
		$this->feed_dom = $feed_dom;
	}

	/**
	 * Get the the body of the feed
	 *
	 * @return string
	 */
	public function getFeedContent() {
		return (string) $this->feed_dom->saveXML();
	}

	/**
	 * Add the parameters into the entry element of the feed
	 *
	 * @param \DOMNode   $entry The feed DOM node.
	 * @param FeedMapper $item The feed item fields mapper.
	 *
	 * @return \DOMNode
	 * @throws FeedException Fail when the fields are not validated.
	 */
	protected function addEntryParameters( \DOMNode $entry, FeedMapper $item ) {
		$required_fields = array(
			'g:id'           => $item->get_id(),
			'g:title'        => $item->get_title(),
			'g:description'  => $this->sanitize_content( $item->get_description() ),
			'g:link'         => $item->get_link(),
			'g:brand'        => $item->get_brand(),
			'g:condition'    => $item->get_condition(),
			'g:availability' => $item->get_availability(),
			'g:price'        => $item->get_price(),
		);

		$optional_fields = array(
			'g:checkout_url'              => $item->get_checkout_url(),
			'g:item_group_id'             => $item->get_item_group_id(),
			'g:short_description'         => $this->sanitize_content( $item->get_short_description() ),
			'g:image_link'                => $item->get_image_url(),
			'g:sale_price'                => $item->get_sale_price(),
			'g:sale_price_effective_date' => $item->get_sale_price_effective_date(),
			'g:additional_image_link'     => $item->get_additional_image_urls(),
			'g:shipping_weight'           => $item->get_shipping_weight(),
			'g:google_product_category'   => $item->get_google_category(),

			'g:custom_label_0'            => $item->get_custom_label_0(),
			'g:custom_label_1'            => $item->get_custom_label_1(),
			'g:custom_label_2'            => $item->get_custom_label_2(),
			'g:custom_label_3'            => $item->get_custom_label_3(),
			'g:custom_label_4'            => $item->get_custom_label_4(),
		);

		if ( $required_fields['g:description'] === $optional_fields['g:short_description'] ) {
			unset( $optional_fields['g:short_description'] );
		}

		/**
		 * Merge leaving only the not empty optional fields
		 */
		$fields = apply_filters( 'aepc_feed_item', array_merge( $required_fields, array_filter( $optional_fields ) ), $item );

		/**
		 * Define which fields must be defined in CDATA
		 */
		$cdata_fields = apply_filters(
			'aepc_product_feed_cdata_fields',
			array(
				'g:title',
				'g:description',
				'g:short_description',
			)
		);

		foreach ( $fields as $key => $values ) {
			if ( ! is_array( $values ) ) {
				$values = array( $values );
			}

			foreach ( $values as $value ) {
				$param = $entry->appendChild( $this->feed_dom->createElement( $key ) );
				$value = $this->sanitizeXML( $value );

				if ( in_array( $key, $cdata_fields, true ) ) {
					$param->appendChild( $this->feed_dom->createCDATASection( $value ) );
				} else {
					$param->appendChild( $this->feed_dom->createTextNode( $value ) );
				}
			}
		}

		return $entry;
	}

	/**
	 * Returns the XML object of a entry
	 *
	 * @param FeedMapper $item The feed mapper instance.
	 *
	 * @throws FeedException Fail when unable to add the feed entry into the content.
	 *
	 * @return void
	 */
	public function addFeedEntry( FeedMapper $item ) {
		$entry = $this->feed_dom->createElement( 'entry' );
		$entry = $this->addEntryParameters( $entry, $item );
		$feed  = $this->feed_dom->documentElement;
		if ( $feed instanceof \DOMNode ) {
			$feed->appendChild( $entry );
		}
	}

	/**
	 * Edit the feed entry
	 *
	 * @param FeedMapper $item The feed mapper instance.
	 *
	 * @throws FeedException Fail when unable to edit the feed entry into the content.
	 *
	 * @return void
	 */
	public function editFeedEntry( FeedMapper $item ) {
		$newentry = $this->feed_dom->createElement( 'entry' );
		$newentry = $this->addEntryParameters( $newentry, $item );

		// Search for actual node.
		$xpath = new \DOMXPath( $this->feed_dom );
		$xpath->registerNamespace( 'g', self::XML_NS_G );
		$entries = $xpath->query( '//g:id[text()="' . $item->get_id() . '"]' );

		if ( ! $entries instanceof \DOMNodeList || 0 === intval( $entries->length ) ) {
			throw FeedException::itemDoesNotExist( $item );
		}

		$domelement = $this->feed_dom->documentElement;
		$oldentry   = $entries->item( 0 );

		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		if ( ! $domelement instanceof \DOMElement || ! $oldentry instanceof \DOMNode || ! $oldentry->parentNode instanceof \DOMNode ) {
			return;
		}

		// Replace.
		$domelement->replaceChild( $newentry, $oldentry->parentNode ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
	}

	/**
	 * Make an action to save the body generated
	 *
	 * @param string $context An action context where we are during the feed saving.
	 *
	 * @return void
	 * @throws FeedException Fail when unable to save the chunk into the feed.
	 * @throws EntityException Fail when unable to set the feed item.
	 */
	public function save_chunk( $context ) {
		$this->feed_dom = $this->initFeedObject();
		$items          = $this->product_catalog->get_items( ProductCatalogManager::FILTER_NOT_SAVED );

		// Entries.
		foreach ( $items as $item ) {
			$this->addFeedEntry( $item );
		}

		// Save XML.
		$xml = $this->getFeedContent();
		$this->filesystem()->dumpFile( $this->directory_helper->get_feed_path_tmp(), $xml );
		$this->db_provider->set_items_saved_in_feed( $items, $this->product_catalog );
		$this->product_catalog->get_entity()->increment_products_counter( count( $items ) );
		$this->product_catalog->get_entity()->set_last_update_date( new \DateTime() );
		$this->product_catalog->update();
	}

	/**
	 * Edit all items marked as EDITED, for the limited chunk
	 *
	 * @param string $context An action context where we are during the feed saving.
	 *
	 * @return void
	 * @throws FeedException Fail when unable to save the chunk into the feed.
	 * @throws EntityException Fail when unable to set the feed item.
	 */
	public function edit_chunk( $context ) {
		$this->feed_dom = $this->initFeedObject();
		$items          = $this->product_catalog->get_items( ProductCatalogManager::FILTER_EDITED );

		// Entries.
		foreach ( $items as $item ) {
			$this->editFeedEntry( $item );
		}

		$xml = $this->getFeedContent();
		$this->filesystem()->dumpFile( $this->directory_helper->get_feed_path_tmp(), $xml );
		$this->db_provider->set_items_saved_in_feed( $items, $this->product_catalog );
		$this->product_catalog->get_entity()->set_last_update_date( new \DateTime() );
		$this->product_catalog->update();
	}

	/**
	 * Make an action to delete the object generated previously
	 *
	 * @param string $context An action context where we are during the feed saving.
	 *
	 * @return void
	 * @throws EntityException Fail when unable to set the feed item.
	 */
	public function delete( $context ) {
		$this->filesystem()->remove( $this->directory_helper->get_feed_path() );
		$this->filesystem()->remove( $this->directory_helper->get_feed_path_tmp() );
		$this->resetProductCatalog();
	}

	/**
	 * Reset the states of the product catalog to the start
	 *
	 * @throws EntityException Fail when unable to set the feed item.
	 *
	 * @return void
	 */
	protected function resetProductCatalog() {
		$this->product_catalog->remove_all_feed_status_flags();
		$this->product_catalog->get_entity()->set_products_count( 0 );
		$this->product_catalog->update();
	}

	/**
	 * Make an action when the feed saving is starting
	 *
	 * @param string $context An action context where we are during the feed saving.
	 *
	 * @return void
	 * @throws EntityException Fail when unable to set the feed item.
	 */
	public function upload_start( $context ) {
		if ( FeedSaver::REFRESH_CONTEXT === $context ) {
			$this->resetProductCatalog();
		}
	}

	/**
	 * Make an action when the feed saving is successfully completed
	 *
	 * @param string $context An action context where we are during the feed saving.
	 *
	 * @return void
	 */
	public function upload_success( $context ) {
		// Rename the tmp file with the official one, overwriting the existing one (for refresh context).
		$this->tmpToFile();
	}

	/**
	 * Make an action when the feed saving is failed
	 *
	 * @param string $context An action context where we are during the feed saving.
	 *
	 * @return void
	 */
	public function upload_failure( $context ) {
		$this->removeTmpFile();
		$this->product_catalog->remove_all_feed_status_flags();
	}

	/**
	 * Rename the TMP file with the official one.
	 *
	 * Overwrite the existing one
	 *
	 * @return void
	 */
	public function tmpToFile() {
		$this->filesystem()->rename(
			$this->directory_helper->get_feed_path_tmp(),
			$this->directory_helper->get_feed_path(),
			true
		);
	}

	/**
	 * Remove the temporary version of the feed file
	 *
	 * @return void
	 */
	public function removeTmpFile() {
		$this->filesystem()->remove( $this->directory_helper->get_feed_path_tmp() );
	}

	/**
	 * Check if the TMP file is existing
	 *
	 * It is useful to check if the background saver process is still running or not
	 *
	 * @return bool
	 */
	public function is_saving() {
		return file_exists( $this->directory_helper->get_feed_path_tmp() );
	}

	/**
	 * Get the filesystem instance
	 *
	 * @return Filesystem
	 */
	protected function filesystem() {
		if ( ! $this->filesystem instanceof Filesystem ) {
			$this->filesystem = new Filesystem();
		}

		return $this->filesystem;
	}

	/**
	 * Set the filesystem instance
	 *
	 * @param Filesystem $filesystem The filesystem instance.
	 *
	 * @return void
	 */
	public function setFilesystem( Filesystem $filesystem ) {
		$this->filesystem = $filesystem;
	}

	/**
	 * Translate shortcodes and strip all HTML tags
	 *
	 * @param string $content The content.
	 *
	 * @return string
	 */
	protected function sanitize_content( $content ) {
		$content = do_shortcode( $content );
		$content = (string) preg_replace( '@<(svg)[^>]*?>.*?</\\1>@si', '', $content );
		$content = wp_strip_all_tags( $content );

		return $content;
	}

	/**
	 * Removes invalid characters from a UTF-8 XML string
	 *
	 * @access public
	 * @param string $string A XML string potentially containing invalid characters.
	 * @return string
	 */
	protected function sanitizeXML( $string ) {
		if ( ! empty( $string ) ) {
			$regex  = '/(
	            [\xC0-\xC1] # Invalid UTF-8 Bytes
	            | [\xF5-\xFF] # Invalid UTF-8 Bytes
	            | \xE0[\x80-\x9F] # Overlong encoding of prior code point
	            | \xF0[\x80-\x8F] # Overlong encoding of prior code point
	            | [\xC2-\xDF](?![\x80-\xBF]) # Invalid UTF-8 Sequence Start
	            | [\xE0-\xEF](?![\x80-\xBF]{2}) # Invalid UTF-8 Sequence Start
	            | [\xF0-\xF4](?![\x80-\xBF]{3}) # Invalid UTF-8 Sequence Start
	            | (?<=[\x0-\x7F\xF5-\xFF])[\x80-\xBF] # Invalid UTF-8 Sequence Middle
	            | (?<![\xC2-\xDF]|[\xE0-\xEF]|[\xE0-\xEF][\x80-\xBF]|[\xF0-\xF4]|[\xF0-\xF4][\x80-\xBF]|[\xF0-\xF4][\x80-\xBF]{2})[\x80-\xBF] # Overlong Sequence
	            | (?<=[\xE0-\xEF])[\x80-\xBF](?![\x80-\xBF]) # Short 3 byte sequence
	            | (?<=[\xF0-\xF4])[\x80-\xBF](?![\x80-\xBF]{2}) # Short 4 byte sequence
	            | (?<=[\xF0-\xF4][\x80-\xBF])[\x80-\xBF](?![\x80-\xBF]) # Short 4 byte sequence (2)
	        )/x';
			$string = (string) preg_replace( $regex, '', $string );

			// phpcs:disable
			$result = '';
			$length = strlen( $string );
			for ( $i = 0; $i < $length; $i++ ) {
				$current = ord( $string[ $i ] );
				if ( ( $current == 0x9 ) ||
					( $current == 0xA ) ||
					( $current == 0xD ) ||
					( ( $current >= 0x20 ) && ( $current <= 0xD7FF ) ) ||
					( ( $current >= 0xE000 ) && ( $current <= 0xFFFD ) ) ||
					( ( $current >= 0x10000 ) && ( $current <= 0x10FFFF ) ) ) {
					$result .= chr( $current );
				} else {
					$result .= ' ';    // use this to replace them with spaces.
				}
			}
			$string = $result;
			// phpcs:enable
		}
		return $string;
	}

}
