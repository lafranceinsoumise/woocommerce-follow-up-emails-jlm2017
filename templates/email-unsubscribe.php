<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$order_id       = 0;
$order_url      = '';
$email_address  = '';
$queue_id       = '';

if ( !empty( $_GET['qid'] ) ) {
    $queue_id   = absint( $_GET['qid'] );
    $queue      = new FUE_Sending_Queue_Item( $queue_id );

    if ( $queue->exists() ) {
        $order_id       = $queue->order_id;
        $order          = WC_FUE_Compatibility::wc_get_order( $order_id );
        $email_address  = $queue->user_email;

        if ( function_exists( 'wc_get_endpoint_url' ) ) {
            $order_url = wc_get_endpoint_url( 'view-order', $order_id, wc_get_page_permalink( 'myaccount' ) );
        } else {
            $order_url = add_query_arg('order', $order_id, get_permalink( woocommerce_get_page_id( 'view_order' ) ) );
        }

    }
}


get_header();
?>
<div id="primary" class="content-area">
    <div id="content" class="site-content" role="main">

        <article class="page type-page status-publish hentry">
            <header class="entry-header">
                <h1 class="entry-title"><?php _e('Unsubscribe from Email List', 'follow_up_emails'); ?></h1>
            </header>
            <?php
            $email = '';
            if (isset($_GET['fue']) && !empty($_GET['fue'])) {
                $email = $_GET['fue'];
            } else {
                $email = $email_address;
            }

            $eid = isset($_GET['fueid']) ? $_GET['fueid'] : '';

            if ( !empty( $_REQUEST['error'] ) ):
            ?>
            <div class="error woocommerce-error">
                <?php echo wp_kses_post( urldecode( $_REQUEST['error'] ) ); ?>
            </div>
            <?php
            endif;
            ?>
            <div class="fue-unsubscribe-form entry-content">
                <form action="" method="post">
                    <input type="hidden" name="fue_action" value="fue_unsubscribe" />
                    <input type="hidden" name="fue_eid" value="<?php echo esc_attr($eid); ?>" />
                    <input type="hidden" name="fue_qid" value="<?php echo esc_attr($queue_id); ?>" />
                    <p>
                        <label for="fue_email"><?php _e('Email Address:', 'follow_up_emails'); ?></label>
                        <br/>
                        <input type="text" id="fue_email" name="fue_email" value="<?php echo esc_attr($email); ?>" size="25" />
                    </p>

                    <?php if ( $order_id ): ?>
                    <p>
                        <label for="fue_unsubscribe_order">
                            <input type="checkbox" name="unsubscribe_order_id" id="fue_unsubscribe_order" value="<?php echo esc_attr( $order_id ); ?>" />
                            <?php printf( __('Do not send me emails regarding <a href="%s">Order %s</a> again.', 'follow_up_emails'), esc_url( $order_url ), $order->get_order_number() ); ?>
                        </label>
                    </p>
                    <?php endif; ?>

                    <p>
                        <label for="fue_unsubscribe_all">
                            <input type="checkbox" name="unsubscribe_all" id="fue_unsubscribe_all" value="yes" />
                            <?php _e('Do not send me non-order emails again', 'follow_up_emails'); ?>
                        </label>
                    </p>

                    <?php do_action('fue_unsubscribe_form', $email); ?>
                    <p>
                        <input type="submit" name="fue_submit" id="fue_submit" value="<?php _e('Unsubscribe', 'follow_up_emails'); ?>" />
                    </p>
                </form>
            </div>
        </article>
    </div><!-- #content -->
</div><!-- #primary -->
<script>
jQuery(document).ready(function($) {

    $(".fue-unsubscribe-form :input[type=checkbox]").change(function() {
        var num_checked = $(".fue-unsubscribe-form :input[type=checkbox]:checked").length;

        if ( num_checked > 0 ) {
            $("#fue_submit").attr("disabled", false);
        } else {
            $("#fue_submit").attr("disabled", true);
        }
    }).change();

} );
</script>
<?php
get_sidebar( 'content' );
get_sidebar();
get_footer();