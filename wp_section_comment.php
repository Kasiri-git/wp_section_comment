<?php
/*
Plugin Name: WP Section Comment
Description: クリックした位置に吹き出し形式でコメントフォームを表示し、コメントを投稿した場所にジャンプするリンクを自動挿入します。
Version: 1.0.38
Author: Your Name
Author URI: https://yourwebsite.com
Plugin URI: https://github.com/your-plugin-repository
*/

// Enqueue scripts and styles
function wpsc_enqueue_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('wpsc-script', plugins_url('js/wpsc-script.js', __FILE__), array('jquery'), null, true);

    $post_id = get_the_ID();
    $comment_post_url = site_url('/wp-comments-post.php');
    wp_localize_script('wpsc-script', 'wpsc_vars', array(
        'comment_post_id' => $post_id,
        'comment_post_url' => $comment_post_url
    ));

    wp_enqueue_style('wpsc-style', plugins_url('css/wpsc-style.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'wpsc_enqueue_scripts');

// Add comment form container to footer
function wpsc_add_comment_form() {
    if (comments_open()) {
        $commenter = wp_get_current_commenter();
        $user = wp_get_current_user();

        comment_form(array(
            'title_reply' => '',
            'comment_notes_before' => '',
            'comment_notes_after' => '',
            'class_submit' => 'submit',
            'submit_button' => '<input name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s" />',
            'must_log_in' => '<p class="must-log-in">' . sprintf(
                __('ログインしてコメントするには<a href="%s">ここ</a>をクリックしてください。'),
                wp_login_url(apply_filters('the_permalink', get_permalink()))
            ) . '</p>',
            'logged_in_as' => '<p class="logged-in-as">' . sprintf(
                __('現在<a href="%1$s">%2$s</a>としてログインしています。<a href="%3$s">ここ</a>をクリックしてログアウトできます。'),
                admin_url('profile.php'),
                $user->display_name,
                wp_logout_url(apply_filters('the_permalink', get_permalink()))
            ) . '</p>',
            'fields' => array(
                'author' => '<p class="comment-form-author">' .
                    '<label for="author">' . __('名前') . ' <span class="required">*</span></label> ' .
                    '<input id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" size="30" required="required" /></p>',
                'email' => '<p class="comment-form-email">' .
                    '<label for="email">' . __('メール') . ' <span class="required">*</span></label> ' .
                    '<input id="email" name="email" type="email" value="' . esc_attr($commenter['comment_author_email']) . '" size="30" required="required" /></p>',
                'url' => '<p class="comment-form-url">' .
                    '<label for="url">' . __('サイト') . '</label> ' .
                    '<input id="url" name="url" type="url" value="' . esc_attr($commenter['comment_author_url']) . '" size="30" /></p>'
            ),
            'cancel_reply_link' => '<button type="button" id="wpsc-cancel-comment-reply-link">キャンセル</button>',
        ));
    }
}
add_action('wp_footer', 'wpsc_add_comment_form');

// Modify comment text to add jump link
function wpsc_modify_comment_text($text, $comment) {
    // Get comment metadata
    $comment_data = get_comment_meta($comment->comment_ID, 'wpsc_comment_data', true);

    if ($comment_data && isset($comment_data['comment_content'])) {
        // Add jump link to comment text
        $jump_link = '<a href="#" class="wpsc-jump-link" data-comment-id="' . $comment->comment_ID . '">[ジャンプ]</a>';
        $text .= $jump_link;
    }

    return $text;
}
add_filter('comment_text', 'wpsc_modify_comment_text', 10, 2);
