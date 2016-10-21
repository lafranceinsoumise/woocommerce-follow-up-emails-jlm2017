<h2><?php _e('Twitter', 'follow_up_emails'); ?></h2>

<p>
    <strong><?php _e('Twitter Handle:', 'follow_up_emails'); ?></strong>
    <?php
    $handle = get_user_meta( get_current_user_id(), 'twitter_handle', true );

    if ( !$handle ) {
        _e('<em>not set</em>', 'follow_up_emails');
    } else {
        echo '@'. sanitize_user( $handle );
    }
    ?>
    <a href="edit-account" style="margin-left: 50px;"><?php _e('Change', 'follow_up_emails'); ?></a>
</p>