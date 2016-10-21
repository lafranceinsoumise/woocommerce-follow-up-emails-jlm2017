<?php

/**
 * Class FUE_Coupons
 *
 * FUE Coupons are basically templates that, when triggered, will create a
 * WC Coupon using the same settings as the template and a randomly generated
 * coupon code. The code generated is then injected into an email using a variable.
 *
 */
class FUE_Coupons{

    /**
     * Hook in the methods
     */
    public function __construct() {
        add_action('fue_menu', array($this, 'menu'), 20);

        // settings styles and scripts
        add_action('admin_enqueue_scripts', array($this, 'settings_scripts'));

        add_action( 'fue_settings_notification', array($this, 'print_notifications') );

        add_action( 'admin_post_fue_save_coupon', array($this, 'save_coupon_from_post') );

        add_filter( 'fue_email_pre_save', array($this, 'remove_coupon_data_from_email'), 10, 2 );
        add_action( 'fue_email_form_email_details', array( $this, 'email_form_coupons_panel' ) );

        add_action( 'fue_email_form_before_message', array($this, 'before_message') );

        add_action( 'fue_after_save_email', array($this, 'maybe_record_coupon'), 10, 2 );

        add_action( 'woocommerce_checkout_order_processed', array($this, 'order_processed'), 10, 2 );

        add_action( 'fue_before_variable_replacements', array($this, 'register_variable_replacements'), 10, 4 );

        // Email Cloned
        add_action( 'fue_email_cloned', array($this, 'apply_coupon_to_cloned_email'), 10, 2 );

        // Email Deleted
        add_action( 'fue_email_deleted', array($this, 'remove_coupon_from_deleted_email') );
        add_action( 'admin_post_fue_delete_coupon', array($this, 'delete_coupon') );
    }

    /**
     * Register the menu page
     */
    public function menu() {
        add_submenu_page( 'followup-emails', __('Coupons', 'follow_up_emails'), __('Coupons', 'follow_up_emails'), 'manage_follow_up_emails', 'followup-emails-coupons', 'FUE_Coupons::settings_main' );
    }

    /**
     * Load the necessary scripts and styles
     */
    public function settings_scripts() {

        if ( isset($_GET['page']) && $_GET['page'] == 'followup-emails-coupons') {
            wp_enqueue_script( 'select2' );
            wp_enqueue_style( 'select2' );

            wp_enqueue_script( 'woocommerce_admin' );
            wp_enqueue_script('farbtastic');
            wp_enqueue_script( 'jquery-ui-datepicker', null, array('jquery-ui-core') );
            wp_enqueue_script( 'jquery-tiptip' );

            wp_enqueue_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css' );
            wp_enqueue_style( 'jquery-ui-css', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/themes/base/jquery-ui.css' );

        }
    }

    /**
     * Save coupon from POST
     */
    public function save_coupon_from_post() {
        $data = self::get_coupon_args_from_post();

        fue_insert_coupon( $data );

        if ( !empty( $data['id'] ) ) {
            wp_redirect('admin.php?page=followup-emails-coupons&coupon_updated=1');
        } else {
            wp_redirect('admin.php?page=followup-emails-coupons&coupon_created=1');
        }

        exit;

    }

    /**
     * Delete the selected coupon from the DB
     */
    public function delete_coupon() {
        fue_delete_coupon( $_GET['id'] );

        wp_redirect('admin.php?page=followup-emails-coupons&coupon_deleted=1');
        exit;
    }

    /**
     * Register the coupon_code variable to be replaced
     *
     * @param FUE_Sending_Email_Variables   $var
     * @param array                 $email_data
     * @param FUE_Email             $email
     * @param object                $queue_item
     */
    public function register_variable_replacements( $var, $email_data, $email, $queue_item ) {
        $variables = array(
            'coupon_code'       => '',
            'coupon_code_used'  => '',
            'coupon_amount'     => ''
        );

        // use test data if the test flag is set
        if ( isset( $email_data['test'] ) && $email_data['test'] ) {
            $variables = $this->add_test_variable_replacements( $variables, $email_data, $email );
        } else {
            $variables = $this->add_variable_replacements( $variables, $email_data, $queue_item, $email );
            $variables = $this->add_used_coupon_replacements( $variables, $email_data, $queue_item, $email );
        }

        $var->register( $variables );
    }

    /**
     * Scan through the keys of $variables and apply the replacement if one is found
     * @param array     $variables
     * @param array     $email_data
     * @param object    $queue_item
     * @param FUE_Email $email
     * @return array
     */
    protected function add_variable_replacements( $variables, $email_data, $queue_item, $email ) {
        global $wpdb;

        $order          = ($queue_item->order_id != 0) ? WC_FUE_Compatibility::wc_get_order( $queue_item->order_id ) : false;
        $email          = new FUE_Email( $queue_item->email_id );
        $send_coupon    = $email->send_coupon;
        $email_coupon_id= $email->coupon_id;

        if ( !$email->exists() || !$send_coupon )
            return $variables;

        $email_to = $email_data['email_to'];

        $coupon_code    = '';
        $coupon         = false;
        if ( $send_coupon == 1 && $email_coupon_id != 0 ) {
            $coupon         = $wpdb->get_row( $wpdb->prepare("SELECT * FROM {$wpdb->prefix}followup_coupons WHERE `id` = %d", $email_coupon_id) );

            if ( !$coupon ) {
                $variables['coupon_code'] = '';
                $queue_item->add_note( sprintf( 'Warning: The email coupon (ID #%s) could not be found', $email_coupon_id ) );
                return $variables;
            }

            $coupon_code    = FUE_Coupons::add_prefix( self::generate_coupon_code(), $coupon, $email_data );
            $coupon_array   = array(
                'post_title'    => $coupon_code,
                'post_author'   => 1,
                'post_date'     => date("Y-m-d H:i:s"),
                'post_status'   => 'publish',
                'comment_status'=> 'closed',
                'ping_status'   => 'closed',
                'post_name'     => $coupon_code,
                'post_parent'   => 0,
                'menu_order'    => 0,
                'post_type'     => 'shop_coupon'
            );
            $coupon_id = wp_insert_post($coupon_array);
            $wpdb->query("UPDATE {$wpdb->prefix}posts SET post_status = 'publish' WHERE ID = $coupon_id");

            $expiry = '';
            if ($coupon->expiry_value > 0 && !empty($coupon->expiry_type)) {
                $exp    = $coupon->expiry_value .' '. $coupon->expiry_type;
                $now    = current_time('mysql');
                $ts     = strtotime("$now +$exp");

                if ($ts !== false) {
                    $expiry = date('Y-m-d', $ts);
                }
            }

            update_post_meta($coupon_id, 'discount_type', $coupon->coupon_type);
            update_post_meta($coupon_id, 'coupon_amount', $coupon->amount);
            update_post_meta($coupon_id, 'individual_use', ($coupon->individual == 0) ? 'no' : 'yes');
            update_post_meta($coupon_id, 'product_ids', $coupon->product_ids);
            update_post_meta($coupon_id, 'exclude_product_ids', $coupon->exclude_product_ids);
            update_post_meta($coupon_id, 'usage_limit', $coupon->usage_limit);
            update_post_meta($coupon_id, 'usage_limit_per_user', $coupon->usage_limit_per_user);
            update_post_meta($coupon_id, 'expiry_date', $expiry);
            update_post_meta($coupon_id, 'apply_before_tax', ($coupon->before_tax == 0) ? 'no' : 'yes');
            update_post_meta($coupon_id, 'free_shipping', ($coupon->free_shipping == 0) ? 'no' : 'yes');
            update_post_meta($coupon_id, 'exclude_sale_items', ($coupon->exclude_sale_items == 0) ? 'no' : 'yes');
            update_post_meta($coupon_id, 'product_categories', maybe_unserialize($coupon->product_categories));
            update_post_meta($coupon_id, 'exclude_product_categories', maybe_unserialize($coupon->exclude_product_categories));
            update_post_meta($coupon_id, 'minimum_amount', $coupon->minimum_amount);
            update_post_meta($coupon_id, 'maximum_amount', $coupon->maximum_amount);

            $product_categories = '';
            $exclude_product_categories = '';

            if (! empty($coupon->product_categories) ) {
                $product_categories = unserialize($coupon->product_categories);
            }
            update_post_meta($coupon_id, 'product_categories', $product_categories);

            if (! empty($coupon->exclude_product_categories) ) {
                $exclude_product_categories = unserialize($coupon->exclude_product_categories);
            }
            update_post_meta($coupon_id, 'exclude_product_categories', $exclude_product_categories);

            $wpdb->query( $wpdb->prepare("UPDATE {$wpdb->prefix}followup_coupons SET `usage_count` = `usage_count` + 1 WHERE `id` = %d", $coupon->id) );

            FUE_Coupons::coupon_log($coupon_id, $coupon->coupon_name, $email->name, $email_to, $coupon_code);

            // record into the email_orders table
            $wpdb->query( $wpdb->prepare("UPDATE `{$wpdb->prefix}followup_email_order_coupons` SET `coupon_name` = %s, `coupon_code` = %s WHERE `email_order_id` = %d", $coupon->coupon_name, $coupon_code, $queue_item->id) );

            $variables['coupon_code'] = $coupon_code;
        }

        return $variables;

    }

    /**
     * Replaces {coupon_code_used} and {coupon_amount} variables
     *
     * @param array     $variables
     * @param array     $email_data
     * @param object    $queue_item
     * @param FUE_Email $email
     * @return array
     */
    protected function add_used_coupon_replacements( $variables, $email_data, $queue_item, $email ) {
        if ( !empty( $queue_item->meta['coupon_code'] ) ) {
            $variables['coupon_code_used'] = $queue_item->meta['coupon_code'];
        }

        if ( !empty( $queue_item->meta['discount_amount'] ) ) {
            $variables['coupon_amount'] = woocommerce_price( $queue_item->meta['discount_amount'] );
        }
        return $variables;

    }

    /**
     * Add variable replacements for test emails
     *
     * @param array     $variables
     * @param array     $email_data
     * @param FUE_Email $email
     *
     * @return array
     */
    protected function add_test_variable_replacements( $variables, $email_data, $email ) {
        $variables['coupon_code']       = 'COUPON_TEST';
        $variables['coupon_code_used']  = 'COUPON_USED_TEST';
        $variables['coupon_amount']     = woocommerce_price( 19.99 );

        return $variables;
    }


    /**
     * Generate a random 8-character unique string that's to used as a coupon code
     * @return string
     */
    public static function generate_coupon_code() {
        global $wpdb;

        $chars = 'abcdefghijklmnopqrstuvwxyz01234567890';
        do {
            $code = '';
            for ($x = 0; $x < 8; $x++) {
                $code .= $chars[ rand(0, strlen($chars)-1) ];
            }

            $check = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM `{$wpdb->prefix}posts` WHERE `post_title` = %s AND `post_type` = 'shop_coupon'", $code));

            if ($check == 0) break;
        } while (true);

        return $code;
    }

    /**
     * Log created coupons for reporting purposes
     * @param int       $id
     * @param string    $name
     * @param string    $email_name
     * @param string    $email_address
     * @param string    $coupon_code
     */
    public static function coupon_log($id, $name, $email_name, $email_address, $coupon_code) {
        global $wpdb;

        $log = array(
            'coupon_id'     => $id,
            'coupon_name'   => $name,
            'email_name'    => $email_name,
            'email_address' => $email_address,
            'date_sent'     => date('Y-m-d H:i:s'),
            'coupon_code'   => $coupon_code
        );
        $wpdb->insert( $wpdb->prefix .'followup_coupon_logs', $log );
    }

    /**
     * Mark used coupons in the logs
     *
     * @param string $code The used coupon code
     * @return void
     */
    public static function mark_used_coupon( $code ) {
        global $wpdb;

        $date = date('Y-m-d H:i:s');
        $wpdb->query( $wpdb->prepare("UPDATE {$wpdb->prefix}followup_coupon_logs SET `coupon_used` = 1, `date_used` = %s WHERE `coupon_code` = %s", $date, $code) );
    }

    /**
     * Add a prefix to the provided coupon code by replacing
     * placeholder text with data from $coupon and $order
     *
     * @param string    $code
     * @param object    $coupon
     * @param array     $email_data
     *
     * @return string
     */
    public static function add_prefix( $code, $coupon, $email_data ) {
        if ( $coupon->coupon_prefix != '' ) {
            $prefix = '';

            switch ( $coupon->coupon_prefix ) {
                case '{customer_first_name}':
                    $prefix = $email_data['first_name'] .'_';
                    break;

                case '{customer_last_name}':
                    $prefix = $email_data['last_name'] .'_';
                    break;

                case '{order_number}':
                    $prefix = apply_filters('woocommerce_order_number', $email_data['order_id']) .'_';
                    break;

                default:
                    $prefix = $coupon->coupon_prefix;
                    break;
            }

            $code = $prefix . $code;
        }

        return $code;
    }

    /**
     * Admin UI controller
     */
    public static function settings_main() {
        global $wpdb;

        $action = (isset($_GET['action'])) ? $_GET['action'] : 'list';

        if ( $action == 'list' ) {
            echo FUE_Coupons::coupons_html();
        } elseif ( $action == 'new-coupon' ) {
            echo FUE_Coupons::coupon_form_html();
        } elseif ( $action == 'edit-coupon' ) {
            echo FUE_Coupons::coupon_form_html();
        } elseif ( $action == 'delete-coupon' ) {
            $id = $_GET['id'];
            // delete
            $wpdb->query( $wpdb->prepare("DELETE FROM `{$wpdb->prefix}followup_coupons` WHERE `id` = %d", $id) );

            wp_redirect('admin.php?page=followup-emails-coupons&coupon_deleted=true');
            exit;
        }

    }

    /**
     * Settings controller
     *
     * @param string $tab The active tab
     */
    public function settings_tab_controller( $tab ) {
        global $wpdb;

        if ($tab == 'coupons') {
            fue_settings_header($tab);
            FUE_Coupons::settings_main();
            fue_settings_footer();
        } elseif ($tab == 'new-coupon') {
            fue_settings_header($tab);
            FUE_Coupons::coupon_form_html();
            fue_settings_footer();
        } elseif ($tab == 'edit-coupon') {
            fue_settings_header($tab);
            FUE_Coupons::coupon_form_html();
            fue_settings_footer();
        }
    }

    /**
     * Display notifications after certain actions have been performed
     */
    public function print_notifications() {
        include FUE_TEMPLATES_DIR .'/coupons/notifications.php';
    }

    /**
     * Record coupon after an email has been created or updated
     *
     * @param WP_Post $post
     */
    public function maybe_record_coupon( $post ) {

        $send_coupon    = isset($_POST['send_coupon']) ? $_POST['send_coupon'] : 0;
        $coupon_id      = isset($_POST['coupon_id']) ? $_POST['coupon_id'] : 0;

        $this->save_email_coupon( $post['ID'], $send_coupon, $coupon_id );

    }

    /**
     * Store coupon data as FUE_Email meta
     *
     * @param int   $email_id
     * @param int   $send_coupon
     * @param int   $coupon_id
     */
    public function save_email_coupon( $email_id, $send_coupon = 0, $coupon_id = 0 ) {
        update_post_meta( $email_id, '_send_coupon', $send_coupon );
        update_post_meta( $email_id, '_coupon_id', $coupon_id );
    }

    /**
     * Do not have FUE save the coupon data because it is already being done by FUE_Coupons
     *
     * @param array     $data
     * @param int       $id
     *
     * @return array
     */
    public function remove_coupon_data_from_email( $data, $id = 0 ) {

        unset($data['send_coupon'], $data['coupon_id']);

        return $data;
    }

    /**
     * Render the coupon panel on the email form
     *
     * @param FUE_Email $email
     */
    public function email_form_coupons_panel($email) {
        include FUE_TEMPLATES_DIR .'/coupons/form-fields.php';
    }


    public function before_message( $defaults ) {
        if ( $defaults['send_coupon'] == 1 )
            echo '<input type="checkbox" id="send_coupon" value="1" checked style="display: none;" />';
    }

    /**
     * Mark coupons as used
     *
     * @param $order_id
     * @param $post
     */
    public function order_processed( $order_id, $post ) {
        if ( class_exists('FUE_Reports') ) {
            // look for used coupons
            $coupons = get_post_meta( $order_id, 'coupons' );

            if (! empty($coupons) ) {
                // look for this coupon in the coupons log and update it
                foreach ( $coupons as $coupon ) {
                    if ( strpos($coupon, ',') !== false ) {
                        $codes = explode(',', $coupon);
                    } else {
                        $codes = array($coupon);
                    }

                    foreach ( $codes as $code ) {
                        $code = trim($code);
                        if ( empty($code) ) continue;

                        FUE_Coupons::mark_used_coupon( $code );
                    }
                }
            }
        }
    }

    /**
     * Coupon list table HTML
     * @return string
     */
    public static function coupons_html() {
        ob_start();
        include FUE_TEMPLATES_DIR .'/coupons/coupon-list-table.php';
        $html = ob_get_clean();

        return $html;
    }

    /**
     * Form for creating and updating coupons
     * @return string The form's HTML
     */
    public static function coupon_form_html() {
        ob_start();

        include FUE_TEMPLATES_DIR .'/coupons/coupon-form.php';

        $html = ob_get_clean();
        return $html;
    }

    /**
     * Backwards compatible method of getting a discount type
     *
     * @param string $type
     * @return string
     */
    public static function get_discount_type( $type ) {
        return (function_exists('wc_get_coupon_type')) ? wc_get_coupon_type($type) : WC()->get_coupon_discount_type($type);
    }

    /**
     * Backwards compatible method of getting all discount types
     *
     * @return array
     */
    public static function get_discount_types() {
        return (function_exists('wc_get_coupon_types')) ? wc_get_coupon_types() : WC()->get_coupon_discount_types();
    }

    /**
     * Apply the same coupon to the cloned email
     *
     * @param int $new_id New FUE_Email ID
     * @param int $source_id FUE_Email ID
     */
    public function apply_coupon_to_cloned_email( $new_id, $source_id ) {
        global $wpdb;

        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}followup_email_coupons WHERE email_id = %d", $source_id), ARRAY_A);

        if ($row) {
            $row['email_id'] = $new_id;
            $wpdb->insert($wpdb->prefix .'followup_email_coupons', $row);
        }
    }

    /**
     * Remove coupon from a deleted FUE_Email
     *
     * @param int $id
     */
    public function remove_coupon_from_deleted_email($id) {
        global $wpdb;

        $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->prefix}followup_email_coupons WHERE email_id = %d", $id));
    }

    /**
     * Get all coupons
     *
     * @return array
     */
    public static function get_coupons() {
        global $wpdb;

        return $wpdb->get_results("SELECT * FROM {$wpdb->prefix}followup_coupons ORDER BY coupon_name ASC");
    }

    /**
     * get_coupon_args_from_post()
     * @return array
     */
    private static function get_coupon_args_from_post() {
        $_POST = array_map('stripslashes_deep', $_POST);

        $data = array(
            'coupon_name'   => $_POST['name'],
            'coupon_prefix' => $_POST['prefix'],
            'coupon_type'   => $_POST['type'],
            'amount'        => floatval($_POST['amount']),
            'individual'    => (isset($_POST['individual_use']) && $_POST['individual_use'] == 'yes') ? 1 : 0,
            'exclude_sale_items'    => (isset($_POST['exclude_sale_items']) && $_POST['exclude_sale_items'] == 'yes') ? 1 : 0,
            'before_tax'    => 1, // before_tax is always true since WC2.3
            'free_shipping' => (isset($_POST['free_shipping']) && $_POST['free_shipping'] == 'yes') ? 1 : 0,
            'minimum_amount'=> (isset($_POST['minimum_amount']) && $_POST['minimum_amount'] > 0 ) ? (float)$_POST['minimum_amount'] : '',
            'maximum_amount'=> (isset($_POST['maximum_amount']) && $_POST['maximum_amount'] > 0 ) ? (float)$_POST['maximum_amount'] : '',
            'usage_limit'   => (isset($_POST['usage_limit']) && !empty($_POST['usage_limit'])) ? (int)$_POST['usage_limit'] : '',
            'usage_limit_per_user'   => (isset($_POST['usage_limit_per_user']) && !empty($_POST['usage_limit_per_user'])) ? (int)$_POST['usage_limit_per_user'] : '',
            'expiry_value'  => (isset($_POST['expiry_value']) && !empty($_POST['expiry_value'])) ? intval($_POST['expiry_value']) : '',
            'expiry_type'   => $_POST['expiry_type']
        );

        if ( isset($_POST['product_ids']) && !empty($_POST['product_ids']) ) {
            $data['product_ids'] = $_POST['product_ids'];
        } else {
            $data['product_ids'] = '';
        }

        if ( isset($_POST['exclude_product_ids']) && !empty($_POST['exclude_product_ids']) ) {
            $data['exclude_product_ids'] = $_POST['exclude_product_ids'];
        } else {
            $data['exclude_product_ids'] = '';
        }

        if ( isset($_POST['product_categories']) && !empty($_POST['product_categories']) ) {
            $data['product_categories'] = serialize($_POST['product_categories']);
        } else {
            $data['product_categories'] = '';
        }

        if ( isset($_POST['exclude_product_categories']) && !empty($_POST['exclude_product_categories']) ) {
            $data['exclude_product_categories'] = serialize($_POST['exclude_product_categories']);
        } else {
            $data['exclude_product_categories'] = '';
        }

        if ( !empty($_POST['id'] ) ) {
            $data['id'] = $_POST['id'];
        }

        return $data;
    }

    /**
     * Get the coupon data
     *
     * @param int $id Coupon ID
     * @return array
     */
    public static function get_coupon_data( $id = 0 ) {
        global $wpdb;

        $defaults   = array(
            'name'                  => '',
            'prefix'                => '',
            'type'                  => 'fixed_cart',
            'amount'                => '',
            'send_mode'             => 'immediately',
            'before_tax'            => 1,
            'individual'            => 0,
            'exclude_sale_items'    => 0,
            'free_shipping'         => 0,
            'minimum_amount'        => '',
            'maximum_amount'        => '',
            'usage_limit'           => '',
            'usage_limit_per_user'  => '',
            'products'              => array(),
            'categories'            => array(),
            'exclude_products'      => array(),
            'exclude_categories'    => array(),
            'expiry_value'          => 0,
            'expiry_type'           => ''
        );

        if ( $id > 0 ) {
            $coupon = $wpdb->get_row( $wpdb->prepare("SELECT * FROM {$wpdb->prefix}followup_coupons WHERE id = %d", $id) );

            if ( $coupon ) {
                $defaults   = array(
                    'id'                    => $id,
                    'name'                  => $coupon->coupon_name,
                    'prefix'                => $coupon->coupon_prefix,
                    'type'                  => $coupon->coupon_type,
                    'amount'                => $coupon->amount,
                    'before_tax'            => 1,
                    'individual'            => $coupon->individual,
                    'exclude_sale_items'    => $coupon->exclude_sale_items,
                    'free_shipping'         => $coupon->free_shipping,
                    'limit'                 => $coupon->usage_limit,
                    'products'              => $coupon->product_ids,
                    'exclude_products'      => $coupon->exclude_product_ids,
                    'categories'            => (empty($coupon->product_categories)) ? array() : maybe_unserialize($coupon->product_categories),
                    'exclude_categories'    => (empty($coupon->exclude_product_categories)) ? array() : maybe_unserialize($coupon->exclude_product_categories),
                    'minimum_amount'        => $coupon->minimum_amount,
                    'maximum_amount'        => $coupon->maximum_amount,
                    'expiry_value'          => $coupon->expiry_value,
                    'expiry_type'           => $coupon->expiry_type,
                    'usage_limit'           => $coupon->usage_limit,
                    'usage_limit_per_user'  => $coupon->usage_limit_per_user,
                );
            }
        }

        return $defaults;

    }

}

$GLOBALS['fue_coupons'] = new FUE_Coupons();
