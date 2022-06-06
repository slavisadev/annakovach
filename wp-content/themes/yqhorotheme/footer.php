<footer>
  <div class="container">
      <?php wp_nav_menu(['menu' => 'Main Menu']); ?>
    <br>
    <small><?php the_field('main_footer_text', 'option') ?></small>
    <small>&copy; <?php echo HORO_NAMESPACE_CAP ?> Man Secrets <?php echo date('Y') ?>. All Rights Reserved</small>
  </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
