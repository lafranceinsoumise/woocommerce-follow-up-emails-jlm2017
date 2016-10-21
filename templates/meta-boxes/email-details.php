<style type="text/css">
    #submitdiv { display:none }
</style>
<?php if ( !$email->type ): ?>
<div id="fue-email-details-notice">
    <p class="meta-box-notice"><?php _e('Please set the email type first', 'follow_up_emails'); ?></p>
</div>
<?php else: ?>
<div id="fue-email-details-content" class="panel-wrap email_details" style="display: none;">
    <div class="fue-tabs-back"></div>
    <ul class="email_details_tabs fue-tabs">
        <?php
        $email_details_tabs = apply_filters( 'fue_email_details_tabs', array(
            'triggers' => array(
                'label'  => __( 'Triggers', 'follow_up_emails' ),
                'icon'   => 'dashicons-admin-settings',
                'target' => 'triggers_details',
                'class'  => array(),
            ),
            'settings' => array(
                'label'  => __( 'Settings', 'follow_up_emails' ),
                'icon'   => 'dashicons-admin-tools',
                'target' => 'settings_details',
                'class'  => array(),
            ),
            'email_settings' => array(
                'label'  => __( 'From/Reply-to', 'follow_up_emails' ),
                'icon'   => 'dashicons-email',
                'target' => 'email_settings',
                'class'  => array(),
            ),
            'tracking' => array(
                'label'  => __( 'Google Analytics', 'follow_up_emails' ),
                'icon'   => 'dashicons-chart-area',
                'target' => 'tracking_details',
                'class'  => array(),
            )
        ), $email );

        // remove the triggers tab if the email is Manual
        if ( $email->type == 'manual' ) {
            unset($email_details_tabs['triggers']);
        }

        foreach ( $email_details_tabs as $key => $tab ) {
            $icon = (isset($tab['icon'])) ? $tab['icon'] : 'dashicons-admin-generic';
            ?><li class="<?php echo $key; ?>_options <?php echo $key; ?>_tab <?php echo implode( ' ' , $tab['class'] ); ?>">
            <a href="#<?php echo $tab['target']; ?>" class="dashicons-before <?php echo $icon; ?>"> <?php echo esc_html( $tab['label'] ); ?></a>
            </li><?php
        }

        do_action( 'fue_write_panel_tabs' );
        ?>
    </ul>

    <div id="triggers_details" class="panel fue_panel">
        <?php include FUE_TEMPLATES_DIR .'/meta-boxes/email-triggers.php'; ?>
    </div>

    <div id="settings_details" class="panel fue_panel">
        <div class="options_group">
        <?php if ( $email->type == 'storewide' ): ?>
            <p class="form-field">
                <label for="always_send">
                    <?php _e('Always Send', 'follow_up_emails'); ?>
                </label>
                <input type="hidden" name="always_send" id="always_send_off" value="0" />
                <input type="checkbox" class="checkbox" name="always_send" id="always_send" value="1" <?php if ($email->always_send == 1) echo 'checked'; ?> />
                <img width="16" height="16" src="<?php echo FUE_TEMPLATES_URL; ?>/images/help.png" class="help_tip" title="<?php _e('Do you want this email to ALWAYS send? Use this setting carefully, as this setting could result in multiple emails being sent per order', 'follow_up_emails'); ?>">
            </p>
        <?php else: ?>
            <input type="hidden" name="always_send" id="always_send_off" value="1" />
        <?php endif; ?>

        <?php if ( !in_array( $email->type, array('signup', 'manual') ) ): ?>
            <p class="form-field">
                <label for="meta_one_time">
                    <?php _e('Send once per customer', 'follow_up_emails'); ?>
                </label>
                <input type="hidden" name="meta[one_time]" id="meta_one_time_off" value="no" />
                <input type="checkbox" class="checkbox" name="meta[one_time]" id="meta_one_time" value="yes" <?php if (isset($email->meta['one_time']) && $email->meta['one_time'] == 'yes') echo 'checked'; ?> />
                <img width="16" height="16" src="<?php echo FUE_TEMPLATES_URL; ?>/images/help.png" class="help_tip" title="<?php _e('A customer will only receive this email once, even if purchased multiple times at different dates', 'follow_up_emails'); ?>">
            </p>

            <p class="form-field">
                <label for="adjust_date">
                    <?php _e('Delay existing email', 'follow_up_emails'); ?>
                </label>
                <input type="hidden" name="meta[adjust_date]" id="adjust_date_off" value="no" />
                <input type="checkbox" class="checkbox" name="meta[adjust_date]" id="adjust_date" value="yes" <?php if (isset($email->meta['adjust_date']) && $email->meta['adjust_date'] == 'yes') echo 'checked'; ?> />
                <img width="16" height="16" src="<?php echo FUE_TEMPLATES_URL; ?>/images/help.png" class="help_tip" title="<?php _e('If you check increase delay - instead of scheduling a new email - if the customer already has this email scheduled it will delay that scheduled email to the new future date.', 'follow_up_emails'); ?>">
            </p>
        <?php endif; ?>

        </div> <!-- /options_group -->

        <?php do_action('fue_email_form_settings', $email); ?>


    </div>

    <div id="email_settings" class="panel fue_panel">
        <p class="form-field">
            <label for="email_bcc">
                <?php _e('Send a copy of this email', 'follow_up_emails'); ?>
            </label>
            <input type="text" name="meta[bcc]" id="email_bcc" value="<?php echo (isset($email->meta['bcc'])) ? esc_attr($email->meta['bcc']) : ''; ?>" class="regular-text" />
            <img width="16" height="16" src="<?php echo FUE_TEMPLATES_URL; ?>/images/help.png" class="help_tip" title="<?php _e('All these emails will be blind carbon copied to this address', 'follow_up_emails'); ?>">
        </p>

        <p class="form-field">
            <label for="email_from_name">
                <?php _e('From/Reply-To Name', 'follow_up_emails'); ?>
            </label>
            <input type="text" name="meta[from_name]" id="email_from_name" value="<?php echo (isset($email->meta['from_name'])) ? esc_attr($email->meta['from_name']) : ''; ?>" class="regular-text" />
            <img width="16" height="16" src="<?php echo FUE_TEMPLATES_URL; ?>/images/help.png" class="help_tip" title="<?php _e('The name that your emails will come from and replied to', 'follow_up_emails'); ?>">
        </p>

        <p class="form-field">
            <label for="email_from">
                <?php _e('From/Reply-To Address', 'follow_up_emails'); ?>
            </label>
            <input type="text" name="meta[from_address]" id="email_from" value="<?php echo (isset($email->meta['from_address'])) ? esc_attr($email->meta['from_address']) : ''; ?>" class="regular-text" />
            <img width="16" height="16" src="<?php echo FUE_TEMPLATES_URL; ?>/images/help.png" class="help_tip" title="<?php _e('The email address that your emails will come from and replied to', 'follow_up_emails'); ?>">
        </p>
    </div>

    <div id="tracking_details" class="panel fue_panel">
        <p class="form-field">
            <label for="tracking_on" class="long">
                <?php _e('Add Google Analytics tracking to links', 'follow_up_emails'); ?>
            </label>
            <input type="checkbox" class="checkbox" name="tracking_on" id="tracking_on" value="1" <?php checked( 1, $email->tracking_on ); ?> />
        </p>

        <p class="form-field tracking_on" style="display: none;">
            <label for="tracking"><?php _e('Link Tracking', 'follow_up_emails'); ?></label>
            <input type="text" name="tracking" id="tracking" class="test-email-field" value="<?php echo esc_attr($email->tracking); ?>" placeholder="e.g. utm_campaign=Follow-up-Emails-by-75nineteen" style="width: 75%;" />
            <img width="16" height="16" src="<?php echo FUE_TEMPLATES_URL; ?>/images/help.png" class="help_tip" title="<?php _e('The value inserted here will be appended to all URLs in the Email Body', 'follow_up_emails'); ?>">
            <span class="description" style="margin-left:80px;"><a href="https://support.google.com/analytics/answer/1033867?hl=en">Guide To Get The Tracking Link</a></span><?php /*@todo*/ ?>
        </p>
    </div>

    <?php do_action('fue_email_form_email_details', $email); ?>

    <div class="clear"></div>
</div>
<?php endif; ?>