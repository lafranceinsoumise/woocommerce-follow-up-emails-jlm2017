<?php

/**
 * List product emails in a table
 */

/* @var FUE_Email $email */
?>
<div class="section" id="<?php echo $type->id; ?>_mails">
    <h3><?php echo $type->label; ?></h3>

    <div class="followup-description">
	        <p class="description"><?php echo $type->long_description; ?></p>
    </div>

    <table class="form-table">
        <tr>
            <th style="width:300px;"><?php _e('Send a copy of all emails of this type to:', 'follow_up_emails'); ?></th>
            <td><input type="text" name="bcc[<?php echo $type->id; ?>]" value="<?php echo esc_attr( $bcc ); ?>" /></td>
        </tr>
        <tr>
            <th><?php _e('Use this address as the From and Reply-To:', 'follow_up_emails'); ?></th>
            <td>
                <input type="text" name="from_name[<?php echo $type->id; ?>]" value="<?php echo esc_attr( $from_name ); ?>" placeholder="<?php _e('Name', 'follow_up_emails'); ?>" />
                &lt;<input type="email" name="from_email[<?php echo $type->id; ?>]" value="<?php echo esc_attr( $from ); ?>" placeholder="<?php _e('Email', 'follow_up_emails'); ?>" />&gt;
            </td>
        </tr>
    </table>

    <h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
        <a href="#<?php echo $type->id; ?>_active_tab" data-key="<?php echo $type->id; ?>" class="status-tab nav-tab nav-tab-active"><?php _e('Active Emails', 'follow_up_emails'); ?></a>
        <a href="#<?php echo $type->id; ?>_archived_tab" data-key="<?php echo $type->id; ?>" class="status-tab nav-tab"><?php _e('Archived Emails', 'follow_up_emails'); ?></a>
    </h2>

    <br/>

    <div class="email-tab <?php echo $type->id; ?>-tab active" id="<?php echo $type->id; ?>_active_tab">
        <table class="wp-list-table widefat fixed striped posts fue fue-sortable <?php echo $type->id; ?>-table active">
            <thead>
            <tr>
                <td style="" id="cb" class="manage-column column-cb check-column" scope="col"><label for="cb-select-all-1" class="screen-reader-text"><?php _e('Select All', 'follow_up_emails'); ?></label><input type="checkbox" id="cb-select-all-1"></td>
                <th scope="col" id="priority" class="manage-column column-priority" width="80"><?php _e('Priority', 'follow_up_emails'); ?> <img class="help_tip" title="<?php _e('Priorities define the order at which emails are queued. If an order matches multiple emails, and always send is not enabled, the priority order will define which email is queued for that customer order.', 'follow_up_emails'); ?>" src="<?php echo FUE_TEMPLATES_URL; ?>/images/help.png" width="16" height="16" /></th>
                <th scope="col" id="title" class="manage-column column-title column-primary" width="200" style=""><?php _e('Name', 'follow_up_emails'); ?></th>
                <th scope="col" id="amount" class="manage-column column-interval" style=""><?php _e('Interval', 'follow_up_emails'); ?></th>
                <th scope="col" id="product" class="manage-column column-product" style=""><?php _e('Product', 'follow_up_emails'); ?></th>
                <th scope="col" id="category" class="manage-column column-category" style=""><?php _e('Category', 'follow_up_emails'); ?></th>
                <th scope="col" id="stats" class="manage-column column-stats" style=""><?php _e('Stats', 'follow_up_emails'); ?></th>
                <?php do_action( 'fue_table_'. $type->id .'_head' ); ?>
                <th scope="col" id="status" class="manage-column column-status"><?php _e('Status', 'follow_up_emails'); ?></th>
            </tr>
            </thead>
            <tbody id="the_list">
            <?php if (empty($emails)): ?>
                <tr scope="row">
                    <th colspan="9"><?php _e('No emails available', 'follow_up_emails'); ?></th>
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
                        <td class="column-priority" style="text-align: center;"><span class="priority"><?php echo $p; ?></span></td>
                        <td class="post-title column-title column-primary">
                            <input type="hidden" name="<?php echo $type->id; ?>_order[]" value="<?php echo $email->id; ?>" />
                            <strong><a class="row-title" href="post.php?post=<?php echo $email->id; ?>&action=edit"><?php echo stripslashes($email->name); ?></a></strong>
                            <div class="row-actions">
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
                            echo $email->get_trigger_string();
                            ?>
                        </td>
                        <td><?php echo ($email->product_id > 0) ? '<a href="post.php?post='. $email->product_id .'&action=edit">'. get_the_title($email->product_id) .'</a>' : '-'; ?></td>
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
                            <span class="status-toggle">
                                <?php if ($email->status == FUE_Email::STATUS_ACTIVE): ?>
                                    <?php _e('Active', 'follow_up_emails'); ?>
                                    <br/><small><a href="#" class="toggle-activation" data-id="<?php echo $email->id; ?>"><?php _e('Deactivate', 'follow_up_emails'); ?></a></small>
                                <?php else: ?>
                                    <?php _e('Inactive', 'follow_up_emails'); ?>
                                    <br/><small><a href="#" class="toggle-activation" data-id="<?php echo $email->id; ?>"><?php _e('Activate', 'follow_up_emails'); ?></a></small>
                                <?php endif; ?>
                            </span>
                            |
                            <small><a href="#" class="archive-email" data-id="<?php echo $email->id; ?>" data-key="<?php echo $type->id; ?>"><?php _e('Archive', 'follow_up_emails'); ?></a></small>

                            <?php do_action( 'fue_table_status_actions', $email ); ?>

                        </td>
                    </tr>
                <?php
                endforeach;
                ?>
            <?php endif; ?>
            </tbody>
        </table>
        <div class="fue_table_footer">
            <div class="order_message"></div>
        </div>

        <p>
            <select class="bulk-action-select bulk-action-<?php echo $type->id; ?>-active" name="bulk_action_<?php echo $type->id; ?>_active">
                <option value=""><?php _e('Bulk Actions', 'follow_up_emails'); ?></option>
                <option value="activate"><?php _e('Activate', 'follow_up_emails'); ?></option>
                <option value="deactivate"><?php _e('Deactivate', 'follow_up_emails'); ?></option>
                <option value="archive"><?php _e('Archive', 'follow_up_emails'); ?></option>
                <option value="delete"><?php _e('Delete', 'follow_up_emails'); ?></option>
                <?php do_action( 'fue_active_bulk_actions', $type ); ?>
            </select>
            <input type="submit" class="button" name="bulk_action_<?php echo $type->id; ?>_active_button" value="<?php _e('Apply', 'follow_up_emails'); ?>" />
        </p>
    </div>

    <div class="email-tab <?php echo $type->id; ?>-tab inactive" id="<?php echo $type->id; ?>_archived_tab">
        <table class="wp-list-table widefat fixed posts fue fue-sortable <?php echo $type->id; ?>-table inactive">

            <thead>
            <tr>
                <th style="" class="manage-column column-cb check-column" scope="col"><label for="cb-select-all-1" class="screen-reader-text"><?php _e('Select All', 'follow_up_emails'); ?></label><input type="checkbox" id="cb-select-all-1"></th>
                <th scope="col" id="priority" class="manage-column column-priority" style="width:50px;"><?php _e('Priority', 'follow_up_emails'); ?></th>
                <th scope="col" id="name" class="manage-column column-name column-primary" style=""><?php _e('Name', 'follow_up_emails'); ?></th>
                <th scope="col" id="amount" class="manage-column column-interval" style=""><?php _e('Interval', 'follow_up_emails'); ?></th>
                <th scope="col" id="product" class="manage-column column-product" style=""><?php _e('Product', 'follow_up_emails'); ?></th>
                <th scope="col" id="category" class="manage-column column-category" style=""><?php _e('Category', 'follow_up_emails'); ?></th>
                <th scope="col" id="stats" class="manage-column column-stats" style=""><?php _e('Stats', 'follow_up_emails'); ?></th>
                <?php do_action( 'fue_table_'. $type->id .'_head' ); ?>
                <th scope="col" id="status" class="manage-column column-status"><?php _e('Status', 'follow_up_emails'); ?></th>
            </tr>
            </thead>
            <tbody id="the_list">

            <?php
            $display = '';

            if ( !empty( $archived_emails ) ) {
                $display = 'none';
            }
            ?>

            <tr scope="row" class="no-archived-emails" style="display: <?php echo $display; ?>;">
                <th colspan="9"><?php _e('No emails available', 'follow_up_emails'); ?></th>
            </tr>

            <?php
            if ( !empty( $archived_emails ) ):
                $p = 0;
                foreach ($archived_emails as $email):
                    $p++;
                    ?>
                    <tr scope="row">
                        <th class="check-column" scope="row">
                            <input type="checkbox" value="<?php echo $email->id; ?>" name="chk_emails[]" id="cb-select-<?php echo $email->id; ?>">
                            <div class="locked-indicator"></div>
                        </th>
                        <td class="column-priority" style="text-align: center;"><span class="priority"><?php echo $p; ?></span></td>
                        <td class="post-title column-title column-primary">
                            <input type="hidden" name="<?php echo $type->id; ?>_order[]" value="<?php echo $email->id; ?>" />
                            <strong><a class="row-title" href="post.php?post=<?php echo $email->id; ?>&action=edit"><?php echo stripslashes($email->name); ?></a></strong>
                            <div class="row-actions">
                                <span class="edit">
                                    <a href="post.php?post=<?php echo $email->id; ?>&action=edit"><?php _e('Edit', 'follow_up_emails'); ?></a>
                                </span>
                                |
                                <span class="trash"><a onclick="return confirm('Really delete this email?');" href="<?php echo wp_nonce_url('admin-post.php?action=fue_followup_delete&id='. $email->id, 'delete-email'); ?>"><?php _e('Delete', 'follow_up_emails'); ?></a></span>
                            </div>
                        </td>
                        <td>
                            <?php
                            echo $email->get_trigger_string();
                            ?>
                        </td>
                        <td><?php echo ($email->product_id > 0) ? '<a href="post.php?post='. $email->product_id .'&action=edit">'. get_the_title($email->product_id) .'</a>' : '-'; ?></td>
                        <td>
                            <?php
                            if ($email->category_id == 0) {
                                echo '-';
                            } else {
                                $term = get_term( $email->category_id, 'product_cat' );

                                if ( !$term ) {
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
                            <?php _e('Archived', 'follow_up_emails'); ?>
                            <br/><small><a href="#" class="unarchive" data-id="<?php echo $email->id; ?>" data-key="<?php echo $type->id; ?>"><?php _e('Activate', 'follow_up_emails'); ?></a></small>
                        </td>
                    </tr>
                <?php
                endforeach;
                ?>
            <?php endif; ?>
            </tbody>
        </table>
        <div class="fue_table_footer">
            <div class="order_message"></div>
        </div>

        <p>
            <select class="bulk-action-select bulk-action-<?php echo $type->id; ?>-archived" name="bulk_action_<?php echo $type->id; ?>_archived">
                <option value=""><?php _e('Bulk Actions', 'follow_up_emails'); ?></option>
                <option value="activate"><?php _e('Activate', 'follow_up_emails'); ?></option>
                <option value="deactivate"><?php _e('Deactivate', 'follow_up_emails'); ?></option>
                <option value="unarchive"><?php _e('Unarchive', 'follow_up_emails'); ?></option>
                <option value="delete"><?php _e('Delete', 'follow_up_emails'); ?></option>
                <?php do_action( 'fue_archived_bulk_actions', $type ); ?>
            </select>
            <input type="submit" class="button" name="bulk_action_<?php echo $type->id; ?>_archived_button" value="<?php _e('Apply', 'follow_up_emails'); ?>" />
        </p>
    </div>

</div>