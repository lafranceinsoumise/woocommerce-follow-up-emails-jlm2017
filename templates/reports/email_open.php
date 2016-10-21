<h3><?php printf(__('Email Opened Report for %s', 'wc_folloup_emails'), $name); ?></h3>
<table class="wp-list-table widefat fixed posts">
    <thead>
    <tr>
        <th scope="col" id="user_email" class="manage-column column-user_email" style=""><?php _e('Email', 'follow_up_emails'); ?></th>
        <th scope="col" id="date_sent" class="manage-column column-date_sent" style=""><?php _e('Date Opened', 'follow_up_emails'); ?></th>
    </tr>
    </thead>
    <tbody id="the_list">
    <?php
    if (empty($reports)):
    ?>
        <tr scope="row">
            <th colspan="2"><?php _e('No reports available', 'follow_up_emails'); ?></th>
        </tr>
    <?php
    else:
        foreach ($reports as $report):
    ?>
        <tr scope="row">
            <td><?php echo esc_html($report->user_email); ?></td>
            <td><?php echo date( get_option('date_format') .' '. get_option('time_format') , strtotime($report->date_added)); ?></td>
        </tr>
    <?php
        endforeach;
    endif;
    ?>
    </tbody>
</table>