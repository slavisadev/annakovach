<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-quiz-builder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden
}

?>

<div id="tie-control-panel">
	<div class="tie-no-margin tie-control-panel-title">
		<div class="tie-title-holder">
			<div id="tqb-logo" class="tie-logo"></div>
			<h2 class="tie-white-text">Thrive Quiz Builder</h2>
		</div>
	</div>
	<div id="tie-cp-wrapper-elements" class="tie-control-panel-controls">
		<div id="tie-element-panel" class="mce-some" style="display: none"></div>
		<div id="tie-canvas-panel">
			<h4 class="tie-panel-subtitle tie-bold"><?php echo esc_html__( 'Simple Content Elements', Thrive_Image_Editor::T ) ?></h4>
			<div class="tie-card">
				<div class="tie-card-content">
					<div class="tie-drag-text tie-cp-element" data-type="text">
						<span class="tie-paragraph-icon"></span>
						<span><?php echo esc_html__( 'Paragraph/Text', Thrive_Image_Editor::T ) ?></span>
					</div>
				</div>
			</div>
			<h4 class="tie-panel-subtitle"><?php echo esc_html__( 'Canvas Properties', Thrive_Image_Editor::T ) ?></h4>
			<div class="tie-card">
				<div class="tie-card-title">
					<?php echo esc_html__( 'Color Overlay', Thrive_Image_Editor::T ) ?>
				</div>
				<div class="tie-card-content tie-color-overlay-box">
					<div class="tie-color-select">
						<label><?php echo esc_html__( 'Color', Thrive_Image_Editor::T ) ?></label>
						<div class="tie-color-selector-container">
							<input type="text" id="tie-color-picker-overlay"
								   value="<?php echo esc_attr( $image->get_settings()->get_data( 'overlay/bg_color' ) ); ?>">
							<div class="tie-color-code-holder">
								<input type="text" class="tie-color-code" id="tie-color-overlay-code"
									   value="<?php echo esc_attr( $image->get_settings()->get_data( 'overlay/bg_color' ) ); ?>">
							</div>
						</div>
					</div>
					<div class="tie-opacity-select">
						<label><?php echo esc_html__( 'Opacity', Thrive_Image_Editor::T ) ?></label>
						<div class="tie-opacity-selector-container">
							<div class="tie-slider-widget"
								 data-value="<?php echo esc_attr( $image->get_settings()->get_data( 'overlay/opacity' ) ); ?>"
								 data-min="0"
								 data-max="100"
								 data-connect-to="#tie-overlay-opacity">
							</div>
							<input id="tie-overlay-opacity" type="number" maxlength="3">
						</div>
					</div>
				</div>
			</div>
			<div id="tie-bg-controller">
				<div class="tie-card">
					<div class="tie-card-title">
						<?php echo esc_html__( 'Background Image', Thrive_Image_Editor::T ) ?>
					</div>
					<div class="tie-card-content" id="tie-image-options-holder">
						<div id="tie-position-holder" style="height: 44px;">
							<div class="left">
								<a id="tie-image-position" href="javascript:void(0)" class="tie-btn">
									<i class="tie-icon-crop"></i>
									<?php echo esc_html__( 'Size & Position', Thrive_Image_Editor::T ) ?>
								</a>
							</div>
						</div>
						<div style="position: relative;">
							<input type="text" id="tie-bg-filename" style="padding-right: 30px;" disabled
								   value="<?php echo esc_attr( $image->get_settings()->get_bg_filename() ); ?>">
							<a id="tie-remove-canvas-bg" href="javascript:void(0)"
							   class="tie-btn tie-delete-background-image-trash"
							   style="display: <?php echo esc_attr( $image->get_settings()->get_data( 'background_image/url' ) !== 'none' ? 'inline-block' : 'none' ); ?>;">

								<i class="tie-icons tie-icon-trash-o2"></i>
							</a>
						</div>
						<a id="tie-choose-canvas-bg" class="tie-btn tie-full-btn tie-btn-gray tie-waves-effect"
						   href="javascript:void(0)">
							<?php echo esc_html__( $image->get_settings()->get_data( 'background_image/url' ) !== 'none' ? 'Replace Image' : 'Select Background Image', Thrive_Image_Editor::T ) ?>
						</a>
					</div>
				</div>
			</div>
			<div id="tie-canvas-size">
				<div class="tie-card">
					<div class="tie-card-title">
						<?php echo esc_html__( 'Canvas Size', Thrive_Image_Editor::T ) ?>
						<span id="tie-canvas-size-label" class="tie-right tie-blue-gray-text tie-text-lighten-3">canvas size</span>
					</div>
					<div class="tie-card-content m15">
						<a id="tie-set-canvas-dimensions" class="tie-btn tie-full-btn tie-btn-gray tie-waves-effect"
						   href="javascript:void(0)">
							<?php echo esc_html__( 'Set Custom Dimensions', Thrive_Image_Editor::T ) ?>
						</a>
						<a id="tie-set-canvas-default-dimensions" class="text" href="javascript:void(0)">
							<?php echo esc_html__( 'Use default size', Thrive_Image_Editor::T ) ?>
						</a>
					</div>
				</div>
			</div>
			<h4 class="tie-panel-subtitle"><?php echo esc_html__( 'Dynamic result', Thrive_Image_Editor::T ) ?></h4>
			<div class="tie-card">
				<div class="tie-card-content">
					<div class="tie-row tie-copy-row">
						<div class="tie-col tie-s8">
							<div class="tie-input-field tve-shortcode-input">
								<input readonly="readonly" class="tie-no-margin tie-copy" type="text" value="%result%"/>
							</div>
						</div>
						<div class="tie-col tie-s4">
							<a class="tie-copy-to-clipboard tie-waves-effect tie-waves-light tie-btn tie-btn-blue tie-btn-small tie-waves-effect"
							   href="javascript:void(0)">
								<span
									class="tie-copy-text"><?php echo esc_html__( 'Copy', Thrive_Quiz_Builder::T ) ?></span>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="control-panel-footer">
		<div class="tie-row tie-margin-top">
			<div class="tie-col tie-s6">
				<a id="tie-save-canvas" class="tie-btn tie-full-btn tie-click tie-waves-effect tie-tooltipped"
				   data-click-callback="save_content" data-params="true,true"
				   href="javascript:void(0)"
				   data-tooltip="<?php echo esc_html__( 'Save and generate the image.', Thrive_Image_Editor::T ) ?>"
				   data-position="top">
					<?php echo esc_html__( 'Save & Exit', Thrive_Image_Editor::T ) ?>
				</a>
			</div>
			<div class="tie-col tie-s6">
				<a class="tie-btn tie-btn-gray tie-full-btn tie-click tie-waves-effect" href="javascript:void(0)"
				   data-click-callback="preview_content">
					<?php echo esc_html__( 'Preview', Thrive_Image_Editor::T ) ?>
				</a>
			</div>
			<div class="tie-col tie-s12 tie-center-align">
				<h4 id="tie-saving-status" class="tie-gray-text tie-text-darken-1">
					<?php echo esc_html__( 'Your changes are auto-saved', Thrive_Image_Editor::T ) ?>
				</h4>
			</div>
		</div>
		<div class="tie-row action-buttons">
			<div id="undo-holder">
				<button class="tie-btn tie-btn-gray tie-waves-effect tie-click tie-disabled" data-click-callback="undo" href="javascript:void(0)">
					<i class="tie-icon-rotate-left"></i>
				</button>
				<p>Undo</p>
			</div>
			<div id="redo-holder">
				<button class="tie-btn tie-btn-gray tie-waves-effect tie-click tie-disabled" data-click-callback="redo" href="javascript:void(0)">
					<i class="tie-icon-rotate-left" id="flipped"></i>
				</button>
				<p>Redo</p>
			</div>
			<div id="templates-holder" class="tie-right">
				<button class="tie-btn tie-btn-gray tie-waves-effect tie-click" data-click-callback="open_templates_modal" href="javascript:void(0)">
					<i class="tie-icon-plus"></i>
				</button>
				<p>Templates</p>
			</div>
		</div>
	</div>
	<div class="slide-control-panel">

	</div>
</div>
