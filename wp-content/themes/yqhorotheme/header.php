<!doctype html>
<html class="no-js">
<head>
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="icon" href="<?php echo get_stylesheet_directory_uri() ?>/favicon.ico" type="image/x-icon">

    <?php
    if (is_page('blog') || is_single()) {
        the_field('facebook_pixel', 'option');
    }
    the_field('google_analitycs', 'option');
    the_field('lucky_orange', 'option');
    the_field('ad_words', 'option');
    the_field('event_snippet');

    wp_head();
    ?>
</head>
<body <?php body_class(); ?> <?php echo get_body_data(); ?>>

<?php

if (is_page('thank-you-for-your-purchase')) {
    the_field('facebook_pixel', 'option');
}

if (is_page('30-secrets-confirmation')) {
    the_field('facebook_pixel', 'option');
    ?>
  <script> fbq('track', 'Lead'); </script>
    <?php
}

if (is_front_page()) {
    ?>
  <!-- Add Pixel Events to the button's click handler -->
  <script type="text/javascript">
  var buttons = document.getElementsByClassName('orderButton');
  for (var i = 0; i < buttons.length; i++) {
    buttons[ i ].addEventListener(
      'click',
      function () {
        fbq('track', 'AddToCart');
      },
      false,
    );
  }
  </script>
    <?php
}

?>

<?php the_field('tag-manager', 'option'); ?>
