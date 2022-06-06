<?php

class AboutWidget extends WP_Widget
{
    function AboutWidget()
    {
        parent::WP_Widget(false, $name = 'YQHORO About Author Widget');
    }

    function widget($args, $instance)
    {
        extract($args);
        ?>
      <div class="c-widget c-widget--sidebar c-widget-author">
        <div class="c-widget-author__media">
          <a href="/about/">
            <img alt="Anna Kovach" src="/wp-content/uploads/2021/04/small-about.jpg"
                 class="avatar avatar-96 photo" height="88" width="88">
          </a>
        </div>

        <div class="c-widget-author__content">
          <h3 class="c-widget-author__title">
            <a href="/about/">Anna Kovach</a>
          </h3>
          <p class="c-widget-author__bio">
            My name is Anna Kovach, and I’m a Relationship Astrologer.
            Welcome to my blog about the Taurus man.
            If you’d like, you can learn <a href="/about/">more about me on this page here</a>.
          </p>
        </div>
      </div>
        <?php
    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        return $instance;
    }

    function form($instance)
    {

    }

} // end class example_widget
add_action('widgets_init', create_function('', 'return register_widget("AboutWidget");'));

class RecentPosts extends WP_Widget
{
    function RecentPosts()
    {
        parent::WP_Widget(false, $name = 'YQHORO Recent Posts Widget');
    }

    function widget($args, $instance)
    {
        extract($args);
        ?>
      <section id="nubia-recent-posts-3" class="c-widget c-widget--sidebar nubia-recent-posts">
        <h5 class="c-widget--sidebar__title">Recent Posts</h5>
          <?php
          $args = [
              'showposts' => 10,
              'post_type' => 'post',
          ];

          $query = new WP_Query($args);

          if ($query->have_posts()) :
              while ($query->have_posts()) :
                  $query->the_post();
                  $featuredImage = get_the_post_thumbnail_url(get_the_ID(), 'blog_featured_regular');
                  ?>
                <a
                  href="<?php the_permalink() ?>"
                  class="c-teaser">
                  <div class="c-teaser__content">
                    <h3 class="c-teaser__title"><?php the_title() ?></h3>
                    <time class="c-teaser__date">
                        <?php echo get_the_date('F jS, Y'); ?>
                    </time>
                  </div>
                  <div class="c-teaser__media">
                    <div class="c-teaser__image js-fadein is-inview full-visible"
                         style="background-image: url(<?php echo $featuredImage; ?>)"
                         aria-label="<?php echo get_the_date('F jS, Y'); ?>"></div>
                  </div>
                </a>
              <?php
              endwhile;
          endif;
          wp_reset_query();
          ?>
      </section>
        <?php
    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        return $instance;
    }

    function form($instance)
    {

    }

} // end class example_widget
add_action('widgets_init', create_function('', 'return register_widget("RecentPosts");'));
