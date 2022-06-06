<?php
/**
 * Template Name: 30 Secrets Thank You
 */
get_header();
the_post();
?>
  <section>
    <div class="container">
        <?php the_content(); ?>
    </div>
  </section>
<?php if (get_field('content_block_1')) : ?>
  <div class="container line-break"></div>
  <div class="container">
      <?php the_field('content_block_1'); ?>
  </div>
  <div class="container">
      <?php
      if (have_rows('main_repeater')):
          while (have_rows('main_repeater')) : the_row();
              ?>
            <div class="left-right-box clearfix">
              <div class="left">
                <h2><?php the_sub_field('heading'); ?></h2>
                <img src="<?php echo get_sub_field('image')['url']; ?>" alt="img">
              </div>
              <div class="right">
                  <?php the_sub_field('list'); ?>
              </div>
            </div>
          <?php
          endwhile;
      else :
          echo '';
      endif;
      ?>
  </div>
  <div class="container line-break"></div>
  <div class="container">
      <?php the_field('content'); ?>
  </div>
  <div class="container line-break"></div>
  <section>
    <div class="container">
      <h2><?php the_field('title2'); ?></h2>
        <?php
        if (have_rows('main_repeater2')):
            while (have_rows('main_repeater2')) : the_row();
                ?>
              <div class="testimonial-box">
                <p><?php the_sub_field('textarea1'); ?></p>
              </div>
            <?php
            endwhile;
        else :
            echo '';
        endif;
        ?>
    </div>
  </section>
  <section>
    <div class="container">
      <div class="left-right-box clearfix">
        <div class="left">
          <p><?php the_field('special_platinum_bonus_l'); ?></p>
        </div>
        <div class="right">
          <p><?php the_field('special_platinum_bonus_r'); ?></p>
        </div>
      </div>
    </div>
  </section>
  <section>
    <div class="container">
        <?php the_field('special_platinum_bonus_b'); ?>
    </div>
  </section>
  <section>
    <div class="container">
        <?php the_field('content3'); ?>
    </div>
  </section>
<?php endif; ?>
<?php
get_footer();
