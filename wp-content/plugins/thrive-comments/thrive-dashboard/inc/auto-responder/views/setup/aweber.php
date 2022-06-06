<?php

/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-dashboard
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}
/** @var $this Thrive_Dash_List_Connection_AWeber */
?>
<h2 class="tvd-card-title"><?php echo esc_html( $this->getTitle() ) ?></h2>
<?php try { ?>
	<div class="tvd-row">
		<p><?php echo esc_html__( 'Click the button below to login to your AWeber account and authorize the API Connection.', TVE_DASH_TRANSLATE_DOMAIN ) ?></p>
		<?php $this->display_video_link(); ?>
	</div>
	<div class="tvd-card-action">
		<div class="tvd-row tvd-no-margin">
			<div class="tvd-col tvd-s12 tvd-m6">
				<a class="tvd-api-cancel tvd-btn-flat tvd-btn-flat-secondary tvd-btn-flat-dark tvd-full-btn tvd-waves-effect"><?php echo esc_html__( "Cancel", TVE_DASH_TRANSLATE_DOMAIN ); ?></a>
			</div>
			<div class="tvd-col tvd-s12 tvd-m6">
				<a class="tvd-waves-effect tvd-waves-light tvd-btn tvd-btn-green tvd-full-btn"
				   href="<?php echo esc_url( $this->getAuthorizeUrl() ) ?>"><?php echo esc_html__( 'Connect', TVE_DASH_TRANSLATE_DOMAIN ); ?></a>
			</div>
		</div>
	</div>

<?php } catch ( Thrive_Dash_Api_AWeber_Exception $e ) { ?>
	<?php
	$url     = false;
	$message = $e->getMessage();
	$api_url = isset( $e->url ) ? $e->url : false;
	?>
	<p style="color: red"><?php echo esc_html__( 'There has been an error while communicating with AWeber API. Below are the error details:', TVE_DASH_TRANSLATE_DOMAIN ); ?></p>
	<?php echo esc_html( $message );
	if ( $api_url ) {
		echo ' (API URL: ' . esc_html( $api_url ) . ')';
	}
}
?>
