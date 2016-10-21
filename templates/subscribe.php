<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>
<div id="fue_subscribe_container">
    <?php if ( isset( $_GET['fue_subscribed'] ) ): ?>
    <div class="fue-success">
        <p><?php echo $success_message; ?></p>
    </div>
    <?php else: ?>
    <form id="fue_subscribe_form" method="post">
        <?php if ( !empty( $_REQUEST['error'] ) ): ?>
            <div class="fue-error">
                <p><?php echo wp_kses_post( $_REQUEST['error'] ); ?></p>
            </div>
        <?php endif; ?>
        <label for="fue_subscriber_email"><?php echo $label; ?></label>
        <input type="email" name="fue_subscriber_email" id="fue_subscriber_email" placeholder="<?php echo $placeholder; ?>" value="" />

        <input type="hidden" name="fue_action" value="subscribe" />
        <input type="hidden" name="fue_email_list" value="<?php echo $list; ?>" />
        <?php wp_nonce_field( 'fue_subscribe' ); ?>
        <input type="submit" id="fue_subscriber_submit" class="button button-submit" value="<?php echo $submit_text; ?>" />
    </form>
    <?php endif; ?>
</div>