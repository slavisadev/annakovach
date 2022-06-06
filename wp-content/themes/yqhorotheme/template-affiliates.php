<?php
/**
 * Template Name: affiliates
 */


$args  = [
    'showposts' => -1,
    'post_type' => 'post',
    'meta_key'  => '_thesis_redirect',
];
$query = new WP_Query($args);

if ($query->have_posts()) :
    while ($query->have_posts()) :
        $query->the_post(); ?>

      <fieldset>
        <a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
        <strong><?php $link = get_post_meta(get_the_ID(), '_thesis_redirect', true);
            echo $link['url']; ?></strong>
      </fieldset>
    <?php
    endwhile;
endif;
