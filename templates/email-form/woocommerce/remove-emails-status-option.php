<div class="options_group">
    <p class="form-field">
        <label for="remove_email_status_change" class="inline">
            <?php _e('Remove on status change', 'follow_up_emails'); ?>
        </label>
        <input type="hidden" name="meta[remove_email_status_change]" value="no" />
        <input type="checkbox" name="meta[remove_email_status_change]" id="remove_email_status_change" value="yes" <?php if (isset($email->meta['remove_email_status_change']) && $email->meta['remove_email_status_change'] == 'yes') echo 'checked'; ?> />
        <img width="16" height="16" src="<?php echo FUE_TEMPLATES_URL; ?>/images/help.png" class="help_tip" title="<?php _e('Remove unsent emails when an order status changes', 'follow_up_emails'); ?>">
    </p>
</div>
