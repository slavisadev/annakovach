<header>
	<div class="headerContainer clearfix">
		<?php echo wp_get_attachment_image(get_field('logo_image', 'option'), 'full_width'); ?>
		<div class="headerHeading">
			<a href="<?php bloginfo('url'); ?>"><h1><?php the_field('logo-title', 'option'); ?></h1></a>
		</div>
	</div>
</header>
