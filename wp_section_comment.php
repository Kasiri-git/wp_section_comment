<?php
/*
Plugin Name: WP Section Comment
Description: クリックした位置に吹き出し形式でコメントフォームを表示します。
Version: 1.0.0
Author: Kasiri
Author URI: https://kasiri.icu
Plugin URI: https://github.com/Kasiri-git/wp_section_comment
*/

// Enqueue scripts and styles
function wpsc_enqueue_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('wpsc-script', plugins_url('js/wpsc-script.js', __FILE__), array('jquery'), null, true);

    // Pass necessary data to JavaScript
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
    ?>
    <div id="wpsc-comment-form-container" style="display: none;">
        <div id="wpsc-comment-form" class="speech-bubble">
            <form id="commentform" action="<?php echo site_url('/wp-comments-post.php'); ?>" method="post">
                <textarea id="comment" name="comment" rows="4"></textarea><br/>
                <input type="submit" name="submit" value="Post Comment"/>
                <?php comment_id_fields(); ?>
            </form>
            <button id="wpsc-close-form">Close</button>
        </div>
    </div>
    <?php
}
add_action('wp_footer', 'wpsc_add_comment_form');
