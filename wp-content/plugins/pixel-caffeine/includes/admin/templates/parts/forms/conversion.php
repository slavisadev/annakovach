<?php
/**
 * Form add/edit of conversion
 *
 * @var AEPC_Admin_View $page
 * @var string $action
 *
 * @package Pixel Caffeine
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<div class="form-group<?php $page->field_class( 'event_name' ); ?>">
	<label for="event_name" class="control-label"><?php esc_html_e( 'Name', 'pixel-caffeine' ); ?></label>
	<div class="control-wrap">
		<input type="text" class="form-control" id="event_name" name="event_name" value="{{ data.name }}" placeholder="<?php esc_attr_e( 'Name...', 'pixel-caffeine' ); ?>">
		<div class="field-helper">
			<?php $page->print_field_error( 'event_name', '<span class="help-block help-block-error">', '</span>' ); ?>
		</div>
	</div>
</div>

<div class="form-group<?php $page->field_class( 'event_trigger_on' ); ?>">
	<label for="event_trigger_on" class="control-label"><?php esc_html_e( 'Trigger on', 'pixel-caffeine' ); ?></label>
	<div class="control-wrap">
		<select name="event_trigger_on" id="event_trigger_on" class="form-control js-dep">
			<option value="page_visit"<# if ( 'page_visit' == data.trigger ) { #> selected="selected"<# } #>><?php esc_html_e( 'Page visit', 'pixel-caffeine' ); ?></option>
			<option value="link_click"<# if ( 'link_click' == data.trigger ) { #> selected="selected"<# } #>><?php esc_html_e( 'Link click', 'pixel-caffeine' ); ?></option>
			<option value="css_selector"<# if ( 'css_selector' == data.trigger ) { #> selected="selected"<# } #>><?php esc_html_e( 'CSS Selector', 'pixel-caffeine' ); ?></option>
			<option value="js_event"<# if ( 'js_event' == data.trigger ) { #> selected="selected"<# } #>><?php esc_html_e( 'JS Event', 'pixel-caffeine' ); ?></option>
		</select>
		<div class="field-helper">
			<?php $page->print_field_error( 'event_trigger_on', '<span class="help-block help-block-error">', '</span>' ); ?>
		</div>
	</div>
</div>

<!-- Show only if link_click or page_visit are selected in the previous select -->
<div class="form-group event_trigger_on-page_visit event_trigger_on-link_click<?php $page->field_class( 'event_url' ); ?>">
	<label for="event_url" class="control-label"><?php esc_html_e( 'URL', 'pixel-caffeine' ); ?></label>
	<div class="multiple-fields-inline select-input">
		<div class="control-wrap">
			<select name="event_url_condition" id="event_url_condition" class="form-control js-dep">
				<option value="contains"<# if ( 'contains' == data.url_condition ) { #> selected="selected"<# } #>><?php esc_html_e( 'Contains', 'pixel-caffeine' ); ?></option>
				<option value="exact"<# if ( 'exact' == data.url_condition ) { #> selected="selected"<# } #>><?php esc_html_e( 'Is Exact', 'pixel-caffeine' ); ?></option>
			</select>
		</div>
		<div class="control-wrap">
			<input type="text" class="form-control" name="event_url" id="event_url" value="{{ data.url }}" placeholder="<?php esc_attr_e( 'URL', 'pixel-caffeine' ); ?>">
			<div class="field-helper">
				<?php $page->print_field_error( 'event_url', '<span class="help-block help-block-error">', '</span>' ); ?>
			</div>
		</div>
	</div>
</div>

<!-- Show only if css_selector is selected in the previous select -->
<div class="form-group event_trigger_on-css_selector<?php $page->field_class( 'event_css' ); ?>">
	<label for="event_css" class="control-label"><?php esc_html_e( 'CSS', 'pixel-caffeine' ); ?></label>
	<div class="control-wrap">
		<input type="text" class="form-control" name="event_css" id="event_css" value="{{ data.css }}" placeholder="<?php esc_attr_e( 'CSS', 'pixel-caffeine' ); ?>">
		<div class="field-helper">
			<?php $page->print_field_error( 'event_css', '<span class="help-block help-block-error">', '</span>' ); ?>
		</div>
	</div>
</div>
<div class="form-group event_trigger_on-js_event<?php $page->field_class( 'js_event_name' ); ?>">
	<label for="js_event_name" class="control-label"><?php esc_html_e( 'JS Event', 'pixel-caffeine' ); ?></label>
	<div class="multiple-fields-inline">
		<div class="controls-wrap">
			<div class="control-wrap w33">
				<input type="text" class="form-control" name="event_js_event_element" id="event_js_event_element" value="{{ data.js_event_element }}" placeholder="<?php esc_attr_e( 'Element by jQuery/CSS selector (`.element` or `#element` or `#element .child`)', 'pixel-caffeine' ); ?>">
				<div class="field-helper">
					<?php $page->print_field_error( 'event_js_event_element', '<span class="help-block help-block-error">', '</span>' ); ?>
				</div>
			</div>
			<div class="control-wrap">
				<input type="text" class="form-control" name="event_js_event_name" id="event_js_event_name" value="{{ data.js_event_name }}" placeholder="<?php esc_attr_e( 'JS Event name', 'pixel-caffeine' ); ?>">
				<div class="field-helper">
					<?php $page->print_field_error( 'event_js_event_name', '<span class="help-block help-block-error">', '</span>' ); ?>
				</div>
			</div>
		</div>
		<div class="field-helper">
			<small class="text"><?php esc_html_e( 'It will be translated in `jQuery(" field1 content ").on(" field2 content ")`', 'pixel-caffeine' ); ?></small>
		</div>
	</div>
</div>

<div class="multi-form-group">
	<div class="form-group<?php $page->field_class( 'event_standard_events' ); ?>">
		<label for="event_standard_events" class="control-label"><?php esc_html_e( 'Event', 'pixel-caffeine' ); ?></label>
		<div class="control-wrap">
			<select name="event_standard_events" id="event_standard_events" class="form-control js-dep">
				<?php foreach ( $page->get_standard_events() as $event => $fields ) : ?>
				<option
					value="<?php echo esc_attr( $event ); ?>"
					data-fields="<?php echo esc_attr( $fields ); ?>"
					<# if ( '<?php echo esc_html( $event ); ?>' == data.event ) { #>selected="selected"<# } #>
				><?php echo esc_html( $event ); ?></option>
				<?php endforeach; ?>
			</select><div class="field-helper">
				<?php $page->print_field_error( 'event_standard_events', '<span class="help-block help-block-error">', '</span>' ); ?>
			</div>
		</div>
	</div>

	<!-- Show only if custom_event is selected in the previous select -->
	<div class="sub-form-group form-vertical event_standard_events-CustomEvent">
		<div class="form-group<?php $page->field_class( 'event_name_custom' ); ?>">
			<label for="event_name_custom" class="control-label"><?php esc_html_e( 'Custom Event Name', 'pixel-caffeine' ); ?></label>
			<div class="control-wrap">
				<input type="text" class="form-control" name="event_name_custom" id="event_name_custom" value="{{ data.custom_event_name }}" placeholder="<?php esc_attr_e( 'Name...', 'pixel-caffeine' ); ?>">
				<div class="field-helper">
					<?php $page->print_field_error( 'event_name_custom', '<span class="help-block help-block-error">', '</span>' ); ?>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Show only if trigger on page visit -->
<div class="form-group event_trigger_on-page_visit<?php $page->field_class( 'event_fire_delay' ); ?>">
	<label for="event_fire_delay" class="control-label"><?php esc_html_e( 'Delay', 'pixel-caffeine' ); ?></label>
	<div class="control-wrap">
		<input type="text" class="form-control" name="event_fire_delay" id="event_fire_delay" value="{{ data.delay }}" placeholder="<?php esc_attr_e( 'seconds (leave empty for default)', 'pixel-caffeine' ); ?>">
		<div class="field-helper">
			<?php $page->print_field_error( 'event_fire_delay', '<span class="help-block help-block-error">', '</span>' ); ?>
		</div>
	</div>
</div>

<div class="multi-form-group">
	<div class="form-group form-toggle<?php $page->field_class( 'event_enable_advanced_data' ); ?>">
		<label for="event_enable_advanced_data" class="control-label"><?php esc_html_e( 'Pass Advanced Data', 'pixel-caffeine' ); ?></label>
		<div class="control-wrap">
			<div class="togglebutton">
				<label>
					<input
						type="checkbox"
						name="event_enable_advanced_data"
						id="event_enable_advanced_data"
						class="js-show-advanced-data js-switch-labeled-tosave"
						data-original-value="{{ data.pass_advanced_params }}"
					<# if ( 'yes' == data.pass_advanced_params ) { #>checked="checked"<# } #>>
				</label>
			</div>

			<?php if ( 'edit' === $action ) : ?>
				<# if ( 'yes' == data.pass_advanced_params ) { #>
					<span class="text-status text-status-on text-success"><?php esc_html_e( 'Advanced data is ON!', 'pixel-caffeine' ); ?></span>
				<# } else { #>
					<span class="text-status text-status-off text-danger"><?php esc_html_e( 'Advanced data is OFF!', 'pixel-caffeine' ); ?></span>
				<# } #>

			<?php else : ?>
					<span class="text-status text-status-off text-danger"><?php esc_html_e( 'Advanced data is OFF!', 'pixel-caffeine' ); ?></span>
			<?php endif; ?>
		</div>
	</div>

	<!-- Show only if togglebutton is checked -->
	<div class="sub-form-group advanced-data form-vertical collapse<?php echo 'edit' === $action ? '<# if ( \'yes\' != data.pass_advanced_params ) { #> in<# } #>' : ''; ?>">
		<div class="form-group event-field value-field<?php $page->field_class( 'event_field_value' ); ?>">
			<label for="event_field_value" class="control-label"><?php esc_html_e( 'Value', 'pixel-caffeine' ); ?></label>
			<div class="control-wrap">
				<input type="text" class="form-control" name="event_field_value" id="event_field_value" value="{{ data.params.value }}" placeholder="<?php esc_attr_e( 'Value', 'pixel-caffeine' ); ?>">
				<div class="field-helper">
					<?php $page->print_field_error( 'event_field_value', '<span class="help-block help-block-error">', '</span>' ); ?>
				</div>
			</div>
		</div>
		<div class="form-group event-field currency-field<?php $page->field_class( 'event_field_currency' ); ?>">
			<label for="event_field_currency" class="control-label"><?php esc_html_e( 'Currency', 'pixel-caffeine' ); ?></label>
			<div class="control-wrap">
				<select name="event_field_currency" id="event_field_currency" class="form-control" data-selected="{{ data.params.currency }}">
					<option></option>
					<?php echo $page->get_currency_dropdown(); //phpcs:ignore ?>
				</select>
				<div class="field-helper">
					<?php $page->print_field_error( 'event_field_currency', '<span class="help-block help-block-error">', '</span>' ); ?>
				</div>
			</div>
		</div>
		<div class="form-group event-field predicted_ltv-field<?php $page->field_class( 'event_field_predicted_ltv' ); ?>">
			<label for="event_field_predicted_ltv" class="control-label"><?php esc_html_e( 'Predicted lifetime value', 'pixel-caffeine' ); ?></label>
			<div class="control-wrap">
				<input type="text" class="form-control" name="event_field_predicted_ltv" id="event_field_predicted_ltv" value="{{ data.params.predicted_ltv }}" placeholder="<?php esc_attr_e( 'Predicted lifetime value', 'pixel-caffeine' ); ?>">
				<div class="field-helper">
					<?php $page->print_field_error( 'event_field_predicted_ltv', '<span class="help-block help-block-error">', '</span>' ); ?>
				</div>
			</div>
		</div>
		<div class="form-group event-field content_name-field<?php $page->field_class( 'event_field_content_name' ); ?>">
			<label for="event_field_content_name" class="control-label"><?php esc_html_e( 'Content Name', 'pixel-caffeine' ); ?></label>
			<div class="control-wrap">
				<input type="text" class="form-control" name="event_field_content_name" id="event_field_content_name" value="{{ data.params.content_name }}" placeholder="<?php esc_attr_e( 'Content Name', 'pixel-caffeine' ); ?>">
				<div class="field-helper">
					<?php $page->print_field_error( 'event_field_content_name', '<span class="help-block help-block-error">', '</span>' ); ?>
				</div>
			</div>
		</div>
		<div class="form-group event-field content_category-field<?php $page->field_class( 'event_field_content_currency' ); ?>">
			<label for="event_field_content_category" class="control-label"><?php esc_html_e( 'Content category', 'pixel-caffeine' ); ?></label>
			<div class="control-wrap">
				<input type="text" class="form-control" name="event_field_content_category" id="event_field_content_category" value="{{ data.params.content_category }}" placeholder="<?php esc_attr_e( 'Content category', 'pixel-caffeine' ); ?>">
				<div class="field-helper">
					<?php $page->print_field_error( 'event_field_content_currency', '<span class="help-block help-block-error">', '</span>' ); ?>
				</div>
			</div>
		</div>
		<div class="form-group event-field content_ids-field<?php $page->field_class( 'event_field_content_ids' ); ?>">
			<label for="event_field_content_ids" class="control-label"><?php esc_html_e( 'Content ids', 'pixel-caffeine' ); ?></label>
			<div class="control-wrap">
				<input type="text" class="form-control" name="event_field_content_ids" id="event_field_content_ids" value="{{ data.params.content_ids }}" placeholder="<?php esc_attr_e( 'Content ids', 'pixel-caffeine' ); ?>">
				<div class="field-helper">
					<?php $page->print_field_error( 'event_field_content_ids', '<span class="help-block help-block-error">', '</span>' ); ?>
				</div>
			</div>
		</div>
		<div class="form-group event-field content_type-field<?php $page->field_class( 'event_field_content_type' ); ?>">
			<label for="event_field_content_type" class="control-label"><?php esc_html_e( 'Content Type', 'pixel-caffeine' ); ?></label>
			<div class="control-wrap">
				<select name="event_field_content_type" id="event_field_content_type" class="form-control">
					<option></option>
					<option value="product"<# if ( 'product' == data.params.content_type ) { #> selected="selected"<# } #>><?php esc_html_e( 'Product', 'pixel-caffeine' ); ?></option>
							<option value="product_group"<# if ( 'product_group' == data.params.content_type ) { #> selected="selected"<# } #>><?php esc_html_e( 'Product Group', 'pixel-caffeine' ); ?></option>
				</select>
				<div class="field-helper">
					<?php $page->print_field_error( 'event_field_content_type', '<span class="help-block help-block-error">', '</span>' ); ?>
				</div>
			</div>
		</div>
		<div class="form-group event-field num_items-field<?php $page->field_class( 'event_field_num_items' ); ?>">
			<label for="event_field_num_items" class="control-label"><?php esc_html_e( 'Num items', 'pixel-caffeine' ); ?></label>
			<div class="control-wrap">
				<input type="text" class="form-control" name="event_field_num_items" id="event_field_num_items" value="{{ data.params.num_items }}" placeholder="<?php esc_attr_e( 'Num items', 'pixel-caffeine' ); ?>">
				<div class="field-helper">
					<?php $page->print_field_error( 'event_field_num_items', '<span class="help-block help-block-error">', '</span>' ); ?>
				</div>
			</div>
		</div>
		<div class="form-group event-field search_string-field<?php $page->field_class( 'event_field_search_string' ); ?>">
			<label for="event_field_search_string" class="control-label"><?php esc_html_e( 'Search string', 'pixel-caffeine' ); ?></label>
			<div class="control-wrap">
				<input type="text" class="form-control" name="event_field_search_string" id="event_field_search_string" value="{{ data.params.search_string }}" placeholder="<?php esc_attr_e( 'Search string', 'pixel-caffeine' ); ?>">
				<div class="field-helper">
					<?php $page->print_field_error( 'event_field_search_string', '<span class="help-block help-block-error">', '</span>' ); ?>
				</div>
			</div>
		</div>
		<div class="form-group event-field status-field<?php $page->field_class( 'event_field_field_status' ); ?>">
			<label for="event_field_status event-field status-field" class="control-label"><?php esc_html_e( 'Status', 'pixel-caffeine' ); ?></label>
			<div class="control-wrap">
				<input type="text" class="form-control" name="event_field_status" id="event_field_status" value="{{ data.params.status }}" placeholder="<?php esc_attr_e( 'Status', 'pixel-caffeine' ); ?>">
				<div class="field-helper">
					<?php $page->print_field_error( 'event_field_field_status', '<span class="help-block help-block-error">', '</span>' ); ?>
				</div>
			</div>
		</div>
		<div class="form-group">
			<label for="custom_params" class="control-label"><?php esc_html_e( 'Custom parameters', 'pixel-caffeine' ); ?></label>
			<div class="multiple-fields js-custom-params">

				<# _.each( data.custom_params, function( param, index ) { #>
					<div class="form-group js-custom-param">
						<div class="control-wrap">
							<input type="text" class="form-control" name="event_custom_params[{{ index }}][key]" value="{{ param.key }}" id="event_custom_params_key_{{ index }}" placeholder="<?php esc_attr_e( 'Key', 'pixel-caffeine' ); ?>">
						</div>
						<div class="control-wrap">
							<input type="text" class="form-control" name="event_custom_params[{{ index }}][value]" value="{{ param.value }}" id="event_custom_params_value_{{ index }}" placeholder="<?php esc_attr_e( 'Value', 'pixel-caffeine' ); ?>">
						</div>
						<div class="actions btn-group-sm">
							<a href="#_" class="btn btn-fab btn-delete btn-danger js-delete-custom-param"></a>
						</div>
					</div><!-- ./form-group -->
				<# } ); #>

				<div class="multiple-fields-actions js-conversion-actions">
					<button class="btn btn-raised btn-primary js-add-custom-param"><?php esc_html_e( 'Add parameter', 'pixel-caffeine' ); ?></button>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
$page->register_script_template(
	'custom-params',
	'
	<div class="form-group js-custom-param">
		<div class="control-wrap">
			<input type="text" class="form-control" name="event_custom_params[{{ data.index }}][key]" id="event_custom_params_key_{{ data.index }}" placeholder="' . __( 'Key', 'pixel-caffeine' ) . '">
		</div>
		<div class="control-wrap">
			<input type="text" class="form-control" name="event_custom_params[{{ data.index }}][value]" id="event_custom_params_value_{{ data.index }}" placeholder="' . __( 'Value', 'pixel-caffeine' ) . '">
		</div>
		<div class="actions btn-group-sm">
			<a href="#_" class="btn btn-fab btn-delete btn-danger js-delete-custom-param"></a>
		</div>
	</div>
'
);
?>
