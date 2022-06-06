<?php
/**
 * Template Name: Special offer
 */
get_header();
the_post();
get_template_part( 'inc/header-parts/main' );
?>
<section class="mainContent">
    <?php the_content(); ?>
    <div class="container">
        <?php the_field('before_form'); ?>
        <?php the_field('form'); ?>
        <?php the_field('after_form'); ?>
    </div>
</section>
<?php
get_footer();