<h3><?php _e('Twitter Application Access Keys', 'follow_up_emails'); ?></h3>

<?php if (isset($_GET['message'])): ?>
    <div id="message" class="updated"><p><?php echo wp_kses_post( urldecode( $_GET['message'] ) ); ?></p></div>
<?php endif; ?>

<a href="#" class="toggle-guide"><?php _e('Guide to getting your API keys', 'follow_up_emails'); ?></a>

<div id="twitter-guide" style="display: none;">
    <blockquote>
        <p>
            <?php _e('To get your API Keys, create a <a href="https://apps.twitter.com/app/new">new Twitter App</a> and set the following values:', 'follow_up_emails'); ?>
        </p>
        <ul>
            <li><strong>Name:</strong> <?php _e('Your app\'s name', 'follow_up_emails'); ?></li>
            <li><strong>Description:</strong> <?php _e('Your application description, which will be shown in user-facing authorization screens', 'follow_up_emails'); ?></li>
            <li><strong>Website:</strong> <?php _e('Your application\'s publicly accessible home page, where users can go to download, make use of, or find out more information about your application', 'follow_up_emails'); ?></li>
            <li><strong>Callback URL:</strong> <?php printf( __('Set to <code>%s</code>', 'follow_up_emails'), admin_url('admin-post.php?action=twitter-oauth') ); ?></li>
            <li><strong>Permissions:</strong> <?php _e('Set to <code>Read and Write</code>', 'follow_up_emails'); ?></li>
        </ul>

        <p><?php _e('After creating your app, click on the Keys and Access Tokens tab to get your Consumer Key and Consumer Secret.', 'follow_up_emails'); ?></p>
    </blockquote>
</div>


<table class="form-table">
    <tbody>
    <tr valign="top">
        <th><label for="twitter_checkout_fields"><?php _e('Checkout Page', 'follow_up_emails'); ?></label></th>
        <td>
            <input type="checkbox" id="twitter_checkout_fields" name="twitter_checkout_fields" value="1" <?php checked( 1, $this->fue_twitter->settings['checkout_fields'] ); ?> />
            <span class="description"><?php _e('Collect twitter handle on the Checkout page', 'follow_up_emails'); ?></span>
        </td>
    </tr>
    <tr valign="top">
        <th><label for="twitter_account_fields"><?php _e('Account Page', 'follow_up_emails'); ?></label></th>
        <td>
            <input type="checkbox" id="twitter_account_fields" name="twitter_account_fields" value="1" <?php checked( 1, $this->fue_twitter->settings['account_fields'] ); ?> />
            <span class="description"><?php _e('Collect twitter handle on the My Account page', 'follow_up_emails'); ?></span>
        </td>
    </tr>
    <tr valign="top">
        <th><label for="twitter_consumer_key"><?php _e('Consumer Key', 'follow_up_emails'); ?></label></th>
        <td>
            <input type="text" name="twitter_consumer_key" id="twitter_consumer_key" value="<?php echo esc_attr( $this->fue_twitter->settings['consumer_key'] ); ?>" size="50" />
        </td>
    </tr>
    <tr valign="top">
        <th><label for="twitter_consumer_secret"><?php _e('Consumer Secret', 'follow_up_emails'); ?></label></th>
        <td>
            <input type="text" name="twitter_consumer_secret" id="twitter_consumer_secret" value="<?php echo esc_attr( $this->fue_twitter->settings['consumer_secret'] ); ?>" size="50" />
        </td>
    </tr>
    <?php
    if ( empty( $this->fue_twitter->settings['access_token'] ) && ( !empty( $this->fue_twitter->settings['consumer_key'] ) && !empty( $this->fue_twitter->settings['consumer_secret'] ) ) ):
        try {
            $connection     = new \Abraham\TwitterOAuth\TwitterOAuth( $this->fue_twitter->settings['consumer_key'], $this->fue_twitter->settings['consumer_secret'] );
            $request_token  = $connection->oauth('oauth/request_token');
            $auth_url       = $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));

            // store the token for 10 minutes
            set_transient( 'fue_twitter_request_token', $request_token, 600 );
    ?>
    <tr valign="top">
        <th><label for="twitter_signin"><?php _e('Grant API Access', 'follow_up_emails'); ?></label></th>
        <td>
            <a href="<?php echo $auth_url; ?>"><img src="<?php echo FUE_TEMPLATES_URL .'/images/sign-in-with-twitter.png'; ?>" alt="<?php _e('Sign In with Twitter', 'follow_up_emails'); ?>" /></a>
        </td>
    </tr>
    <?php
        } catch ( Exception $e ) {
            $exception = json_decode( $e->getMessage() );
            $error = array_pop( $exception->errors );
            echo '<div class="error"><p>Twitter Error: '. $error->message .'</p></div>';
        }
    else:
    ?>
        <tr valign="top">
            <th>&nbsp;</th>
            <td>
                <a href="admin-post.php?action=fue_reset_twitter" class="button"><?php _e('Reset Twitter Data', 'follow_up_email'); ?></a>
            </td>
        </tr>
    <?php
    endif;
        ?>
    </tbody>
</table>

<script>
    jQuery(".toggle-guide").click(function(e) {
        e.preventDefault();

        jQuery("#twitter-guide").slideToggle();
    });
</script>