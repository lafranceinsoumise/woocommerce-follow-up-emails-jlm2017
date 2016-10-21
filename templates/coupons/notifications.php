<?php

/**
 * Coupon Notifications
 */

if (isset($_GET['coupon_created'])): ?>
<div id="message" class="updated"><p><?php _e('Coupon created', 'follow_up_emails'); ?></p></div>
<?php
endif;

if (isset($_GET['coupon_deleted'])): ?>
    <div id="message" class="updated"><p><?php _e('Coupon deleted!', 'follow_up_emails'); ?></p></div>
<?php
endif;

if (isset($_GET['coupon_updated'])): ?>
    <div id="message" class="updated"><p><?php _e('Coupon updated', 'follow_up_emails'); ?></p></div>
<?php
endif;