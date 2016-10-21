<?php

/**
 * List manual emails in a table
 */

?>
<div class="section" id="manual_mails" style="display:none;">
    <h3><?php _e('Manual Emails', 'follow_up_emails'); ?></h3>

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
            <td><input type="email" name="from_email[<?php echo $type->id; ?>]" value="<?php echo esc_attr( $from ); ?>" /></td>
        </tr>
    </table>

    <table class="wp-list-table widefat fixed striped posts fue <?php echo $type->id; ?>-table active">

        <thead>
        <tr>
            <td style="" class="manage-column column-cb check-column" scope="col"><label for="cb-select-all-1" class="screen-reader-text"><?php _e('Select All', 'follow_up_emails'); ?></label><input type="checkbox" id="cb-select-all-1"></td>
            <th scope="col" class="manage-column column-type column-primary" style=""><?php _e('Name', 'follow_up_emails'); ?></th>
            <th scope="col" class="manage-column column-stats" style=""><?php _e('Stats', 'follow_up_emails'); ?></th>
            <?php do_action( 'fue_table_manual_head' ); ?>
        </tr>
        </thead>
        <tbody id="the_list">
        <?php if (empty($emails)): ?>
            <tr scope="row">
                <th colspan="3"><?php _e('No emails available', 'follow_up_emails'); ?></th>
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
                        <input type="hidden" name="manual_order[]" value="<?php echo $email->id; ?>" />
                        <strong><a class="row-title" href="post.php?post=<?php echo $email->id; ?>&action=edit"><?php echo stripslashes($email->name); ?></a></strong>
                        <div class="row-actions">
                            <span class="send"><a href="admin.php?page=followup-emails&tab=send&id=<?php echo $email->id; ?>"><?php _e('Send', 'follow_up_emails'); ?></a></span>
                            |
                            <span class="edit"><a href="<?php echo $email->get_preview_url(); ?>" target="_blank"><?php _e('Preview', 'follow_up_emails'); ?></a></span>
                            |
                            <span class="edit">
                                <a href="post.php?post=<?php echo $email->id; ?>&action=edit"><?php _e('Edit', 'follow_up_emails'); ?></a>
                            </span>
                            |
                            <span class="trash"><a onclick="return confirm('Really delete this email?');" href="<?php echo wp_nonce_url('admin-post.php?action=fue_followup_delete&id='. $email->id, 'delete-email'); ?>"><?php _e('Delete', 'follow_up_emails'); ?></a></span>
                        </div>
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
                    <?php do_action( 'fue_table_manual_body' ); ?>
                </tr>
            <?php
            endforeach;
            ?>
        <?php endif; ?>
        </tbody>
    </table>

    <p>
        <select class="bulk-action-select bulk-action-<?php echo $type->id; ?>_active" name="bulk_action_<?php echo $type->id; ?>_active">
            <option value=""><?php _e('Bulk Actions', 'follow_up_emails'); ?></option>
            <option value="delete"><?php _e('Delete', 'follow_up_emails'); ?></option>
        </select>
        <input type="submit" class="button" name="bulk_action_<?php echo $type->id; ?>_active_button" value="<?php _e('Apply', 'follow_up_emails'); ?>" />
    </p>

</div>