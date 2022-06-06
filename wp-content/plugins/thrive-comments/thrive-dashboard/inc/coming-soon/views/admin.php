<?php include TVE_DASH_PATH . '/templates/header.phtml'; ?>

<div class="tvd-breadcrumb coming-soon-nav">
	<a href="<?php echo admin_url( 'admin.php?page=tve_dash_section' ); ?>" class="tvd-breadcrumb-back">
		<?php echo __( 'Thrive Dashboard', TVE_DASH_TRANSLATE_DOMAIN ); ?>
	</a>
	<span>
		<?php echo __( 'Coming Soon', TVE_DASH_TRANSLATE_DOMAIN ); ?>
	</span>
</div>

<div class="tvd-wrap cs-content">
	<div class="tvd-actions">
		<h2>
			<?php echo __( 'Enable "Coming Soon" mode', TVE_DASH_TRANSLATE_DOMAIN ); ?>
		</h2>
		<p>
			<?php echo __( 'If you activate this feature, you will be able to set up a "Coming Soon" page, that your visitors will be redirected to, while you build your website. However, the logged in users will still be able to see your website.', TVE_DASH_TRANSLATE_DOMAIN ); ?>
			<a class="learn-more-link" href="https://help.thrivethemes.com/en/articles/5366468-add-a-coming-soon-page-to-your-website" target="_blank">
				<?php echo __( 'Learn more', TVE_DASH_TRANSLATE_DOMAIN ); ?>
			</a>
		</p>

		<div class="tvd-wrap space-between">
			<div class="tvd-switch">
				<label>
					<?php echo __( 'ACTIVE', TVE_DASH_TRANSLATE_DOMAIN ); ?>
					<input type="checkbox" class="tvd-toggle-input" <?php checked( true, $coming_soon ); ?>>
					<span class="tvd-lever"></span>
				</label>
			</div>
		</div>
		<div class="tvd-cs-page-selector"></div>
	</div>
</div>

<div id="tvd-back-td" class="tvd-col tvd-m6">
	<a href="<?php echo admin_url( 'admin.php?page=tve_dash_section' ); ?>" class="tvd-waves-effect tvd-waves-light tvd-btn-small tvd-btn-gray">
		<?php echo __( 'Back To Dashboard', TVE_DASH_TRANSLATE_DOMAIN ); ?>
	</a>
</div>
