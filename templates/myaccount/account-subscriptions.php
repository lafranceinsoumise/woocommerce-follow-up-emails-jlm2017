<div class="follow-up-subscriptions">
    <h2><?php echo wp_kses_post( get_option( 'fue_email_subscriptions_page_title', 'Email Subscriptions' ) ); ?></h2>

    <a href="<?php echo site_url('/email-preferences'); ?>"><?php _e('Manage email subscriptions', 'follow_up_emails'); ?></a>
</div>