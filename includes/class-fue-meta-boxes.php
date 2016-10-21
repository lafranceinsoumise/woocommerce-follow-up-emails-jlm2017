<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class FUE_Meta_Boxes {

    /** @var FUE_Email */
    private static $email = null;

    /**
     * Class constructor
     */
    public function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this, 'email_form_scripts' ), 12 );

        add_filter( 'enter_title_here', array( $this, 'form_title_placeholder' ), 100, 2 );
        add_action( 'edit_form_after_title', array( $this, 'add_subject_field'), 0 );
        add_action( 'edit_form_after_title', array( $this, 'add_dummy_content'), 100 );

        add_filter( 'tiny_mce_before_init', array( $this, 'attach_editor_listeners' ) );

        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 30 );
        add_action( 'add_meta_boxes', array( $this, 'remove_meta_boxes' ), 100 );
        add_action( 'save_post', array( $this, 'save_email' ), 1, 2 );

        // inject template sections into the variables list
        add_action( 'fue_email_variables_list', array( $this, 'add_template_sections_to_variables' ) );
    }

    /**
     * Load all scripts and styles for the email form
     */
    public function email_form_scripts() {
        $screen = get_current_screen();

        if ( $screen->id == 'follow_up_email' ) {
            // disable autosave
            wp_dequeue_script( 'autosave' );

            wp_enqueue_style( 'fue_email_form', FUE_TEMPLATES_URL .'/email-form.css' );
            wp_enqueue_script( 'fue-form', plugins_url( 'templates/js/email-form.js', FUE_FILE ), array('jquery', 'jquery-tiptip'), FUE_VERSION );

            // Select2
            wp_enqueue_script( 'select2' );
            wp_enqueue_style( 'select2', '//cdnjs.cloudflare.com/ajax/libs/select2/3.5.2/select2.css' );

            wp_enqueue_script('farbtastic');
            wp_enqueue_script( 'jquery-ui-sortable' );
            wp_enqueue_script( 'jquery-ui-core', null, array('jquery') );
            wp_enqueue_script( 'jquery-ui-datepicker', null, array('jquery-ui-core') );
            wp_enqueue_script( 'jquery-ui-autocomplete', null, array('jquery-ui-core') );
            
            wp_enqueue_style( 'jquery-ui-css', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/themes/base/jquery-ui.css' );

            do_action( 'fue_email_form_scripts' );
        }

    }

    /**
     * Change the default placeholder text on the Title field
     * @param string $title
     * @param WP_Post $post
     *
     * @return string
     */
    public function form_title_placeholder( $title, $post ) {
        if ( $post->post_type == 'follow_up_email' ) {
            $title = __('Follow-up name', 'follow_up_emails');
        }

        return $title;
    }

    /**
     * Insert the Subject field after the Title
     */
    public function add_subject_field() {
        global $post;

        if ( $post->post_type != 'follow_up_email' ) {
            return;
        }
        ?>
        <div id="subjectdiv">
            <div id="subjectwrap">
                <label for="post_excerpt" class="fue-label"><?php _e('Email subject line', 'follow_up_emails'); ?></label>
                <label class="screen-reader-text" id="subject-prompt-text" for="subject"><?php echo apply_filters( 'enter_subject_here', __( 'Your subject line' ), $post ); ?></label>
                <input type="text" placeholder="<?php echo apply_filters( 'enter_subject_here', __( 'Your subject line' ), $post ); ?>" name="post_excerpt" size="30" value="<?php echo esc_attr( htmlspecialchars( $post->post_excerpt ) ); ?>" id="post_excerpt" autocomplete="off" tabindex="0" />
            </div>
        </div>

        <?php
    }

    /**
     * Add placeholder text to the rich-text editor
     * @param WP_Post $post
     */
    public function add_dummy_content( $post ) {
        if ( $post->post_type != 'follow_up_email' || !empty( $post->post_content ) ) {
            return;
        }

        ?>
        <div id="email_content_dummy">
            <?php _e("<p>Create the content of your follow-up by adding it here - just like any other WordPress post or page. You can just about anything with this content. The editor accepts full HTML - just like posts and pages - or you can keep the follow-up plain text. It is up to you.</p><p>Do not wish to use the default WooCommerce templates for every email? You can choose to build a completely custom template for each email you create - if you choose. You can download two examples by clicking the Templates link to your left, and you can edit the template from that view as well.</p><p>If you wish to include variables that will add dynamic content from the list to the right, you can do that. For example, if you want your customer's name in the email, simply add {customer_name} where you want in your content, and when the email is sent that variable will be replaced with the customer's name.</p>", 'follow_up_email'); ?>
        </div>
        <br class="clear" />
        <?php
    }

    /**
     * Attach JS listeners to the WP Editor to toggle the display of the editor helper text
     * @param array $init
     * @return array
     */
    public function attach_editor_listeners( $init ) {
        $init['setup'] = 'function(ed){
            ed.on("blur", function(e){
                if ( "" == ed.getContent() )
                    jQuery("#email_content_dummy").removeClass("screen-reader-text");
            });

            ed.on("focus", function(e){
                jQuery("#email_content_dummy").addClass("screen-reader-text");
            });

            ed.on("keydown", function(ed){
                jQuery("#email_content_dummy").addClass("screen-reader-text");
            });
        }';
        return $init;
    }

    /**
     * Add a delete link to the email form to replace the core's Trash link
     */
    public function add_delete_email_link() {
        global $post;

        if ( $post->post_type != 'follow_up_email' ) {
            return;
        }
        ?>
        <div id="fue-delete-action">
        <a class="submitdelete deletion" onclick="return confirm('Really delete this email?');" href="<?php echo wp_nonce_url('admin-post.php?action=fue_followup_delete&id='. $post->ID, 'delete-email'); ?>"><?php _e('Delete', 'follow_up_email'); ?></a>
        </div>
        <?php
    }

    /**
     * Display the email status switcher
     */
    public function add_email_status_option() {
        global $post;

        if ( $post->post_type != 'follow_up_email' ) {
            return;
        }

        $email = self::get_email( $post );
        ?>
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
        <?php
    }

    /**
     * Add custom metaboxes
     */
    public function add_meta_boxes() {
        add_meta_box( 'fue-email-details', __( 'Follow-up Details', 'follow-up-emails' ), 'FUE_Meta_Boxes::email_details_view', 'follow_up_email', 'normal', 'high' );

        add_meta_box( 'fue-email-type', __( 'Follow-up Type', 'follow-up-emails' ), 'FUE_Meta_Boxes::email_type_view', 'follow_up_email', 'side', 'high' );
        add_meta_box( 'fue-email-template', __( 'Template', 'follow-up-emails' ), 'FUE_Meta_Boxes::email_template_view', 'follow_up_email', 'side', 'high' );
        add_meta_box( 'fue-email-variables', __( 'Variables', 'follow-up-emails' ), 'FUE_Meta_Boxes::email_variables_view', 'follow_up_email', 'side', 'high' );
        add_meta_box( 'fue-email-test', __( 'Send Test', 'follow-up-emails' ), 'FUE_Meta_Boxes::email_test_view', 'follow_up_email', 'side', 'low' );
        add_meta_box( 'fue-email-actions', __( 'Follow-up Actions', 'follow-up-emails' ), 'FUE_Meta_Boxes::email_actions', 'follow_up_email', 'side', 'high' );

        remove_meta_box( 'slugdiv', 'follow_up_email', 'normal' );
    }

    /**
     * Remove metaboxes that do not belong to FUE
     */
    public function remove_meta_boxes() {
        global $wp_meta_boxes;

        $screen     = get_current_screen();
        $exceptions = array('submitdiv', 'tagsdiv-follow_up_email_campaign');

        if ( $screen->id != 'follow_up_emails' ) {
            return;
        }

        $metaboxes = $wp_meta_boxes['follow_up_email'];

        foreach ( $metaboxes as $context => $priorities ) {

            foreach ( $priorities as $priority => $boxes ) {

                foreach ( $boxes as $id => $box ) {

                    // if $id is not in the exceptions list
                    // and it's not prefixed with fue-, unregister it
                    if ( in_array( $id, $exceptions ) ) {
                        continue;
                    }

                    if ( strpos( $id, 'fue-' ) === 0 ) {
                        continue;
                    }

                    unset( $wp_meta_boxes['follow_up_email'][ $context ][ $priority ][ $id ] );

                }
            }

        }
    }

    public static function get_email( $post ) {
        if ( is_null( self::$email ) || self::$email->id != $post->ID ) {
            self::$email = new FUE_Email( $post->ID );
        }

        return self::$email;
    }

    /**
     * Email Details metabox
     * @param WP_Post $post
     */
    public static function email_details_view( $post ) {
        $email = self::get_email( $post );
        include FUE_TEMPLATES_DIR .'/meta-boxes/email-details.php';
    }

    /**
     * Email Type metabox
     * @param WP_Post $post
     */
    public static function email_type_view( $post ) {
        $email = self::get_email( $post );
        include FUE_TEMPLATES_DIR .'/meta-boxes/email-type.php';
    }

    /**
     * Email Template metabox
     * @param WP_Post $post
     */
    public static function email_template_view( $post ) {
        $email = self::get_email( $post );
        include FUE_TEMPLATES_DIR .'/meta-boxes/email-template.php';
    }

    /**
     * Save email metabox
     * @param WP_Post $post
     */
    public static function email_actions( $post ) {
        if ( $post->post_type != 'follow_up_email' ) {
            return;
        }
        $email = self::get_email( $post );
        include FUE_TEMPLATES_DIR .'/meta-boxes/email-actions.php';
    }

    /**
     * List of variables metabox
     * @param WP_Post $post
     */
    public static function email_variables_view( $post ) {
        $email = self::get_email( $post );
        include FUE_TEMPLATES_DIR .'/meta-boxes/email-variables.php';
    }

    /**
     * Test Email view
     * @param WP_Post $post
     */
    public static function email_test_view( $post ) {
        $email = self::get_email( $post );
        ?>
        <p>
            <input type="text" id="email" placeholder="Email Address" value="" class="test-email-field" />
        </p>

        <?php do_action('fue_test_email_fields', $email); ?>

        <p>
            <input type="button" id="test_send" value="<?php _e('Send Test', 'follow_up_emails'); ?>" class="button" />
            &nbsp; <a href="<?php echo $email->get_preview_url(); ?>" target="_blank"><?php _e('Preview in browser', 'follow_up_emails'); ?></a>
        </p>

        <?php
    }

    /**
     * Process the data posted when saving the email
     */
    public function save_email( $post_id, $post ) {
        // $post_id and $post are required
        if ( empty( $post_id ) || empty( $post ) ) {
            return;
        }

        // Dont' save meta boxes for revisions or autosaves
        if ( defined( 'DOING_AUTOSAVE' ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
            return;
        }

        // Check the post being saved == the $post_id to prevent triggering this call for other save_post events
        if ( empty( $_POST['post_ID'] ) || $_POST['post_ID'] != $post_id ) {
            return;
        }

        if ( $post->post_type != 'follow_up_email' ) {
            return;
        }

        $status = $_POST['post_status'];

        if ( $status == 'draft' ) {
            $status = FUE_Email::STATUS_INACTIVE;
        } elseif ( $status == 'publish' ) {
            $status = FUE_Email::STATUS_ACTIVE;
        }

        $conditions = array();

        if ( !empty( $_POST['conditions'] ) ) {
            foreach ( $_POST['conditions'] as $idx => $condition ) {

                if ( $idx === '_idx_' ) {
                    continue;
                }

                $conditions[ $idx ] = $condition;

            }
        }

        $email = new FUE_Email( $post_id );


        $data = apply_filters( 'fue_save_email_data', array(
            'type'              => $_POST['email_type'],
            'status'            => $status,
            'ID'                => $post_id,
            'template'          => $_POST['template'],
            'meta'              => $_POST['meta'],
            'product_id'        => (!empty($_POST['product_id'])) ? $_POST['product_id'] : 0,
            'category_id'       => (!empty($_POST['category_id'])) ? $_POST['category_id'] : 0,
            'interval_num'      => (!empty($_POST['interval'])) ? $_POST['interval'] : 1,
            'interval_duration' => (!empty($_POST['interval_duration'])) ? $_POST['interval_duration'] : '',
            'interval_type'     => (!empty($_POST['interval_type'])) ? $_POST['interval_type'] : '',
            'send_date'         => (!empty($_POST['send_date'])) ? $_POST['send_date'] : '',
            'send_date_hour'    => (!empty($_POST['send_date_hour'])) ? $_POST['send_date_hour'] : '',
            'send_date_minute'  => (isset($_POST['send_date_minute'])) ? $_POST['send_date_minute'] : '',
            'always_send'       => (!empty($_POST['always_send'])) ? $_POST['always_send'] : '',
            'tracking_on'       => (!empty($_POST['tracking_on'])) ? $_POST['tracking_on'] : 0,
            'tracking'          => (!empty($_POST['tracking'])) ? $_POST['tracking'] : '',
            'coupon_id'         => (!empty($_POST['coupon_id'])) ? $_POST['coupon_id'] : '',
            'conditions'        => (!empty($conditions)) ? $conditions : '',
        ), $post_id, $post );

        // unhook this function so it doesn't loop infinitely
        remove_action( 'save_post', array( $this, 'save_email' ), 1 );

        fue_save_email( $data );

        add_action( 'save_post', array( $this, 'save_email' ), 1, 2 );

        do_action( 'fue_after_save_email', $data );

    }

    /**
     * Add template sections as email variables
     *
     * @param FUE_Email $email
     */
    public function add_template_sections_to_variables( $email ) {
        if ( empty( $email->template ) ) {
            return;
        }

        $tpl = new FUE_Email_Template( $email->template );

        foreach ( $tpl->get_sections() as $section ) {
            ?>
            <li class="var hideable var_template_section"><strong>{section:<?php echo $section; ?>}...{/section}</strong> <img class="help_tip" title="<?php _e('Email template section', 'follow_up_emails'); ?>" src="<?php echo FUE_TEMPLATES_URL; ?>/images/help.png" width="16" height="16" /></li>
            <?php
        }
    }

}

new FUE_Meta_Boxes();