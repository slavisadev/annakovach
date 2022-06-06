<?php
/**
 * Template Name: Advanced Taurus Thank You
 */
the_post();
get_header();
get_template_part( 'inc/header-parts/main' );
?>
    <section id="thankYou" class="mainContent">
        <div class="container">
			<?php the_content(); ?>
			<?php the_field( 'form_area' ); ?>
        </div>
    </section>
<?php
get_footer();