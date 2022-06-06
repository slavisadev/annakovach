<?php

namespace PixelYourSite\Pinterest;

use PixelYourSite;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function renderPixelIdField() {
    
    if ( PixelYourSite\Pinterest()->enabled() ) : ?>
    
        <div class="row align-items-center">
            <div class="col-2 py-2">
                <img class="tag-logo" src="<?php echo PYS_PINTEREST_URL; ?>/dist/images/pinterest-square-small.png">
            </div>
            <div class="col-6">
                Your Pinterest Tag
            </div>
            <div class="col-4">
                <label for="pin_settings_switch" class="btn btn-block btn-sm btn-primary btn-settings">Click for settings</label>
            </div>
        </div>
        <input type="checkbox" id="pin_settings_switch" style="display: none">
        <div class="settings_content">
            <div class="plate">
                <div class="row pt-3 pb-3">
                    <div class="col-12">
                        <h4 class="label">Pinterest Pixel ID:</h4>
                        <?php PixelYourSite\Pinterest()->render_pixel_id( 'pixel_id', 'Pinterest Pixel ID' ); ?>
                        <?php if ( isPysProActive() ) : ?>
                            <small class="form-text"><a
                                        href="https://www.pixelyoursite.com/documentation/add-your-pinterest-tag"
                                        target="_blank">How to get it?</a></small>
                        <?php else : ?>
                            <small class="form-text"><a
                                        href="https://www.pixelyoursite.com/pixelyoursite-free-version/add-your-pinterest-tag"
                                        target="_blank">How to get it?</a></small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <hr>
            <?php
                if(function_exists("PixelYourSite\addMetaTagFields")) {
                    if (isPysProActive()) {
                        $url = "https://www.pixelyoursite.com/documentation/add-your-pinterest-tag";
                    } else {
                        $url = "https://www.pixelyoursite.com/pixelyoursite-free-version/add-your-pinterest-tag";
                    }

                    PixelYourSite\addMetaTagFields(PixelYourSite\Pinterest(),$url);
                }
            ?>
        </div>
        <hr>
        
    <?php

    endif;

}

function adminSecondaryNavTabs( $tabs ) {
	
	$tabs['pinterest_settings'] = array(
		'url'  => PixelYourSite\buildAdminUrl( 'pixelyoursite', 'pinterest_settings' ),
		'name' => 'Pinterest Settings',
	);

	return $tabs;

}

function renderSettingsPage() {
	
	/** @noinspection PhpIncludeInspection */
    include PYS_PINTEREST_PATH . '/modules/pinterest/views/html-settings.php';
    
}

function adminNoticePysCoreNotActive() {
	
	if ( current_user_can( 'manage_options' ) ) : ?>

        <div class="notice notice-error">
            <p>PixelYourSite Pinterest Add-on needs PixelYourSite PRO or Free in order to work. Activate it now.</p>
        </div>
	
	<?php endif;
	
}

function adminNoticePysProOutdated() {
	
	if ( current_user_can( 'manage_options' ) ) : ?>

        <div class="notice notice-error">
            <p>PixelYourSite Pinterest Add-on requires PixelYourSite PRO version <?php echo
                PYS_PINTEREST_PRO_MIN_VERSION; ?> or newer.</p>
        </div>
	
	<?php endif;
	
}

function adminNoticePysFreeOutdated() {
    
    if ( current_user_can( 'manage_options' ) ) : ?>

        <div class="notice notice-error">
            <p>PixelYourSite Pinterest Add-on requires PixelYourSite Free version <?php echo
                PYS_PINTEREST_FREE_MIN_VERSION; ?> or newer.</p>
        </div>
    
    <?php endif;
    
}