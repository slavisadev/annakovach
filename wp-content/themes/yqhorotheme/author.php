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
                <div class="content">
                    <div id="post-<?php echo get_the_ID(); ?>" class="post_box grt top">
                        <div class="post_content" itemprop="text">
                            <?php
                            $curauth = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author));
                            ?>

                            <h2>Posts by <?php echo $curauth->nickname; ?>:</h2>

                            <ul>
                                <!-- The Loop -->

                                <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                                    <li>
                                        <a href="<?php the_permalink() ?>" rel="bookmark"
                                           title="Permanent Link: <?php the_title(); ?>">
                                            <?php the_title(); ?></a>,
                                        <?php the_time('d M Y'); ?> in <?php the_category('&'); ?>
                                    </li>

                                <?php endwhile;
                                else: ?>
                                    <p><?php _e('No posts by this author.'); ?></p>

                                <?php endif; ?>

                                <!-- End Loop -->

                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <?php get_template_part('inc/sidebar'); ?>
        </div>
    </div>
<?php
get_footer('blog');