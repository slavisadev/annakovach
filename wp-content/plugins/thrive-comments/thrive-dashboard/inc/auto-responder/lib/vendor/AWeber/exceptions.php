<?php

/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-dashboard
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

class Thrive_Dash_Api_AWeber_Exception extends Exception {
}

/**
 * Thrown when the API returns an error. (HTTP status >= 400)
 *
 *
 * @uses Thrive_Dash_Api_AWeber_Exception
 * @package
 * @version $id$
 */
class Thrive_Dash_Api_AWeber_APIException extends Thrive_Dash_Api_AWeber_Exception {

	public $type;
	public $status;
	public $message;
	public $documentation_url;
	public $url;

	public function __construct( $error, $url ) {
		// record specific details of the API exception for processing
		$this->url               = $url;
		$this->type              = $error['type'];
		$this->status            = array_key_exists( 'status', $error ) ? $error['status'] : '';
		$this->message           = $error['message'];
		$this->documentation_url = $error['documentation_url'];

		parent::__construct( $this->message );
	}
}

/**
 * Thrown when attempting to use a resource that is not implemented.
 *
 * @uses Thrive_Dash_Api_AWeber_Exception
 * @package
 * @version $id$
 */
class Thrive_Dash_Api_AWeber_ResourceNotImplemented extends Thrive_Dash_Api_AWeber_Exception {

	public function __construct( $object, $value = '' ) {
		$this->object = $object;
		$this->value  = $value;
		parent::__construct( "Resource \"{$value}\" is not implemented on this resource." );
	}
}

/**
 * Thrive_Dash_Api_AWeber_ResourceNotImplemented
 *
 * Thrown when attempting to call a method that is not implemented for a resource
 * / collection.  Differs from standard method not defined errors, as this will
 * be thrown when the method is infact implemented on the base class, but the
 * current resource type does not provide access to that method (ie calling
 * getByMessageNumber on a web_forms collection).
 *
 * @uses Thrive_Dash_Api_AWeber_Exception
 * @package
 * @version $id$
 */
class Thrive_Dash_Api_AWeber_MethodNotImplemented extends Thrive_Dash_Api_AWeber_Exception {

	public function __construct( $object ) {
		$this->object = $object;
		parent::__construct( "This method is not implemented by the current resource." );

	}
}

/**
 * Thrive_Dash_Api_AWeber_OAuthException
 *
 * OAuth exception, as generated by an API JSON error response
 * @uses Thrive_Dash_Api_AWeber_Exception
 * @package
 * @version $id$
 */
class Thrive_Dash_Api_AWeber_OAuthException extends Thrive_Dash_Api_AWeber_Exception {

	public function __construct( $type, $message ) {
		$this->type    = $type;
		$this->message = $message;
		parent::__construct( "{$type}: {$message}" );
	}
}

/**
 * Thrive_Dash_Api_AWeber_OAuthDataMissing
 *
 * Used when a specific piece or pieces of data was not found in the
 * response. This differs from the exception that might be thrown as
 * an Thrive_Dash_Api_AWeber_OAuthException when parameters are not provided because
 * it is not the servers' expectations that were not met, but rather
 * the expecations of the client were not met by the server.
 *
 * @uses Thrive_Dash_Api_AWeber_Exception
 * @package
 * @version $id$
 */
class Thrive_Dash_Api_AWeber_OAuthDataMissing extends Thrive_Dash_Api_AWeber_Exception {

	public function __construct( $missing ) {
		if ( ! is_array( $missing ) ) {
			$missing = array( $missing );
		}
		$this->missing = $missing;
		$required      = join( ', ', $this->missing );
		parent::__construct( "OAuthDataMissing: Response was expected to contain: {$required}" );

	}
}

/**
 * Thrive_Dash_Api_AWeber_ResponseError
 *
 * This is raised when the server returns a non-JSON response. This
 * should only occur when there is a server or some type of connectivity
 * issue.
 *
 * @uses Thrive_Dash_Api_AWeber_Exception
 * @package
 * @version $id$
 */
class Thrive_Dash_Api_AWeber_ResponseError extends Thrive_Dash_Api_AWeber_Exception {

	public function __construct( $uri ) {
		$this->uri = $uri;
		parent::__construct( "Request for {$uri} did not respond properly." );
	}

}
