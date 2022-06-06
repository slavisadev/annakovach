<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

?>

[tcb_post_title]

[tcb_post_featured_image]

<div class="thrv_wrapper thrv-columns">
	<div class="tcb-flex-row tcb--cols--3">
		<div class="tcb-flex-col">
			<div class="tcb-col">
				[tcb_post_published_date]
			</div>
		</div>
		<div class="tcb-flex-col">
			<div class="tcb-col">
				[tcb_post_author_name]
			</div>
		</div>
		<div class="tcb-flex-col">
			<div class="tcb-col">
				<div class="tcb-clear">
					<div class="thrv_wrapper thrv_text_element theme-comments-number">
						<p>
							<span class="thrive-shortcode-content" data-id="tcb_post_comments_number"
								  data-shortcode="tcb_post_comments_number inline='1' url='0'" data-url="0">[tcb_post_comments_number inline='1' url='0']</span>
							<?php echo __( 'comments', THEME_DOMAIN ); ?>
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

[tcb_post_content size="content"]
