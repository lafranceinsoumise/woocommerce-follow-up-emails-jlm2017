<div class="subscribers-container">
    <?php if (isset($_GET['opt-out-restored']) && $_GET['opt-out-restored'] > 0): ?>
        <div id="message" class="updated"><p><?php printf( _n('1 email has been restored', '%d emails have been restored', intval($_GET['opt-out-restored']), 'follow_up_emails'), intval($_GET['opt-out-restored'])); ?></p></div>
    <?php endif; ?>

    <?php if (isset($_GET['opt-out-added'])): ?>
        <div id="message" class="updated"><p><?php printf(__('<em>%s</em> has been added to the opt-out list', 'follow_up_emails'), strip_tags($_GET['opt-out-added'])); ?></p></div>
    <?php endif; ?>

    <?php if (isset($_GET['opt-out-error']) ): ?>
        <div id="message" class="error"><p><?php echo esc_html($_GET['opt-out-error']); ?></p></div>
    <?php endif; ?>

    <div class="subscribers-col1">
        <?php
        $list_table = new FUE_Subscribers_Optouts_List_Table();
        $list_table->prepare_items();
        $list_table->display();
        ?>
    </div>
    <div class="subscribers-col2">
        <form action="admin-post.php" method="post">
            <input type="hidden" name="action" value="fue_optout_manage" />

            <div class="meta-box no-padding">
                <h3 class="handle"><?php _e('Add Email to Opt-out', 'follow_up_emails'); ?></h3>
                <div class="inside">
                    <p>
                        <input type="email" name="email" placeholder="Email address" />
                    </p>

                    <div class="meta-box-actions">
                        <input type="submit" name="button_add" class="button button-primary" value="<?php _e('Add Email', 'follow_up_emails'); ?>">
                        <div class="clear"></div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>