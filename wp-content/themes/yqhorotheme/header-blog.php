<!DOCTYPE html>
<html lang="en-US">
<head>
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="icon" href="<?php echo get_stylesheet_directory_uri() ?>/favicon.ico" type="image/x-icon">

    <?php
    the_field('facebook_pixel', 'option');
    the_field('google_analitycs', 'option');

    wp_head();
    ?>
</head>

<body <?php body_class() ?>>

<div class='js-off-canvas-container c-off-canvas-container'>

  <header class='c-header'>
    <div class='o-grid'>

      <div class='o-grid__col o-grid__col--3-4-s o-grid__col--4-4-m'>
          <?php if (is_single()): ?>
            <h1>
              <a class="c-logo-link" href="<?php bloginfo('url'); ?>">
                <img src="<?php the_field('logo_blog', 'option'); ?>" alt="">
              </a>
            </h1>
          <?php else: ?>
            <h1>
              <a class="c-logo-link" href="<?php bloginfo('url'); ?>">
                <img src="<?php the_field('logo_blog', 'option'); ?>" alt="">
              </a>
            </h1>
          <?php endif; ?>
      </div>

      <div class='o-grid__col o-grid__col--1-4-s o-grid__col--3-4-l o-grid__col--full'>
        <div class='c-off-canvas-content js-off-canvas-content'>
          <label class='js-off-canvas-toggle c-off-canvas-toggle c-off-canvas-toggle--close'>
            <span class='c-off-canvas-toggle__icon'></span>
          </label>

          <div class='o-grid'>
            <div class='o-grid__col o-grid__col--4-4-s o-grid__col--3-4-l o-grid__col--full'>

              <h2 class='screen-reader-text'>Primary Navigation</h2>

              <nav class='c-nav-wrap'>
                <ul class='c-nav o-plain-list'>
                  <li id="menu-item-300" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-300">
                    <a href="<?php bloginfo('url') ?>/contact/">Contact</a>
                  </li>
                </ul>
              </nav>
            </div>
          </div>
        </div>

        <label class='js-off-canvas-toggle c-off-canvas-toggle' aria-label='Toggle navigation'>
          <span class='c-off-canvas-toggle__icon'></span>
        </label>
      </div>

    </div>
  </header>
