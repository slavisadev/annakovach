<?php
/**
 * Template Name: Advanced Special Offer
 */
the_post();
get_header();
get_template_part( 'inc/header-parts/main' );
?>
    <div class="orderSteps">
        <ul class="clearfix">
            <li>1. Checkout</li>
            <li class="selectedStep">2. Product Options</li>
            <li>3. Instant Access</li>
        </ul>
    </div>
    <section class="mainContent">
        <div class="container">
            <?php the_content(); ?>
        </div>
    </section>
<?php
get_footer();