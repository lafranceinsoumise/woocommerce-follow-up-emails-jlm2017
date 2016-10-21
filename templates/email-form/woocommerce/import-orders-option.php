<div class="options_group">
    <p class="form-field">
        <label for="import_orders" class="inline">
            <?php _e('Import Existing Orders', 'follow_up_emails'); ?>
        </label>
        <input type="hidden" name="meta[import_orders]" value="no" />
        <input type="checkbox" name="meta[import_orders]" id="import_orders" value="yes" <?php if (isset($email->meta['import_orders']) && $email->meta['import_orders'] == 'yes') echo 'checked'; ?> />
        <img width="16" height="16" src="<?php echo FUE_TEMPLATES_URL; ?>/images/help.png" class="help_tip" title="<?php _e('Import existing orders that match this email criteria', 'follow_up_emails'); ?>">
    </p>
</div>
