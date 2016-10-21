<div class="wrap">
    <div class="icon32"><img src="<?php echo FUE_TEMPLATES_URL .'/images/send_mail.png'; ?>" /></div>
    <h2>
        <?php _e('Manage Opt-outs', 'follow_up_emails'); ?>
    </h2>

    <?php if (isset($_GET['restored']) && $_GET['restored'] > 0): ?>
        <div id="message" class="updated"><p><?php printf( _n('1 email has been restored', '%d emails have been restored', intval($_GET['restored']), 'follow_up_emails'), intval($_GET['restored'])); ?></p></div>
    <?php endif; ?>

    <?php if (isset($_GET['added'])): ?>
        <div id="message" class="updated"><p><?php printf(__('<em>%s</em> has been added to the opt-out list', 'follow_up_emails'), strip_tags($_GET['added'])); ?></p></div>
    <?php endif; ?>

    <?php if (isset($_GET['error']) ): ?>
        <div id="message" class="error"><p><?php echo esc_html($_GET['error']); ?></p></div>
    <?php endif; ?>

    <form action="admin-post.php" method="post">

        <div class="tablenav top">

            <div class="alignleft actions bulkactions">
                <input type="text" name="email_address" placeholder="Add email to opt-out" />
                <input type="submit" name="button_add" id="post-query-submit" class="button button-secondary" value="Add">
            </div>
            <br class="clear">
        </div>
        <table class="wp-list-table widefat fixed striped posts">
            <thead>
            <tr>
                <td scope="col" id="cb" class="manage-column column-cb check-column" style=""><label class="screen-reader-text" for="cb-select-all-1">Select All</label><input id="cb-select-all-1" type="checkbox"></td>
                <th scope="col" class="manage-column column-email_address" style=""><?php _e('Email Address', 'follow_up_emails'); ?></th>
                <th scope="col" class="manage-column column-date" style="width:200px;"><?php _e('Date', 'follow_up_emails'); ?></th>
                <th scope="col" class="manage-column column-actions" style="width: 50px;">&nbsp;</th>
            </tr>
            </thead>
            <tbody id="the_list">
            <?php
            $date_format = get_option('date_format') .' '. get_option('time_format');
            $rows = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}followup_email_excludes ORDER BY date_added DESC");

            if ( empty($rows) ):
            ?>
            <tr>
                <td colspan="4" align="center"><?php _e('No rows found', 'followup_emails'); ?></td>
            </tr>
            <?php else:
                foreach ($rows as $row):
            ?>
            <tr>
                <th class="check-column"><input type="checkbox" id="cb-select-<?php echo esc_attr($row->id); ?>"  name="email[]" value="<?php echo esc_attr($row->id); ?>" /></th>
                <td><?php echo esc_html($row->email); ?></td>
                <td><?php echo date( $date_format, strtotime($row->date_added) ); ?></td>
                <td></td>
            </tr>
            <?php
                endforeach;
            endif;
            ?>
            </tbody>
        </table>

        <div class="tablenav bottom">
            <div class="alignleft actions bulkactions">
                <select name="action2">
                    <option value="restore">Remove from Opt-Out List</option>
                </select>
                <input type="submit" name="button_restore" id="doaction2" class="button action" value="Apply">
            </div>
        </div>

        <input type="hidden" name="action" value="fue_optout_manage" />
    </form>
</div>