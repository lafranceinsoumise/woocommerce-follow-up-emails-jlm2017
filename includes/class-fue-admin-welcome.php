<?php

/**
 * FUE_Admin_Welcome class
 */
class FUE_Admin_Welcome {

    /** @var array Tweets user can optionally send after install */
    private $tweets = array(
        'Follow-up Emails drives user engagement for websites. It is used on over 5,000 websites running #WordPress and #WooCommerce.',
        'Building an online store? Follow-ups is the leading marketing plugin for WordPress and WooCommerce.'
    );

    /**
     * Hook in tabs.
     */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'admin_menus') );
        add_action( 'admin_head', array( $this, 'admin_head' ) );
        shuffle( $this->tweets );
    }

    /**
     * Add admin menus/screens.
     */
    public function admin_menus() {
        $welcome_page_name  = __( 'About Follow-Up Emails', 'follow-up-emails' );
        $welcome_page_title = __( 'Welcome to Follow-Up Emails', 'follow-up-emails' );

        if ( $_GET['page'] == 'fue-about' ) {
            $page = add_dashboard_page( $welcome_page_title, $welcome_page_name, 'manage_options', 'fue-about', array( $this, 'about_screen' ) );
            add_action( 'admin_print_styles-' . $page, array( $this, 'admin_css' ) );
        }
    }

    /**
     * admin_css function.
     */
    public function admin_css() {
        //wp_enqueue_style( 'woocommerce-activation', WC()->plugin_url() . '/assets/css/activation.css', array(), WC_VERSION );
        wp_enqueue_style( 'fue-welcome', FUE_TEMPLATES_URL . '/welcome.css', array(), FUE_VERSION );
    }

    /**
     * Add styles just for this page, and remove dashboard page links.
     */
    public function admin_head() {
        remove_submenu_page( 'index.php', 'fue-about' );
    }

    public function about_screen() {
        $this->intro();
    }

    /**
     * Intro text/links shown on all about pages.
     */
    private function intro() {
        // Drop minor version if 0
        $major_version = substr( FUE_VERSION, 0, 3 );

        include FUE_TEMPLATES_DIR .'/welcome.php';
    }
}

new FUE_Admin_Welcome();