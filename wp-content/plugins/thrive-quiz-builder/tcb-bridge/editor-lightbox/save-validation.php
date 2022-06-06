<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}
?>

<span class="tcb-modal-title"><?php echo esc_html__( 'Links missing', Thrive_Quiz_Builder::T ) ?></span>
<div class="margin-top-20">
	<?php echo esc_html__( 'Your page has neither a link to the next step in the quiz nor a form connected to the email service, so your visitors will be blocked on this step.', Thrive_Quiz_Builder::T ) ?>
	<a class="tqb-open-external-link" href="<?php echo esc_url( Thrive_Quiz_Builder::KB_NEXT_STEP_ARTICLE ); ?>" target="_blank"><?php echo esc_html__( 'Learn how to add links to the next step in the quiz.', Thrive_Quiz_Builder::T ) ?></a>
</div>
<div class="tcb-modal-footer flex-end">
	<button type="button" class="tcb-right tve-button medium red click" data-fn="close">
		<?php echo esc_html__( 'Continue', Thrive_Quiz_Builder::T ) ?>
	</button>
</div>
