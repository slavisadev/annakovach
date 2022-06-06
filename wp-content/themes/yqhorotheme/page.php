<?php
the_post();
get_header();
get_template_part( 'inc/header-parts/main' );
?>
    <section id="<?php echo clbs_get_page_id( get_the_title() ) ?>" class="mainContent">
        <div class="container">
			<?php the_content(); ?>
        </div>
    </section>
<?php
get_footer();