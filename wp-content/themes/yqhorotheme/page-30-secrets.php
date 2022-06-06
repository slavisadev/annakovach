<?php
/**
 * Template Name: 30 secrets
 */
get_header();
the_post();
$image = get_field( 'image' );
?>
    <section>
        <div class="container clearfix">
            <h1><?php the_field( 'title' ); ?></h1>
            <div class="left">
				<?php if ( $image ) {
					echo '<img src="' . $image["url"] . '" alt="' . $image["alt"] . '">';
				} ?>
            </div>
            <div class="right">
				<?php the_content(); ?>
				<?php the_field( 'form' ); ?>
            </div>
        </div>
    </section>
<?php
get_footer();