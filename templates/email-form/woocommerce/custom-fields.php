<div class="options_group storewide non-signup reminder hideable <?php do_action('fue_form_custom_field_tr_class', $email); ?> use_custom_field_tr">

    <p class="form-field">
        <label for="use_custom_field"><?php _e('Use Custom Field', 'follow_up_emails'); ?></label>
        <input type="hidden" id="product_id" value="<?php echo $email->product_id; ?>" />
        <input type="checkbox" name="meta[use_custom_field]" value="1" id="use_custom_field" <?php checked(1, $use_custom_field); ?> />
    </p>

    <p class="form-field show-if-custom-field custom_field_tr" <?php if (! $use_custom_field ) echo 'style="display: none;"'; ?>>
        <label for="cf_product"><?php _e('Select the product and custom field to use', 'follow_up_emails'); ?></label>
        <span class="if-product-selected custom_field_select_div">
            <br/>
            <select name="custom_fields" id="custom_fields">
                <?php
                $meta   = get_post_custom($email->product_id);

                if ( !$meta ) {
                    $meta = array();
                }

                foreach ( $meta as $key => $value ): ?>
                    <option value="<?php echo $key; ?>"><?php echo $key; ?></option>
                <?php endforeach; ?>
            </select>
            <span class="show-if-cf-selected"><input type="text" readonly onclick="jQuery(this).select();" value="" size="25" id="custom_field" /></span>
        </span>
    </p>
</div>