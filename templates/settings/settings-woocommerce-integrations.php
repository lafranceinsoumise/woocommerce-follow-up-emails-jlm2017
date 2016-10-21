<h3><?php _e('WooCommerce Settings', 'follow_up_emails'); ?></h3>

<h4><?php _e('Remove WooCommerce Email Styles', 'follow_up_emails'); ?></h4>

<p><?php _e('You can easily remove WooCommerce email styles to quickly be able to add full HTML to your emails directly in the email editor. Simply check this box, and the default WooCommerce styling will be removed from the emails you send via Follow-up Emails. Conversely, you can create your own templates and choose them instead of the default WooCommerce template.', 'follow_up_emails'); ?></p>

<table class="form-table">
    <tr>
        <th>
            <label for="disable_email_wrapping">
                <input type="checkbox" name="disable_email_wrapping" id="disable_email_wrapping" value="1" <?php checked(1, get_option('fue_disable_wrapping')); ?> />
                <?php _e('Click here to disable the wrapping of styles in the WooCommerce email templates.', 'follow_up_emails'); ?>
            </label>
        </th>
    </tr>
</table>

<hr>

<h3><?php _e('Abandoned Cart Settings', 'follow_up_emails'); ?></h3>

<h4><?php _e('Cart Conversion Time', 'follow_up_emails'); ?></h4>

<p><?php printf(__('Record cart conversions up to %s days after an email has been sent.', 'follow_up_emails'), '<input type="text" size="3" name="wc_conversion_days" id="wc_conversion_days" placeholder="14" value="'. get_option('fue_wc_conversion_days', 14) .'" />' ); ?></p></table>

<h4><?php _e('Set Cart as Abandoned After', 'follow_up_emails'); ?></h4>

<p><?php
    $value = get_option('fue_wc_abandoned_cart_value', 3);
    $unit  = get_option('fue_wc_abandoned_cart_unit', 'hours');
    printf(
        __('Carts older than %s %s are to be considered as abandoned.', 'follow_up_emails'),
        '<input type="text" size="3" name="wc_abandoned_cart_value" id="wc_abandoned_cart_value" placeholder="1" value="'. $value .'" />',
        '<select name="wc_abandoned_cart_unit" id="wc_abandoned_cart_unit" style="vertical-align: top;">
            <option value="minutes" '. selected('minutes', $unit, false) .'>'. __('minutes', 'follow_up_emails') .'</option>
            <option value="hours" '. selected('hours', $unit, false) .'>'. __('hours', 'follow_up_emails') .'</option>
            <option value="days" '. selected('days', $unit, false) .'>'. __('days', 'follow_up_emails') .'</option>
        </select>'
    );
    ?>
</p>

<hr>