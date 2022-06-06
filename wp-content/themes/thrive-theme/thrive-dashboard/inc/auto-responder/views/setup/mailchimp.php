<?php

/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-dashboard
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}
 $admin_email = get_option( 'admin_email' ); ?>
<?php $mandirll_key = $this->param( 'mandrill-key' ) ?>
<h2 class="tvd-card-title"><?php echo esc_html( $this->getTitle() ); ?></h2>
<div class="tvd-row">
	<p class="card-mb-10">
		<strong><?php echo esc_html__( 'Notification:', TVE_DASH_TRANSLATE_DOMAIN ) ?> </strong><?php echo esc_html__( 'If you would like to use Transactional emails. You should fill in the Mandrill API Key and Mandrill-approved email address optional fields.', TVE_DASH_TRANSLATE_DOMAIN ) ?>
	</p>
	<form class="tvd-col tvd-s12">
		<input type="hidden" name="api" value="<?php echo esc_attr( $this->getKey() ); ?>"/>
		<div class="tvd-input-field">
			<input id="tvd-mm-api-key" type="text" name="connection[key]"
					value="<?php echo esc_attr( $this->param( 'key' ) ); ?>">
			<label for="tvd-mm-api-key"><?php echo esc_html__( "API key", TVE_DASH_TRANSLATE_DOMAIN ) ?></label>
		</div>
		<input type="checkbox" id="tvd-show-mandrill-options" class="tvd-api-show-extra-options" name="connection[show_mandrill]"
			<?php echo ! empty( $mandirll_key ) ? 'checked' : '' ?> >
		<label for="tvd-show-mandrill-options">Add Mandrill Connection</label>
		<a href="#">Link</a>
		<div class="tvd-hide tvd-extra-options">
			<h4><strong><?php echo esc_html__( 'Mandrill:', TVE_DASH_TRANSLATE_DOMAIN ) ?> </strong></h4>
			<div class="tvd-input-field">
				<input id="tvd-pm-api-email" type="text" name="connection[email]"
						value="<?php echo esc_attr( $this->param( 'email', $admin_email ) ); ?>">
				<label for="tvd-pm-api-email">
					<?php echo esc_html__( "Mandrill-approved email address", TVE_DASH_TRANSLATE_DOMAIN ) ?>
				</label>
			</div>
			<div class="tvd-input-field">
				<input id="tvd-mandrill-api-key" type="text" name="connection[mandrill-key]"
						value="<?php echo esc_attr( $this->param( 'mandrill-key' ) ); ?>">
				<label for="tvd-mandrill-api-key"><?php echo esc_html__( "Mandrill API key", TVE_DASH_TRANSLATE_DOMAIN ) ?></label>
			</div>
		</div>
		<div class="tvd-row tvd-hide tvd-extra-options">
			<div class="tvd-col tvd-s12">
				<p class="tvd-form-description tvd-note-text">
					<?php echo esc_html__( 'Note: sending from Mandrill only works if the email you enter has been verified in Mandrill.', TVE_DASH_TRANSLATE_DOMAIN ) ?>
					<a href="https://mandrillapp.com/settings/sending-domains" target="_blank"><?php echo esc_html__( 'Learn more', TVE_DASH_TRANSLATE_DOMAIN ) ?></a>.
				</p>
			</div>
		</div>
		<?php $this->display_video_link(); ?>
	</form>
</div>
<div class="tvd-card-action">
	<div class="tvd-row tvd-no-margin">
		<div class="tvd-col tvd-s12 tvd-m6">
			<a class="tvd-api-cancel tvd-btn-flat tvd-btn-flat-secondary tvd-btn-flat-dark tvd-full-btn tvd-waves-effect"><?php echo esc_html__( "Cancel", TVE_DASH_TRANSLATE_DOMAIN ) ?></a>
		</div>
		<div class="tvd-col tvd-s12 tvd-m6">
			<a class="tvd-api-connect tvd-waves-effect tvd-waves-light tvd-btn tvd-btn-green tvd-full-btn"><?php echo esc_html__( "Connect", TVE_DASH_TRANSLATE_DOMAIN ) ?></a>
		</div>
	</div>
</div>

