<?php

/**
 * List Twitter Messages
 * @var FUE_Email $email
 * @var FUE_Email_Type $type
 */
?>
<div class="section" id="<?php echo $type->id; ?>_mails">
    <h3><?php echo $type->label; ?></h3>

    <div class="followup-description">
	        <p class="description"><?php echo $type->long_description; ?></p>
    </div>

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
                <th scope="col" id="usage_count" class="manage-column column-usage_count" style=""><?php _e('Used', 'follow_up_emails'); ?></th>
                <?php do_action( 'fue_table_'. $type->id .'_head' ); ?>
                <th scope="col" id="status" class="manage-column column-status"><?php _e('Status', 'follow_up_emails'); ?></th>
            </tr>
            </thead>
            <tbody id="the_list">
            <?php if (empty($emails)): ?>
                <tr scope="row">
                    <th colspan="6"><?php _e('No messages available', 'follow_up_emails'); ?></th>
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
                            <span class="edit">
                                <a href="post.php?post=<?php echo $email->id; ?>&action=edit"><?php _e('Edit', 'follow_up_emails'); ?></a>
                            </span>
                                |
                                <span class="edit"><a href="#" class="clone-email" data-id="<?php echo $email->id; ?>"><?php _e('Clone as...', 'follow_up_emails'); ?></a></span>
                                |
                                <span class="trash"><a onclick="return confirm('Really delete this message?');" href="<?php echo wp_nonce_url('admin-post.php?action=fue_followup_delete&id='. $email->id, 'delete-email'); ?>"><?php _e('Delete', 'follow_up_emails'); ?></a></span>
                            </div>
                        </td>
                        <td>
                            <?php
                            echo $email->get_trigger_string();
                            ?>
                        </td>
                        <td>
                            <?php echo $email->usage_count; ?>
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
                <th scope="col" id="type" class="manage-column column-name column-primary" style=""><?php _e('Name', 'follow_up_emails'); ?></th>
                <th scope="col" id="amount" class="manage-column column-interval" style=""><?php _e('Interval', 'follow_up_emails'); ?></th>
                <th scope="col" id="usage_count" class="manage-column column-usage_count" style=""><?php _e('Used', 'follow_up_emails'); ?></th>
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
                <th colspan="6"><?php _e('No messages available', 'follow_up_emails'); ?></th>
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
                            <strong><a class="row-title" href="admin.php?page=followup-emails-form&step=1&id=<?php echo $email->id; ?>"><?php echo stripslashes($email->name); ?></a></strong>
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
                        <td>
                            <?php echo $email->usage_count; ?>
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