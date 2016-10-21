<?php

/**
 * Class FUE_ActionScheduler_Logger
 *
 * Stops Action Scheduler from saving logs as WP Comments
 */
if (! class_exists('ActionScheduler_wpCommentLogger') ) {
    require_once 'lib/action-scheduler/classes/ActionScheduler_Logger.php';
    require_once 'lib/action-scheduler/classes/ActionScheduler_wpCommentLogger.php';
}

class FUE_ActionScheduler_Logger extends ActionScheduler_wpCommentLogger {

    public function log( $action_id, $message, DateTime $date = null ) {
        $groups = wp_get_post_terms( $action_id, 'action-group' );
        $is_fue = false;

        foreach ( $groups as $group ) {
            if ( $group->slug == 'fue' ) {
                $is_fue = true;
                break;
            }
        }

        if ( get_the_title( $action_id ) == 'fue_send_summary' ) {
            $is_fue = true;
        }

        if (! $is_fue ) {
            parent::log( $action_id, $message, $date );
        }


    }

}