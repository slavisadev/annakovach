<?php
if (post_password_required()) {
    return;
}
?>

<div id="respond" class="comment-respond">

    <?php
    // You can start editing here -- including this comment!
    if (have_comments()) :
        ?>
        <h2 class="comments-title">
            <?php
            $taurusmansecrets_comment_count = get_comments_number();
            if ('1' === $taurusmansecrets_comment_count) {
                printf(
                /* translators: 1: title. */
                    esc_html__('One thought on &ldquo;%1$s&rdquo;', 'taurusmansecrets'),
                    '<span>' . get_the_title() . '</span>'
                );
            } else {
                printf(
                    esc_html(_nx('%1$s thought on &ldquo;%2$s&rdquo;', '%1$s thoughts on &ldquo;%2$s&rdquo;', $taurusmansecrets_comment_count, 'comments title', 'taurusmansecrets')),
                    number_format_i18n($taurusmansecrets_comment_count),
                    '<span>' . get_the_title() . '</span>'
                );
            }
            ?>
        </h2><!-- .comments-title -->

        <?php the_comments_navigation(); ?>

        <ol class="comment-list">
            <?php
            wp_list_comments(array(
                'style' => 'ol',
                'short_ping' => true,
            ));
            ?>
        </ol><!-- .comment-list -->

        <?php
        the_comments_navigation();

        // If comments are closed and there are comments, let's leave a little note, shall we?
        if (!comments_open()) :
            ?>
            <p class="no-comments"><?php esc_html_e('Comments are closed.', 'taurusmansecrets'); ?></p>
            <?php
        endif;

    endif; // Check for have_comments().
    $comments_args = [
        'class_submit' => 'input_submit',
        'title_reply' => 'Leave a Comment',
        'title_reply_before' => '<p class="comment_form_title">',
        'title_reply_after' => '</p>',
        'label_submit' => __('Submit'),
    ];
    comment_form($comments_args);
    ?>
</div><!-- #comments -->
