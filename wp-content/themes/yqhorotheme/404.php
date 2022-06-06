<?php
get_header('blog');
the_post();
?>
    <div class="container">
        <span class="menu_control">≡ Menu</span>
        <div id="header" class="header">
            <h1 id="site_title"><a href="<?php bloginfo('url') ?>/blog"><?php bloginfo('site_title') ?> — Anna
                    Kovach's Blog</a></h1>
        </div>
        <div class="columns">
            <div class="content">
                <div class="post_box grt top">

                    <h1>Page Not Found</h1>
                    <p>Sorry, but the page you were trying to view does not exist.</p>
                </div>
            </div>
            <?php get_template_part('inc/sidebar'); ?>
        </div>
    </div>
<?php
get_footer('blog');