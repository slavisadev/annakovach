<?php
/**
 * Template Name: Cheat sheet Confirmation
 */
get_header();
the_post();
?>
<section>
    <div class="container clearfix">
        <div class="center">
			<?php the_content(); ?>
        </div>
    </div>
</section>
<?php
get_footer();