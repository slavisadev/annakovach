<?php
/**
 * Template Name: Blog
 */
get_header('blog');
?>

  <div class='o-wrapper' id='content'>


    <div class='o-grid js-grid'>
        <?php
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

        $args = [
            'posts_per_page' => 14,
            'post_type'      => 'post',
            'post_status'      => 'publish',
            'paged'          => $paged,
        ];

        $query = new WP_Query($args);

        if ($query->have_posts()) :
            $counter = 1;
            while ($query->have_posts()) :
                $query->the_post();

                if ($counter === 1) {
                    $classValue    = 'o-grid__col o-grid__col--4-4-s o-grid__col--2-4-m o-grid__col--1-3-l c-post-card-wrap js-post-card-wrap o-grid__col--4-4-m o-grid__col--2-3-l';
                    $class2        = 'c-post-card c-post-card--first';
                    $featuredImage = get_the_post_thumbnail_url(get_the_ID(), 'blog_featured_long');
                } elseif ($counter === 2) {
                    $classValue    = 'o-grid__col o-grid__col--4-4-s o-grid__col--2-4-m o-grid__col--1-3-l c-post-card-wrap js-post-card-wrap';
                    $class2        = 'c-post-card c-post-card--half';
                    $featuredImage = get_the_post_thumbnail_url(get_the_ID(), 'blog_featured_square');
                } else {
                    $classValue    = 'o-grid__col o-grid__col--4-4-s o-grid__col--2-4-m o-grid__col--1-3-l c-post-card-wrap js-post-card-wrap';
                    $class2        = 'c-post-card c-post-card--first';
                    $featuredImage = get_the_post_thumbnail_url(get_the_ID(), 'blog_featured_regular');
                }

                if ($paged === 1) {
                    $counter++;
                } else {
                    $classValue    = 'o-grid__col o-grid__col--4-4-s o-grid__col--2-4-m o-grid__col--1-3-l c-post-card-wrap js-post-card-wrap';
                    $class2        = 'c-post-card c-post-card--first';
                    $featuredImage = get_the_post_thumbnail_url(get_the_ID(), 'blog_featured_regular');
                }
                ?>

              <div
                class="<?php echo $classValue; ?>">
                <div class='<?php echo $class2; ?>'>

                  <div class='c-post-card__media'>
                    <a
                      href="<?php the_permalink() ?>"
                      class='c-post-card__image js-fadein'

                      style='background-image: url(<?php echo $featuredImage; ?>)'
                      aria-label="<?php the_title() ?>">
                    <span title="Featured Post">
                      <span class='c-post-card--featured__icon' data-icon='ei-star' data-size='s'></span>
                    </span>
                    </a>
                  </div>

                  <div class="c-post-card__content  ">
                    <div class='c-post-card__tags'></div>

                    <h2 class='c-post-card__title'>
                      <a href="<?php the_permalink() ?>" class='c-post-card__title-link'>
                          <?php the_title() ?>
                      </a>
                    </h2>

                    <div class='c-post-card__meta'>
                      <time class='c-post-card__date' datetime="2015-12-22T15:06:49+00:00"
                            title="2015-12-22T15:06:49+00:00"><?php echo get_the_date('F jS, Y'); ?>
                      </time>
                      <div class='c-post-card__author'>
                        <a href="" title="Posts by Anna" rel="author">Anna Kovach</a>
                      </div>
                    </div>
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

        <?php wp_reset_query(); ?>
      <script type='text/javascript'>
      /* <![CDATA[ */
      var yqhoro_config = {
        'yqhoro_page_number_max': <?php echo $query->max_num_pages; ?>,
        'yqhoro_page_number_next': '2',
        'yqhoro_page_link_next': '<?php echo get_bloginfo('url'); ?>\/blog\/page\/9999999999\/',
        'yqhoro_load_more': 'More Posts',
        'yqhoro_loading': 'Loading',
      };
      /* ]]> */
      </script>
    </div>

    <div class='o-grid'>

      <div class='o-grid__col o-grid__col--center o-grid__col--4-4-s o-grid__col--1-3-l'>
        <div class='c-ajax-pagination'>
          <button class='c-btn c-btn--full js-load-posts'>More Posts</button>
        </div>
      </div>
    </div>


  </div>
</div>
<!-- End off-canvas-container -->

<?php
get_footer('blog');
?>
