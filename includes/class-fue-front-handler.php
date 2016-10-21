<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Handle frontend actions
 */
class FUE_Front_Handler {

    public static function init() {
        // catch unsubscribe request
        add_action( 'wp', 'FUE_Front_Handler::process_unsubscribe_request' );
        add_action( 'template_redirect', 'FUE_Front_Handler::process_optout_request' );

        // fue subscriptions
        add_action( 'wp', 'FUE_Front_Handler::process_subscription_request' );

        // email preview
        add_action( 'template_redirect', 'FUE_Front_Handler::preview_email' );

        // web version
        add_action( 'template_redirect', 'FUE_Front_Handler::web_version' );

        add_action( 'wp_enqueue_scripts', 'FUE_Front_Handler::account_subscription_script' );
    }

    /**
     * Process unsubscribe request. Add the submitted email address to the Excluded Emails list
     */
    public static function process_unsubscribe_request() {
        global $wpdb;

        if (isset($_POST['fue_action']) && $_POST['fue_action'] == 'fue_unsubscribe') {
            $email      = str_replace( ' ', '+', $_POST['fue_email'] );
            $email_id   = $_POST['fue_eid'];
            $error      = '';

            if ( empty( $email ) || !is_email( $email ) ) {
                $error = urlencode( __('Please enter a valid email address', 'follow_up_emails') );
            }

            $order_id    = (!empty( $_POST['unsubscribe_order_id'] ) ) ? absint( $_POST['unsubscribe_order_id'] ) : 0;
            $unsubscribe = (!empty( $_POST['unsubscribe_all'] ) && $_POST['unsubscribe_all'] == 'yes' ) ? true : false;

            if ( fue_is_email_excluded( $email, 0, $order_id ) ) {
                if ( $order_id > 0 ) {
                    $error = sprintf( __('The email (%s) is already unsubscribed from receiving emails regarding Order %d', 'follow_up_emails'), $email, $order_id );
                } else {
                    $error = sprintf( __('The email (%s) is already unsubscribed from receiving emails', 'follow_up_emails'), $email );
                }
            }

            if ( !empty( $error ) ) {
                $url = add_query_arg( array(
                    'fueid' => $_POST['fue_eid'],
                    'qid'   => (!empty($_POST['fue_qid'])) ? $_POST['fue_qid'] : '',
                    'error' => urlencode( $error )
                ), fue_get_unsubscribe_url());

                wp_redirect( $url );
                exit;
            }

            if ( $unsubscribe ) {
                fue_exclude_email_address( $email, $email_id, 0 );

                if ( isset($_GET['fue']) ) {
                    do_action('fue_user_unsubscribed', $_GET['fue']);
                }

                // remove the email from the subscriber table if it exists
                fue_remove_subscriber( $email );

            } elseif ( $order_id > 0 ) {
                fue_exclude_email_address( $email, $email_id, $order_id );

                $wpdb->query( $wpdb->prepare("DELETE FROM {$wpdb->prefix}followup_email_orders WHERE user_email = %s AND order_id = %d AND is_sent = 0", $email, $order_id) );
            }

            wp_redirect( add_query_arg( 'fue_unsubscribed', 1, Follow_Up_Emails::get_account_url() ) );
            exit;

        } elseif (isset($_GET['fue_unsubscribed'])) {
            Follow_Up_Emails::show_message( __('Thank you. Your email settings have been saved.', 'follow_up_emails') );
        }
    }

    /**
     * Handle opt-in and opt-out requests
     */
    public static function process_optout_request() {

        if (isset($_POST['fue_action']) && $_POST['fue_action'] == 'fue_save_myaccount') {
            $opted_out  = (isset($_POST['fue_opt_out']) && $_POST['fue_opt_out'] == 1) ? true : false;
            $user       = wp_get_current_user();

            if ( $opted_out ) {
                // unsubscribe this user using his/her email
                fue_add_user_opt_out( $user->ID );
            } else {
                fue_remove_user_opt_out( $user->ID );
            }

            wp_redirect( add_query_arg('fue_updated', 1, Follow_Up_Emails::get_account_url()) );
            exit;
        } elseif (isset($_GET['fue_updated'])) {
            Follow_Up_Emails::show_message(__('Account updated', 'follow_up_emails'));
        }
    }

    /**
     * Handle newsletter subscription requests
     */
    public static function process_subscription_request() {
        if ( empty( $_POST['fue_action'] ) || $_POST['fue_action'] != 'subscribe' ) {
            return;
        }

        if (
            ! isset( $_POST['_wpnonce'] )
            || ! wp_verify_nonce( $_POST['_wpnonce'], 'fue_subscribe' )
        ) {
            wp_die('Sorry, your browser submitted an invalid request. Please try again.');
        }

        $back   = $_POST['_wp_http_referer'];
        $email  = !empty( $_POST['fue_subscriber_email'] ) ? $_POST['fue_subscriber_email'] : '';
        $list   = !empty( $_POST['fue_email_list'] ) ? $_POST['fue_email_list'] : '';
        $id     = fue_add_subscriber( $email, $list );



        if ( is_wp_error( $id ) ) {
            $args = array(
                'error' => urlencode( $id->get_error_message() ),
                'email' => urlencode( $email )
            );
        } else {
            $args = array(
                'error'             => '',
                'fue_subscribed'    => 'yes'
            );
        }

        wp_redirect( add_query_arg( $args, $back ) );
        exit;
    }

    /**
     * Show email preview
     */
    public static function preview_email() {
        if ( empty( $_GET['fue-preview'] ) ) {
            return;
        }

        $email_id = absint( $_GET['email'] );
        $email  = new FUE_Email( $email_id );

        if ( empty( $_GET['key'] ) || $_GET['key'] != md5($email->post->post_title) ) {
            wp_die('Sorry, your browser submitted an invalid request. Please try again.');
        }

        $data = array(
            'test'          => true,
            'username'      => '75nineteen',
            'first_name'    => '75nineteen',
            'last_name'     => 'Media',
            'cname'         => '75nineteen Media',
            'user_id'       => '0',
            'order_id'      => '',
            'product_id'    => '',
            'email_to'      => '',
            'tracking_code' => '',
            'store_url'     => home_url(),
            'store_url_secure' => home_url( null, 'https' ),
            'store_name'    => get_bloginfo('name'),
            'unsubscribe'   => fue_get_unsubscribe_url(),
            'subject'       => $email->subject,
            'message'       => $email->message,
            'meta'          => array()
        );

        $html = Follow_Up_Emails::instance()->mailer->get_email_preview_html( $data, $email );

        die($html);
    }

    /**
     * Display the web version of an email
     */
    public static function web_version() {
        if ( empty( $_GET['fue-web-version'] ) ) {
            return;
        }

        $id     = absint( $_GET['email-id'] );
        $key    = $_GET['key'];
        $item   = new FUE_Sending_Queue_Item( $id );

        if ( !$item->exists() || $item->is_sent != 1 ) {
            wp_die( __('Email could not be found', 'follow_up_emails') );
        }

        $item_key = md5( $item->user_email .'.'. $item->email_id .'.'. $item->send_on );

        if ( $item_key != $key ) {
            wp_die( __('Invalid request. Please try again.', 'follow_up_emails') );
        }

        // track pageview
        $tracker = new FUE_Report_Email_Tracking( Follow_Up_Emails::instance() );
        $tracker->log_event( 'web_open', array(
            'event'     => 'web_open',
            'queue_id'  => $id,
            'email_id'  => $item->email_id,
            'user_id'   => $item->user_id,
            'user_email'=> $item->user_email
        ) );

        $html = Follow_Up_Emails::instance()->mailer->get_email_web_version( $item );
        $html .= '<style>a.webversion {display:none;}</style>';
        echo $html;
        exit;
    }

    /**
     * Register script that handles the updating of email subscriptions from the account page
     */
    public static function account_subscription_script() {
        wp_enqueue_script( 'fue-account-subscriptions', FUE_TEMPLATES_URL .'/js/fue-account-subscriptions.js', array('jquery'), FUE_VERSION );
        wp_localize_script( 'fue-account-subscriptions', 'FUE', array(
            'ajaxurl'       => admin_url('admin-ajax.php'),
            'ajax_loader'   => plugins_url() .'/woocommerce-follow-up-emails/templates/images/ajax-loader.gif'
        ));

        wp_enqueue_script( 'fue-front-script', FUE_TEMPLATES_URL .'/js/fue-front.js', array('jquery'), FUE_VERSION, true );
        wp_localize_script( 'fue-front-script', 'FUE_Front', array(
            'is_logged_in'  => is_user_logged_in(),
            'ajaxurl'       => admin_url('admin-ajax.php')
        ) );
    }

}

FUE_Front_Handler::init();