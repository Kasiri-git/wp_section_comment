jQuery(document).ready(function($) {
    var xPos, yPos; // 座標の定義

    $(document).on('click', function(event) {
        // Check if the click target is not inside the comment form
        if (!$(event.target).closest('#wpsc-comment-form').length && !$(event.target).is('#comment')) {
            // Remove any existing comment forms
            $('#wpsc-comment-form-container').remove();

            // Get click coordinates
            xPos = event.pageX;
            yPos = event.pageY;

            // Create comment form and append to body
            var commentForm = '<div id="wpsc-comment-form-container" class="speech-bubble" style="left: ' + xPos + 'px; top: ' + yPos + 'px;">';
            commentForm += '<div id="wpsc-comment-form">';
            commentForm += $('#respond').html(); // Use existing comment form HTML
            commentForm += '<button id="wpsc-close-form">Close</button>'; // Add close button
            commentForm += '</div>';
            commentForm += '</div>';
            $('body').append(commentForm);

            // Show the comment form container
            $('#wpsc-comment-form-container').fadeIn();
        }
    });

    // Handle click on close button
    $(document).on('click', '#wpsc-close-form', function() {
        $('#wpsc-comment-form-container').fadeOut(200, function() {
            $(this).remove();
        });
    });

    // Handle form submission
    $(document).on('submit', '#commentform', function(e) {
        e.preventDefault();

        var formData = $(this).serialize();
        var commentContent = $('#comment').val();
        var commentLink = ' <a href="#" class="wpsc-jump-link">[ジャンプ]</a>'; // Create jump link

        $.ajax({
            url: wpsc_vars.comment_post_url,
            type: 'POST',
            data: formData,
            success: function(response) {
                // Append jump link to comment content and save
                var newComment = '<p>' + commentContent + commentLink + '</p>';
                $.post(wpsc_vars.comment_post_url, {
                    action: 'wpsc_save_comment',
                    comment_content: newComment,
                    comment_x_pos: xPos,
                    comment_y_pos: yPos
                });

                // Remove comment form container
                $('#wpsc-comment-form-container').fadeOut(200, function() {
                    $(this).remove();
                });
            }
        });
    });

    // Handle jump link click to scroll to comment position
    $(document).on('click', '.wpsc-jump-link', function(e) {
        e.preventDefault();
        var commentContainer = $(this).closest('.wpsc-comment-container');
        var commentPosition = commentContainer.offset().top;
        $('html, body').animate({
            scrollTop: commentPosition
        }, 500);
    });
});
