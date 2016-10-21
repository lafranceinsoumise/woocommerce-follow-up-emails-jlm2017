<div class="wrap">
    <h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
        <a href="admin.php?page=followup-emails&amp;tab=history" class="nav-tab nav-tab-active"><?php _e('Emails History', 'follow_up_emails'); ?></a>
        <a href="admin.php?page=followup-emails-reports&amp;tab=dbg_queue" class="nav-tab <?php if ($tab == 'dbg_queue') echo 'nav-tab-active'; ?>"><?php _e('Queue', 'follow_up_emails'); ?></a>
    </h2>

    <form id="email-history" action="" method="get">
        <input type="hidden" name="page" value="followup-emails" />
        <input type="hidden" name="tab" value="history" />
        <div class="tablenav top">
            <div class="alignleft actions">
                <select id="email_filter" name="id">
                    <option selected="selected" value=""><?php _e('Select Email', 'follow_up_emails'); ?></option>
                    <?php foreach ( $emails as $email ): ?>
                    <option value="<?php echo $email->id; ?>" <?php selected( $id, $email->id ); ?>><?php echo $email->name; ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="submit" value="Show History" class="button action" id="doaction">
            </div>
            <br class="clear">
        </div>
        <table class="wp-list-table widefat fixed posts">
            <thead>
            <tr>
                <th>Email</th>
                <th>User</th>
                <th>Log</th>
                <th>Date</th>
            </tr>
            </thead>
            <tbody>
            <?php if ( empty( $logs ) ): ?>
            <tr>
                <td colspan="4">No logs found</td>
            </tr>
            <?php
            else:
                foreach ( $logs as $log ):
                    $email  = new FUE_Email( $log->followup_id );
                    $user   = get_user_by( 'id', $log->user_id );

                    if ( !$email->exists() ) {
                        continue;
                    }
            ?>
                <tr>
                    <td><?php printf( '%s (%d)', $email->name, $email->id ); ?></td>
                    <td><?php echo $user->display_name; ?></td>
                    <td><?php echo $log->content; ?></td>
                    <td><?php echo $log->date_added; ?></td>
                </tr>
            <?php
                endforeach;
            endif;
            ?>
            </tbody>
        </table>
    </form>
</div>