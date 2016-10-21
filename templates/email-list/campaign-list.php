<?php
$slug       = $_GET['campaign'];
$campaign   = get_term_by( 'slug', $slug, 'follow_up_email_campaign' );
$emails     = fue_get_emails( 'any', '', array('follow_up_email_campaign' => $slug));
?>

<div id="message" class="updated">
    <p><?php printf( __('You are viewing emails under the <b>%s</b> campaign. <a href="admin.php?page=followup-emails">Remove filter</a>', 'follow_up_emails'), $campaign->name ); ?></p>
</div>

<h3><?php printf( __('%s Emails', 'follow_up_emails'), $campaign->name ); ?></h3>

<table class="wp-list-table widefat fixed posts fue striped <?php echo $slug; ?>-table">
    <thead>
    <tr>
        <td style="" class="manage-column column-cb check-column" scope="col"><label for="cb-select-all-1" class="screen-reader-text"><?php _e('Select All', 'follow_up_emails'); ?></label><input type="checkbox" id="cb-select-all-1"></td>
        <th scope="col" id="title" class="manage-column column-title column-primary" width="200" style=""><?php _e('Name', 'follow_up_emails'); ?></th>
        <th scope="col" id="type" class="manage-column column-type" width="200" style=""><?php _e('Type', 'follow_up_emails'); ?></th>
        <th scope="col" id="amount" class="manage-column column-amount" style=""><?php _e('Interval', 'follow_up_emails'); ?></th>
        <th scope="col" id="product" class="manage-column column-product" style=""><?php _e('Product', 'follow_up_emails'); ?></th>
        <th scope="col" id="category" class="manage-column column-category" style=""><?php _e('Category', 'follow_up_emails'); ?></th>
        <th scope="col" id="stats" class="manage-column column-stats" style=""><?php _e('Stats', 'follow_up_emails'); ?></th>
        <?php do_action( 'fue_table_category_'. $slug .'_head' ); ?>
        <th scope="col" id="status" class="manage-column column-status"><?php _e('Status', 'follow_up_emails'); ?></th>
    </tr>
    </thead>
    <tbody id="the_list">
    <?php if (empty($emails)): ?>
        <tr scope="row">
            <th colspan="8"><?php _e('No emails available', 'follow_up_emails'); ?></th>
        </tr>
    <?php
    else:
        $p = 0;
        foreach ($emails as $email):
            $p++;
            ?>
            <tr scope="row">
                <th class="check-column" scope="row">
                    <input type="checkbox" value="<?php echo $email->id; ?>" name="chk_emails[]" id="cb-select-<?php echo $email->id; ?>">
                    <div class="locked-indicator"></div>
                </th>
                <td class="post-title column-title column-primary">
                    <strong><a class="row-title" href="post.php?post=<?php echo $email->id; ?>&action=edit"><?php echo stripslashes($email->name); ?></a></strong>
                    <div class="row-actions">
                        <?php if ( $email->is_type('manual') ): ?>
                        <span class="send"><a href="admin.php?page=followup-emails&tab=send&id=<?php echo $email->id; ?>"><?php _e('Send', 'follow_up_emails'); ?></a></span> |
                        <?php endif; ?>
                        
	                    <span class="edit"><a href="<?php echo $email->get_preview_url(); ?>" target="_blank"><?php _e('Preview', 'follow_up_emails'); ?></a></span>
						|
                        <span class="edit">
                            <a href="post.php?post=<?php echo $email->id; ?>&action=edit"><?php _e('Edit', 'follow_up_emails'); ?></a>
                        </span>
                        |
                        <span class="edit"><a href="#" class="clone-email" data-id="<?php echo $email->id; ?>"><?php _e('Clone as...', 'follow_up_emails'); ?></a></span>
                        |
                        <span class="trash"><a onclick="return confirm('Really delete this email?');" href="<?php echo wp_nonce_url('admin-post.php?action=fue_followup_delete&id='. $email->id, 'delete-email'); ?>"><?php _e('Delete', 'follow_up_emails'); ?></a></span>
                    </div>
                </td>
                <td>
                    <?php
                    $type = $email->get_email_type();
                    echo $type->singular_label;
                    ?>
                </td>
                <td>
                    <?php
                    echo $email->get_trigger_string();
                    ?>
                </td>
                <td>
                    <?php
                    if ( $email->product_id > 0 ) {
                        $product = WC_FUE_Compatibility::wc_get_product( $email->product_id );

                        if ( $product && $product->exists() ) {
                            echo '<a href="post.php?post='. $email->product_id .'&action=edit">'. $product->get_formatted_name() .'</a>';
                        } else {
                            echo '-';
                        }
                    } else {
                        echo '-';
                    }
                    ?>
                </td>
                <td>
                    <?php
                    if ( empty( $email->category_id ) ) {
                        echo '-';
                    } else {
                        $term = get_term( $email->category_id, 'product_cat' );

                        if (! $term ) {
                            echo '-';
                        } else {
                            echo '<a href="edit-tags.php?action=edit&taxonomy=product_cat&tag_ID='. $email->category_id .'&post_type=product">'. $term->name .'</a>';
                        }
                    }
                    ?>
                </td>
                <td>
                    <?php
                    $sent      = $email->usage_count;
                    $opens     = FUE_Reports::count_event_occurences( $email->id, 'open' );
                    $clicks    = FUE_Reports::count_unique_clicks( $email->id );
                    $opens_pct = 0;

                    if ( $sent > 0 ) {
                        $opens_pct = ($opens / $sent) * 100;
                    }

                    printf( '<small>Sent: %d<br/>Opens: %d (%.2f%%)<br/>Clicks: %d</small>', $sent, $opens, $opens_pct, $clicks );
                    ?>
                </td>

                <?php do_action( 'fue_table_all_products_body' ); ?>
                <td class="status">
                    <?php if ($email->status == FUE_Email::STATUS_ACTIVE): ?>
                        <?php _e('Active', 'follow_up_emails'); ?>
                        <br/><small><a href="#" class="toggle-activation" data-id="<?php echo $email->id; ?>"><?php _e('Deactivate', 'follow_up_emails'); ?></a></small>
                    <?php else: ?>
                        <?php _e('Inactive', 'follow_up_emails'); ?>
                        <br/><small><a href="#" class="toggle-activation" data-id="<?php echo $email->id; ?>"><?php _e('Activate', 'follow_up_emails'); ?></a></small>
                    <?php endif; ?>

                    <?php do_action( 'fue_table_status_actions', $email ); ?>

                </td>
            </tr>
        <?php
        endforeach;
        ?>
    <?php endif; ?>
    </tbody>
</table>

<p>
    <select class="bulk-action-select" name="bulk_action_category_<?php echo $slug; ?>">
        <option value=""><?php _e('Bulk Actions', 'follow_up_emails'); ?></option>
        <option value="activate"><?php _e('Activate', 'follow_up_emails'); ?></option>
        <option value="deactivate"><?php _e('Deactivate', 'follow_up_emails'); ?></option>
        <option value="archive"><?php _e('Archive', 'follow_up_emails'); ?></option>
        <option value="delete"><?php _e('Delete', 'follow_up_emails'); ?></option>
        <?php do_action( 'fue_campaign_bulk_actions', $campaign ); ?>
    </select>
    <input type="submit" class="button" name="bulk_action_category_<?php echo $slug; ?>_button" value="<?php _e('Apply', 'follow_up_emails'); ?>" />
</p>