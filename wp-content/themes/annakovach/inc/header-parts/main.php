<header>
  <div class="headerContainer clearfix">
      <?php

      $headerTitle1 = get_field('header_text1');
      if (!$headerTitle1) {
          $headerTitle1 = 'ANNA KOVACH\'S';
      }
      
      $headerTitle2 = get_field('header_text2');
      if (!$headerTitle2) {
          $headerTitle2 = 'LOVE COMPATIBILITY READING';
      }

      $headerImage = get_field('header_image');
      if (!$headerImage) {
          $custom_logo_id = get_theme_mod('custom_logo');
          $image    = wp_get_attachment_image_src($custom_logo_id, 'full');
          $headerImage = $image[0];
      }

      ?>
    <img src="<?php echo $headerImage; ?>" alt="Anna Kovach Logo">
    <div class="headerHeading">
      <a href="<?php bloginfo('url'); ?>">
        <h1>

            <?php echo $headerTitle1 ?><br><span><?php echo $headerTitle2 ?></span>
        </h1>
      </a>
    </div>
  </div>
</header>
