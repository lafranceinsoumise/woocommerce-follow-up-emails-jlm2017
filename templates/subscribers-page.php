<style>
    a.remove-from-list.dashicons {
        color: #999;
        font-size: 14px;
        line-height: 20px;
    }
    div.subscribers-container {
        margin-right: 300px;
    }
    div.subscribers-col1 {
        float: left;
        min-width: 463px;
        width: 100%;
        position: relative;
    }
    div.subscribers-col2 {
        float: right;
        margin-right: -300px;
        width: 280px;
        padding-top: 10px;
    }
    div.meta-box {
        background: #fff none repeat scroll 0 0;
        border: 1px solid #e5e5e5;
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.04);
        min-width: 255px;
        margin-bottom: 20px;
    }
    .subscribers-container h3 {
        font-size: 14px;
        line-height: 1.4;
        margin: 0;
        padding: 8px 12px;
    }
    form#subscribers_list_form, form#subscribers_optout_form {
        margin-top: 10px;
    }
    .meta-box .handle {
        border-bottom: 1px solid #eee;
    }
    .meta-box.no-padding .inside {
        padding: 0;
        margin: 11px 0;
        position: relative;
    }
    .meta-box .inside {
        font-size: 13px;
        line-height: 1.4em;
        padding: 0 12px 12px;
    }
    .meta-box .inside p {
        margin: 15px 10px;
    }
    .meta-box .meta-box-actions {
        background: #f5f5f5 none repeat scroll 0 0;
        border-top: 1px solid #ddd;
        clear: both;
        padding: 10px;
        margin: 10px 0 -11px 0;
    }
    .meta-box .meta-box-actions input, .meta-box .meta-box-actions .action {
        float: right;
        line-height: 23px;
        text-align: right;
    }
</style>
<div class="wrap">
    <h2>
        <?php _e('Manage Lists & Subscribers', 'follow_up_emails'); ?>
    </h2>

    <?php if (isset($_GET['deleted']) && $_GET['deleted'] > 0): ?>
        <div id="message" class="updated"><p><?php printf( _n('1 email has been deleted', '%d emails have been deleted', intval($_GET['deleted']), 'follow_up_emails'), intval($_GET['deleted'])); ?></p></div>
    <?php endif; ?>

    <?php if (isset($_GET['added'])): ?>
        <div id="message" class="updated"><p><?php printf(_n('1 subscriber has been added', '%d subscribers have been added', intval($_GET['added']), 'follow_up_emails'), intval($_GET['added'])); ?></p></div>
    <?php endif; ?>

    <?php if (isset($_GET['moved'])): ?>
        <div id="message" class="updated"><p><?php printf(_n('1 subscriber has been moved', '%d subscribers have been moved', intval($_GET['moved']), 'follow_up_emails'), intval($_GET['moved'])); ?></p></div>
    <?php endif; ?>

    <?php if (isset($_GET['added_subscriber'])): ?>
        <div id="message" class="updated"><p><?php printf( __('%s has been added', 'follow_up_emails'), wp_kses_post( sanitize_email( $_GET['added_subscriber'] ) ) ); ?></p></div>
    <?php endif; ?>

    <?php if (isset($_GET['added_list'])): ?>
        <div id="message" class="updated"><p><?php printf( __('List "%s" has been added', 'follow_up_emails'), wp_kses_post( $_GET['added_list'] ) ); ?></p></div>
    <?php endif; ?>

    <?php if (isset($_GET['imported'])): ?>
        <div id="message" class="updated"><p><?php printf( _n('1 has been added', '%d emails have been added', intval($_GET['imported']), 'follow_up_emails'), intval($_GET['imported'])); ?></p></div>
    <?php endif; ?>

    <?php if (isset($_GET['error']) ): ?>
        <div id="message" class="error"><p><?php echo esc_html($_GET['error']); ?></p></div>
    <?php endif; ?>

    <h2 class="nav-tab-wrapper woo-nav-tab-wrapper subscribers-overview-tabs">
        <a class="nav-tab <?php if ( empty($_GET['view']) ) echo 'nav-tab-active'; ?>" href="<?php echo admin_url('admin.php?page=followup-emails-subscribers'); ?>"><?php _e('Subscribers', 'follow_up_emails'); ?></a>
        <a class="nav-tab <?php if ( !empty($_GET['view']) && $_GET['view'] == 'lists') echo 'nav-tab-active'; ?>" href="<?php echo admin_url('admin.php?page=followup-emails-subscribers&view=lists'); ?>"><?php _e('Lists', 'follow_up_emails'); ?></a>
        <a class="nav-tab <?php if ( !empty($_GET['view']) && $_GET['view'] == 'opt-outs') echo 'nav-tab-active'; ?>" href="<?php echo admin_url('admin.php?page=followup-emails-subscribers&view=opt-outs'); ?>"><?php _e('Opt-outs', 'follow_up_emails'); ?></a>
    </h2>
    <?php
    if ( $view == 'subscribers' ) {
        include FUE_TEMPLATES_DIR .'/subscribers-table.php';
    } elseif ( $view == 'lists' ) {
        include FUE_TEMPLATES_DIR .'/subscribers-lists.php';
    } elseif ( $view == 'opt-outs' ) {
        include FUE_TEMPLATES_DIR .'/subscribers-opt-outs.php';
    } else {
        echo '<div class="error"><p>'. __('The page could not be found', 'follow_up_emails') .'</p></div>';
    }
    ?>
</div>