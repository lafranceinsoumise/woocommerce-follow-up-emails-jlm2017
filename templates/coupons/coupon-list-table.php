<?php
$coupons = FUE_Coupons::get_coupons();
?>
<div class="wrap woocommerce">
    <div class="icon32"><img src="<?php echo FUE_TEMPLATES_URL .'/images/send_mail.png'; ?>" /></div>
    <h2>
        <?php _e('Follow-Up Emails &raquo; Email Coupons', 'follow_up_emails'); ?>
        <a href="admin.php?page=followup-emails-coupons&action=new-coupon" class="add-new-h2"><?php _e('Add Coupon', 'wc_followup_emalis'); ?></a>
    </h2>
    
    <p><?php _e('Add customized and automated coupons to your follow-ups. You must create the coupon in this menu first, then select to include it in your Follow-up. The coupon code generation feature used here allows coupons to be created that are are unique for each user.', 'follow_up_emails'); ?></p> 

    <?php include 'notifications.php'; ?>

    <div class="subsubsub_section">
        <ul class="subsubsub">
            <li>
                <a href="#coupons" class="current"><?php _e('Coupons', 'follow_up_emails'); ?></a> |
                <a href="#usage" class=""><?php _e('Reports', 'follow_up_emails'); ?></a>
            </li>
        </ul>
        <br class="clear">

        <div class="section" id="coupons">
            <form action="admin-post.php" method="post">
                <table class="widefat fixed striped posts">
                    <thead>
                    <tr>
                        <th scope="col" id="name" class="manage-column column-name" style=""><?php _e('Name', 'follow_up_emails'); ?></th>
                        <th scope="col" id="type" class="manage-column column-type" style=""><?php _e('Type', 'follow_up_emails'); ?></th>
                        <th scope="col" id="amount" class="manage-column column-amount" style=""><?php _e('Amount', 'follow_up_emails'); ?></th>
                        <th scope="col" id="usage_count" class="manage-column column-usage_count" style=""><?php _e('Sent', 'follow_up_emails'); ?></th>
                    </tr>
                    </thead>
                    <tbody id="the_list">
                    <?php
                    if (empty($coupons)):
                        ?>
                        <tr scope="row">
                            <th colspan="4"><?php _e('No coupons available', 'follow_up_emails'); ?></th>
                        </tr>
                    <?php
                    else:
                        foreach ($coupons as $coupon):
                            ?>
                            <tr scope="row">
                                <td class="post-title column-title">
                                    <strong><a class="row-title" href="admin.php?page=followup-emails-coupons&action=edit-coupon&id=<?php echo $coupon->id; ?>"><?php echo stripslashes($coupon->coupon_name); ?></a></strong>
                                    <div class="row-actions">
                                        <span class="edit"><a href="admin.php?page=followup-emails-coupons&action=edit-coupon&id=<?php echo $coupon->id; ?>"><?php _e('Edit', 'follow_up_emails'); ?></a></span>
                                        |
                                        <span class="trash"><a onclick="return confirm('<?php _e('Really delete this entry?', 'follow_up_emails'); ?>');" href="admin-post.php?action=fue_delete_coupon&id=<?php echo $coupon->id; ?>"><?php _e('Delete', 'follow_up_emails'); ?></a></span>
                                    </div>
                                </td>
                                <td><?php echo FUE_Coupons::get_discount_type($coupon->coupon_type); ?></td>
                                <td><?php echo floatval($coupon->amount); ?></td>
                                <td><?php echo $coupon->usage_count; ?></td>
                            </tr>
                        <?php
                        endforeach;
                    endif;
                    ?>
                    </tbody>
                </table>
            </form>
        </div>

        <div class="section" id="usage">
            <?php
            // coupons sorting
            $sort['sortby'] = 'date_sent';
            $sort['sort']   = 'desc';

            if ( isset($_GET['sortby']) && !empty($_GET['sortby']) ) {
            $valid = array('date_sent', 'email_address', 'coupon_used');
            if ( in_array($_GET['sortby'], $valid) ) {
            $sort['sortby'] = $_GET['sortby'];
            $sort['sort']   = (isset($_GET['sort']) && $_GET['sort'] == 'asc') ? 'asc' : 'desc';
            }
            }

            $coupon_reports = FUE_Reports::get_reports(array('type' => 'coupons', 'sort' => $sort));

            $email_address_class    = ($sort['sortby'] != 'email_address') ? 'sortable' : 'sorted';
            $email_address_sort     = ($email_address_class == 'sorted') ? $sort['sort'] : 'asc';
            $email_address_dir      = ($email_address_sort == 'asc') ? 'desc' : 'asc';

            $used_class     = ($sort['sortby'] != 'coupon_used') ? 'sortable' : 'sorted';
            $used_sort      = ($used_class == 'sorted') ? $sort['sort'] : 'asc';
            $used_dir       = ($used_sort == 'asc') ? 'desc' : 'asc';

            $sent_class     = ($sort['sortby'] != 'date_sent') ? 'sortable' : 'sorted';
            $sent_sort      = ($sent_class == 'sorted') ? $sort['sort'] : 'asc';
            $sent_dir       = ($sent_sort == 'asc') ? 'desc' : 'asc';

            ?>
            <form action="admin-post.php" method="post">
                <table class="widefat fixed striped posts">
                    <thead>
                    <tr>
                        <td scope="col" id="cb" class="manage-column column-cb check-column">
                            <label class="screen-reader-text" for="cb-select-all-coupons">Select All</label>
                            <input id="cb-select-all-coupons" type="checkbox">
                        </td>
                        <th scope="col" id="coupon_name" class="manage-column column-type" style=""><?php _e('Coupon Name', 'follow_up_emails'); ?></th>
                        <th scope="col" id="email_address" class="manage-column column-usage_count <?php echo $email_address_class .' '. $email_address_sort; ?>" style="">
                            <a href="admin.php?page=followup-emails-reports&tab=reports&sortby=email_address&sort=<?php echo $email_address_dir; ?>&v=coupons">
                                <span><?php _e('Email Address', 'follow_up_emails'); ?></span>
                                <span class="sorting-indicator"></span>
                            </a>
                        </th>
                        <th scope="col" id="coupon_code" class="manage-column column-usage_count" style=""><?php _e('Coupon Code', 'follow_up_emails'); ?> <img class="help_tip" width="16" height="16" title="<?php _e('This is the unique coupon code generated by the follow-up email for this specific email address', 'follow_up_emails'); ?>" src="<?php echo FUE_TEMPLATES_URL; ?>/images/help.png" /></th>
                        <th scope="col" id="email_name" class="manage-column column-usage_count" style=""><?php _e('Email Name', 'follow_up_emails'); ?> <img class="help_tip" width="16" height="16" title="<?php _e('This is the name of the follow-up email that generated the coupon that was sent to this specific email address', 'follow_up_emails'); ?>" src="<?php echo FUE_TEMPLATES_URL; ?>/images/help.png" /></th>
                        <th scope="col" id="used" class="manage-column column-used <?php echo $used_class .' '. $used_sort; ?>" style="">
                            <a href="admin.php?page=followup-emails-reports&tab=reports&sortby=coupon_used&sort=<?php echo $used_dir; ?>&v=coupons">
                                <span><?php _e('Used', 'follow_up_emails'); ?>  <img class="help_tip" width="16" height="16" title="<?php _e('This tells you if this specific coupon code generated and sent via follow-up emails has been used, and if it has, it includes the date and time', 'follow_up_emails'); ?>" src="<?php echo FUE_TEMPLATES_URL; ?>/images/help.png" /></span>
                                <span class="sorting-indicator"></span>
                            </a>
                        </th>
                        <th scope="col" id="date_sent" class="manage-column column-date_sent <?php echo $sent_class .' '. $sent_sort; ?>" style="">
                            <a href="admin.php?page=followup-emails-reports&tab=reports&sortby=date_sent&sort=<?php echo $sent_dir; ?>&v=coupons">
                                <span><?php _e('Date Sent', 'follow_up_emails'); ?> <img class="help_tip" width="16" height="16" title="<?php _e('This is the date and time that this specific coupon code was sent to this email address', 'follow_up_emails'); ?>" src="<?php echo FUE_TEMPLATES_URL; ?>/images/help.png" /></span>
                                <span class="sorting-indicator"></span>
                            </a>
                        </th>
                    </tr>
                    </thead>
                    <tbody id="the_list">
                    <?php
                    if (empty($coupon_reports)) {
                        echo '
                        <tr scope="row">
                            <th colspan="7">'. __('No reports available', 'follow_up_emails') .'</th>
                        </tr>';
                    } else {
                        foreach ($coupon_reports as $report) {
                            $used = __('No', 'follow_up_emails');

                            if ( $report->coupon_used == 1 ) {
                                $date = date( get_option('date_format') .' '. get_option('time_format') , strtotime($report->date_used));
                                $used = sprintf(__('Yes (%s)', 'follow_up_emails'), $date);
                            }

                            echo '
                            <tr scope="row">
                                <th scope="row" class="check-column">
                                    <input id="cb-select-'. $report->id .'" type="checkbox" name="coupon_id[]" value="'. $report->id .'">
                                    <div class="locked-indicator"></div>
                                </th>
                                <td class="post-title column-title">
                                    <strong>'. stripslashes($report->coupon_name) .'</strong>
                                </td>
                                <td>'. esc_html($report->email_address) .'</td>
                                <td>'. esc_html($report->coupon_code) .'</td>
                                <td>'. esc_html($report->email_name) .'</td>
                                <td>'. $used .'</td>
                                <td>'. date( get_option('date_format') .' '. get_option('time_format') , strtotime($report->date_sent)) .'</td>
                            </tr>
                            ';
                        }
                    }
                    ?>
                    </tbody>
                </table>
                <div class="tablenav bottom">
                    <div class="alignleft actions bulkactions">
                        <input type="hidden" name="action" value="fue_reset_reports" />
                        <input type="hidden" name="type" value="coupons" />
                        <select name="coupons_action">
                            <option value="-1" selected="selected"><?php _e('Bulk Actions', 'wordpress'); ?></option>
                            <option value="trash"><?php _e('Delete Selected', 'follow_up_emails'); ?></option>
                        </select>
                        <input type="submit" name="" id="doaction-coupon" class="button action" value="Apply">
                    </div>
                </div>
            </form>

        </div>
    </div>

</div>
<script>
    jQuery(document).ready(function($) {
        $("div.section:gt(0)").hide();

        // Subsubsub tabs
        jQuery('div.subsubsub_section ul.subsubsub li a:eq(0)').addClass('current');
        jQuery('div.subsubsub_section .section:gt(0)').hide();

        jQuery('div.subsubsub_section ul.subsubsub li a').click(function(){
            var $clicked = jQuery(this);
            var $section = $clicked.closest('.subsubsub_section');
            var $target  = $clicked.attr('href');

            $section.find('a').removeClass('current');

            if ( $section.find('.section:visible').size() > 0 ) {
                $section.find('.section:visible').fadeOut( 100, function() {
                    $section.find( $target ).fadeIn('fast');
                });
            } else {
                $section.find( $target ).fadeIn('fast');
            }

            $clicked.addClass('current');
            jQuery('#last_tab').val( $target );

            return false;
        });
    });
</script>