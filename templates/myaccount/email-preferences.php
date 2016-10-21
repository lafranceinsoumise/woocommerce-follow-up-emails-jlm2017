<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( !is_user_logged_in() ) {
    wp_redirect( fue_get_login_url( site_url('/email-preferences') ) );
    exit;
}

$me         = wp_get_current_user();
$newsletter = new FUE_Newsletter();
$lists      = $newsletter->get_public_lists();
$subscriber = $newsletter->get_subscriber( $me->user_email );
$subscriber_lists = array();

if ( isset( $subscriber['lists'] ) ) {
    foreach ( $subscriber['lists'] as $list ) {
        $subscriber_lists[] = $list['id'];
    }
}

get_header(); ?>
<div id="primary" class="content-area">
    <div id="content" class="site-content" role="main">

        <article class="page type-page status-publish hentry">
            <header class="entry-header">
                <h1 class="entry-title"><?php echo wp_kses_post( get_option( 'fue_email_subscriptions_page_title', 'Email Subscriptions' ) ); ?></h1>
            </header>

            <div class="entry-content">
                <div class="follow-up-subscriptions">

                    <div class="fue-subscriptions-message hidden fue-success">
                        <p><span class="dashicons dashicons-yes"></span> <?php _e('Saved', 'follow_up_emails'); ?></p>
                    </div>

                    <form id="fue-subscriptions-form" action="" method="post">
                        <ul class="follow-up-lists">
                            <?php foreach ( $lists as $list ): ?>
                                <li class="list-<?php echo $list['id']; ?>">
                                    <label>
                                        <input type="checkbox" class="chk-fue-list" name="fue_lists[]" value="<?php echo $list['id']; ?>" <?php checked( true, in_array( $list['id'], $subscriber_lists ) ); ?> />
                                        <?php echo $list['list_name']; ?>
                                    </label>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                        <input type="submit" class="button button-primary fue-button" value="<?php echo esc_attr( get_option('fue_email_subscriptions_button_text', 'Update Subscriptions') ); ?>" />
                    </form>
                </div>
            </div>
        </article>
    </div><!-- #content -->
</div><!-- #primary -->
<?php
get_sidebar( 'content' );
get_sidebar();
get_footer();