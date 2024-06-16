jQuery(document).ready(function($) {
    // Handle click event to show comment form
    $(document).on('click', function(event) {
        // Check if the click target is not inside the comment form
        if (!$(event.target).closest('#wpsc-comment-form').length && !$(event.target).is('#comment')) {
            // Remove any existing comment forms
            $('#wpsc-comment-form-container').remove();

            // Get click coordinates
            var xPos = event.pageX;
            var yPos = event.pageY;

            // Create comment form and append to body
            var commentForm = '<div id="wpsc-comment-form-container" class="speech-bubble" style="left: ' + xPos + 'px; top: ' + yPos + 'px;">';
            commentForm += '<div id="wpsc-comment-form">';
            commentForm += '<form id="commentform" action="' + wpsc_vars.comment_post_url + '" method="post">';
            commentForm += '<textarea id="comment" name="comment" rows="4"></textarea><br/>';
            commentForm += '<input type="submit" name="submit" value="Post Comment"/>';
            commentForm += '<input type="hidden" name="comment_post_ID" value="' + wpsc_vars.comment_post_id + '"/>';
            commentForm += '<?php comment_id_fields(); ?>';
            commentForm += '</form>';
            commentForm += '<button id="wpsc-close-form">Close</button>';
            commentForm += '</div>';
            commentForm += '</div>';
            $('body').append(commentForm);

            // Show the comment form container
            $('#wpsc-comment-form-container').fadeIn();
        }
    });

    // Handle form submission
    $(document).on('submit', '#commentform', function(event) {
        event.preventDefault(); // Prevent default form submission

        var formData = $(this).serialize(); // Serialize form data
        var formAction = $(this).attr('action'); // Form action URL

        // Submit the form via Ajax
        $.ajax({
            type: 'POST',
            url: formAction,
            data: formData,
            success: function(response) {
                // Optionally, handle success response (e.g., display success message)
                alert('Comment submitted successfully!');
                $('#wpsc-comment-form-container').fadeOut(200, function() {
                    $(this).remove();
                });
            },
            error: function(xhr, status, error) {
                // Optionally, handle error response (e.g., display error message)
                alert('Comment submission failed.');
            }
        });
    });

    // Close comment form when close button is clicked
    $(document).on('click', '#wpsc-close-form', function(event) {
        $('#wpsc-comment-form-container').fadeOut(200, function() {
            $(this).remove();
        });
    });

    // Prevent form from closing if clicked inside form
    $(document).on('click', '#wpsc-comment-form', function(event) {
        event.stopPropagation(); // Prevent click event from bubbling up
    });
});
