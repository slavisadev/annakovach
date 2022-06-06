<?php
/**
 * General admin settings page
 *
 * This is the template with the HTML code for the General Settings admin page
 *
 * @var AEPC_Admin_View $page
 * @var string $title
 * @var string $message
 *
 * @package Pixel Caffeine
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<!-- FB Disconnect modal -->
<div id="modal-confirm-disconnect-fb" class="modal fade modal-centered modal-confirm modal-confirm-disconnect-fb" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h4 class="modal-title"><?php esc_html_e( 'Disconnect Your FB Account', 'pixel-caffeine' ); ?></h4>
			</div>
			<div class="modal-body">
				<p><?php esc_html_e( 'Are you sure you want to disconnect your FB Account?', 'pixel-caffeine' ); ?></p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php esc_html_e( 'No', 'pixel-caffeine' ); ?></button>
				<button type="button" class="btn btn-raised btn-danger btn-ok"><?php esc_html_e( 'Yes', 'pixel-caffeine' ); ?></button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
