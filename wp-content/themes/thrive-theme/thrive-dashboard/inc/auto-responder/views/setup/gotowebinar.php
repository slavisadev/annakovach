<?php

/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-dashboard
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}
 $version = (int) $this->get_version(); ?>
<h2 class="tvd-card-title"><?php echo 2 === (int) $version ? esc_html( $this->getTitle() . ' ' . $version . '.0' ) : esc_html( $this->getTitle() ); ?></h2>
<div class="tvd-row">
	<?php	/** @var $this Thrive_Dash_List_Connection_GoToWebinar */
	?>
	<?php if ( $this->isConnected() && $this->expiresIn() > 30 && 2 !== $version ) : ?>
		<p class="tvd-card-spacer tvd-card-margin"><?php echo esc_html__( 'GoToWebinar is connected. The access token expires on:', TVE_DASH_TRANSLATE_DOMAIN ); ?>
			<strong><?php echo esc_html( $this->getExpiryDate() ); ?></strong></p>
	<?php elseif ( $this->isExpired() && 2 !== $version ) : ?>
		<p class="tvd-card-spacer  tvd-card-margin">
			<?php echo esc_html__( 'The GoToWebinar access token has expired on:', TVE_DASH_TRANSLATE_DOMAIN ); ?>
			<strong><?php echo esc_html( $this->getExpiryDate() ); ?></strong>. <?php echo esc_html__( 'You need to renew the token by providing your GoToWebinar credentials below', TVE_DASH_TRANSLATE_DOMAIN ); ?>
		</p>
	<?php elseif ( $this->isConnected() && $this->expiresIn() <= 30 && 2 !== $version ) : ?>
		<p class="tvd-card-spacer tvd-card-margin"><?php echo sprintf( esc_html__( 'The GoToWebinar access token will expire in <strong>%s days</strong>. Renew the token by providing your GoToWebinar credentials below', TVE_DASH_TRANSLATE_DOMAIN ), esc_html( $this->expiresIn() ) ); ?></p>
	<?php else : ?>
		<p class="tvd-card-spacer tvd-card-margin"><?php echo esc_html__( 'Fill in your GoToWebinar username (email) and password below to connect', TVE_DASH_TRANSLATE_DOMAIN ); ?></p>
	<?php endif ?>
	<form class="tvd-col tvd-s12" autocomplete="false">
		<input type="hidden" name="api" value="<?php echo esc_attr( $this->getKey() ); ?>"/>
		<div class="tvd-input-field tvd-margin-top">
			<input id="tvd-gtw-api-email" type="text" class="text" autocomplete="new-email" name="gtw_email" value="<?php echo esc_attr( $this->getUsername() ); ?>"/>
			<label for="tvd-gtw-api-email"><?php echo esc_html__( 'Email', TVE_DASH_TRANSLATE_DOMAIN ); ?></label>
		</div>
		<div class="tvd-input-field">
			<input id="tvd-gtw-api-password" type="password" autocomplete="new-password" class="text" name="gtw_password" value="<?php echo esc_attr( $this->getPassword() ); ?>"/>
			<label for="tvd-gtw-api-password"><?php echo esc_html__( 'Password', TVE_DASH_TRANSLATE_DOMAIN ); ?></label>
		</div>
		<div class="tvd-col tvd-s12 tvd-m6 tvd-no-padding">
			<p>
				<input class="tvd-version-1 tvd-api-hide-extra-options" name="connection[version]" type="radio" value="1" disabled id="tvd-version-1" <?php checked( $version, 1, true ); ?> />
				<label for="tvd-version-1"><?php echo esc_html__( 'Version 1', TVE_DASH_TRANSLATE_DOMAIN ); ?></label>
			</p>
		</div>
		<div class="tvd-col tvd-s12 tvd-m6 tvd-no-padding">
			<p>
				<input class="tvd-version-2 tvd-api-show-extra-options" name="connection[version]" type="radio" value="2" id="tvd-version-2" <?php echo ! $this->isConnected() || 2 === $version ? 'checked="checked"' : ''; ?> />
				<label for="tvd-version-2"><?php echo esc_html__( 'Version 2', TVE_DASH_TRANSLATE_DOMAIN ); ?></label>
				<input type="hidden" name="connection[versioning]" value="1">
			</p>
		</div>
		<?php $this->display_video_link(); ?>
	</form>
</div>
<div class="tvd-card-action">
	<div class="tvd-row tvd-no-margin">
		<div class="tvd-col tvd-s12 tvd-m6">
			<a class="tvd-api-cancel tvd-btn-flat tvd-btn-flat-secondary tvd-btn-flat-dark tvd-full-btn tvd-waves-effect"><?php echo esc_html__( 'Cancel', TVE_DASH_TRANSLATE_DOMAIN ); ?></a>
		</div>
		<div class="tvd-col tvd-s12 tvd-m6">
			<a class="tvd-api-connect tvd-waves-effect tvd-waves-light tvd-btn tvd-btn-green tvd-full-btn"><?php echo esc_html__( 'Connect', TVE_DASH_TRANSLATE_DOMAIN ); ?></a>
		</div>
	</div>
</div>
