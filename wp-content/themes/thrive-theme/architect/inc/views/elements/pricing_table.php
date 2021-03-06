<?php
/**
 * Created by PhpStorm.
 * User: Ovidiu
 * Date: 6/27/2018
 * Time: 2:07 PM
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

$instance_1 = rand( 0, 10000000 );

/**
 * This is for not to repeat this loooong code inside the template file
 */
$svg_icon_check = '<svg class="tcb-icon" viewBox="0 0 32 32" data-name="check"><path d="M29.333 10.267c0 0.4-0.133 0.8-0.533 1.2l-14.8 14.8c-0.267 0.267-0.667 0.4-1.067 0.4s-0.933-0.133-1.2-0.533l-2.4-2.267-6.267-6.267c-0.267-0.267-0.4-0.667-0.4-1.2s0.133-0.8 0.533-1.2l2.4-2.4c0.267-0.133 0.667-0.4 1.067-0.4s0.8 0.133 1.2 0.533l5.067 5.067 11.2-11.333c0.267-0.267 0.667-0.533 1.2-0.533 0.4 0 0.8 0.133 1.2 0.533l2.4 2.4c0.267 0.267 0.4 0.667 0.4 1.2z"></path></svg>';

?>
<div class="thrv_wrapper thrv-pricing-table" data-ct="pricing_table-24177" data-ct-name="Default Pricing Table">
	<div class="thrv_wrapper thrv-button-group tcb-no-clone tcb-no-delete tve_no_drag tcb-no-save tcb-permanently-hidden">
		<div class="thrv_wrapper thrv-button-group-item tcb-active-state tcb-no-clone tcb-no-delete tve_no_drag tcb-no-title tcb-no-save" data-default="true" data-instance="<?php echo esc_attr( $instance_1 ); ?>">
			<a href="#" class="tcb-button-link">
				<span class="tcb-button-texts"><span class="tcb-button-text thrv-inline-text">Instance 1</span></span>
			</a>
		</div>
	</div>
	<div class="tcb-flex-row tcb-pricing-table-box-container tcb--cols--3" data-instance="<?php echo esc_attr( $instance_1 ); ?>">
		<div class="tcb-flex-col" data-label="Basic">
			<div class="tcb-col">
				<div class="thrv_wrapper thrv_contentbox_shortcode thrv-content-box tcb-pt-cb-wrapper">
					<div class="tve-content-box-background tcb-pt-card"></div>
					<div class="tve-cb tcb-pt-card-content">
						<div class="thrv_wrapper thrv_heading" data-tag="h6">
							<h5 class="tcb-pt-card-title"><?php echo esc_html__( 'Basic', 'thrive-cb' ) ?></h5>
						</div>
						<div class="thrv_wrapper thrv_text_element tcb-pt-card-description">
							<p>
								<em><?php echo esc_html__( 'Simple, fast and effective flexible move', 'thrive-cb' ) ?></em>
							</p>
						</div>
						<div class="tcb-pt-wrapper">
							<div class="thrv_wrapper thrv_text_element tcb-pt-price tcb-pt-currency">
								<p><?php echo esc_html__( '$', 'thrive-cb' ) ?></p>
							</div>
							<div class="thrv_wrapper thrv_text_element tcb-pt-price tcb-pt-value">
								<p><?php echo esc_html__( '99.99', 'thrive-cb' ) ?></p>
							</div>
							<div class="thrv_wrapper thrv_text_element tcb-pt-price tcb-pt-period">
								<p><?php echo esc_html__( '/mo', 'thrive-cb' ) ?></p>
							</div>
						</div>
						<div class="thrv_wrapper thrv-styled_list" data-icon-code="icon-check">
							<ul class="tcb-styled-list">
								<li class="thrv-styled-list-item">
									<div class="tcb-styled-list-icon">
										<div class="thrv_wrapper thrv_icon tve_no_drag tcb-no-delete tcb-no-clone tcb-no-save tcb-icon-inherit-style">
											<?php echo $svg_icon_check; ?>
										</div>
									</div>
									<span class="thrv-advanced-inline-text tve_editable tcb-styled-list-icon-text tcb-no-delete tcb-no-save"><?php echo esc_html__( 'Working time 24/7 all days', 'thrive-cb' ) ?></span>
								</li>
								<li class="thrv-styled-list-item">
									<div class="tcb-styled-list-icon">
										<div class="thrv_wrapper thrv_icon tve_no_drag tcb-no-delete tcb-no-clone tcb-no-save tcb-icon-inherit-style">
											<?php echo $svg_icon_check; ?>
										</div>
									</div>
									<span class="thrv-advanced-inline-text tve_editable tcb-styled-list-icon-text tcb-no-delete tcb-no-save"><?php echo esc_html__( 'Free Tea & Coffee', 'thrive-cb' ) ?></span>
								</li>
								<li class="thrv-styled-list-item">
									<div class="tcb-styled-list-icon">
										<div class="thrv_wrapper thrv_icon tve_no_drag tcb-no-delete tcb-no-clone tcb-no-save tcb-icon-inherit-style">
											<?php echo $svg_icon_check; ?>
										</div>
									</div>
									<span class="thrv-advanced-inline-text tve_editable tcb-styled-list-icon-text tcb-no-delete tcb-no-save"><?php echo esc_html__( 'Max 15 team members', 'thrive-cb' ) ?></span>
								</li>
								<li class="thrv-styled-list-item">
									<div class="tcb-styled-list-icon">
										<div class="thrv_wrapper thrv_icon tve_no_drag tcb-no-delete tcb-no-clone tcb-no-save tcb-icon-inherit-style">
											<?php echo $svg_icon_check; ?>
										</div>
									</div>
									<span class="thrv-advanced-inline-text tve_editable tcb-styled-list-icon-text tcb-no-delete tcb-no-save"><?php echo esc_html__( 'Superfast wifi', 'thrive-cb' ) ?></span>
								</li>
								<li class="thrv-styled-list-item">
									<div class="tcb-styled-list-icon">
										<div class="thrv_wrapper thrv_icon tve_no_drag tcb-no-delete tcb-no-clone tcb-no-save tcb-icon-inherit-style">
											<?php echo $svg_icon_check; ?>
										</div>
									</div>
									<span class="thrv-advanced-inline-text tve_editable tcb-styled-list-icon-text tcb-no-delete tcb-no-save"><?php echo esc_html__( 'Free Kitchen', 'thrive-cb' ) ?></span>
								</li>
							</ul>
						</div>
						<div class="thrv_wrapper thrv-button tcb-pt-button">
							<a href="#" class="tcb-button-link tcb-pt-button-link">
								<span class="tcb-button-texts"><span class="tcb-button-text thrv-inline-text"><?php echo esc_html__( 'Book Now', 'thrive-cb' ) ?></span></span>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="tcb-flex-col" data-label="Fulltime">
			<div class="tcb-col">
				<div class="thrv_wrapper thrv_contentbox_shortcode thrv-content-box tcb-excluded-from-group-item tcb-pt-cb-wrapper tcb-pt-featured-box">
					<div class="tve-content-box-background tcb-pt-card"></div>
					<div class="tve-cb tcb-pt-card-content">
						<div class="thrv_wrapper thrv_heading" data-tag="h6">
							<h5 class="tcb-pt-card-title"><?php echo esc_html__( 'Fulltime', 'thrive-cb' ) ?></h5>
						</div>
						<div class="thrv_wrapper thrv_text_element tcb-pt-card-description">
							<p>
								<em><?php echo esc_html__( 'Creative working space, not noisy, fully equipped and convenient', 'thrive-cb' ) ?></em>
							</p>
						</div>
						<div class="tcb-pt-wrapper">
							<div class="thrv_wrapper thrv_text_element tcb-pt-price tcb-pt-currency">
								<p><?php echo esc_html__( '$', 'thrive-cb' ) ?></p>
							</div>
							<div class="thrv_wrapper thrv_text_element tcb-pt-price tcb-pt-value">
								<p><?php echo esc_html__( '199.99', 'thrive-cb' ) ?></p>
							</div>
							<div class="thrv_wrapper thrv_text_element tcb-pt-price tcb-pt-period">
								<p><?php echo esc_html__( '/mo', 'thrive-cb' ) ?></p>
							</div>
						</div>
						<div class="thrv_wrapper thrv-styled_list" data-icon-code="icon-check">
							<ul class="tcb-styled-list">
								<li class="thrv-styled-list-item">
									<div class="tcb-styled-list-icon">
										<div class="thrv_wrapper thrv_icon tve_no_drag tcb-no-delete tcb-no-clone tcb-no-save tcb-icon-inherit-style">
											<?php echo $svg_icon_check; ?>
										</div>
									</div>
									<span class="thrv-advanced-inline-text tve_editable tcb-styled-list-icon-text tcb-no-delete tcb-no-save"><?php echo esc_html__( 'Working time 24/7 all days', 'thrive-cb' ) ?></span>
								</li>
								<li class="thrv-styled-list-item">
									<div class="tcb-styled-list-icon">
										<div class="thrv_wrapper thrv_icon tve_no_drag tcb-no-delete tcb-no-clone tcb-no-save tcb-icon-inherit-style">
											<?php echo $svg_icon_check; ?>
										</div>
									</div>
									<span class="thrv-advanced-inline-text tve_editable tcb-styled-list-icon-text tcb-no-delete tcb-no-save"><?php echo esc_html__( 'Free Tea & Coffee', 'thrive-cb' ) ?></span>
								</li>
								<li class="thrv-styled-list-item">
									<div class="tcb-styled-list-icon">
										<div class="thrv_wrapper thrv_icon tve_no_drag tcb-no-delete tcb-no-clone tcb-no-save tcb-icon-inherit-style">
											<?php echo $svg_icon_check; ?>
										</div>
									</div>
									<span class="thrv-advanced-inline-text tve_editable tcb-styled-list-icon-text tcb-no-delete tcb-no-save"><?php echo esc_html__( 'Max 15 team members', 'thrive-cb' ) ?></span>
								</li>
								<li class="thrv-styled-list-item">
									<div class="tcb-styled-list-icon">
										<div class="thrv_wrapper thrv_icon tve_no_drag tcb-no-delete tcb-no-clone tcb-no-save tcb-icon-inherit-style">
											<?php echo $svg_icon_check; ?>
										</div>
									</div>
									<span class="thrv-advanced-inline-text tve_editable tcb-styled-list-icon-text tcb-no-delete tcb-no-save"><?php echo esc_html__( 'Superfast wifi', 'thrive-cb' ) ?></span>
								</li>
								<li class="thrv-styled-list-item">
									<div class="tcb-styled-list-icon">
										<div class="thrv_wrapper thrv_icon tve_no_drag tcb-no-delete tcb-no-clone tcb-no-save tcb-icon-inherit-style">
											<?php echo $svg_icon_check; ?>
										</div>
									</div>
									<span class="thrv-advanced-inline-text tve_editable tcb-styled-list-icon-text tcb-no-delete tcb-no-save"><?php echo esc_html__( '1 free meeting room', 'thrive-cb' ) ?></span>
								</li>
								<li class="thrv-styled-list-item">
									<div class="tcb-styled-list-icon">
										<div class="thrv_wrapper thrv_icon tve_no_drag tcb-no-delete tcb-no-clone tcb-no-save tcb-icon-inherit-style">
											<?php echo $svg_icon_check; ?>
										</div>
									</div>
									<span class="thrv-advanced-inline-text tve_editable tcb-styled-list-icon-text tcb-no-delete tcb-no-save"><?php echo esc_html__( 'Free Kitchen', 'thrive-cb' ) ?></span>
								</li>
							</ul>
						</div>
						<div class="thrv_wrapper thrv-button tcb-pt-button tcb-excluded-from-group-item">
							<a href="#" class="tcb-button-link tcb-pt-button-link">
								<span class="tcb-button-texts"><span class="tcb-button-text thrv-inline-text"><?php echo esc_html__( 'Book Now', 'thrive-cb' ) ?></span></span>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="tcb-flex-col" data-label="Private">
			<div class="tcb-col">
				<div class="thrv_wrapper thrv_contentbox_shortcode thrv-content-box tcb-pt-cb-wrapper">
					<div class="tve-content-box-background tcb-pt-card"></div>
					<div class="tve-cb tcb-pt-card-content">
						<div class="thrv_wrapper thrv_heading" data-tag="h5">
							<h5 class="tcb-pt-card-title"><?php echo esc_html__( 'Private', 'thrive-cb' ) ?></h5>
						</div>
						<div class="thrv_wrapper thrv_text_element tcb-pt-card-description">
							<p>
								<em><?php echo esc_html__( 'Simple, fast and effective flexible move', 'thrive-cb' ) ?></em>
							</p>
						</div>
						<div class="tcb-pt-wrapper">
							<div class="thrv_wrapper thrv_text_element tcb-pt-price tcb-pt-currency">
								<p><?php echo esc_html__( '$', 'thrive-cb' ) ?></p>
							</div>
							<div class="thrv_wrapper thrv_text_element tcb-pt-price tcb-pt-value">
								<p><?php echo esc_html__( '299.99', 'thrive-cb' ) ?></p>
							</div>
							<div class="thrv_wrapper thrv_text_element tcb-pt-price tcb-pt-period">
								<p><?php echo esc_html__( '/mo', 'thrive-cb' ) ?></p>
							</div>
						</div>
						<div class="thrv_wrapper thrv-styled_list" data-icon-code="icon-check">
							<ul class="tcb-styled-list">
								<li class="thrv-styled-list-item">
									<div class="tcb-styled-list-icon">
										<div class="thrv_wrapper thrv_icon tve_no_drag tcb-no-delete tcb-no-clone tcb-no-save tcb-icon-inherit-style">
											<?php echo $svg_icon_check; ?>
										</div>
									</div>
									<span class="thrv-advanced-inline-text tve_editable tcb-styled-list-icon-text tcb-no-delete tcb-no-save"><?php echo esc_html__( 'Working time 24/7 all days', 'thrive-cb' ) ?></span>
								</li>
								<li class="thrv-styled-list-item">
									<div class="tcb-styled-list-icon">
										<div class="thrv_wrapper thrv_icon tve_no_drag tcb-no-delete tcb-no-clone tcb-no-save tcb-icon-inherit-style">
											<?php echo $svg_icon_check; ?>
										</div>
									</div>
									<span class="thrv-advanced-inline-text tve_editable tcb-styled-list-icon-text tcb-no-delete tcb-no-save"><?php echo esc_html__( 'Free Tea & Coffee', 'thrive-cb' ) ?></span>
								</li>
								<li class="thrv-styled-list-item">
									<div class="tcb-styled-list-icon">
										<div class="thrv_wrapper thrv_icon tve_no_drag tcb-no-delete tcb-no-clone tcb-no-save tcb-icon-inherit-style">
											<?php echo $svg_icon_check; ?>
										</div>
									</div>
									<span class="thrv-advanced-inline-text tve_editable tcb-styled-list-icon-text tcb-no-delete tcb-no-save"><?php echo esc_html__( 'Max 15 team members', 'thrive-cb' ) ?></span>
								</li>
								<li class="thrv-styled-list-item">
									<div class="tcb-styled-list-icon">
										<div class="thrv_wrapper thrv_icon tve_no_drag tcb-no-delete tcb-no-clone tcb-no-save tcb-icon-inherit-style">
											<?php echo $svg_icon_check; ?>
										</div>
									</div>
									<span class="thrv-advanced-inline-text tve_editable tcb-styled-list-icon-text tcb-no-delete tcb-no-save"><?php echo esc_html__( 'Superfast wifi', 'thrive-cb' ) ?></span>
								</li>
								<li class="thrv-styled-list-item">
									<div class="tcb-styled-list-icon">
										<div class="thrv_wrapper thrv_icon tve_no_drag tcb-no-delete tcb-no-clone tcb-no-save tcb-icon-inherit-style">
											<?php echo $svg_icon_check; ?>
										</div>
									</div>
									<span class="thrv-advanced-inline-text tve_editable tcb-styled-list-icon-text tcb-no-delete tcb-no-save"><?php echo esc_html__( 'Free Kitchen', 'thrive-cb' ) ?></span>
								</li>
							</ul>
						</div>
						<div class="thrv_wrapper thrv-button tcb-pt-button">
							<a href="#" class="tcb-button-link tcb-pt-button-link">
								<span class="tcb-button-texts"><span class="tcb-button-text thrv-inline-text"><?php echo esc_html__( 'Book Now', 'thrive-cb' ) ?></span></span>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
