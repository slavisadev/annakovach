<?php
get_header('blog');
the_post();
?>
<div class="c-search js-search">
  <div class="o-grid">
    <div class="o-grid__col o-grid__col--4-4-s o-grid__col--3-4-m o-grid__col--2-4-l o-grid__col--center">
      <form class="c-search__form" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
        <div class="icon icon--ei-search icon--m c-search__icon"><svg class="icon__cnt" width="0" height="0"><use xlink:href="#ei-search-icon"></use></svg></div>
        <input type="search" name="s" class="c-search__input js-search-input" placeholder="Type and hit enter" aria-label="Search">
      </form>
    </div>
  </div>

  <div class="c-search__close js-search-close">
    <div class="icon icon--ei-close icon--s "><svg class="icon__cnt" width="0" height="0"><use xlink:href="#ei-close-icon"></use></svg></div>
  </div>
</div>

<div class="c-post-hero">
  <div class="o-grid">

    <div class="o-grid__col o-grid__col--4-4-s o-grid__col--4-4-m o-grid__col--2-3-l">
      <div class="c-post-hero__media">
          <?php $featuredImage = get_the_post_thumbnail_url(get_the_ID(), 'blog_featured_long'); ?>
        <img width="760"
             height="737"
             src="<?php echo $featuredImage; ?>"
             class="attachment-large size-large wp-post-image is-inview full-visible"
             alt=""
             srcset=""/>
      </div>
    </div>

    <div class="o-grid__col o-grid__col--4-4-s o-grid__col--1-3-l ">
      <div class="c-post-hero__content  ">
        <h1 class="c-post-hero__title"><?php the_title(); ?></h1>
        <time class="c-post-hero__date" datetime="<?php echo get_the_date('F jS, Y'); ?>"><?php echo get_the_date('F jS, Y'); ?></time>
      </div>
    </div>
  </div>

  <div class="o-wrapper" id="content">

    <div class="o-grid">
      <div class="o-grid__col o-grid__col--center o-grid__col--3-4-m o-grid__col--2-3-l">

        <article id="post-16"
                 class="c-post entry post-16 post type-post status-publish format-standard has-post-thumbnail hentry category-design tag-lifestyle tag-travel">

          <div class="c-content">
              <?php the_content(); ?>
          </div>

          <div class="o-grid">
            <div class="o-grid__col o-grid__col--4-4-s">
              <div class="c-tags">
                  <?php the_tags('') ?>
              </div>
            </div>
          </div>

          <hr>

          <nav class="navigation post-navigation" role="navigation">
            <h2 class="screen-reader-text">Post navigation</h2>
            <div class="nav-links">

                <?php $next_post = get_next_post(); ?>
                <?php $prev_post = get_previous_post(); ?>

              <div class="nav-previous">
                <a
                  href="<?php echo esc_url(get_permalink($next_post->ID)); ?>"
                  rel="prev">
                <span class="meta-nav" aria-hidden="true"><div
                    class="icon icon--ei-chevron-left icon--s pagination__icon"><svg class="icon__cnt"><use
                        xlink:href="#ei-chevron-left-icon"></use></svg></div><span
                    class="pagination__text">Previous</span></span><span
                    class="screen-reader-text">Previous post:</span><span
                    class="post-title"><?php echo $next_post->post_title; ?></span></a>

              </div>
              <div class="nav-next">
                <a
                  href="<?php echo esc_url(get_permalink($prev_post->ID)); ?>"
                  rel="next"><span class="meta-nav" aria-hidden="true"><span
                      class="pagination__text">Next</span><div
                      class="icon icon--ei-chevron-right icon--s pagination__icon"><svg class="icon__cnt"><use
                          xlink:href="#ei-chevron-right-icon"></use></svg></div></span> <span
                    class="screen-reader-text">Next post:</span><span
                    class="post-title"><?php echo $prev_post->post_title; ?></span></a></div>
            </div>
          </nav>

          <hr>

          <div id="comments" class="comments-area">

              <?php if (comments_open() || get_comments_number()) :
                  comments_template();
              else :
                  echo '<p class="comments_closed">Comments on this entry are closed.</p>';
              endif; ?>
              <?php
              /*
               * <div id="respond" class="comment-respond">
              <h3 id="reply-title" class="comment-reply-title">Leave a Reply
                <small><a class="c-btn tiny" rel="nofollow" id="cancel-comment-reply-link"
                          href="/2015/12/21/i-was-not-going-to-stand-by-and-see-another-marine-die-just-to-live-by-those-fucking-rules/#respond"
                          style="display:none;">Cancel reply</a></small>
              </h3>
              <form action="http://nubia-wordpress.aspirethemes.com/wp-comments-post.php" method="post" id="commentform"
                    class="comment-form" novalidate="">
                <p class="comment-notes"><span id="email-notes">Your email address will not be published.</span> Required
                  fields are marked <span class="required">*</span></p>
                <p class="comment-form-comment"><label for="comment">Comment</label> <textarea placeholder="Comment"
                                                                                               id="comment" name="comment"
                                                                                               cols="45" rows="8"
                                                                                               maxlength="65525"
                                                                                               required="required"></textarea>
                </p>
                <p class="comment-form-author"><input id="author" name="author" type="text" placeholder="Name *" value=""
                                                      size="20" aria-required="true"
                                                      style="background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABHklEQVQ4EaVTO26DQBD1ohQWaS2lg9JybZ+AK7hNwx2oIoVf4UPQ0Lj1FdKktevIpel8AKNUkDcWMxpgSaIEaTVv3sx7uztiTdu2s/98DywOw3Dued4Who/M2aIx5lZV1aEsy0+qiwHELyi+Ytl0PQ69SxAxkWIA4RMRTdNsKE59juMcuZd6xIAFeZ6fGCdJ8kY4y7KAuTRNGd7jyEBXsdOPE3a0QGPsniOnnYMO67LgSQN9T41F2QGrQRRFCwyzoIF2qyBuKKbcOgPXdVeY9rMWgNsjf9ccYesJhk3f5dYT1HX9gR0LLQR30TnjkUEcx2uIuS4RnI+aj6sJR0AM8AaumPaM/rRehyWhXqbFAA9kh3/8/NvHxAYGAsZ/il8IalkCLBfNVAAAAABJRU5ErkJggg==&quot;); background-repeat: no-repeat; background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%;">
                </p>
                <p class="comment-form-email"><input id="email" name="email" type="text" placeholder="Email *" value=""
                                                     size="20" aria-required="true"></p>
                <p class="comment-form-url"><input id="url" name="url" type="text" placeholder="Website" value=""
                                                   size="20" aria-required="true"></p>
                <p class="form-submit"><input name="submit" type="submit" id="submit" class="c-btn" value="Post Comment">
                  <input type="hidden" name="comment_post_ID" value="16" id="comment_post_ID">
                  <input type="hidden" name="comment_parent" id="comment_parent" value="0">
                </p>
                <p style="display: none;"><input type="hidden" id="akismet_comment_nonce" name="akismet_comment_nonce"
                                                 value="5e2a3ed8cd"></p>
                <p style="display: none;"></p>      <input type="hidden" id="ak_js" name="ak_js" value="1547578480078">
              </form>
            </div><!-- #respond -->
               */
              ?>

          </div><!-- #comments -->
        </article>
      </div>

        <?php get_sidebar('blog') ?>
    </div>

    <script type='text/javascript'>
    /* <![CDATA[ */
    var yqhoro_config = {
      'yqhoro_page_number_max': 0,
      'yqhoro_page_number_next': 0,
      'yqhoro_page_link_next': '<?php echo get_bloginfo('url'); ?>\/blog\/page\/9999999999\/',
      'yqhoro_load_more': 'More Posts',
      'yqhoro_loading': 'Loading',
    };
    /* ]]> */
    </script>
  </div>

  <div class='o-grid'>
    <p style="text-align: center; width: 100%">
        <a class="tve-froala" href="https://annakovach.com/privacy-policy/" style="outline: none;" target="_blank">&nbsp;Privacy Policy</a>&nbsp;|&nbsp;
        <a class="tve-froala" href="https://annakovach.com/terms-of-use/" style="outline: none;" target="_blank">Terms of Use</a>&nbsp;|&nbsp;
        <a class="tve-froala" href="https://annakovach.com/refund-policy/" style="outline: none;" target="_blank">Refund Policy</a>&nbsp;|&nbsp;
        <a href="https://annakovach.com/gdpr-privacy/">GDPR Policy</a>
      </p>
      </div>
</div>

<?php
get_footer('blog');
?>
