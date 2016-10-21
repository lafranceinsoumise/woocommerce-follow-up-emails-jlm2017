<?php if ( $unsubscribed ): ?>
    <div class="woocommerce-message"><?php _e('Successfully unsubscribed from the selected email', 'follow_up_emails'); ?></div>
<?php
endif;

global $post;

if ( $emails ):
    $ref_url = get_permalink( $post->ID );
?>
<table class="shop_table my_accout_emails">
    <thead>
        <tr>
            <th class="order-number"><span class="nobr"><?php _e('Order', 'woocommerce'); ?></span></th>
            <th class="actions"><span class="nobr"><?php _e('Actions', 'follow_up_emails'); ?></span></th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ( $emails as $email ):
            $order = WC_FUE_Compatibility::wc_get_order($email->order_id);

            if ( function_exists( 'wc_get_endpoint_url' ) ) {
                $order_url = wc_get_endpoint_url( 'view-order', $email->order_id, wc_get_page_permalink( 'myaccount' ) );
            } else {
                $order_url = add_query_arg('order', $email->order_id, get_permalink( woocommerce_get_page_id( 'view_order' ) ) );
            }
        ?>
        <tr>
            <td class="order-number">
                <a href="<?php echo esc_url( $order_url ); ?>">
                    <?php echo $order->get_order_number(); ?></a>
                    &ndash;
                <em>(<?php printf( _n('1 email', '%d emails', $email->num, 'follow_up_emails'), $email->num ); ?>)</em>
            </td>
            <td><a href="<?php echo wp_nonce_url(add_query_arg(array('fue_action' => 'order_unsubscribe', 'email' => $email->user_email, 'order_id' => $email->order_id, 'ref' => rawurlencode( $ref_url ) ) ), 'fue_unsubscribe'); ?>" class="button"><?php _e('Unsubscribe', 'follow_up_emails'); ?></a></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<div class="woocommerce-info">
    <a href="<?php echo get_permalink( woocommerce_get_page_id('myaccount') ); ?>" class="button"><?php _e('Back to My Account', 'follow_up_emails'); ?></a>
    <?php _e('You are not subscribed to any emails.', 'follow_up_emails'); ?>
</div>
<?php endif; ?>
