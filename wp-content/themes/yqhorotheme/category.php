<?php get_header('blog'); ?>
    <div class="container">
        <span class="menu_control">≡ Menu</span>
        <div id="header" class="header">
            <h1 id="site_title"><a href="<?php bloginfo('url') ?>/blog">Taurus Man Secrets — Anna Kovach's
                    Blog</a></h1>
        </div>
        <div class="columns">
            <div class="content">
                <?php
                $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

                if (have_posts()) :
                    while (have_posts()) :
                        the_post(); ?>

                        <div id="<?php echo get_the_ID(); ?>" class="post_box grt" itemscope=""
                             itemtype="http://schema.org/BlogPosting">
                            <div class="headline_area">
                                <h2 class="headline" itemprop="headline"><a
                                            href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
                                </h2>
                                <div class="byline small">
                                </div>
                            </div>
                            <a class="featured_image_link" href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('blog_featured'); ?>
                            </a>
                            <div class="post_content" itemprop="text">
                                <p><?php echo get_the_excerpt(); ?> </p>
                                <p><a href="<?php the_permalink(); ?>/#more-<?php echo get_the_ID(); ?>"
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
                        'total' => $wp_query->max_num_pages,
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

get_footer('blog');