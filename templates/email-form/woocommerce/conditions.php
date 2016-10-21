<?php
$value                      = (!empty( $conditions[ $idx ]['value'] ) ) ? $conditions[ $idx ]['value'] : '';
$products                   = (!empty( $conditions[ $idx ]['products'] ) ) ? $conditions[ $idx ]['products'] : '';
$condition_categories       = (!empty( $conditions[ $idx ]['categories'] ) ) ? $conditions[ $idx ]['categories'] : array();
$condition_payment_gateway  = !empty( $conditions[ $idx ]['payment_method'] ) ? $conditions[ $idx ]['payment_method'] : '';
$condition_shipping_method  = !empty( $conditions[ $idx ]['shipping_method'] ) ? $conditions[ $idx ]['shipping_method'] : '';
?>
<span class="value" style="display: none;">
    <span class="value-currency" style="display: none;"><?php echo get_woocommerce_currency_symbol(); ?></span>
    <input type="text" name="conditions[<?php echo $idx; ?>][value]" class="condition-value" disabled value="<?php echo esc_attr($value); ?>" >
</span>
<div class="value-products" style="display: none; margin: 5px 0 0 45px;">
    <?php
    $product_ids    = array_filter( array_map( 'absint', explode( ',', $products ) ) );
    $json_ids       = array();

    foreach ( $product_ids as $product_id ) {
        $product = WC_FUE_Compatibility::wc_get_product( $product_id );
        $json_ids[ $product_id ] = wp_kses_post( $product->get_formatted_name() );
    }
    ?>
    <input
        type="hidden"
        class="ajax-select2-init"
        name="conditions[<?php echo $idx; ?>][products]"
        id="conditions_<?php echo $idx; ?>_products"
        data-multiple="true"
        data-placeholder="<?php _e('Search for a product...', 'follow_up_emails'); ?>"
        value="<?php echo implode( ',', array_keys( $json_ids ) ); ?>"
        data-selected="<?php echo esc_attr( json_encode( $json_ids ) ); ?>"
    >
</div>
<div class="value-categories" style="display: none; margin: 5px 0 0 45px;">
    <select id="conditions_<?php echo $idx; ?>_categories" name="conditions[<?php echo $idx; ?>][categories][]" class="select2-init" multiple="multiple" data-placeholder="No categories" style="width:500px;">
        <?php
        foreach ($categories as $category):
            $selected = (!in_array($category->term_id, $condition_categories)) ? '' : 'selected';
            ?>
            <option value="<?php _e($category->term_id); ?>" <?php echo $selected; ?>><?php echo esc_html($category->name); ?></option>
        <?php endforeach; ?>
    </select>
</div>
<div class="value-payment-method" style="display: none; margin: 5px 0 0 45px;">
    <?php $payment_gateways = WC_Payment_Gateways::instance()->payment_gateways(); ?>
    <select id="conditions_<?php echo $idx; ?>_payment_method" name="conditions[<?php echo $idx; ?>][payment_method]" class="select2-init">
        <?php foreach ( $payment_gateways as $gateway ): ?>
            <option value="<?php echo $gateway->id; ?>" <?php selected( $condition_payment_gateway, $gateway->id ); ?>><?php echo esc_attr( $gateway->title ); ?></option>
        <?php endforeach; ?>
        <option value="other"><?php _e('Other', 'woocommerce'); ?></option>
    </select>
</div>
<div class="value-shipping-method" style="display: none; margin: 5px 0 0 45px;">
    <?php $shipping_methods = WC_Shipping::instance()->get_shipping_methods(); ?>
    <select id="conditions_<?php echo $idx; ?>_shipping_method" name="conditions[<?php echo $idx; ?>][shipping_method]" class="select2-init">
        <?php foreach ( $shipping_methods as $method ): ?>
            <option value="<?php echo $method->id; ?>" <?php selected( $condition_shipping_method, $method->id ); ?>><?php echo esc_attr( $method->title ); ?></option>
        <?php endforeach; ?>
    </select>
</div>