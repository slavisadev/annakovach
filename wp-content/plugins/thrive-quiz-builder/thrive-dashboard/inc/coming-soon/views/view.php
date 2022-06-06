<div class="view-page-wrapper">
	<span class="page-title">
		<#= pageName #>
	</span>
	<div class="page-options">
		<a class="page-option option-view-page" target="_blank" href="<#= previewUrl #>">
			<span class="tvd-icon-eye"></span>
			<?php echo __( 'View', TVE_DASH_TRANSLATE_DOMAIN ); ?>
		</a>
		<span class="tvd-sep"></span>
		<a class="page-option option-edit-page" target="_blank" href="<#= editUrl #>">
			<span class="tvd-icon-architect"></span>
			<?php echo __( 'Edit with Thrive Architect', TVE_DASH_TRANSLATE_DOMAIN ); ?>
		</a>
		<span class="tvd-sep"></span>
		<button class="page-option option-remove-page enable-state" data-enable="delete" target="_blank">
			<span class="tvd-icon-delete"></span>
			<?php echo __( 'Remove page', TVE_DASH_TRANSLATE_DOMAIN ); ?>
		</button>
	</div>
</div>