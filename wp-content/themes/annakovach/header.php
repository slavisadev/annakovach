<!doctype html>
<html class="no-js">
<head>
	<meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="<?php bloginfo('template_directory') ?>/favicon.ico" type="image/x-icon">
    <?php the_field('facebook_pixel', 'options'); ?>
    <?php the_field('google_analitycs', 'options'); ?>
    <?php the_field('google_tag_manager', 'options'); ?>
    <?php
    the_field('ad_words', 'option');
    the_field('event_snippet');
    ?>
    <?php wp_head(); ?>
	
	<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-228687753-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-228687753-1');
</script>
	
</head>
<body <?php body_class(); ?>>
<?php the_field('google_tag_manager_body', 'options'); ?>
	
	