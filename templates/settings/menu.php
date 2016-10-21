<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
    <a href="admin.php?page=followup-emails-settings&amp;tab=documentation" class="nav-tab <?php if ($tab == 'documentation') echo 'nav-tab-active'; ?>"><?php _e(' Get Started', 'follow_up_emails'); ?></a>
    <a href="admin.php?page=followup-emails-settings&amp;tab=system" class="nav-tab <?php if ($tab == 'system') echo 'nav-tab-active'; ?>"><?php _e(' General Settings', 'follow_up_emails'); ?></a>
    <a href="admin.php?page=followup-emails-settings&amp;tab=auth" class="nav-tab <?php if ($tab == 'auth') echo 'nav-tab-active'; ?>"><?php _e(' DKIM & SPF', 'follow_up_emails'); ?></a>
    <a href="admin.php?page=followup-emails-settings&amp;tab=subscribers" class="nav-tab <?php if ($tab == 'subscribers') echo 'nav-tab-active'; ?>"><?php _e(' Subscribers', 'follow_up_emails'); ?></a>
    <a href="admin.php?page=followup-emails-settings&amp;tab=tools" class="nav-tab <?php if ($tab == 'tools') echo 'nav-tab-active'; ?>"><?php _e(' Tools', 'follow_up_emails'); ?></a>
    <a href="admin.php?page=followup-emails-settings&amp;tab=integration" class="nav-tab <?php if ($tab == 'integration') echo 'nav-tab-active'; ?>"><?php _e(' More Functionality', 'follow_up_emails'); ?></a>
    <?php do_action( 'fue_settings_tabs' ); ?>
</h2>