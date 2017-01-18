<?php
 /**
  * Plugin Name: Follow-Up Emails
  * Plugin URI: http://www.woothemes.com/products/follow-up-emails/
  * Description: Automate your email marketing, and create scheduled newletters to drive customer engagement for WordPress, WooCommerce, and Sensei.
  * Version: 4.4.17
  * Author: WooThemes
  * Author URI: http://www.woocommerce.com
  * Text domain: follow_up_emails
  */
  
/** Path and URL constants **/
define( 'FUE_VERSION', '4.4.17' );
define( 'FUE_KEY', 'aHR0cDovLzc1bmluZXRlZW4uY29tL2Z1ZS5waH' );
define( 'FUE_FILE', __FILE__ );
define( 'FUE_URL', plugins_url('', __FILE__) );
define( 'FUE_DIR', plugin_dir_path( __FILE__ ) );
define( 'FUE_INC_DIR', FUE_DIR .'includes' );
define( 'FUE_INC_URL', FUE_URL .'/includes' );
define( 'FUE_ADDONS_DIR', FUE_DIR .'/addons' );
define( 'FUE_ADDONS_URL', FUE_URL .'/addons' );
define( 'FUE_TEMPLATES_DIR', FUE_DIR .'templates' );
define( 'FUE_TEMPLATES_URL', FUE_URL .'/templates' );

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) )
    require_once( 'woo-includes/woo-functions.php' );

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), '05ece68fe94558e65278fe54d9ec84d2', '18686' );


load_plugin_textdomain( 'follow_up_emails', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

global $fue, $wpdb;
require_once FUE_INC_DIR .'/class-follow-up-emails.php';
$fue = new Follow_Up_Emails( $wpdb );

if ( !function_exists( 'FUE' ) ):
    /**
     * Returns an instance of the Follow_Up_Emails class
     * @since 5.0
     * @return Follow_Up_Emails
     */
    function FUE() {
        return Follow_Up_Emails::instance();
    }
endif;