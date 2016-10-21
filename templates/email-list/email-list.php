<div class="wrap">
    <div class="icon32"><img src="<?php echo FUE_TEMPLATES_URL .'/images/send_mail.png'; ?>" /></div>

    <h2>
        <?php _e('Follow-Up Emails', 'follow_up_emails'); ?>
        <a class="add-new-h2" href="post-new.php?post_type=follow_up_email"><?php _e('Create New Follow-Up', 'follow_up_emails'); ?></a>
    </h2>

    <?php include FUE_TEMPLATES_DIR .'/email-list/notifications.php'; ?>

    <style type="text/css">
        @media screen and (max-width: 782px) {
            th.column-priority, td.column-priority {
                display: none !important;
            }
        }
        span.priority {
            display: inline;
            padding: 1px 7px;
            background: #EAF2FA;
            border-radius: 10px;
            border: 1px solid #ddd;
        }

        .fue_table_footer {
            position: relative;
            overflow: hidden;
            padding: 8px;
            background: #EAF2FA;
            border: #c7d7e2 solid 1px;
            border-top:0 none;
        }

        .fue_table_footer .order_message {
            background: url(<?php echo FUE_TEMPLATES_URL; ?>/images/drag_and_drop_to_reorder.png);
            width: 161px;
            height: 23px;
            float: left;
            margin-left: 20px;
        }

        .ui-sortable tr {
            cursor: move;
        }

        table.fue th, table.fue td {
            overflow: visible !important;
        }

        div.row-actions span.edit.edit-control {
            display: inline-block;
            position: relative;
        }

        ul.fue-edit-action {
            display: none;
            position: absolute;
            top: 5px;
            left: 0;
            z-index: 100;
            width: 200px;
            padding: 0;
            border: 1px solid #DADADA;
            background: #FFF;
        }

        ul.fue-edit-action li {
            display: block;
            margin: 0;
        }

        ul.fue-edit-action li:hover {
            background: #E5EEF9;
        }

        ul.fue-edit-action li a {
            padding: 5px 10px !important;
            display: block;
        }

        div.row-actions span.edit.edit-control:hover {
            position: relative;
            overflow: visible;
        }

        div.row-actions span.edit.edit-control:hover ul.fue-edit-action {
            display: block;
        }
    </style>

    <?php
    include FUE_TEMPLATES_DIR .'/email-list/campaign-menu.php';

    if ( !empty($_GET['campaign']) ):
        include FUE_TEMPLATES_DIR .'/email-list/campaign-list.php';
    else:
    ?>
        <form action="admin-post.php" method="post" id="update_priorities">

            <div class="subsubsub_section">

                <?php include FUE_TEMPLATES_DIR .'/email-list/menu.php'; ?>

                <?php
                foreach ( $types as $key => $type ) {
                    $emails             = fue_get_emails( $type->id, array('fue-active', 'fue-inactive') );
                    $archived_emails    = fue_get_emails( $type->id, 'fue-archived' );

                    $bcc    = isset($bccs[$type->id]) ? $bccs[$type->id] : '';
                    $from   = isset($from_addresses[$type->id]) ? $from_addresses[$type->id] : '';
                    $from_name = isset($from_names[$type->id]) ? $from_names[$type->id] : '';

                    if ( !empty( $type->list_template ) && is_readable( $type->list_template ) ) {
                        include $type->list_template;
                    } else {
                        include FUE_TEMPLATES_DIR .'/email-list/simple-list.php';
                    }

                }

                do_action('fue_email_types_section');
                ?>
            </div>

            <p class="submit">
                <input type="hidden" name="action" value="fue_followup_save_list" />
                <input type="submit" name="update_priorities" value="<?php _e('Update Priorities', 'follow_up_emails'); ?>" class="button-primary" />
            </p>
        </form>
    <?php
    endif;
    ?>

</div>
