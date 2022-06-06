<?php
get_header();
?>
<div class="container">
    <span class="menu_control">≡ Menu</span>
    <ul class="menu"></ul>
    <div id="header" class="header">
        <h1 id="site_title"><a href="<?php bloginfo( 'url' ); ?>/blog"><?php the_field( 'blog-title',731 ); ?></a>
        </h1>
        <div id="site_tagline"><?php the_field( 'blog-subtitle',731 ); ?></div>
    </div>
    <div class="columns">
        <div class="content">
            <div class="post_box grt top">

                <h1>Page Not Found</h1>
                <p>Sorry, but the page you were trying to view does not exist.</p>
            </div>
        </div>
		<?php get_template_part( 'inc/sidebar' ); ?>
    </div>
</div>
<?php
get_footer();