<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package capricornmansecrets
 */
get_header();
?>
    <div class="container">
        <span class="menu_control">≡ Menu</span>
        <div id="header" class="header">
            <h1 id="site_title"><a href="<?php bloginfo('url'); ?>/blog"><?php the_field('blog-title', 1891); ?></a>
            </h1>
            <div id="site_tagline"><?php the_field('blog-subtitle', 1891); ?></div>
        </div>
        <div class="columns">
            <div class="content">
                <div class="archive_intro post_box grt top">
                    <h1 class="archive_title headline">Search: <?php echo $_GET['s']; ?></h1>
                </div>
                <?php
                $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

                $args = [
                    'showposts' => 10,
                    'post_type' => 'post',
                    'paged' => $paged,
                    's' => $_GET['s']
                ];

                $query = new WP_Query($args);
                if ($query->have_posts()) :
                    while ($query->have_posts()) :
                        $query->the_post(); ?>

                        <div id="<?php echo get_the_ID(); ?>" class="post_box grt" itemscope=""
                             itemtype="http://schema.org/BlogPosting">
                            <div class="headline_area">
                                <h2 class="headline" itemprop="headline">
                                    <a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
                                </h2>
                                <div class="byline small">
                                </div>
                            </div>

                        </div>
                        <?php
                    endwhile;
                else:
                    ?>
                    <div class="error">
                        No Posts
                    </div>
                <?php endif; ?>
                <div class="prev_next">
                    <?php
                    global $wp_query;
                    echo paginate_links([
                        'format' => '?paged=%#%',
                        'total' => $query->max_num_pages,
                        'next_text' => '<span class="next_posts">Next Posts</span>',
                        'prev_text' => '<span class="previous_posts">Previous Posts</span>'
                    ]);
                    ?>
                </div>
                <?php wp_reset_query(); ?>
            </div>
            <?php get_template_part('inc/sidebar'); ?>
        </div>
    </div>
<?php

get_footer();