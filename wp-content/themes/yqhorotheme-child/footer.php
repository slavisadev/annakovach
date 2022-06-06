<footer>
  <div class="container">
    <div class="clearfix">  <?php wp_nav_menu(['menu' => 'Main Menu']); ?></div>

    <div>
        <small><?php the_field('main_footer_text', 'option') ?></small>
    </div>
    <small>&copy; AnnaKovach.com <?php echo date('Y') ?>. All Rights Reserved</small>
  </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
