<?php
/**
 * Template Name: Blog
 */

get_header();
?>
    <div class="container">
        <span class="menu_control">≡ Menu</span>
        <div id="header" class="header">
            <h1 id="site_title"><a href="<?php bloginfo('url'); ?>"><?php the_field('blog-title'); ?></a></h1>
            <div id="site_tagline"><?php the_field('blog-subtitle'); ?></div>
        </div>
        <div class="columns">
            <div class="content">
                <?php
                $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

                $args = array(
                    'showposts' => 10,
                    'post_type' => 'post',
                    'paged' => $paged
                );

                $query = new WP_Query($args);

                if ($query->have_posts()) :
                    while ($query->have_posts()) :
                        $query->the_post(); ?>

                        <div id="<?php echo get_the_ID(); ?>" class="post_box grt" itemscope=""
                             itemtype="http://schema.org/BlogPosting">
                            <div class="headline_area">
                                <h2 class="headline text-left" itemprop="headline"><a
                                            href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
                                </h2>
                                <div class="byline small">
                                    <span class="post_author_intro">by</span>
                                    <span class="post_author" itemprop="author"><?php echo get_the_author(); ?></span>
                                </div>
                            </div>
                            <a class="featured_image_link" href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('blog_featured'); ?>
                            </a>
                            <div class="post_content" itemprop="text">
                                <p class="p1"><span class="s1"><?php echo get_the_excerpt(); ?></span></p>
                                <p class="p1"><a href="<?php the_permalink(); ?>/#more-<?php echo get_the_ID(); ?>"
                                                 class="more-link">[click to continue…]</a></p>
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
