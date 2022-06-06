<h2 class="tvd-card-title"><?php echo esc_html( $this->getTitle() ); ?></h2>
<div class="tvd-row">
	<form class="tvd-col tvd-s12">
		<input type="hidden" name="api" value="<?php echo esc_attr( $this->getKey() ); ?>"/>
		<div class="tvd-input-field">
			<input id="tvd-ew-api-key" type="text" name="connection[key]" value="<?php echo esc_attr( $this->param( 'key' ) ); ?>">
			<label for="tvd-ew-api-key">
				<?php echo esc_html__( 'API key', TVE_DASH_TRANSLATE_DOMAIN ) ?>
			</label>
		</div>
	</form>
	<p><?php echo __( 'For more information please read the following', TVE_DASH_TRANSLATE_DOMAIN ) ?> <a href="https://help.thrivethemes.com/en/articles/5151467-how-to-set-up-use-an-api-connection-with-everwebinar" target="_blank"><strong><?php echo __( 'Knowledge Base Article', TVE_DASH_TRANSLATE_DOMAIN ) ?></strong></a></p>
	<?php $this->display_video_link(); ?>
</div>
<div class="tvd-card-action">
	<div class="tvd-row tvd-no-margin">
		<div class="tvd-col tvd-s12 tvd-m6">
			<a class="tvd-api-cancel tvd-btn-flat tvd-btn-flat-secondary tvd-btn-flat-dark tvd-full-btn tvd-waves-effect">
				<?php echo esc_html__( 'Cancel', TVE_DASH_TRANSLATE_DOMAIN ) ?>
			</a>
		</div>
		<div class="tvd-col tvd-s12 tvd-m6">
			<a class="tvd-api-connect tvd-waves-effect tvd-waves-light tvd-btn tvd-btn-green tvd-full-btn">
				<?php echo esc_html__( 'Connect', TVE_DASH_TRANSLATE_DOMAIN ) ?>
			</a>
		</div>
	</div>
</div>
