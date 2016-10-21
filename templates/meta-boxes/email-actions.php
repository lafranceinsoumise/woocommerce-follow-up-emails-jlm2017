<div id="email-actions-box" class="submitbox">

    <div id="minor-publishing">

        <div style="display:none;">
            <p class="submit"><input type="submit" value="Save" class="button" id="save" name="save"></p></div>

        <div id="minor-publishing-actions">
            <div id="save-action">
                <input type="submit" class="button" value="Save Draft" id="save-post" name="save">
                <span class="spinner"></span>
            </div>
            <div class="clear"></div>
        </div><!-- #minor-publishing-actions -->

        <div id="misc-publishing-actions">

            <div class="misc-pub-section misc-pub-email-status"><label for="post_status"><?php _e('Status:') ?></label>
            <span id="post-status-display">
            <?php
            switch ( $email->status ) {
                case FUE_Email::STATUS_ACTIVE:
                    _e('Active', 'follow_up_emails');
                    break;
                case FUE_Email::STATUS_INACTIVE:
                case 'draft':
                case 'auto-draft':
                    _e('Inactive', 'follow_up_emails');
                    break;
                case FUE_Email::STATUS_ARCHIVED:
                    _e('Archived');
                    break;
            }
            ?>
            </span>

                <a href="#post_status" class="edit-post-status hide-if-no-js"><span aria-hidden="true"><?php _e( 'Edit' ); ?></span> <span class="screen-reader-text"><?php _e( 'Edit status' ); ?></span></a>

                <div id="post-status-select" class="hide-if-js">
                    <input type="hidden" name="hidden_post_status" id="hidden_post_status" value="<?php echo esc_attr( ('auto-draft' == $email->status ) ? 'draft' : $email->status); ?>" />
                    <select name='post_status' id='post_status'>
                        <option<?php selected( $email->status, FUE_Email::STATUS_ACTIVE ); ?> value='<?php esc_attr_e(FUE_Email::STATUS_ACTIVE); ?>'><?php _e('Active', 'follow_up_emails') ?></option>
                        <option<?php selected( $email->status, FUE_Email::STATUS_INACTIVE ); ?> value='<?php esc_attr_e(FUE_Email::STATUS_INACTIVE); ?>'><?php _e('Inactive', 'follow_up_emails') ?></option>
                        <option<?php selected( $email->status, FUE_Email::STATUS_ARCHIVED ); ?> value='<?php esc_attr_e(FUE_Email::STATUS_ARCHIVED); ?>'><?php _e('Archived', 'follow_up_emails') ?></option>
                        <?php if ( 'auto-draft' == $post->post_status ) : ?>
                            <option<?php selected( $post->post_status, 'auto-draft' ); ?> value='draft'><?php _e('Draft') ?></option>
                        <?php endif; ?>
                    </select>
                    <a href="#post_status" class="save-post-status hide-if-no-js button"><?php _e('OK'); ?></a>
                    <a href="#post_status" class="cancel-post-status hide-if-no-js button-cancel"><?php _e('Cancel'); ?></a>
                </div>

            </div><!-- .misc-pub-section -->
        </div>
        <div class="clear"></div>
    </div>

    <div id="publishing-actions">
        <div id="fue-delete-action">
            <a class="submitdelete deletion" onclick="return confirm('Really delete this email?');" href="<?php echo wp_nonce_url('admin-post.php?action=fue_followup_delete&id='. $post->ID, 'delete-email'); ?>"><?php _e('Delete', 'follow_up_email'); ?></a>
        </div>

        <div id="publishing-action">
            <span class="spinner"></span>
            <input type="submit" class="button save_email button-primary" name="save" value="<?php _e( 'Save Email', 'follow-up-emails' ); ?>" />
        </div>
        <div class="clear"></div>
    </div>
</div>