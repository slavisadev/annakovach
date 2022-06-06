<?php
/**
 * Template Name: Homepage
 */
get_header();
get_template_part('inc/header-parts/main');
?>

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PX348M8"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

  <section class="mainContent">
    <div class="container">
      <p><i><?php the_field('before_title'); ?></i></p>
      <h2 class="mainHeading"><?php the_field('main_title'); ?></h2>
        <?php the_field('content'); ?>
    </div>
    <div class="listBlock special">
      <div class="container">
        <h2><?php the_field('pink_block_1'); ?></h2>
      </div>
    </div>
    <div class="container">
        <?php the_field('content2'); ?>
    </div>
    <div class="guarantee">
      <div class="container clearfix">
          <?php the_field('content4'); ?>
      </div>
    </div>
    <div class="container">
        <?php the_field('content_5'); ?>

    </div>
  </section>
<?php
get_footer();
